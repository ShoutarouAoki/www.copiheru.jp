<?php
################################ FILE MANAGEMENT ################################
##
##	mypageController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	MYPAGE PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	MYPAGE PAGE 各種処理
##
##	page : index	-> 
##	page : edit		-> プロフィール編集
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

/** USERINFO MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/UserinfoModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");


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

################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# USERINFO MODEL
$userinfoModel				= new UserinfoModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);


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
	**	マイページトップ
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){

		# プラットフォームから情報を引っ張る
		$user_data									= $authClass->getUserDataFromNijiyomeByUserId($members_data['user_id']);

		# プロフ画像
		if(!empty($user_data['entry']['0']['thumbnailUrl'])){
			$image_url								= "https:".$user_data['entry']['0']['thumbnailUrl'];
		}else{
			$image_url								= "/images/smart/noimage.png";
		}

		if($members_data['birthday'] > 0){

			# 誕生日修正
			$birth_length = mb_strlen($members_data['birthday'], "UTF-8");

			if($birth_length == 3){
				$members_data['birthday']			= "0".$members_data['birthday'];
			}

			$birthday								= date("m月d日",strtotime("2016".$members_data['birthday']));
			$unset_birthday							= NULL;

		}else{

			$birthday								= "<span class=\"style-red\">未設定</span>";
			$unset_birthday							= 1;

		}

		# USER INFOS
		$userinfos_data								= $userinfoModel->getUserinfoDataByUserId($members_data['id']);


	/************************************************
	**
	**	EDIT
	**	============================================
	**	プロフィール編集
	**
	************************************************/

	# EDIT
	}elseif($data['page'] == "edit"){

		$error										= NULL;
		$errormessage								= NULL;
		$exection									= NULL;

		# 更新
		if(!empty($_POST['edit'])){

			if(empty($_POST['nickname'])){
				$error								= 1;
				$errormessage						.= "ニックネームを入力して下さい<br />";
			}else{
				$nickname_length					= mb_strlen($_POST['nickname']);
				if($nickname_length > NICKNAME_MAX_LENGTH){
					$error							= 1;
					$errormessage					.= "ニックネームの最大文字数は".NICKNAME_MAX_LENGTH."までです<br />";
				}
			}

			if(!empty($_POST['message'])){
				$message_length						= mb_strlen($_POST['message']);
				if($message_length > PROFILE_MESSAGE_MAX_LENGTH){
					$error							= 1;
					$errormessage					.= "自己紹介の最大文字数は".NICKNAME_MAX_LENGTH."までです<br />";
				}
			}


			# 処理開始
			if(empty($error)){

				/************************************************
				**
				**	MASTER DATABASE切り替え
				**
				************************************************/

				# AUTHORITY / 既にマスターに接続してるかチェック
				$db_auth							= $database->checkAuthority();

				# DATABASE CHANGE / スレーブだったら
				if(empty($db_auth)){

					# CLOSE DATABASE SLAVE
					$database->closeDb();

					# CONNECT DATABASE MASTER
					$database->connectDb(MASTER_ACCESS_KEY);

					$db_check						= 1;

				}

				# TRANSACTION START
				$database->beginTransaction();


				# MEMBERS UPDATE
				$members_update['nickname']					= $_POST['nickname'];
				$members_update_where						= "id = :id";
				$members_update_conditions[':id']			= $members_data['id'];

				# 【UPDATE】 / members
				$query_result								= $database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

				# userinfos
				if(!empty($query_result)){

					# userinfosに情報あるかチェック
					$userinfos_data							= $userinfoModel->getUserinfoDataByUserId($members_data['id']);

					if(!empty($_POST['message'])){
						$message							= $_POST['message'];
					}else{
						$message							= NULL;
					}

					# なければインサート
					if(empty($userinfos_data['id'])){

						$userinfos_insert					= array();
						$userinfos_insert					= array(
							'user_id'						=> $members_data['id'],
							'site_cd'						=> $members_data['site_cd'],
							'message'						=> $message,
							'parameter'						=> "para1=1,para2=1,para3=1,para4=1,para5=1"
						);

						# 【INSERT】userinfos
						$query_result						= $database->insertDb("userinfos",$userinfos_insert);


					# あればアップデート
					}else{

						$userinfos_update					= array();
						$userinfos_update					= array(
							'message'						=> $message
						);

						$userinfos_update_where				= "id = :id";
						$userinfos_update_conditions[':id']	= $userinfos_data['id'];

						# 【UPDATE】 / userinfos
						$query_result						= $database->updateDb("userinfos",$userinfos_update,$userinfos_update_where,$userinfos_update_conditions);

					}

				}

				if(empty($query_result)){
					$error									= 2;
					$errormessage							= "プロフィールを正常に更新できませんでした。";
				}


				# RESULT
				if(empty($error)){

					# COMMIT
					$database->commit();

					# EXECTION SUCCESS
					$exection								= 1;

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

		if($members_data['birthday'] > 0){

			# 誕生日修正
			$birth_length = mb_strlen($members_data['birthday'], "UTF-8");

			if($birth_length == 3){
				$members_data['birthday']			= "0".$members_data['birthday'];
			}


			$birthday								= date("m月d日",strtotime("2016".$members_data['birthday']));
			$unset_birthday							= NULL;

		}else{

			$birthday								= "<span class=\"style-red\">未設定</span>";
			$unset_birthday							= 1;

		}

		# USER INFOS
		$userinfos_data								= $userinfoModel->getUserinfoDataByUserId($members_data['id']);

		# 成功時上書き
		if(!empty($exection)){
			$members_data['nickname']				= $_POST['nickname'];
			$userinfos_data['message']				= $_POST['message'];
		}

		# ERROR INPUT DATA 上書き
		if(!empty($error)){

			# ニックネーム
			$nickname								= $_POST['nickname'];

			# 自己紹介
			$message								= $_POST['message'];

		# INPUT DATA
		}else{

			# ニックネーム
			$nickname								= $members_data['nickname'];

			# 自己紹介
			$message								= $userinfos_data['message'];

		}

	}


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



}


################################## FILE END #####################################
?>