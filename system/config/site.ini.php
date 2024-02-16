<?php
################################ FILE MANAGEMENT ################################
##
##	site.ini.php
##	=============================================================================
##
##	■PAGE / 
##	SITE ROOT CONFIG
##	DEFAULT SITE ROOT SETTING
##
##	=============================================================================
##
##	■MEANS / 
##	ROOT内サイト基本【定数】設定
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
#################################################################################



/***********************************************************
 **
 **	SERVER SETTING
 **	-------------------------------------------------------
 **	DEFINE SERVER
 **	-------------------------------------------------------
 **	SETTING SERVER
 **
 ***********************************************************/

# BROWSE
define("USER_BROWSE",                        $_SERVER['HTTP_USER_AGENT']);

# USER AGENT
define("USER_AGENT",                        $_SERVER['HTTP_USER_AGENT']);

# SCRIPT FILE
define("SCRIPT_FILE",                        $_SERVER['SCRIPT_FILENAME']);

# REQUEST URI
define("URI_REQUEST",                        $_SERVER['REQUEST_URI']);

# REQUEST URI
define("THIS_DOCUMENT",                        $_SERVER['DOCUMENT_ROOT']);

# REMOTE ADDR / ロードバランサのアクセスだとロードバランサのグローバルIPになる為別途取得
define("REMOTE_ADDR",                        $_SERVER['REMOTE_ADDR']);

# REMOTE HOST
define("REMOTE_HOST",                        $_SERVER['REMOTE_HOST']);

# REQUEST METHOD
define("REQUEST_METHOD",                    $_SERVER['REQUEST_METHOD']);

# SERVER HOST
define("SERVER_HOST",                        $_SERVER['SERVER_ADDR']);

# HTTP REFERER
define("HTTP_REFERER",                        $_SERVER['HTTP_REFERER']);

# HTTP HOST
define("HTTP_HOST",                            $_SERVER['HTTP_HOST']);

# HTTP HOST
define("THIS_HOST",                            "https://" . HTTP_HOST . URI_REQUEST);



/***********************************************************
 **
 **	SERVER SETTING
 **	-------------------------------------------------------
 **	DEFINE SERVER
 **	-------------------------------------------------------
 **	SETTING ACCESS IP
 **
 ***********************************************************/

# HEADERS
//$headers									= getallheaders();
//define("REMOTE_ADDR",						$headers['X-Cluster-Client-Ip']);



/***********************************************************
 **
 **	DOCUMENT ROOT SETTING
 **	-------------------------------------------------------
 **	DEFINE DOCUMENT ROOT
 **	-------------------------------------------------------
 **	SERVER ROOT
 **
 ***********************************************************/

# DEFAULT DOMAIN
$defaultDomain                                = "copiheru.higatest.com";

# IMAGE DOMAIN
$imageDomain                                  = "copiheru.higatest.com";

# DOCUMENT ROOT
$documentRoot                                 = "/var/www/htdocs";



/***********************************************************
 **
 **	DOMAIN SETTING
 **	-------------------------------------------------------
 **	DEFINE HTTP
 **	-------------------------------------------------------
 **	APP OFFICIAL
 **
 ***********************************************************/

# DEFAULT DOMAIN
define("DEFAULT_DOMAIN",                    $defaultDomain);

# OFFICIAL DOMAIN
define("HTTP_DEFAULT",                        "https://" . DEFAULT_DOMAIN);

# OFFICIAL DOMAIN
define("HTTP_DOMAIN",                        "https://" . DEFAULT_DOMAIN);

# IMAGE DOMAIN / WEB サーバー内にぶら下げたのでドメインはなし
//define("IMAGE_DOMAIN",                        "/attachments");

// IMAGE DOMAIN / copiheru内に変更
define("IMAGE_DOMAIN",						"https://".$imageDomain."/attachments");

/***********************************************************
 **
 **	DOMAIN SETTING
 **	-------------------------------------------------------
 **	DEFINE HTTP
 **	-------------------------------------------------------
 **	APP SETTLEMENT DOMAIN
 **
 ***********************************************************/

# DOMAIN IMAGE
define("HTTP_SETTLEMENT",                     HTTP_DOMAIN . "/settlement");



/***********************************************************
 **
 **	DOMAIN SETTING
 **	-------------------------------------------------------
 **	DEFINE HTTP
 **	-------------------------------------------------------
 **	APP IMAGE DOMAIN
 **
 ***********************************************************/

# DOMAIN IMAGE
define("HTTP_IMAGE",                         IMAGE_DOMAIN . "/img");

