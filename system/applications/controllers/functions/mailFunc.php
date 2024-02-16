<?php
## mailController用関数
/********************************************************
**
**	mailFun.php
**	-----------------------------------------------------
**	controllers/mailController.php用関数群
**	-----------------------------------------------------
**	2017.10.23 A.cos
*********************************************************/

## ※関数を作る際の注意
## - なるべく単一の役割を担わせること（DB問い合わせとビュー部分の作成、とかは同一関数内ではなるべくやってはならない）
## -

/************************************************
**
**	ランキングポイント加算
**	============================================
**	
**
************************************************/
# $event_id:イベントID
# $consum_ranking_point：加算応援ポイント
# $user_data:ユーザーデータ
# $character_data：キャラクターデータ
# $first_parent_flg:１なら初回で親用の処理、キャラのmemberデータのuser_countに1を加算する
function addRankingPoint($event_id, $consum_ranking_point, $user_data, $character_data, $first_parent_flg=0){
	global $database;
	global $rankingModel;

	# ランキングテーブルに親でUPDATE
	$ranking_conditions = array();
	$ranking_conditions = array(
		'user_id' => $user_data['id'],
		'character_id' => $character_data['id'],
		'point' => $consum_ranking_point,
		'event_id' => $event_id
	);

	#【INSERT】rankings
	$rankingModel->insertRanking($ranking_conditions);
	
	# UPDATE CHARACTER DATA
	$character_update = array();
	if($first_parent_flg>0){
		$character_update = array(
			'ranking_point' => $character_data['ranking_point'] + $consum_ranking_point
		);
	}else{
		$character_update = array(
			'ranking_point' => $character_data['ranking_point'] + $consum_ranking_point,
			'user_count' => $character_data['user_count'] + 1
		);
	}
	$character_update_where				= "id = :id";
	$character_update_conditions[':id']	= $character_data['id'];

	# 【UPDATE】members
	$database->updateDb("members",$character_update,$character_update_where,$character_update_conditions);
	
	return;
}


/************************************************
**
**	ランキングポイント報酬（応援報酬）、イベント＆キャラで列挙
**	============================================
**	
**
************************************************/
# $db:DBオブジェクト
# $main:mainClassオブジェクト
# $shopModel:shopModelオブジェクト
# $itemModel:itemModelオブジェクト
# $imageModel:imageModelオブジェクト
# $itemboxModel:itemboxModelオブジェクト
# $site_cd：サイトコード
# $event_id：イベントID
# $member_id：ユーザのID
# $character_id:キャラの親ID

