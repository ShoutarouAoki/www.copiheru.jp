<?php
################################ FILE MANAGEMENT ################################
##
##	characterController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	CHARACTER PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	CHARACTER PAGE 各種処理
##
##	page : index	-> 表示可能親キャラキャラ一覧
##	page : profile	-> キャラ詳細
##
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

/** MAILUSER MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MailuserModel.php");

/** ATTACH MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AttachModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemboxModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

/** EMOJI PLUGINS **/
require_once(DOCUMENT_SYSTEM_PLUGINS."/Emoji/lib/mobile_class_8.php");


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

# RETIREMENT
$retirement					= NULL;

if($data['page'] == "index" && !empty($data['id']) && $data['id'] == "retirement"){
	$retirement				= 1;
}


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# MAILUSER MODEL
$mailuserModel				= new MailuserModel($database,$mainClass);

# ATTACH MODEL
$attachModel				= new AttachModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMBOX MODEL
$itemboxModel				= new ItemboxModel($database,$mainClass);

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
	**	INDEX
	**	============================================
	**
	**	表示可能親キャラ一覧
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){

		/************************************************
		**
		**	やりとり中キャラ一覧取得
		**	============================================
		**
		**	getMailList
		**
		**	============================================
		**	【概要】
		**	まずmailsから最新メールをキャラ毎に取得
		**	↓
		**	それが子キャラか親キャラかチェック
		**
		************************************************/

		# MAILS ARRY
		$mails_conditions								= array();
		$mails_conditions								= array(
			'user_id'									=> $members_data['id'],
			'status'									=> 0,
			'last_flg'									=> 0,
			'group'										=> 'send_id',
			'order'										=> 'send_date DESC',
			'type'										=> 1
		);

		# MAILS COLUMN
		$mails_column									= "id,send_id,recv_id,recv_flg,title,naruto,send_date";

		$mails_rtn										= $mailModel->getMailList($mails_conditions,$mails_column);


		# 除外親ID
		$exclusion_id									= NULL;
		$exclusion_id									= array();

		$i=0;
		while($mails_data = $database->fetchAssoc($mails_rtn)){


			/************************************************
			**
			**	親かどうかの判別はmailsのnarutoで
			**
			************************************************/

			$parent_id									= 0;

			# 親だったら
			if($mails_data['naruto'] == 0){

				$parent_id								= $mails_data['send_id'];
				$check_parent							= 1;

			# 子だったら
			}else{

				$parent_id								= $mails_data['naruto'];
				$check_parent							= NULL;

			}


			/************************************************
			**
			**	キャラのデータをmembersから取得
			**
			************************************************/

			# CHARACTER DATA
			$character_data								= $memberModel->getMemberDataById($parent_id,NULL,"id,nickname,status,age,pref,city,chikuwa,open_flg,media_flg,naruto");

			# 公開状態か引退状態か
			if(empty($retirement) && $character_data['open_flg'] > 1){
				continue;
			}elseif(!empty($retirement) && $character_data['open_flg'] < 3){
				continue;
			}

			# MAIL USER DATA
			$mailusers_conditions						= array();
			$mailusers_conditions						= array(
				'user_id'								=> $members_data['id'],
				'character_id'							=> $mails_data['send_id'],
				'status'								=> 0
			);

			$mailusers_data								= $mailuserModel->getMailuserData($mailusers_conditions,"virtual_age,virtual_name,degree_name");


			# サムネ画像取得
			$attaches_conditions						= array();
			$attaches_conditions						= array(
				'user_id'								=> $parent_id,
				'category'								=> $list_image_category,
				'use_flg'								=> 1,
				'pay_count'								=> 0,
				'device'								=> $device_number,
				'status'								=> 1,
				'order'									=> 'pay_count,reg_date DESC',
				'limit'									=> 1,
				'group'									=> NULL
			);
			$attaches_data								= $attachModel->getAttachData($attaches_conditions);

			# 鍵チェック
			$secret_key									= NULL;
			$key_name									= NULL;
			$key_image									= NULL;
			$key_result									= NULL;

			# 鍵付きキャラだったら
			if($character_data['media_flg'] == 1){

				$secret_key								= 1;

				# まず鍵を確認
				$items_conditions						= array(
					'character_id'						=> $character_data['id']
				);
				$items_data								= $itemModel->getItemData($items_conditions);

				# 鍵アイテムあり
				if(!empty($items_data['id'])){

					# 鍵アイテム名
					$key_name							= $items_data['name'];

					# 鍵アイテム画像
					$key_image							= HTTP_ITEM_IMAGE."/".$items_data['image'];

					# ユーザーがそのアイテム持ってるかチェック
					$itembox_conditions					= array();
					$itembox_conditions					= array(
						'user_id'						=> $members_data['id'],
						'item_id'						=> $items_data['id'],
						'status'						=> 0
					);

					$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

					# 持ってる
					if(!empty($itembox_data['id'])){

						$secret_key						= 2;

						# 鍵使って開放してるかチェック
						$itemuse_conditions				= array();
						$itemuse_conditions				= array(
							'item_id'					=> $items_data['id'],
							'user_id'					=> $members_data['id'],
							'character_id'				=> $character_data['id'],
							'status'					=> 0
						);

						$itemuse_rows					= $itemuseModel->getItemuseCount($itemuse_conditions);

						# 開放済み
						if($itemuse_rows > 0){
							$secret_key					= NULL;
							$key_result					= 1;
						}

					}

				}

			}


			# 未読メール件数確認
			$no_read									= $mailModel->getNoReadCount($members_data['id'],$mails_data['send_id']);

			# 絵文字セット ネーム
			if(!empty($mailusers_data['virtual_name'])){
				$display_name							= $emoji_obj->emj_decode($mailusers_data['virtual_name']);
			}else{
				$display_name							= $emoji_obj->emj_decode($character_data['nickname']);
			}

			# 結果セット
			$character_list['id'][$i]					= $mails_data['send_id'];
			$character_list['parent'][$i]				= $check_parent;
			$character_list['send_id'][$i]				= $mails_data['send_id'];
			$character_list['name'][$i]					= $display_name['web'];

			# 最終受信時刻
			$character_list['send_date'][$i]			= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));

			# サムネイル画像
			if(!empty($attaches_data)){
				$character_list['image'][$i]			= $attaches_data['attached'];
			}

			# 年齢セット
			if($mailusers_data['virtual_age'] > 0){
				$character_list['age'][$i]				= $mailusers_data['virtual_age'];
			}else{
				$character_list['age'][$i]				= $character_data['age'];
			}

			# 親チェック
			$character_list['parent_id'][$i]			= $parent_id;

			# やり取りあれば
			if(!empty($mailusers_data['id'])){

				$character_list['mail'][$i]				= 2;

			# キャラからの送信のみ
			}else{

				$character_list['mail'][$i]				= 1;

			}

			# 未読件数
			$character_list['no_read'][$i]				= $no_read;

			# 鍵
			$character_list['secret_key'][$i]			= $secret_key;
			$character_list['key_name'][$i]				= $key_name;
			$character_list['key_image'][$i]			= $key_image;

			# 除外親キャラのIDを配列で生成
			$exclusion_id[$i]							= $parent_id;

			$i++;

		}

		$database->fetchAssoc($mails_rtn,1);



		/************************************************
		**
		**	やりとり無しキャラ一覧取得
		**	============================================
		**
		**	getMemberList
		**
		**	============================================
		**	【概要】
		**	membersから親キャラ取得
		**	
		**
		************************************************/

		if(empty($retirement)){
			$open_flg									= 1;
		}else{
			$open_flg									= 3;
		}

		# MEMBERS ARRAY
		$characters_conditions							= array();
		$characters_conditions							= array(
			'open_flg'									=> $open_flg,
			'naruto'									=> 0,
			'order'										=> 'id DESC'
		);

		$characters_rtn									= $memberModel->getMemberList($characters_conditions,$exclusion_id,"id,nickname,status,age,pref,city,chikuwa,open_flg,media_flg,naruto");

		# $iは引継ぎ
		while($characters_data = $database->fetchAssoc($characters_rtn)){

			# リスト画像取得
			$attaches_conditions						= array();
			$attaches_conditions						= array(
				'user_id'								=> $characters_data['id'],
				'category'								=> $list_image_category,
				'use_flg'								=> 1,
				'pay_count'								=> 0,
				'device'								=> $device_number,
				'status'								=> 1,
				'limit'									=> 1,
				'group'									=> NULL
			);
			$attaches_data								= $attachModel->getAttachData($attaches_conditions);

			# 鍵チェック
			$secret_key									= NULL;
			$key_name									= NULL;
			$key_image									= NULL;
			$key_result									= NULL;

			# 鍵付きキャラだったら
			if($characters_data['media_flg'] == 1){

				$secret_key								= 1;

				# まず鍵を確認
				$items_conditions						= array(
					'character_id'						=> $characters_data['id']
				);
				$items_data								= $itemModel->getItemData($items_conditions);

				# 鍵アイテムあり
				if(!empty($items_data['id'])){

					# 鍵アイテム名
					$key_name							= $items_data['name'];

					# 鍵アイテム画像
					$key_image							= HTTP_ITEM_IMAGE."/".$items_data['image'];

					# ユーザーがそのアイテム持ってるかチェック
					$itembox_conditions					= array();
					$itembox_conditions					= array(
						'user_id'						=> $members_data['id'],
						'item_id'						=> $items_data['id'],
						'status'						=> 0
					);

					$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

					# 持ってる
					if(!empty($itembox_data['id'])){

						$secret_key						= 2;

						# 鍵使って開放してるかチェック
						$itemuse_conditions				= array();
						$itemuse_conditions				= array(
							'item_id'					=> $items_data['id'],
							'user_id'					=> $members_data['id'],
							'character_id'				=> $characters_data['id'],
							'status'					=> 0
						);

						$itemuse_rows					= $itemuseModel->getItemuseCount($itemuse_conditions);

						# 開放済み
						if($itemuse_rows > 0){
							$secret_key					= NULL;
							$key_result					= 1;
						}

					}

				}

			}


			# 絵文字セット ネーム
			$display_name								= $emoji_obj->emj_decode($characters_data['nickname']);

			# 結果セット
			$character_list['id'][$i]					= $characters_data['id'];
			$character_list['parent_id'][$i]			= $characters_data['id'];
			$character_list['name'][$i]					= $display_name['web'];


			# リスト画像
			if(!empty($attaches_data)){
				$character_list['image'][$i]			= $attaches_data['attached'];
			}

			# 年齢セット
			$character_list['age'][$i]					= $characters_data['age'];

			# 親チェック
			$character_list['parent'][$i]				= 1;

			# 鍵キャラ開放してたら
			if(!empty($key_result)){

				$character_list['mail'][$i]				= 1;

			# mailsデータなし
			}else{

				$character_list['mail'][$i]				= 0;

			}

			# 鍵
			$character_list['secret_key'][$i]			= $secret_key;
			$character_list['key_name'][$i]				= $key_name;
			$character_list['key_image'][$i]			= $key_image;

			$i++;

		}

		$database->fetchAssoc($characters_rtn,1);



	/************************************************
	**
	**	PROFILE
	**	============================================
	**	キャラプロフィール
	**
	************************************************/

	# PROFILE
	}elseif($data['page'] == "profile"){

		$error											= NULL;
		$errormessage									= NULL;
		$standby										= NULL;
		$image											= NULL;

		if(empty($data['id']) || !is_numeric($data['id'])){

			$error										= 1;
			$errormessage								= "不正なアクセスです";

		}

		# 処理
		if(empty($error)){

			# CHARACTER DATA
			$character_data								= $memberModel->getMemberDataById($data['id'],NULL,"id,nickname,status,age,pref,city,chikuwa,open_flg,media_flg,naruto");

			if(!empty($character_data['id'])){

				if($character_data['naruto'] == 0){
					$parent_id							= $character_data['id'];
				}else{
					$parent_id							= $character_data['narutos'];
				}

				# プロフィール画像取得
				$attaches_conditions					= array();
				$attaches_conditions					= array(
					'user_id'							=> $parent_id,
					'category'							=> $profile_image_category,
					'use_flg'							=> 1,
					'pay_count'							=> 0,
					'device'							=> $device_number,
					'status'							=> 1,
					'order'								=> 'pay_count,reg_date DESC',
					'limit'								=> 1,
					'group'								=> NULL
				);
				$attaches_data							= $attachModel->getAttachData($attaches_conditions);

				if(!empty($attaches_data['id'])){

					$image								= $attaches_data['attached'];

				}else{

					$standby							= 1;
					$errormessage						= "プロフィール準備中です<br />今しばらくお待ち下さい";

				}

			}else{

				$error									= 2;
				$errormessage							= "キャラクターのデータが見つかりません";

			}


		}




	/************************************************
	**
	**	OPEN
	**	============================================
	**	鍵付きキャラ開放処理
	**
	************************************************/

	# OPEN
	}elseif($data['page'] == "open"){

		$error											= 0;
		$errormessage									= NULL;
		$character_id									= NULL;
		$message										= NULL;
		$key_check										= NULL;
		$key_name										= NULL;
		$key_image										= NULL;

		if(empty($data['id'])){
			$error										= 1;
			$errormessage								= "不正なアクセスです";
		}

		# OK
		if(empty($error)){

			# キャラ情報確認
			$character_data								= $memberModel->getMemberDataById($data['id'],NULL,"id,nickname,status,age,pref,city,chikuwa,open_flg,media_flg,naruto");

			# 親キャラで鍵付きキャラかチェック
			if(!empty($character_data['id']) && $character_data['naruto'] == 0 && $character_data['media_flg'] == 1){

				$character_id							= $character_data['id'];

				# まず鍵を確認
				$items_conditions						= array(
					'character_id'						=> $character_id
				);
				$items_data								= $itemModel->getItemData($items_conditions);

				# 鍵アイテムあり
				if(!empty($items_data['id'])){

					# 鍵アイテム名
					$key_name							= $items_data['name'];

					# 鍵アイテム画像
					$key_image							= HTTP_ITEM_IMAGE."/".$items_data['image'];

					# ユーザーがそのアイテム持ってるかチェック
					$itembox_conditions					= array();
					$itembox_conditions					= array(
						'user_id'						=> $members_data['id'],
						'item_id'						=> $items_data['id'],
						'status'						=> 0
					);

					$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

					# 持ってる
					if(!empty($itembox_data['id'])){

						# 鍵使って開放してるかチェック
						$itemuse_conditions				= array();
						$itemuse_conditions				= array(
							'item_id'					=> $items_data['id'],
							'user_id'					=> $members_data['id'],
							'character_id'				=> $character_id,
							'status'					=> 0
						);

						$itemuse_rows					= $itemuseModel->getItemuseCount($itemuse_conditions);

						# まだ未使用
						if($itemuse_rows == 0){


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

							}

							# itemuse にinsert
							$itemuse_insert				= array();
							$itemuse_insert				= array(
								'site_cd'				=> $members_data['site_cd'],
								'item_id'				=> $items_data['id'],
								'user_id'				=> $members_data['id'],
								'character_id'			=> $character_id
							);

							# 【insert】itemuse
							$insert_id					= $database->insertDb("itemuse",$itemuse_insert);

							if(!empty($insert_id)){

								$key_check				= 1;
								$message				= "<div style=\"text-align: center;\"><br >".$character_data['nickname']."を開放しました！</div><br />";

							}else{

								$error					= 6;
								$errormessage			= "正常に処理ができませんでした";

							}

						}else{

							$error						= 5;
							$errormessage				= "既にアイテムを使用済みです";

						}

					}else{

						$error							= 4;
						$errormessage					= "アイテムをお持ちではありません";

					}

				}else{

					$error								= 3;
					$errormessage						= "アイテム情報に不備があります";

				}



			}else{

				$error									= 2;
				$errormessage							= "キャラクター情報に不備があります";

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
		**	リザルト
		**
		************************************************/

		$result['send_id']								= $character_id;
		$result['message']								= $message;
		$result['key_check']							= $key_check;
		$result['key_name']								= $key_name;
		$result['key_image']							= $key_image;


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