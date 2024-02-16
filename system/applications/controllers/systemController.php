<?php
################################ FILE MANAGEMENT ################################
##
##	indexController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	INDEX SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	INDEX 各種処理
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2014/10/31
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
################################# ACCESS CHECK ##################################

if(!defined("SYSTEM_CHECK")){
	exit();
}

################################# REQUIRE MODEL #################################


/************************************************
**
**	MODEL FILE REQUIRE
**	---------------------------------------------
**	MODEL CLASS FILE READING
**
************************************************/

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

/** RANKING MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/RankingModel.php");

/** PRESENT MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PresentModel.php");


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

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);

# CAMPAIGNSET MODEL
$campaignsetModel			= new CampaignsetModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMBOX MODEL
$itemboxModel				= new ItemboxModel($database,$mainClass);

# ITEMUSE MODEL
$itemuseModel				= new ItemuseModel($database,$mainClass);

# RANKING MODEL
$rankingModel				= new RankingModel($database,$mainClass);

# PRESENT MODEL
$presentModel				= new PresentModel($database,$mainClass);


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

	# INDEX
	if($data['page'] == "index"){




	# PAYMENT
	}elseif($data['page'] == "payment"){



		if($data['id'] == "finish"){

			print("finish");


		}else{




		}


	# PRESENT
	}elseif($data['page'] == "present"){


		/************************************************
		**
		**	MASTER DATABASE切り替え
		**
		************************************************/

		# AUTHORITY / 既にマスターに接続してるかチェック
		$db_auth								 = $database->checkAuthority();

		# DATABASE CHANGE / スレーブだったら
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

		}

		# TRANSACTION START
		$database->beginTransaction();

		# ERROR
		$error									= NULL;

		# 初期化
		$present_dialog							= NULL;
		$present_check							= NULL;
		$present_message						= NULL;
		$present_message						= array();

		$presents_conditions					= array(
			'check_distribution_date'			=> 1,
			'status'							=> 0
		);

		$presents_rtn							= $presentModel->getPresentList($presents_conditions);

		$i=0;
		while($presents_data = $database->fetchAssoc($presents_rtn)){

			$acceptance_conditions				= array();
			$acceptance_conditions				= array(
				'user_id'						=> $members_data['id'],
				'present_id'					=> $presents_data['id'],
				'limit'							=> 1
			);

			$acceptance_check					= $presentboxModel->getPresentboxCount($acceptance_conditions);

			# 受け取り済み
			if(!empty($acceptance_check)){
				continue;
			}

			# TICKET
			if($presents_data['type'] == $number_ticket){

				$present_data					= $shopModel->getShopDataById($presents_data['target_id'],"id,name,image");

			# ITEM
			}elseif($presents_data['type'] == $number_item){

				$present_data					= $itemModel->getItemDataById($presents_data['target_id'],"id,name,image");

			# IMAGE
			}elseif($presents_data['type'] == $number_image){

				$image_data						= $imageModel->getImageDataById($presents_data['target_id'],"id,img_name,img_key");
				$present_data['id']				= $image_data['id'];
				$present_data['name']			= $image_data['img_key'];
				$present_data['image']			= $image_data['img_name'];

			}

			$presentbox_insert					= array(
				'site_cd'						=> $members_data['site_cd'],
				'user_id'						=> $members_data['id'],
				'present_id'					=> $presents_data['id'],
				'acceptance_date'				=> date("Y-m-d"),
				'category'						=> $presents_data['category'],
				'type'							=> $presents_data['type'],
				'target_id'						=> $presents_data['target_id'],
				'unit'							=> $presents_data['unit'],
				'limit_date'					=> $presents_data['limit_date'],
				'status'						=> 0
			);

			$insert_id							= $database->insertDb("presentbox",$presentbox_insert);

			# ERROR 吐いたら処理止め
			if(empty($insert_id)){
				$error							= 1;
				$present_check					= NULL;
				$present_message				= NULL;
				$present_message				= array();
				break;
			}

			# LOGIN BONUS 表示生成
			if(isset($present_message[$presents_data['category']])){
				if(!empty($presents_data['message'])){
					$present_message[$presents_data['category']]	.= $presenst_data['message'];
				}else{
					$present_message[$presents_data['category']]	.= $present_data['name']." × ".$bonuses_data['unit']."<br />";
				}
			}else{
				if(!empty($presents_data['message'])){
					$present_message[$presents_data['category']]	 = $presenst_data['message'];
				}else{
					$present_message[$presents_data['category']]	 = $present_data['name']." × ".$bonuses_data['unit']."<br />";
				}
			}

			$i++;

		}

		$database->freeResult($presents_rtn);

		# INSERT 処理
		if(empty($error)){

			if($i > 0){

				# COMMIT
				$database->commit();

				$present_check				= 1;

				foreach($present_message as $key => $value){

					# ログインボーナス
					if($key == 1){
						$present_dialog		.= "<span style=\"color: #FF6699;\">【ログインボーナス】</span><br />";
					# 通常配布
					}elseif($key == 2){
						$present_dialog		.= "<span style=\"color: #FF6699;\">【プレゼント】</span><br />";
					# その他
					}else{

					}

					$present_dialog			.= $value."<br />";

				}

				$present_dialog				.= "<br />をプレゼントBOXにお届けしました！";

			}

		}

		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}



	# CAMPAIGN
	}elseif($data['page'] == "campaign"){



		$campaign_data		= $campaignsetModel->getCampaignsetData($members_data);

		print("<pre>");
		print_r($campaign_data);
		print("</pre>");





	# MAIL
	}elseif($data['page'] == "mail"){






	# ITEM
	}elseif($data['page'] == "item"){


			/************************************************
			**
			**	所持アイテム
			**	===========================================
			**	ItemBox
			**	===========================================
			**	itemsとJOINして所持アイテム情報取得
			**
			************************************************/

			$item_list											= NULL;
			$item_list											= array();
			$item_list_nouse									= NULL;
			$item_list_nouse									= array();

			$itembox_conditions									= array(
				'user_id'										=> $members_data['id'],
				'status'										=> 0,
				'order'											=> 'i.name'

			);
			$itembox_rtn										= $itemboxModel->getItemboxListJoinOnItems($itembox_conditions);

			$i=0;
			$j=0;
			while($itembox_data = $database->fetchAssoc($itembox_rtn)){

				# 返信画面用しか使えません
				if($itembox_data['category'] == 0 || $itembox_data['category'] == 1){

					$item_list['id'][$i]						= $itembox_data['itembox_id'];
					$item_list['unit'][$i]						= $itembox_data['itembox_unit'];

					if(!empty($itembox_data['name'])){
						$item_list['name'][$i]					= $itembox_data['name'];
					}

					if(!empty($itembox_data['image'])){
						$item_list['image'][$i]					= $itembox_data['image'];
					}

					if(!empty($itembox_data['description'])){
						$item_list['description'][$i]			= $itembox_data['description'];
					}

					$i++;

				# 使えないアイテム
				}else{

					$item_list_nouse['id'][$j]					= $itembox_data['itembox_id'];
					$item_list_nouse['unit'][$j]				= $itembox_data['itembox_unit'];

					if(!empty($itembox_data['name'])){
						$item_list_nouse['name'][$j]			= $itembox_data['name'];
					}

					if(!empty($itembox_data['image'])){
						$item_list_nouse['image'][$j]			= $itembox_data['image'];
					}

					if(!empty($itembox_data['description'])){
						$item_list_nouse['description'][$j]		= $itembox_data['description'];
					}

					$j++;

				}

			}

			$database->freeResult($itembox_rtn);

			pr($item_list);
			pr($item_list_nouse);



	}elseif($data['page'] == "itemuse"){

		$itemuse_list										= NULL;
		$itemuse_list										= array();
		$itemuse_check										= array();

		$itemuse_conditions									= array(
			'user_id'										=> $members_data['id'],
			'character_id'									=> 5,
			'status'										=> 0,
			'order'											=> 'i.id'
		);

		$itemuse_list										= $itemuseModel->checkItemUseLimit($itemuse_conditions);

		pr($itemuse_list);


	}elseif($data['page'] == "ranking"){

		$event_id						= 1;
		$consumption_ranking			= 2;

		$ranking_conditions				= array();
		$ranking_conditions				= array(
			'user_id'					=> $members_data['id'],
			'character_id'				=> 5,
			'point'						=> $consumption_ranking,
			'event_id'					=> $event_id
		);

		$rankingModel->insertRanking($ranking_conditions);



	}elseif($data['page'] == "rollback"){

		$database->beginTransaction();

		$tests_insert1			= array();
		$tests_insert1			= array(
			"category"			=> 1,
			"title"				=> "title1",
			"message"			=> "message1",
			"status"			=> 1
		);

		$insert_id1				= $database->insertDb("tests",$tests_insert1);

		print($insert_id1."<br />");

		$test_update1					= array();
		$test_update1					= array(
			'category'					=> 9,
			"title"						=> "upd_title1",
			"message"					=> "upd_message1",
		);
		$test_update_where1				= "id = :id";
		$test_update_conditions1[':id']	= 1;

		//$database->multiUpdateDb("tests",$test_update1,$test_update_where1,$test_update_conditions1);


		$tests_insert2			= array();
		$tests_insert2			= array(
			"type"				=> 2,
			"title"				=> "title2",
			"message"			=> "message2",
			"status"			=> 2
		);

		$insert_id2				= $database->insertDb("tests",$tests_insert2);

		print($insert_id2."<br />");

		$test_update2					= array();
		$test_update2					= array(
			'test'						=> 10,
			"title"						=> "upd_title2",
			"message"					=> "upd_message2",
		);
		$test_update_where2				= "id = :id";
		$test_update_conditions2[':id']	= 2;

		//$database->multiUpdateDb("tests",$test_update2,$test_update_where2,$test_update_conditions2);


		# 一気に処理
		$database->commit();



	}elseif($data['page'] == "tran"){



		$tests_insert1			= array();
		$tests_insert1			= array(
			"category"			=> 1,
			"title"				=> "title1",
			"message"			=> "message1",
			"status"			=> 1
		);

		$database->multiInsertDb("tests",$tests_insert1);

		$test_update1					= array();
		$test_update1					= array(
			'category'					=> 9,
			"title"						=> "upd_title1",
			"message"					=> "upd_message1",
		);
		$test_update_where1				= "id = :id";
		$test_update_conditions1[':id']	= 1;

		$database->multiUpdateDb("tests",$test_update1,$test_update_where1,$test_update_conditions1);


		$tests_insert2			= array();
		$tests_insert2			= array(
			"category"			=> 2,
			"title"				=> "title2",
			"message"			=> "message2",
			"status"			=> 2
		);

		$database->multiInsertDb("tests",$tests_insert2);

		$test_update2					= array();
		$test_update2					= array(
			'test'						=> 10,
			"title"						=> "upd_title2",
			"message"					=> "upd_message2",
		);
		$test_update_where2				= "id = :id";
		$test_update_conditions2[':id']	= 2;

		$database->multiUpdateDb("tests",$test_update2,$test_update_where2,$test_update_conditions2);

		# 一気に処理
		$database->multiExection();


	}

	$mainClass->outputDebugSystem();
	exit();

}


################################## FILE END #####################################
?>