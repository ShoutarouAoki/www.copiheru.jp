<?php
################################ FILE MANAGEMENT ################################
##
##	rankingController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	RANKING PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	RANKING PAGE 各種処理
##
##	index		: ランキングイベント一覧
##	character	: ランキングイベント キャラクター順位
##	user		: ランキングイベント ユーザー順位
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2016/05/31
##	CREATER		:
##
##	=============================================================================
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
	**	イベントリスト
	**
	************************************************/

	if($data['page'] == "index"){

		$nowtime								= date("YmdHis");

		$events_conditions						= array();
		$events_conditions						= array(
			'date_s'							=> $nowtime,
			'type'								=> 1,
			'status'							=> 0,
			'order'								=> 'date_e DESC'
		);
		$events_rtn								= $eventModel->getEventList($events_conditions);

		$i=0;
		$j=0;
		while($events_data = $database->fetchAssoc($events_rtn)){

			# 開催中イベント
			if($events_data['date_s'] <= $nowtime && $events_data['date_e'] >= $nowtime){

				$event_list['id'][$i]			= $events_data['id'];
				$event_list['name'][$i]			= $events_data['name'];
				$event_list['image'][$i]		= $events_data['image'];

				$i++;

			# それ以外
			}else{

				$event_past['id'][$j]			= $events_data['id'];
				$event_past['name'][$j]			= $events_data['name'];
				$event_past['image'][$j]		= $events_data['image'];

				$j++;

			}

		}

		$database->freeResult($events_rtn);


	/************************************************
	**
	**	CHARACTER
	**	============================================
	**	キャラクターランキング
	**
	************************************************/

	}elseif($data['page'] == "character"){

		# イベント情報
		if(empty($data['event_id']) || $data['event_id'] == 0){
			$event_title						= "総合ランキング";
		}else{
			$events_data						= $eventModel->getEventDataById($data['event_id']);
			$event_title						= $events_data['name'];
		}

		
		# ランキング名をMemcachedでストックしておく、なくなっていればDBに問い合わせ直してストック
		$mc = new Memcached();
		$mc->addServer(MEMCACHED_SERVER, 11211);
		$ranking_past_id = MEMCACHED_RANKING_PAST_KEY.$data['event_id'];
		
		$cache_flg = 0;
		$nowtime	=	date("YmdHis");
		if(empty($data['event_id']) || $data['event_id'] == 0){
			//総合ランキング
			$cache_flg = 0;
		}else{
			//現在のイベント
			if($events_data['date_s'] <= $nowtime && $events_data['date_e'] >= $nowtime){
				$cache_flg = 0;
			}else{
				//過去イベント
				if( $ranking_list = $mc->get($ranking_past_id) ){
					$cache_flg = 2;//キャッシュ登録済み
				}else{
					$cache_flg = 1;//キャッシュ未登録、これから登録する
				}
				
			}
		}

		if($cache_flg!=2){
			# トータルポイント
			if(defined("SYSTEM_CHECK") || $members_data['debug'] == 1){
				$open_flg							= "1,2,9";
			}else{
				$open_flg							= "1";
			}

			$point_conditions						= array();
			$point_conditions						= array(
				'event_id'							=> $data['event_id'],
				'status'							=> 0,
				'open_flg'							=> $open_flg,
				'open_in'							=> 1,
				'limit'								=> 1,
				'group'								=> 'r.event_id'
			);

			$point_column							= "SUM(r.point) AS total_point";
			$point_data								= $rankingModel->getRankingDataJoinOnMembers($point_conditions,$point_column);


			# キャラ毎の順位で取得
			$rankings_conditions					= array();
			$rankings_conditions					= array(
				'event_id'							=> $data['event_id'],
				'status'							=> 0,
				'open_flg'							=> $open_flg,
				'open_in'							=> 1,
				'order'								=> 'ranking_point DESC, r.character_id',
				'group'								=> 'r.character_id'
			);

			$rankings_column						= "SUM(r.point) as ranking_point, r.character_id, m.id as members_id, m.nickname, m.naruto";
			$rankings_rtn							= $rankingModel->getRankingListJoinOnMembers($rankings_conditions,$rankings_column);

			$i=0;
			while($rankings_data = $database->fetchAssoc($rankings_rtn)){

				# キャラ情報取得
				$character_data						= $memberModel->getMemberDataById($rankings_data['character_id'],1,"id,nickname,naruto");

				if($character_data['naruto'] > 0){
					$character_id					= $character_data['naruto'];
				}else{
					$character_id					= $character_data['id'];
				}

				# キャラ サムネイル画像取得
				$attaches_conditions				= array();
				$attaches_conditions				= array(
					'user_id'						=> $character_id,
					'category'						=> $thumbnail_image_category,
					'use_flg'						=> 1,
					'pay_count'						=> 0,
					'device'						=> $device_number,
					'status'						=> 1,
					'limit'							=> 1,
					'group'							=> NULL
				);
				$attaches_data						= $attachModel->getAttachData($attaches_conditions);

				# 割合計算
				if($rankings_data['ranking_point'] == 0){

					$percent						= 0;

				}else{

					$percent_result					= ($rankings_data['ranking_point'] / $point_data['total_point']) * 100;
					$percent						= round($percent_result,1);
					$percent_width					= round($percent_result,0);

				}

				# 順位
				$ranking_number						= $i + 1;

				# 同順位チェック
				if($i > 0 && isset($check_point)){

					if($check_point == $rankings_data['ranking_point']){
						$ranking_number				= $ranking_number - 1;
					}

				}

				$ranking_list['character_id'][$i]	= $character_id;
				$ranking_list['name'][$i]			= $character_data['nickname'];
				$ranking_list['ranking'][$i]		= $ranking_number;
				$ranking_list['point'][$i]			= $rankings_data['ranking_point'];
				$ranking_list['percent'][$i]		= $percent;
				$ranking_list['percent_width'][$i]	= $percent_width;

				# サムネイル画像
				if(!empty($attaches_data)){
					$ranking_list['image'][$i]		= $attaches_data['attached'];
				}
				$check_point						= $rankings_data['ranking_point'];
				$i++;
			}
			$database->freeResult($rankings_rtn);

			if($cache_flg==1 && !empty($ranking_list)){
				$res = $mc->set($ranking_past_id, $ranking_list, 86400);
				//echo "saved<br/>";
			}
		}


	/************************************************
	**
	**	USER
	**	============================================
	**	ユーザーランキング
	**
	************************************************/

	}elseif($data['page'] == "user"){

		# キャラクター
		if(!empty($data['character_id']) && is_numeric($data['character_id'])){

			# キャラ情報取得
			$character_data								= $memberModel->getMemberDataById($data['character_id'],1,"id,nickname,naruto");

			if(!empty($character_data['id'])){

				if($character_data['naruto'] > 0){
					$character_id						= $character_data['naruto'];
				}else{
					$character_id						= $character_data['id'];
				}

				# キャラ サムネイル画像取得
				$attaches_conditions					= array();
				$attaches_conditions					= array(
					'user_id'							=> $character_id,
					'category'							=> $thumbnail_image_category,
					'use_flg'							=> 1,
					'pay_count'							=> 0,
					'device'							=> $device_number,
					'status'							=> 1,
					'limit'								=> 1,
					'group'								=> NULL
				);
				$attaches_data							= $attachModel->getAttachData($attaches_conditions);

				# イベント情報
				if(empty($data['event_id']) || $data['event_id'] == 0){
					$event_title						= $character_data['nickname']." ファン総合ランキング";
				}else{
					$events_data						= $eventModel->getEventDataById($data['event_id']);
					$event_title						= $events_data['name']." ".$character_data['nickname']." ファンランキング";
				}

				# トータルポイント
				$point_conditions						= array();
				$point_conditions						= array(
					'character_id'						=> $character_id,
					'event_id'							=> $data['event_id'],
					'status'							=> 0,
					'limit'								=> 1,
					'group'								=> 'event_id'
				);

				$point_column							= "SUM(point) AS total_point";
				$point_data								= $rankingModel->getRankingData($point_conditions,$point_column);


				# キャラ毎の順位で取得
				$rankings_conditions					= array();
				$rankings_conditions					= array(
					'character_id'						=> $character_id,
					'event_id'							=> $data['event_id'],
					'status'							=> 0,
					'order'								=> 'ranking_point DESC',
					'limit'								=> 10,
					'group'								=> 'user_id'
				);

				$rankings_column						= "user_id,SUM(point) AS ranking_point";
				$rankings_rtn							= $rankingModel->getRankingList($rankings_conditions,$rankings_column);

				$i=0;
				while($rankings_data = $database->fetchAssoc($rankings_rtn)){

					$user_data							= $memberModel->getMemberDataById($rankings_data['user_id'],NULL,"id,nickname");

					# 割合計算
					if($rankings_data['ranking_point'] == 0){

						$percent						= 0;

					}else{

						$percent_result					= ($rankings_data['ranking_point'] / $point_data['total_point']) * 100;
						$percent						= round($percent_result,1);
						$percent_width					= round($percent_result,0);

					}

					# 順位
					$ranking_number						= $i + 1;

					# 同順位チェック
					if($i > 0 && isset($check_point)){

						if($check_point == $rankings_data['ranking_point']){
							$ranking_number				= $ranking_number - 1;
						}

					}

					$ranking_list['user_id'][$i]		= $user_data['id'];
					$ranking_list['name'][$i]			= $user_data['nickname'];
					$ranking_list['ranking'][$i]		= $ranking_number;
					$ranking_list['point'][$i]			= $rankings_data['ranking_point'];
					$ranking_list['percent'][$i]		= $percent;
					$ranking_list['percent_width'][$i]	= $percent_width;

					$check_point						= $rankings_data['ranking_point'];

					$i++;

				}

				$database->freeResult($rankings_rtn);

				$mainClass->debug($ranking_list);


				# 自分の順位を知る
				$myrank									= NULL;

				$myrank_conditions						= array();
				$myrank_conditions						= array(
					'user_id'							=> $members_data['id'],
					'character_id'						=> $character_id,
					'event_id'							=> $data['event_id'],
					'status'							=> 0,
					'limit'								=> 1,
				);

				$myrank_column							= "point";
				$myrank_data							= $rankingModel->getRankingData($myrank_conditions,$myrank_column);

				# 自分よりポイント高いユーザーの人数算出
				if(!empty($myrank_data['point'])){

					$userrank_conditions				= array();
					$userrank_conditions				= array(
						'character_id'					=> $character_id,
						'rank_check'					=> $myrank_data['point'],
						'event_id'						=> $data['event_id'],
						'status'						=> 0,
					);

					$userrank_column					= "COUNT(user_id) AS ranker_num";
					$userrank_data						= $rankingModel->getRankingData($userrank_conditions,$userrank_column);

					$myrank								= $userrank_data['ranker_num'] + 1;

					$mypercent							= ($myrank_data['point'] / $point_data['total_point']) * 100;
					$mypercent							= round($mypercent,1);

				}


			# ERROR
			}else{

				$error									= 1;
				$errormessage							= "該当のキャラクター情報がありません<br />";
				$event_title							= "ファンランキング";

			}


		}else{

			$error										= 1;
			$errormessage								= "お探しのページは存在しせん<br />";
			$event_title								= "ファンランキング";

		}


	/************************************************
	**
	**	FRAME
	**	============================================
	**	PC FRAME用 直近イベントキャラクターランキング
	**
	************************************************/

	}elseif($data['page'] == "frame"){

		$event_id								= 0;

		# ランキング名をMemcachedでストックしておく、なくなっていればDBに問い合わせ直してストック
		$mc = new Memcached();
		$mc->addServer(MEMCACHED_SERVER, 11211);
		$ranking_frame_id = MEMCACHED_RANKING_FRAME_KEY;
		

		if( $cachedata = $mc->get($ranking_frame_id) ){
			//キャッシュデータがある場合の処理
			//echo "From Cache.<br>";
			$event_title = $cachedata["event_title"];
			$event_id = $cachedata["event_id"];
			$ranking_list = $cachedata["ranking_list"];
		}else{
			//echo "From Database.<br>";

			$nowtime								= date("YmdHis");

			$events_conditions						= array();
			$events_conditions						= array(
				'date_s'							=> $nowtime,
				'type'								=> 1,
				'status'							=> 0,
				'order'								=> 'date_e DESC'
			);
			$events_rtn								= $eventModel->getEventList($events_conditions);
	
			$i=0;
			while($events_data = $database->fetchAssoc($events_rtn)){
	
				# 開催中イベント
				if($events_data['date_s'] <= $nowtime && $events_data['date_e'] >= $nowtime){
	
					$event_list['id']			= $events_data['id'];
					$event_list['name']			= $events_data['name'];
					break;
	
				# それ以外
				}else{
	
					continue;
	
				}
	
			}
	
			$database->freeResult($events_rtn);
	
			# イベント情報
			if(empty($event_list['id']) || $event_list['id'] == 0){
				$event_title						= "総合ランキング";
			}else{
				$event_title						= $event_list['name']." ランキング";
				$event_id							= $event_list['id'];
			}

			# トータルポイント
			$open_flg								= "1";

			$point_conditions						= array();
			$point_conditions						= array(
				'event_id'							=> $event_id,
				'status'							=> 0,
				'open_flg'							=> $open_flg,
				'open_in'							=> 1,
				'limit'								=> 1,
				'group'								=> 'r.event_id'
			);

			$point_column							= "SUM(r.point) AS total_point";
			$point_data								= $rankingModel->getRankingDataJoinOnMembers($point_conditions,$point_column);

			# キャラ毎の順位で取得
			$rankings_conditions					= array();
			$rankings_conditions					= array(
				'event_id'							=> $event_id,
				'status'							=> 0,
				'open_flg'							=> $open_flg,
				'open_in'							=> 1,
				'order'								=> 'ranking_point DESC, r.character_id',
				'group'								=> 'r.character_id'
			);

			$rankings_column						= "SUM(r.point) as ranking_point, r.character_id, m.id as members_id, m.nickname, m.naruto";
			$rankings_rtn							= $rankingModel->getRankingListJoinOnMembers($rankings_conditions,$rankings_column);
			
			$i=0;
			while($rankings_data = $database->fetchAssoc($rankings_rtn)){
	
				# キャラ情報取得
				$character_data						= $memberModel->getMemberDataById($rankings_data['character_id'],1,"id,nickname,naruto,media_flg");
	
				if($character_data['naruto'] > 0){
					$character_id					= $character_data['naruto'];
				}else{
					$character_id					= $character_data['id'];
				}
	
				# キャラ サムネイル画像取得
				$attaches_conditions				= array();
				$attaches_conditions				= array(
					'user_id'						=> $character_id,
					'category'						=> $thumbnail_image_category,
					'use_flg'						=> 1,
					'pay_count'						=> 0,
					'device'						=> $device_number,
					'status'						=> 1,
					'limit'							=> 1,
					'group'							=> NULL
				);
				$attaches_data						= $attachModel->getAttachData($attaches_conditions);
	
				# 割合計算
				if($rankings_data['ranking_point'] == 0){
	
					$percent						= 0;
	
				}else{
	
					$percent_result					= ($rankings_data['ranking_point'] / $point_data['total_point']) * 100;
					$percent						= round($percent_result,1);
					$percent_width					= round($percent_result,0);
	
				}
	
				# 順位
				$ranking_number						= $i + 1;
	
				# 同順位チェック
				if($i > 0 && isset($check_point)){
	
					if($check_point == $rankings_data['ranking_point']){
						$ranking_number				= $ranking_number - 1;
					}
	
				}
	
				$ranking_list['character_id'][$i]	= $character_id;
				$ranking_list['name'][$i]			= $character_data['nickname'];
				$ranking_list['media_flg'][$i]		= $character_data['media_flg'];
				$ranking_list['ranking'][$i]		= $ranking_number;
				$ranking_list['point'][$i]			= $rankings_data['ranking_point'];
				$ranking_list['percent'][$i]		= $percent;
				$ranking_list['percent_width'][$i]	= $percent_width;
	
				# サムネイル画像
				if(!empty($attaches_data)){
					$ranking_list['image'][$i]		= $attaches_data['attached'];
				}
	
				$check_point						= $rankings_data['ranking_point'];
	
				$i++;
	
			}
	
			$database->freeResult($rankings_rtn);
	
			# CLOSE DATABASE
			//$database->closeDb();
			//$database->closeStmt();
			
			//キャッシュデータがない場合の処理
			if(isset($ranking_list['character_id'])){
				$cachedata = array("event_title"=>$event_title, "event_id"=>$event_id, "ranking_list"=>$ranking_list);
				$res = $mc->set("ranking_frame_id", $cachedata, 120);
			}
		}

		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();

		$view_directory								= $mainClass->getViewDirectory($directory,$data['page'],$default_device);

		# 読み込み
		include_once($view_directory);

		exit();


	}


# ERROR
}else{

	$error										= 1;
	$errormessage								= "該当のランキング情報がありません<br />";

}


################################## FILE END #####################################
?>