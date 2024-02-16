<?php
################################## ファイル概要 #################################
##
##	user_attaches.php
##	----------------------------------------------------------------------------
##	ユーザーメディアアップロード
##	----------------------------------------------------------------------------
##
##################################### CONF ######################################

/** CONF FILE **/
require_once(dirname(__FILE__)."/../CONF/config.php");

/** CLASS FILE **/
require_once(dirname(__FILE__).'/../class/images.php');

################################### PAGE TYPE ###################################

if(!$_REQUEST['page_type']){
	$page_type = 1;
}else{
	$page_type = $_REQUEST['page_type'];
}

################################## IMAGE SIZE ###################################

# IMAGE SETTING ( FILE NAME : FILE SIZE : MAX WIDTH ) / IMAGES NEW
$images	 	= new images();
$image_data	= $images->makeImageData("attaches","500000000","600");

$movie_max_size	= 500000000;


################################# MEDIA CREATE #################################

/***************************************************************
**
**	写メアップロード
**
****************************************************************/

# MAKE IMAGE
if($_FILES['image_file'] != "" && $_SERVER["REQUEST_METHOD"] == "POST"){

	# MAKE IMAGE ( UPLOAD FILE / SETTING DATA / $_POST['image'] )
	$image	 = $images->makeMultiImage($_FILES['image_file'],$image_data,$_POST['images'],$_REQUEST['send_id']);

	# ERROR
	if(!$image || $image == "error"){
		$error	= 1;
	}else{
		$sec_data      .= "&upload=image";
		$count = count($image);
		for($i=0; $i<$count; $i++){
			$sec_data .= "&file[".$i."]=".$image[$i];
		}
	}

}

/***************************************************************
**
**	動画アップロード
**
****************************************************************/

# MAKE MOVIE
if($_FILES['movie_file'] != "" && $_SERVER["REQUEST_METHOD"] == "POST"){

	# MAKE IMAGE ( UPLOAD FILE / SETTING DATA / $_POST['image'] )
	$image	 = $images->makeMovie($_FILES['movie_file'],$movie_max_size,$_REQUEST['send_id']);

	# ERROR
	if(!$image || $image == "error"){
		$error	= 2;
	}else{
		$sec_data      .= "&upload=movie";
	}

}

################################ CONTENTS DATA ##################################

# sec情報
$sec_data	.= "&send_id=".$_REQUEST['send_id'];
$sec_data	.= "&page_type=".$page_type;
$sec_data	.= "&error=".$error;

################################## REDIRECT #####################################


if($_REQUEST['master'] == "on"){
	header("Location: ".MASTER_HTTP."user/user_attaches.php?".$sec_data);
	exit;
}

if($page_type == 6 || $page_type == 7){
	header("Location: ".ADMIN_HTTP."option/box_image.php?".$sec_data);
	exit();
}

header("Location: ".ADMIN_HTTP."user/user_attaches.php?".$sec_data);
exit();

##################################### END #######################################
?>