function getSupportRewards($site_cd, $event_id, $member_id, $character_id, $members_data){
	
	global $database;
	global $main;
	global $shopModel;
	global $itemModel;
	global $imageModel;
	global $itemboxModel;

	global $point_no_array, $point_name_array;
	global $present_category_array;

	# ランキングポイントの取得ユーザ&キャラ&イベント
	$rankinng_point_sql = "select point from rankings where site_cd=".$site_cd." AND status=0";
	$rankinng_point_sql .= " AND event_id = ".$event_id." AND user_id = ".$member_id." AND character_id=".$character_id;
	$rankinng_point_rtn = $database->query($rankinng_point_sql);
	$rankinng_point = $database->fetchAssoc($rankinng_point_rtn);
	
	# 個人の応援報酬の状態（キャラクター＆イベント）
	//副問い合わせ
	$sub_sql = "SELECT * FROM support_rewards_history WHERE site_cd = ".$site_cd." AND user_id= ".$member_id;
	
	//問い合わせ
	$main_sql = "SELECT sr.id as id, sr.site_cd, sr.event_id as event_id, sr.character_id as character_id, sr.point as point, ";
	$main_sql .= "sr.type as type, sr.target_id as target_id, sr.unit as unit, srh.user_id as user_id, srh.reg_date as reg_date,sr.status as status";
	$main_sql .= " FROM support_rewards AS sr LEFT JOIN (".$sub_sql.") AS srh ON sr.id = srh.rewards_id";
	$main_sql .= " WHERE sr.status=0 AND sr.site_cd = ".$site_cd." AND sr.event_id = ".$event_id." AND sr.character_id = ".$character_id;
	$main_sql .= " AND sr.point <= ".$rankinng_point["point"];
	$main_sql .= " ORDER BY point ASC";
	
	$rewards_rtn = $database->query($main_sql);
	$reward_character = $database->numRows($rewards_rtn);
	
	$rewards_list = array();
	$result = NULL;
	while($rewards = $database->fetchAssoc($rewards_rtn)){
		//既に受け取られている報酬
		if(!empty($rewards["user_id"])){
			continue;
		}else{
			//報酬を受け取った履歴を追加
			$reward_history_sql = "INSERT INTO support_rewards_history (site_cd, rewards_id, user_id, reg_date) ";
			$reward_history_sql .= " VALUES (".$site_cd.", ".$rewards["id"].", ".$member_id.", ".date("YmdHis").")";
			$reward_history_rtn = $database->query($reward_history_sql);
			$error = $database->errorDb("getSupportRewards",$reward_history_rtn->errorCode(),__FILE__,__LINE__);
			if(!empty($error)){
				$result = "Error";
				return $result;
			}
			
			//応援報酬は固定で90日にする。
			$limit_date							= date("YmdHis",strtotime("+90 day"));

			//タイプ別で報酬を受け取る
			switch($rewards['type']){
				case 1:// TICKET
					//直接配布じゃなく、プレゼントBOXを介す
					// チケットデータ取得
					$rewards_data = $shopModel->getShopDataById($rewards['target_id'],"id,type,name,image");

					//プレゼントBOXへ
					$presentbox_insert					= array(
						'site_cd'						=> $members_data['site_cd'],
						'user_id'						=> $members_data['id'],
						'present_id'					=> 0,//応援報酬のチケットは暫定的に"0"としておく
						'acceptance_date'				=> date("YmdHis"),
						'category'						=> $present_category_array['support_rewards_ticket'],//応援報酬のチケットは暫定的に"31"としておく
						'type'							=> $rewards['type'],
						'target_id'						=> $rewards['target_id'],
						'unit'							=> $rewards['unit'],
						'limit_date'					=> $limit_date,
						'status'						=> 0
					);
					break;
				case 2:// ITEM
					//直接配布じゃなく、プレゼントBOXを介す
					//表示用へ渡す
					$rewards_data = $itemModel->getItemDataById($rewards['target_id'],"id,name,image");
					
					//プレゼントBOXへ
					$presentbox_insert					= array(
						'site_cd'						=> $members_data['site_cd'],
						'user_id'						=> $members_data['id'],
						'present_id'					=> 0,//応援報酬のアイテムは暫定的に"-2"としておく
						'acceptance_date'				=> date("YmdHis"),
						'category'						=> $present_category_array['support_rewards_item'],//応援報酬のチケットは暫定的に"32"としておく
						'type'							=> $rewards['type'],
						'target_id'						=> $rewards['target_id'],
						'unit'							=> $rewards['unit'],
						'limit_date'					=> $limit_date,
						'status'						=> 0
					);
					break;
				case 3:// IMAGE
					//20171128 update by A.cos
					//直接配布じゃなく、プレゼントBOXを介す
					//画像データのチェック
					$image_data = $imageModel->getImageDataById($rewards['target_id'],"id,img_name,img_key");
					$rewards_data['id'] = $image_data['id'];
					$rewards_data['name'] = $image_data['img_key'];
					$rewards_data['image'] = $image_data['img_name'];

					//プレゼントBOXへ
					$presentbox_insert					= array(
						'site_cd'						=> $members_data['site_cd'],
						'user_id'						=> $members_data['id'],
						'present_id'					=> 0,//応援報酬の開放シーンは暫定的に"0"としておく
						'acceptance_date'				=> date("YmdHis"),
						'category'						=> 33,//応援報酬のチケットは暫定的に"33"としておく
						'type'							=> $rewards['type'],
						'target_id'						=> $rewards['target_id'],
						'unit'							=> $rewards['unit'],
						'limit_date'					=> $limit_date,
						'status'						=> 0
					);
					break;
			}

			//20171128 update by A.cos
			//直接配布じゃなく、プレゼントBOXを介す
			$insert_id							= $database->insertDb("presentbox",$presentbox_insert);
			
			
			//mail("eikoshi@k-arat.co.jp","getSupportRewards_items", var_export($rewards_data, true),"From:info@kyabaheru.net");
			
			//取得応援ポイントとアイテム個数のセット（表示用）
			$rewards_data["point"] = $rewards['point'];
			$rewards_data["unit"] = $rewards['unit'];
			
			//報酬をうけとったメッセージを返す
			$result[] = $rewards_data;
			
			//mail("eikoshi@k-arat.co.jp","getSupportRewards3",var_export($rewards_data, true),"From:info@kyabaheru.net");
		}
	}
	$database->freeResult($rewards_rtn);
	$database->freeResult($rankinng_point_rtn);
	
	
	return $result;
}	

/************************************************
**
**	レベルアップ時のプレゼントチェック
**
************************************************/
function checkPresentAtLevelUp($parent_id,$members_data,$levelup_bonus_category,
						$favorite_level,$number_ticket, $number_item, $number_image){

	global $database;
	global $presentModel;
	global $presentboxModel;
	global $shopModel;
	global $itemModel;
	global $imageModel;

    # 初期化
	$acceptance_check = NULL;
	
	$present_check = NULL;
	$present_dialog = NULL;
	$present_message = "";
    # プレゼントチェック
    $presents_conditions = array(
        'category' => $levelup_bonus_category,
        'character_id' => $parent_id,
        'level' => $favorite_level,
        'status' => 0
    );

    $presents_count = $presentModel->getPresentCount($presents_conditions);

    if($presents_count > 0){

        $presents_rtn = $presentModel->getPresentList($presents_conditions);

        $i=0;
        while($data = $database->fetchAssoc($presents_rtn)){

            $acceptance_conditions = array();
            $acceptance_conditions = array(
                'user_id' => $members_data['id'],
                'present_id' => $data['id'],
                'limit' => 1
            );

            $acceptance_check					= $presentboxModel->getPresentboxCount($acceptance_conditions);

            # 受け取り済み
            if(!empty($acceptance_check)){
                continue;
            }

            # TICKET
            if($data['type'] == $number_ticket){

                $present_data					= $shopModel->getShopDataById($data['target_id'],"id,name,image");

            # ITEM
            }elseif($data['type'] == $number_item){

                $present_data					= $itemModel->getItemDataById($data['target_id'],"id,name,image");

            # IMAGE
            }elseif($data['type'] == $number_image){

                $image_data						= $imageModel->getImageDataById($data['target_id'],"id,img_name,img_key");
                $present_data['id']				= $image_data['id'];
                $present_data['name']			= $image_data['img_key'];
                $present_data['image']			= $image_data['img_name'];

            }
            
            $limit_date							= date("YmdHis",strtotime("+".$data['limit_date']." day"));

            $presentbox_insert					= array(
                'site_cd'						=> $members_data['site_cd'],
                'user_id'						=> $members_data['id'],
                'present_id'					=> $data['id'],
                'acceptance_date'				=> date("YmdHis"),
                'category'						=> $data['category'],
                'type'							=> $data['type'],
                'target_id'						=> $data['target_id'],
                'unit'							=> $data['unit'],
                'limit_date'					=> $limit_date,
                'status'						=> 0
            );

            $insert_id							= $database->insertDb("presentbox",$presentbox_insert);

            # ERROR 吐いたら処理止め
            if(empty($insert_id)){
                $i								= 0;
                $error							= 1;
                $present_check					= NULL;
                $present_message				= NULL;
                //$present_message				= array();
                break;
            }

            # PRESENT 表示生成
            if(!empty($data['message'])){
                $present_message				.= $data['message']."<br />";
                $present_message				.= $present_data['name']." × ".$data['unit']."<br />";
            }else{
                $present_message			 	.= $present_data['name']." × ".$data['unit']."<br />";
            }

            $i++;

        }

        if($i > 0 && !empty($present_message)){
            $present_check						= $i;
            $present_dialog						= $present_message."をプレゼントBOXにお届けしました！";
        }

        $database->freeResult($presents_rtn);

        return array($present_check, $present_dialog);
    }

    return array(NULL, NULL);

}

