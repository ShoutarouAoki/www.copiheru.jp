<?php
################################ FILE MANAGEMENT################################
##
##	index.php
##=============================================================================
##
##	■PAGE / 
##	INDEX PAGE
##
##=============================================================================
##
##	■MEANS / 
##	ここで全ての処理を行う。
##	--------------------------------------------
##	キャッシュがあればキャッシュを読み込み。
##	なければ処理。
##
##
##=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: AKITOSHI TAKAI
##	CREATE DATE : 2012/12/01
##	CREATER		:
##
##=============================================================================
##
##	■ REWRITE (改修履歴)
##
##
##
##
##
##
##
#################################### CONF ######################################


/************************************************
**
**	CONF FILE
**	---------------------------------------------
**	基本設定ファイル読み込み
**	必須項目
**
************************************************/

/** CONFIG PHP **/
require_once(dirname(__FILE__)."/../config/config.php");

######################### WEB DISPLAY MAINTENANCE / CHECK #######################

# MAINTENANCE ON
if(WEB_MAINTENANCE == "ON" || ALL_MAINTENANCE == "ON"){
	if(!defined("SYSTEM_CHECK")){
		include_once(DOCUMENT_ROOT_MAINTENANCE);
		exit();
	}
}

################################ BASIC MODELS ###################################


/************************************************
**
**	BASIC LIBRARY FILE
**	---------------------------------------------
**	BASIC LIBRARY CLASS FILE読み込み
**	必須項目
**
************************************************/

/** DATABASE LIBRARY **/
require_once(DOCUMENT_ROOT_DATABASE);

/** MAIN LIBRARY **/
require_once(DOCUMENT_ROOT_VENDORS."/MainClass.php");

/** DEVICE LIBRARY **/
require_once(DOCUMENT_ROOT_VENDORS."/DeviceClass.php");

/** SESSION LIBRARY **/
require_once(DOCUMENT_ROOT_VENDORS."/SessionClass.php");

/** HTML LIBRARY **/
require_once(DOCUMENT_ROOT_VENDORS."/HtmlClass.php");

/** OPTION LIBRARY **/
require_once(DOCUMENT_ROOT_VENDORS."/OptionClass.php");

/** AUTH LIBRARY **/
require_once(DOCUMENT_ROOT_VENDORS."/AuthClass.php");


################################ LIBRARY CLASS##################################


/************************************************
**
**	BASIC LIBRARY CLASS CALL
**	---------------------------------------------
**	PHP BASIC LIBRARY CLASS CALL
**	必須項目
**
************************************************/

# DATABASE CLASS
$database				= new Database();

# MAIN CLASS
$mainClass				= new MainClass();

# DEVICE CLASS
$deviceClass			= new DeviceClass();

# SESSION CLASS
$sessionClass			= new SessionClass();

# HTML CLASS
$htmlClass				= new HtmlClass();

# OPTION CLASS
$optionClass			= new OptionClass();

# AUTH CLASS
$authClass				= new AuthClass();


################################ DEVICE FUNCTION ################################


/************************************************
**
**	DEVICE
**	---------------------------------------------
**	ACCESS DEVICE CHECK
**	READING DIRECTORY SEPALATE
**
************************************************/

# GET DEVICE TYPE(CARRIER)
$device_type			= $deviceClass->getDeviceType();

# GET DEVICE FILE(ACCESS DIRECTORY)
$device_file			= $deviceClass->getIncludeDirectry();

# GET DEVICE NUMBER(ACCESS DEVICE)
$device_number			= $deviceClass->getDeviceNumber();

# GET OS NUMBER(ACCESS OS / ONLY SMARTPHONE)
$os_number				= $deviceClass->getOsNumber();

# GET iOS VERSION
$os_version				= $deviceClass->getOsVersion();

# DEFAULT DEVICE KEEP
$default_device			= $device_file;

# DEFAULT DEVICE KEEP
$default_number			= $device_number;

# DEFAULT OS KEEP
$default_os				= $os_number;

# SYSTEM TEST
if(defined("SYSTEM_CHECK") && $device_number == 1){
	$device_file		= DEBUG_VIEW;
	$device_number		= $deviceClass->getDeviceNumberByFile($device_file);
	$os_number			= $deviceClass->getOsNumberByFile($device_file,$default_os);
	$os_number			= 2;
}

if($device_file == "pc"){
	$device_file		= "smart";
	$device_number		= 2;
	$os_number			= 2;
}

# IE CHECK
$ie_check				= NULL;
if($default_device == "pc"){
	$browser			= $deviceClass->getUserBrowser();
	if($browser['browser'] == "ie"){
		$ie_check		= 1;
	}
}

################################# NOTICE THROUGH ################################

# REQUEST NOTICE ERROR THROUGH
$request				= $mainClass->getRequestData($_REQUEST);


################################# SESSION EXIST #################################


/************************************************
**
**	SESSION
**	---------------------------------------------
**	SESSION受け渡し
**	$session_exist	-> 1;
**
************************************************/

