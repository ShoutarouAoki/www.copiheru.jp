<?php
################################## ファイル概要 #################################
##
##	chara_attaches.php
##	----------------------------------------------------------------------------
##	キャラメディアアップロード
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
$image_data	= $images->makeImageData("attaches","5000000","800");

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
	$image	 = $images->makeImage($_FILES['image_file'],$image_data,$_POST['images'],$_REQUEST['send_id']);

	# ERROR
	if(!$image || $image == "error"){
		$error			 = 1;
	}else{
		$sec_data      .= "&upload=image";
	}
}

################################ CONTENTS DATA ##################################

# sec情報
$sec_data	.= "&send_id=".$_REQUEST['send_id'];
$sec_data	.= "&file=".$image;
$sec_data	.= "&device=".$_REQUEST['device'];
$sec_data	.= "&error=".$error;

################################## REDIRECT #####################################



if($_REQUEST['master'] == "on"){
	header("Location: ".MASTER_HTTP."user/chara_attaches.php?".$sec_data);
	exit();
}
header("Location: ".ADMIN_HTTP."user/chara_attaches.php?".$sec_data);
exit();

##################################### END #######################################
?>