# DOMAIN IMAGE / ATTACHES
define("HTTP_ATTACHES",                        IMAGE_DOMAIN . "/img/attaches");

# DOMAIN IMAGE / ITEM
define("HTTP_ITEM_IMAGE",                    IMAGE_DOMAIN . "/img/item");

# DOMAIN IMAGE / BANNER
define("HTTP_BANNER_IMAGE",                    IMAGE_DOMAIN . "/img/banner");

# DOMAIN IMAGE / WEB
define("HTTP_WEB_IMAGE",                    IMAGE_DOMAIN . "/img/web");

# DOMAIN IMAGE / EVENT
define("HTTP_EVENT_IMAGE",                    IMAGE_DOMAIN . "/img/event");

# DOMAIN IMAGE / CAMPAIGN
define("HTTP_CAMPAIGN_IMAGE",                IMAGE_DOMAIN . "/img/campaign");

# DOMAIN FILE
define("HTTP_FILE",                         HTTP_DEFAULT . "/files");

# DOMAIN FLASH
define("HTTP_FLASH",                         HTTP_FILE . "/flash");



/***********************************************************
 **
 **	DOMAIN SETTING
 **	-------------------------------------------------------
 **	DEFINE ROOT
 **	-------------------------------------------------------
 **	APP IMAGE ROOT
 **
 ***********************************************************/

# PATH IMAGES
define("PATH_IMAGES",                        "/images");

# PATH FILES
define("PATH_FILES",                         "/files");



/***********************************************************
 **
 **	MAIL DOMAIN
 **	-------------------------------------------------------
 **	DEFINE MAIL
 **	-------------------------------------------------------
 **	APP OFFICIAL
 **
 ***********************************************************/

# MAIL DOMAIN
define("MAIL_DOMAIN",                        $defaultDomain);

# MAIL INFO
define("MAIL_INFO",                            "info@" . MAIL_DOMAIN);

# MAIL SUPPORT
define("MAIL_SUPPORT",                        "info@" . MAIL_DOMAIN);

# MAIL SYSTEM
define("MAIL_SYSTEM",                        "eikoshi@k-arat.co.jp");



/***********************************************************
 **
 **	DOCUMENT ROOT SETTING
 **	-------------------------------------------------------
 **	DEFINE DOCUMENT ROOT
 **	-------------------------------------------------------
 **	SERVER ROOT
 **
 ***********************************************************/

# SERVER ROOT
define("DOCUMENT_ROOT_DIRECTORY",            $documentRoot . "/www.copiheru.jp");



/***********************************************************
 **
 **	DOCUMENT ROOT SETTING
 **	-------------------------------------------------------
 **	DEFINE DOCUMENT ROOT
 **	-------------------------------------------------------
 **	DEFAULT DIRECTORY
 **
 ***********************************************************/

# DOCUMENT HOME ROOT
define("DOCUMENT_ROOT_LOCAL",                DOCUMENT_ROOT_DIRECTORY . "/public_html");

# DOCUMENT SYSTEM ROOT
define("DOCUMENT_ROOT_SYSTEM",                DOCUMENT_ROOT_DIRECTORY . "/system");



/***********************************************************
 **
 **	DOCUMENT ROOT SETTING
 **	-------------------------------------------------------
 **	DEFINE DOCUMENT ROOT
 **	-------------------------------------------------------
 **	WEB DIRECTORY
 **
 ***********************************************************/

# DOCUMENT ADMIN ROOT
define("DOCUMENT_ROOT_WEB",                    DOCUMENT_ROOT_LOCAL);

# DOCUMENT IMAGES
define("DOCUMENT_ROOT_IMAGES",                DOCUMENT_ROOT_LOCAL . "/images");



/***********************************************************
 **
 **	DOCUMENT ROOT SETTING
 **	-------------------------------------------------------
 **	DEFINE DOCUMENT ROOT
 **	-------------------------------------------------------
 **	SYSTEM DIRECTORY
 **
 ***********************************************************/

# DOCUMENT CONSOLE ROOT
define("DOCUMENT_ROOT_APPLICATIONS",        DOCUMENT_ROOT_SYSTEM . "/applications");

# DOCUMENT LIBS
define("DOCUMENT_ROOT_LIBS",                DOCUMENT_ROOT_SYSTEM . "/libs");

# SYSTEM PLUGINS
define("DOCUMENT_SYSTEM_PLUGINS",            DOCUMENT_ROOT_SYSTEM . "/plugins");

# SYSTEM VENDORS
define("DOCUMENT_SYSTEM_VENDORS",            DOCUMENT_ROOT_SYSTEM . "/vendors");

