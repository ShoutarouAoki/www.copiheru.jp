<?php
################################## ファイル概要 #################################
##
##	user_attaches_certify.php
##	----------------------------------------------------------------------------
##	ユーザーメディア認証処理
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
require_once(dirname(__FILE__).'/../class/pointsets.php');
require_once(dirname(__FILE__).'/../class/points.php');
require_once(dirname(__FILE__).'/../class/campaignsets.php');

################################### CERTIFY #####################################

/**  パスワードの認証 **/
$adminMain	= new adminMain($db);
$certify	= $adminMain->adminCertify($_REQUEST['op_id'],$_REQUEST['sec']);

################################## HTML CLASS ###################################

$html_class	= new htmlClass();

################################### PAGE TYPE ###################################

if(!$_REQUEST['page_type']){
	$page_type = 1;
}else{
	$page_type = $_REQUEST['page_type'];
}

# ERROR
if(!$_REQUEST['attaches_id']){
	$err_msg	= "対象者を選択して下さい";
}

if($err_msg){
	$html_class->outputError($err_msg);
	exit;
}

################################## IMAGE SIZE ###################################

# IMAGE SETTING ( FILE NAME : FILE SIZE : MAX WIDTH ) / IMAGES NEW
$images	 	= new images();
//$image_data	= $images->makeImageData("attaches","500000","240");
$image_data	= $images->makeImageData("attaches","3000000","600");


$movie_max_size	= 50000000;

if($_REQUEST['certify']){
	$purpose	= 1;
	$page_title	= "認証";
}elseif($_REQUEST['delete']){
	$purpose	= 2;
	$page_title	= "認証拒否";
}

################################ DATABASE CONNECT ###############################

$db = new accessDb(0);
$db->connectDb();

################################## MAIN SQL #####################################

$attaches_id	= $_REQUEST['attaches_id'];

# POINTS
$points			= new points();

# USER DATA
$members		= new members();
$user_column	= "id,nickname,sex,reg_date,site_cd,op_id,chikuwa,owner_id,status,domain_flg,pay_count,point,s_point,ad_code";