/************************************************
**
**	レベルアップ時の称号チェック
**
************************************************/
function checkDegreeAtLevelUp($parent_id, $before_level, $favorite_level, $mailusers_data){

	global $degreeModel;

    # mailusersに個別称号がない場合のみ処理
    if(empty($mailusers_data['degree_name'])){

        # まずレベルアップ前の称号を取得
        $before_conditions					= array();
        $before_conditions					= array(
            'character_id'					=> $parent_id,
            'level'							=> $before_level
        );

        $before_data						= $degreeModel->getDegreeData($before_conditions,"id,name");

        # OK
        if(!empty($before_data['id'])){

            # レベルアップ後の称号を取得
            $after_conditions				= array();
            $after_conditions				= array(
                'character_id'				=> $parent_id,
                'level'						=> $favorite_level
            );

            $after_data						= $degreeModel->getDegreeData($after_conditions,"id,name");

            # OK
            if(!empty($after_data['id'])){

                # 比較
                if($before_data['id'] != $after_data['id']){

                    $degree_up_name			 = $after_data['name'];
                    $degree_up_message		 = DEGREE_NAME."がアップしました！<br />";
					$degree_up_message		.= "<span class=\"style-red\">【".$before_data['name']."】→【".$after_data['name']."】</span><br /><br />";

					return array($degree_up_name, $degree_up_message);

                }

            }

        }
	}
	
	return array(NULL, NULL);
}

/************************************************
**
**	レベルアップ時の画像チェック
**	============================================
**	attachesのlevel_sに該当画像があれば差し替え
**
************************************************/
function checkImageAtLevelUp($character_data, $attaches_category, 
    $before_level, $consumption_level, $device_number, $display_image){
	
	global $database;
	global $attachModel;

	$animation_image = "";

    # 平常モード時のみ切り替え
    if($character_data['word'] == 0){

        # 子キャラなら
        if(!empty($character_data['naruto'])){

            $attaches_user_id				= $character_data['naruto'];

        # 親キャラなら
        }else{

            $attaches_user_id				= $character_data['id'];

        }
		//mail("eikoshi@k-arat.co.jp","画像チェック１", "attaches_user_id=".$attaches_user_id,"From:info@kyabaheru.net");

        # レベルの上がり幅分ひとつずつ該当画像があるかチェック
        for($i=0;$i<$consumption_level;$i++){

            # 好感度レベル
            $before_level++;

            # レベルアップ後のfavirite_levelでattachesのlevel_sが設定されてたら
            $attaches_check_conditions		= array();
            $attaches_check_conditions		= array(
                'user_id'					=> $attaches_user_id,
                'category'					=> $attaches_category,
                'use_flg'					=> 1,
                'level_s'					=> $before_level,
                'device'					=> $device_number,
                'status'					=> 1,
                'order'						=> 'pay_count',
                'limit'						=> 1,
                'group'						=> NULL
            );
            $check_attaches					= $attachModel->checkAttachByLevel($attaches_check_conditions);

			//mail("eikoshi@k-arat.co.jp","画像チェック2", "check_attaches=".$check_attaches,"From:info@kyabaheru.net");
		
            # 新しい画像あれば
            if($check_attaches > 0){

                # ATTACHES
                $attaches_conditions		= array();
                $attaches_conditions		= array(
                    'user_id'				=> $attaches_user_id,
                    'category'				=> $attaches_category,
                    'use_flg'				=> 1,
                    'level'					=> $before_level,
                    'device'				=> $device_number,
                    'status'				=> 1,
                    'order'					=> 'pay_count',
                    'limit'					=> NULL,
                    'group'					=> NULL
                );
                $attaches_rtn				= $attachModel->getAttachList($attaches_conditions);

                $i=0;
                while($attaches_data = $database->fetchAssoc($attaches_rtn)){

                    if($i == 0){
                        $animation_image	= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";
                    }

                    $display_image			.= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";

                    $i++;

                }

				//mail("eikoshi@k-arat.co.jp","画像チェック3", "attaches_count=".$attaches_count,"From:info@kyabaheru.net");
                # 画像枚数
                $attaches_count				= $i;

                $database->freeResult($attaches_rtn);

				# 画像ランクアップメッセージ
				//20190218 update by A.cos
				$image_up_message			 = "";
                //$image_up_message			 = "キャラクター画像がランクアップ！<br /><br />";

				# ループストップ
				return array($animation_image, $display_image, $attaches_count, $image_up_message);
                //break;

            }

        }

    }

    return array(NULL, NULL, NULL, NULL);

}

