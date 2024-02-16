<?php
################################ FILE MANAGEMENT ################################
##
##	enterController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	ENTER LOGIN SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	プラットフォーム側とのOauth認証処理
##	membersでのユーザー初回ログイン認証処理
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

/** VISITOR MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/VisitorModel.php");

/** ACCESS MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AccessModel.php");


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

# VISITOR MODEL
$visitorModel				= new VisitorModel($database,$mainClass);

# ACCESS MODEL
$accessModel				= new AccessModel($database,$mainClass);


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

	# INDEX
	if($data['page'] == "index"){


		/************************************************
		**
		**	格納したセッション情報の取り出し
		**
		************************************************/

		# GET AUTH SESSION
		$auth_data								= $sessionClass->getAuthSession();


		/************************************************
		**
		**	プラットフォームとのOAuth認証
		**
		************************************************/

		# CHECK OAUTH API
		$auth_check								= $authClass->checkOauthApiFromNijiyome($auth_data);


		# 認証OK
		if(!empty($auth_check)){

			/************************************************
			**
			**	プラットフォームのユーザー情報取得
			**
			************************************************/

			# PF側の ユーザーデータ 取得
			$user_data							= $authClass->getUserDataFromNijiyome();

			# PF側の ユーザーデータ 取得エラー
			if(!empty($user_data['error'])){
				$mainClass->redirect("/error/index/".$user_data['error']."/");
				exit();

			# OK
			}else{

				# USER ACCESS DEVICE
				$user_device['access_device']	= $default_device;
				$user_device['access_os']		= $default_os;

				# DB DATA 取得
				$members_data					= $memberModel->checkFirstCertify($user_data['entry']['0']['id'],$user_device);

				# OK
		        if(empty($members_data['error'])){

					# セッションに情報格納
					$_SESSION['member_id']		= $members_data['id'];
					$_SESSION['user_id']		= $members_data['user_id'];
					$_SESSION['user_pass']		= $members_data['user_pass'];
					
					/**/
					// リダイレクト時にsetがなぜか消えた。必ずここは解消すること。
					$_SESSION['set']			= 1;
					/** */

					# 今日最初のアクセス
					if(!empty($members_data['access'])){
						$_SESSION['access']		= $members_data['access'];
					}

					# 新規ユーザー初ログイン
					if(!empty($members_data['regist'])){
						$_SESSION['regist']		= $members_data['regist'];
					}


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

						$db_check				 = 1;

					}

					# UPDATE VISITOR COUNT
					$visitorModel->countVisitor($members_data['site_cd'],$members_data['id'],$default_number,$default_os);

					# INSERT ACCCESS COUNT
					$accessModel->insertAccess($members_data['site_cd'],$members_data['id'],$default_number,$default_os);

					# CLOSE DATABASE
					$database->closeDb();
					$database->closeStmt();


					/******************************************
					**
					**	REDIRECT
					**	---------------------------------------
					**	メインページ / もしくはチュートリアルにリダイレクト
					**
					*******************************************/

					# REDIRECT
					if($members_data['tutorial'] == 0){
						$mainClass->redirect("/tutorial/");
						
					}else{
						$mainClass->redirect("/main/");
					}

					# EXIT
					exit();

				# ERROR
				}else{
					$mainClass->redirect("/error/index/".$members_data['error']."/");
					exit();

				}

			}


		# プラットフォーム API 認証エラー
		}else{
			$mainClass->redirect("/error/index/1/");
			exit();

		}

	}

}

# CLOSE DATABASE
$database->closeDb();
$database->closeStmt();

exit();


################################## FILE END #####################################
?>