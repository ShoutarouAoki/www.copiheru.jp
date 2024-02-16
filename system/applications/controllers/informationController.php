<?php
################################ FILE MANAGEMENT ################################
##
##	informationController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	INFORMATION PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	INFORMATION PAGE 各種処理
##
##	page : index	-> MENU LIST
##	page : guide	-> 遊び方ガイド
##	page : help		-> ヘルプ
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

/** INFORMATION MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/InformationModel.php");

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

################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# INFORMATION MODEL
$informationModel			= new InformationModel($database,$mainClass);

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
	**	ページ毎にif文で処理分岐
	**
	************************************************/


	/************************************************
	**
	**	INDEX
	**	============================================
	**
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){




	/************************************************
	**
	**	GUIDE
	**	============================================
	**	遊び方ガイド
	**
	************************************************/

	# GUIDE
	}elseif($data['page'] == "guide"){

		# DETAIL
		$display_detail								= NULL;

		# LIST
		if(empty($data['id'])){

			# GUIDE CONDITIONS
			$guide_conditions						= array();
			$guide_conditions						= array(
				'category'							=> $category,
				'type'								=> 0,
				'status'							=> 1,
				'order'								=> "display_date DESC , id DESC"
			);

			$guide_rtn								= $informationModel->getInformationList($guide_conditions,"id,title");

			$i=0;
			while($guide_data = $database->fetchAssoc($guide_rtn)){


				/************************************************
				**
				**	NEWS VIEW
				**	---------------------------------------------
				**	VIEWに渡す配列データ生成
				**
				************************************************/

				$guide_list['id'][$i]				= $guide_data['id'];
				$guide_list['title'][$i]			= $guide_data['title'];

				$i++;

			}

			$database->freeResult($guide_rtn,1);

		# DETAIL
		}elseif(!empty($data['id']) && is_numeric($data['id'])){

			# GUIDE DATA
			$guide_data							= $informationModel->getInformationById($data['id']);

			# OK
			if(!empty($guide_data['id'])){

				# CONTENT
				$content						= $imageModel->replaceContentsImage($guide_data['content'],$guide_data['site_cd'],$category,HTTP_WEB_IMAGE);

				$display_detail					= 1;

			# ERROR
			}else{

				$error							= 1;
				$errormessage					= "該当のデータはありません。";

			}

		}




	/************************************************
	**
	**	HELP
	**	============================================
	**	ヘルプ
	**
	************************************************/

	# HELP
	}elseif($data['page'] == "help"){

		# HELP CONDITIONS
		$help_conditions						= array();
		$help_conditions						= array(
			'category'							=> $category,
			'type'								=> 1,
			'status'							=> 1,
			'order'								=> "display_date DESC , id DESC"
		);

		$help_rtn								= $informationModel->getInformationList($help_conditions,"id,title,content");

		$i=0;
		while($help_data = $database->fetchAssoc($help_rtn)){


			/************************************************
			**
			**	NEWS VIEW
			**	---------------------------------------------
			**	VIEWに渡す配列データ生成
			**
			************************************************/

			$help_list['id'][$i]				= $help_data['id'];
			$help_list['number'][$i]			= $i + 1;
			$help_list['title'][$i]				= $help_data['title'];
			$help_list['content'][$i]			= $help_data['content'];

			$i++;

		}

		$database->freeResult($help_rtn,1);


	}




}


################################## FILE END #####################################
?>