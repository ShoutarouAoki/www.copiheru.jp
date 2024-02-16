<?php
################################## ファイル概要 #################################
##
##	user_age_attaches.php
##	----------------------------------------------------------------------------
##	ユーザーメディアアップロード
##	----------------------------------------------------------------------------
##
##################################### CONF ######################################

/** CONF FILE **/
require_once(dirname(__FILE__)."/../CONF/config.php");

/** CLASS FILE **/
require_once(dirname(__FILE__).'/../class/images.php');

################################## IMAGE SIZE ###################################

# IMAGE SETTING ( FILE NAME : FILE SIZE : MAX WIDTH ) / IMAGES NEW
$images	 	= new images();
$image_data	= $images->makeImageData("attaches","500000","320");

$movie_max_size	= 50000000;


################################# MEDIA CREATE #################################

/***************************************************************
**
**	写メアップロード
**
****************************************************************/

# MAKE IMAGE
if($_FILES['image_file'] != "" && $_SERVER["REQUEST_METHOD"] == "POST"){

	# MAKE IMAGE ( UPLOAD FILE / SETTING DATA / $_POST['image'] )
	$image	 = $images->makeImage($_FILES['image_file'],$image_data,$_POST['images'],$_REQUEST['send_id']);

	# ERROR
	if(!$image || $image == "error"){
		$error	= 1;
	}else{
		$sec_data      .= "&upload=image";
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
$sec_data	.= "&file=".$image;
$sec_data	.= "&error=".$error;

################################## REDIRECT #####################################

header("Location: ".ADMIN_HTTP."user/user_age_attaches.php?".$sec_data);

##################################### END #######################################
?>
