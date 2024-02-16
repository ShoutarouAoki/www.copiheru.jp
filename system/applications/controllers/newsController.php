<?php
################################ FILE MANAGEMENT ################################
##
##	newsController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	NEWS SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	NEWS 各種処理
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: AKITOSHI TAKAI
##	CREATE DATE : 2015/06/13
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
$list						= NEWS_LIST_UNIT;

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
$next_previous_path			= $page_path."index/";


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
	**	INDEX / LIST
	**	============================================
	**	お知らせ一覧
	**
	************************************************/

	# INDEX
	if($data['page'] == "index" || $data['page'] == "list"){

		# NEWS CONDITIONS
		$news_conditions						= array();
		$news_conditions						= array(
			'category'							=> $category,
			'type'								=> 0,
			'status'							=> 1,
			'order'								=> "display_date DESC , id DESC",
			'list'								=> $list,
			'set'								=> $set
		);

		# COUNT
		$news_rows								= $informationModel->getInformationCount($news_conditions);
		$next_previous							= $htmlClass->makeNextPreviousLink($next_previous_path,$news_rows,$list,$set,$hidden_data);

		$news_rtn								= $informationModel->getInformationList($news_conditions,"id,display_date,title");

		$i=0;
		while($news_data = $database->fetchAssoc($news_rtn)){

			/************************************************
			**
			**	HTML DISPLAY
			**	---------------------------------------------
			**	表示セットアップ
			**
			************************************************/

			# DATE
			$display_date						= date("Y.m.d", strtotime($news_data['display_date']));


			/************************************************
			**
			**	NEWS VIEW
			**	---------------------------------------------
			**	VIEWに渡す配列データ生成
			**
			************************************************/

			$news_list['id'][$i]				= $news_data['id'];
			$news_list['date'][$i]				= $display_date;
			$news_list['title'][$i]				= $news_data['title'];

			$i++;

		}

		$database->freeResult($news_rtn,1);


	/************************************************
	**
	**	DETAIL
	**	============================================
	**	お知らせ詳細
	**
	************************************************/

	# DETAIL
	}elseif($data['page'] == "detail"){

		if(!empty($data['id']) && is_numeric($data['id'])){

			# NEWS DATA
			$news_data							= $informationModel->getInformationById($data['id']);

			# OK
			if(!empty($news_data['id'])){

				# DATE
				$display_date					= date("Y.m.d", strtotime($news_data['display_date']));

				# CONTENT
				$content						= $imageModel->replaceContentsImage($news_data['content'],$news_data['site_cd'],$category,HTTP_WEB_IMAGE);

			# ERROR
			}else{

				$error							= 1;
				$errormessage					= "該当のお知らせ記事はありません。";

			}

		# ERROR
		}else{

			$error								= 1;
			$errormessage						= "該当のお知らせ記事はありません。";


		}


	}


}


################################## FILE END #####################################
?>