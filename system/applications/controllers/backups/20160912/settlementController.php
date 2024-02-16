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

/** ITEMBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemboxModel.php");


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

# ITEMBOX MODEL
$itemboxModel				= new ItemboxModel($database,$mainClass);


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

		$error										= NULL;
		$errormessage								= NULL;
		$errorthrough								= NULL;
		$subject									= NULL;
		$message									= NULL;

		# 決済処理 ステータス OK
		if(!empty($_REQUEST['status']) && $_REQUEST['status'] == 2){

			# APP IDがあるか
			if(empty($_REQUEST['opensocial_app_id'])){

				$error								 = 2;
				$errormessage						 = "APP ID NULL";

			# SITE IDが一致するか
			}elseif(!empty($_REQUEST['opensocial_app_id']) && $_REQUEST['opensocial_app_id'] != APP_ID){

				$error								 = 3;
				$errormessage						 = "APP ID 不一致";

			# USER IDがない
			}elseif(empty($_REQUEST['opensocial_viewer_id'])){

				$error								 = 4;
				$errormessage						 = "USER ID NULL";

			# PAYMENT IDがない
			}elseif(empty($_REQUEST['paymentId'])){

				$error								 = 5;
				$errormessage						 = "PAYMENT ID NULL";

			}

			# 処理開始
			if(empty($error)){

				# USER ID
				$user_id							 = $_REQUEST['opensocial_viewer_id'];

				# まずmembersのチェック
				$members_data						 = $memberModel->getMemberByUserId($user_id);

				if(empty($members_data['id'])){
					$error							 = 6;
					$errormessage					 = "ユーザー情報取得できませんでした。";
				}

				# members status
				if(empty($error) && $members_data['status'] < 8){

					$settlement_id					 = $point_no_array[$point_name_array['gacha_coin']][2];

					# 決済情報チェック
					$pays_conditions				 = array();
					$pays_conditions				 = array(
						'user_id'					 => $members_data['id'],
						"order_date"				 => $_REQUEST['orderedTime'],
						"settlement_id"				 => $settlement_id,
						'sid'						 => $_REQUEST['paymentId'],
						'clear'						 => 0,
						'finish'					 => 0,
						'error'						 => 0,
						'status'					 => 0
					);

					$pays_data						 = $payModel->getPayData($pays_conditions,"id,campaign_id,pay_amount,settlement_id,object");

					# チェック OK
					if(!empty($pays_data['id'])){

						# TRANSACTION START
						$database->beginTransaction();

						$datetime								= date("YmdHis");

						# PAYS UPDATE
						$pays_update							= array();
						$pays_update							= array(
							'pay_date'							=> $datetime,
							'clear'								=> 1
						);

						# UPDATE WHERE
						$pays_update_where						= "id = :id";
						$pays_update_conditions[':id']			= $pays_data['id'];

						# 【UPDATE】 / pays
						$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);

						# MEMBERS UPDATE
						$new_point								= $members_data['point'] + $pays_data['object'];

						$members_update['point']				= $new_point;
						$members_update['pay_count']			= $members_data['pay_count'] + 1;
						$members_update['pay_amount']			= $members_data['pay_amount'] + $pays_data['pay_amount'];
						$members_update['last_pay_date']		= $datetime;

						if(empty($members_data['first_pay_date']) || $members_data['first_pay_date'] == 0){
							$members_update['first_pay_date']	= $datetime;
						}

						# UPDATE WHERE
						$members_update_where					= "id = :id";
						$members_update_conditions[':id']		= $members_data['id'];

						# 【UPDATE】 / members
						$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);


						# PF通過でガチャ引いた場合は消費としてpointsに入れない
						/*
						# pay_flg 判定 無料ユーザー
						if($members_data['status'] == 3) {
							$pay_flg							= 2;
						# 定額ユーザー
						}elseif($members_data['status'] == 2) {
							$pay_flg							= 3;
						# 通常ユーザー
						}elseif($members_data['pay_count'] != 0){

							# 無課金
							if($members_data['pay_count'] == 0){
								$pay_flg						= 2;
							# 課金
							}else{
								$pay_flg						= 1;
							}

						# その他
						}else{
							$pay_flg							= 0;
						}

						# POINTS INSERT
						$points_insert							= array();
						$points_insert							= array(
							'user_id'							=> $members_data['id'],
							'site_cd'							=> $members_data['site_cd'],
							'sex'								=> $members_data['sex'],
			                'ad_code'							=> $members_data['ad_code'],
			                'domain_flg'						=> $members_data['domain_flg'],
							'point'								=> $pays_data['pay_amount'],
							'point_no_id'						=> $settlement_id,
							'campaign_id'						=> $pays_data['campaign_id'],
			                'point_type'						=> 0,
							'log_date'							=> $datetime,
			                'pay_flg'							=> $pay_flg
						);

						# 【insert】points
						$insert_id								= $database->insertDb("points",$points_insert);
						*/

						$result									= 1;

						# 処理
						if(!empty($result)){

							# COMMIT
							$database->commit();

							# OK
							print("200");

						}else{

							$error								= 9;
							$errormessage						= "DB ERROR";

						}


					# チェック ERROR
					}else{

						$error									 = 8;
						$errormessage							 = "決済データが見つかりません";
						$errorthrough							 = 1;

					}


				}else{

					if(empty($error)){
						$error									 = 7;
						$errormessage							 = "ユーザーが退会もしくは削除済み";
					}

				}


			}

			# 決済情報をエラーに
			if(!empty($error)){

				if(!empty($_REQUEST['paymentId']) && empty($errorthrough)){

					# ROLLBACK
					$database->rollBack();

					$error_update['error']							= $error;
					$error_where									= "sid = :sid";
					$error_conditions[':sid']						= $_REQUEST['paymentId'];

					# 【UPDATE】 / pays
					$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);


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

				$error											 = $_REQUEST['status'];
				$errormessage									 = "ステータス異常";

			}


		}


		# ERROR
		if(!empty($error)){

			$subject								 = "Settlement Error";
			$message								 = "決済エラーが発生致しました。\n";
			$message								.= "【ERROR NO】\n".$error."\n\n";
			$message								.= "【STATUS】\n".$errormessage."\n\n";
			$message								.= "【TIME】\n".date("Y-m-d H:i:s")."\n\n";
			ml($subject,$message,$_REQUEST);
			pr($message);

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
				$members_data										 = $memberModel->getMemberByUserId($user_id);

				if(empty($members_data['id'])){
					$error											 = 6;
					$errormessage									 = "ユーザー情報取得できませんでした。";
				}

				# members status
				if(empty($error) && $members_data['status'] < 8){

					# SETTLEMENT ID
					$point_no_name									 = $data['page']."_".$data['type'];
					$settlement_id									 = $point_no_array[$point_name_array[$point_no_name]][2];

					# 決済情報チェック
					$pays_conditions								 = array();
					$pays_conditions								 = array(
						'user_id'									 => $members_data['id'],
						"order_date"								 => $_REQUEST['orderedTime'],
						"settlement_id"								 => $settlement_id,
						'sid'										 => $_REQUEST['paymentId'],
						'clear'										 => 0,
						'finish'									 => 0,
						'error'										 => 0,
						'status'									 => 0
					);

					$pays_data										 = $payModel->getPayData($pays_conditions,"id,campaign_id,pay_amount,settlement_id,object");

					# チェック OK
					if(!empty($pays_data['id']) && !empty($pays_data['object'])){

						# TRANSACTION START
						$database->beginTransaction();

						# SHOPS DATA
						$shops_data									= $shopModel->getShopDataById($pays_data['object']);

						# 処理開始
						if(!empty($shops_data['id'])){

							# ポイント購入
							if($data['type'] == "point"){

								# pointsets
								$pointsets_data						= $pointsetModel->getPointset($settlement_id,$members_data,NULL);

								$point								= DEFAULT_SHOP_POINT;

								if(!empty($pointsets_data)){

									$count							= count($pointsets_data);
									for($i=0;$i<$count;$i++){

										# ショップ購入で付与されるポイント単価
										if($pointsets_data[$i]['point_no_id'] == $settlement_id){
											$point					= $pointsets_data[$i]['point'];
										}

									}

								}

								# 購入枚数を乗算
								$point								= $point * $shops_data['unit'];

								# MEMBERS UPDATE
								$new_point							= $members_data['point'] + $point;

								$members_update['point']			= $new_point;

								# ここは通過
								$acceptance							= 1;

								# pointsをインサート
								$points_insert_flg					= 1;


							# アイテム購入
							}elseif($data['type'] == "item"){

								$shop_conditions					= array();
								$shop_conditions					= array(
									'shop_id'						=> $shops_data['id'],
									'category'						=> $shops_data['category'],
									'campaign_id'					=> $shops_data['campaign_id'],
									'status'						=> 0
								);

								$shop_rtn							= $shopModel->getShopList($shop_conditions);

								$i=0;
								while($shop_data = $database->fetchAssoc($shop_rtn)){

									$acceptance						= NULL;

									if(empty($shop_data['item_id'])){
										$error						= 99;
										$errormessage				= "アイテム情報に不備がありました。";
										break;
									}

									# 所持確認
									$itembox_conditions				= array();
									$itembox_conditions				= array(
										'user_id'					=> $members_data['id'],
										'item_id'					=> $shop_data['item_id'],
										'status'					=> 0
									);
									$itembox_data					= $itemboxModel->getItemboxData($itembox_conditions,"id,unit");

									# 持ってれば加算
									if(!empty($itembox_data['id'])){

										$update_unit				= $itembox_data['unit'] + $shop_data['unit'];

										$itembox_update['unit']		= $update_unit;
										$itembox_update_where		= "id = :id";
										$itembox_update_conditions[':id']	= $itembox_data['id'];

										# 【UPDATE】 / itembox
										$return						= $database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

										if(!empty($return)){
											$acceptance				= 1;
										}

										# ERROR
										if(empty($acceptance)){
											$error					= 99;
											$errormessage			= "アイテム情報購入処理に不備がありました";
											break;
										}

									# なければ追加
									}else{

										$itembox_insert				= array();
										$itembox_insert				= array(
											'user_id'				=> $members_data['id'],
											'item_id'				=> $shop_data['item_id'],
											'unit'					=> $shop_data['unit'],
											'status'				=> 0
										);

										# 【INSERT】 / itembox
										$acceptance					= $database->insertDb("itembox",$itembox_insert);

										# ERROR
										if(empty($acceptance)){
											$error					= 99;
											$errormessage			= "アイテム情報購入処理に不備がありました";
											break;
										}

									}

									$i++;

								}

								$database->freeResult($shop_rtn);

								if($i == 0){
									$error							= 100;
									$errormessage					= "アイテム情報購入処理に不備がありました";
								}

								$point								= $pays_data['pay_amount'];

							}

							# 各種処理OK
							if(empty($error)){

								$datetime							= date("YmdHis");

								# PAYS UPDATE
								$pays_update						= array();
								$pays_update						= array(
									'pay_date'						=> $datetime,
									'clear'							=> 1
								);

								# UPDATE WHERE
								$pays_update_where					= "id = :id";
								$pays_update_conditions[':id']		= $pays_data['id'];

								# 【UPDATE】 / pays
								$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);


								# MEMBERS UPDATE
								$members_update['pay_count']		= $members_data['pay_count'] + 1;
								$members_update['pay_amount']		= $members_data['pay_amount'] + $pays_data['pay_amount'];
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
									}elseif($members_data['pay_count'] != 0){

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
										'campaign_id'				=> $pays_data['campaign_id'],
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

								# 処理
								if(empty($error)){

									# COMMIT
									$database->commit();

									# OK
									print("200");

								}

							}

						# チェック ERROR
						}else{

							$error									 = 9;
							$errormessage							 = "ショップデータが見つかりません";

						}

					# チェック ERROR
					}else{

						$error										 = 8;
						$errormessage								 = "決済データが見つかりません";
						$errorthrough								 = 1;

					}

				}else{

					if(empty($error)){
						$error										 = 7;
						$errormessage								 = "ユーザーが退会もしくは削除済み";
					}

				}


			}


			# 決済情報をエラーに
			if(!empty($error)){

				if(!empty($_REQUEST['paymentId']) && empty($errorthrough)){

					# ROLLBACK
					$database->rollBack();

					$error_update['error']							= $error;
					$error_where									= "sid = :sid";
					$error_conditions[':sid']						= $_REQUEST['paymentId'];

					# 【UPDATE】 / pays
					$database->updateDb("pays",$error_update,$error_where,$error_conditions);


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

				$error											 = $_REQUEST['status'];
				$errormessage									 = "ステータス異常";

			}


		}


		# ERROR
		if(!empty($error)){

			$subject								 = "Settlement Error";
			$message								 = "決済エラーが発生致しました。\n";
			$message								.= "【ERROR NO】\n".$error."\n\n";
			$message								.= "【STATUS】\n".$errormessage."\n\n";
			$message								.= "【TIME】\n".date("Y-m-d H:i:s")."\n\n";
			ml($subject,$message,$_REQUEST);
			pr($message);

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