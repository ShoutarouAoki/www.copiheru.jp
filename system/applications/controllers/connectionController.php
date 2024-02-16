<?php
################################ FILE MANAGEMENT ################################
##
##	connectionController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	CONNECTION SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	プラットフォーム側との通信処理
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
	**	ADD / 登録処理
	**	============================================
	**	プラットフォーム側のユーザー情報を取得しておく
	**
	************************************************/

	# ADD
	}elseif($data['page'] == "add"){

		if(!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){

			# にじよめユーザーID
			$user_id								= $_REQUEST['id'];

			# members check
			$members_data							= $memberModel->getMemberDataByUserId($user_id,"all");

			/************************************************
			**
			**	登録処理
			**
			************************************************/

			# USER情報なかったら登録
			if(empty($members_data['id'])){


				/************************************************
				**
				**	プラットフォームのユーザー情報取得
				**
				************************************************/

				$user_data							= $authClass->getUserDataFromNijiyomeByUserId($user_id);

				# ERROR
				if(!empty($user_data['error'])){

					exit();

				# OK
				}else{

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

						//ml("INSERT","UESR OK",$members_update);

					# プラットフォーム側のユーザー情報取れなかったら
					}else{


						# 性別(システム用)
						$sex						= 1;

						$device						= 0;

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
							'age'					=> $age,
							'sex'					=> $sex,
			                'ad_code'				=> $adcode,
							's_point'				=> $s_point,
							'device'				=> $device,
							'entry_date'			=> date("YmdHis"),
							'mail_flg'				=> 8,
							'open_flg'				=> 1,
						);

						//ml("INSERT","UESR NON",$members_update);

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


				}


			/************************************************
			**
			**	更新処理
			**
			************************************************/

			# 既にUSER情報があったら
			}else{

				/************************************************
				**
				**	プラットフォームのユーザー情報取得
				**
				************************************************/

				$user_data							= $authClass->getUserDataFromNijiyomeByUserId($user_id);

				# ERROR
				if(!empty($user_data['error'])){


				# DELETE
				}elseif($user_data['status'] == 9 || $user_data['status'] == 19 || $user_data['status'] == 29 || $user_data['status'] == 39 || $user_data['status'] > 39){


				# OK
				}else{

					$members_update					= array();

					# 退会復活
					if($members_data['status'] == 8 || $members_data['status'] == 18 || $members_data['status'] == 28 || $members_data['status'] == 38){

						$status						= substr($members_data['status'],0,-1);

						if(empty($status)){
							$status					= 0;
						}

						$members_update['status']		= $status;
						$members_update['leave_date']	= "0000-00-00";

					}

					# プラットフォーム側のユーザー情報取れたら
					if(!empty($user_data['entry']['0']['id'])){

						/************************************************
						**
						**	データ生成
						**
						************************************************/

						# NICKNAME
						if(empty($members_data['nickname'])){
							if(!empty($user_data['entry']['0']['nickname'])){
								$members_update['nickname']	= $user_data['entry']['0']['nickname'];
							}elseif(!empty($user_data['entry']['0']['displayName'])){
								$members_update['nickname']	= $user_data['entry']['0']['displayName'];
							}
						}

						# 性別
						if(empty($members_data['gender'])){
							if(!empty($user_data['entry']['0']['gender'])){
								if(isset($gender_array[$user_data['entry']['0']['gender']])){
									$members_update['gender']	= $gender_array[$user_data['entry']['0']['gender']];
								}
								if(empty($members_update['gender'])){
									$members_update['gender']	= 0;
								}
							}
						}

						# 年齢
						if(empty($members_data['age'])){
							if(!empty($user_data['entry']['0']['age'])){
								$members_update['age']		= $user_data['entry']['0']['age'];
							}
							if(empty($members_update['age'])){
								$members_update['age']		= 0;
							}
						}

						# 生年月日
						if(empty($members_data['birthday'])){
							if(!empty($user_data['entry']['0']['birthday'])){
								$members_update['birthday']	= $user_data['entry']['0']['birthday'];
							}
							if(empty($members_update['birthday'])){
								$members_update['birthday']	= 0;
							}
						}

						# ユーザータイプ
						if(empty($members_data['type'])){
							if(!empty($user_data['entry']['0']['userType'])){
								$members_update['type']		= $user_data['entry']['0']['userType'];
							}
							if(empty($members_update['type'])){
								$members_update['type']		= 0;
							}
						}

						# ユーザーグレード
						if(empty($members_data['chikuwa'])){
							if(!empty($user_data['entry']['0']['userGrade'])){
								$members_update['chikuwa']	= $user_data['entry']['0']['userGrade'];
							}
							if(empty($members_update['chikuwa'])){
								$members_update['chikuwa']		= 0;
							}
						}

						# パスワード(必要ないけど念のため)
						if(empty($members_data['nickname'])){
			    			$members_update['user_ps']    	= mt_rand("1000", "9555");
						}

						# adcode
						if(empty($members_data['ad_code'])){
							$members_update['ad_code']	= DEFAULT_ADCODE.date("Ym");
						}

						# 他ユーザーからの招待だったら
						if(isset($_REQUEST['invite_from_id'])){

							# どこから来たか
							if(isset($_REQUEST['invite_from'])){

							}

						}

						//ml("UPDATE","UESR OK",$members_update);

					}

					# 【UPDATE】members
					if(!empty($members_update)){

						$members_update_where		= "id = :id";
						$members_update_conditions[':id']	= $members_data['id'];
						$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

					}

				}

			}

		}


	/************************************************
	**
	**	RESERVE / 事前予約登録処理
	**	============================================
	**	プラットフォーム側のユーザーIDは取れないので
	**	ユーザーIDだけ入れておく
	**
	************************************************/

	# RESERVE
	}elseif($data['page'] == "reserve"){


		if(!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){

			# にじよめユーザーID
			$user_id								= $_REQUEST['id'];

			# members check
			$members_data							= $memberModel->getMemberDataByUserId($user_id,"all");


			/************************************************
			**
			**	登録処理
			**
			************************************************/

			# USER情報なかったら登録
			if(empty($members_data['id'])){


				/************************************************
				**
				**	プラットフォームのユーザー情報取得
				**
				************************************************/

				$user_data							= $authClass->getUserDataFromNijiyomeByUserId($user_id);

				# ERROR
				if(!empty($user_data['error'])){

					pr($user_data);
					exit();

				# OK
				}else{

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
						$adcode						= RESERVED_ADCODE.date("Ym");

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
							'reserve'				=> 1
						);

						//ml("INSERT","UESR OK",$members_update);

					# プラットフォーム側のユーザー情報取れなかったら
					}else{


						# 性別(システム用)
						$sex						= 1;

						$age						= 20;

						$device						= 0;

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
						$adcode						= RESERVED_ADCODE.date("Ym");

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
							'age'					=> $age,
							'sex'					=> $sex,
			                'ad_code'				=> $adcode,
							's_point'				=> $s_point,
							'device'				=> $device,
							'entry_date'			=> date("YmdHis"),
							'mail_flg'				=> 8,
							'open_flg'				=> 1,
							'reserve'				=> 1
						);

						//ml("INSERT","UESR NON",$members_update);

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


				}



			}


		}





	/************************************************
	**
	**	REMOVE / 退会処理
	**
	************************************************/

	# REMOVE
	}elseif($data['page'] == "remove"){

		if(!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){

			# にじよめユーザーID
			$user_id							= $_REQUEST['id'];

			# members check
			$members_data						= $memberModel->getMemberDataByUserId($user_id,"all");


			/************************************************
			**
			**	退会処理
			**
			************************************************/

			# USER情報あったら退会処理
			if(!empty($members_data['id'])){

				if($members_data['status'] < 8){

					$status						= $members_data['status']."8";

					if(empty($status)){
						$status					= 8;
					}

					$members_update				= array(
						'status'				=> $status,
						'leave_date'			=> date("Y-m-d")
					);

					$members_update_where		= "id = :id";
					$members_update_conditions[':id']	= $members_data['id'];
					$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

					# 送信メール削除 退会は8
					$send_update				= array(
						'del_flg'				=> 8
					);

					$send_update_where							= "send_id = :send_id AND del_flg = :send_del_flg";
					$send_update_conditions[':send_id']			= $members_data['id'];
					$send_update_conditions[':send_del_flg']	= 0;
					$database->updateDb("mails",$send_update,$send_update_where,$send_update_conditions);


					# 受信メール削除 退会は8
					$recv_update				= array(
						'del_flg'				=> 8
					);

					$recv_update_where							= "recv_id = :recv_id AND del_flg = :recv_del_flg";
					$recv_update_conditions[':recv_id']			= $members_data['id'];
					$recv_update_conditions[':recv_del_flg']	= 0;
					$database->updateDb("mails",$recv_update,$recv_update_where,$recv_update_conditions);

				}

			}


		}


	/************************************************
	**
	**	RESUME / 復活処理
	**
	************************************************/

	# RESUME
	}elseif($data['page'] == "resume"){

		ml("DEBUG",$data['page'],$_REQUEST);






	}

}

############################### DATABASE CLOSE ##################################

$database->closeDb();
$database->closeStmt();

################################## FILE END #####################################

exit();

################################## FILE END #####################################
?>