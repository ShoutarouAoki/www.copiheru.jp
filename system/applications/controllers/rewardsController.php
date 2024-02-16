<?php
################################ FILE MANAGEMENT ################################
##
##	rewardsController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	REWARDS PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	REWARDS PAGE 各種処理
##
##	index : 開催中イベントでの応援報酬キャラ一覧
##	display : 各キャラの報酬一覧と、閲覧しているユーザの報酬獲得履歴
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2017/10/31
##	CREATER		:
##
##	=============================================================================
##
##	■ CHECK (要チェックや)
##  データモデルなし！取り急ぎでSQL直打ちしてます！
##
##	■ REWRITE (改修履歴)
##
##
##
##
##
##
##
##
##
##
################################# REQUIRE MODEL #################################

/************************************************
**
**	MODEL FILE REQUIRE
**	---------------------------------------------
**	MODEL CLASS FILE READING
**
************************************************/

/** RANKING MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/RankingModel.php");

/** ATTACH MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AttachModel.php");

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");


################################# POST ARRAY ####################################

$value_array				= array('page','event_id','character_id');
$data						= $mainClass->getArrayContents($value_array,$values);

############################## INDIVIDUAL SETTING ###############################


/************************************************
**
**	THIS PAGE INDIVIDUAL SETTING
**	---------------------------------------------
**	DATABASE / PATH / CATEGORY ...etc
**
************************************************/

# PAGE
if(empty($data['page'])){
	$data['page']				= "index";
}

# EVENT ID
if(empty($data['event_id'])){
	$data['event_id']			= 0;
}


# CHARACTER ID
if(empty($data['character_id'])){
	$data['character_id']			= 0;
}

# ERROR
$error							= NULL;
$errormessage					= NULL;


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# RANKING MODEL
$rankingModel				= new RankingModel($database,$mainClass);

# ATTACH MODEL
$attachModel				= new AttachModel($database,$mainClass);

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);


##################################### FUNCTIONS ######################################

/** FUNCTIONS FILE **/
//↓はない
//require_once(dirname(__FILE__)."/functions/rewardsFunc.php");


################################## MAIN SQL #####################################


/************************************************
**
**	PAGE SEPALATE
**	---------------------------------------------
**	DISPLAY
**	---------------------------------------------
**	PAGE CONTROLL
**
**	$exectionがNULLなら
**	表示処理開始
**	---------------------------------------------
**	PAGE :	
**
************************************************/

