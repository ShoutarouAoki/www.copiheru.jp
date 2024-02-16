<?php
################################## ファイル概要 #################################
##
##	chara_attaches_exe.php
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


################################ DATABASE CONNECT ###############################

$db = new accessDb(0);
$db->connectDb();

################################## MAIN SQL #####################################

$adminMain	= new adminMain($db);

$table					 = "attaches";

# サムネイル
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
		$reset_data['use_flg']	 = 0;
		$reset_where			 = "user_id =".$user_data['id']." ";
		$reset_where			.= "AND site_cd =".$user_data['site_cd']." ";
		$reset_where			.= "AND category = ".$_REQUEST['category']." ";
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

	# 公開取り消しUPDATE
	if($_REQUEST['unupdate_id']){

		# UPDATE SQL
		$update_data['use_flg']	 = "0";
		$update_where			 = "id =".$_REQUEST['unupdate_id']." ";

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

		$db = new accessDb(0);
		$db->connectDb();

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

	}


# メイン画像
}elseif($_REQUEST['purpose'] == 22){

	$images		= new images();

	$members	= new members();

	# USER DATA
	$user_where	 = "site_cd = ".$_REQUEST['site_cd']." ";
	$user_where	.= "AND id = ".$_REQUEST['send_id']."";

	$user_data	 = $members->getUser($user_column,$user_where,$info_column);

	# 公開UPDATE
	if($_REQUEST['update_id']){

		# UPDATE SQL
		$update_data['use_flg']	 = 1;
		$update_where			 = "id =".$_REQUEST['update_id']." ";

		$db->updateDb($table,$update_data,$update_where);
		$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
		if($db_err){ print($db_err); exit; }

	}


	# 公開取り消しUPDATE
	if($_REQUEST['unupdate_id']){

		# UPDATE SQL
		$update_data['use_flg']	 = "0";
		$update_where			 = "id =".$_REQUEST['unupdate_id']." ";

		$db->updateDb($table,$update_data,$update_where);
		$db_err = $db->errorDb("",$db->errno,__FILE__,__LINE__);
		if($db_err){ print($db_err); exit; }

	}


	# 表示順UPDATE
	if(!empty($_REQUEST['change_id'])){

		$table						 = "attaches";

		$update_data['pay_count']	 = $_REQUEST['pay_count'][$_REQUEST['change_id']];
		$update_data['level_s']		 = $_REQUEST['level_s'][$_REQUEST['change_id']];
		$update_data['level_e']		 = $_REQUEST['level_e'][$_REQUEST['change_id']];

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

		$db = new accessDb(0);
		$db->connectDb();

		# 削除SQL
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

	}

}

################################ DATABASE CLOSE #################################

$db->closeDb();

################################ CONTENTS DATA ##################################

# sec情報
$sec_data	.= "&send_id=".$_REQUEST['send_id'];
$sec_data	.= "&purpose=".$_REQUEST['purpose'];
$sec_data	.= "&device=".$_REQUEST['device'];
$sec_data	.= "&error=".$error_msg;

################################## REDIRECT #####################################

header("Location: ".ADMIN_HTTP."user/chara_attaches_exe.php?".$sec_data);

##################################### END #######################################
?>
