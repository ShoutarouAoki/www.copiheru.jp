<?php
################################ FILE MANAGEMENT ################################
##
##	albumController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	ALBUM PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	ALBUM PAGE 各種処理
##
##	page : index -> 
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

/** ALBUM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AlbumModel.php");

################################# POST ARRAY ####################################

$value_array				= array('page','set','id');
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

# 一覧の表示件数
$list						= ALBUM_LIST_UNIT;

# LIST SET
if(isset($_POST['set'])){
	$set					= $_POST['set'];
}elseif(!empty($data['set'])){
	$set					= $data['set'];
}

# SET EMPTY
if(!isset($set)){
	$set					= 0;
}

# PAGE PATH
$next_previous_path			= $page_path."list/";


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# ALBUM MODEL
$albumModel					= new AlbumModel($database,$mainClass);


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
	**	INDEX / LIST
	**	============================================
	**	アルバムページ
	**
	************************************************/

	# INDEX
	if($data['page'] == "index" || $data['page'] == "list" || $data['page'] == "slide"){

		# リスト表示は件数指定
		if($data['page'] == "index" || $data['page'] == "list"){

			$albums_conditions						= array();
			$albums_conditions						= array(
				'user_id'							=> $members_data['id'],
				'status'							=> 0,
				'order'								=> "id DESC",
				'list'								=> $list,
				'set'								=> $set
			);

			# COUNT
			$albums_rows							= $albumModel->getAlbumCount($albums_conditions);
			$next_previous							= $htmlClass->makeNextPreviousLink($next_previous_path,$albums_rows,$list,$set,$hidden_data);

		# スライド表示は全抽出
		}else{

			$albums_conditions						= array();
			$albums_conditions						= array(
				'user_id'							=> $members_data['id'],
				'status'							=> 0,
				'order'								=> "id DESC"
			);

		}

		$albums_rtn									= $albumModel->getAlbumList($albums_conditions);

		$i=0;
		while($albums_data = $database->fetchAssoc($albums_rtn)){

			$album_list['id'][$i]					= $albums_data['id'];
			$album_list['image'][$i]				= $albums_data['image'];
			$album_list['name'][$i]					= $albums_data['name'];

			$i++;

		}

		$database->freeResult($albums_rtn);


	}

}


################################## FILE END #####################################
?>