$count	= count($attaches_id);
for($i=0;$i<$count;$i++){

	$media_id	= $attaches_id[$i];

	/**********************************************
	**
	**	ATTACHES
	**
	***********************************************/

	$table	 = "attaches";
	$column	 = "*";
	$where	 = "id = '".$media_id."' ";

	$rtn	 = $db->selectDb($table,$column,$where,$order,$limit);
	$db->errorDb("",$db->errno,__FILE__,__LINE__);
	$data  	 = $db->fetchAssoc($rtn);


	/**********************************************
	**
	**	USER DATA
	**
	***********************************************/

	$user_where		= "site_cd = ".$data['site_cd']." AND id = ".$data['user_id'];
	$user_data		= $members->getUser($user_column,$user_where,$info_column);


	/**********************************************
	**
	**	ATTACHES DELETE
	**
	***********************************************/

	if($_REQUEST['delete']){

		# UPDATE STATUS
		$update['status']	= 9;
		$where	 = "id = '".$media_id."' ";
		$db->updateDb("attaches",$update,$where);
		$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
		if($db_err){ print($db_err); exit; }

		#$result	= $images->deleteAttacheData($data['attached'],$data['category'],1);

		$delete_user	.= "【ID : ".$user_data['id']."】".$user_data['nickname']."<br />";

		$post_hidden	.= "<input type=\"hidden\" name=\"user_id[]\" value=\"".$user_data['id']."\" />\n";

		continue;

	}


	/**********************************************
	**
	**	POINT 消費計算
	**
	***********************************************/

	if($user_data['pay_count'] != 0){
		$point_data['pay_flg']	= 1;
	}else{
		$point_data['pay_flg']	= 2;
	}

	# PHOTO
	if($data['category'] == 1){
		$point_no_id	= 12;
	# MOVIE
	}elseif($data['category'] == 2){
		$point_no_id	= 13;
	}


	# 消費POINT
	if($user_data['status'] == 1){

		# CAMPAIGN CHECK
		$campaignsets	= new campaignsets($db,$html_class,$user_data['site_cd'],NULL);
		$campaign_id	= $campaignsets->checkUserCampaign($user_data['id'],"2");

		$point_data['domain_flg']	= $user_data['domain_flg'];
		$point_data['user_id']		= $user_data['id'];
		$point_data['sex']			= $user_data['sex'];
		$point_data['campaign_id']	= $campaign_id;

		# POINT FRAME
		$pointsets	= new pointsets($db,$html_class,$user_data['site_cd'],$point_data);
		$use_point	= $pointsets->checkUserPointUse($point_no_id,$column);

	}else{
		$use_point	= 0;
	}

	# USER POINT
	$user_point	= $user_data['point'] + $user_data['s_point'];

	if($user_point < $use_point){
		$ng_user		.= "【ID : ".$user_data['id']."】".$user_data['nickname']."<br />";
		$post_hidden	.= "<input type=\"hidden\" name=\"user_id[]\" value=\"".$user_data['id']."@@@\" />\n";
		continue;
	}


	/**********************************************
	**
	**	PHOTO CERTIFY
	**
	***********************************************/

	if($data['category'] == 1){

		$photos	= $images->makeImageCertify($data['attached'],$image_data);

		if($photos){

			# UPDATE STATUS
			$update['status']	= 1;
			$db->updateDb($table,$update,$where);
			$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
			if($db_err){ print($db_err); exit; }

			$result	= "OK";

		}else{

			$out_user		.= "【ID : ".$user_data['id']."】".$user_data['nickname']."<br />";
			$post_hidden	.= "<input type=\"hidden\" name=\"user_id[]\" value=\"".$user_data['id']."@@@\" />\n";
			continue;

		}

	/**********************************************
	**
	**	MOVIE CERTIFY
	**
	***********************************************/

	}elseif($data['category'] == 2){

		# MOVIE CREATE -> FFMPEG
		$movies	= $images->makeMovieCertify($data['attached'],$movie_max_size,$user_data['id']);

		if($movies){

			# UPDATE STATUS
			$update['status']	= 1;
			$db->updateDb($table,$update,$where);
			$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
			if($db_err){ print($db_err); exit; }

			$result	= "OK";

		}else{

			$out_user		.= "【ID : ".$user_data['id']."】".$user_data['nickname']."<br />";
			$post_hidden	.= "<input type=\"hidden\" name=\"user_id[]\" value=\"".$user_data['id']."@@@\" />\n";
			continue;

		}

	}


	/**********************************************
	**
	**	POINT 消費
	**
	***********************************************/

	if($result){

		$point['user_id']		= $user_data['id'];
		$point['sex']			= $user_data['sex'];
		$point['point']			= $user_data['point'];
		$point['s_point']		= $user_data['s_point'];
		$point['domain_flg']	= $user_data['domain_flg'];
		$point['ad_code']		= $user_data['ad_code'];

		if($user_data['status'] == 2){
			$point['pay_flg'] = 3;
		}elseif($user_data['pay_count'] == 0 || $user_data['status'] == 3){
			$point['pay_flg'] = 2;
		}else{
			$point['pay_flg'] = 1;
		}

		$point_result	= $points->userPointUse($user_data['site_cd'],$use_point,$point_no_id,$point);

		if(!$point_result){
			$ng_user		.= "【ID : ".$user_data['id']."】".$user_data['nickname']."<br />";
			$post_hidden	.= "<input type=\"hidden\" name=\"user_id[]\" value=\"".$user_data['id']."@@@\" />\n";
			continue;
		}

		$success_user	.= "【ID : ".$user_data['id']."】".$user_data['nickname']."<br />";
		$post_hidden	.= "<input type=\"hidden\" name=\"user_id[]\" value=\"".$user_data['id']."\" />\n";

	}



}


################################ DATABASE CLOSE #################################

$db->closeDb();

################################# HTML HEADER ###################################

$html_class->htmlHeader("sub",$_REQUEST['site_cd']);

############################### REQUIRE INC FILE ################################

# SUCCESS
if($success_user){
$end_msg	 = "<span class=\"style_blue\">\n".$success_user."</span>\nのデータ認証処理を致しました<br />";
# DELETE
}elseif($delete_user){
$end_msg	 = "<span class=\"style_blue\">\n".$delete_user."</span>\nのデータを削除しました<br />";
}

if($ng_user){
$end_msg	.= "<span class=\"style_black\">\n".$ng_user."</span>\nはポイント不足の為データ認証できませんでした<br />";
}

if($out_user){
$end_msg	.= "<span class=\"style_black\">\n".$out_user."</span>\nはデータを正常に処理できませんでした<br />";
}

$end_msg	.= "<br />\n";
$end_msg	.= "<form action=\"".ADMIN_HTTP."user/user_attaches_mail.php\" method=\"post\">\n";
$end_msg	.= $form_sec_data;
$end_msg	.= $post_hidden;
$end_msg	.= "<input type=\"hidden\" name=\"purpose\" value=\"".$purpose."\" />\n";
$end_msg	.= "<input type=\"submit\" class=\"submit\" name=\"send\" value=\"対象ユーザーに".$page_title."メールを送る\" />\n";
$end_msg	.= "</form>\n";

print($html_class->outputExection($end_msg));

################################# HTML FOOTER ###################################

$html_class->htmlFooter();

##################################### END #######################################
?>
