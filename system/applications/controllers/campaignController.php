<?php
################################ FILE MANAGEMENT ################################
##
##	campaignController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	CAMPAIGN PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	CAMPAIGN PAGE 各種処理
##
##	キャンペーン詳細
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

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");

/** FILESET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/FilesetModel.php");

/** EMOJI PLUGINS **/
require_once(DOCUMENT_SYSTEM_PLUGINS."/Emoji/lib/mobile_class_8.php");


################################# POST ARRAY ####################################

$value_array				= array('id');
$data						= $mainClass->getArrayContents($value_array,$values);

############################## INDIVIDUAL SETTING ###############################


/************************************************
**
**	THIS PAGE INDIVIDUAL SETTING
**	---------------------------------------------
**	DATABASE / PATH / CATEGORY ...etc
**
************************************************/

# PAGE -> 固定
$data['page']				= "index";

# ERROR
$error						= NULL;
$errormessage				= NULL;


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

# CAMPAIGNSET MODEL
$campaignsetModel			= new CampaignsetModel($database,$mainClass);

# FILESET MODEL
$filesetModel				= new FilesetModel($database,$mainClass);


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

if(empty($exection) && empty($error)){



	/************************************************
	**
	**	INDEX
	**	============================================
	**	キャンペーン詳細取得
	**
	************************************************/

	# INDEX
	if(!empty($data['id']) && is_numeric($data['id'])){


		/************************************************
		**
		**	キャンペーン該当チェック
		**	============================================
		**	getCampaignsetDataでキャンペーン該当チェック
		**	checkCampaignUpdateでmembersへのアプローチ
		**
		**
		**
		************************************************/

		$campaign_contents					= NULL;
		$campaign_body						= NULL;

		$campaign_data						= $campaignsetModel->getCampaignDataById($data['id']);

		# キャンペーンあれば -> 表示文言 / 画像 取得
		if(!empty($campaign_data['id']) && !empty($campaign_data['file_html_id'])){

			$campaign_contents				= $filesetModel->getFilesetDataById($campaign_data['file_html_id']);
			if(!empty($campaign_contents['id']) && !empty($campaign_contents['body_normal'])){
				$campaign_body				= $imageModel->replaceContentsImage($campaign_contents['body_normal'],$members_data['site_cd'],$campaign_category,HTTP_CAMPAIGN_IMAGE);
			}

		# ERROR
		}else{

			$error							= 1;
			$errormessage					= "該当のキャンペーンは既に終了しております<br />";

		}


	# ERROR
	}else{

		$error								= 1;
		$errormessage						= "該当のキャンペーンデータがありません<br />";

	}


# ERROR
}else{

	$error									= 1;
	$errormessage							= "該当のキャンペーンデータがありません<br />";

}


################################## FILE END #####################################
?>