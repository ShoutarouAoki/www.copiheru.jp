<?php
################################ FILE MANAGEMENT ################################
##
##	tutorialController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	TUTORIAL SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	チュートリアル
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

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** USERINFO MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/UserinfoModel.php");

/** AUTOMAIL MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AutomailModel.php");

/** MAIL MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MailModel.php");

/** REGISTCOUNT MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/RegistcountModel.php");

################################# POST ARRAY ####################################

$value_array				= array('page','number');
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

# NUMBER
if(!empty($_POST['number'])){
	$data['number']			= $_POST['number'];
}

# HEADER / FOTTER HIDE
$header_hide				= 1;
$footer_hide				= 1;

# サブヘッダーとサブフッターを表示
$sub_header					= 1;
$sub_footer					= 1;


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);

# USERINFO MODEL
$userinfoModel				= new UserinfoModel($database,$mainClass);

# AUTOMAIL MODEL
$automailModel				= new AutomailModel($database,$mainClass);

# MAIL MODEL
$mailModel					= new MailModel($database,$mainClass);

# REGISTCOUNT MODEL
$registcountModel			= new RegistcountModel($database,$mainClass);

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
	**	チュートリアルスタート
	**
	************************************************/

	# INDEX / PAGE
	if($data['page'] == "index" || $data['page'] == "page"){

		$tutorial						= array();
		$this_number					= NULL;
		$next_number					= NULL;
		$previous_number				= NULL;
		$last_number					= NULL;

		$error							= 0;
		$errormessage					= NULL;


		if(!empty($data['number'])){
			$this_number				= $data['number'];
		}else{
			$this_number				= 0;
		}

		# 背景画像取得
		$image_file_type				= $web_filetype_array[$directory];

		$image_conditions				= array();
		$image_conditions				= array(
			'file_type'					=> $image_file_type,
			'category'					=> $web_image_category,
			'target_id'					=> 0,
			'status'					=> 1,
			'order'						=> 'rank'
		);

		$image_rtn						= $imageModel->getImageList($image_conditions);

		$i=0;
		while($image_data = $database->fetchAssoc($image_rtn)){

			$image_list['id'][$i]		= $image_data['id'];
			$image_list['image'][$i]	= $image_data['img_name'];
			$image_list['rank'][$i]		= $image_data['rank'];

			$i++;

		}

		$database->freeResult($image_rtn);

		if($i > 0){

			# 最終ページ番号
			$last_number				= $i - 1;

			# 最終ページじゃなかったら
			if($this_number != $last_number){
				$next_number			= $this_number + 1;
			}

			# 初回ページじゃなければ
			if($this_number > 0){
				$previous_number		= $this_number - 1;
			}

			$result['id']				= $image_list['id'][$this_number];
			$result['image']			= HTTP_WEB_IMAGE."/".$image_list['image'][$this_number];
			$result['next']				= $next_number;
			$result['previous']			= $previous_number;

		}

		# ERROR
		if(empty($result['image'])){

			$error						= 1;
			$errormessage				= "チュートリアルページに不備がありました";

		}


		/************************************************
		**
		**	pageだったらjsonで返す
		**
		************************************************/

		if($data['page'] == "page"){


			/************************************************
			**
			**	DATABASE 切断
			**
			************************************************/

			# CLOSE DATABASE
			$database->closeDb();
			$database->closeStmt();


			# ERROR
			$result['error']			= $error;
			$result['errormessage']		= $errormessage;


			/************************************************
			**
			**	jsonでリザルトを返す
			**
			************************************************/

			header('Content-Type: application/json; charset=utf-8');
			print(json_encode($result));
			exit();

			# 終了
			exit();

		}




	/************************************************
	**
	**	プロフィール記入
	**
	************************************************/

	# PROFILE
	}elseif($data['page'] == "profile"){

		# 作成済み
		if($members_data['tutorial'] == 1){
			$mainClass->redirect("/main/");
			exit();
		}

		# MONTH
		$month_option					= NULL;
		for($i=0;$i<12;$i++){
			$month						= $i + 1;
			if($month < 10){
				$month					= "0".$month;
			}
			$month_option				.= "<option value=\"".$month."\">".$month."月</option>\n";
		}

		# DAY
		$day_option						= NULL;
		for($i=0;$i<31;$i++){
			$day						= $i + 1;
			if($day < 10){
				$day					= "0".$day;
			}
			$day_option					.= "<option value=\"".$day."\">".$day."日</option>\n";
		}



	/************************************************
	**
	**	プロフィール処理
	**
	************************************************/

	# EDIT
	}elseif($data['page'] == "edit"){

		$result							= array();
		$success						= NULL;

		$error							= 0;
		$errormessage					= NULL;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$_POST['nickname']							= "俺や";
			$_POST['month']								= "05";
			$_POST['day']								= "13";
			$_POST['message']							= "メッセージ";
		}

		# ERROR
		if(empty($_POST['nickname'])){
			$error						= 1;
			$errormessage				.= "ニックネームを入力して下さい<br />";
		}else{
			$nickname_length			= mb_strlen($_POST['nickname']);
			if($nickname_length > NICKNAME_MAX_LENGTH){
				$error					= 1;
				$errormessage			.= "ニックネームの最大文字数は".NICKNAME_MAX_LENGTH."までです<br />";
			}
		}

		if(empty($_POST['month']) || empty($_POST['day'])){
			$error						= 1;
			$errormessage				.= "誕生日が不正です<br />";
		}

		$day_check = intVal($_POST['day']);
		switch($_POST['month']){
			case "02":
				if($day_check>29){
					$error						= 1;
					$errormessage				.= "誕生日が不正です<br />";
				}
				break;
			case "04":
			case "06":
			case "09":
			case "11":
				if($day_check>30){
					$error						= 1;
					$errormessage				.= "誕生日が不正です<br />";
				}
				break;
		}

		if(!empty($_POST['message'])){
			$message_length				= mb_strlen($_POST['message']);
			if($message_length > PROFILE_MESSAGE_MAX_LENGTH){
				$error					= 1;
				$errormessage			.= "自己紹介の最大文字数は".NICKNAME_MAX_LENGTH."までです<br />";
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
			$db_auth									= $database->checkAuthority();

			# DATABASE CHANGE / スレーブだったら
			if(empty($db_auth)){

				# CLOSE DATABASE SLAVE
				$database->closeDb();

				# CONNECT DATABASE MASTER
				$database->connectDb(MASTER_ACCESS_KEY);

				$db_check								= 1;

			}

			# TRANSACTION START
			$database->beginTransaction();


			$birthday									= $_POST['month'].$_POST['day'];

			$members_update['nickname']					= $_POST['nickname'];
			$members_update['status']					= 1;
			$members_update['birthday']					= $birthday;
			$members_update['reg_date']					= date("YmdHis");
			$members_update['tutorial']					= 1;
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

			# 登録ERROR
			if(empty($query_result)){

				$error									= 2;
				$errormessage							= "プロフィールを正常に作成できませんでした。";

			# 登録OK / カウント
			}else{

				# INSERT REGIST COUNT
				$registcountModel->insertRegistcount($members_data['site_cd'],$default_number,$default_os);

			}

			# 初回メールインサート
			if(empty($error)){

				# 初回メールはauto_type = 9
				$automails_conditions					= array();
				$automails_conditions					= array(
					'auto_type'							=> 9,
					'status'							=> 0
				);

				$automails_list							= $automailModel->getAutomailList($automails_conditions);

				$automails_count = count($automails_list);
				for($i=0; $i<$automails_count; $i++){
					if(!empty($automails_list[$i]['id'])
						&& !empty($automails_list[$i]['title']) && !empty($automails_list[$i]['message'])){

						# キャラデータ
						$character_data						= $memberModel->getMemberDataById($automails_list[$i]['send_id'],NULL,"*");

						/************************************************
						**
						**	mails insert / array
						**
						************************************************/

						if(!empty($character_data['id'])){

							$insert_subject		= $mailModel->sendAllReplace($automails_list[$i]['title'],$_POST['nickname'],$members_data,NULL);
							$insert_message		= $mailModel->sendAllReplace($automails_list[$i]['message'],$_POST['nickname'],$members_data,NULL);

							# もし子キャラだったら
							if($character_data['naruto'] > 0){
								$character_data['id']		= $character_data['naruto'];
							}

							$mails_insert					= array();
							$mails_insert					= array(
								'site_cd'					=> $members_data['site_cd'],
								'send_id'					=> $character_data['id'],
								'recv_id'					=> $members_data['id'],
								'send_date'					=> date("YmdHis"),
								'title'						=> $insert_subject,
								'message'					=> $insert_message,
								'recv_flg'					=> 1,
								'pref'						=> $members_data['pref'],
								'city'						=> $members_data['city'],
								'age'						=> $character_data['id'],
								'media'						=> $automails_list[$i]['media'],
								'media_flg'					=> $automails_list[$i]['media_flg'],
								'op_id'						=> $automails_list[$i]['op_id'],
								'owner_id'					=> $automails_list[$i]['owner_id']
							);

							# 【INSERT】mails
							$mails_insert_id				= $database->insertDb("mails",$mails_insert);

							if(empty($mails_insert_id)){
								$error						= 3;
								$errormessage				= "正常に処理できませんでした。";
							}

						}

					}

				}

			}

			# RESULT
			if(empty($error)){

				# COMMIT
				$database->commit();

				# SUCCESS
				$success								= 1;

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


		# ERROR
		$result['error']			= $error;
		$result['errormessage']		= $errormessage;


		# RESULT
		$result['success']			= $success;

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

		# 終了
		exit();



	/************************************************
	**
	**	チュートリアル終了
	**
	************************************************/

	# FINISH
	}elseif($data['page'] == "finish"){

		$error						= NULL;
		$errormessage				= NULL;

		# 新規ユーザー初ログイン
		if(!isset($_SESSION['regist'])){
			$_SESSION['regist']		= 1;
		}

		# まだチュートリアル終わってない
		if($members_data['tutorial'] == 0){

			$error					= 1;
			$errormessage			= "チュートリアルが正常に終了しておりません。<br />お手数ですが最初からやり直して下さい。";

		}


	}

}


################################## FILE END #####################################
?>