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

		$albums_conditions								= array();
		$albums_conditions								= array(
			'user_id'									=> $members_data['id'],
			'status'									=> 0,
			'order'										=> "id DESC"
		);

		$albums_rtn										= $albumModel->getAlbumList($albums_conditions);

		$check											= array();

		$i=0;
		while($albums_data = $database->fetchAssoc($albums_rtn)){

			if(!isset($check[$albums_data['image']])){
				$check[$albums_data['image']]			= $albums_data['image'];
				$album_list['none'][$i]					= 1;
			}

			$album_list['id'][$i]						= $albums_data['id'];
			$album_list['image'][$i]					= $albums_data['image'];
			$album_list['name'][$i]						= $albums_data['name'];

			$i++;

		}

		$database->freeResult($albums_rtn);


		/************************************************
		**
		**	キャンペーン
		**	============================================
		**
		**
		************************************************/

		# CAMPAIGN
		$campaign_id									= 0;
		$campaign_data									= $campaignsetModel->getCampaignsetData($members_data);
		$campaign_check									= $memberModel->checkCampaignUpdate($members_data,$campaign_data);

		# campaign_type が2か3か5だったら(消費ポイントキャンペーン)
		if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2 || $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
			$campaign_id								= $campaign_data['id'];
		# それ以外はチェックも外す
		}elseif(empty($campaign_check)){
			$campaign_check								= 0;
		}

		# EXCHANGE POINT
		$exchange_point									= 0;

		# POINT NO ID
		$point_no_id									= $point_no_array[$point_name_array['exchange_image']][2];

		# pointsets
		$pointsets_data									= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

		# OK
		if(!empty($pointsets_data)){

			$count										= count($pointsets_data);
			for($i=0;$i<$count;$i++){

				# 送信
				if($pointsets_data[$i]['point_no_id'] == $point_no_id){
					$exchange_point						= $pointsets_data[$i]['point'];
				}

			}

		}


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

		# CHECK
		if(!empty($_POST['purpose']) && $_POST['purpose'] == 2){

			# VALIDATION
			if(empty($_POST['exchange'])){
				$error									= 2;
				$errormessage							= "交換する画像を選択して下さい。";
			}

			# OK
			if(empty($error)){

				/************************************************
				**
				**	キャンペーン
				**	============================================
				**
				**
				************************************************/

				# CAMPAIGN
				$campaign_id							= 0;
				$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);
				$campaign_check							= $memberModel->checkCampaignUpdate($members_data,$campaign_data);

				# campaign_type が2か3か5だったら(消費ポイントキャンペーン)
				if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2 || $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
					$campaign_id						= $campaign_data['id'];
				# それ以外はチェックも外す
				}elseif(empty($campaign_check)){
					$campaign_check						= 0;
				}


				# POINT NO ID
				$point_no_id							= $point_no_array[$point_name_array['exchange_image']][2];

				# pointsets
				$pointsets_data							= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

				# OK
				if(!empty($pointsets_data)){

					$count								= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# 送信
						if($pointsets_data[$i]['point_no_id'] == $point_no_id){
							$point_data['exchange']		= $pointsets_data[$i]['point'];
						}

					}



					# EXCHANGE DATA
					$exchange_count						= count($_POST['exchange']);

					# 処理
					if($exchange_count > 0){

						# PLUS POINT
						$exchange_point					= $point_data['exchange'] * $exchange_count;


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


						foreach($_POST['exchange'] as $key => $albums_id){

							if(empty($albums_id)){
								continue;
							}

							$albums_data						= $albumModel->getAlbumDataById($albums_id,"id");

							if(!empty($albums_data['id'])){

								# UPDATE ALBUMS DATA
								$albums_update					= array();
								$albums_update					= array(
									'status'					=> 8
								);
								$albums_update_where				= "id = :id";
								$albums_update_conditions[':id']	= $albums_id;

								# 【UPDATE】mails
								$database->updateDb("albums",$albums_update,$albums_update_where,$albums_update_conditions);

							}else{

								$error							= 3;
								$errormessage					= "既に交換済みのアイテムが含まれております。";
								break;

							}


						}


						if(empty($error)){

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
								'campaign_id'					=> $campaign_id,
				                'point_type'					=> $s_point_no,
								'log_date'						=> date("YmdHis"),
				                'pay_flg'						=> $pay_flg
							);

							# 【insert】points
							$database->insertDb("points",$points_insert);

						}


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


			}


		# ERROR
		}else{

			$error										= 1;
			$errormessage								= "不正なアクセスです。";

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