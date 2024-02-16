<?php
################################ FILE MANAGEMENT ################################
##
##	exchangeController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	EXCHANGE PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	EXCHANGE PAGE 各種処理
##
##	page : index -> 
##
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

/** ALBUM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AlbumModel.php");

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

################################# POST ARRAY ####################################

$value_array				= array('page','id');
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
	$data['page']			= "index";
}

# ID
if(!empty($_POST['id'])){
	$data['id']				= $_POST['id'];
}


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# ALBUM MODEL
$albumModel					= new AlbumModel($database,$mainClass);

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);

# CAMPAIGNSET MODEL
$campaignsetModel			= new CampaignsetModel($database,$mainClass);

# ITEMUSE MODEL
$itemuseModel				= new ItemuseModel($database,$mainClass);

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

if(empty($exection)){


	/************************************************
	**
	**	ページ毎にif文で処理分岐
	**
	************************************************/


	/************************************************
	**
	**	INDEX / LIST
	**	============================================
	**	画像リスト
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){

		/************************************************
		**
		**	共通処理
		**	============================================
		**	所持アイテム
		**	===========================================
		**	ItemBox
		**	===========================================
		**	itemsとJOINして所持アイテム情報取得
		**
		************************************************/

		$item_list										= NULL;
		$item_list										= array();

		$itembox_conditions								= array(
			'user_id'									=> $members_data['id'],
			'character_id'								=> 1,//1ならば鍵だけを探しにいく,０なら鍵だけを探さない
			'status'									=> 0,
			'order'										=> 'i.name'
		);
		$itembox_rtn									= $itemboxModel->getItemboxListJoinOnItems($itembox_conditions);

		$i=0;
		$j=0;
		while($itembox_data = $database->fetchAssoc($itembox_rtn)){

			# 残り数ゼロで使用中データもなければ非表示
			if($itembox_data['unit'] == 0){
				continue;
			}

			# 鍵使って開放してるかチェック
			$itemuse_conditions				= array();
			$itemuse_conditions				= array(
				'item_id'					=> $itembox_data['item_id'],
				'user_id'					=> $members_data['id'],
				'character_id'				=> $itembox_data['character_id'],
				'status'					=> 0
			);

			$itemuse_rows					= $itemuseModel->getItemuseCount($itemuse_conditions);

			# まだ未使用
			if($itemuse_rows == 0){
				$item_list['use'][$i] = 0;
			}else{
				$item_list['use'][$i] = 1;
			}

			$item_list['id'][$i]						= $itembox_data['itembox_id'];
			$item_list['unit'][$i]						= $itembox_data['unit'];

			if(!empty($itembox_data['name'])){
				$item_list['name'][$i]					= $itembox_data['name'];
			}

			if(!empty($itembox_data['image'])){
				$item_list['image'][$i]					= $itembox_data['image'];
			}

			if(!empty($itembox_data['description'])){
				$item_list['description'][$i]			= $itembox_data['description'];
			}

			if(!empty($itembox_data['exchange'])){
				$item_list['exchange'][$i]			= $itembox_data['exchange'];
			}

			$i++;

		}

		$database->freeResult($itembox_rtn);

		$exection_message								= NULL;
		if(!empty($data['id']) && is_numeric($data['id'])){

			$exection_message							= "所持".TICKET_NAME."が<br /><span style=\"font-weight: bold;\"><span style=\"color: #0099FF;\">【".$data['id']."】</span> → <span style=\"color: #FF0000;\">【".$members_data['total_point']."】</span></span><br />になりました。";

		}



	/************************************************
	**
	**	EXECTION
	**	============================================
	**	処理
	**
	************************************************/

	# INDEX
	}elseif($data['page'] == "exection"){

		$error											= 0;
		$errormessage									= NULL;
		$result											= NULL;

		# VALIDATION
		if(empty($_POST['itembox_id'])){
			$error									= 2;
			$errormessage							= "交換する鍵を選択して下さい。";
		}

		//mail("eikoshi@k-arat.co.jp","KEY01",var_export($_POST, true),"From:info@kyabaheru.net");

		# OK
		if(empty($error)){
			/************************************************
			**
			**	POINT NO ID
			**
			************************************************/
			$point_no_id							= $point_no_array[$point_name_array['exchange_key']][2];

			/************************************************
			**
			**	アイテムボックスデータ取得
			**
			************************************************/

			$itembox_id = $_POST['itembox_id'];
			$itembox_data = $itemboxModel->getItemboxDataById($itembox_id);

			//mail("eikoshi@k-arat.co.jp","KEY02",var_export($itembox_data, true),"From:info@kyabaheru.net");


			if(!empty($itembox_data['id'])){

				# 処理

				# PLUS POINT
				$exchange_point					= $itembox_data['unit'] * intVal($_POST['exchange']);


				/************************************************
				**
				**	MASTER DATABASE切り替え
				**
				************************************************/

				# AUTHORITY / 既にマスターに接続してるかチェック
				$db_auth						 = $database->checkAuthority();

				# DATABASE CHANGE / スレーブだったら
				if(empty($db_auth)){

					# CLOSE DATABASE SLAVE
					$database->closeDb();

					# CONNECT DATABASE MASTER
					$database->connectDb(MASTER_ACCESS_KEY);

				}


				/************************************************
				**
				**	トランザクション開始
				**	============================================
				**	このページではトランザクション処理をする
				**	何か問題が起きたら全てロールバック
				**
				************************************************/

				# トランザクションスタート
				$database->beginTransaction();
				
				/************************************************
				**
				**	itembox UPDATE
				**
				************************************************/
				$itembox_update					= array();
				$itembox_update					= array(
					'unit'					=> 0
				);
				$itembox_update_where				= "id = :id";
				$itembox_update_conditions[':id']	= $itembox_id;

				# 【UPDATE】mails
				$database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);


				/************************************************
				**
				**	members UPDATE
				**
				************************************************/

				# NEW S_POINT
				$new_s_point						= $members_data['s_point'] + $exchange_point;

				# UPDATE MEMBERS
				$members_update						= array();
				$members_update						= array(
					's_point'						=> $new_s_point
				);

				# UPDATE WHERE
				$members_update_where				= "id = :id";
				$members_update_conditions[':id']	= $members_data['id'];

				# 【UPDATE】 / members
				$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);


				/************************************************
				**
				**	points INSERT
				**
				************************************************/

				# pay_flg 判定 無料ユーザー
				if($members_data['status'] == 3) {
					$pay_flg						= 2;
				# 定額ユーザー
				}elseif($members_data['status'] == 2) {
					$pay_flg						= 3;
				# 通常ユーザー
				}elseif($members_data['status'] != 0){

					# 無課金
					if($members_data['pay_count'] == 0){
						$pay_flg					= 2;
					# 課金
					}else{
						$pay_flg					= 1;
					}

				# その他
				}else{
					$pay_flg						= 0;
				}

				# S POINTなので1
				$s_point_no							= 1;

				$points_insert						= array();
				$points_insert						= array(
					'user_id'						=> $members_data['id'],
					'site_cd'						=> $members_data['site_cd'],
					'sex'							=> $members_data['sex'],
					'ad_code'						=> $members_data['ad_code'],
					'domain_flg'					=> $members_data['domain_flg'],
					'point'							=> $exchange_point,
					'point_no_id'					=> $point_no_id,
					'campaign_id'					=> 0,
					//'campaign_id'					=> $campaign_id,
					'point_type'					=> $s_point_no,
					'log_date'						=> date("YmdHis"),
					'pay_flg'						=> $pay_flg
				);

				# 【insert】points
				$database->insertDb("points",$points_insert);
				
				/************************************************
				**
				**	コミット
				**	============================================
				**	正常にきたらコミットする
				**	何か問題があればロールバック
				**
				************************************************/

				# COMMIT : 一括処理
				if(empty($error)){
					$database->commit();

					$new_point							= $members_data['total_point'] + $exchange_point;
					$result['success']					= 1;
					$result['success_title']			= "処理完了";
					$result['success_message']			= "所持".TICKET_NAME."が<br /><span style=\"font-weight: bold;\"><span style=\"color: #0099FF;\">【".$members_data['total_point']."】</span> → <span style=\"color: #FF0000;\">【".$new_point."】</span></span><br />になりました。";
					$result['redirect']					= "/".$directory."/index/".$members_data['total_point']."/";
				# ROLLBACK : 巻き戻し
				}else{
					$database->rollBack();
				}
			}
			
		}

		/************************************************
		**
		**	DATABASE 切断
		**
		************************************************/

		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();



		/************************************************
		**
		**	エラー
		**
		************************************************/


		$result['error']								= $error;
		$result['errormessage']							= $errormessage;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();
			exit();

		}

		/************************************************
		**
		**	jsonでリザルトを返す
		**
		************************************************/

		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();


	}

}


################################## FILE END #####################################
?>