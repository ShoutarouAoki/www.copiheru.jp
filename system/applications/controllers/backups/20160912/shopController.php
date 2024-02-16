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

$value_array				= array('page','type','campaign_id','shops_id','pays_id');
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

# CAMPAIGN ID
if(!empty($_POST['campaign_id'])){
	$data['campaign_id']	= $_POST['campaign_id'];
}

if(empty($data['campaign_id'])){
	$data['campaign_id']	= 0;
}

# SHOPS ID
if(!empty($_POST['shops_id'])){
	$data['shops_id']		= $_POST['shops_id'];
}

# PAYS ID
if(!empty($_POST['pays_id'])){
	$data['pays_id']		= $_POST['pays_id'];
}


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

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
	**	SHOPページトップ
	**
	************************************************/

	if($data['page'] == "index"){



		/************************************************
		**
		**	キャンペーン
		**	============================================
		**
		**
		************************************************/

		# CAMPAIGN
		$campaign_id								= 0;
		$campaign_data								= $campaignsetModel->getCampaignsetData($members_data);

		# campaign_type が1か5だったら販売キャンペーン
		if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 1 || $campaign_data['campaign_type'] == 5){
			$campaign_id							= $campaign_data['id'];
		}














	/************************************************
	**
	**	LIST
	**	============================================
	**	一覧
	**
	************************************************/


	}elseif($data['page'] == "list"){


		# 初期化
		$error										= NULL;
		$errormessage								= NULL;
		$shop_category								= NULL;
		$page_title									= "ショップ";


		# ここでショップデータ
		if(!empty($data['type'])){
			if(isset($shop_category_array[$data['type']])){
				$shop_category						= $shop_category_array[$data['type']];
				$page_title							= $shop_name_array[$data['type']];
			}
		}


		# 処理開始
		if(!empty($shop_category)){

			$shops_conditions						= array();
			$shops_conditions						= array(
				'shop_id'							=> 0,
				'item_id'							=> 0,
				'date'								=> date("YmdHis"),
				'category'							=> $shop_category,
				'type'								=> 0,
				'campaign_id'						=> $data['campaign_id'],
				'status'							=> 0,
				'order'								=> "rank"
			);

			$shops_rtn								= $shopModel->getShopList($shops_conditions);

			$i=0;
			while($shops_data = $database->fetchAssoc($shops_rtn)){

				$shops_list['id'][$i]				= $shops_data['id'];
				$shops_list['name'][$i]				= $shops_data['name'];
				$shops_list['image'][$i]			= $shops_data['image'];
				$shops_list['description'][$i]		= $shops_data['description'];
				$shops_list['price'][$i]			= $shops_data['price'];
				$shops_list['unit'][$i]				= $shops_data['unit'];

				$i++;

			}

			$database->freeResult($shops_rtn);

		}




	/************************************************
	**
	**	BUY
	**	============================================
	**	購入ページ
	**	一旦プラットフォームの決済ページへ遷移
	**
	************************************************/

	}elseif($data['page'] == "buy"){

		# 初期化
		$error										= NULL;
		$errormessage								= NULL;
		$shop_category								= NULL;

		# カテゴリ
		if(!empty($data['type'])){
			if(isset($shop_category_array[$data['type']])){
				$shop_category						= $shop_category_array[$data['type']];
			}
		}else{
			$error									= 1;
			$errormessage							= "不正なアクセスです";
		}

		# PAGE CHECK
		if(empty($data['shops_id']) || empty($shop_category)){
			$error									= 2;
			$errormessage							= "不正なアクセスです";
		}

		# OK
		if(empty($error)){


			/************************************************
			**
			**	shops data
			**	============================================
			**	該当アイテムのデータ
			**
			************************************************/

			# pointsets
			$shops_data								= $shopModel->getShopDataById($data['shops_id']);

			# 処理開始
			if(!empty($shops_data['id'])){

				# ITEM DATA
				$item_data							= array();
				$item_data							= array(
					'itemId'						=> $shops_data['id'],
					'itemName'						=> $shops_data['name'],
					'unitPrice'						=> $shops_data['price'],
					'quantity'						=> 1,
					'imageUrl'						=> HTTP_ITEM_IMAGE."/".$shops_data['image'],
					'description'					=> $shops_data['description'],
					'callbackUrl'					=> HTTP_SETTLEMENT."/".$directory."/".$data['type']."/".$shops_data['id']."/",
					'finishPageUrl'					=> HTTP_DOMAIN."/".$directory."/check/".$data['type']."/".$data['campaign_id']."/".$shops_data['id']."/"
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
						$pay_amount					= $shops_data['price'];

						# SETTLEMENT ID
						$point_no_name				= $directory."_".$data['type'];
						$settlement_id				= $point_no_array[$point_name_array[$point_no_name]][2];

						# LIMIT TIME (決済申込開始から30分で処理期限終了)
						$limit_time					= date("YmdHis",strtotime("+30 minutes",strtotime($ordered_time)));


						# 未処理で残ってしまってるレコードがあるかチェック
						$pays_conditions			= array();
						$pays_conditions			= array(
							'user_id'				=> $members_data['id'],
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
								'campaign_id'		=> $data['campaign_id'],
								'domain_flg'		=> $members_data['domain_flg'],
								'sex'				=> $members_data['sex'],
				                'ad_code'			=> $members_data['ad_code'],
				                'reg_date'			=> $members_data['reg_date'],
								'order_date'		=> $ordered_time,
								'pay_date'			=> 0,
								'pay_amount'		=> $pay_amount,			// -> 必要PF通貨数(にじコイン数)
								'settlement_id'		=> $settlement_id,
								'object'			=> $shops_data['id'],	// -> shopsテーブル ID
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
								'campaign_id'		=> $data['campaign_id'],
								'domain_flg'		=> $members_data['domain_flg'],
								'sex'				=> $members_data['sex'],
				                'ad_code'			=> $members_data['ad_code'],
				                'reg_date'			=> $members_data['reg_date'],
								'order_date'		=> $ordered_time,
								'pay_amount'		=> $pay_amount,				// -> 必要PF通貨数(にじコイン数)
								'settlement_id'		=> $settlement_id,
								'object'			=> $shops_data['id'],		// -> shopsテーブル ID
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

							$error					= 4;
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

			}else{

				$error								= 3;
				$errormessage						= "アイテム情報が見つかりません。";

			}

		}



	/************************************************
	**
	**	check
	**	============================================
	**	購入・決済処理確認ページ
	**
	************************************************/

	}elseif($data['page'] == "check"){


		/************************************************
		**
		**	MASTER DATABASE切り替え
		**
		************************************************/

		# AUTHORITY / 既にマスターに接続してるかチェック
		$db_auth								 = $database->checkAuthority();

		# DATABASE CHANGE / スレーブだったら
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check							= 1;

		}


		# 初期化
		$error									= NULL;
		$errormessage							= NULL;
		$errorthrough							= NULL;

		# カテゴリ
		if(!empty($data['type'])){
			if(isset($shop_category_array[$data['type']])){
				$shop_category					= $shop_category_array[$data['type']];
			}
		}else{
			$error								= 1;
			$errormessage						= "不正なアクセスです";
		}

		# PAGE CHECK
		if(empty($data['shops_id']) || empty($shop_category)){
			$error								= 2;
			$errormessage						= "不正なアクセスです";
		}


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

						$errorthrough			 = 1;


					# ユーザーキャンセル -> こいつはリダイレクト
					}elseif($payment_result['entry']['status'] == 3){

						$error					= 33;
						$errorthrough			= 1;

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
					$point_no_name				= $directory."_".$data['type'];
					$settlement_id				= $point_no_array[$point_name_array[$point_no_name]][2];

					# 決済情報チェック
					$pays_conditions			= array();
					$pays_conditions			= array(
						'user_id'				=> $members_data['id'],
						"settlement_id"			=> $settlement_id,
						'sid'					=> $_REQUEST['paymentId'],
						'clear'					=> 1,
						'finish'				=> 0,
						'error'					=> 0,
						'status'				=> 0
					);

					$pays_data					= $payModel->getPayData($pays_conditions);

					# 決済データOK
					if(!empty($pays_data['id'])){

						# CLOSE DATABASE
						$database->closeDb();
						$database->closeStmt();

						$redirect_url			= "/".$directory."/finish/".$data['type']."/".$data['campaign_id']."/".$pays_data['object']."/".$pays_data['id']."/";

						# REDIRECT
						header("Location: ".$redirect_url);
						exit();

					# 決済データエラー
					}else{

						$error					= 6;
						$errormessage			= COIN_NAME."の処理が正常ではありません";
						$errorthrough			= 1;

					}

				}

			# 通信エラー
			}else{

				$error							= $payment_result['error'];
				$errormessage					= $payment_result['message'];

			}

		}

		# ERROR
		if(!empty($error) && !empty($_REQUEST['paymentId']) && empty($errorthrough)){

			$error_update['error']				= $error;
			$error_where						= "sid = :sid";
			$error_conditions[':sid']			= $_REQUEST['paymentId'];

			# 【UPDATE】 / pays
			$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);

		}


		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}


	/************************************************
	**
	**	FINISH
	**	============================================
	**	購入完了ページ
	**
	************************************************/

	}elseif($data['page'] == "finish"){

		# このページではヘッダーとフッターを消す(スマフォ)
		$header_hide								= 1;
		$footer_hide								= 1;

		# サブヘッダーとサブフッターを表示
		$sub_header									= 1;
		$sub_footer									= 1;

		# 初期化
		$result										= NULL;
		$error										= NULL;
		$errormessage								= NULL;

		if(empty($data['shops_id']) || empty($data['pays_id'])){
			$error									= 1;
			$errormessage							=  "不正なアクセスです";
		}

		# OK
		if(empty($error)){

			$shops_data								= $shopModel->getShopDataById($data['shops_id']);
			$pays_data								= $payModel->getPayDataById($data['pays_id']);

			# ERROR
			if(empty($shops_data['id']) || empty($pays_data['id'])){
				$error								= 2;
				$errormessage						=  "購入情報が見つかりません";
			}elseif($shops_data['id'] != $pays_data['object']){
				$error								= 3;
				$errormessage						=  "購入情報が正常ではありません";
			}elseif($members_data['id'] != $pays_data['user_id']){
				$error								= 4;
				$errormessage						=  "お客様情報が一致しません";

			# ここから決済データの内容チェック
			}else{

				# まずここで受け取りが完了しているのであればリダイレクト
				if($pays_data['finish'] > 0){

					# CLOSE DATABASE
					$database->closeDb();
					$database->closeStmt();

					# REDIRECT
					header("Location: /".$directory."/");
					exit();

				}

				if($pays_data['status'] != 0){
					$error							= 5;
					$errormessage					=  "決済情報が正常ではありません。";
				}elseif($pays_data['clear'] != 1){
					$error							= 6;
					$errormessage					=  "決済正常に終了しておりません。";
				}elseif($pays_data['finish'] != 0){
					$error							= 7;
					$errormessage					=  "決済正常に終了しておりません。";
				}elseif($pays_data['error'] != 0){
					$error							= 8;
					$errormessage					=  "決済正常に終了しておりません。";
				}

				# 全ての処理が正常ならpays の finishをアップデート
				if(empty($error)){

					# AUTHORITY / 既にマスターに接続してるかチェック
					$db_auth						 = $database->checkAuthority();

					# DATABASE CHANGE / スレーブだったら
					if(empty($db_auth)){

						# CLOSE DATABASE SLAVE
						$database->closeDb();

						# CONNECT DATABASE MASTER
						$database->connectDb(MASTER_ACCESS_KEY);

						$db_check					= 1;

					}

					# PAYS UPDATE
					$pays_update					= array();
					$pays_update					= array(
						'finish'					=> 1
					);

					# UPDATE WHERE
					$pays_update_where				= "id = :id";
					$pays_update_conditions[':id']	= $pays_data['id'];

					# 【UPDATE】 / pays
					$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);

					$result								= 1;

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

	}

}


################################## FILE END #####################################
?>