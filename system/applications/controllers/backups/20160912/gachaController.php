<?php
################################ FILE MANAGEMENT ################################
##
##	gachaController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	PRESENTBOX PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	PRESENTBOX PAGE 各種処理
##
##	page : index		-> ガチャTOPページ
##	page : start		-> スタートページ
##	page : lottery		-> 抽選
##	page : acceptance	-> 受け取り処理
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

/** GACHA MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/GachaModel.php");

/** SHOP MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ShopModel.php");

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemboxModel.php");

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

/** PAY MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PayModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");


################################# POST ARRAY ####################################

$value_array				= array('page','type','pay','campaign_id');
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

# TYPE
if(!empty($_POST['type'])){
	$data['type']			= $_POST['type'];
}

# PAY
if(!empty($_POST['pay'])){
	$data['pay']			= $_POST['pay'];
}

# CAMPAIGN ID
if(!empty($_POST['campaign_id'])){
	$data['campaign_id']	= $_POST['campaign_id'];
}

if(empty($data['campaign_id'])){
	$data['campaign_id']	= 0;
}

# PID
$pays_id					= 0;
if(!empty($_POST['pays_id'])){
	$pays_id				= $_POST['pays_id'];
}


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# GACHA MODEL
$gachaModel					= new GachaModel($database,$mainClass);

# SHOP MODEL
$shopModel					= new ShopModel($database,$mainClass);

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMBOX MODEL
$itemboxModel				= new ItemboxModel($database,$mainClass);

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);

# PAY MODEL
$payModel					= new PayModel($database,$mainClass);

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
	**	INDEX
	**	============================================
	**	ガチャトップ
	**
	************************************************/

	if($data['page'] == "index"){


		/************************************************
		**
		**	通常
		**	============================================
		**
		**
		************************************************/

		# 無料ガチャ
		$free_gacha									= NULL;

		if($members_data['last_gacha_date'] < date("Y-m-d")){
			$free_gacha								= 1;
		}

		# LOOP
		$loop_single								= $gacha_loop_array['single'];
		$loop_multi									= $gacha_loop_array['multi'];

		# POINT NO
		$point_no_id								= $point_no_array[$point_name_array['gacha_point']][2];

		# pointsets
		$pointsets_data								= $pointsetModel->getPointset($point_no_id,$members_data,NULL);

		$point_data['gacha']						= DEFAULT_GACHA_POINT;

		if(!empty($pointsets_data)){

			$count									= count($pointsets_data);
			for($i=0;$i<$count;$i++){

				# ガチャポイント
				if($pointsets_data[$i]['point_no_id'] == $point_no_id){
					$point_data['gacha']			= $pointsets_data[$i]['point'];
				}

			}

		}

		# 回数分
		$point_single								= $point_data['gacha'] * $loop_single;
		$point_multi								= $point_data['gacha'] * $loop_multi;

		$check_point_single							= NULL;
		$check_point_multi							= NULL;

		if($members_data['total_point'] < $point_single){
			$check_point_single						= 1;
		}

		if($members_data['total_point'] < $point_multi){
			$check_point_multi						= 1;
		}


		/************************************************
		**
		**	キャンペーン
		**	============================================
		**
		**
		************************************************/

		# CAMPAIGN
		$campaign_id								= 0;
		$campaign_point								= NULL;
		$campaign_data								= $campaignsetModel->getCampaignsetData($members_data);

		# campaign_type が2だったら(消費ポイント)
		if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2){
			$campaign_point							= $campaign_data['id'];
		# campaign_type が4だったら(ガチャキャンペーン)
		}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 4){
			$campaign_id							= $campaign_data['id'];
		# 両方
		}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 5){
			$campaign_point							= $campaign_data['id'];
			$campaign_id							= $campaign_data['id'];
		}


		/************************************************
		**
		**	キャンペーンバナー / キャンペーンポイント設定取得
		**
		************************************************/

		if(!empty($campaign_point) || !empty($campaign_id)){


			# POINT
			if(!empty($campaign_point)){

				# pointsets
				$campaign_pointsets_data				= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_point);

				$campaign_point_data['gacha']			= DEFAULT_GACHA_POINT;

				if(!empty($campaign_pointsets_data)){

					$count								= count($campaign_pointsets_data);
					for($i=0;$i<$count;$i++){

						# ガチャポイント
						if($campaign_pointsets_data[$i]['point_no_id'] == $point_no_id){
							$campaign_point_data['gacha']	= $campaign_pointsets_data[$i]['point'];
						}

					}

				}

				# 回数分
				$campaign_point_single					= $campaign_point_data['gacha'] * $loop_single;
				$campaign_point_multi					= $campaign_point_data['gacha'] * $loop_multi;

				$check_campaign_point_single			= NULL;
				$check_campaign_point_multi				= NULL;

				if($members_data['total_point'] < $campaign_point_single){
					$check_campaign_point_single		= 1;
				}

				if($members_data['total_point'] < $campaign_point_multi){
					$check_campaign_point_multi			= 1;
				}

			# POINT設定なければ通常データで上書き
			}else{

				$campaign_point_single					= $point_single;
				$campaign_point_multi					= $point_multi;
				$check_campaign_point_single			= $check_point_single;
				$check_campaign_point_multi				= $check_point_multi;

			}

			# BANNER
			$banner_conditions						= array();
			$banner_conditions						= array(
				'file_type'							=> 5,
				'category'							=> 100,
				'site_cd'							=> $members_data['site_cd'],
				'target_id'							=> $campaign_data['id'],
				'status'							=> 0
			);

			$banner_rtn								= $imageModel->getImageList($banner_conditions);

			$i=0;
			while($banner_data = $database->fetchAssoc($banner_rtn)){

				$banner_list['id'][$i]				= $banner_data['id'];
				$banner_list['image'][$i]			= $banner_data['img_name'];
				$banner_list['link'][$i]			= $banner_data['img_key'];
				$banner_list['target_id'][$i]		= $banner_data['target_id'];

				# SINGLE
				if($banner_data['img_key'] == "single"){

					if(!empty($check_campaign_point_single)){
						$banner_list['point'][$i]	= NULL;
					}else{
						$banner_list['point'][$i]	= $campaign_point_single;
					}

				}elseif($banner_data['img_key'] == "multi"){

					if(!empty($check_campaign_point_multi)){
						$banner_list['point'][$i]	= NULL;
					}else{
						$banner_list['point'][$i]	= $campaign_point_multi;
					}

				}


				$i++;

			}

			$database->freeResult($banner_rtn);

		}




	/************************************************
	**
	**	PAYMENT
	**	============================================
	**	PF通過でガチャ引く場合
	**	一旦プラットフォームの決済ページへ遷移
	**
	************************************************/

	}elseif($data['page'] == "payment"){

		# LOOP回数
		$loop									= 0;

		# ガチャループタイプ
		if(!empty($data['type'])){
			$loop								= $gacha_loop_array[$data['type']];
		}else{
			$error								= 21;
			$errormessage						= "不正なアクセスです";
		}

		# PAGE CHECK
		if(empty($data['pay']) || $data['pay'] != "coin"){
			$error								= 22;
			$errormessage						= "不正なアクセスです";
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
			$campaign_id						= 0;
			$campaign_point						= NULL;
			$campaign_data						= $campaignsetModel->checkCampaign($data['campaign_id']);

			# campaign_type が2だったら(消費ポイント)
			if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2){
				$campaign_point					= $campaign_data['id'];
			# campaign_type が4だったら(ガチャキャンペーン)
			}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 4){
				$campaign_id					= $campaign_data['id'];
			# 両方
			}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 5){
				$campaign_point					= $campaign_data['id'];
				$campaign_id					= $campaign_data['id'];
			}



			/************************************************
			**
			**	pointsets
			**	============================================
			**	pointsetsデータ取得
			**	ガチャ消費ポイント
			**
			************************************************/

			$gacha_point						= 0;
			$unit_price							= 0;

			# POINT NO
			$point_no_point						= $point_no_array[$point_name_array['gacha_point']][2];
			$point_no_coin						= $point_no_array[$point_name_array['gacha_coin']][2];
			$point_no_id						= $point_no_point.",".$point_no_coin;

			# pointsets
			$pointsets_data						= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

			$point_data['gacha_point']			= DEFAULT_GACHA_POINT;
			$point_data['gacha_coin']			= DEFAULT_GACHA_POINT;

			if(!empty($pointsets_data)){

				$count							= count($pointsets_data);
				for($i=0;$i<$count;$i++){

					# ポイント
					if($pointsets_data[$i]['point_no_id'] == $point_no_point){
						$point_data['gacha_point']	= $pointsets_data[$i]['point'];
					}

					# コイン
					if($pointsets_data[$i]['point_no_id'] == $point_no_coin){
						$point_data['gacha_coin']	= $pointsets_data[$i]['point'];
					}

				}

			}

			# ガチャを引く為に必要なサイト内ポイント数
			$gacha_point						= $point_data['gacha_point'] * $loop;

			# ガチャを引く為に必要なPF通貨数
			$unit_price							= $point_data['gacha_coin'];

			# ITEM DATA
			$item_data							= array();
			$item_data							= array(
				'itemId'						=> GACHA_ITEM_ID,
				'itemName'						=> GACHA_ITEM_NAME,
				'unitPrice'						=> $unit_price,
				'quantity'						=> $loop,
				'imageUrl'						=> HTTP_DOMAIN."/images/icon/icon-gacha.jpg",
				'description'					=> GACHA_ITEM_DESCRIPTION,
				'callbackUrl'					=> HTTP_SETTLEMENT."/".$directory."/",
				'finishPageUrl'					=> HTTP_DOMAIN."/".$directory."/start/".$data['type']."/coin/".$campaign_id."/"
			);

			# 決済
			$item_result						= $authClass->createPaymentObjectFromNijiyomeByUserId($members_data['user_id'],$item_data);

			# ERROR
			if(!empty($item_result['error'])){

				$error							= $item_result['error'];
				$errormessage					= $item_result['message'];

			# OK
			}else{

				# STATUS ERROR
				if($item_result['entry']['status'] == 3){

					$error						= $item_result['entry']['status'];
					$errormessage				= "既にキャンセルされております。";

				}elseif($item_result['entry']['status'] == 4){

					$error						= $item_result['entry']['status'];
					$errormessage				= "既に購入可能期限が過ぎております。";

				}elseif($item_result['entry']['status'] == 5){

					$error						= $item_result['entry']['status'];
					$errormessage				= "既にキャンセルされております。";

				}

				# 正常処理
				if(empty($error) && $item_result['entry']['status'] == 1){


					$payment_id					= $item_result['entry']['paymentId'];
					$transaction_url			= $item_result['entry']['transactionUrl'];
					$ordered_time				= $item_result['entry']['orderedTime'];


					/************************************************
					**
					**	MASTER DATABASE切り替え
					**
					************************************************/

					# AUTHORITY / 既にマスターに接続してるかチェック
					$db_auth					 = $database->checkAuthority();

					# DATABASE CHANGE / スレーブだったら
					if(empty($db_auth)){

						# CLOSE DATABASE SLAVE
						$database->closeDb();

						# CONNECT DATABASE MASTER
						$database->connectDb(MASTER_ACCESS_KEY);

						$db_check				= 1;

					}

					# PAY AMOUNT
					$pay_amount					= $unit_price * $loop;

					# SETTLEMENT ID
					$settlement_id				= $point_no_coin;

					# LIMIT TIME (決済申込開始から30分で処理期限終了)
					$limit_time					= date("YmdHis",strtotime("+30 minutes",strtotime($ordered_time)));


					# 未処理で残ってしまってるレコードがあるかチェック
					$pays_conditions			= array();
					$pays_conditions			= array(
						'user_id'				=> $members_data['id'],
						"pay_amount"			=> $pay_amount,
						"settlement_id"			=> $settlement_id,
						'clear'					=> 0,
						'error'					=> 0,
						'status'				=> 0
					);

					$pays_data					= $payModel->getPayData($pays_conditions,"id");

					# データあればそれに上書き
					if(!empty($pays_data['id'])){

						# PAYS UPDATE
						$pays_update			= array();
						$pays_update			= array(
							'site_cd'			=> $members_data['site_cd'],
							'user_id'			=> $members_data['id'],
							'campaign_id'		=> $campaign_id,
							'domain_flg'		=> $members_data['domain_flg'],
							'sex'				=> $members_data['sex'],
			                'ad_code'			=> $members_data['ad_code'],
			                'reg_date'			=> $members_data['reg_date'],
							'order_date'		=> $ordered_time,
							'pay_amount'		=> $pay_amount,			// -> 必要PF通貨数(にじコイン数)
							'settlement_id'		=> $settlement_id,
							'object'			=> $gacha_point,		// -> ガチャを回す為のサイト内必要ポイント
							'sid'				=> $payment_id,
							'limit_time'		=> $limit_time,
							'clear'				=> 0,
							'finish'			=> 0,
							'error'				=> 0,
			                'status'			=> 0
						);

						# UPDATE WHERE
						$pays_update_where		= "id = :id";
						$pays_update_conditions[':id']	= $pays_data['id'];

						# 【UPDATE】 / pays
						$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);

						$result					= 1;


					# なければINSERT
					}else{

						# PAYS INSERT
						$pays_insert			= array();
						$pays_insert			= array(
							'site_cd'			=> $members_data['site_cd'],
							'user_id'			=> $members_data['id'],
							'campaign_id'		=> $campaign_id,
							'domain_flg'		=> $members_data['domain_flg'],
							'sex'				=> $members_data['sex'],
			                'ad_code'			=> $members_data['ad_code'],
			                'reg_date'			=> $members_data['reg_date'],
							'order_date'		=> $ordered_time,
							'pay_amount'		=> $pay_amount,			// -> 必要PF通貨数(にじコイン数)
							'settlement_id'		=> $settlement_id,
							'object'			=> $gacha_point,		// -> ガチャを回す為のサイト内必要ポイント
							'sid'				=> $payment_id,
							'limit_time'		=> $limit_time,
			                'status'			=> 0
						);

						# 【insert】pays
						$result					= $database->insertDb("pays",$pays_insert);


					}


					# 決済画面へ
					if(!empty($result)){

						# CLOSE DATABASE
						$database->closeDb();
						$database->closeStmt();

						# REDIRECT
						header("Location: ".$transaction_url);
						exit();

					}else{

						$error					= 10;
						$errormessage			= "正常に処理できませんでした";

					}

					# DATABASE CHANGE
					if(!empty($db_check)){

						# CLOSE DATABASE MASTER
						$database->closeDb();

						# CONNECT DATABASE SLAVE
						$database->connectDb();

					}

				}

			}

		}



	/************************************************
	**
	**	START
	**	============================================
	**	スタートページ
	**
	************************************************/

	}elseif($data['page'] == "start"){

		# このページではヘッダーとフッターを消す(スマフォ)
		$header_hide									= 1;
		$footer_hide									= 1;

		# サブヘッダーとサブフッターを表示
		$sub_header										= 1;
		$sub_footer										= 1;

		# 初期化
		$error											= NULL;
		$errormessage									= NULL;

		# LOOP回数
		$loop											= 0;

		# ガチャループタイプ
		if(!empty($data['type'])){
			$loop										= $gacha_loop_array[$data['type']];
		}else{
			$error										= 21;
			$errormessage								= "不正なアクセスです";
		}


		# 無料ガチャ処理
		if($data['type'] == "free"){

			if($members_data['last_gacha_date'] == date("Y-m-d")){
				$error									= 1;
				$errormessage							= "既に本日の無料ガチャはご利用済みです";
			}


		}else{


			# チケットで処理
			if($data['pay'] == "point"){


				/************************************************
				**
				**	キャンペーン
				**	============================================
				**
				**
				************************************************/

				# CAMPAIGN
				$campaign_id							= 0;
				$campaign_point							= NULL;
				$campaign_data							= $campaignsetModel->checkCampaign($data['campaign_id']);

				# campaign_type が2だったら(消費ポイント)
				if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2){
					$campaign_point						= $campaign_data['id'];
				# campaign_type が4だったら(ガチャキャンペーン)
				}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 4){
					$campaign_id						= $campaign_data['id'];
				# 両方
				}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 5){
					$campaign_point						= $campaign_data['id'];
					$campaign_id						= $campaign_data['id'];
				}

				# POINT NO
				$point_no_id							= $point_no_array[$point_name_array['gacha_point']][2];

				# pointsets
				$pointsets_data							= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_point);

				$point_data['gacha']					= DEFAULT_GACHA_POINT;

				if(!empty($pointsets_data)){

					$count								= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# ガチャポイント
						if($pointsets_data[$i]['point_no_id'] == $point_no_id){
							$point_data['gacha']		= $pointsets_data[$i]['point'];
						}

					}

				}

				# 回数分
				$point_gacha							= $point_data['gacha'] * $loop;

				if($members_data['total_point'] < $point_gacha){
					$error								= 42;
					$errormessage						= TICKET_NAME."が足りません。";
				}


			# PFの決済で処理
			}elseif($data['pay'] == "coin"){


				/************************************************
				**
				**	settlement check
				**	============================================
				**	正常に決済処理が終わってるかチェック
				**
				**	戻り値
				**	[opensocial_app_id] => 445
				**	[opensocial_owner_id] => 4151
				**	[opensocial_viewer_id] => 4151
				**	[paymentId] => 445-4151-20160905184013077433
				**
				************************************************/

				# 初期化
				$add_path								= NULL;

				# APP IDがあるか
				if(empty($_REQUEST['opensocial_app_id'])){

					$error								= 1;
					$errormessage						= "正常に処理ができません";

				# APP IDが一致するか
				}elseif(!empty($_REQUEST['opensocial_app_id']) && $_REQUEST['opensocial_app_id'] != APP_ID){

					$error								= 2;
					$errormessage						= "正常に処理ができません";

				# USER IDがない
				}elseif(empty($_REQUEST['opensocial_viewer_id'])){

					$error								= 3;
					$errormessage						= "お客様情報が確認できません";

				# USER ID が一致するか
				}elseif($_REQUEST['opensocial_viewer_id'] != $members_data['user_id']){

					$error								= 4;
					$errormessage						= "お客様情報が確認できません";

				# PAYMENT IDがない
				}elseif(empty($_REQUEST['paymentId'])){

					$error								= 5;
					$errormessage						= COIN_NAME."の処理が正常ではありません";

				}

				# チェック
				if(empty($error)){

					# PF側の決済情報をチェック
					$payment_result						= $authClass->checkPaymentObjectFromNijiyomeByUserId($members_data['user_id'],$_REQUEST['paymentId']);

					# 通信OK
					if(empty($payment_result['error'])){

						# ステータスが決済完了以外だったらエラー
						if(!empty($payment_result['entry']['status']) &&  $payment_result['entry']['status'] != 2){

							# 申し込みで止まってる
							if($payment_result['entry']['status'] == 1){

								$error					= 11;
								$errormessage			= "お申込頂いた".COIN_NAME."決済処理が正常に終了しておりません。<br />お手数ですが当ページをリロードしてください。";

								$params					 = "?opensocial_app_id=".$_REQUEST['opensocial_app_id'];
								$params					.= "&opensocial_owner_id=".$_REQUEST['opensocial_owner_id'];
								$params					.= "&opensocial_viewer_id=".$_REQUEST['opensocial_viewer_id'];
								$params					.= "&paymentId=".$_REQUEST['paymentId'];


							# ユーザーキャンセル -> こいつはリダイレクト
							}elseif($payment_result['entry']['status'] == 3){

								$error					= 33;

								# CLOSE DATABASE
								$database->closeDb();
								$database->closeStmt();

								# REDIRECT
								header("Location: /".$directory."/");
								exit();

							# 期限切れ
							}elseif($payment_result['entry']['status'] == 4){

								$error					= 44;
								$errormessage			= "お申込頂いた".COIN_NAME."決済の期限が切れております。<br />お手数ですが再度お試し下さい。";

							# HTTPステータスキャンセル
							}elseif($payment_result['entry']['status'] == 5){

								$error					= 55;
								$errormessage			= "決済処理でエラーが起こった可能性があります。<br />お問い合わせよりご連絡下さい。";

							}

						}

						# 決済OK
						if(empty($error)){

							# SETTLEMENT ID
							$settlement_id				= $point_no_array[$point_name_array['gacha_coin']][2];

							# 決済情報チェック
							$pays_conditions			= array();
							$pays_conditions			= array(
								'user_id'				=> $members_data['id'],
								"settlement_id"			=> $settlement_id,
								'sid'					=> $_REQUEST['paymentId'],
								'clear'					=> 1,
								'error'					=> 0,
								'status'				=> 0
							);

							$pays_data					= $payModel->getPayData($pays_conditions);

							# 決済データOK / 持ちポイントチェック
							if(!empty($pays_data['id'])){

								# 既にこの決済IDでガチャを引いていたら強制リダイレクト
								if($pays_data['finish'] > 0){

									$error				= 12;

									# CLOSE DATABASE
									$database->closeDb();
									$database->closeStmt();

									# REDIRECT
									header("Location: /".$directory."/");
									exit();

								}

								# ポイントチェック
								if($members_data['point'] < $pays_data['object']){
									$error				= 43;
									$errormessage		= COIN_NAME."が足りません。";
								}


								# 全ての処理OKならpaysのidをjavascriptに渡す
								if(empty($error)){
									$pays_id			= $pays_data['id'];
								}

							# 決済データエラー
							}else{

								$error					= 6;
								$errormessage			= COIN_NAME."の処理が正常ではありません";

							}

						}

					# 通信エラー
					}else{

						$error							= $payment_result['error'];
						$errormessage					= $payment_result['message'];

					}

				}

			}else{

				$error									= 7;
				$errormessage							= "不正なアクセスです";

			}

		}


	/************************************************
	**
	**	LOTTERY
	**	============================================
	**	ガチャ抽選処理
	**
	************************************************/

	}elseif($data['page'] == "lottery"){


		# 初期化
		$error											= NULL;
		$errormessage									= NULL;

		# 受け取りフラグ
		$acceptance										= NULL;
		$exection										= NULL;

		# LOOP回数
		$loop											= 0;

		# ガチャループタイプ
		if(!empty($data['type'])){
			$loop										= $gacha_loop_array[$data['type']];
		}else{
			$error										= 21;
			$errormessage								= "不正なアクセスです";
		}

		# 無料だったらチェック
		if($data['type'] == "free"){

			if($members_data['last_gacha_date'] == date("Y-m-d")){
				$error									= 31;
				$errormessage							= "既に本日の無料ガチャはご利用済みです";
			}

		}


		# OK
		if(empty($error) && $loop > 0){


			/************************************************
			**
			**	キャンペーン
			**	============================================
			**
			**
			************************************************/

			# CAMPAIGN
			$campaign_id												= 0;
			$campaign_point												= NULL;
			$campaign_data												= $campaignsetModel->checkCampaign($data['campaign_id']);

			# campaign_type が2だったら(消費ポイント)
			if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2){
				$campaign_point											= $campaign_data['id'];
			# campaign_type が4だったら(ガチャキャンペーン)
			}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 4){
				$campaign_id											= $campaign_data['id'];
			# 両方
			}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 5){
				$campaign_point											= $campaign_data['id'];
				$campaign_id											= $campaign_data['id'];
			}



			/************************************************
			**
			**	pointsets
			**	============================================
			**	pointsetsデータ取得
			**
			************************************************/

			$point_gacha												= 0;

			# 一日一回無料
			if($data['type'] == "free"){

				$point_data['gacha']									= 0;
				$point_gacha											= $point_data['gacha'];


			# サイト内ポイント(チケット)でのガチャ
			}elseif($data['pay'] == "point"){

				# POINT NO
				$point_no_id											= $point_no_array[$point_name_array['gacha_point']][2];

				# pointsets
				$pointsets_data											= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_point);

				$point_data['gacha']									= DEFAULT_GACHA_POINT;

				if(!empty($pointsets_data)){

					$count												= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# ガチャポイント
						if($pointsets_data[$i]['point_no_id'] == $point_no_id){
							$point_data['gacha']						= $pointsets_data[$i]['point'];
						}

					}

				}

				# 回数分
				$point_gacha											= $point_data['gacha'] * $loop;

				if($members_data['total_point'] < $point_gacha){
					$error												= 42;
					$errormessage										= TICKET_NAME."が足りません。";
				}


			# PF通貨でのガチャ
			}elseif($data['pay'] == "coin"){

				# POINT NO -> ここはサイト内ガチャ消費ポイント(point_no_id = 41で出す)
				$point_no_point											= $point_no_array[$point_name_array['gacha_point']][2];

				# PF通貨でのpoint_no_id
				$point_no_coin											= $point_no_array[$point_name_array['gacha_coin']][2];

				# pointsets
				$pointsets_data											= $pointsetModel->getPointset($point_no_point,$members_data,$campaign_point);

				$point_data['gacha']									= DEFAULT_GACHA_POINT;

				if(!empty($pointsets_data)){

					$count												= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# ガチャコイン
						if($pointsets_data[$i]['point_no_id'] == $point_no_point){
							$point_data['gacha']						= $pointsets_data[$i]['point'];
						}

					}

				}

				# 回数分
				$point_gacha											= $point_data['gacha'] * $loop;

				if($members_data['point'] < $point_gacha){
					$error												= 43;
					$errormessage										= COIN_NAME."が足りません。";
				}

			}

			# POINT OK
			if(empty($error)){


				/************************************************
				**
				**	MASTER DATABASE切り替え
				**
				************************************************/

				# AUTHORITY / 既にマスターに接続してるかチェック
				$db_auth												 = $database->checkAuthority();

				# DATABASE CHANGE / スレーブだったら
				if(empty($db_auth)){

					# CLOSE DATABASE SLAVE
					$database->closeDb();

					# CONNECT DATABASE MASTER
					$database->connectDb(MASTER_ACCESS_KEY);

				}


				/************************************************
				**
				**	gachas
				**	============================================
				**	ガチャデータ抽出
				**
				************************************************/

				$gachas_conditions										= array();
				$gachas_conditions										= array(
					'site_cd'											=> $members_data['site_cd'],
					'campaign_id'										=> $campaign_id,
					'status'											=> 0
				);

				$gachas_rtn												= $gachaModel->getGachaList($gachas_conditions);

				$i=0;
				while($gachas_data = $database->fetchAssoc($gachas_rtn)){

					$gachas_list[]										= $gachas_data['id'];
					$probability[]										= $gachas_data['percent'];

					$i++;

				}

				$database->freeResult($gachas_rtn);

				# GACHA DATA HIT
				if($i > 0){

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

					# 本日
					$today												= date("Y-m-d");


					/************************************************
					**
					**	gachas
					**	============================================
					**	確率からヒットIDを出す
					**
					************************************************/

					$update_s_point										= 0;
					$update_f_point										= 0;
					$point_recv[1]										= array();
					$point_recv[2]										= array();
					$points_insert_flg									= NULL;

					for($count=0;$count<$loop;$count++){

						$gachas_id										= NULL;
						$acceptance										= NULL;
						$result											= array();

						for($i=0;$i<1;$i++){

							$key										= $optionClass->weightedRandom($probability);

							if(!isset($result[$key])){
								$result[$key]							= 0;
							}

							$result[$key]++;

						}

						ksort($result);

						foreach($result as $key => $value){
							#echo "$wlistname[$key][$wlist[$key]] $value" . "<br>";
						}

						# HIT
						$gacha_id										= $gachas_list[$key];

						if(!empty($gacha_id)){

							# HIT ガチャデータ 取得
							$gacha_data									= $gachaModel->getGachaDataById($gacha_id);

							# OK
							if(!empty($gacha_data['id'])){

								# TICKET
								if($gacha_data['type'] == $number_ticket){

									$get_data							= $shopModel->getShopDataById($gacha_data['target_id'],"id,name,image,type");

									# ログイン無料配布
									if($get_data['type'] == 1){

										# f_pointに加算
										$update_f_point					+= $gacha_data['unit'];
										$point_recv[2][]				 = $gacha_data['unit'];

									# プレゼント配布
									}elseif($get_data['type'] == 2){

										# s_pointに加算
										$update_s_point					+= $gacha_data['unit'];
										$point_recv[1][]				 = $gacha_data['unit'];

									# もしそれ以外があったらs_pointに加算
									}else{

										# s_pointに加算
										$update_s_point					+= $gacha_data['unit'];
										$point_recv[1][]				 = $gacha_data['unit'];

									}

									$points_insert_flg					= 1;

									$acceptance							= 1;


								# ITEM
								}elseif($gacha_data['type'] == $number_item){

									# アイテムデータ
									$get_data							= $itemModel->getItemDataById($gacha_data['id'],"id,name,image");

									# 所持確認
									$itembox_conditions					= array();
									$itembox_conditions					= array(
										'user_id'						=> $members_data['id'],
										'item_id'						=> $gacha_data['target_id'],
										'status'						=> 0
									);
									$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions,"id,unit");

									# 持ってれば加算
									if(!empty($itembox_data['id'])){

										$update_unit					= $itembox_data['unit'] + $gacha_data['unit'];

										$itembox_update['unit']			= $update_unit;
										$itembox_update_where			= "id = :id";
										$itembox_update_conditions[':id']	= $itembox_data['id'];

										# 【UPDATE】 / itembox
										$return							= $database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

										if(!empty($return)){
											$acceptance					= 1;
										}

									# なければ追加
									}else{

										$itembox_insert					= array();
										$itembox_insert					= array(
											'user_id'					=> $members_data['id'],
											'item_id'					=> $gacha_data['target_id'],
											'unit'						=> $gacha_data['unit'],
											'status'					=> 0
										);

										# 【INSERT】 / itembox
										$acceptance						= $database->insertDb("itembox",$itembox_insert);

									}

								# IMAGE
								}elseif($gacha_data['type'] == $number_image){

									$attachment_data					= $imageModel->getImageDataById($gacha_data['target_id'],"id,img_name,img_key");

									if(!empty($attachment_data['id'])){

										# ここでユーザーが既に画像を持っているかチェック



										$get_data['id']					= $attachment_data['id'];
										$get_data['name']				= $attachment_data['img_key'];
										$get_data['image']				= "thumb/".$attachment_data['img_name'];

										$albums_insert					= array();
										$albums_insert					= array(
											'user_id'					=> $members_data['id'],
											'image'						=> $attachment_data['img_name'],
											'name'						=> $attachment_data['img_key'],
											'acceptance_date'			=> $today,
											'status'					=> 0
										);

										# 【INSERT】 / albums
										if($gacha_data['unit'] > 1){

											for($j=0;$j<$gacha_data['unit'];$j++){
												$acceptance				= $database->insertDb("albums",$albums_insert);
											}

										}else{

											$acceptance					= $database->insertDb("albums",$albums_insert);

										}

									}

								}


								# 正常に処理されなければエラー
								if(empty($acceptance)){

									$error								= 2;
									break;

								}

								$gacha_list['id'][$count]				= $gacha_data['id'];
								$gacha_list['unit'][$count]				= $gacha_data['unit'];
								$gacha_list['name'][$count]				= $get_data['name'];
								$gacha_list['image'][$count]			= $get_data['image'];

							}else{

								$error									= 3;
								$errormessage							= "正常に処理できませんでした<br />";
								break;

							}

						}else{

							$error										= 4;
							$errormessage								= "正常に処理できませんでした<br />";
							break;

						}

					}

				}else{

					$error												= 5;
					$errormessage										= "正常に処理できませんでした<br />";

				}

			}

			if(empty($error)){

				# 数チェック
				if($loop == $count){


					/************************************************
					**
					**	決済処理
					**	============================================
					**	
					**
					************************************************/

					# pay_flg 判定 無料ユーザー
					if($members_data['status'] == 3) {
						$pay_flg										= 2;
					# 定額ユーザー
					}elseif($members_data['status'] == 2) {
						$pay_flg										= 3;
					# 通常ユーザー
					}elseif($members_data['pay_count'] != 0){

						# 無課金
						if($members_data['pay_count'] == 0){
							$pay_flg									= 2;
						# 課金
						}else{
							$pay_flg									= 1;
						}

					# その他
					}else{
						$pay_flg										= 0;
					}

					$datetime											= date("YmdHis");

					# 一日一回無料
					if($data['type'] == "free"){

						# membersのgacha_date を今日にアップデート
						$members_update['last_gacha_date']				= date("Y-m-d");

					}else{

						# チケットで処理
						if($data['pay'] == "point"){

							# ガチャ回数分処理
							for($count=0;$count<$loop;$count++){

								# 毎回初期化
								unset($point_result['members']['point']);
								unset($point_result['members']['s_point']);
								unset($point_result['members']['f_point']);

								$point_result							= $pointsetModel->makePointConsume($pointsets_data,$members_data);

								# OK
								if(empty($point_result['error']) && isset($point_result['points'])){

									foreach($point_result['points'] as $points_key => $points_array){

										if(isset($points_array['point'])){

											$i=0;
											foreach($points_array['point'] as $key => $value){

												$points_insert			= array();
												$points_insert			= array(
													'user_id'			=> $members_data['id'],
													'site_cd'			=> $members_data['site_cd'],
													'sex'				=> $members_data['sex'],
									                'ad_code'			=> $members_data['ad_code'],
									                'domain_flg'		=> $members_data['domain_flg'],
													'point'				=> $value,
													'point_no_id'		=> $points_array['point_no_id'],
													'campaign_id'		=> $campaign_id,
									                'point_type'		=> $key,
													'log_date'			=> $datetime,
									                'pay_flg'			=> $pay_flg
												);

												# 【insert】points
												$database->insertDb("points",$points_insert);

												$i++;

											}

										}

									}

									# members上書き
									if(isset($point_result['members']['point'])){
										$members_data['point']			= $point_result['members']['point'];
										$members_update['point']		= $point_result['members']['point'];
									}

									if(isset($point_result['members']['s_point'])){
										$members_data['s_point']		= $point_result['members']['s_point'];
										$members_update['s_point']		= $point_result['members']['s_point'];
									}

									if(isset($point_result['members']['f_point'])){
										$members_data['f_point']		= $point_result['members']['f_point'];
										$members_update['f_point']		= $point_result['members']['f_point'];
									}

								# ERROR
								}else{

									$error								= 42;
									$errormessage						= TICKET_NAME."が足りません。";
									break;

								}

							}

							$mainClass->debug($point_result);



						# PFの決済で処理
						}elseif($data['pay'] == "coin"){

							# 処理OK
							if($members_data['point'] >= $point_gacha){

								$consume_point							= $members_data['point'];

								# ガチャ回数分処理
								for($count=0;$count<$loop;$count++){

									if($consume_point >= $point_data['gacha']){

										$consume_point					= $consume_point - $point_data['gacha'];

										# PF通貨でガチャ引いた場合は消費としてpointsに入れない
										/*
										$points_insert					= array();
										$points_insert					= array(
											'user_id'					=> $members_data['id'],
											'site_cd'					=> $members_data['site_cd'],
											'sex'						=> $members_data['sex'],
							                'ad_code'					=> $members_data['ad_code'],
							                'domain_flg'				=> $members_data['domain_flg'],
											'point'						=> $point_data['gacha'],
											'point_no_id'				=> $point_no_coin,
											'campaign_id'				=> $campaign_id,
							                'point_type'				=> 0,
											'log_date'					=> $datetime,
							                'pay_flg'					=> $pay_flg
										);

										# 【insert】points
										$database->insertDb("points",$points_insert);
										*/

									}else{

										$error							= 43;
										$errormessage					= COIN_NAME."が足りません。";
										break;

									}


								}

								if(empty($error)){

									$members_data['point']				= $consume_point;
									$members_update['point']			= $consume_point;

								}


							# ERROR
							}else{

								$error									= 43;
								$errormessage							= COIN_NAME."が足りません。";

							}

						}

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

						# s_point 追加分
						if(!empty($update_s_point)){

							if(isset($members_update['s_point'])){
								$members_update['s_point']				= $members_update['s_point'] + $update_s_point;
							}else{
								$members_update['s_point']				= $members_data['s_point'] + $update_s_point;
							}

						}

						# f_point 追加分
						if(!empty($update_f_point)){

							if(isset($members_update['f_point'])){
								$members_update['f_point']				= $members_update['f_point'] + $update_f_point;
							}else{
								$members_update['f_point']				= $members_data['f_point'] + $update_f_point;
							}

							# ユーザーf_pointは毎日ログイン配布される分だけしか所持できない為
							if($members_update['f_point'] > FREE_POINT_LIMIT){
								$members_update['f_point']				= FREE_POINT_LIMIT;
							}

						}

						# UPDATE WHERE
						$members_update_where							= "id = :id";
						$members_update_conditions[':id']				= $members_data['id'];

						# 【UPDATE】 / members
						$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

						# ポイント当たった場合はここでログ入れる
						if(!empty($points_insert_flg)){

							$gacha_recv_point_no_id						= $point_no_array[$point_name_array['gacha_recv']][2];

							foreach($point_recv as $point_type => $point_value){

								foreach($point_value as $key => $value){

									$points_recv_insert					= array();
									$points_recv_insert					= array(
										'user_id'						=> $members_data['id'],
										'site_cd'						=> $members_data['site_cd'],
										'sex'							=> $members_data['sex'],
						                'ad_code'						=> $members_data['ad_code'],
						                'domain_flg'					=> $members_data['domain_flg'],
										'point'							=> $value,
										'point_no_id'					=> $gacha_recv_point_no_id,
										'campaign_id'					=> $campaign_id,
						                'point_type'					=> $point_type,
										'log_date'						=> $datetime,
						                'pay_flg'						=> $pay_flg
									);

									# 【insert】points
									$database->insertDb("points",$points_recv_insert);

								}

							}

						}


						# PAYS UPDATE
						if(!empty($pays_id)){

							$pays_update['finish']						= 1;

							# UPDATE WHERE
							$pays_update_where							= "id = :id";
							$pays_update_conditions[':id']				= $pays_id;

							# 【UPDATE】 / pays
							$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);

						}


						$mainClass->debug($gacha_list);

						$database->commit();
						$exection										= 1;

					}else{
						$error											= 6;
						$errormessage									= "正常に処理できませんでした";
					}

				}else{

					$error												= 7;
					$errormessage										= "正常に処理できませんでした";

				}

				if(!empty($error)){
					$database->rollBack();
				}


			# ROLLBACK : 巻き戻し
			}else{

				if(empty($errormessage)){
					$database->rollBack();
					$errormessage									= "正常に処理できませんでした";
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
		**	finish.incをここで読んで終了
		**
		************************************************/

		# VIEW FILE チェック & パス生成
		$view_directory								= $mainClass->getViewDirectory($directory,"finish",$device_file);

		# 読み込み
		include_once($view_directory);

		# DEBUG
		if(defined("SYSTEM_CHECK")){

			# SYSTEM DEBUG
			$mainClass->outputDebugSystem();

		}

		# 終了
		exit();


	}

}


################################## FILE END #####################################
?>