<?php
################################ FILE MANAGEMENT ################################
##
##	guideController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	GUIDE PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	MAIN PAGE 各種処理
##
##
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

# NONE


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

# ID
if(!empty($_POST['id'])){
	$data['id']				= $_POST['id'];
}


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# NONE


################################## MAIN SQL #####################################





/************************************************
**
**	ページ毎にif文で処理分岐
**
************************************************/


/************************************************
**
**	INDEX
**	============================================
**
**	
**
************************************************/

# INDEX
if($data['page'] == "index"){








/************************************************
**
**	BROWSER
**	===========================================
**	ブラウザ確認
**
************************************************/

}elseif($data['page'] == "browser"){



}




################################## FILE END #####################################
?>