/************************************************
**
**	アイテム使用チェック
**	--------------------------------------------
**	開封処理では【応援ポイント効果】のみ
**	$items_data['effect']	= 2
**
************************************************/
function checkItemUse($members_data, $parent_id, $campaign_id, $campaign_data,
						$consumption_favorite, $consumption_ranking, $item_end_count){
	global $database;
	global $itemuseModel;
	global $itemModel;

	$itemuse_list							= NULL;
	$itemuse_list							= array();

	$itemuse_conditions						= NULL;
	$itemuse_conditions						= array(
		'user_id'							=> $members_data['id'],
		'character_id'						=> $parent_id,
		'status'							=> 0
	);
	$itemuse_rtn							= $itemuseModel->getItemuseList($itemuse_conditions);

	$itemuse_count							= 0;

	$itemend_list = array();

	while($itemuse_data = $database->fetchAssoc($itemuse_rtn)){
	
		/************************************************
		**
		**	itemsから効果取得 / ひとつだけ取得(複数設定は基本ない)
		**
		************************************************/
		# 初期化
		$items_data							= NULL;
		$items_data							= array();
	
		#アイテム情報取得
		$items_data							= $itemModel->getItemDataById($itemuse_data['item_id']);

		# キャンペーン中に効果変動設定してあるアイテムがあるか(アイテム効果変動はcampaign_type = 3)
		if(!empty($campaign_id) && $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
			$items_campaign_conditions		= array();
			$items_campaign_conditions		= array(
				'item_id'					=> $itemuse_data['item_id'],
				'campaign_id'				=> $campaign_id,
				'status'					=> 0,
				'order'						=> 'id'
			);
			
			$items_campaign_data			= $itemModel->getItemData($items_campaign_conditions);
			# キャンペーンアイテムがあれば情報上書き
			if(!empty($items_campaign_data['id'])){
				
				if(!empty($items_campaign_data['description'])){
					$items_data['description']		= $items_campaign_data['description'];
				}
	
				if(!empty($items_campaign_data['message'])){
					$items_data['message']			= $items_campaign_data['message'];
				}
	
				if(!empty($items_campaign_data['word'])){
					$items_data['word']				= $items_campaign_data['word'];
				}
	
				if(!empty($items_campaign_data['count'])){
					$items_data['count']			= $items_campaign_data['count'];
				}
	
				if(!empty($items_campaign_data['limit_date'])){
					$items_data['limit_date']		= $items_campaign_data['limit_date'];
				}
	
				if(!empty($items_campaign_data['magnification'])){
					$items_data['magnification']	= $items_campaign_data['magnification'];
				}
	
			}
	
		}

		# アイテムデータHITしたら
		if(!empty($items_data['id'])){
			
			# 返信画面使用アイテムじゃなかったら除外
			if($items_data['category'] != 0 && $items_data['category'] != 1){
				continue;
			}
/*
			# 閲覧の場合、応援ポイント効果以外だったら除外
			if($use == "read"){
				if($items_data['effect'] != 2){
					continue;
				}
			}
*/
			# 計算データ
			$calculation_array				= array();
			$calculation_array				= array(
				'favorite'					=> $consumption_favorite,
				'ranking'					=> $consumption_ranking,
				'end'						=> NULL
			);

			# itemuseModel内にて計算
			$calculation_data				= $itemuseModel->calculatePoint($itemuse_data,$items_data,$calculation_array);
			$consumption_favorite			= $calculation_data['favorite'];
			$consumption_ranking			= $calculation_data['ranking'];

			# 使い終わったアイテム
			if(!empty($calculation_data['end'])){
				$itemend_list['end'][$item_end_count]		= $calculation_data['end'];
				$itemend_list['end_id'][$item_end_count]	= $calculation_data['end_id'];
				$item_end_count++;
			}

		}

		$itemuse_count++;
		
	}

	$database->freeResult($itemuse_rtn);

	# まだ使用中のアイテムがあるかチェック
	//$item_using_count						= $itemuseModel->getItemuseCount($itemuse_conditions);
	$item_using_count						= $itemuse_count - $item_end_count;
	if($item_using_count < 0){
		$item_using_count					= 0;
	}

	return array($item_using_count, $item_end_count, $itemend_list, $consumption_favorite, $consumption_ranking);
}


/************************************************
**
**	メールプレゼント
**	============================================
**	
**
************************************************/
# $database:DBオブジェクト
# $dialog_message：ダイアログに出すメッセージ
# $members_data：ユーザデータ
# $mails_data_present_type：プレゼントタイプ（１：PP、２：アイテム、３：ライブチケット）
# $mails_data_present_targetid：プレゼントのID（１：shopsテーブル、２：itemsテーブル、３：imagesテーブル）
# $mails_data_present_unit：プレゼント配布数