# SESSION EXIST
$session_exist			= $sessionClass->getSessionExist($device_type,$request['device']);

# AUTH SESSION
$account				= $sessionClass->checkAuthSession($device_type,$request['ai'],$request['ui'],$request['up']);

############################# DIRECTORY DISTINCTION #############################


/************************************************
**
**	DIRECTORY
**	---------------------------------------------
**	DIRECTORY ROOT PATH CHECK
**	READING DIRECTORY PATH CHECK
**
************************************************/

# DIRECTORY SCRIPT FILE
$directory				= $mainClass->getControllerDirectory($request['directory'],$device_file);

################################ SESSION CHECK ##################################

# USER SESSION 情報がなかったら強制的にINDEXへ
if(!isset($login_through_array[$directory]) && empty($_SESSION['set'])){
	// $directory			= "error";
	$directory			= "index";
	// $error_no						= 1;
}

################################# SETTING DATA ##################################


/************************************************
**
**	SETTING DATA
**	---------------------------------------------
**	DEFAULT DATA SETTING
**
************************************************/

# VALUES
$values					= $mainClass->getHttpValues($request['values']);

# POST
$cache_post				= $mainClass->getPostRoot($_POST);

# GET
$cache_get				= $mainClass->getPostRoot($_GET);

# DIRECTORY CATEGORY
if(isset($web_category_array[$directory])){
	$category			= $web_category_array[$directory];
}

# PAGE NUMBER
if(!empty($values[0])){
	$access_page		= $values[0];
}else{
	$access_page		= "index";
}

if(isset($access_page_array[$access_page])){
	$page_number		= $access_page_array[$access_page];
}

# キャンペーンはpage numberが存在しない為
if($directory == "campaign"){
	$page_number		= 1;
}

# CHECK
$mainClass->debug("CATEGORY --- ".$category."<br />PAGE --- ".$page_number);


################################# MAIN MODELS ###################################


/************************************************
**
**	MAIN MODEL FILE
**	---------------------------------------------
**	MAIN MODEL CLASS FILE READING
**	必須項目
**
************************************************/

/** SITEINFO MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/SiteinfoModel.php");

/** MEMBER MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MemberModel.php");

/** MAIL MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MailModel.php");

/** EVENT MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/EventModel.php");

/** ITEMBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemboxModel.php");

/** PRESENTBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PresentboxModel.php");

/** PAGEVIEW MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PageviewModel.php");

require_once(DOCUMENT_ROOT_STATIC_DB);
Libs\Database::connectDb();

################################# MODEL CLASS ###################################


/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# SITEINFO MODEL
$siteinfoModel						= new SiteinfoModel($database,$mainClass);

# MEMBER MODEL
$memberModel						= new MemberModel($database,$mainClass);

# MAIL MODEL
$mailModel							= new MailModel($database,$mainClass);

# EVENT MODEL
$eventModel							= new EventModel($database,$mainClass);

# ITEMBOX MODEL
$itemboxModel						= new ItemboxModel($database,$mainClass);

# PRESENTBOX MODEL
$presentboxModel					= new PresentboxModel($database,$mainClass);

# PAGEVIEW MODEL
$pageviewModel						= new PageviewModel($database,$mainClass);


################################# PAGE ROOT #####################################

# PAGE ROOT
$page_path							= "/".$directory."/";

################################# CONNECT DB ####################################

# CONNECT DATABASE
$database->connectDb();

################################# SITE INFOS ####################################

$site_data							= $siteinfoModel->getSiteinfoData();

# MAINTENANCE ON
if(!empty($site_data) && $site_data['maintenance'] == 1){

	if(!defined("SYSTEM_CHECK")){

		# INCLUDE MAINTENANCE HTML
		include_once(DOCUMENT_ROOT_MAINTENANCE);

		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();

		# EXIT
		exit();

	}

}

################################ CHECK TOKEN ####################################
if(!$sessionClass->checkToken($request['token'])){
	$token_check					= NULL;
	if(isset($_REQUEST['purpose'])){ $_REQUEST['purpose'] = NULL; $token_check = 1; }
	if(isset($_POST['purpose'])){ $_POST['purpose'] = NULL; $token_check = 1; }
	if(!empty($request['purpose'])){ $request['purpose'] = NULL; $token_check = 1; }
	if(!empty($token_check)){ $token_error = 1; }
}

################################### USER DATA ###################################

# LOGIN CHECK -> 常時ログイン認証
if(!isset($login_through_array[$directory])){
	$members_data					= $memberModel->checkMemberCertify($account);
}

################################ FIXED SCRIPT ###################################

/************************************************
**
**	常時処理
**	---------------------------------------------
**	どのページでも常に処理するものがあればここに
**	controller処理前
**
************************************************/

