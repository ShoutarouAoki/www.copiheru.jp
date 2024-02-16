<?php
################################## ファイル概要 #################################
##
##	image_upload.php
##	----------------------------------------------------------------------------
##	画像UPLOADファイル
##	----------------------------------------------------------------------------
##
##################################### CONF ######################################

/** CONF FILE **/
require_once(dirname(__FILE__)."/../CONF/config.php");

/** CLASS FILE **/
require_once(dirname(__FILE__).'/../class/images.php');

################################# CATEGORY TYPE #################################


# FILE TYPE
if($_REQUEST['file_type']){
	$file_type		= $_REQUEST['file_type'];
}else{
	$file_type		= 0;
}

# CATEGORY
if($_REQUEST['category']){
	$category	= $_REQUEST['category'];
}else{
	$category	= 1;
}

$thumb_width	= NULL;

#################################### SETTING ####################################

# CATEGORY SETTING BANNER
for($web_cnt=1;$web_cnt<count($image_setting_type);$web_cnt++){

	if($image_setting_type[$web_cnt][3] == 0){ continue; }

	if($image_setting_type[$web_cnt][1] == $category && $image_setting_type[$web_cnt][3] == $file_type){
		$max_size	= MAX_IMAGE_SIZE;
		$max_width	= MAX_IMAGE_WIDTH;
		$image_dir	= $image_setting_type[$web_cnt][5];
	}

}


# CATEGORY SETTING MAIL
for($mail_cnt=1;$mail_cnt<count($mail_file_type);$mail_cnt++){

	if($mail_file_type[$mail_cnt][3] == 0){ continue; }

	if($mail_file_type[$mail_cnt][1] == $category && $mail_file_type[$mail_cnt][3] == $file_type){
		$max_size	= MAX_IMAGE_SIZE;
		$max_width	= MAX_IMAGE_WIDTH;
		$image_dir	= $mail_file_type[$mail_cnt][5];
	}

}

# CATEGORY SETTING HTML
for($html_cnt=1;$html_cnt<count($html_file_type);$html_cnt++){

	if($html_file_type[$html_cnt][3] == 0){ continue; }

	if($html_file_type[$html_cnt][1] == $category && $html_file_type[$html_cnt][3] == $file_type){
		$max_size	= MAX_IMAGE_SIZE;
		$max_width	= MAX_IMAGE_WIDTH;
		$image_dir	= $html_file_type[$html_cnt][5];
	}

}

# CATEGORY SETTING ITEM
for($item_cnt=1;$item_cnt<count($item_file_type);$item_cnt++){

	if($item_file_type[$item_cnt][3] == 0){ continue; }

	if($item_file_type[$item_cnt][1] == $category && $item_file_type[$item_cnt][3] == $file_type){

		$max_size	= MAX_ICON_SIZE;
		$image_dir	= $item_file_type[$item_cnt][5];

		if(!empty($item_file_type[$item_cnt][8])){
			$max_width	= $item_file_type[$item_cnt][8];
		}else{
			$max_width	= MAX_ICON_WIDTH;
		}

		if(!empty($item_file_type[$item_cnt][9])){
			$thumb_width	= $item_file_type[$item_cnt][9];
		}

	}

}


# CATEGORY SETTING BANNER
for($banner_cnt=1;$banner_cnt<count($banner_file_type);$banner_cnt++){

	if($banner_file_type[$banner_cnt][3] == 0){ continue; }

	if($banner_file_type[$banner_cnt][1] == $category && $banner_file_type[$banner_cnt][3] == $file_type){
		$max_size	= MAX_IMAGE_SIZE;
		$max_width	= MAX_IMAGE_WIDTH;
		$image_dir	= $banner_file_type[$banner_cnt][5];
	}

}

################################## IMAGES CLASS #################################

# IMAGE SETTING ( FILE NAME : FILE SIZE : MAX WIDTH ) / IMAGES NEW
$images	 	= new images();
$image_data	= $images->makeImageData($image_dir,$max_size,$max_width);

################################ IMAGE CREATED ##################################

# MAKE IMAGE
if($_FILES['image_file'] != "" && $_SERVER["REQUEST_METHOD"] == "POST"){

	# MAKE IMAGE ( UPLOAD FILE / SETTING DATA / $_POST['image'] )
	$image	 = $images->makeImage($_FILES['image_file'],$image_data,$_POST['images'],NULL,$thumb_width);

	# ERROR
	if(!$image || $image == "error"){
		$error	= 1;
	}else{
		$sec_data      .= "&upload=image";
	}

}


################################ CONTENTS DATA ##################################

# sec情報
$sec_data	.= "&file_type=".$file_type;
$sec_data	.= "&category=".$category;
$sec_data	.= "&campaign=".$_REQUEST['campaign'];
$sec_data	.= "&error=".$error;
$sec_data	.= "&file=".$image;

################################## REDIRECT #####################################

header("Location: ".ADMIN_HTTP."setting_db/setting_image.php?".$sec_data);

##################################### END #######################################
?>
