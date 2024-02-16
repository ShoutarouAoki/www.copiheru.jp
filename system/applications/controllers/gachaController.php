<?php
################################ FILE MANAGEMENT ################################
##
##	gachaController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	PRESENTBOX PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	PRESENTBOX PAGE 各種処理
##
##	page : index		-> ガチャTOPページ
##	page : start		-> スタートページ
##	page : lottery		-> 抽選
##	page : acceptance	-> 受け取り処理
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

/** GACHA MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/GachaModel.php");

/** SHOP MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ShopModel.php");

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

/** PAY MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PayModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");


################################# POST ARRAY ####################################

$value_array				= array('page','type','pay','campaign_id');
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

# TYPE
if(!empty($_POST['type'])){
	$data['type']			= $_POST['type'];
}

# PAY
if(!empty($_POST['pay'])){
	$data['pay']			= $_POST['pay'];
}

# CAMPAIGN ID
if(!empty($_POST['campaign_id'])){
	$data['campaign_id']	= $_POST['campaign_id'];
}

if(empty($data['campaign_id'])){
	$data['campaign_id']	= 0;
}

# PID
$pays_id					= 0;
if(!empty($_POST['pays_id'])){
	$pays_id				= $_POST['pays_id'];
}


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# GACHA MODEL
$gachaModel					= new GachaModel($database,$mainClass);

# SHOP MODEL
$shopModel					= new ShopModel($database,$mainClass);

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);

# PAY MODEL
$payModel					= new PayModel($database,$mainClass);

# CAMPAIGNSET MODEL
$campaignsetModel			= new CampaignsetModel($database,$mainClass);

##################################### FUNCTIONS ######################################

/** FUNCTIONS FILE **/
require_once(dirname(__FILE__)."/functions/gachaController.inc");