# ログインエラー
if(isset($members_data['error'])){

	$directory						= "error";

	$error_no						= $members_data['error'];

	if(!empty($members_data['message'])){
		$error_message				= $members_data['message'];
	}

# ログイン後
}elseif(!empty($members_data['id'])){

	# 未読メールカウント
	$no_read_count					= $mailModel->getNoReadCount($members_data['id']);

	# ヘッダー用
	$newarrival_mail_count			= $no_read_count;
	if($newarrival_mail_count > 99){
		$newarrival_mail_count		= 99;
	}

	# 開催イベントチェック
	$event_data						= $eventModel->getEventData($members_data);


}

############################### PAGE VIEW COUNT #################################

# LOGIN CHECK -> 常時ログイン認証
if(!isset($web_count_out[$directory]) && !empty($category) && !empty($page_number)){
	$pageviewModel->countPageview(SITE_CD,$category,$page_number,$default_number,$default_os);
}

################################ SCRIPT FILE ####################################


/************************************************
**
**	REQUIRE SCRIPT FILE
**	---------------------------------------------
**	PHP SCRIPT FILE READING
**
************************************************/


# REQUIRE COMPONENT FILE
//require_once(DOCUMENT_ROOT_COMPONENTS."/".$device_file."Component.php");
require_once(DOCUMENT_ROOT_COMPONENTS."/".$default_device."Component.php");

# REQUIRE MAIN CONTROLLER FILE
require_once(DOCUMENT_ROOT_CONTROLLERS."/".$directory."Controller.php");


################################ FIXED SCRIPT ###################################

/************************************************
**
**	常時処理
**	---------------------------------------------
**	どのページでも常に処理するものがあればここに
**	controller処理後
**
************************************************/

# ログイン後
if(!empty($members_data['id'])){

	# プレゼントのカウント
	$present_count					= $presentboxModel->getUserPresentboxCount($members_data['id']);
	$newarrival_present_count		= $present_count;

	# ヘッダー用
	if($newarrival_present_count > 99){
		$newarrival_present_count	= 99;
	}

}

################################## CLOSE DB #####################################

# CLOSE DATABASE
$database->closeDb();
$database->closeStmt();

################################## SET TOKEN ####################################

$sessionClass->setToken();

################################# TOKEN ERROR ###################################

if(isset($token_error)){
	$post_data['error']		= "不正なアクセスです。";
}

######################### CHECK SYSTEM PROCESS TIME END #########################

$system_process_time		= $mainClass->getMicroEndTime($system_start_time,$system_start_secd);

#################### CHECK WEB HTML DISPLAY PROCESS TIME START ##################

$display_start_time			= $mainClass->getStartTime();

################################## OB START #####################################

# OB START HEADER
$htmlClass->obStartHeader($device_file,$device_type,$deviceClass->getIncludeDirectry(),$account_data);

############################## HTML HEADER / BODY ###############################


/************************************************
**
**	INCLUED VIEWS
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/

# INCLUDE VIEW FILE
$view_directory				= $mainClass->getViewDirectory($directory,$data['page'],$device_file);
include_once(DOCUMENT_ROOT_VIEWS."/".$device_file."/templates/layout.inc");

//$view_directory				= $mainClass->getViewDirectory($directory,$data['page'],$default_device);
//include_once(DOCUMENT_ROOT_VIEWS."/".$default_device."/templates/layout.inc");


#################### CHECK WEB HTML DISPLAY PROCESS TIME START ##################

$display_process_time		= $mainClass->getMicroEndTime($display_start_time['time'],$display_start_time['secd']);

################################# SYSTEM DEBUG ##################################

# DEBUG -> SYSTEM PROCESS TIME
$mainClass->debug($system_process_time." sec","SYSTEM PROCESS TIME");

# DEBUG -> WEB HTML DISPLAY PROCESS TIME
$mainClass->debug($display_process_time." sec","WEB HTML DISPLAY PROCESS TIME");

# CONTROLLER DIRECTORY
$mainClass->debug("CONTROLLER DIRECTORY : ".DOCUMENT_ROOT_CONTROLLERS."/".$directory."Controller.php");

# VIEW DIRECTORY
$mainClass->debug("VIEW DIRECTORY : ".$view_directory);

# PAGE
$mainClass->debug("PAGE : ".$data['page']);

# BONUS TIME
$mainClass->debug("BONUS DATE TIME : ".BONUS_DATE_TIME."<br />BONUS DATE : ".BONUS_DATE."<br />BONUS TIME : ".BONUS_TIME);

# ERROR
if(!empty($error)){
	$mainClass->debug("ERROR NO : ".$error."<br />ERROR MESSAGE : ".$errormessage);
}

# HOST
$mainClass->debug("THIS SERVER : ".SERVER_HOST);

# SYSTEM DEBUG
$mainClass->outputDebugSystem();

################################# HTML FOOTER ###################################

# HTML FOOTER
$htmlClass->htmlFooter();

################################ OB END FLUSH ###################################

ob_end_flush();

################################## FILE END #####################################
# EXIT
exit();

#################################################################################
?>