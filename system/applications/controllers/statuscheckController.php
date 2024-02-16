<?php
################################ FILE MANAGEMENT ################################
##
##	indexController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	INDEX SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	INDEX 各種処理
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2014/10/31
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
################################# ACCESS CHECK ##################################



################################# REQUIRE MODEL #################################


/************************************************
**
**	MODEL FILE REQUIRE
**	---------------------------------------------
**	MODEL CLASS FILE READING
**
************************************************/

# NONE

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

# ERROR
$error						= NULL;

################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# NONE


################################## MAIN SQL #####################################


	/************************************************
	**
	**	ページ毎にif文で処理分岐
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){




	# IP & SESSION GET
	}elseif($data['page'] == "first"){


		if(empty($_POST['user_id'])){

			$error			= "にじよめIDをご記入下さい<br />";

		}


		if(empty($error)){

			# STATUS CHECK
			$user_device	= $deviceClass->getDeviceType();
			$user_ip		= REMOTE_ADDR;
			$user_session	= $_SESSION['session_id'];
			$user_browser	= $deviceClass->getUserBrowser();

			$body			 = "■チェック日時\n".date("Y/m/d H:i:s")."\n\n";
			$body			.= "■ユーザーID\n".$_POST['user_id']."\n\n";
			$body			.= "■ユーザーデバイス\n".$user_device."\n\n";
			$body			.= "■ユーザーIP\n".$user_ip."\n\n";
			$body			.= "■ユーザーセッション\n".$user_session."\n\n";
			$body			.= "■ユーザーブラウザ\n".$user_browser['browser']."\n\n";
			$body			.= "---------------------------\n\n";

			# LOG FILE
			$file_name		 = $_POST['user_id']."-".date("Ymd");
			$logfile_path	 = DOCUMENT_ROOT_STATUSLOG."/".$file_name.".txt";

			# LOG FILE CHECK
			if(!file_exists($logfile_path)){
				$create = fopen($logfile_path,'w');
				fclose($create);
			}

			$fp				 = fopen($logfile_path, "ab");

			if(!empty($fp)){
			    if(flock($fp, LOCK_EX)){
			        if(fwrite($fp,$body) === FALSE){

			        }else{

			        }
			        flock($fp, LOCK_UN);
			    }else{

			    }
			}

			fclose($fp);

		}


	# IP & SESSION CHECK
	}elseif($data['page'] == "second"){

		# STATUS CHECK
		$user_device		= $deviceClass->getDeviceType();
		$user_ip			= REMOTE_ADDR;
		$user_session		= $_SESSION['session_id'];
		$user_browser		= $deviceClass->getUserBrowser();

		$device_check		= NULL;
		$ip_check			= NULL;
		$session_check		= NULL;
		$browser_check		= NULL;

		if($_POST['user_device'] != $user_device){
			$error			.= "アクセスデバイスに差異があります<br />";
			$device_check	 = 1;
		}

		if($_POST['user_ip'] != $user_ip){
			$error			.= "IPが変更されています<br />";
			$ip_check		 = 1;
		}

		if($_POST['user_session'] != $user_session){
			$error			.= "セッションが保持されていません<br />";
			$session_check	 = 1;
		}

		if($_POST['user_browser'] != $user_browser['browser']){
			$error			.= "ブラウザの認識に不整合があります<br />";
			$browser_check	 = 1;
		}


		$body			 = "■チェック日時\n".date("Y/m/d H:i:s")."\n\n";
		$body			.= "■ユーザーID\n".$_POST['user_id']."\n\n";
		$body			.= "■ユーザーデバイス\n".$_POST['user_device']."\n";
		$body			.= $user_device."\n\n";
		$body			.= "■ユーザーIP\n".$_POST['user_ip']."\n";
		$body			.= $user_ip."\n\n";
		$body			.= "■ユーザーセッション\n".$_POST['user_session']."\n";
		$body			.= $user_session."\n\n";
		$body			.= "■ユーザーブラウザ\n".$_POST['user_browser']."\n";
		$body			.= $user_browser['browser']."\n\n";
		$body			.= "---------------------------\n\n";


		$logfile_path	 = DOCUMENT_ROOT_STATUSLOG."/".$_POST['file_name'].".txt";

		# LOG FILE CHECK
		if(!file_exists($logfile_path)){
			$create = fopen($logfile_path,'w');
			fclose($create);
		}

		$fp				 = fopen($logfile_path, "ab");

		if(!empty($fp)){
		    if(flock($fp, LOCK_EX)){
		        if(fwrite($fp,$body) === FALSE){

		        }else{

		        }
		        flock($fp, LOCK_UN);
		    }else{

		    }
		}

		fclose($fp);


	# USER DATA
	}elseif($data['page'] == "third"){

		# ERROR
		$user_check				= NULL;
		$members_check			= NULL;

		$body					= NULL;

		# にじよめユーザーチェック
		$user_data				= $authClass->getUserDataFromNijiyomeByUserId($_POST['user_id']);

		# ERROR
		if(!empty($user_data['error'])){
			$error				.= "にじよめからお客様の情報が取得できません。<br />にじよめIDが正しいかご確認下さい。<br /><br />";
			$user_check			 = 1;
			$body				.= "■にじよめ状態\nERROR\n\n";
		}else{
			$body				.= "■にじよめ状態\n正常\n";
			$body				.= $user_data['entry']['0']['id']."\n\n";
		}

		# きゃばへるデータ
		$members_data			 = $memberModel->getMemberDataByUserId($_POST['user_id']);

		if(empty($members_data['id'])){
			$error				.= "きゃばへるにはお客様のデータがありません。<br />ゲーム内お問い合わせにてご連絡下さい<br /><br />";
			$members_check		 = 1;
			$body				.= "■きゃばへる状態\nERROR\n\n";
		}else{
			$body				.= "■きゃばへる状態\n正常\n";
			$body				.= "ID : ".$members_data['id']."\n";
			$body				.= "STATUS : ".$members_data['status']."\n";
			$body				.= "NICKNAME : ".$members_data['nickname']."\n\n";
		}

		$body			.= "---------------------------\n\n";

		$logfile_path	 = DOCUMENT_ROOT_STATUSLOG."/".$_POST['file_name'].".txt";

		# LOG FILE CHECK
		if(!file_exists($logfile_path)){
			$create = fopen($logfile_path,'w');
			fclose($create);
		}

		$fp				 = fopen($logfile_path, "ab");

		if(!empty($fp)){
		    if(flock($fp, LOCK_EX)){
		        if(fwrite($fp,$body) === FALSE){

		        }else{

		        }
		        flock($fp, LOCK_UN);
		    }else{

		    }
		}

		fclose($fp);



	}


################################## FILE END #####################################
?>