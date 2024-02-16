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
##	プラットフォーム側とのOauth認証処理
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

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

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

# このページではヘッダーとフッターを消す(スマフォ)
$header_hide				= 1;
$footer_hide				= 1;

# サブヘッダーとサブフッターを表示
//$sub_header					= 1;
//$sub_footer					= 1;

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

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);


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
	**	プラットフォームのAPI AUTH認証
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){


		if(defined("SYSTEM_CHECK")){

			$_SESSION['member_id']		= NULL;
			$_SESSION['user_id']		= NULL;
			$_SESSION['user_pass']		= NULL;
			$_SESSION['session_time']	= NULL;

			$sessionClass->resetAuthSession();

		}


		/************************************************
		**
		**	iframeで渡されたGETパラメーターをセッションに格納
		**
		************************************************/

		# SET SESSION
		$sessionClass->setAuthSession($_GET);


		/************************************************
		**
		**	格納したセッション情報の取り出し
		**
		************************************************/

		# GET AUTH SESSION
		$auth_data			= $sessionClass->getAuthSession();
		/************************************************
		**
		**	プラットフォームとのOAuth認証
		**
		************************************************/

		# CHECK OAUTH API
		$auth_check			= $authClass->checkOauthApiFromNijiyome($auth_data);

		# 認証OK
		if(!empty($auth_check)){

			# DEBUG PRINT
			$mainClass->debug("base_string : ".$auth_check['basestring']);
			$mainClass->debug("signature_key : ".$auth_check['signature_key']);
			$mainClass->debug("渡された oauth_signature : ".$auth_check['oauth_signature']);
			$mainClass->debug("生成した oauth_signature : ".$auth_check['signature']);

			/************************************************
			**
			**	プラットフォームのユーザー情報取得
			**
			************************************************/

			# PF側の ユーザーデータ 取得
			$user_data		= $authClass->getUserDataFromNijiyome();


			# PF側の ユーザーデータ 取得エラー
			if(!empty($user_data['error'])){
				$mainClass->redirect("/error/index/".$user_data['error']."/");
				exit();

			# OK
			}else{

				# DEBUG PRINT
				$mainClass->debug($user_data);

				# 背景画像取得
				$image_file_type		= $web_filetype_array[$directory];

				$image_conditions		= array();
				$image_conditions		= array(
					'file_type'			=> $image_file_type,
					'category'			=> $web_image_category,
					'target_id'			=> 0,
					'status'			=> 1
				);

				$image_data				= $imageModel->getImageData($image_conditions);
				$user_id				= $user_data['entry']['0']['id'];
				$members_check			= $memberModel->getMemberDataByUserId($user_id);

				if(empty($members_check['id'])){

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

						$db_check							 = 1;

					}

					$mainClass->debug("NOW REGIST");

					# プラットフォーム側のユーザー情報取れたら
					if(!empty($user_data['entry']['0']['id'])){


						/************************************************
						**
						**	データ生成
						**
						************************************************/

						# 初期化
						$nickname					= NULL;
						$sex						= 1;
						$gender						= 0;
						$age						= 20;
						$birthday					= 0;
						$type						= 0;
						$chikuwa					= 0;
						$device						= 0;

						# NICKNAME
						if(!empty($user_data['entry']['0']['nickname'])){
							$nickname				= $user_data['entry']['0']['nickname'];
						}elseif(!empty($user_data['entry']['0']['displayName'])){
							$nickname				= $user_data['entry']['0']['displayName'];
						}

						# 性別
						if(!empty($user_data['entry']['0']['gender'])){
							if(isset($gender_array[$user_data['entry']['0']['gender']])){
								$gender				= $gender_array[$user_data['entry']['0']['gender']];
							}
						}

						# 年齢
						if(!empty($user_data['entry']['0']['age'])){
							$age					= $user_data['entry']['0']['age'];
						}

						# 生年月日
						if(!empty($user_data['entry']['0']['birthday'])){
							$birthday				= $user_data['entry']['0']['birthday'];
						}

						# ユーザータイプ
						if(!empty($user_data['entry']['0']['userType'])){
							$type					= $user_data['entry']['0']['userType'];
						}

						# ユーザーグレード
						if(!empty($user_data['entry']['0']['userGrade'])){
							$chikuwa				= $user_data['entry']['0']['userGrade'];
						}

						# 他ユーザーからの招待だったら
						if(isset($_REQUEST['invite_from_id'])){

							# どこから来たか
							if(isset($_REQUEST['invite_from'])){

							}

						}

						# DEVICE
						if(isset($_REQUEST['device'])){

							if($_REQUEST['device'] == "iPhone"){
								$device				= 5;
							}elseif($_REQUEST['device'] == "Android"){
								$device				= 7;
							}else{
								$device				= 4;
							}

						}

						# パスワード(必要ないけど念のため)
			    		$password    				= mt_rand("1000", "9555");

						# adcode
						$adcode						= DEFAULT_ADCODE.date("Ym");

						# 登録時 s_point
						$point_no_id				= 5;
						$pointsets_conditions		= array();
						$pointsets_conditions		= array(
							'site_cd'				=> SITE_CD,
							'sex'					=> $sex,
							'pay_count'				=> 0
						);

						$pointsets_data				= $pointsetModel->getPointset($point_no_id,$pointsets_conditions);

						$s_point					= $pointsets_data[0]['point'];

						$members_insert				= array();
						$members_insert				= array(
							'user_id'				=> $user_id,
							'user_ps'				=> $password,
							'site_cd'				=> SITE_CD,
							'nickname'				=> $nickname,
							'age'					=> $age,
							'sex'					=> $sex,
							'gender'				=> $gender,
			                'ad_code'				=> $adcode,
							's_point'				=> $s_point,
		           		    'chikuwa'				=> $chikuwa,
							'device'				=> $device,
							'birthday'				=> $birthday,
							'entry_date'			=> date("YmdHis"),
							'mail_flg'				=> 8,
							'open_flg'				=> 1,
			                'type'					=> $type,
						);

					}

					# 【INSERT】members
					$insert_id						= $database->insertDb("members",$members_insert);
					
					# USER INFOS
					if(!empty($insert_id)){

						$userinfos_insert			= array();
						$userinfos_insert			= array(
							'user_id'				=> $insert_id,
							'site_cd'				=> SITE_CD,
							'title'					=> "はじめまして",
							'message'				=> "こんにちわ",
							'parameter'				=> "para1=1,para2=1,para3=1,para4=1,para5=1"
						);

						# 【INSERT】userinfos
						$userinfos_id				= $database->insertDb("userinfos",$userinfos_insert);

					}

					# DATABASE CHANGE
					if(!empty($db_check)){

						# CLOSE DATABASE MASTER
						$database->closeDb();

						# CONNECT DATABASE SLAVE
						$database->connectDb();

					}


				}else{

					$mainClass->debug("MEMBERS OK");

				}



			}

		# プラットフォーム API 認証エラー
		}else{
			$mainClass->redirect("/error/index/1/");
			exit();

		}

	}

}


################################## FILE END #####################################
?>