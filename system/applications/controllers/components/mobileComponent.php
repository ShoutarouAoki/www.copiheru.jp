<?php
################################ FILE MANAGEMENT ################################
##
##	mobileComponent.php
##	=============================================================================
##
##	■PAGE / 
##	MAG OFFICIAL ADMIN
##	COMPONENT SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	MOBILE コンポーネント
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: AKITOSHI TAKAI
##	CREATE DATE : 2012/12/01
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
############################## MOBILEDESIGN SETTING #############################

# BACK GROUND COLOR
$body_background			= " background-color: #FFFFFF;";
$title_background			= " background-color: #E6D8BB";
$sub_title_background		= " background-color: #FF9933;";
$contents_background		= " background-color: #FFFFFF;";
$footer_background			= " background-color: #E6D8BB;";

# TEXT COLOR
$text_color					= " color: #333333;";
$title_color				= " color: #000000;";
$sub_title_color			= " color: #FF6600;";
$bold_color1				= " color: #333333;";
$bold_color2				= " color: #FF0000;";
$bold_color3				= " color: #0099CC;";
$footer_color				= " color: #222222;";

# LINK COLOR
$link_color					= "#004D8E";
$link_hover					= "#FFFFFF";
$link_visited				= "#800080";

# INPUT TYPE
$input_type					= $deviceClass->getDeviceMode();


/************************************************
**
**	CARRIER
**	---------------------------------------------
**	ドコモ用設定
**
************************************************/

if($device_type == "DoCoMo"){

	# BORDER COLOR
	$border_color			= " border-color: #CCCCCC;";

	# FONT SIZE
	$font_size				= " font-size: xx-small;";
	$font_large				= " font-size: large;";
	$font_medium			= " font-size: medium;";
	$font_small				= " font-size: xx-small;";

	# INPUT STYLE
	$style_mail				= $input_type['alphabet'];
	$style_zip				= $input_type['numeric'];
	$style_tel				= $input_type['numeric'];


/************************************************
**
**	CARRIER
**	---------------------------------------------
**	ソフトバンク用設定
**
************************************************/

}elseif($device_type == "SoftBank" || $device_type == "JPHONE"){

	# BORDER COLOR
	$border_color			= " border-color: CCCCCC;";

	# FONT SIZE
	$font_size				= " font-size: small;";
	$font_large				= " font-size: large;";
	$font_medium			= " font-size: medium;";
	$font_small				= " font-size: small;";

	# INPUT STYLE
	$style_mail				= $input_type['alphabet']." width: 200px;";
	$style_zip				= $input_type['numeric']." width: 60px;";
	$style_tel				= $input_type['numeric']." width: 100px;";


/************************************************
**
**	CARRIER
**	---------------------------------------------
**	AU用設定 / PC
**
************************************************/

}else{

	# BORDER COLOR
	$border_color			= " color: #CCCCCC;";

	# FONT SIZE
	$font_size				= " font-size: xx-small;";
	$font_large				= " font-size: large;";
	$font_medium			= " font-size: medium;";
	$font_small				= " font-size: xx-small;";

	# INPUT STYLE
	$style_mail				= $input_type['alphabet']." width: 200px;";
	$style_zip				= $input_type['numeric']." width: 60px;";
	$style_tel				= $input_type['numeric']." width: 100px;";

}


# TAG
$hr							 = "<hr style=\"background-color: #CCCCCC; color: #CCCCCC; ";
$hr							.= "height:1px; border:0px solid #CCCCCC ; margin:0.3em 0;\" size=\"1\" color=\"#CCCCCC\" />\n";

$header_spacer				= "<div><img src=\"/images/spacer.gif\" width=\"1\" height=\"5\" /></div>\n";
$spacer						= "<div><img src=\"/images/spacer.gif\" width=\"1\" height=\"5\" /></div>\n";


$contents_table				= "<table width=\"100%\" cellspasing=\"0\" cellpadding=\"5\" border=\"0\">\n<tr>\n<td>\n<div style=\"".$font_size."\">";
$table_end					= "</div>\n</td>\n</tr>\n</table>\n";


# PREVIEW ACCESS CHECK
$access_check				= $deviceClass->getIncludeDirectry();

# SETTING
if(!empty($access_check)){
	$preview_setting		= " width:240px; margin: 0 auto; border-left: 1px solid #999999; border-right: 1px solid #999999;";
}else{
	$preview_setting		= NULL;
}


################################## FILE END #####################################
?>