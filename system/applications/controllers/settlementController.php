<?php
################################ FILE MANAGEMENT ################################
##
##	settlementController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	CONNECTION SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	プラットフォーム側との決済通信処理
##	危険性を考慮してconnectionControllerとは切り分けて処理
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

/** MEMBER MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MemberModel.php");

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

/** PAY MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PayModel.php");

/** SHOP MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ShopModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");

/** GACHA MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/GachaModel.php");//20180319 add by A.cos

################################# POST ARRAY ####################################

$value_array				= array('page','type','id');
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

if(empty($data['id'])){
	$data['id']				= 0;
}


################################# CONNECT DB ####################################

# CLOSE DATABASE SLAVE
$database->closeDb();

# CONNECT DATABASE MASTER
$database->connectDb(MASTER_ACCESS_KEY);

################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# MEMBER MODEL
$memberModel				= new MemberModel($database,$mainClass);

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);

# PAY MODEL
$payModel					= new PayModel($database,$mainClass);

# SHOP MODEL
$shopModel					= new ShopModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# CAMPAIGNSET MODEL
$campaignsetModel			= new CampaignsetModel($database,$mainClass);

# GACHA MODEL
$gachaModel					= new GachaModel($database,$mainClass);//20180319 add by A.cos

##################################### FUNCTIONS ######################################

/** FUNCTIONS FILE **/
require_once(dirname(__FILE__)."/functions/gachaController.inc");

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
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){
















	/************************************************
	**
	**	ガチャ / 決済通知処理
	**	============================================
	**	PFからの戻り値
	**
	**【opensocial_app_id】 APP ID
	**	445
	**
	**【opensocial_owner_id】 membersのuser_id
	**	4151
	**
	**【opensocial_viewer_id】membersのuser_id
	**	4151
	**
	**【orderedTime】 paysのpay_date
	**	20160905124811
	**
	**【paymentId】paysのsid
	**	445-4151-20160905124811047513
	**
	**【status】状態
	**	2
	**
	**
	************************************************/

	}elseif($data['page'] == "gacha"){

		$loop												= 0;
		$error												= NULL;
		$errormessage										= NULL;
		$errorthrough										= NULL;
		$subject											= NULL;
		$message											= NULL;

		# 決済処理 ステータス OK
		if(!empty($_REQUEST['status']) && $_REQUEST['status'] == 2){

			# APP IDがあるか
			if(empty($_REQUEST['opensocial_app_id'])){

				$error										= 2;
				$errormessage								= "APP ID NULL";

			# SITE IDが一致するか
			}elseif(!empty($_REQUEST['opensocial_app_id']) && $_REQUEST['opensocial_app_id'] != APP_ID){

				$error										= 3;
				$errormessage								= "APP ID 不一致";

			# USER IDがない
			}elseif(empty($_REQUEST['opensocial_viewer_id'])){

				$error										= 4;
				$errormessage								= "USER ID NULL";

			# PAYMENT IDがない
			}elseif(empty($_REQUEST['paymentId'])){

				$error										= 5;
				$errormessage								= "PAYMENT ID NULL";

			# PAGE TYPE NULL
			}elseif(empty($data['type'])){
				$error										= 6;
				$errormessage								= "不正なアクセスです";
			}


			# 処理開始
			if(empty($error)){

				# USER ID
				$user_id									= $_REQUEST['opensocial_viewer_id'];

				# まずmembersのチェック
				$members_data								= $memberModel->getMemberDataByUserId($user_id);

				if(empty($members_data['id'])){
					$error									= 7;
					$errormessage							= "ユーザー情報取得できませんでした。";
				}

				# members status
				if(empty($error) && $members_data['status'] < 8){


					/************************************************
					**
					**	キャンペーン
					**	============================================
					**
					**
					************************************************/

					$campaign_id							= 0;
					$campaign_point							= NULL;


					# CAMPAIGN
					if(!empty($data['id'])){

						$campaign_data						= $campaignsetModel->checkCampaign($data['id']);

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

					}


					/************************************************
					**
					**	pointsets
					**	============================================
					**	pointsetsデータ取得
					**	ガチャ消費ポイント
					**
					************************************************/

					$loop									= $gacha_loop_array[$data['type']];
					$gacha_point							= 0;
					$unit_price								= 0;

					# POINT NO
					$point_no_point							= $point_no_array[$point_name_array['gacha_point']][2];
					//20180315 update by A.cos
					switch($data['type']){
						case "limitted1":
						case "limitted2":
						case "limitted3":
							$point_no_coin				= $point_no_array[$point_name_array['limitted_gacha_coin']][2];
							break;
						case "stepup1":
						case "stepup2":
						case "stepup3":
							$point_no_coin				= $point_no_array[$point_name_array['stepup_gacha_coin']][2];
							break;
						default:
							$point_no_coin				= $point_no_array[$point_name_array['gacha_coin']][2];
							break;
					}
					//$point_no_coin							= $point_no_array[$point_name_array['gacha_coin']][2];
					$point_no_id							= $point_no_point.",".$point_no_coin;

					# pointsets
					$pointsets_data							= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

					$point_data['gacha_point']				= DEFAULT_GACHA_POINT;
					$point_data['gacha_coin']				= DEFAULT_GACHA_POINT;

					if(!empty($pointsets_data)){

						$count								= count($pointsets_data);
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

					//"limitted1", "stepup1"の場合はDBから値段を取り出す
					switch($data['type']){
						case "limitted1":
						case "limitted2":
						case "limitted3":
						case "stepup1":
						case "stepup2":
						case "stepup3":
							$stepup_data = NULL;
							list($stepup_data,$stepup_use_rest,$stepup_use_max) = getStepupGachaSettingData($gachaModel, $members_data["id"], $campaign_id, $stepup_gacha_number[$data['type']]);
							//mail("eikoshi@k-arat.co.jp","settlement",var_export($stepup_data, true),"From:info@mailanime.net");
							if(isset($stepup_data["id"])){
								$point_data['gacha_coin'] = $stepup_data["price"];
								$point_data['gacha_point'] = $point_data['gacha_point']*$stepup_data["times"];
							}
							break;
					}

					
					# ガチャを引く為に必要なサイト内ポイント数
					$gacha_point							= $point_data['gacha_point'] * $loop;

					# ガチャを引く為に必要なPF通貨数
					$unit_price								= $point_data['gacha_coin'];

					# PAY AMOUNT
					$pay_amount								= $unit_price * $loop;

					# SETTLEMENT ID
					$settlement_id							= $point_no_coin;

					# PAYMENT ID
					$payment_id								= $_REQUEST['paymentId'];
					$ordered_time							= $_REQUEST['orderedTime'];

					# LIMIT TIME (決済申込開始から30分で処理期限終了)
					$limit_time								= date("YmdHis",strtotime("+30 minutes",strtotime($ordered_time)));

					# NOW DATE TIME
					$datetime								= date("YmdHis");


					/************************************************
					**
					**	TRANSACTION
					**	============================================
					**	ここからインサート処理
					**
					************************************************/

					# TRANSACTION START
					$database->beginTransaction();


					# PAYS INSERT
					$pays_insert							= array();
					$pays_insert							= array(
						'site_cd'							=> $members_data['site_cd'],
						'user_id'							=> $members_data['id'],
						'campaign_id'						=> $campaign_id,
						'domain_flg'						=> $members_data['domain_flg'],
						'sex'								=> $members_data['sex'],
		                'ad_code'							=> $members_data['ad_code'],
		                'reg_date'							=> $members_data['reg_date'],
						'order_date'						=> $ordered_time,
						'pay_date'							=> $datetime,
						'pay_amount'						=> $pay_amount,			// -> 必要PF通貨数(にじコイン数)
						'settlement_id'						=> $settlement_id,
						'object'							=> $gacha_point,		// -> ガチャを回す為のサイト内必要ポイント
						'sid'								=> $payment_id,
						'clear'								=> 1,
						'limit_time'						=> $limit_time,
		                'status'							=> 0
					);

					# 【insert】pays
					$insert_id								= $database->insertDb("pays",$pays_insert);

					# INSERT OK
					if(!empty($insert_id)){

						# MEMBERS UPDATE
						$new_point							= $members_data['point'] + $gacha_point;

						$members_update['point']			= $new_point;
						$members_update['pay_count']		= $members_data['pay_count'] + 1;
						$members_update['pay_amount']		= $members_data['pay_amount'] + $pay_amount;
						$members_update['last_pay_date']	= $datetime;

						if(empty($members_data['first_pay_date']) || $members_data['first_pay_date'] == 0){
							$members_update['first_pay_date']	= $datetime;
						}

						# UPDATE WHERE
						$members_update_where				= "id = :id";
						$members_update_conditions[':id']	= $members_data['id'];

						# 【UPDATE】 / members
						$result								= $database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);


						# pointsログ取り
						$point_no_id						= $point_no_array[$point_name_array['buy_gacha_coin']][2];

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

						# POINTS INSERT
						$points_insert						= array();
						$points_insert						= array(
							'user_id'						=> $members_data['id'],
							'site_cd'						=> $members_data['site_cd'],
							'sex'							=> $members_data['sex'],
			                'ad_code'						=> $members_data['ad_code'],
			                'domain_flg'					=> $members_data['domain_flg'],
							'point'							=> $gacha_point,
							'point_no_id'					=> $point_no_id,
							'campaign_id'					=> $campaign_id,
			                'point_type'					=> 0,
							'log_date'						=> $datetime,
			                'pay_flg'						=> $pay_flg
						);

						# 【insert】points
						$points_insert_id					= $database->insertDb("points",$points_insert);

						if(empty($points_insert_id)){
							$result							= NULL;
						}


					}

					# 処理
					if(!empty($result)){

						# COMMIT
						$database->commit();

						# OK
						http_response_code(200);

					}else{

						$error								= 9;
						$errormessage						= "DB ERROR";

					}

				}else{

					if(empty($error)){
						$error								= 8;
						$errormessage						= "ユーザーが退会もしくは削除済み";
					}

				}


			}


			# エラーの場合はロールバックしてエラー情報をインサート
			if(!empty($error)){

				if(!empty($_REQUEST['paymentId']) && empty($errorthrough)){

					# ROLLBACK
					$database->rollBack();

					# PAYS ERROR INSERT
					$error_insert['site_cd']				= SITE_CD;

					if(!empty($members_data['id'])){
						$error_insert['user_id']			= $members_data['id'];
						$error_insert['sex']				= $members_data['sex'];
						$error_insert['ad_code']			= $members_data['ad_code'];
						$error_insert['reg_date']			= $members_data['reg_date'];
					}

					if(!empty($_REQUEST['orderedTime'])){
						$error_insert['order_date']			= $_REQUEST['orderedTime'];
					}else{
						$error_insert['order_date']			= date("YmdHis");
					}

					if(!empty($pay_amount)){
						$error_insert['pay_amount']			= $pay_amount;
					}

					if(!empty($settlement_id)){
						$error_insert['settlement_id']		= $settlement_id;
					}

					if(!empty($gacha_point)){
						$error_insert['object']				= $gacha_point;
					}

					if(!empty($_REQUEST['paymentId'])){
						$error_insert['sid']				= $_REQUEST['paymentId'];
					}

					$error_insert['error']					= $error;

					# 【insert】pays
					$error_id								= $database->insertDb("pays",$error_insert);

				}

			}

		# ERROR
		}else{

			# ユーザーキャンセル
			if($_REQUEST['status'] == 3){

			# 期限切れ
			}elseif($_REQUEST['status'] == 4){

			# それ以外はエラー
			}else{

				$error										= $_REQUEST['status'];
				$errormessage								= "ステータス異常";

			}

		}


		# ERROR
		if(!empty($error)){

			$subject										= "Settlement Error";
			$message										= "決済エラーが発生致しました。\n";
			$message										.= "【ERROR NO】\n".$error."\n\n";
			$message										.= "【STATUS】\n".$errormessage."\n\n";
			$message										.= "【TIME】\n".date("Y-m-d H:i:s")."\n\n";
			ml($subject,$message,$_REQUEST);

			# ERROR STATUS
			http_response_code(500);


		}



	/************************************************
	**
	**	ショップ / 決済通知処理
	**	============================================
	**	PFからの戻り値
	**
	**【opensocial_app_id】 APP ID
	**	445
	**
	**【opensocial_owner_id】 membersのuser_id
	**	4151
	**
	**【opensocial_viewer_id】membersのuser_id
	**	4151
	**
	**【orderedTime】 paysのorder_date
	**	20160905124811
	**
	**【paymentId】paysのsid
	**	445-4151-20160905124811047513
	**
	**【status】状態
	**	2
	**
	**
	************************************************/

	}elseif($data['page'] == "shop"){

		$result														= NULL;
		$acceptance													= NULL;
		$error														= NULL;
		$errormessage												= NULL;
		$errorthrough												= NULL;
		$subject													= NULL;
		$message													= NULL;
		$points_insert_flg											= NULL;

		# 決済処理 ステータス OK
		if(!empty($_REQUEST['status']) && $_REQUEST['status'] == 2){

			# APP IDがあるか
			if(empty($_REQUEST['opensocial_app_id'])){

				$error												 = 2;
				$errormessage										 = "APP ID NULL";

			# SITE IDが一致するか
			}elseif(!empty($_REQUEST['opensocial_app_id']) && $_REQUEST['opensocial_app_id'] != APP_ID){

				$error												 = 3;
				$errormessage										 = "APP ID 不一致";

			# USER IDがない
			}elseif(empty($_REQUEST['opensocial_viewer_id'])){

				$error												 = 4;
				$errormessage										 = "USER ID NULL";

			# PAYMENT IDがない
			}elseif(empty($_REQUEST['paymentId'])){

				$error												 = 5;
				$errormessage										 = "PAYMENT ID NULL";

			# ページタイプがない
			}elseif(empty($data['type'])){

				$error												 = 21;
				$errormessage										 = "PAGE TYPE NULL";

			# SHOP IDがない
			}elseif(empty($data['id'])){

				$error												 = 22;
				$errormessage										 = "SHOP ID NULL";

			}


			# 処理開始
			if(empty($error)){

				# USER ID
				$user_id											 = $_REQUEST['opensocial_viewer_id'];

				# まずmembersのチェック
				$members_data										 = $memberModel->getMemberDataByUserId($user_id);

				if(empty($members_data['id'])){
					$error											 = 6;
					$errormessage									 = "ユーザー情報取得できませんでした。";
				}

				# members status
				if(empty($error) && $members_data['status'] < 8){

					$shops_data										 = $shopModel->getShopDataById($data['id']);

					# チェック OK
					if(!empty($shops_data['id'])){

						# SETTLEMENT ID
						$point_no_name								 = $data['page']."_".$data['type'];
						$point_no_id								 = $point_no_array[$point_name_array[$point_no_name]][2];

						# PAY AMOUNT
						$pay_amount									 = $shops_data['price'];


						/************************************************
						**
						**	TRANSACTION
						**	============================================
						**	ここからインサート処理
						**
						************************************************/

						# TRANSACTION START
						$database->beginTransaction();

						# ポイント購入
						if($data['type'] == "point"){

							# pointsets
							$pointsets_data							= $pointsetModel->getPointset($point_no_id,$members_data,$shops_data['campaign_id']);

							$point									= DEFAULT_SHOP_POINT;

							if(!empty($pointsets_data)){

								$count								= count($pointsets_data);
								for($i=0;$i<$count;$i++){

									# ショップ購入で付与されるポイント単価
									if($pointsets_data[$i]['point_no_id'] == $point_no_id){
										$point						= $pointsets_data[$i]['point'];
									}

								}

							}

							# 購入枚数を乗算
							$point									= $point * $shops_data['unit'];

							# MEMBERS UPDATE
							$new_point								= $members_data['point'] + $point;

							$members_update['point']				= $new_point;

							# ここは通過
							$acceptance								= 1;

							# pointsをインサート
							$points_insert_flg						= 1;


						# アイテム購入
						}elseif($data['type'] == "item"){

							$shop_conditions						= array();
							$shop_conditions						= array(
								'shop_id'							=> $shops_data['id'],
								'category'							=> $shops_data['category'],
								'campaign_id'						=> $shops_data['campaign_id'],
								'status'							=> 0
							);

							$shop_rtn								= $shopModel->getShopList($shop_conditions);

							$i=0;
							while($shop_data = $database->fetchAssoc($shop_rtn)){

								$acceptance							= NULL;

								if(empty($shop_data['item_id'])){
									$error							= 99;
									$errormessage					= "アイテム情報に不備がありました。";
									break;
								}

								# 所持確認
								$itembox_conditions					= array();
								$itembox_conditions					= array(
									'user_id'						=> $members_data['id'],
									'item_id'						=> $shop_data['item_id'],
									'status'						=> 0
								);
								$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions,"id,unit");

								# 持ってれば加算
								if(!empty($itembox_data['id'])){

									$update_unit					= $itembox_data['unit'] + $shop_data['unit'];

									$itembox_update['unit']			= $update_unit;
									$itembox_update_where			= "id = :id";
									$itembox_update_conditions[':id']	= $itembox_data['id'];

									# 【UPDATE】 / itembox
									$return							= $database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

									if(!empty($return)){
										$acceptance					= 1;
									}

									# ERROR
									if(empty($acceptance)){
										$error						= 99;
										$errormessage				= "アイテム情報購入処理に不備がありました";
										break;
									}

								# なければ追加
								}else{

									$itembox_insert					= array();
									$itembox_insert					= array(
										'user_id'					=> $members_data['id'],
										'item_id'					=> $shop_data['item_id'],
										'unit'						=> $shop_data['unit'],
										'status'					=> 0
									);

									# 【INSERT】 / itembox
									$acceptance						= $database->insertDb("itembox",$itembox_insert);

									# ERROR
									if(empty($acceptance)){
										$error						= 99;
										$errormessage				= "アイテム情報購入処理に不備がありました";
										break;
									}

								}

								$i++;

							}

							$database->freeResult($shop_rtn);

							if($i == 0){
								$error								= 100;
								$errormessage						= "アイテム情報購入処理に不備がありました";
							}

							$point									= $pay_amount;

						}

						# 各種処理OK
						if(empty($error)){

							# PAYMENT ID
							$payment_id								= $_REQUEST['paymentId'];
							$ordered_time							= $_REQUEST['orderedTime'];
							$datetime								= date("YmdHis");
							$settlement_id							= $point_no_id;

							# LIMIT TIME (決済申込開始から30分で処理期限終了)
							$limit_time								= date("YmdHis",strtotime("+30 minutes",strtotime($ordered_time)));

							# PAYS INSERT
							$pays_insert							= array();
							$pays_insert							= array(
								'site_cd'							=> $members_data['site_cd'],
								'user_id'							=> $members_data['id'],
								'campaign_id'						=> $shops_data['campaign_id'],
								'domain_flg'						=> $members_data['domain_flg'],
								'sex'								=> $members_data['sex'],
				                'ad_code'							=> $members_data['ad_code'],
				                'reg_date'							=> $members_data['reg_date'],
								'order_date'						=> $ordered_time,
								'pay_date'							=> $datetime,
								'pay_amount'						=> $pay_amount,				// -> 必要PF通貨数(にじコイン数)
								'settlement_id'						=> $settlement_id,
								'object'							=> $shops_data['id'],		// -> shopsテーブル ID
								'sid'								=> $payment_id,
								'clear'								=> 1,
								'limit_time'						=> $limit_time,
				                'status'							=> 0
							);

							# 【insert】pays
							$insert_id								= $database->insertDb("pays",$pays_insert);

							# OK
							if(!empty($insert_id)){

								# MEMBERS UPDATE
								$members_update['pay_count']		= $members_data['pay_count'] + 1;
								$members_update['pay_amount']		= $members_data['pay_amount'] + $pay_amount;
								$members_update['last_pay_date']	= $datetime;

								if(empty($members_data['first_pay_date']) || $members_data['first_pay_date'] == 0){
									$members_update['first_pay_date']	= $datetime;
								}

								# UPDATE WHERE
								$members_update_where				= "id = :id";
								$members_update_conditions[':id']	= $members_data['id'];

								# 【UPDATE】 / members
								$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);


								# POINTS
								if(!empty($points_insert_flg)){

									# pay_flg 判定 無料ユーザー
									if($members_data['status'] == 3) {
										$pay_flg					= 2;
									# 定額ユーザー
									}elseif($members_data['status'] == 2) {
										$pay_flg					= 3;
									# 通常ユーザー
									}elseif($members_data['status'] != 0){

										# 無課金
										if($members_data['pay_count'] == 0){
											$pay_flg				= 2;
										# 課金
										}else{
											$pay_flg				= 1;
										}

									# その他
									}else{
										$pay_flg					= 0;
									}

									# POINTS INSERT
									$points_insert					= array();
									$points_insert					= array(
										'user_id'					=> $members_data['id'],
										'site_cd'					=> $members_data['site_cd'],
										'sex'						=> $members_data['sex'],
						                'ad_code'					=> $members_data['ad_code'],
						                'domain_flg'				=> $members_data['domain_flg'],
										'point'						=> $point,
										'point_no_id'				=> $settlement_id,
										'campaign_id'				=> $shops_data['campaign_id'],
						                'point_type'				=> 0,
										'log_date'					=> $datetime,
						                'pay_flg'					=> $pay_flg
									);

									# 【insert】points
									$insert_id						= $database->insertDb("points",$points_insert);

									if(empty($insert_id)){
										$error						= 10;
										$errormessage				= "POINTS INSERT ERROR";
									}

								}

							# PAYS ERROR
							}else{

								$error								= 11;
								$errormessage						= "PAYS INSERT ERROR";

							}


							# 処理
							if(empty($error)){

								# COMMIT
								$database->commit();

								# OK
								http_response_code(200);

							}

						}


					# チェック ERROR
					}else{

						$error										 = 8;
						$errormessage								 = "ショップデータが見つかりません";

					}

				}else{

					if(empty($error)){
						$error										 = 7;
						$errormessage								 = "ユーザーが退会もしくは削除済み";
					}

				}

			}

			# エラーの場合はロールバックしてエラー情報をインサート
			if(!empty($error)){

				if(!empty($_REQUEST['paymentId']) && empty($errorthrough)){

					# ROLLBACK
					$database->rollBack();

					# PAYS ERROR INSERT
					$error_insert['site_cd']						= SITE_CD;

					if(!empty($members_data['id'])){
						$error_insert['user_id']					= $members_data['id'];
						$error_insert['sex']						= $members_data['sex'];
						$error_insert['ad_code']					= $members_data['ad_code'];
						$error_insert['reg_date']					= $members_data['reg_date'];
					}

					if(!empty($_REQUEST['orderedTime'])){
						$error_insert['order_date']					= $_REQUEST['orderedTime'];
					}else{
						$error_insert['order_date']					= date("YmdHis");
					}

					if(!empty($pay_amount)){
						$error_insert['pay_amount']					= $pay_amount;
					}

					if(!empty($point_no_id)){
						$error_insert['settlement_id']				= $point_no_id;
					}

					if(!empty($data['id'])){
						$error_insert['object']						= $data['id'];
					}

					if(!empty($_REQUEST['paymentId'])){
						$error_insert['sid']						= $_REQUEST['paymentId'];
					}

					$error_insert['error']							= $error;

					# 【insert】pays
					$error_id										= $database->insertDb("pays",$error_insert);

				}

			}


		# ERROR
		}else{

			# ユーザーキャンセル
			if($_REQUEST['status'] == 3){

			# 期限切れ
			}elseif($_REQUEST['status'] == 4){

			# それ以外はエラー
			}else{

				$error												= $_REQUEST['status'];
				$errormessage										= "ステータス異常";

			}

		}


		# ERROR
		if(!empty($error)){

			$subject												= "Settlement Error";
			$message												= "決済エラーが発生致しました。\n";
			$message												.= "【ERROR NO】\n".$error."\n\n";
			$message												.= "【STATUS】\n".$errormessage."\n\n";
			$message												.= "【TIME】\n".date("Y-m-d H:i:s")."\n\n";
			ml($subject,$message,$_REQUEST);

			# ERROR STATUS
			http_response_code(500);


		}


	}

}

############################### DATABASE CLOSE ##################################

$database->closeDb();
$database->closeStmt();

################################## FILE END #####################################

exit();

################################## FILE END #####################################
?>