function sendMailPresent($dialog_message, $members_data, $mails_data_present_type, $mails_data_present_targetid, $mails_data_present_unit){
	//外部定数＆クラスインスタンス
	global $database;
	global $shopModel,$itemModel,$imageModel;
	global $present_category_array;

	//応援報酬は固定で90日にする。
	$limit_date = date("YmdHis",strtotime("+90 day"));
	
	if($mails_data_present_type){
		switch($mails_data_present_type){
			case 1:// TICKET
				//直接配布じゃなく、プレゼントBOXを介す
				// チケットデータ取得
				$mail_present_data = $shopModel->getShopDataById($mails_data_present_targetid,"id,type,name,image");

				//プレゼントBOXへ
				$presentbox_insert					= array(
					'site_cd'						=> $members_data['site_cd'],
					'user_id'						=> $members_data['id'],
					'present_id'					=> 0,//暫定的に"0"としておく
					'acceptance_date'				=> date("YmdHis"),
					'category'						=> $present_category_array['mail_present_ticket'],//応援報酬のチケットは暫定的に"41"としておく
					'type'							=> $mails_data_present_type,
					'target_id'						=> $mails_data_present_targetid,
					'unit'							=> $mails_data_present_unit,
					'limit_date'					=> $limit_date,
					'status'						=> 0
				);
				break;
			case 2:// ITEM
				//直接配布じゃなく、プレゼントBOXを介す
				//表示用へ渡す
				$mail_present_data = $itemModel->getItemDataById($mails_data_present_targetid,"id,name,image");
				
				//プレゼントBOXへ
				$presentbox_insert					= array(
					'site_cd'						=> $members_data['site_cd'],
					'user_id'						=> $members_data['id'],
					'present_id'					=> 0,//暫定的に"0"としておく
					'acceptance_date'				=> date("YmdHis"),
					'category'						=> $present_category_array['mail_present_item'],//応援報酬のチケットは暫定的に"42"としておく
					'type'							=> $mails_data_present_type,
					'target_id'						=> $mails_data_present_targetid,
					'unit'							=> $mails_data_present_unit,
					'limit_date'					=> $limit_date,
					'status'						=> 0
				);
				break;
			case 3:// IMAGE
				//直接配布じゃなく、プレゼントBOXを介す
				//画像データのチェック
				$image_data = $imageModel->getImageDataById($mails_data_present_targetid,"id,img_name,img_key");
				$mail_present_data['id'] = $image_data['id'];
				$mail_present_data['name'] = $image_data['img_key'];
				$mail_present_data['image'] = $image_data['img_name'];

				//プレゼントBOXへ
				$presentbox_insert					= array(
					'site_cd'						=> $members_data['site_cd'],
					'user_id'						=> $members_data['id'],
					'present_id'					=> 0,//暫定的に"0"としておく
					'acceptance_date'				=> date("YmdHis"),
					'category'						=> $present_category_array['mail_present_picture'],//応援報酬のチケットは暫定的に"43"としておく
					'type'							=> $mails_data_present_type,
					'target_id'						=> $mails_data_present_targetid,
					'unit'							=> $mails_data_present_unit,
					'limit_date'					=> $limit_date,
					'status'						=> 0
				);
				break;
		}

		if(!empty($presentbox_insert)){
			$mail_present_insert_id							= $database->insertDb("presentbox",$presentbox_insert);

		
			if(!empty($mail_present_insert_id)){
				$dialog_message .= "<div style=\"text-align: center;\">";
				$dialog_message .= "<img src=\"".HTTP_ITEM_IMAGE."/".$mail_present_data["image"]."\" height=60 ><br/>";
				switch($mails_data_present_type){
					case 1:// TICKET
						$dialog_message .= $mail_present_data['name']."&nbsp;".$mails_data_present_unit."枚をプレゼントBOXにお送りしました。<br/>";
						break;
					case 2:// ITEM
						$dialog_message .= $mail_present_data['name']."&nbsp;".$mails_data_present_unit."個をプレゼントBOXにお送りしました。<br/>";
						break;
					case 3:// IMAGE
						$dialog_message .= $mail_present_data['name']."をプレゼントBOXにお送りしました。<br/>";
						break;
				}
				
				$dialog_message .= "</div><br/>";
				$dialog_message .= "<br/>";
			}else{//ERROR吐いたらエラーメッセージ
				$dialog_message .= "<div style=\"text-align: center;\">";
				$dialog_message .= "<img src=\"".HTTP_ITEM_IMAGE."/".$mail_present_data["image"]."\"><br/>";
				$dialog_message .= "通信エラーがあったため".$mail_present_data['name']."はお渡しできませんでした。お問い合わせフォームからご連絡ください。<br/>";
				$dialog_message .= "</div><br/>";
				$dialog_message .= "<br/>";
			}
		}
	}

	return $dialog_message;

}


/**************************************************
**
**	checkPointConsume_kyabaheru_sp
**	----------------------------------------------
**	持ちポイント足りるかチェック
**
**************************************************/

function checkPointConsume_kyabaheru_sp($point_no_id,$members_data,$character_data){

	# ERROR
	if(empty($point_no_id)){
		return FALSE;
	}

	# POINT NUMBER / NAME ARRAY
	global $database;
	global $point_no_array;
	global $point_name_array;

	# RESULT
	$result					= array();

	# CHECK POINT 初期化
	$check_point			= 0;

	# 送信NO
	$point_no_send			= $point_no_array[$point_name_array['send']][2];

	# 開封NO
	$point_no_read			= $point_no_array[$point_name_array['read']][2];

	# 消費ポイントNo
	$point_no_count			= explode(',',$point_no_id);

	# ポイントデータ配列
	$point_data = array();

	# 親ID
	if($character_data['naruto'] == 0){
		$parent_id							= $character_data['id'];
	}else{
		$parent_id							= $character_data['naruto'];
	}

	for($i=0;$i<count($point_no_count);$i++){

		# 現在のキャラクターのスケジュールデータ（キャラクターID、消費ポイントID）
		$schedules_table	= "schedules";
		$schedules_select	= "character_id, cp_id";
		$schedules_where	= "site_cd = :site_cd AND character_id = :character_id AND del_flg = :del_flg";
		$schedules_array = array();
		$schedules_array[':site_cd'] = $members_data["site_cd"];
		$schedules_array[':character_id'] = $parent_id;
		$schedules_array[':del_flg'] = 0;
		$schedules_rtn		= $database->selectDb($schedules_table,$schedules_select,$schedules_where,$schedules_array,$schedules_order=NULL,1);
		$database->errorDb($schedules_table, $schedules_rtn->errorCode(),__FILE__,__LINE__);
		
		$schedules_data = $database->fetchAssoc($schedules_rtn);
		
		$cp_table	= "consume_points";
		$cp_select	= "character_id, send_point, recv_point";
		$cp_where	= "id = :id";
		$cp_array = array();
		$cp_array[':id'] = $schedules_data['cp_id'];
		$cp_rtn		= $database->selectDb($cp_table,$cp_select,$cp_where,$cp_array,$cp_order=NULL,1);
		$database->errorDb($cp_table,$cp_rtn->errorCode(),__FILE__,__LINE__);
		$cp_data		= $database->fetchAssoc($cp_rtn);

		if(!empty($cp_data)){
			# 受信
			if ($point_no_count[$i] == $point_no_read) {
				$point_data[$i]['point_no_id']	= $point_no_read;
				$point_data[$i]['point']		= $cp_data["recv_point"];
			}
			# 送信
			if ($point_no_count[$i] == $point_no_send) {
				$point_data[$i]['point_no_id']	= $point_no_send;
				$point_data[$i]['point']		= $cp_data["send_point"];
			}
		}else{ # 設定がなかったら
			# 受信
			if ($point_no_count[$i] == $point_no_read) {
				$point_data[$i]['point_no_id']	= $point_no_read;
				$point_data[$i]['point']		= DEFAULT_READ_POINT;
			}
			# 送信
			if ($point_no_count[$i] == $point_no_send) {
				$point_data[$i]['point_no_id']	= $point_no_send;
				$point_data[$i]['point']		= DEFAULT_SEND_POINT;
			}
		}

		# 消費POINT加算
		if(isset($point_data[$i]['point']) && $point_data[$i]['point']){
			$check_point				+= $point_data[$i]['point'];
		}

	}

	# 持ちポイントが消費ポイントより多ければOK
	if($members_data['total_point'] >= $check_point){
		$result							= $point_data;
	}

	return $result;
}

