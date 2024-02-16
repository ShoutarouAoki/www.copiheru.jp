<?php
################################## ファイル概要 #################################
##
##	user_attaches_exe.php
##	----------------------------------------------------------------------------
##	ユーザーメディア更新削除処理
##	----------------------------------------------------------------------------
##
##################################### CONF ######################################

/** CONF FILE **/
require_once(dirname(__FILE__)."/../CONF/config.php");

/** CLASS FILE **/
require_once(dirname(__FILE__).'/../class/database.php');
require_once(dirname(__FILE__).'/../class/images.php');
require_once(dirname(__FILE__).'/../class/main.php');
require_once(dirname(__FILE__).'/../class/html_class.php');
require_once(dirname(__FILE__).'/../class/members.php');

################################### PAGE TYPE ###################################

if(!$_REQUEST['page_type']){
	$page_type = 1;
}else{
	$page_type = $_REQUEST['page_type'];
}

################################ DATABASE CONNECT ###############################

$db = new accessDb(0);
$db->connectDb();

################################## MAIN SQL #####################################

$adminMain	= new adminMain($db);

if($_REQUEST['purpose'] == 2){

	$images		= new images();

	$members	= new members();

	# USER DATA
	$user_where	 = "site_cd = ".$_REQUEST['site_cd']." ";
	$user_where	.= "AND id = ".$_REQUEST['send_id']."";

	$user_data	 = $members->getUser($user_column,$user_where,$info_column);

	# 公開UPDATE
	if($_REQUEST['update_id']){

		# RESET SQL
		$table					 = "attaches";
		$reset_data['use_flg']	 = 0;
		$reset_where			 = "user_id =".$user_data['id']." ";
		$reset_where			.= "AND site_cd =".$user_data['site_cd']." ";
		$reset_where			.= "AND category = ".$_REQUEST['page_type']." ";
		$reset_where			.= "AND use_flg != 0";

		$db->updateDb($table,$reset_data,$reset_where);
		$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
		if($db_err){ print($db_err); exit; }

		# UPDATE SQL
		$update_data['use_flg']	 = 1;
		$update_where			 = "id =".$_REQUEST['update_id']." ";

		$db->updateDb($table,$update_data,$update_where);
		$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
		if($db_err){ print($db_err); exit; }

	}



	# 入金回数UPDATE
	if($_REQUEST['change_id']){

		$table						 = "attaches";

		$reg_date					 = $_REQUEST['reg_y'].$_REQUEST['reg_m'].$_REQUEST['reg_d'];
		$reg_date					.= $_REQUEST['reg_h'].$_REQUEST['reg_i'].$_REQUEST['reg_s'];

		# UPDATE SQL
		if($reg_date){
		$update_data['reg_date']	 = $reg_date;
		}
		$update_data['pay_count']	 = $_REQUEST['pay_count'][$_REQUEST['change_id']];

		$update_where				 = "id =".$_REQUEST['change_id']." ";

		$db->updateDb($table,$update_data,$update_where);
		$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
		if($db_err){ print($db_err); exit; }

	}


	# 削除
	if($_REQUEST['delete_id']){

		$error_msg = "";

		# 削除ID
		$delete_id				= $_REQUEST['delete_id'];
		# 削除名
		$attached				= $_REQUEST['attached'];

		# 自動予約にセットされていたら警告

		$db = new accessDb(2);
		$db->connectDb();

			$chk_column = "id";
			$chk_where  = "site_cd = ".$_REQUEST['site_cd']." AND media = '".$attached."' AND send_id = ".$user_data['id']." AND status = 0";
			$chk_order  = "";
			$chk_limit  = "1";

			$check_rtn1  = $db->selectDb("automails",$chk_column,$chk_where,$chk_order,$chk_limit);
			$db->errorDb("",$db->errno,__FILE__,__LINE__);
			$check_row1 = $db->numRows($check_rtn1);
			if($check_row1 > 0){ $error_msg .= "新規自動で削除対象が予約されておりますので削除できません。<br />"; }

			$check_rtn2  = $db->selectDb("automulties",$chk_column,$chk_where,$chk_order,$chk_limit);
			$db->errorDb("",$db->errno,__FILE__,__LINE__);
			$check_row2 = $db->numRows($check_rtn2);
			if($check_row2 > 0){ $error_msg .= "自動同報で削除対象が予約されておりますので削除できません。<br />"; }

			$check_rtn3  = $db->selectDb("autoreceives",$chk_column,$chk_where,$chk_order,$chk_limit);
			$db->errorDb("",$db->errno,__FILE__,__LINE__);
			$check_row3 = $db->numRows($check_rtn3);
			if($check_row3 > 0){ $error_msg .= "自動返信で削除対象が予約されておりますので削除できません。<br />"; }

		$db->closeDb();

		$db = new accessDb(0);
		$db->connectDb();

		#mail("okuma@d-ef.co.jp","file:user_attaches_exe",$check_row1."\n".$check_row2."\n".$check_row3);

		if($check_row1 == 0 && $check_row2 == 0 && $check_row3 == 0){

			# 削除SQL
			$table					= "attaches";
			$upd_data['status'] 	= 9;
			$upd_data['use_flg']	= 0;

			$column	 = "*";
			$where	 = "id = ".$delete_id;

			$rtn	 = $db->selectDb($table,$column,$where,$order,$limit);
			$db->errorDb("",$db->errno,__FILE__,__LINE__);
			$data  	 = $db->fetchAssoc($rtn);

			$db->updateDb($table,$upd_data,$where);
			$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
			if($db_err){ print($db_err); exit; }

			#$result	= $images->deleteAttacheData($data['attached'],$data['category'],2);

		}else{

			$error_msg = urlencode($error_msg);

		}

	}

}

################################ DATABASE CLOSE #################################

$db->closeDb();

################################ CONTENTS DATA ##################################

# sec情報
$sec_data	.= "&send_id=".$_REQUEST['send_id'];
$sec_data	.= "&page_type=".$page_type;
$sec_data	.= "&purpose=".$_REQUEST['purpose'];
$sec_data	.= "&error=".$error_msg;

################################## REDIRECT #####################################

header("Location: ".ADMIN_HTTP."user/user_attaches_exe.php?".$sec_data);

##################################### END #######################################
?>
