<?php
################################ FILE MANAGEMENT ################################
##
##	tutorialController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	TUTORIAL SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	チュートリアル
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2016/05/31
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
################################# REQUIRE MODEL #################################


/************************************************
**
**	MODEL FILE REQUIRE
**	---------------------------------------------
**	MODEL CLASS FILE READING
**
************************************************/

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");


################################# POST ARRAY ####################################

$value_array				= array('page','id');
$data						= $mainClass->getArrayContents($value_array,$values);

############################## INDIVIDUAL SETTING ###############################


/************************************************
**
**	THIS PAGE INDIVIDUAL SETTING
**	---------------------------------------------
**	DATABASE / PATH / CATEGORY ...etc
**
************************************************/

# PAGE
if(empty($data['page'])){
	$data['page']			= "index";
}

# HEADER / FOTTER HIDE
$header_hide				= 1;
$footer_hide				= 1;

################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);


################################## MAIN SQL #####################################


/************************************************
**
**	PAGE SEPALATE
**	---------------------------------------------
**	DISPLAY
**	---------------------------------------------
**	PAGE CONTROLL
**
**	$exectionがNULLなら
**	表示処理開始
**	---------------------------------------------
**	PAGE :	
**
************************************************/

if(empty($exection)){


	/************************************************
	**
	**	チュートリアルスタート
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){

		# 背景画像取得
		$image_file_type				= $web_filetype_array[$directory];

		$image_conditions				= array();
		$image_conditions				= array(
			'file_type'					=> $image_file_type,
			'category'					=> $web_image_category,
			'target_id'					=> 0,
			'status'					=> 1
		);

		$image_rtn						= $imageModel->getImageList($image_conditions);

		$i=0;
		while($image_data = $database->fetchAssoc($image_rtn)){

			$image_list['id'][$i]		= $image_data['id'];
			$image_list['image'][$i]	= $image_data['img_name'];

			$i++;

		}

		$database->freeResult($image_rtn);

		# MONTH
		$month_option					= NULL;
		for($i=0;$i<12;$i++){
			$month						= $i + 1;
			if($month < 10){
				$month					= "0".$month;
			}
			$month_option				.= "<option value=\"".$month."\">".$month."月</option>\n";
		}

		# DAY
		$day_option						= NULL;
		for($i=0;$i<31;$i++){
			$day						= $i + 1;
			if($day < 10){
				$day					= "0".$day;
			}
			$day_option					.= "<option value=\"".$day."\">".$day."日</option>\n";
		}

	}

}


################################## FILE END #####################################
?>