/**************************************************
**
**	makePointConsume_kyabaheru_sp
**	----------------------------------------------
**	ポイント消費計算メソッド
**
**  ※pointModel->makePointConsume()と同じ。あとで改変が加わることを想定して分けておく
**
**************************************************/
function makePointConsume_kyabaheru_sp($point_data,$members_data){

	# ERROR
	if(empty($point_data) || empty($members_data)){
		return FALSE;
	}

	# ERROR 初期化
	$error												= NULL;
	$errormessage										= NULL;

	# ユーザー持ちポイント(課金)
	$user_point											= $members_data['point'];

	# ユーザー持ちポイント(サービス配布)
	$user_s_point										= $members_data['s_point'];

	# ユーザー持ちポイント(ログイン無料配布)
	$user_f_point										= $members_data['f_point'];

	# 繰越サービスポイント初期化
	$takeover_s_point									= 0;

	# 繰越課金ポイント初期化
	$takeover_point										= 0;

	# トータル消費ポイント初期化
	$consumption_point									= 0;

	# UPDATE CHECK FLG
	$update_point_flg									= NULL;
	$update_s_point_flg									= NULL;
	$update_f_point_flg									= NULL;

	# リザルト初期化
	$result												= array();


	/************************************************
	**
	**	消費ポイント計算
	**
	************************************************/

	$i=0;
	foreach($point_data as $key => $value){

		# トータル消費ポイント計算
		$consumption_point								+= $value['point'];

		# point_no_id
		$point_no_check									= $value['point_no_id'];

		# points INSERT用
		$result['points'][$i]['point_no_id']			= $point_no_check;

		# 消費ポイント0ならpoints用のデータだけ作ってcontinue / 無料ポイント扱いなので point_type は 2
		if($value['point'] == 0 || empty($value['point'])){
			$result['points'][$i]['point'][2]			= 0;
			continue;
		}

		# 持ち無料ポイントが消費ポイントより少なかった場合
		if ($user_f_point < $value['point']) {


			# 元々f_pointがゼロならここの処理はしない
			if($user_f_point == 0){

				$takeover_s_point						= $value['point'];

			# 差分処理
			}else{

				# 繰越サービス配布ポイント
				$takeover_s_point						= $value['point'] - $user_f_point;

				# points INSERT用
				$result['points'][$i]['point'][2]		= $user_f_point;

				# f_point
				$user_f_point							= 0;

				# UPDATE FLG
				$update_f_point_flg						= 1;

			}


			# 持ちサービス配布ポイントが繰越サービス配布ポイントより少なかった場合
			if($user_s_point < $takeover_s_point){

				# 繰越課金ポイント
				$takeover_point							= $takeover_s_point - $user_s_point;

				# 元々s_pointがゼロならここの処理はしない
				if($user_s_point == 0){

					# 繰越課金ポイント
					$takeover_point						= $takeover_s_point;

				# 差分処理
				}else{

					# 繰越課金ポイント
					$takeover_point						= $takeover_s_point - $user_s_point;

					# points INSERT用
					$result['points'][$i]['point'][1]	= $user_s_point;

					# s_point
					$user_s_point						= 0;

					# UPDATE FLG
					$update_s_point_flg					= 1;

				}


				# 持ち課金ポイントが繰越課金ポイントより少なかった場合(基本有り得ないけど念のため)
				if($user_point < $takeover_point){

					$error								= 2;
					$errormessage						= TICKET_NAME."が足りません。";
					break;

				# 課金ポイント & サービス配布ポイント & ログイン無料配布ポイント 処理
				}else{

					# point
					$user_point							= $user_point - $takeover_point;

					# points INSERT用
					$result['points'][$i]['point'][0]	= $takeover_point;

					# UPDATE FLG
					$update_point_flg					= 1;

				}

			# サービス配布ポイント & ログイン無料配布ポイント 処理
			}else{

				# s_point
				$user_s_point							= $user_s_point - $takeover_s_point;

				# points INSERT用
				$result['points'][$i]['point'][1]		= $takeover_s_point;

				# UPDATE FLG
				$update_s_point_flg						= 1;

			}

		# ログイン無料配布ポイントのみで処理
		}else{

			# f_point
			$user_f_point								= $user_f_point - $value['point'];

			# UPDATE FLG
			$update_f_point_flg							= 1;

			# points INSERT用
			$result['points'][$i]['point'][2]			= $value['point'];

		}

		$i++;

	}


	# ERROR
	if(!empty($error)){

		$result['error']									= $error;
		$result['errormessage']								= $errormessage;

	}else{

		if(!empty($update_point_flg)){
			$result['members']['point']						= $user_point;
		}

		if(!empty($update_s_point_flg)){
			$result['members']['s_point']					= $user_s_point;
		}

		if(!empty($update_f_point_flg)){
			$result['members']['f_point']					= $user_f_point;
		}

		# TOTAL
		$result['consumption_point']						= $consumption_point;

	}


	return $result;


}

