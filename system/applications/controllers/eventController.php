<?php
################################ FILE MANAGEMENT ################################
##
##	eventController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	EVENT PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	EVENT PAGE 各種処理
##
##	index		: イベント一覧
##	detail		: イベント詳細
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

/** FILESET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/FilesetModel.php");

/** EMOJI PLUGINS **/
require_once(DOCUMENT_SYSTEM_PLUGINS."/Emoji/lib/mobile_class_8.php");


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
	$data['page']				= "index";
}

# ERROR
$error							= NULL;
$errormessage					= NULL;


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
	**	イベントリスト
	**
	************************************************/

	if($data['page'] == "index"){

		$nowtime								= date("YmdHis");

		$events_conditions						= array();
		$events_conditions						= array(
			'date_s'							=> $nowtime,
			'status'							=> 0,
			'order'								=> 'date_e DESC'
		);
		$events_rtn								= $eventModel->getEventList($events_conditions);

		$i=0;
		$j=0;
		while($events_data = $database->fetchAssoc($events_rtn)){

			# 開催中イベント
			if($events_data['date_s'] <= $nowtime && $events_data['date_e'] >= $nowtime){

				$event_list['id'][$i]			= $events_data['id'];
				$event_list['name'][$i]			= $events_data['name'];
				$event_list['image'][$i]		= $events_data['image'];

				$i++;

			# それ以外
			}else{

				$event_past['id'][$j]			= $events_data['id'];
				$event_past['name'][$j]			= $events_data['name'];
				$event_past['image'][$j]		= $events_data['image'];

				$j++;

			}

		}

		$database->freeResult($events_rtn);


	/************************************************
	**
	**	DETAIL
	**	============================================
	**	イベント情報詳細
	**
	************************************************/

	}elseif($data['page'] == "detail"){

		# INDEX
		if(!empty($data['id']) && is_numeric($data['id'])){

			$event_contents						= NULL;
			$event_body							= NULL;

			$event_data							= $eventModel->getEventDataById($data['id']);

			# キャンペーンあれば -> 表示文言 / 画像 取得
			if(!empty($event_data['id']) && !empty($event_data['file_html_id'])){

				$event_contents				= $filesetModel->getFilesetDataById($event_data['file_html_id']);
				if(!empty($event_contents['id']) && !empty($event_contents['body_normal'])){
					$event_body				= $imageModel->replaceContentsImage($event_contents['body_normal'],$members_data['site_cd'],$event_category,HTTP_EVENT_IMAGE);
				}

			# ERROR
			}else{

				$error							= 1;
				$errormessage					= "該当のイベントは既に終了しております<br />";

			}


		# ERROR
		}else{

			$error								= 1;
			$errormessage						= "該当のイベントがありません<br />";

		}

	}


# ERROR
}else{

	$error									= 1;
	$errormessage							= "該当のイベントがありません<br />";

}


################################## FILE END #####################################
?>