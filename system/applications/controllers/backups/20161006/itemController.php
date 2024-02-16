<?php
################################ FILE MANAGEMENT ################################
##
##	itemController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	ITEM PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	MAIN PAGE 各種処理
##
##	page : index 	-> アイテム
##	page :	use		-> アイテム使用処理
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

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemboxModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

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

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMBOX MODEL
$itemboxModel				= new ItemboxModel($database,$mainClass);

# ITEMUSE MODEL
$itemuseModel				= new ItemuseModel($database,$mainClass);

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
	**
	**	
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){








	/************************************************
	**
	**	MAIL
	**	============================================
	**	アイテムリスト表示(返信画面)
	**	============================================
	**	ajaxにて通信
	**	/item/mail.inc読み込み
	**
	**
	**	※未使用だけど念のため残しておくよ
	**
	**
	************************************************/

	}elseif($data['page'] == "mail"){


		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']										= 5;
		}


		# キャラクターID
		$character_id										= $data['id'];


		/************************************************
		**
		**	使用中アイテム
		**	===========================================
		**	itemuse_list
		**	===========================================
		**	itemsとJOINして使用中アイテム情報取得
		**
		************************************************/

		$itemuse_list										= NULL;
		$itemuse_list										= array();
		$itemuse_check										= array();

		$itemuse_conditions									= array(
			'user_id'										=> $members_data['id'],
			'character_id'									=> $character_id,
			'status'										=> 0,
			'order'											=> 'i.id',
			'page'											=> 'mail'
		);

		$itemuse_list										= $itemuseModel->getItemuseListJoinOnItems($itemuse_conditions);
		$mainClass->debug($itemuse_list);


		/************************************************
		**
		**	所持アイテム
		**	===========================================
		**	ItemBox
		**	===========================================
		**	itemsとJOINして所持アイテム情報取得
		**
		************************************************/

		$item_list											= NULL;
		$item_list											= array();
		$item_list_nouse									= NULL;
		$item_list_nouse									= array();
		$item_using											= 0;

		$itembox_conditions									= array(
			'user_id'										=> $members_data['id'],
			'status'										=> 0,
			'order'											=> 'i.name'
		);
		$itembox_rtn										= $itemboxModel->getItemboxListJoinOnItems($itembox_conditions);

		$i=0;
		$j=0;
		while($itembox_data = $database->fetchAssoc($itembox_rtn)){

			# 使用中チェック
			$use_check										= NULL;

			if(isset($itemuse_list[$itembox_data['item_id']]['id'])){
				$use_check									= 1;
				$item_using									= 1;
			}

			# 残り数ゼロで使用中データもなければ非表示
			if($itembox_data['unit'] == 0 && empty($use_check)){
				continue;
			}

			# 返信画面用しか使えません
			if($itembox_data['category'] == 0 || $itembox_data['category'] == 1){

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

				$item_list['use_check'][$i]					= $use_check;

				$i++;

			# 使えないアイテム
			}else{

				$item_list_nouse['id'][$j]					= $itembox_data['itembox_id'];
				$item_list_nouse['unit'][$j]				= $itembox_data['unit'];

				if(!empty($itembox_data['name'])){
					$item_list_nouse['name'][$j]			= $itembox_data['name'];
				}

				if(!empty($itembox_data['image'])){
					$item_list_nouse['image'][$j]			= $itembox_data['image'];
				}

				if(!empty($itembox_data['description'])){
					$item_list_nouse['description'][$j]		= $itembox_data['description'];
				}

				$item_list_nouse['use_check'][$j]			= $use_check;

				$j++;

			}

		}

		$database->freeResult($itembox_rtn);


		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();

		# VIEW FILE チェック & パス生成
		$view_directory										= $mainClass->getViewDirectory($directory,$data['page'],$device_file);

		# 読み込み
		include_once($view_directory);

		# 終了
		exit();



	/************************************************
	**
	**	USE
	**	============================================
	**	アイテム使用処理
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# USE
	}elseif($data['page'] == "use"){


		$error											= 0;
		$errormessage									= NULL;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']									= 5;
			$_POST['itembox_id']						= 1;
			$_POST['sender']							= "mail";
		}


		# キャラクターID
		$character_id									= $data['id'];

		# items ID
		$itembox_id										= $_POST['itembox_id'];

		# sender : 送信元ページ
		$sender											= NULL;
		if(isset($_POST['sender'])){
			$sender										= $_POST['sender'];
		}


		/************************************************
		**
		**	MASTER DATABASE切り替え
		**
		************************************************/

		# AUTHORITY / 既にマスターに接続してるかチェック
		$db_auth										 = $database->checkAuthority();

		# DATABASE CHANGE / スレーブだったら
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

		}

		# ITEM ID OK
		if(!empty($itembox_id) && is_numeric($itembox_id)){

			# ユーザー所持アイテム取得
			$itembox_data								= $itemboxModel->getItemboxDataById($itembox_id);

			# 所持アイテム取得 or 所持数OK
			if(!empty($itembox_data['id']) && $itembox_data['unit'] > 0){


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
				$campaign_check								= $memberModel->checkCampaignUpdate($members_data,$campaign_data);

				# campaign_type が3か5だったら(アイテム効果変動キャンペーン)
				if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
					$campaign_id							= $campaign_data['id'];
				# それ以外はチェックも外す
				}else{
					$campaign_check							= 0;
				}


				# 初期化
				$items_data									= NULL;
				$items_data									= array();

				# アイテム情報取得
				$items_data									= $itemModel->getItemDataById($itembox_data['item_id']);

				# キャンペーン中に効果変動設定してあるアイテムがあるか(アイテム効果変動はcampaign_type = 3)
				if(!empty($campaign_id) && $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){

					$items_campaign_conditions				= array();
					$items_campaign_conditions				= array(
						'item_id'							=> $itembox_data['item_id'],
						'campaign_id'						=> $campaign_id,
						'status'							=> 0,
						'order'								=> 'id'
					);

					$items_campaign_data					= $itemModel->getItemData($items_campaign_conditions);

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


				# 基本情報取得OK
				if(!empty($items_data['id'])){


					/************************************************
					**
					**	同一タイプの効果のものは使えません
					**
					************************************************/

					# 返信画面
					if($sender == "mail"){

						$itemuse_check_conditions		= array();
						$itemuse_check_conditions		= array(
							'user_id'					=> $members_data['id'],
							'character_id'				=> $character_id,
							'effect'					=> $items_data['effect'],
							'status'					=> 0
						);

					# 通常画面
					}elseif($sender == "itembox"){

						$itemuse_check_conditions		= array();
						$itemuse_check_conditions		= array(
							'user_id'					=> $members_data['id'],
							'effect'					=> $items_data['effect'],
							'status'					=> 0
						);

					# その他
					}else{

						if(!empty($character_id)){

							$itemuse_check_conditions	= array();
							$itemuse_check_conditions	= array(
								'user_id'				=> $members_data['id'],
								'character_id'			=> $character_id,
								'effect'				=> $items_data['effect'],
								'status'				=> 0
							);

						}else{

							$itemuse_check_conditions	= array();
							$itemuse_check_conditions	= array(
								'user_id'				=> $members_data['id'],
								'effect'				=> $items_data['effect'],
								'status'				=> 0
							);

						}

					}

					$itemuse_check						= $itemuseModel->checkMatchItemuseTypeListJoinOnItems($itemuse_check_conditions);

					# itemuseで使用中のアイテムをチェック
					if(!empty($itemuse_check['result'])){
						$error							= 1;
						$errormessage					= "同一効果のアイテムは同時に使用できません。";
					}


					# 同一効果チェックOK
					if(empty($error)){


						/************************************************
						**
						**	使用場所チェック & バリデート
						**
						************************************************/

						# 比較変数初期化
						$comparison_sender				= NULL;

						# 返信画面
						if($sender == "mail"){
							if($items_data['category'] != 0 && $items_data['category'] != 1){
								$error					= 1;
								$errormessage			= "ここでは使用できません";
							}
						# アイテムBOX
						}elseif($sender == "itembox"){
							if($items_data['category'] != 0 && $items_data['category'] != 2){
								$error					= 1;
								$errormessage			= "ここでは使用できません";
							}
						}


						# 使用場所チェックOK
						if(empty($error)){


							/************************************************
							**
							**	ここで効果を確認し、itemuseにinsert
							**	============================================
							**	処理開始↓↓↓↓
							**
							************************************************/

							# 初期化
							$limit_time					= 0;
							$limit_count				= 0;


							/************************************************
							**
							**	type : 0
							**	===========================================
							**	効果なし(スルー)
							**
							************************************************/

							if($items_data['type'] == 0){






							/************************************************
							**
							**	type : 1
							**	===========================================
							**	時間適用 / $items_data['count'] が 時間カウントになる(minutes) -> $limit_count
							**
							************************************************/

							}elseif($items_data['type'] == 1){

								$limit_time				= date("YmdHis",strtotime("+".$items_data['count']." minutes"));

								# 好感度アップ
								if($items_data['effect'] == 1){


								# 応援ポイントアップ
								}elseif($items_data['effect'] == 2){




								# その他はスルー
								}else{



								}


							/************************************************
							**
							**	type : 2
							**	===========================================
							**	回数適用 / $items_data['count'] が 回数カウントになる -> $limit_time
							**
							************************************************/

							}elseif($items_data['type'] == 2){

								$limit_count			= $items_data['count'];



							/************************************************
							**
							**	type : 3
							**	===========================================
							**	有効期限 / $items_data['end_date']を適用 -> $limit_time
							**
							************************************************/

							}elseif($items_data['type'] == 3){

								# 有効期限チェック OK
								if($items_data['end_date'] >= date("YmdHis")){

									$limit_time			= $items_data['end_date'];

								# 有効期限チェック ERROR
								}else{

									$error				= 1;
									$errormessage		= "このアイテムは既に有効期限が過ぎています。";

								}


							# その他はスルー
							}else{


							}


							/************************************************
							**
							**	itemuse INSERT
							**
							************************************************/

							if(empty($error)){

								# itemuse にinsert
								$itemuse_insert			= array();
								$itemuse_insert			= array(
									'site_cd'			=> $members_data['site_cd'],
									'itembox_id'		=> $itembox_id,
									'item_id'			=> $items_data['id'],
									'user_id'			=> $members_data['id'],
									'character_id'		=> $character_id,
									'limit_time'		=> $limit_time,
									'limit_count'		=> $limit_count,
								);


								# 【insert】itemuse
								$insert_id				= $database->insertDb("itemuse",$itemuse_insert);

								# INSERT OK
								if(!empty($insert_id)){

									# アイテムのカウントを減らす
									$itembox_update['unit']				= $itembox_data['unit'] - 1;

									# UPDATE WHERE
									$itembox_update_where				= "id = :id";
									$itembox_update_conditions[':id']	= $itembox_id;

									# 【UPDATE】 / itembox
									$database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

									$nowtime							= date("YmdHis");

									# RESULT
									$result['item_id']					= $itembox_id;
									$result['itemuse_id']				= $insert_id;
									$result['name']						= $items_data['name'];
									$result['message']					= $items_data['message'];
									$result['word']						= $items_data['word'];
									$result['type']						= $items_data['type'];
									$result['limit_time']				= $itemuseModel->getLimitTime($nowtime,$limit_time);
									$result['limit_count']				= $limit_count;


								}else{

									$error					= 1;
									$errormessage			= "正常に処理できませんでした。";

								}

							}

						}

					}


				# 基本情報なし
				}else{

					$error								= 1;
					$errormessage						= "正常に処理できませんでした。";

				}


			# アイテムデータ無し or 所持数足りない
			}else{

				if(empty($itembox_data['id'])){

					$error								= 1;
					$errormessage						= "正常に処理できませんでした。";

				}elseif($itembox_data['unit'] == 0){

					$error								= 1;
					$errormessage						= "アイテムの所持数が足りません。";

				}

			}


		# ERROR
		}else{

			$error										= 1;
			$errormessage								= "正常に処理できませんでした。";

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

		$result['error']							= $error;
		$result['errormessage']						= $errormessage;

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



	/************************************************
	**
	**	USING
	**	============================================
	**	使用中アイテム情報
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# USE
	}elseif($data['page'] == "using"){

		$error											= 0;
		$errormessage									= NULL;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']									= 5;
			$_POST['itembox_id']						= 1;
		}

		# キャラクターID
		$character_id									= $data['id'];

		# itembox ID
		$itembox_id										= $_POST['itembox_id'];

		# RESULT
		$result											= NULL;

		# NOW TIME
		$nowtime										= date("YmdHis");


		/************************************************
		**
		**	使用中のアイテムデータ取得
		**
		************************************************/

		$itembox_data									= $itemboxModel->getItemboxDataById($itembox_id);

		if(!empty($itembox_data['id'])){

			$itemuse_conditions							= array();
			$itemuse_conditions							= array(
				'user_id'								=> $members_data['id'],
				'character_id'							=> $character_id,
				'item_id'								=> $itembox_data['item_id'],
				'status'								=> 0
			);

			$itemuse_data								= $itemuseModel->getItemuseDataJoinOnItems($itemuse_conditions);

			# OK
			if(!empty($itemuse_data['itemuse_id'])){

				$result['id']							= $itemuse_data['itemuse_id'];
				$result['item_id']						= $itemuse_data['items_id'];
				$result['name']							= $itemuse_data['name'];
				$result['word']							= $itemuse_data['word'];
				$result['type']							= $itemuse_data['type'];
				$result['limit_time']					= $itemuseModel->getLimitTime($nowtime,$itemuse_data['limit_time']) + 1;
				$result['limit_count']					= $itemuse_data['limit_count'];

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

		$result['error']							= $error;
		$result['errormessage']						= $errormessage;

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