/************************************************
**
**	各種ポイントデータチェック
**	--------------------------------------------
**	めるぺろシリーズのデフォのやり方（きゃばへるでは使わない）
**	
**
************************************************/
function checkPointData($mail_status, $members_data, $campaign_id){
	global $point_no_array;
	global $point_name_array;

	# POINT NO
	$point_no_send								= $point_no_array[$point_name_array['send']][2];
	$point_no_read								= $point_no_array[$point_name_array['read']][2];
	$point_no_image								= $point_no_array[$point_name_array['image']][2];
	$point_no_id								= $point_no_send.",".$point_no_read.",".$point_no_image;

	# pointsets
	$pointsets_data								= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

	# 初期化
	$point_data['send']							= DEFAULT_SEND_POINT;
	$point_data['read']							= DEFAULT_READ_POINT;
	$point_data['image']						= DEFAULT_IMAGE_POINT;

	if(!empty($pointsets_data)){

		$count									= count($pointsets_data);
		for($i=0;$i<$count;$i++){

			# 送信
			if($pointsets_data[$i]['point_no_id'] == $point_no_send){
				$point_data['send']				= $pointsets_data[$i]['point'];
			}

			# 開封
			if($pointsets_data[$i]['point_no_id'] == $point_no_read){
				$point_data['read']				= $pointsets_data[$i]['point'];
			}

			# 画像閲覧
			if($pointsets_data[$i]['point_no_id'] == $point_no_image){
				$point_data['image']			= $pointsets_data[$i]['point'];
			}

		}

	}

	# 送信無料上書き
	if(!empty($mail_status['send'])){

		$point_data['send']						= 0;

	# 受信無料上書き
	}elseif(!empty($mail_status['read'])){

		$point_data['read']						= 0;
		$point_data['image']					= 0;

	}

	# $mail_status 再設定 / キャンペーンでゼロ設定がある為
	if($point_data['send'] == 0 && empty($mail_status['send'])){
		$mail_status['send']					= 1;
	}

	if($point_data['read'] == 0 && empty($mail_status['read'])){
		$mail_status['read']					= 1;
		$point_data['image']					= 1;
	}

	if(!empty($mail_status['send']) && !empty($mail_status['read']) && empty($mail_status['all'])){
		$mail_status['all']						= 1;
	}

	return array($mail_status, $point_data);
}


/************************************************
**
**	各種ポイントデータチェック
**	--------------------------------------------
**	きゃばへるのデフォのやり方
**	親データからデフォ消費を引き出して充てておく
**
************************************************/
function checkPointData_Kyabaheru($members_data,$character_data){


	# POINT NUMBER / NAME ARRAY
	global $database;

	# ポイントデータ配列
	$point_data = array();

	# 親ID
	if($character_data['naruto'] == 0){
		$parent_id							= $character_data['id'];
	}else{
		$parent_id							= $character_data['naruto'];
	}

	# 現在のキャラクターのスケジュールデータ（キャラクターID、消費ポイントID）
	$schedules_table	= "schedules";
	$schedules_select	= "character_id, cp_id";
	$schedules_where	= "site_cd = :site_cd AND character_id = :character_id AND del_flg = :del_flg";
	$schedules_array = array();
	$schedules_array[':site_cd'] = $members_data["site_cd"];
	$schedules_array[':character_id'] = $parent_id;
	$schedules_array[':del_flg'] = 0;
	$schedules_rtn		= $database->selectDb($schedules_table,$schedules_select,$schedules_where,$schedules_array,$schedules_order=NULL,1);
	$database->errorDb($schedules_table, $schedules_rtn->errorCode(),__FILE__,__LINE__);
	
	$schedules_data = $database->fetchAssoc($schedules_rtn);
	
	$cp_table	= "consume_points";
	$cp_select	= "character_id, send_point, recv_point";
	$cp_where	= "id = :id";
	$cp_array = array();
	$cp_array[':id'] = $schedules_data['cp_id'];
	$cp_rtn		= $database->selectDb($cp_table,$cp_select,$cp_where,$cp_array,$cp_order=NULL,1);
	$database->errorDb($cp_table,$cp_rtn->errorCode(),__FILE__,__LINE__);
	$cp_data		= $database->fetchAssoc($cp_rtn);

	if(!empty($cp_data)){
		$point_data['send'] = $cp_data["send_point"];
		$point_data['read'] = $cp_data["recv_point"];
		$point_data['image'] = DEFAULT_IMAGE_POINT;
	}else{ # 設定がなかったら
		$point_data['send'] = DEFAULT_SEND_POINT;
		$point_data['read'] = DEFAULT_READ_POINT;
		$point_data['image'] = DEFAULT_IMAGE_POINT;
	}

	return $point_data;
}