# SYSTEM LOGS
define("DOCUMENT_SYSTEM_LOGS",                DOCUMENT_ROOT_SYSTEM . "/logs");

# SYSTEM FILES
define("DOCUMENT_SYSTEM_FILES",                DOCUMENT_ROOT_SYSTEM . "/files");

# DOCUMENT TEMPORARY
define("DOCUMENT_SYSTEM_TEMPORARYS",        DOCUMENT_ROOT_SYSTEM . "/tmp");

# SYSTEM SHELLS
define("DOCUMENT_SYSTEM_SHELLS",            DOCUMENT_SYSTEM_VENDORS . "/shells");



/***********************************************************
 **
 **	DOCUMENT ROOT SETTING
 **	-------------------------------------------------------
 **	DEFINE DOCUMENT ROOT
 **	-------------------------------------------------------
 **	APPLICATIONS ROOT
 **
 ***********************************************************/

# ROOT MODELS
define("DOCUMENT_ROOT_MODELS",                DOCUMENT_ROOT_APPLICATIONS . "/models");

# ROOT SERVICES
define("DOCUMENT_ROOT_SERVICES",                DOCUMENT_ROOT_APPLICATIONS . "/services");

# ROOT CONTROLLERS
define("DOCUMENT_ROOT_CONTROLLERS",            DOCUMENT_ROOT_APPLICATIONS . "/controllers");

# ROOT VIEWS
define("DOCUMENT_ROOT_VIEWS",                DOCUMENT_ROOT_APPLICATIONS . "/views");

# ROOT VENDORS
define("DOCUMENT_ROOT_VENDORS",                DOCUMENT_ROOT_APPLICATIONS . "/vendors");

# ROOT COMPONENT
define("DOCUMENT_ROOT_COMPONENTS",            DOCUMENT_ROOT_CONTROLLERS . "/components");


# ROOT SITE WEB
define("DOCUMENT_ROOT_SITE_WEB",            DOCUMENT_ROOT_WEB);

# ROOT SITE IMAGE
define("DOCUMENT_ROOT_SITE_IMAGES",            DOCUMENT_ROOT_SITE_WEB . "/images");

# WEB ROOT
define("WEB_ROOT",                            "");

# SITE ROOT
define("SITE_ROOT",                            WEB_ROOT);

# IMAGE ROOT
define("IMAGE_ROOT",                        SITE_ROOT . "/images");

# ROOT DIRECTORY
define("SITE_DOMAIN",                        HTTP_DOMAIN . SITE_ROOT);


/***********************************************************
 **
 **	DATABASE SYSTEM
 **	-------------------------------------------------------
 **	DEFINE DATABASE SYSTEM FILE
 **	-------------------------------------------------------
 **	DATABASE MODEL
 **
 ***********************************************************/

# SYSTEM DATABASES
define("DOCUMENT_ROOT_DATABASE",            DOCUMENT_ROOT_LIBS . "/db/Database.php");

# SYSTEM DATABASES
define("DOCUMENT_ROOT_STATIC_DB",            DOCUMENT_ROOT_LIBS . "/db/database_.php");

/***********************************************************
 **
 **	MAINTENANCE SYSTEM
 **	-------------------------------------------------------
 **	DEFINE MAINTENANCE SYSTEM FILE
 **	-------------------------------------------------------
 **	MAINTENANCE HTML FILE
 **
 ***********************************************************/

# SYSTEM MAINTENANCE
define("DOCUMENT_ROOT_MAINTENANCE",            DOCUMENT_ROOT_LIBS . "/maintenance/index.html");



/***********************************************************
 **
 **	CACHE SYSTEM
 **	-------------------------------------------------------
 **	DEFINE CACHE SYSTEM FILE
 **	-------------------------------------------------------
 **	CACHE LITE
 **
 ***********************************************************/

# CACHE LITE
define("DOCUMENT_CACHE_LITE",                DOCUMENT_SYSTEM_PLUGINS . "/Cache/Lite.php");


# CACHE DIRECTORY
define("DOCUMENT_ROOT_CACHE",                DOCUMENT_SYSTEM_TEMPORARYS . "/cache/");



/***********************************************************
 **
 **	LOGS SYSTEM
 **	-------------------------------------------------------
 **	DEFINE ACCESS LOG SYSTEM FILE
 **	-------------------------------------------------------
 **	LOG
 **
 ***********************************************************/

# VISITOR LOG DIRECTORY
define("DOCUMENT_ROOT_VISITORLOG",            DOCUMENT_SYSTEM_LOGS . "/visitor/");

