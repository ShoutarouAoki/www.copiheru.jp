<?php
################################ FILE MANAGEMENT ################################
##
##	/exection/news.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	NEWS EXECTION SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	BBS DB 格納処理
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
################################ BASIC MODELS ###################################


/************************************************
**
**	BASIC LIBRARY FILE
**	---------------------------------------------
**	BASIC LIBRARY CLASS FILE読み込み
**	必須項目
**
************************************************/

/** VALIDATION LIBRARY **/
require_once(DOCUMENT_ROOT_VENDORS."/ValidationClass.php");


################################ LIBRARY CLASS ##################################


/************************************************
**
**	BASIC LIBRARY CLASS CALL
**	---------------------------------------------
**	PHP BASIC LIBRARY CLASS CALL
**	必須項目
**
************************************************/

# VALIDATION CLASS
$validationClass	= new ValidationClass();


################################### EXECTION  ###################################


/******************************************
**
**	ERROR CHECK
**	---------------------------------------
**	VALIDATION MODEL
**
*******************************************/

# VALIDATION
$post_data			= $validationClass->validateBbs($_POST);

/******************************************
**
**	処理
**
*******************************************/

if(empty($post_data['error'])){


	# CLOSE DATABASE
	$database->closeDb();

	# CONNECT DATABASE MASTER
	$database->connectDb(MASTER_ACCESS_KEY);


	/***********************************************
	**
	**	INSERT
	**
	************************************************/

	if($post_data['purpose'] == 1){

		# TABLE
		$table								= "bbs";

		# CONDITIONING
		$edit_data							= $database->arrangeData($table,$post_data,$post_data['purpose']);

		# ADD
		$edit_data['category']				= $page_data['category'];
		$edit_data['type']					= $page_data['type'];
		$edit_data['status']				= 1;

		if(!empty($post_data['system_write'])){
			$edit_data['write_date']		= date("Y-m-d H:i:s", strtotime($post_data['system_write']));
		}else{
			$edit_data['write_date']		= date("Y-m-d H:i:s");
		}

		# USER ID
		if(!empty($user_data['id'])){
			$edit_data['user_id']			= $user_data['id'];
		}

		# NUMBER
		$max_number							= $bbsModel->getBbsMaxNumber($post_data['thread_id'], $page_data['category']);
		$edit_data['number']				= $max_number + 1;

		# INSERT
		$insert_id							= $database->insertDb($table,$edit_data);
		$error								= $database->errorDb("INSERT BBS",mysql_errno(),__FILE__,__LINE__);
		if(!empty($error)){ $mainClass->outputError($error); }

		# COMMENT COUNTUP
		$articleModel->updateArticleCommentCount($post_data['thread_id']);

		$redirect_url						= $page_path."exection/#exection";



	/***********************************************
	**
	**	UPDATE
	**
	************************************************/

	}elseif($post_data['purpose'] == 2){





	/***********************************************
	**
	**	DELETE
	**
	************************************************/

	}elseif($post_data['purpose'] == 3){



	}


	# CLOSE DATABASE
	$database->closeDb();


}


/******************************************
**
**	EXECTION
**
*******************************************/

if(empty($post_data['error'])){


	/******************************************
	**
	**	CACHE DELETE
	**
	*******************************************/

	$optionClass->deleteCache(SITE_DIRECTORY);


	/******************************************
	**
	**	EXECTION COMPLETE
	**
	*******************************************/

	# EXECTION COMPLETE
	$exection	= 1;

	# DIRECTORY REWRITE
	$directory	= "exection";

	# UNSET POST
	unset($_POST);

	# 今回はリダイレクトにしよう
	header("Location: ".$page_path."exection/#exection");
	exit();


}else{

	# CONNECT DATABASE MASTER
	$database->connectDb();

}








################################## FILE END #####################################
?>