/************************************************
**
**	キャラクターのスケジュールを取得
**	--------------------------------------------
**	20181022 add by A.cos
**	
**
************************************************/
function getCharacterSchedule($members_data,$parent_id){
	# POINT NUMBER / NAME ARRAY
	global $database;

	# 現在のキャラクターのスケジュールデータ（キャラクターID、消費ポイントID）
	$schedules_table	= "schedules";
	$schedules_select	= "id,site_cd, character_id, reset_date";
	$schedules_where	= "site_cd = :site_cd AND character_id = :character_id AND del_flg = :del_flg";
	//$schedules_where	= "site_cd = :site_cd AND character_id = :character_id AND del_flg = :del_flg AND presense<2";
	$schedules_array = array();
	$schedules_array[':site_cd'] = $members_data["site_cd"];
	$schedules_array[':character_id'] = $parent_id;
	$schedules_array[':del_flg'] = 0;
	$schedules_rtn		= $database->selectDb($schedules_table,$schedules_select,$schedules_where,$schedules_array,$schedules_order=NULL,1);
	$database->errorDb($schedules_table, $schedules_rtn->errorCode(),__FILE__,__LINE__);
	$schedules_data = $database->fetchAssoc($schedules_rtn);

	return $schedules_data;
}

/************************************************
**
**	checkMailsPastExist
**	--------------------------------------------
**	20190218 add by A.cos
**	# pamameter
**	$members_data:会員情報
**
**	# return
**	$array_exit:サービス開始年から今年までのメール分離テーブルの有無を納めた配列
**
************************************************/
function checkMailsPastExist($members_data){
	# POINT NUMBER / NAME ARRAY
	global $database;

	$array_exit = array();
	for($year_past=intVal(date("Y")); $year_past>=intVal(SITE_OPEN_YEAR); $year_past--){
		if($members_data["reg_date"] <= intVal($year_past."1231235959")){
			$rows_exist = 0;
			$sql_exist = "SHOW TABLES LIKE 'mails_past_".$year_past."'";
			$rtn_exist = $database->query($sql_exist);
			$database->errorDb("CHECK EXIST TABLE 4 PAST",$rtn_exist->errorCode(),__FILE__,__LINE__);
			$rows_exist   = $database->numRows($rtn_exist);

			if($rows_exist == 0){//無い場合はフラグを立てない。
				$array_exit[$year_past] = 0;
			} else {//ある場合はフラグを立てるだけ
				$array_exit[$year_past] = 1;
			}
		}
	}
	
	return $array_exit;
}


/************************************************
**
**	countFromMailsPast
**	--------------------------------------------
**	20190218 add by A.cos
**	# pamameter
**	$mails_count:メール数格納変数
**	$members_data:会員情報
**	$parent_id:親キャラID
**	$array_exit:サービス開始年から今年までのメール分離テーブルの有無を納めた配列
**	$send_or_receive: 0/SEND, 1/RECEIVE
**
**	# return
**	$mails_count:メール数格納変数
**	
**
************************************************/
function countFromMailsPast($mails_count, $members_data, $parent_id, $array_exit, $send_or_receive){
	# POINT NUMBER / NAME ARRAY
	global $database;

	## 過去メールからの件数を取得
	foreach($array_exit as $key=>$val){
		if($val){
			# PARAMETER
			$array_past					 = array();
			$array_past[':site_cd']		 = $members_data['site_cd'];
			$array_past[':user_id']		 = $members_data['id'];
			$array_past[':del_flg']		 = 0;
			$array_past[':character_id']	 = $parent_id;

			$where_past	= "site_cd = :site_cd ";
			if($send_or_receive){
			# RECEIVE ONLY
				$where_past	.= "AND send_id = :character_id ";
				$where_past	.= "AND recv_id = :user_id ";
			}else{
			# SEND ONLY
				$where_past	.= "AND send_id = :user_id ";
				$where_past	.= "AND recv_id = :character_id ";
			}
			$where_past	.= "AND del_flg = :del_flg";

			$order_past	 = NULL;
			$limit_past	 = NULL;
			$group_past	 = NULL;
			
			$rtn_past	=	$database->selectDb("mails_past_".$key,
								"id",$where_past,$array_past,$order_past,$limit_past,$group_past,1);
			$error_past		= $database->errorDb("GET MAIL PAST COUNT",$rtn_past->errorCode(),__FILE__,__LINE__);

			$rows_past					 = $database->numRows($rtn_past);
			$mails_count += $rows_past;
			$database->freeResult($rtn_past);
		}
	}
	
	return $mails_count;
}

/************************************************
**
**	getImagesFromMailsPast
**	--------------------------------------------
**	20190218 add by A.cos
**	# pamameter
**	$array_exit:サービス開始年から今年までのメール分離テーブルの有無を納めた配列
**	$mails_id: 画像を取り出すメールID
**
**	# return
**	$mails_data:画像を持った過去メールデータ
**	
**
************************************************/
function getImagesFromMailsPast($array_exit, $mails_id){
	# POINT NUMBER / NAME ARRAY
	global $database;

	$mails_data = NULL;

	foreach($array_exit as $key=>$val){
		if($val){
			# PARAMETER
			$pastmails_array			 = array();
			$pastmails_array[':site_cd'] = SITE_CD;
			$pastmails_array[':id']		 = $mails_id;
			$pastmails_array[':del_flg'] = 0;

			$pastmails_where			 = "site_cd = :site_cd ";
			$pastmails_where			.= "AND id = :id ";
			$pastmails_where			.= "AND del_flg = :del_flg";

			$pastmails_order			 = NULL;
			$pastmails_limit			 = 1;
			$pastmails_group			 = NULL;

			$pastmails_column = "id,media,media_flg";

			$pastmails_rtn	 = $database->selectDb("mails_past_".$key,$pastmails_column,
													$pastmails_where,$pastmails_array,
													$pastmails_order,$pastmails_limit,$pastmails_group,1);
			$pastmails_error	 = $database->errorDb("/mail/image/",$pastmails_rtn->errorCode(),__FILE__,__LINE__);
			
			$mails_data					= $database->fetchAssoc($pastmails_rtn);
				
			if(!empty($mails_data['id']))
				break;
		}
	}

	return $mails_data;
}


?>