# ACCESS LOG DIRECTORY
define("DOCUMENT_ROOT_ACCESSLOG",            DOCUMENT_SYSTEM_LOGS . "/access/");

# PAGEVIEW LOG DIRECTORY
define("DOCUMENT_ROOT_PAGEVIEWLOG",            DOCUMENT_SYSTEM_LOGS . "/pageview/");

# REGIST LOG DIRECTORY
define("DOCUMENT_ROOT_REGISTLOG",            DOCUMENT_SYSTEM_LOGS . "/regist/");

# ERROR LOG DIRECTORY
define("DOCUMENT_ROOT_ERRORLOG",            DOCUMENT_SYSTEM_LOGS . "/error/");

# STATUS LOG DIRECTORY
define("DOCUMENT_ROOT_STATUSLOG",            DOCUMENT_SYSTEM_LOGS . "/status/");



/***********************************************************
 **
 **	DOCUMENT USER ROOT SETTING
 **	-------------------------------------------------------
 **	DEFINE DOCUMENT USER ROOT
 **	-------------------------------------------------------
 **	USER FILE
 **
 ***********************************************************/

# USER FILES
define("DOCUMENT_USER_FILES",                DOCUMENT_ROOT_LOCAL . "/files");

# USER FLASH
define("DOCUMENT_USER_FLASH",                DOCUMENT_USER_FILES . "/flash");

# USER IMAGE
define("DOCUMENT_USER_IMAGE",                DOCUMENT_ROOT_LOCAL . "/images");



/***********************************************************
 **
 **	CHARSET SETTING
 **	-------------------------------------------------------
 **	DEFINE CHARSET
 **	-------------------------------------------------------
 **	SETTING CHARSET
 **
 ***********************************************************/

# SITE CHARSE
define("SITE_CHARSET",                        "UTF-8");

# ENCODING CHARSE
define("ENCODING_CHARSET",                    "UTF-8");

# OUT PUT CHARSET
define("OUTPUT_CHARSET",                    "SJIS");



/***********************************************************
 **
 **	ID/PASS SETTING
 **	-------------------------------------------------------
 **	DEFINE BASIC AUTHOR
 **
 ***********************************************************/

# LOGIN ID
define("LOGIN_ID",                            "kidumax");

# LOGIN PASS
define("LOGIN_PASS",                        "oppabu1919kuma");

# BASIC ID
define("BASIC_USER",                        "kidumax");

# BASIC PASS
define("BASIC_PASS",                        "oppabu1919kuma");



/***********************************************************
 **
 **	YEAR SETTING
 **	-------------------------------------------------------
 **	DEFINE YEAR
 **	-------------------------------------------------------
 **	SETTING YEAR
 **
 ***********************************************************/

# SITE OPEN YEAR
define("SITE_OPEN_YEAR",                    "2018");

# SITE YEAR
define("SITE_YEAR_SETTING",                    "2");



/***********************************************************
 **
 **	IMAGE THUMBNAIL SETTING
 **	-------------------------------------------------------
 **	DEFINE THUMBNAIL
 **	-------------------------------------------------------
 **	IMAGE THUMBNAIL
 **
 ***********************************************************/

# SITE CHARSE
define("THUMBNAIL_WIDTH",                    "100");



/***********************************************************
 **
 **	SYSTEM / ADMIN / WEB MAINTENANCE SETTING
 **	-------------------------------------------------------
 **	DEFINE SYSTEM / ADMIN / WEB MAINTENANCE
 **	-------------------------------------------------------
 **	SETTING SYSTEM / ADMIN / WEB MAINTENANCE
 **
 ***********************************************************/


# ALL MAINTENANCE
define("ALL_MAINTENANCE",                    "OFF");

# WEB MAINTENANCE
define("WEB_MAINTENANCE",                    "OFF");



/***********************************************************
 **
 **	SYSTEM / ADMIN / WEB SESSION SETTING
 **	-------------------------------------------------------
 **	DEFINE SYSTEM / ADMIN / WEB SESSION
 **	-------------------------------------------------------
 **	SETTING SYSTEM / ADMIN / WEB SESSION
 **
 ***********************************************************/


# SYSTEM SESSION
define("SYSTEM_SESSION",                    "ON");

# ADMIN SESSION
define("ADMIN_SESSION",                        "ON");

# WEB SESSION
define("WEB_SESSION",                        "ON");



/***********************************************************
 **
 **	OPEN DATE TIME
 **
 ***********************************************************/

# OPEN DATE TIME
define("OPEN_DATE_TIME",                    "20180701000000");
