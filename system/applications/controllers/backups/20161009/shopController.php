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
	$data['page']			= "list";
}

# TYPE
if(!empty($_POST['type'])){
	$data['type']			= $_POST['type'];
}

if(empty($data['type'])){
	$data['type']			= "point";
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
		**	ページトップバナー 設定取得
		**
		************************************************/

		# BANNER
		$banner_conditions							= array();
		$banner_conditions							= array(
			'file_type'								=> 7,
			'category'								=> $banner_image_category,
			'device'								=> $device_number,
			'site_cd'								=> $members_data['site_cd'],
			'target_id'								=> 0,
			'display_check'							=> 1,
			'status'								=> 0
		);

		$banner_rtn									= $imageModel->getImageList($banner_conditions);

		$i=0;
		while($banner_data = $database->fetchAssoc($banner_rtn)){

			$banner_list['id'][$i]					= $banner_data['id'];
			$banner_list['image'][$i]				= $banner_data['img_name'];
			$banner_list['link'][$i]				= $banner_data['img_key'];

			$i++;

		}

		$database->freeResult($banner_rtn);



		/************************************************
		**
		**	キャンペーンボタンバナー 設定取得
		**
		************************************************/

		if(!empty($campaign_id)){

			$point_button_hidden					= NULL;
			$item_button_hidden						= NULL;

			# BUTTON
			$button_conditions						= array();
			$button_conditions						= array(
				'file_type'							=> 8,
				'category'							=> $button_image_category,
				'site_cd'							=> $members_data['site_cd'],
				'target_id'							=> $campaign_data['id'],
				'status'							=> 0
			);

			$button_rtn								= $imageModel->getImageList($button_conditions);

			$i=0;
			while($button_data = $database->fetchAssoc($button_rtn)){

				$button_list['id'][$i]				= $button_data['id'];
				$button_list['image'][$i]			= $button_data['img_name'];
				$button_list['link'][$i]			= $button_data['img_key'];
				$button_list['target_id'][$i]		= $button_data['target_id'];

				if($button_data['img_key'] == "point"){
					$point_button_hidden			= 1;
				}

				if($button_data['img_key'] == "item"){
					$item_button_hidden				= 1;
				}

				$i++;

			}

			$database->freeResult($button_rtn);

		}


		/************************************************
		**
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

			$i++;

		}

		$database->freeResult($itembox_rtn);


	/************************************************
	**
	**	LIST
	**	============================================
	**	一覧
	**
	************************************************/


	}elseif($data['page'] == "list"){

		# 初期化
		$error											= NULL;
		$errormessage									= NULL;
		$shop_category									= NULL;
		$page_title										= "ショップ";

		# ここでショップデータ
		if(!empty($data['type'])){
			if(isset($shop_category_array[$data['type']])){
				$shop_category							= $shop_category_array[$data['type']];
				$page_title								= $shop_name_array[$data['type']];
			}
		}


		# 処理開始
		if(!empty($shop_category)){


			/************************************************
			**
			**	キャンペーン
			**	============================================
			**
			**
			************************************************/

			# CAMPAIGN
			$campaign_id								= 0;

			# もし別のキャンペーンIDを持ってこのページに来たら一応その商品出す
			if(!empty($data['campaign_id'])){

				$campaign_id							= $data['campaign_id'];

			# なければ開催中のキャンペーンがあるかチェック
			}else{
				
				$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);

				# campaign_type が1か5だったら販売キャンペーン
				if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 1 || $campaign_data['campaign_type'] == 5){
					$campaign_id						= $campaign_data['id'];
				}

			}


			/************************************************
			**
			**	キャンペーンボタンバナー 設定取得
			**	============================================
			**	ここは渡されたキャンペーンIDに関わらず
			**	現時点で開催されいて、HITしているキャンペーンの
			**	バナーのみを出す
			**
			************************************************/

			if(!empty($campaign_data['id'])){

				$point_button_hidden					= NULL;
				$item_button_hidden						= NULL;

				# BUTTON
				$button_conditions						= array();
				$button_conditions						= array(
					'file_type'							=> 8,
					'category'							=> $button_image_category,
					'site_cd'							=> $members_data['site_cd'],
					'target_id'							=> $campaign_data['id'],
					'status'							=> 0
				);

				$button_rtn								= $imageModel->getImageList($button_conditions);

				$i=0;
				while($button_data = $database->fetchAssoc($button_rtn)){

					if($button_data['img_key'] == $data['type']){
						continue;
					}

					$button_list['id'][$i]				= $button_data['id'];
					$button_list['image'][$i]			= $button_data['img_name'];
					$button_list['link'][$i]			= $button_data['img_key'];
					$button_list['target_id'][$i]		= $button_data['target_id'];

					if($button_data['img_key'] == "point"){
						$point_button_hidden			= 1;
					}

					if($button_data['img_key'] == "item"){
						$item_button_hidden				= 1;
					}

					$i++;

				}

				$database->freeResult($button_rtn);

			}


			/************************************************
			**
			**	キャンペーン用アイテム抽出
			**
			************************************************/

			$campaign_item								= NULL;

			if(!empty($campaign_id)){

				$shops_conditions						= array();
				$shops_conditions						= array(
					'shop_id'							=> 0,
					'item_id'							=> 0,
					'category'							=> $shop_category,
					'type'								=> 0,
					'campaign_id'						=> $campaign_id,
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

				# CHECK
				if($i > 0){
					$campaign_item						= 1;
				}


			}


			/************************************************
			**
			**	キャンペーン用アイテムなければ通常アイテム
			**
			************************************************/

			if(empty($campaign_item)){

				$shops_conditions						= array();
				$shops_conditions						= array(
					'shop_id'							=> 0,
					'item_id'							=> 0,
					'date'								=> date("YmdHis"),
					'category'							=> $shop_category,
					'type'								=> 0,
					'campaign_id'						=> 0,
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



		}


		/************************************************
		**
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

			$i++;

		}

		$database->freeResult($itembox_rtn);




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

			# SHOPS DATA
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

					# ユーザーキャンセル
					if($item_result['entry']['status'] == 3){

						$error						= $item_result['entry']['status'];

						# REDIRECT
						$mainClass->redirect("/".$directory."/");
						exit();

					# 期限切れ
					}elseif($item_result['entry']['status'] == 4){

						$error						= $item_result['entry']['status'];
						$errormessage				= "お申込頂いた".COIN_NAME."決済の期限が切れております。<br />お手数ですが再度お試し下さい。";

					# HTTPステータスキャンセル
					}elseif($item_result['entry']['status'] == 5){

						$error						= $item_result['entry']['status'];
						$errormessage				= "決済処理でエラーが起こった可能性があります。<br />お問い合わせよりご連絡下さい。";

					}

					# 正常処理
					if(empty($error) && $item_result['entry']['status'] == 1){

						$transaction_url			= $item_result['entry']['transactionUrl'];

						# 決済画面へREDIRECT
						$mainClass->redirect($transaction_url);
						exit();

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

						# REDIRECT
						$mainClass->redirect("/".$directory."/");
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
						$mainClass->redirect($redirect_url);
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
			$database->updateDb("pays",$error_update,$error_where,$error_conditions);

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

					# REDIRECT
					$mainClass->redirect("/".$directory."/");
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