require_once(dirname(__FILE__)."/functions/commonFunc.php");


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
	**	ガチャトップ
	**
	************************************************/

	if($data['page'] == "index"){

		/************************************************
		**
		**	画像参照に使う入金有無データ
		**
		************************************************/
		# 入有り
		if($members_data['pay_count'] > 0)
			$pay_flg = 1;
		# 入無し
		else
			$pay_flg = 2;
		
		/************************************************
		**
		**	画像参照に使うポイント購入有無データ
		**
		************************************************/
		$buy_data = checkMemberBuyFlag($members_data);//ポイント購入ユーザのチェック

		/*print_r($pays_data['id']);
		if($members_data['pay_count'] <= 0){
			$buy_flg = 2;
		}else{*/
			//有り
			if(!empty($buy_data['id'])){
				$buy_flg = 1;
			//無し
			}else{
				$buy_flg = 2;
			}
		//}

		/************************************************
		**
		**	ユーザのガチャポイント数取得
		**
		************************************************/
		# 所持ガチャポイント
		$gachapoint = 0;
		$gachapoint_itemboxid = NULL;

		# まずガチャポイントを確認
		$item_table	= "items";
		$item_select	= "*";
		$item_where	= "site_cd = :site_cd AND effect = :effect AND status = :status";
		$item_array = array();
		$item_array[':site_cd'] = $members_data["site_cd"];
		$item_array[':effect'] = 4;
		$item_array[':status'] = 0;
		$item_rtn		= $database->selectDb($item_table,$item_select,$item_where,$item_array,$item_order=NULL,1);
		$database->errorDb($item_table, $item_rtn->errorCode(),__FILE__,__LINE__);
		$gachapo_data = $database->fetchAssoc($item_rtn);

		# ガチャポイントあり
		if(!empty($gachapo_data['id'])){

			# ガチャポイント名
			$gachapo_name							= $gachapo_data['name'];

			# マニーアイテム画像
			$gachapo_image							= HTTP_ITEM_IMAGE."/".$gachapo_data['image'];

			# ユーザーがそのアイテム持ってるかチェック
			$itembox_conditions					= array();
			$itembox_conditions					= array(
				'user_id'						=> $members_data['id'],
				'item_id'						=> $gachapo_data['id'],
				'status'						=> 0
			);
			$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

			# 持ってる
			if(!empty($itembox_data['id']) || $itembox_data['unit']){
				$gachapoint = $itembox_data['unit'];
				$gachapoint_itemboxid = $itembox_data['id'];

			}
		}

		
		/************************************************
		**
		**	ユーザ所有ガチャポイント減算
		**
		************************************************/
		/*
		# UPDATE / itembox
		$itembox_table					= "itembox";
		$itembox_update					= array();
		$itembox_update					= array(
			'unit' => $gachapoint -
		);
		$itembox_update_where				= "id = :id";
		$itembox_update_conditions[':id']	= $gachapoint_itemboxid;
		$database->updateDb($itembox_table,$itembox_update,$itembox_update_where,$itembox_update_conditions);

		*/
		## 20180614 add by A.cos(START)
		/************************************************
		**
		**	PC環境のみガチャリトライ
		**
		************************************************/
		/*
		if($default_device === "pc"){
		
			# 引き終わってないガチャチェック
			##################
			# ノーマル
			##################
			$pays_conditions						= array(
				'user_id'							=> $members_data['id'],
				'settlement_id'						=> $point_no_array[$point_name_array['gacha_coin']][2],
				'clear'								=> 1,
				'finish'							=> 0,
				'error'								=> 0,
				'status'							=> 0
			);

			$check_retry_data						= $payModel->getPayData($pays_conditions);

			# ガチャリトライ
			if(!empty($check_retry_data['id'])){

				# キャンペーンあれば
				if(!empty($check_retry_data['campaign_id'])){

					$pointsets_conditions			= array(
						'site_cd'					=> $members_data['site_cd'],
						'point_no_id'				=> $point_no_array[$point_name_array['gacha_coin']][2],
						'campaign_id'				=> $check_retry_data['campaign_id'],
					);

				# 通常
				}else{

					# 無課金
					if($members_data['pay_count'] == 0){
						$pay_flg					= 2;
					# 課金
					}else{
						$pay_flg					= 1;
					}

					$pointsets_conditions			= array(
						'site_cd'					=> $members_data['site_cd'],
						'point_no_id'				=> $point_no_array[$point_name_array['gacha_coin']][2],
						'sex'						=> $members_data['sex'],
						'pay_flg'					=> $pay_flg
					);

				}

				$pointsets_data						= $pointsetModel->getPointsetData($pointsets_conditions);

				# SINGLE(単発)かMULTI(10連)か
				$multi_point						= $pointsets_data['point'] * $gacha_loop_array['multi'];

				if($check_retry_data['pay_amount'] == $pointsets_data['point']){
					$page_type						= "single";
				}elseif($check_retry_data['pay_amount'] == $multi_point){
					$page_type						= "multi";
				}else{
					$page_type						= "single";
				}

				# PATAMETER
				$redirect_parametar					 = "?opensocial_app_id=".APP_ID;
				$redirect_parametar					.= "&opensocial_viewer_id=".$members_data['user_id'];
				$redirect_parametar					.= "&paymentId=".$check_retry_data['sid'];

				# REDIRECT
				$mainClass->redirect("/gacha/retry/".$page_type."/coin/".$check_retry_data['campaign_id']."/".$redirect_parametar);
				exit();

			}

			##################
			# 限定ガチャ
			##################
			$pays_conditions_limitted				= array(
				'user_id'							=> $members_data['id'],
				'settlement_id'						=> $point_no_array[$point_name_array['limitted_gacha_coin']][2],
				'clear'								=> 1,
				'finish'							=> 0,
				'error'								=> 0,
				'status'							=> 0
			);

			$check_retry_data_limitted						= $payModel->getPayData($pays_conditions_limitted);

			# ガチャリトライ
			if(!empty($check_retry_data_limitted['id'])){
				# PAGETYPE
				$page_type						= "limitted1";
				
				# PATAMETER
				$redirect_parametar					 = "?opensocial_app_id=".APP_ID;
				$redirect_parametar					.= "&opensocial_viewer_id=".$members_data['user_id'];
				$redirect_parametar					.= "&paymentId=".$check_retry_data_limitted['sid'];

				# REDIRECT
				$mainClass->redirect("/gacha/retry/".$page_type."/coin/".$check_retry_data_limitted['campaign_id']."/".$redirect_parametar);
				exit();

			}

			##################
			# ステップアップガチャ
			##################
			$pays_conditions_stepup				= array(
				'user_id'							=> $members_data['id'],
				'settlement_id'						=> $point_no_array[$point_name_array['stepup_gacha_coin']][2],
				'clear'								=> 1,
				'finish'							=> 0,
				'error'								=> 0,
				'status'							=> 0
			);

			$check_retry_data_stepup						= $payModel->getPayData($pays_conditions_stepup);

			# ガチャリトライ
			if(!empty($check_retry_data_stepup['id'])){
				# PAGETYPE
				$page_type						= "stepup1";
				
				# PATAMETER
				$redirect_parametar					 = "?opensocial_app_id=".APP_ID;
				$redirect_parametar					.= "&opensocial_viewer_id=".$members_data['user_id'];
				$redirect_parametar					.= "&paymentId=".$check_retry_data_stepup['sid'];

				# REDIRECT
				$mainClass->redirect("/gacha/retry/".$page_type."/coin/".$check_retry_data_stepup['campaign_id']."/".$redirect_parametar);
				exit();

			}
		}
		*/
		## 20180614 add by A.cos(END)

		/************************************************
		**
		**	通常
		**	============================================
		**
		**
		************************************************/

		# 無料ガチャ
		$free_gacha									= NULL;

		if($members_data['last_gacha_date'] < date("Y-m-d")){
			$free_gacha								= 1;
		}

		# LOOP
		$loop_single								= $gacha_loop_array['single'];
		$loop_multi									= $gacha_loop_array['multi'];

		# POINT NO
		$point_no_id								= $point_no_array[$point_name_array['gacha_point']][2];

		# pointsets
		$pointsets_data								= $pointsetModel->getPointset($point_no_id,$members_data,NULL);

		$point_data['gacha']						= DEFAULT_GACHA_POINT;

		if(!empty($pointsets_data)){

			$count									= count($pointsets_data);
			for($i=0;$i<$count;$i++){

				# ガチャポイント
				if($pointsets_data[$i]['point_no_id'] == $point_no_id){
					$point_data['gacha']			= $pointsets_data[$i]['point'];
				}

			}

		}

		# 回数分
		$point_single								= $point_data['gacha'] * $loop_single;
		$point_multi								= $point_data['gacha'] * $loop_multi;

		$check_point_single							= NULL;
		$check_point_multi							= NULL;

		if($members_data['total_point'] < $point_single){
			$check_point_single						= 1;
		}

		if($members_data['total_point'] < $point_multi){
			$check_point_multi						= 1;
		}


		/************************************************
		**
		**	キャンペーン
		**	============================================
		**
		**
		************************************************/
		list($campaign_id, $campaign_point, $campaign_data) = getCampaignDataForIndex($campaignsetModel, $members_data);//20170915 updated by  A.cos(replace function)

		/************************************************
		**
		**	キャンペーンIDを取得したので、ステップアップガチャの回転数を取得
		**	============================================
		**
		**
		************************************************/

		//20180314 add by A.cos 単発と10連しかなかったときの仕様にあわせてデフォは1としておく
		# 限定ガチャ
		$stepup_data = NULL;
		$stepup_use_rest = 0;
		$stepup_use_max = 0;
		if(!empty($campaign_data['id'])){
			list($stepup_data, $stepup_use_rest, $stepup_use_max) = getStepupGachaSettingData($gachaModel, $members_data["id"], $campaign_data['id'], $stepup_gacha_number["limitted1"]);
			if(isset($stepup_data["id"])){
				$loop_limitted1 = $stepup_data["times"];
				$abs_limitted1 = $stepup_data["abs_num"];
				$rest_limitted1 = $stepup_use_rest;
				$max_limitted1 = $stepup_use_max;
			}
		}

		# ステップアップガチャ
		$stepup_data = NULL;
		$stepup_use_rest = 0;
		$stepup_use_max = 0;
		if(!empty($campaign_data['id'])){
			list($stepup_data, $stepup_use_rest,$stepup_use_max) = getStepupGachaSettingData($gachaModel, $members_data["id"], $campaign_data['id'], $stepup_gacha_number["stepup1"]);
			if(isset($stepup_data["id"])){
				$loop_stepup1 = $stepup_data["times"];
				$abs_stepup1 = $stepup_data["abs_num"];
				$rest_stepup1 = $stepup_use_rest;
				$max_stepup1 = $stepup_use_max;
			}
		}

		/************************************************
		**
		**	固定シングルガチャ向け、ガチャポイント可不可フラグ
		**
		************************************************/
		//ガチャポイント
		$single_gachapo	= 0;
		if(($gachapoint - GACHA_SERVICE_POINT*$loop_single)>=0){
			$single_gachapo	= GACHA_SERVICE_POINT*$loop_single;
		}
		
		/************************************************
		**
		**	キャンペーンバナー / キャンペーンポイント設定取得
		**
		************************************************/

		if(!empty($campaign_point) || !empty($campaign_id)){


			# POINT
			if(!empty($campaign_point)){

				# pointsets
				$campaign_pointsets_data				= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_point);

				$campaign_point_data['gacha']			= DEFAULT_GACHA_POINT;

				if(!empty($campaign_pointsets_data)){

					$count								= count($campaign_pointsets_data);
					for($i=0;$i<$count;$i++){

						# ガチャポイント
						if($campaign_pointsets_data[$i]['point_no_id'] == $point_no_id){
							$campaign_point_data['gacha']	= $campaign_pointsets_data[$i]['point'];
						}

					}

				}

				# 回数分
				$campaign_point_single					= $campaign_point_data['gacha'] * $loop_single;
				$campaign_point_multi					= $campaign_point_data['gacha'] * $loop_multi;

				$check_campaign_point_single			= NULL;
				$check_campaign_point_multi				= NULL;

				if($members_data['total_point'] < $campaign_point_single){
					$check_campaign_point_single		= 1;
				}

				if($members_data['total_point'] < $campaign_point_multi){
					$check_campaign_point_multi			= 1;
				}

				//20180314 add by A.cos
				$campaign_point_stepup = array();
				$check_campaign_point_stepup = array();
				$check_stepup_gacha_abs = array();
				$check_stepup_gacha_rest = array();
				$check_stepup_gacha_max = array();
				# 限定
				if(!empty($loop_limitted1)){
					$campaign_point_stepup["limitted1"] = $campaign_point_data['gacha'] * $loop_limitted1;
					if($members_data['total_point'] < $campaign_point_stepup["limitted1"]){
						$check_campaign_point_stepup["limitted1"]			= 1;
					}
					$check_stepup_gacha_times["limitted1"] = $loop_limitted1;
					$check_stepup_gacha_abs["limitted1"] = $abs_limitted1;
					$check_stepup_gacha_rest["limitted1"] = $rest_limitted1;
					$check_stepup_gacha_max["limitted1"] = $max_limitted1;
				}
				
				# ステップアップ
				if(!empty($loop_stepup1)){
					$campaign_point_stepup["stepup1"] = $campaign_point_data['gacha'] * $loop_stepup1;
					if($members_data['total_point'] < $campaign_point_stepup["stepup1"]){
						$check_campaign_point_stepup["stepup1"]			= 1;
					}
					$check_stepup_gacha_times["stepup1"] = $loop_stepup1;
					$check_stepup_gacha_abs["stepup1"] = $abs_stepup1;
					$check_stepup_gacha_rest["stepup1"] = $rest_stepup1;
					$check_stepup_gacha_max["stepup1"] = $max_stepup1;
				}

			# POINT設定なければ通常データで上書き
			}else{

				$campaign_point_single					= $point_single;
				$campaign_point_multi					= $point_multi;
				$check_campaign_point_single			= $check_point_single;
				$check_campaign_point_multi				= $check_point_multi;

			}

			/************************************************
			**
			**	キャンペーンボタンバナー 設定取得
			**
			************************************************/

			# BUTTON
			$button_conditions						= array();
			$button_conditions						= array(
				'file_type'							=> 6,
				'category'							=> $button_image_category,
				'site_cd'							=> $members_data['site_cd'],
				'target_id'							=> $campaign_data['id'],
				'status'							=> 0,
				'order'								=> "id DESC"
			);

			$button_rtn								= $imageModel->getImageList($button_conditions);

			$i=0;
			while($button_data = $database->fetchAssoc($button_rtn)){

				$button_list['id'][$i]				= $button_data['id'];
				$button_list['image'][$i]			= $button_data['img_name'];
				$button_list['link'][$i]			= $button_data['img_key'];
				$button_list['target_id'][$i]		= $button_data['target_id'];

				# PAYMENT
				if(!empty($button_data['content'])){
					$button_list['content'][$i]		= $button_data['content'];
				}else{
					$button_list['content'][$i]		= NULL;
				}

				# SINGLE
				if($button_data['img_key'] == "single"){

					if(!empty($check_campaign_point_single)){
						$button_list['point'][$i]	= NULL;
					}else{
						$button_list['point'][$i]	= $campaign_point_single;
					}

					//ガチャポイント可不可フラグ
					$button_list['gachapo'][$i]	= 0;
					if(($gachapoint - GACHA_SERVICE_POINT*$loop_single)>=0){
						$button_list['gachapo'][$i]	= GACHA_SERVICE_POINT*$loop_single;
					}

					$button_list['gachacount'][$i]	= $loop_single;
					
				}elseif($button_data['img_key'] == "multi"){

					if(!empty($check_campaign_point_multi)){
						$button_list['point'][$i]	= NULL;
					}else{
						$button_list['point'][$i]	= $campaign_point_multi;
					}

					//ガチャポイント可不可フラグ
					$button_list['gachapo'][$i]	= 0;
					if(($gachapoint - GACHA_SERVICE_POINT*$loop_multi)>=0){
						$button_list['gachapo'][$i]	= GACHA_SERVICE_POINT*$loop_multi;
					}
					
					$button_list['gachacount'][$i]	= $loop_multi;

				}elseif($button_data['img_key'] == "limitted1"){

					if(!empty($check_campaign_point_stepup["limitted1"])){
						$button_list['point'][$i]	= NULL;
					}else{
						if(isset($campaign_point_stepup["limitted1"]))
							$button_list['point'][$i]	= $campaign_point_stepup["limitted1"];
						else
							$button_list['point'][$i]	= NULL;
					}
					//20180319 add by A.cos
					if(!empty($check_stepup_gacha_rest["limitted1"])){
						$button_list['rest'][$i]	= $check_stepup_gacha_rest["limitted1"];
					}else{
						$button_list['rest'][$i]	= 0;
					}
					//20180322 add by A.cos
					if(!empty($check_stepup_gacha_max["limitted1"])){
						$button_list['max'][$i]	= $check_stepup_gacha_max["limitted1"];
					}else{
						$button_list['max'][$i]	= 0;
					}
					//20180323 add by A.cos
					if(!empty($check_stepup_gacha_abs["limitted1"])){
						$button_list['abs'][$i]	= $check_stepup_gacha_abs["limitted1"];
					}else{
						$button_list['abs'][$i]	= 0;
					}
					if(!empty($check_stepup_gacha_times["limitted1"])){
						$button_list['times'][$i]	= $check_stepup_gacha_times["limitted1"];
					}else{
						$button_list['times'][$i]	= 0;
					}

				}elseif($button_data['img_key'] == "stepup1"){

					if(!empty($check_campaign_point_stepup["stepup1"])){
						$button_list['point'][$i]	= NULL;
					}else{
						if(isset($campaign_point_stepup["stepup1"]))
							$button_list['point'][$i]	= $campaign_point_stepup["stepup1"];
						else
							$button_list['point'][$i]	= NULL;
					}

					//20180319 add by A.cos
					if(!empty($check_stepup_gacha_rest["stepup1"])){
						$button_list['rest'][$i]	= $check_stepup_gacha_rest["stepup1"];
					}else{
						$button_list['rest'][$i]	= 0;
					}
					//20180322 add by A.cos
					if(!empty($check_stepup_gacha_max["stepup1"])){
						$button_list['max'][$i]	= $check_stepup_gacha_max["stepup1"];
					}else{
						$button_list['max'][$i]	= 0;
					}
					//20180323 add by A.cos
					if(!empty($check_stepup_gacha_abs["stepup1"])){
						$button_list['abs'][$i]	= $check_stepup_gacha_abs["stepup1"];
					}else{
						$button_list['abs'][$i]	= 0;
					}
					if(!empty($check_stepup_gacha_times["stepup1"])){
						$button_list['times'][$i]	= $check_stepup_gacha_times["stepup1"];
					}else{
						$button_list['times'][$i]	= 0;
					}
				}

				$i++;

			}

			$database->freeResult($button_rtn);

		}


		//20181011 add by A.cos
		/************************************************
		**
		**	ガチャ確率一覧取得
		**
		************************************************/
		# まずガチャポイントを確認
		$gp_table	= "gacha_prizes";
		$gp_select	= "*";
		$gp_where	= "site_cd = :site_cd";
		$gp_array = array();
		$gp_array[':site_cd'] = $members_data["site_cd"];
		$gp_order = "id";
		$gp_limit = 1;
		$gp_rtn		= $database->selectDb($gp_table,$gp_select,$gp_where,$gp_array,$gp_order,$gp_limit);
		$database->errorDb($gp_table, $gp_rtn->errorCode(),__FILE__,__LINE__);
		$gp_data = $database->fetchAssoc($gp_rtn);
		
		
		/************************************************
		**
		**	ページトップバナー 設定取得
		**
		************************************************/

		# BANNER
		$banner_conditions							= array();
		$banner_conditions							= array(
			'file_type'								=> 5,
			'category'								=> $banner_image_category,
			'device'								=> $device_number,
			'site_cd'								=> $members_data['site_cd'],
			'target_id'								=> 0,
			'display_check'							=> 1,
			'payment'								=> $pay_flg,
			'buy'									=> $buy_flg,
			'status'								=> 0
		);

		$banner_rtn									= $imageModel->getImageList($banner_conditions);

		$i=0;
		while($banner_data = $database->fetchAssoc($banner_rtn)){

			$banner_list['id'][$i]					= $banner_data['id'];
			$banner_list['image'][$i]				= $banner_data['img_name'];
			$banner_list['link'][$i]				= $banner_data['img_key'];
			$i++;

		}

		$database->freeResult($banner_rtn);



	/************************************************
	**
	**	BUY
	**	============================================
	**	PF通過でガチャ引く場合
	**	一旦プラットフォームの決済ページへ遷移
	**
	************************************************/

	}elseif($data['page'] == "buy"){

		# LOOP回数
		$loop									= 0;

		# ガチャループタイプ("limitted1", "stepup1"は１とする)
		if(!empty($data['type'])){
			$loop								= $gacha_loop_array[$data['type']];
		}else{
			$error								= 21;
			$errormessage						= "不正なアクセスです";
		}

		# PAGE CHECK
		if(empty($data['pay']) || $data['pay'] != "coin"){
			$error								= 22;
			$errormessage						= "不正なアクセスです";
		}

		# ガチャ抽選ループは別に保持
		$gacha_loop								= $loop;

		# 10連は1回追加
		if($data['type'] == "multi"){
			$gacha_loop							= $gacha_loop_array['multi_loop'];
		}
		# $gacha_loop、使ってない（－－；

		# OK
		if(empty($error)){


			/************************************************
			**
			**	キャンペーン
			**	============================================
			**
			**
			************************************************/

			# CAMPAIGN
			$campaign_id						= 0;
			$campaign_point						= NULL;

			if(!empty($data['campaign_id'])){
				list($campaign_id, $campaign_point, $campaign_data) = getCampaignData($campaignsetModel, $data);//20170915 updated by  A.cos(replace function)
			}


			/************************************************
			**
			**	pointsets
			**	============================================
			**	pointsetsデータ取得
			**	ガチャ消費ポイント
			**
			************************************************/

			$gacha_point						= 0;
			$unit_price							= 0;

			# POINT NO
			$point_no_point						= $point_no_array[$point_name_array['gacha_point']][2];
			$point_no_coin						= $point_no_array[$point_name_array['gacha_coin']][2];
			$point_no_id						= $point_no_point.",".$point_no_coin;

			# pointsets
			$pointsets_data						= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

			$point_data['gacha_point']			= DEFAULT_GACHA_POINT;
			$point_data['gacha_coin']			= DEFAULT_GACHA_POINT;

			if(!empty($pointsets_data)){

				$count							= count($pointsets_data);
				for($i=0;$i<$count;$i++){

					# ポイント
					if($pointsets_data[$i]['point_no_id'] == $point_no_point){
						$point_data['gacha_point']	= $pointsets_data[$i]['point'];
					}

					# コイン
					if($pointsets_data[$i]['point_no_id'] == $point_no_coin){
						$point_data['gacha_coin']	= $pointsets_data[$i]['point'];
					}

				}

			}

			# ガチャを引く為に必要なサイト内ポイント数
			$gacha_point						= $point_data['gacha_point'] * $loop;

			# ガチャを引く為に必要なPF通貨数
			$unit_price							= $point_data['gacha_coin'];

			//"limitted1", "stepup1"の場合はDBから値段を取り出す
			switch($data['type']){
				case "limitted1":
				case "limitted2":
				case "limitted3":
				case "stepup1":
				case "stepup2":
				case "stepup3":
					$stepup_data = NULL;
					$stepup_use_rest=0;
					$stepup_use_max = 0;
					list($stepup_data,$stepup_use_rest,$stepup_use_max) = getStepupGachaSettingData($gachaModel, $members_data["id"], $campaign_id, $stepup_gacha_number[$data['type']]);
					if(isset($stepup_data["id"]))
						$unit_price = $stepup_data["price"];
					break;
			}

			# ITEM DATA
			$item_data							= array();
			$item_data							= array(
				'itemId'						=> GACHA_ITEM_ID,
				'itemName'						=> GACHA_ITEM_NAME,
				'unitPrice'						=> $unit_price,
				'quantity'						=> $loop,
				'imageUrl'						=> HTTP_DOMAIN."/images/icon/icon-gacha.jpg",
				'description'					=> GACHA_ITEM_DESCRIPTION,
				'callbackUrl'					=> HTTP_SETTLEMENT."/".$directory."/".$data['type']."/".$campaign_id."/",
				'finishPageUrl'					=> HTTP_DOMAIN."/".$directory."/start/".$data['type']."/coin/".$campaign_id."/"
			);

			# 決済
			$item_result						= $authClass->createPaymentObjectFromNijiyomeByUserId($members_data['user_id'],$item_data);

			# ERROR
			if(!empty($item_result['error'])){

				$error							= $item_result['error'];
				$errormessage					= $item_result['message'];

			# OK
			}else{

				# ユーザーキャンセル
				if($item_result['entry']['status'] == 3){

					$error						= $item_result['entry']['status'];

					# REDIRECT
					$mainClass->redirect("/".$directory."/");
					exit();


				# 期限切れ
				}elseif($item_result['entry']['status'] == 4){

					$error						= $item_result['entry']['status'];
					$errormessage				= "お申込頂いた".COIN_NAME."決済の期限が切れております。<br />お手数ですが再度お試し下さい。";

				# HTTPステータスキャンセル
				}elseif($item_result['entry']['status'] == 5){

					$error						= $item_result['entry']['status'];
					$errormessage				= "決済処理でエラーが起こった可能性があります。<br />お問い合わせよりご連絡下さい。";

				}

				# 正常処理
				if(empty($error) && $item_result['entry']['status'] == 1){

					$transaction_url			= $item_result['entry']['transactionUrl'];

					# 決済画面へREDIRECT
					$mainClass->redirect($transaction_url);
					exit();

				}

			}

		}



	/************************************************
	**
	**	START/RETRY
	**	============================================
	**	スタートページ/リトライスタートページ(最後尾のエラー処理が違う)
	**
	************************************************/
	}elseif($data['page'] == "start" || $data['page'] == "retry"){

		# このページではヘッダーとフッターを消す(スマフォ)
		$header_hide									= 1;
		$footer_hide									= 1;

		# サブヘッダーとサブフッターを表示
		$sub_header										= 1;
		$sub_footer										= 1;

		# 初期化
		$error											= NULL;
		$errormessage									= NULL;

		# LOOP回数
		$loop											= 0;

		# ガチャループタイプ
		if(!empty($data['type'])){
			$loop										= $gacha_loop_array[$data['type']];
		}else{
			$error										= 21;
			$errormessage								= "不正なアクセスです";
		}


		# 無料ガチャ処理
		if($data['type'] == "free"){

			if($members_data['last_gacha_date'] == date("Y-m-d")){
				$error									= 1;
				$errormessage							= "既に本日の無料ガチャはご利用済みです";
			}


		}else{


			# チケットで処理
			if($data['pay'] == "point"){


				/************************************************
				**
				**	キャンペーン
				**	============================================
				**
				**
				************************************************/

				# CAMPAIGN
				$campaign_id							= 0;
				$campaign_point							= NULL;
				
				list($campaign_id, $campaign_point, $campaign_data) = getCampaignData($campaignsetModel, $data);//20170915 updated by  A.cos(replace function)

				# POINT NO
				$point_no_id							= $point_no_array[$point_name_array['gacha_point']][2];

				# pointsets
				$pointsets_data							= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_point);

				$point_data['gacha']					= DEFAULT_GACHA_POINT;

				if(!empty($pointsets_data)){

					$count								= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# ガチャポイント
						if($pointsets_data[$i]['point_no_id'] == $point_no_id){
							$point_data['gacha']		= $pointsets_data[$i]['point'];
						}

					}

				}

				# 回数分
				$point_gacha							= $point_data['gacha'] * $loop;

				if($members_data['total_point'] < $point_gacha){
					$error								= 42;
					$errormessage						= TICKET_NAME."が足りません。";
				}


			# PFの決済で処理
			}elseif($data['pay'] == "coin"){


				/************************************************
				**
				**	settlement check
				**	============================================
				**	正常に決済処理が終わってるかチェック
				**
				**	戻り値
				**	[opensocial_app_id] => 445
				**	[opensocial_owner_id] => 4151
				**	[opensocial_viewer_id] => 4151
				**	[paymentId] => 445-4151-20160905184013077433
				**
				************************************************/

				# 初期化
				$add_path								= NULL;

				# APP IDがあるか
				if(empty($_REQUEST['opensocial_app_id'])){

					$error								= 1;
					$errormessage						= "正常に処理ができません";

				# APP IDが一致するか
				}elseif(!empty($_REQUEST['opensocial_app_id']) && $_REQUEST['opensocial_app_id'] != APP_ID){

					$error								= 2;
					$errormessage						= "正常に処理ができません";

				# USER IDがない
				}elseif(empty($_REQUEST['opensocial_viewer_id'])){

					$error								= 3;
					$errormessage						= "お客様情報が確認できません";

				# USER ID が一致するか
				}elseif($_REQUEST['opensocial_viewer_id'] != $members_data['user_id']){

					$error								= 4;
					$errormessage						= "お客様情報が確認できません";

				# PAYMENT IDがない
				}elseif(empty($_REQUEST['paymentId'])){

					$error								= 5;
					$errormessage						= COIN_NAME."の処理が正常ではありません";

				}

				# チェック
				if(empty($error)){

					# PF側の決済情報をチェック
					$payment_result						= $authClass->checkPaymentObjectFromNijiyomeByUserId($members_data['user_id'],$_REQUEST['paymentId']);

					# 通信OK
					if(empty($payment_result['error'])){

						# ステータスが決済完了以外だったらエラー
						if(!empty($payment_result['entry']['status']) &&  $payment_result['entry']['status'] != 2){

							# 申し込みで止まってる
							if($payment_result['entry']['status'] == 1){

								$error					= 11;
								$errormessage			= "お申込頂いた".COIN_NAME."決済処理が正常に終了しておりません。<br />お手数ですが当ページをリロードしてください。";

								$params					 = "?opensocial_app_id=".$_REQUEST['opensocial_app_id'];
								$params					.= "&opensocial_owner_id=".$_REQUEST['opensocial_owner_id'];
								$params					.= "&opensocial_viewer_id=".$_REQUEST['opensocial_viewer_id'];
								$params					.= "&paymentId=".$_REQUEST['paymentId'];


							# ユーザーキャンセル -> こいつはリダイレクト
							}elseif($payment_result['entry']['status'] == 3){

								$error					= 33;

								# REDIRECT
								$mainClass->redirect("/".$directory."/");
								exit();

							# 期限切れ
							}elseif($payment_result['entry']['status'] == 4){

								$error					= 44;
								$errormessage			= "お申込頂いた".COIN_NAME."決済の期限が切れております。<br />お手数ですが再度お試し下さい。";

							# HTTPステータスキャンセル
							}elseif($payment_result['entry']['status'] == 5){

								$error					= 55;
								$errormessage			= "決済処理でエラーが起こった可能性があります。<br />お問い合わせよりご連絡下さい。";

							}

						}

						# 決済OK
						if(empty($error)){

							# SETTLEMENT ID
							//20180315 update by A.cos
							switch($data['type']){
								case "limitted1":
								case "limitted2":
								case "limitted3":
									$settlement_id				= $point_no_array[$point_name_array['limitted_gacha_coin']][2];
									break;
								case "stepup1":
								case "stepup2":
								case "stepup3":
									$settlement_id				= $point_no_array[$point_name_array['stepup_gacha_coin']][2];
									break;
								default:
									$settlement_id				= $point_no_array[$point_name_array['gacha_coin']][2];
									break;
							}
							
							//$settlement_id				= $point_no_array[$point_name_array['gacha_coin']][2];

							# 決済情報チェック
							$pays_conditions			= array();
							$pays_conditions			= array(
								'user_id'				=> $members_data['id'],
								"settlement_id"			=> $settlement_id,
								'sid'					=> $_REQUEST['paymentId'],
								'clear'					=> 1,
								'error'					=> 0,
								'status'				=> 0
							);

							$pays_data					= $payModel->getPayData($pays_conditions);

							# 決済データOK / 持ちポイントチェック
							if(!empty($pays_data['id'])){

								# 既にこの決済IDでガチャを引いていたら強制リダイレクト
								if($pays_data['finish'] > 0){

									$error				= 12;

									# REDIRECT
									$mainClass->redirect("/".$directory."/");
									exit();

								}

								# ポイントチェック
								if($members_data['point'] < $pays_data['object']){
									$error				= 43;
									$errormessage		= COIN_NAME."が足りません。";
								}


								# 全ての処理OKならpaysのidをjavascriptに渡す
								if(empty($error)){
									$pays_id			= $pays_data['id'];
								}

							# 決済データエラー
							}else{

								$error					= 6;
								$errormessage			= COIN_NAME."の処理が正常ではありません";

							}

						}

					# 通信エラー
					}else{
						if($data['page'] != "retry"){//通常
							$error							= $payment_result['error'];
							$errormessage					= $payment_result['message'];
						}else{//通信エラーでリトライの場合
							# SETTLEMENT ID
							//20180315 update by A.cos
							switch($data['type']){
								case "limitted1":
								case "limitted2":
								case "limitted3":
									$settlement_id				= $point_no_array[$point_name_array['limitted_gacha_coin']][2];
									break;
								case "stepup1":
								case "stepup2":
								case "stepup3":
									$settlement_id				= $point_no_array[$point_name_array['stepup_gacha_coin']][2];
									break;
								default:
									$settlement_id				= $point_no_array[$point_name_array['gacha_coin']][2];
									break;
							}
							//$settlement_id				= $point_no_array[$point_name_array['gacha_coin']][2];

							# 決済情報チェック
							$pays_conditions			= array();
							$pays_conditions			= array(
								'user_id'				=> $members_data['id'],
								"settlement_id"			=> $settlement_id,
								'sid'					=> $_REQUEST['paymentId'],
								'clear'					=> 1,
								'error'					=> 0,
								'status'				=> 0
							);

							$pays_data					= $payModel->getPayData($pays_conditions);

							# 決済データOK / 持ちポイントチェック
							if(!empty($pays_data['id'])){

								# 既にこの決済IDでガチャを引いていたら強制リダイレクト
								if($pays_data['finish'] > 0){

									$error				= 12;

									# REDIRECT
									$mainClass->redirect("/".$directory."/");
									exit();

								}

								# ポイントチェック
								if($members_data['point'] < $pays_data['object']){
									$error				= 43;
									$errormessage		= COIN_NAME."が足りません。";
								}


								# 全ての処理OKならpaysのidをjavascriptに渡す
								if(empty($error)){
									$pays_id			= $pays_data['id'];
								}

							# 決済データエラー
							}else{
								$error					= 6;
								$errormessage			= COIN_NAME."の処理が正常ではありません";
							}
						}
					}
				}
			
			# ガチャポイントで処理
			}elseif($data['pay'] == "gachapo"){
				//何もしない
			}else{
				$error									= 7;
				$errormessage							= "不正なアクセスです";
			}
		}

	/************************************************
	**
	**	LOTTERY
	**	============================================
	**	ガチャ抽選処理
	**
	************************************************/

	}elseif($data['page'] == "lottery"){


		# 初期化
		$error											= NULL;
		$errormessage									= NULL;

		# 受け取りフラグ
		$acceptance										= NULL;
		$exection										= NULL;

		# メディアフラグ
		$media_flg_update								= NULL;

		# LOOP回数
		$loop											= 0;

		# ガチャループタイプ
		if(!empty($data['type'])){
			$loop										= $gacha_loop_array[$data['type']];
		}else{
			$error										= 21;
			$errormessage								= "不正なアクセスです";
		}

		# ガチャ抽選ループは別に保持
		$gacha_loop										= $loop;

		# 10連は1回追加
		if($data['type'] == "multi"){
			$gacha_loop									= $gacha_loop_array['multi_loop'];
		}

		# 無料だったらチェック
		if($data['type'] == "free"){

			if($members_data['last_gacha_date'] == date("Y-m-d")){
				$error									= 31;
				$errormessage							= "既に本日の無料ガチャはご利用済みです";
			}

		}


		# OK
		if(empty($error) && $loop > 0){


			/************************************************
			**
			**	キャンペーン
			**	============================================
			**
			**
			************************************************/

			# CAMPAIGN
			$campaign_id												= 0;
			$campaign_point												= NULL;

			if(!empty($data['campaign_id'])){
				list($campaign_id, $campaign_point, $campaign_data) = getCampaignData($campaignsetModel, $data);//20170915 updated by  A.cos(replace function)
			}


			/************************************************
			**
			**	pointsets
			**	============================================
			**	pointsetsデータ取得
			**
			************************************************/

			$point_gacha												= 0;

			# 一日一回無料
			if($data['type'] == "free"){

				$point_data['gacha']									= 0;
				$point_gacha											= $point_data['gacha'];


			# サイト内ポイント(チケット)でのガチャ
			}elseif($data['pay'] == "point"){

				# POINT NO
				//20180320 update by A.cos
				switch($data['type']){
					case "limitted1":
					case "limitted2":
					case "limitted3":
						$point_no_id				= $point_no_array[$point_name_array['limitted_gacha_point']][2];
						break;
					case "stepup1":
					case "stepup2":
					case "stepup3":
						$point_no_id				= $point_no_array[$point_name_array['stepup_gacha_point']][2];
						break;
					default:
						$point_no_id				= $point_no_array[$point_name_array['gacha_point']][2];
						break;
				}
				//$point_no_id											= $point_no_array[$point_name_array['gacha_point']][2];

				# pointsets
				$pointsets_data											= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_point);

				$point_data['gacha']									= DEFAULT_GACHA_POINT;

				if(!empty($pointsets_data)){

					$count												= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# ガチャポイント
						if($pointsets_data[$i]['point_no_id'] == $point_no_id){
							$point_data['gacha']						= $pointsets_data[$i]['point'];
						}

					}

				}

				# 回数分
				$point_gacha											= $point_data['gacha'] * $loop;

				if($members_data['total_point'] < $point_gacha){
					$error												= 42;
					$errormessage										= TICKET_NAME."が足りません。";
				}


			# PF通貨でのガチャ
			}elseif($data['pay'] == "coin"){

				# POINT NO -> ここはサイト内ガチャ消費ポイント(point_no_id = 41で出す)
				//20180320 update by A.cos
				$point_no_point	= $point_no_array[$point_name_array['gacha_point']][2];//やっぱりガチャは内部処理用コインの単価固定
				/*switch($data['type']){
					case "limitted1":
					case "limitted2":
					case "limitted3":
						$point_no_point				= $point_no_array[$point_name_array['limitted_gacha_point']][2];
						break;
					case "stepup1":
					case "stepup2":
					case "stepup3":
						$point_no_point				= $point_no_array[$point_name_array['stepup_gacha_point']][2];
						break;
					default:
						$point_no_point				= $point_no_array[$point_name_array['gacha_point']][2];
						break;
				}
				*/
				//$point_no_point											= $point_no_array[$point_name_array['gacha_point']][2];

				# PF通貨でのpoint_no_id
				//20180320 update by A.cos
				switch($data['type']){
					case "limitted1":
					case "limitted2":
					case "limitted3":
						$point_no_coin				= $point_no_array[$point_name_array['limitted_gacha_coin']][2];
						break;
					case "stepup1":
					case "stepup2":
					case "stepup3":
						$point_no_coin				= $point_no_array[$point_name_array['stepup_gacha_coin']][2];
						break;
					default:
						$point_no_coin				= $point_no_array[$point_name_array['gacha_coin']][2];
						break;
				}
				//$point_no_coin											= $point_no_array[$point_name_array['gacha_coin']][2];

				# pointsets
				$pointsets_data											= $pointsetModel->getPointset($point_no_point,$members_data,$campaign_point);

				$point_data['gacha']									= DEFAULT_GACHA_POINT;

				if(!empty($pointsets_data)){
					$count												= count($pointsets_data);
					for($i=0;$i<$count;$i++){
						# ガチャコイン
						if($pointsets_data[$i]['point_no_id'] == $point_no_point){
							$point_data['gacha']						= $pointsets_data[$i]['point'];
						}
					}
				}

				//20180316 update by A.cos
				switch($data['type']){
					case "limitted1":
					case "limitted2":
					case "limitted3":
					case "stepup1":
					case "stepup2":
					case "stepup3":
						$stepup_data = NULL;
						$stepup_use_rest=0;
						$stepup_use_max = 0;
						list($stepup_data,$stepup_use_rest,$stepup_use_max) = getStepupGachaSettingData($gachaModel, $members_data["id"], $campaign_id, $stepup_gacha_number[$data['type']]);
						//if(isset($stepup_data["id"]))
						//	$point_data['gacha'] = $stepup_data["price"];
						# 回数分
						$point_gacha											= $point_data['gacha'] * $stepup_data["times"];
						//$point_gacha											= $point_data['gacha'] * $loop;
						//mail("eikoshi@k-arat.co.jp","getStepupGachaSettingData2",var_export($stepup_data, true)."\n".$stepup_use_rest."/".$stepup_use_max,"From:info@kyabaheru.net");
						break;
					default:
						
						# 回数分
						$point_gacha											= $point_data['gacha'] * $loop;
						break;
				}
				/*
				# 回数分
				$point_gacha											= $point_data['gacha'] * $loop;
				*/
				if($members_data['point'] < $point_gacha){
					$error												= 43;
					$errormessage										= COIN_NAME."が足りません。";
				}

			# ガチャポ
			}elseif($data['pay'] == "gachapo"){
				/************************************************
				**
				**	ユーザのガチャポイント数取得
				**
				************************************************/
				# 必要ガチャポイント
				$service_point_gacha				= GACHA_SERVICE_POINT * $loop;

				# 所持ガチャポイント
				$gachapoint = 0;
				$gachapoint_itemboxid = NULL;

				# ガチャポイントアイテム
				$gachapo_item_id = NULL;

				# まずガチャポイントを確認
				$item_table	= "items";
				$item_select	= "*";
				$item_where	= "site_cd = :site_cd AND effect = :effect AND status = :status";
				$item_array = array();
				$item_array[':site_cd'] = $members_data["site_cd"];
				$item_array[':effect'] = 4;
				$item_array[':status'] = 0;
				$item_rtn		= $database->selectDb($item_table,$item_select,$item_where,$item_array,$item_order=NULL,1);
				$database->errorDb($item_table, $item_rtn->errorCode(),__FILE__,__LINE__);
				$gachapo_data = $database->fetchAssoc($item_rtn);


				# ガチャポイントあり
				if(!empty($gachapo_data['id'])){
					# ガチャポイントアイテム
					$gachapo_item_id = $gachapo_data['id'];

					# ガチャポイント名
					$gachapo_name							= $gachapo_data['name'];

					# マニーアイテム画像
					$gachapo_image							= HTTP_ITEM_IMAGE."/".$gachapo_data['image'];

					# ユーザーがそのアイテム持ってるかチェック
					$itembox_conditions					= array();
					$itembox_conditions					= array(
						'user_id'						=> $members_data['id'],
						'item_id'						=> $gachapo_data['id'],
						'status'						=> 0
					);
					$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

					# 持ってない
					if(empty($itembox_data['id']) || !$itembox_data['unit']){
						$error												= 45;
						$errormessage										= $gachapo_name."が足りません。";
					}else{
						# 足らない
						if($itembox_data['unit'] < $service_point_gacha){
							$error												= 45;
							$errormessage										= $gachapo_name."が足りません。";
						}
						$gachapoint = $itembox_data['unit'];
						$gachapoint_itemboxid = $itembox_data['id'];
					}

				}else{
					$error												= 46;
					$errormessage										= "ガチャチケット、システムエラー。";
				}

			}

			# POINT OK
			if(empty($error)){


				/************************************************
				**
				**	MASTER DATABASE切り替え
				**
				************************************************/

				# AUTHORITY / 既にマスターに接続してるかチェック
				$db_auth												 = $database->checkAuthority();

				# DATABASE CHANGE / スレーブだったら
				if(empty($db_auth)){

					# CLOSE DATABASE SLAVE
					$database->closeDb();

					# CONNECT DATABASE MASTER
					$database->connectDb(MASTER_ACCESS_KEY);

				}

				if($data['type'] == "limitted1" || $data['type'] == "limitted2" || $data['type'] == "limitted3"
					|| $data['type'] == "stepup1" || $data['type'] == "stepup2" || $data['type'] == "stepup3"){//20180316 update by A.cos
					/************************************************
					**
					**	gachas_stepup（確定）
					**	============================================
					**	ガチャデータ抽出（確定）
					**
					************************************************/

					$gachas_abs_conditions										= array();
					$gachas_abs_conditions										= array(
						'site_cd'											=> $members_data['site_cd'],
						'campaign_id'										=> $campaign_id,
						'use_flg'											=> $stepup_gacha_number[$data['type']],
						'absolute'											=> 1,
						'status'											=> 0
					);

					$gachas_abs_rtn												= $gachaModel->getStepupGachaList($gachas_abs_conditions);

					$i=0;
					while($gachas_abs_data = $database->fetchAssoc($gachas_abs_rtn)){
						$gachas_list_abs[]										= $gachas_abs_data['id'];
						$probability_abs[]										= $gachas_abs_data['percent'];
						$i++;
					}
					$database->freeResult($gachas_abs_rtn);

					/************************************************
					**
					**	gachas_stepup（通常）
					**	============================================
					**	ガチャデータ抽出（通常）
					**
					************************************************/

					$gachas_normal_conditions										= array();
					$gachas_normal_conditions										= array(
						'site_cd'											=> $members_data['site_cd'],
						'campaign_id'										=> $campaign_id,
						'use_flg'											=> $stepup_gacha_number[$data['type']],
						'absolute'											=> 0,
						'status'											=> 0
					);

					$gachas_normal_rtn												= $gachaModel->getStepupGachaList($gachas_normal_conditions);

					$j=0;
					while($gachas_normal_data = $database->fetchAssoc($gachas_normal_rtn)){
						$gachas_list_normal[]										= $gachas_normal_data['id'];
						$probability_normal[]										= $gachas_normal_data['percent'];
						$j++;
					}
					$database->freeResult($gachas_normal_rtn);


					$gacha_loop	= $stepup_data["times"];

					# GACHA DATA HIT
					if($i > 0){

						/************************************************
						**
						**	トランザクション開始
						**	============================================
						**	このページではトランザクション処理をする
						**	何か問題が起きたら全てロールバック
						**
						************************************************/

						# トランザクションスタート
						$database->beginTransaction();

						# 本日
						$today												= date("Y-m-d");

						/************************************************
						**
						**	確率からヒットIDを出す
						**
						************************************************/

						$update_s_point										= 0;
						$update_f_point										= 0;
						$point_recv[1]										= array();
						$point_recv[2]										= array();
						$points_insert_flg									= NULL;

						/************************************************
						**
						**	gachas_stepup（確定）
						**
						************************************************/
						for($count=0;$count<$stepup_data["abs_num"];$count++){

							$gachas_id										= NULL;
							$acceptance										= NULL;
							$result											= array();
							
							for($a=0;$a<1;$a++){
								$key										= $optionClass->weightedRandom($probability_abs);
								if(!isset($result[$key])){
									$result[$key]							= 0;
								}
								$result[$key]++;
							}
							ksort($result);
							
							# HIT
							$gacha_id										= $gachas_list_abs[$key];

							if(!empty($gacha_id)){
								# HIT ガチャデータ 取得
								$gacha_data									= $gachaModel->getStepupGachaDataById($gacha_id);

								# OK
								if(!empty($gacha_data['id'])){

									# TICKET
									if($gacha_data['type'] == $number_ticket){
										$get_data							= $shopModel->getShopDataById($gacha_data['target_id'],"id,name,image,type");

										# 無料/有料ポイント配布データ更新
										list($update_f_point, $update_s_point, $point_recv)
											= receiveTicketPointOnGacha($get_data, $gacha_data, $update_f_point, $update_s_point, $point_recv);
										$points_insert_flg					= 1;
										$acceptance							= 1;

									# ITEM
									}elseif($gacha_data['type'] == $number_item){
										# アイテムデータ
										$get_data							= $itemModel->getItemDataById($gacha_data['target_id'],"id,name,image");
										$acceptance	= receiveItemOnGacha($database, $itemboxModel, $members_data, $gacha_data);

									# IMAGE
									}elseif($gacha_data['type'] == $number_image){
										$attachment_data					= $imageModel->getImageDataById($gacha_data['target_id'],"id,img_name,img_key");

										if(!empty($attachment_data['id'])){
											# ここでユーザーが既に画像を持っているかチェック
											$get_data['id']					= $attachment_data['id'];
											$get_data['name']				= $attachment_data['img_key'];
											$get_data['image']				= "thumb/".$attachment_data['img_name'];

											$albums_insert					= array();
											$albums_insert					= array(
												'user_id'					=> $members_data['id'],
												'image'						=> $attachment_data['img_name'],
												'name'						=> $attachment_data['img_key'],
												'acceptance_date'			=> $today,
												'status'					=> 0
											);

											# 【INSERT】 / albums
											if($gacha_data['unit'] > 1){
												for($j=0;$j<$gacha_data['unit'];$j++){
													$acceptance				= $database->insertDb("albums",$albums_insert);
												}
											}else{
												$acceptance					= $database->insertDb("albums",$albums_insert);
											}

											if($members_data['media_flg'] != 10){
												$media_flg_update			= 1;
											}
										}

									}


									# 正常に処理されなければエラー
									if(empty($acceptance)){

										$error								= 2;
										break;

									}

									$gacha_list['id'][$count]				= $gacha_data['id'];
									$gacha_list['unit'][$count]				= $gacha_data['unit'];
									$gacha_list['name'][$count]				= $get_data['name'];
									$gacha_list['image'][$count]			= $get_data['image'];

									# 優先表示画像
									$gacha_list['display'][$count]			= $gacha_data['image'];

								}else{

									$error									= 3;
									$errormessage							= "正常に処理できませんでした<br />";
									break;

								}

							}else{

								$error										= 4;
								$errormessage								= "正常に処理できませんでした<br />";
								break;

							}

						}

						/************************************************
						**
						**	gachas_stepup（普通）
						**
						************************************************/
						for($count=$stepup_data["abs_num"];$count<$gacha_loop;$count++){
							$gachas_id										= NULL;
							$acceptance										= NULL;
							$result											= array();
							
							for($b=0;$b<1;$b++){
								$key										= $optionClass->weightedRandom($probability_normal);
								if(!isset($result[$key])){
									$result[$key]							= 0;
								}
								$result[$key]++;
							}
							ksort($result);

							# HIT
							$gacha_id										= $gachas_list_normal[$key];

							if(!empty($gacha_id)){
								# HIT ガチャデータ 取得
								$gacha_data									= $gachaModel->getStepupGachaDataById($gacha_id);

								# OK
								if(!empty($gacha_data['id'])){
									# TICKET
									if($gacha_data['type'] == $number_ticket){
										$get_data							= $shopModel->getShopDataById($gacha_data['target_id'],"id,name,image,type");

										# 無料/有料ポイント配布データ更新
										list($update_f_point, $update_s_point, $point_recv)
											= receiveTicketPointOnGacha($get_data, $gacha_data, $update_f_point, $update_s_point, $point_recv);
										$points_insert_flg					= 1;
										$acceptance							= 1;

									# ITEM
									}elseif($gacha_data['type'] == $number_item){
										# アイテムデータ
										$get_data							= $itemModel->getItemDataById($gacha_data['target_id'],"id,name,image");
										$acceptance	= receiveItemOnGacha($database, $itemboxModel, $members_data, $gacha_data);

									# IMAGE
									}elseif($gacha_data['type'] == $number_image){
										$attachment_data					= $imageModel->getImageDataById($gacha_data['target_id'],"id,img_name,img_key");

										if(!empty($attachment_data['id'])){
											# ここでユーザーが既に画像を持っているかチェック
											$get_data['id']					= $attachment_data['id'];
											$get_data['name']				= $attachment_data['img_key'];
											$get_data['image']				= "thumb/".$attachment_data['img_name'];

											$albums_insert					= array();
											$albums_insert					= array(
												'user_id'					=> $members_data['id'],
												'image'						=> $attachment_data['img_name'],
												'name'						=> $attachment_data['img_key'],
												'acceptance_date'			=> $today,
												'status'					=> 0
											);

											# 【INSERT】 / albums
											if($gacha_data['unit'] > 1){
												for($j=0;$j<$gacha_data['unit'];$j++){
													$acceptance				= $database->insertDb("albums",$albums_insert);
												}
											}else{
												$acceptance					= $database->insertDb("albums",$albums_insert);
											}

											if($members_data['media_flg'] != 10){
												$media_flg_update			= 1;
											}
										}

									}


									# 正常に処理されなければエラー
									if(empty($acceptance)){

										$error								= 2;
										break;

									}

									$gacha_list['id'][$count]				= $gacha_data['id'];
									$gacha_list['unit'][$count]				= $gacha_data['unit'];
									$gacha_list['name'][$count]				= $get_data['name'];
									$gacha_list['image'][$count]			= $get_data['image'];

									# 優先表示画像
									$gacha_list['display'][$count]			= $gacha_data['image'];

								}else{

									$error									= 3;
									$errormessage							= "正常に処理できませんでした<br />";
									break;

								}

							}else{

								$error										= 4;
								$errormessage								= "正常に処理できませんでした<br />";
								break;

							}
						}

					}else{

						$error												= 5;
						$errormessage										= "正常に処理できませんでした<br />";

					}

				}else{//type=free, single, multi
					/************************************************
					**
					**	gachas
					**	============================================
					**	ガチャデータ抽出
					**
					************************************************/

					$gachas_conditions										= array();
					$gachas_conditions										= array(
						'site_cd'											=> $members_data['site_cd'],
						'campaign_id'										=> $campaign_id,
						'status'											=> 0
					);

					$gachas_rtn												= $gachaModel->getGachaList($gachas_conditions);

					$i=0;
					while($gachas_data = $database->fetchAssoc($gachas_rtn)){

						$gachas_list[]										= $gachas_data['id'];
						$probability[]										= $gachas_data['percent'];

						$i++;

					}

					$database->freeResult($gachas_rtn);

					# GACHA DATA HIT
					if($i > 0){

						/************************************************
						**
						**	トランザクション開始
						**	============================================
						**	このページではトランザクション処理をする
						**	何か問題が起きたら全てロールバック
						**
						************************************************/

						# トランザクションスタート
						$database->beginTransaction();

						# 本日
						$today												= date("Y-m-d");


						/************************************************
						**
						**	gachas
						**	============================================
						**	確率からヒットIDを出す
						**
						************************************************/

						$update_s_point										= 0;
						$update_f_point										= 0;
						$point_recv[1]										= array();
						$point_recv[2]										= array();
						$points_insert_flg									= NULL;

						for($count=0;$count<$gacha_loop;$count++){

							$gachas_id										= NULL;
							$acceptance										= NULL;
							$result											= array();

							for($i=0;$i<1;$i++){

								$key										= $optionClass->weightedRandom($probability);

								if(!isset($result[$key])){
									$result[$key]							= 0;
								}

								$result[$key]++;

							}

							ksort($result);

							foreach($result as $key => $value){
								#echo "$wlistname[$key][$wlist[$key]] $value" . "<br>";
							}

							# HIT
							$gacha_id										= $gachas_list[$key];

							if(!empty($gacha_id)){

								# HIT ガチャデータ 取得
								$gacha_data									= $gachaModel->getGachaDataById($gacha_id);

								# OK
								if(!empty($gacha_data['id'])){
									# TICKET
									if($gacha_data['type'] == $number_ticket){
										$get_data							= $shopModel->getShopDataById($gacha_data['target_id'],"id,name,image,type");
										# 無料/有料ポイント配布データ更新
										list($update_f_point, $update_s_point, $point_recv)
											= receiveTicketPointOnGacha($get_data, $gacha_data, $update_f_point, $update_s_point, $point_recv);
										$points_insert_flg					= 1;
										$acceptance							= 1;

									# ITEM
									}elseif($gacha_data['type'] == $number_item){
										# アイテムデータ
										$get_data							= $itemModel->getItemDataById($gacha_data['target_id'],"id,name,image");
										$acceptance	= receiveItemOnGacha($database, $itemboxModel, $members_data, $gacha_data);

									# IMAGE
									}elseif($gacha_data['type'] == $number_image){

										$attachment_data					= $imageModel->getImageDataById($gacha_data['target_id'],"id,img_name,img_key");

										if(!empty($attachment_data['id'])){
											# ここでユーザーが既に画像を持っているかチェック
											$get_data['id']					= $attachment_data['id'];
											$get_data['name']				= $attachment_data['img_key'];
											$get_data['image']				= "thumb/".$attachment_data['img_name'];

											$albums_insert					= array();
											$albums_insert					= array(
												'user_id'					=> $members_data['id'],
												'image'						=> $attachment_data['img_name'],
												'name'						=> $attachment_data['img_key'],
												'acceptance_date'			=> $today,
												'status'					=> 0
											);

											# 【INSERT】 / albums
											if($gacha_data['unit'] > 1){
												for($j=0;$j<$gacha_data['unit'];$j++){
													$acceptance				= $database->insertDb("albums",$albums_insert);
												}
											}else{
												$acceptance					= $database->insertDb("albums",$albums_insert);
											}

											if($members_data['media_flg'] != 10){
												$media_flg_update			= 1;
											}
										}
									}


									# 正常に処理されなければエラー
									if(empty($acceptance)){
										$error								= 2;
										break;
									}

									$gacha_list['id'][$count]				= $gacha_data['id'];
									$gacha_list['unit'][$count]				= $gacha_data['unit'];
									$gacha_list['name'][$count]				= $get_data['name'];
									$gacha_list['image'][$count]			= $get_data['image'];

									# 優先表示画像
									$gacha_list['display'][$count]			= $gacha_data['image'];

								}else{

									$error									= 3;
									$errormessage							= "正常に処理できませんでした<br />";
									break;

								}

							}else{

								$error										= 4;
								$errormessage								= "正常に処理できませんでした<br />";
								break;

							}

						}

					}else{

						$error												= 5;
						$errormessage										= "正常に処理できませんでした<br />";

					}
				}

			}

			if(empty($error)){

				# 数チェック
				if($gacha_loop == $count){


					/************************************************
					**
					**	決済処理
					**	============================================
					**	
					**
					************************************************/

					# pay_flg 判定 無料ユーザー
					if($members_data['status'] == 3) {
						$pay_flg										= 2;
					# 定額ユーザー
					}elseif($members_data['status'] == 2) {
						$pay_flg										= 3;
					# 通常ユーザー
					}elseif($members_data['status'] != 0){

						# 無課金
						if($members_data['pay_count'] == 0){
							$pay_flg									= 2;
						# 課金
						}else{
							$pay_flg									= 1;
						}

					# その他
					}else{
						$pay_flg										= 0;
					}

					$datetime											= date("YmdHis");

					# 一日一回無料
					if($data['type'] == "free"){

						# membersのgacha_date を今日にアップデート
						$members_update['last_gacha_date']				= date("Y-m-d");

					}else{

						# チケットで処理
						if($data['pay'] == "point"){

							# ガチャ回数分処理
							for($count=0;$count<$loop;$count++){

								# 毎回初期化
								unset($point_result['members']['point']);
								unset($point_result['members']['s_point']);
								unset($point_result['members']['f_point']);

								$point_result							= $pointsetModel->makePointConsume($pointsets_data,$members_data);

								# OK
								if(empty($point_result['error']) && isset($point_result['points'])){

									foreach($point_result['points'] as $points_key => $points_array){

										if(isset($points_array['point'])){

											$i=0;
											foreach($points_array['point'] as $key => $value){

												$points_insert			= array();
												$points_insert			= array(
													'user_id'			=> $members_data['id'],
													'site_cd'			=> $members_data['site_cd'],
													'sex'				=> $members_data['sex'],
									                'ad_code'			=> $members_data['ad_code'],
									                'domain_flg'		=> $members_data['domain_flg'],
													'point'				=> $value,
													'point_no_id'		=> $points_array['point_no_id'],
													'campaign_id'		=> $campaign_id,
									                'point_type'		=> $key,
													'log_date'			=> $datetime,
									                'pay_flg'			=> $pay_flg
												);

												# 【insert】points
												$database->insertDb("points",$points_insert);

												$i++;

											}

										}

									}

									# members上書き
									if(isset($point_result['members']['point'])){
										$members_data['point']			= $point_result['members']['point'];
										$members_update['point']		= $point_result['members']['point'];
									}

									if(isset($point_result['members']['s_point'])){
										$members_data['s_point']		= $point_result['members']['s_point'];
										$members_update['s_point']		= $point_result['members']['s_point'];
									}

									if(isset($point_result['members']['f_point'])){
										$members_data['f_point']		= $point_result['members']['f_point'];
										$members_update['f_point']		= $point_result['members']['f_point'];
									}

								# ERROR
								}else{

									$error								= 42;
									$errormessage						= TICKET_NAME."が足りません。";
									break;

								}

							}

							$mainClass->debug($point_result);



						# PFの決済で処理
						}elseif($data['pay'] == "coin"){

							# 処理OK
							if($members_data['point'] >= $point_gacha){

								$consume_point							= $members_data['point'];

								//20180316 update by A.cos
								# ガチャ回数分処理
								switch($data['type']){
									case "limitted1":
									case "limitted2":
									case "limitted3":
									case "stepup1":
									case "stepup2":
									case "stepup3":
										for($count=0;$count<$gacha_loop;$count++){
											if($consume_point >= $point_data['gacha']){
												$consume_point					= $consume_point - $point_data['gacha'];
												# PF通貨でガチャ引いた場合は消費としてpointsに入れない
												$points_insert					= array();
												$points_insert					= array(
													'user_id'					=> $members_data['id'],
													'site_cd'					=> $members_data['site_cd'],
													'sex'						=> $members_data['sex'],
													'ad_code'					=> $members_data['ad_code'],
													'domain_flg'				=> $members_data['domain_flg'],
													'point'						=> $point_data['gacha'],
													'point_no_id'				=> $point_no_coin,
													'campaign_id'				=> $campaign_id,
													'point_type'				=> 0,
													'log_date'					=> $datetime,
													'pay_flg'					=> $pay_flg
												);
												# 【insert】points
												$database->insertDb("points",$points_insert);
											}else{
												$error							= 43;
												$errormessage					= COIN_NAME."が足りません。";
												break;
											}
										}
										break;
									default:
										for($count=0;$count<$loop;$count++){
											if($consume_point >= $point_data['gacha']){
												$consume_point					= $consume_point - $point_data['gacha'];
												# PF通貨でガチャ引いた場合は消費としてpointsに入れない
												$points_insert					= array();
												$points_insert					= array(
													'user_id'					=> $members_data['id'],
													'site_cd'					=> $members_data['site_cd'],
													'sex'						=> $members_data['sex'],
													'ad_code'					=> $members_data['ad_code'],
													'domain_flg'				=> $members_data['domain_flg'],
													'point'						=> $point_data['gacha'],
													'point_no_id'				=> $point_no_coin,
													'campaign_id'				=> $campaign_id,
													'point_type'				=> 0,
													'log_date'					=> $datetime,
													'pay_flg'					=> $pay_flg
												);
												# 【insert】points
												$database->insertDb("points",$points_insert);
											}else{
												$error							= 43;
												$errormessage					= COIN_NAME."が足りません。";
												break;
											}
										}
										break;
								}
								if(empty($error)){

									$members_data['point']				= $consume_point;
									$members_update['point']			= $consume_point;

								}


							# ERROR
							}else{

								$error									= 43;
								$errormessage							= COIN_NAME."が足りません。";

							}
						# ガチャポで処理
						}elseif($data['pay'] == "gachapo"){
							

							/************************************************
							**
							**	ユーザガチャポ減算
							**
							************************************************/
							# UPDATE / itembox
							$itembox_use_table					= "itembox";
							$itembox_use_update					= array();
							$itembox_use_update					= array(
								'unit' => (intval($gachapoint) - $service_point_gacha)
							);
							$itembox_use_update_where				= "id = :id";
							$itembox_use_update_conditions[':id']	= $gachapoint_itemboxid;
							$database->updateDb($itembox_use_table,$itembox_use_update,$itembox_use_update_where,$itembox_use_update_conditions);
							
							# itemuse にinsert
							for($i=0;$i<$loop;$i++){
								$itemuse_insert			= array();
								$itemuse_insert			= array(
									'site_cd'			=> $members_data['site_cd'],
									'itembox_id'		=> $gachapoint_itemboxid,
									'item_id'			=> $gachapo_item_id,
									'user_id'			=> $members_data['id'],
									//'character_id'		=> $character_id,
									//'limit_time'		=> $limit_time,
									//'limit_count'		=> $limit_count,
									'reg_date'			=> date("YmdHis"),//20180920 add by A.cos
								);
								# 【insert】itemuse
								$insert_id				= $database->insertDb("itemuse",$itemuse_insert);
							}

						}

					}


					/************************************************
					**
					**	コミット
					**	============================================
					**	正常にきたらコミットする
					**	何か問題があればロールバック
					**
					************************************************/

					# COMMIT : 一括処理
					if(empty($error)){

						# s_point 追加分
						if(!empty($update_s_point)){

							if(isset($members_update['s_point'])){
								$members_update['s_point']				= $members_update['s_point'] + $update_s_point;
							}else{
								$members_update['s_point']				= $members_data['s_point'] + $update_s_point;
							}

						}

						# f_point 追加分
						if(!empty($update_f_point)){

							if(isset($members_update['f_point'])){
								$members_update['f_point']				= $members_update['f_point'] + $update_f_point;
							}else{
								$members_update['f_point']				= $members_data['f_point'] + $update_f_point;
							}

							# ユーザーf_pointは毎日ログイン配布される分だけしか所持できない為
							if($members_update['f_point'] > FREE_POINT_LIMIT){
								$members_update['f_point']				= FREE_POINT_LIMIT;
							}

						}

						# アルバムに画像追加したら
						if(!empty($media_flg_update)){
							$members_update['media_flg']				= 10;
						}

						# UPDATE WHERE
						$members_update_where							= "id = :id";
						$members_update_conditions[':id']				= $members_data['id'];

						# 【UPDATE】 / members
						$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

						# ポイント当たった場合はここでログ入れる
						if(!empty($points_insert_flg)){

							$gacha_recv_point_no_id						= $point_no_array[$point_name_array['gacha_recv']][2];

							foreach($point_recv as $point_type => $point_value){

								foreach($point_value as $key => $value){

									$points_recv_insert					= array();
									$points_recv_insert					= array(
										'user_id'						=> $members_data['id'],
										'site_cd'						=> $members_data['site_cd'],
										'sex'							=> $members_data['sex'],
						                'ad_code'						=> $members_data['ad_code'],
						                'domain_flg'					=> $members_data['domain_flg'],
										'point'							=> $value,
										'point_no_id'					=> $gacha_recv_point_no_id,
										'campaign_id'					=> $campaign_id,
						                'point_type'					=> $point_type,
										'log_date'						=> $datetime,
						                'pay_flg'						=> $pay_flg
									);

									# 【insert】points
									$database->insertDb("points",$points_recv_insert);

								}

							}

						}


						# PAYS UPDATE
						if(!empty($pays_id)){

							$pays_update['finish']						= 1;

							# UPDATE WHERE
							$pays_update_where							= "id = :id";
							$pays_update_conditions[':id']				= $pays_id;

							# 【UPDATE】 / pays
							$database->updateDb("pays",$pays_update,$pays_update_where,$pays_update_conditions);

						}

						# ステップアップガチャの場合、回数をカウントアップしておく
						# ※限定（101～）はMAXに達したらそのまま、ステップアップ（201）はMAXに達したら0にする
						//20180316 update by A.cos
						switch($data['type']){
							case "limitted1":
							case "limitted2":
							case "limitted3":
								$post_data = array();
								$post_data["campaign_id"] = $campaign_id;
								$post_data["use_flg"] = $stepup_gacha_number[$data['type']];
								$gacha_phase	= $gachaModel->countupUserPhaseOnStepupGacha($members_data['id'], $post_data);
								//$mainClass->debugSystem("gacha_phase:".$gacha_phase[0]."/".$gacha_phase[1]."<br>");
								break;
							case "stepup1":
							case "stepup2":
							case "stepup3":
								$post_data = array();
								$post_data["campaign_id"] = $campaign_id;
								$post_data["use_flg"] = $stepup_gacha_number[$data['type']];
								$gacha_phase	= $gachaModel->countupUserPhaseOnStepupGacha($members_data['id'], $post_data);
								//$mainClass->debugSystem("gacha_phase:".$gacha_phase[0]."/".$gacha_phase[1]."<br>");
								if($gacha_phase[0]==-1){//MAXに達していたら初期化しちゃう
									$gachaModel->resetUserPhaseOnStepupGacha($members_data['id'], $post_data);
								}
								break;
						}

						$mainClass->debug($gacha_list);

						$database->commit();
						$exection										= 1;

					}else{
						$error											= 6;
						$errormessage									= "正常に処理できませんでした";
					}

				}else{

					$error												= 7;
					$errormessage										= "正常に処理できませんでした";

				}

				if(!empty($error)){
					$database->rollBack();
				}


			# ROLLBACK : 巻き戻し
			}else{

				if(empty($errormessage)){
					$database->rollBack();
					$errormessage									= "正常に処理できませんでした";
				}

			}

		}


		/************************************************
		**
		**	DATABASE 切断
		**
		************************************************/

		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();


		/************************************************
		**
		**	finish.incをここで読んで終了
		**
		************************************************/

		# VIEW FILE チェック & パス生成
		$view_directory								= $mainClass->getViewDirectory($directory,"finish",$device_file);

		# 読み込み
		include_once($view_directory);

		# DEBUG
		if(defined("SYSTEM_CHECK")){

			# SYSTEM DEBUG
			$mainClass->outputDebugSystem();

		}

		# 終了
		exit();


	}

}


################################## FILE END #####################################
?>