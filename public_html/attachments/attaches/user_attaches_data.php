<?php
################################## ファイル概要 #################################
##
##	user_attaches_data.php
##	----------------------------------------------------------------------------
##	ユーザーメディアデータ
##	----------------------------------------------------------------------------
##
##################################### CONF ######################################

/** CONF FILE **/
require_once(dirname(__FILE__)."/../CONF/config.php");

/** CLASS FILE **/
require_once(dirname(__FILE__).'/../class/database.php');
require_once(dirname(__FILE__).'/../class/main.php');
require_once(dirname(__FILE__).'/../class/html_class.php');
require_once(dirname(__FILE__).'/../class/members.php');
require_once(dirname(__FILE__).'/../class/images.php');

################################### CERTIFY #####################################

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/**  パスワードの認証 **/
$adminMain	= new adminMain($db);
$certify	= $adminMain->adminCertify($_REQUEST['op_id'],$_REQUEST['sec']);

################################## HTML CLASS ###################################

$html_class	= new htmlClass();

################################ DATABASE CONNECT ###############################

$db = new accessDb(0);
$db->connectDb();

################################## MAIN SQL #####################################

$images	 	= new images();

/*********************************************************
**
**	ユーザーメディア抽出
**
*********************************************************/

$attaches_table		 = "attaches";
$attaches_select	 = "*";
$attaches_where		.= "site_cd = '".$_REQUEST['site_cd']."' ";
$attaches_where		.= "AND id = '".$_REQUEST['attaches_id']."' ";
$attaches_order		 = "";
$attaches_limit		 = "1";
$attaches_rtn		= $db->selectDb($attaches_table,$attaches_select,$attaches_where,$attaches_order,$attaches_limit);
$db->errorDb("",$db->errno,__FILE__,__LINE__);

$attaches_data = $db->fetchAssoc($attaches_rtn);

# IMAGE
if($attaches_data['category'] == 1){
	$size	= 240;
	list($width,$height,$type,$attr)	= getimagesize("/var/www/htdocs/img/attaches/".$attaches_data['attached']);
	if(empty($width)){
		$disp_attaches	= "<img src=\"".IMAGE_HTTP."attaches/".$attaches_data['attached']."\" />";
	}else{
		$height			= round($height * $size / $width);
		$disp_attaches	= "<img src=\"".IMAGE_HTTP."attaches/".$attaches_data['attached']."\" width=\"".$size."\" height=\"".$height."\" />";
	}
# MOVIE
}else{
	$movie_data		= $images->getPreMovieFile($attaches_data['attached']);
	$disp_attaches	= $movie_data['display'];
}

$disp_contents	.= "<div align=\"center\">\n";
$disp_contents	.= $disp_attaches;
$disp_contents	.= "</div>\n";



################################ DATABASE CLOSE #################################

$db->closeDb();

################################# HTML HEADER ###################################

$html_class->htmlHeader("sub",$_REQUEST['site_cd']);

############################### REQUIRE INC FILE ################################

# VIEWS
print($disp_contents);

################################# HTML FOOTER ###################################

$html_class->htmlFooter();

##################################### END #######################################
?>