if(empty($exection) && empty($error)){



	/************************************************
	**
	**	INDEX
	**	============================================
	**	開催中イベントでの応援報酬キャラ一覧
	**
	**
	************************************************/

	if($data['page'] == "index"){

		/************************************************
		**
		**	開催イベントチェックがtype=1
		**
		************************************************/
		# 開催イベントチェック(応援報酬)
		$event_data						= $eventModel->getEventData($members_data, 1);
		
		$event_id = $event_data["id"];

		# 開催イベントチェック(応援報酬)
		if(empty($event_id) || $event_id == 0){
			$event_title						= "貢献度報酬";
		}else{
			$events_data						= $eventModel->getEventDataById($event_id);
			$event_title						= $events_data['name']."【開催中】";
		}
		
		/************************************************
		**
		**	キャラ一覧取得
		**
		*************************************************/
		//親キャラ一覧を取得、名前とnarutoだけ使用。
		$chara_array = array();
		$chara_array[":site_cd"] = SITE_CD;

		$chara_table	= "members";
		$chara_column	= "id,nickname";
		$chara_where	= "site_cd = :site_cd ";
		$chara_where	.= "AND op_id > 0 ";
		$chara_where	.= "AND naruto = 0 ";
		$chara_where	.= "AND status in(0,1) ";
		//$chara_where	.= "AND root_character_id = 0";
		$chara_order = "id ASC";
		$chara_limit = NULL;
		$chara_group = NULL;

		$chara_rtn = $database->selectDb($chara_table, $chara_column, $chara_where, 
								$chara_array, $chara_order, $chara_limit, $chara_group);
		$error = $database->errorDb("rewardsController:".$chara_table,$chara_rtn->errorCode(),__FILE__,__LINE__);

		# 応援報酬追加用キャラ一覧
		$characters = array();
		while($chara_data   = $database->fetchAssoc($chara_rtn)){
			$characters[$chara_data["id"]] = $chara_data["nickname"];
		}

		$database->freeResult($chara_rtn);
		

		# 応援報酬取得
		$reward_character_list = array();
		
		$rewards_character_sql = "SELECT character_id,count(*) as num FROM support_rewards WHERE site_cd = ".SITE_CD." AND status=0 AND event_id = ".$event_data["id"]." GROUP BY character_id";
		$rewards_character_rtn = $database->query($rewards_character_sql);
		
		$reward_character = $database->numRows($rewards_character_rtn);
		//if($rewards_character_rtn->num_rows>0){
			
			while($rewards = $database->fetchAssoc($rewards_character_rtn)){

				if($rewards["num"]>0 && $rewards["character_id"]>0){//20180707 ypdate by A.cos
				//if($rewards["num"]>0){
					$tmp = array();

					# 応援ポイント取得
					# ランキングポイントの取得ユーザ&キャラ&イベント
					$rankinng_point_sql = "select point from rankings where site_cd=".SITE_CD." AND status=0";
					$rankinng_point_sql .= " AND event_id = ".$event_data["id"]." AND user_id = ".$members_data["id"]." AND character_id=".$rewards["character_id"];
					$rankinng_point_rtn = $database->query($rankinng_point_sql);
					$rankinng_point = $database->fetchAssoc($rankinng_point_rtn);
					
					# キャラ画像取得
					$attaches_conditions				= array();
					$attaches_conditions				= array(
						'user_id'						=> $rewards["character_id"],
						'category'						=> $list_image_category,
						'use_flg'						=> 1,
						'pay_count'						=> 0,
						'device'						=> $device_number,
						'status'						=> 1,
						'limit'							=> 1,
						'group'							=> NULL
					);
					$attaches_data						= $attachModel->getAttachData($attaches_conditions);
					
					$tmp["character_id"] = $rewards["character_id"];
					$tmp["character_name"] = $characters[$rewards["character_id"]];
					$tmp["character_image"] = HTTP_ATTACHES."/".$attaches_data['attached'];
					$tmp["rewards_num"] = $rewards["num"];
					$tmp["rewards_point"] = ((!empty($rankinng_point["point"]))?$rankinng_point["point"]:"0");
					
					$reward_character_list[] = $tmp;
				}
			}
		//}
		$database->freeResult($rewards_character_rtn);

	/************************************************
	**
	**	display
	**	============================================
	**	各キャラの報酬一覧と、閲覧しているユーザの報酬獲得履歴
	**
	**
	************************************************/

	}elseif($data['page'] == "display"){
		# イベント情報
		# 開催イベントチェック(応援報酬)
		$event_id = $data['event_id'];
		if(empty($data['event_id']) || $data['event_id'] == 0){
			$event_title						= "貢献度報酬";
		}else{
			$events_data						= $eventModel->getEventDataById($event_id);
			$event_title						= $events_data['name']."【開催中】";
		}
		
		# キャラクター
		if(!empty($data['event_id']) && is_numeric($data['event_id'])
			&& !empty($data['character_id']) && is_numeric($data['character_id'])){
			
			# キャラ画像取得
			$attaches_conditions				= array();
			$attaches_conditions				= array(
				'user_id'						=> $data["character_id"],
				'category'						=> $list_image_category,
				'use_flg'						=> 1,
				'pay_count'						=> 0,
				'device'						=> $device_number,
				'status'						=> 1,
				'limit'							=> 1,
				'group'							=> NULL
			);
			$attaches_data						= $attachModel->getAttachData($attaches_conditions);
			$character_image = HTTP_ATTACHES."/".$attaches_data['attached'];
			
			# チケットリスト取得
			$tikects = array();
			$tikects_sql  = "SELECT id,name,image FROM shops ORDER BY id";
			$tikects_rtn = $database->query($tikects_sql);
			//$database->errorDb('shops',$database->errno,__FILE__,__LINE__);
			while($ticket = $database->fetchAssoc($tikects_rtn)){
				$tikects[$ticket["id"]] = array("name" => $ticket["name"], "image" => HTTP_ITEM_IMAGE."/".$ticket["image"]);
			}
			$database->freeResult($tikects_rtn);
/*
echo "<pre>";
print_r($tikects);
echo "</pre>";
*/
			# アイテムリスト取得
			$items = array();
			$items_sql  = "SELECT id,name,image FROM items ORDER BY id";
			$items_rtn = $database->query($items_sql);
			//$database->errorDb('items',$database->errno,__FILE__,__LINE__);
			while($item = $database->fetchAssoc($items_rtn)){
				$items[$item["id"]] = array("name" => $item["name"], "image" => HTTP_ITEM_IMAGE."/".$item["image"]);
			}
			$database->freeResult($items_rtn);
/*
echo "<pre>";
print_r($items);
echo "</pre>";
*/
			# 画像リスト取得
			$images = array();
			$images_sql  = "SELECT id,img_key,img_name FROM images ORDER BY id";
			$images_rtn = $database->query($images_sql);
			//$database->errorDb('images',$database->errno,__FILE__,__LINE__);
			while($image = $database->fetchAssoc($images_rtn)){
				$images[$image["id"]] = array("name" => $image["img_key"], "image" => HTTP_ITEM_IMAGE."/".$image["img_name"]);
			}
			$database->freeResult($images_rtn);
/*
echo "<pre>";
print_r($images);
echo "</pre>";						
*/
			# 個人の応援報酬の状態（キャラクター＆イベント）
			$sub_sql = "SELECT * FROM support_rewards_history WHERE site_cd = ".SITE_CD." AND user_id= ".$members_data["id"];
			$main_sql = "SELECT sr.id as id, sr.site_cd, sr.event_id as event_id, sr.character_id as character_id, sr.point as point, ";
			$main_sql .= "sr.type as type, sr.target_id as target_id, sr.unit as unit, srh.user_id as user_id, srh.reg_date as reg_date,sr.status as status";
			$main_sql .= " FROM support_rewards AS sr LEFT JOIN (".$sub_sql.") AS srh ON sr.id = srh.rewards_id";
			$main_sql .= " WHERE sr.status=0 AND sr.site_cd = ".SITE_CD." AND sr.event_id = ".$data['event_id']." AND sr.character_id = ".$data['character_id'];
			$main_sql .= " ORDER BY point ASC";
			$rewards_rtn = $database->query($main_sql);
			$reward_character = $database->numRows($rewards_rtn);
			
			$rewards_list = array();
			while($rewards = $database->fetchAssoc($rewards_rtn)){
				//ゲット済みチェック
				if(!empty($rewards["user_id"])){
					$rewards["getted"] = 1;
				}else{
					$rewards["getted"] = 0;
				}
				
				switch($rewards["type"]){
					case 1:
						if(!empty($tikects[$rewards["target_id"]]))
							$rewards = array_merge($rewards, $tikects[$rewards["target_id"]]);
							//$rewards["rewards"] = $tikects[$rewards["target_id"]];
						break;
					case 2:
						if(!empty($items[$rewards["target_id"]]))
							$rewards = array_merge($rewards, $items[$rewards["target_id"]]);
							//$rewards["rewards"] = $items[$rewards["target_id"]];
						break;
					case 3:
						if(!empty($images[$rewards["target_id"]]))
							$rewards = array_merge($rewards, $images[$rewards["target_id"]]);
							//$rewards["rewards"] = $images[$rewards["target_id"]];
						break;
					default:
						break;
				}				
				
				$rewards_list[] = $rewards;
			}
			$database->freeResult($rewards_rtn);
				
		}else{

			$error										= 1;
			$errormessage								= "お探しのページは存在しせん<br />";
			$event_title								= "貢献度報酬";

		}
	}
# ERROR
}else{

	$error										= 1;
	$errormessage								= "該当の貢献度報酬情報がありません<br />";

}


################################## FILE END #####################################
?>