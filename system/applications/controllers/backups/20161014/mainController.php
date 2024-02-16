<?php
################################ FILE MANAGEMENT ################################
##
##	mainController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	MAIN PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	MAIN PAGE 各種処理
##
##	page : index -> ログイン後トップページ
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

/** MAILUSER MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MailuserModel.php");

/** ATTACH MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AttachModel.php");

/** SHOP MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ShopModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemboxModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

/** PRESENT MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PresentModel.php");

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");

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

# MAILUSER MODEL
$mailuserModel				= new MailuserModel($database,$mainClass);

# ATTACH MODEL
$attachModel				= new AttachModel($database,$mainClass);

# SHOP MODEL
$shopModel					= new ShopModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMBOX MODEL
$itemboxModel				= new ItemboxModel($database,$mainClass);

# ITEMUSE MODEL
$itemuseModel				= new ItemuseModel($database,$mainClass);

# PRESENT MODEL
$presentModel				= new PresentModel($database,$mainClass);

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
	**	ログイン後トップページ
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){


		/************************************************
		**
		**	ログインボーナス / プレゼント
		**	============================================
		**	受け取り処理
		**
		************************************************/

		# ERROR
		$error									= NULL;

		# 初期化
		$first_login_bonus						= NULL;
		$present_dialog							= NULL;
		$present_check							= NULL;
		$present_message						= NULL;
		$present_message						= array();
		$present_submessage						= NULL;
		$present_submessage						= array();

		# COMMIT
		$commit_exection						= 1;



		/************************************************
		**
		**	チュートリアル終わってなかったら戻す
		**
		************************************************/

		if($members_data['tutorial'] == 0){

			$mainClass->redirect("/tutorial/");
			exit();

		}



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

			$db_check							 = 1;

		}

		# TRANSACTION START
		$database->beginTransaction();


		/************************************************
		**
		**	登録直後初回ログインボーナス
		**	============================================
		**	受け取り処理
		**
		************************************************/

		# 初ログインユーザー
		if(!empty($_SESSION['regist']) && empty($members_data['present_recv_date'])){

			$login_bonus_category					= $present_category_array['login'];

			# プレゼントチェック
			$bonuses_conditions						= array(
				'check_distribution_date'			=> 1,
				'category'							=> $login_bonus_category,
				'status'							=> 0
			);

			$bonuses_rtn							= $presentModel->getPresentList($bonuses_conditions);

			$i=0;
			while($bonuses_data = $database->fetchAssoc($bonuses_rtn)){

				$acceptance_conditions				= array();
				$acceptance_conditions				= array(
					'user_id'						=> $members_data['id'],
					'present_id'					=> $bonuses_data['id'],
					'limit'							=> 1
				);

				$acceptance_check					= $presentboxModel->getPresentboxCount($acceptance_conditions);

				# 受け取り済み
				if(!empty($acceptance_check)){
					continue;
				}

				# TICKET
				if($bonuses_data['type'] == $number_ticket){

					$bonus_data						= $shopModel->getShopDataById($bonuses_data['target_id'],"id,name,image");

				# ITEM
				}elseif($bonuses_data['type'] == $number_item){

					$bonus_data						= $itemModel->getItemDataById($bonuses_data['target_id'],"id,name,image");

				# IMAGE
				}elseif($bonuses_data['type'] == $number_image){

					$image_data						= $imageModel->getImageDataById($bonuses_data['target_id'],"id,img_name,img_key");
					$bonus_data['id']				= $image_data['id'];
					$bonus_data['name']				= $image_data['img_key'];
					$bonus_data['image']			= $image_data['img_name'];

				}

				$presentbox_insert					= array(
					'site_cd'						=> $members_data['site_cd'],
					'user_id'						=> $members_data['id'],
					'present_id'					=> $bonuses_data['id'],
					'acceptance_date'				=> date("YmdHis"),
					'category'						=> $bonuses_data['category'],
					'type'							=> $bonuses_data['type'],
					'target_id'						=> $bonuses_data['target_id'],
					'unit'							=> $bonuses_data['unit'],
					'limit_date'					=> $bonuses_data['limit_date'],
					'status'						=> 0
				);

				$insert_id							= $database->insertDb("presentbox",$presentbox_insert);

				# ERROR 吐いたら処理止め
				if(empty($insert_id)){
					$i								= 0;
					$error							= 1;
					$present_check					= NULL;
					$present_message				= NULL;
					$present_message				= array();
					$present_submessage				= NULL;
					$present_submessage				= array();
					break;
				}

				# LOGIN BONUS 表示生成
				if(isset($present_message[$bonuses_data['category']])){
					$present_message[$bonuses_data['category']]			.= $bonus_data['name']." × ".$bonuses_data['unit']."<br />";
				}else{
					$present_message[$bonuses_data['category']]	 		= $bonus_data['name']." × ".$bonuses_data['unit']."<br />";
				}

				# SUB MESSAGE
				if(!empty($bonuses_data['message'])){
					if(isset($present_submessage[$bonuses_data['category']])){
						$present_submessage[$bonuses_data['category']]	.= $bonuses_data['message']."<br />";
					}else{
						$present_submessage[$bonuses_data['category']]	 = $bonuses_data['message']."<br />";
					}
				}

				$i++;

			}

			$database->freeResult($bonuses_rtn);

			if(empty($error) && $i > 0){
				$first_login_bonus					= 1;
			}

		}



		/************************************************
		**
		**	通常ログインボーナス / プレゼント
		**	============================================
		**	受け取り処理
		**
		************************************************/

		if(empty($error) && empty($first_login_bonus)){

			# 初期化
			$acceptance_check						= NULL;

			# プレゼントチェック
			$presents_conditions					= array();
			$presents_conditions					= array(
				'check_distribution_date'			=> 1,
				'reg_date'							=> $members_data['reg_date'],
				'present_recv_date'					=> $members_data['present_recv_date'],
				'bonus'								=> 1,
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
					'acceptance_date'				=> date("YmdHis"),
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
					$i								= 0;
					$error							= 1;
					$present_check					= NULL;
					$present_message				= NULL;
					$present_message				= array();
					$present_submessage				= NULL;
					$present_submessage				= array();
					break;
				}

				# LOGIN BONUS 表示生成
				if(isset($present_message[$presents_data['category']])){
					$present_message[$presents_data['category']]		.= $present_data['name']." × ".$presents_data['unit']."<br />";
				}else{
					$present_message[$presents_data['category']]		 = $present_data['name']." × ".$presents_data['unit']."<br />";
				}

				# SUB MESSAGE
				if(!empty($presents_data['message'])){
					if(isset($present_submessage[$presents_data['category']])){
						$present_submessage[$presents_data['category']]	.= $presents_data['message']."<br />";
					}else{
						$present_submessage[$presents_data['category']]	 = $presents_data['message']."<br />";
					}
				}

				$i++;

			}

			$database->freeResult($presents_rtn);

		}



		/************************************************
		**
		**	連続ログインボーナス
		**	============================================
		**	受け取り処理
		**
		************************************************/

		if(empty($error)){

			# 初期化
			$acceptance_check						= NULL;

			# プレゼントチェック
			$continuity_conditions					= array();
			$continuity_conditions					= array(
				'category'							=> $present_category_array['continuity'],
				'level'								=> $members_data['login_day'],
				'status'							=> 0
			);

			$continuity_rtn							= $presentModel->getPresentList($continuity_conditions);

			$j=0;
			while($continuity_data = $database->fetchAssoc($continuity_rtn)){

				$acceptance_conditions				= array();
				$acceptance_conditions				= array(
					'user_id'						=> $members_data['id'],
					'present_id'					=> $continuity_data['id'],
					'limit'							=> 1
				);

				$acceptance_check					= $presentboxModel->getPresentboxCount($acceptance_conditions);

				# 受け取り済み
				if(!empty($acceptance_check)){
					continue;
				}

				# TICKET
				if($continuity_data['type'] == $number_ticket){

					$present_data					= $shopModel->getShopDataById($continuity_data['target_id'],"id,name,image");

				# ITEM
				}elseif($continuity_data['type'] == $number_item){

					$present_data					= $itemModel->getItemDataById($continuity_data['target_id'],"id,name,image");

				# IMAGE
				}elseif($continuity_data['type'] == $number_image){

					$image_data						= $imageModel->getImageDataById($continuity_data['target_id'],"id,img_name,img_key");
					$present_data['id']				= $image_data['id'];
					$present_data['name']			= $image_data['img_key'];
					$present_data['image']			= $image_data['img_name'];

				}

				$limit_date							= date("YmdHis",strtotime("+".$continuity_data['limit_date']." day"));

				$presentbox_insert					= array(
					'site_cd'						=> $members_data['site_cd'],
					'user_id'						=> $members_data['id'],
					'present_id'					=> $continuity_data['id'],
					'acceptance_date'				=> date("YmdHis"),
					'category'						=> $continuity_data['category'],
					'type'							=> $continuity_data['type'],
					'target_id'						=> $continuity_data['target_id'],
					'unit'							=> $continuity_data['unit'],
					'limit_date'					=> $limit_date,
					'status'						=> 0
				);

				$insert_id							= $database->insertDb("presentbox",$presentbox_insert);

				# ERROR 吐いたら処理止め
				if(empty($insert_id)){
					$j								= 0;
					$error							= 1;
					$present_check					= NULL;
					$present_message				= NULL;
					$present_message				= array();
					$present_submessage				= NULL;
					$present_submessage				= array();
					break;
				}

				# LOGIN BONUS 表示生成
				if(isset($present_message[$continuity_data['category']])){
					$present_message[$continuity_data['category']]			.= $present_data['name']." × ".$continuity_data['unit']."<br />";
				}else{
					$present_message[$continuity_data['category']]			 = $present_data['name']." × ".$continuity_data['unit']."<br />";
				}

				# SUB MESSAGE
				if(!empty($continuity_data['message'])){
					if(isset($present_submessage[$continuity_data['category']])){
						$present_submessage[$continuity_data['category']]	.= $continuity_data['message']."<br />";
					}else{
						$present_submessage[$continuity_data['category']]	 = $continuity_data['message']."<br />";
					}
				}

				$j++;

			}

			$database->freeResult($continuity_rtn);


		}


		# INSERT 処理
		if(empty($error)){

			if($i > 0 || $j > 0 || !empty($first_login_bonus)){

				$members_update['present_recv_date']			= date("YmdHis");
				$members_update_where							= "id = :id";
				$members_update_conditions[':id']				= $members_data['id'];

				# 【UPDATE】 / members
				$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

				# COMMIT EXECTION
				$xcommit_exection								= 1;

				$present_check									= 1;

				if(!empty($_SESSION['access'])){
					$_SESSION['access']							= NULL;
					unset($_SESSION['access']);
				}

				if(!empty($_SESSION['regist'])){
					$_SESSION['regist']							= NULL;
					unset($_SESSION['regist']);
				}

				foreach($present_message as $key => $value){

					# ログインボーナス(日毎)
					if($key == $present_category_array['login']){
						$present_dialog							.= "【通常ログインボーナス】<br />";
					# 通常配布
					}elseif($key == $present_category_array['normal']){
						$present_dialog							.= "【プレゼント】<br />";
					# ログインボーナス(連続)
					}elseif($key == $present_category_array['continuity']){
						$present_dialog							.= "【連続ログインボーナス ".$members_data['login_day']."日目】<br />";
					# その他
					}else{

					}

					$present_dialog								.= $value."<br />";

				}

				$present_dialog									.= "をプレゼントBOXにお届けしました！<br />";

				# SUB MESSAGE
				foreach($present_submessage as $key => $value){

					$present_dialog								.= $value."<br />";

				}

			}

		}


		/************************************************
		**
		**	メイン画像
		**
		************************************************/

		# 背景画像取得
		$image_file_type		= $web_filetype_array[$directory];

		$image_conditions		= array();
		$image_conditions		= array(
			'file_type'			=> $image_file_type,
			'category'			=> $web_image_category,
			'target_id'			=> 0,
			'status'			=> 1
		);

		$header_data			= $imageModel->getImageData($image_conditions);


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

		$campaign_data						= $campaignsetModel->getCampaignsetData($members_data);
		$campaign_check						= $memberModel->checkCampaignUpdate($members_data,$campaign_data);

		# キャンペーンあれば
		if(!empty($campaign_data['id'])){

			# UPDATE 処理
			if($campaign_check == 2 || $campaign_check == 3){
				$commit_exection			= 1;
			}

			/* TOPページでは表示しない
			# 表示文言 / 画像 取得
			if(!empty($campaign_data['filesets_id'])){
				$campaign_contents			= $filesetModel->getFilesetDataById($campaign_data['filesets_id']);
				if(!empty($campaign_contents['id']) && !empty($campaign_contents['body_normal'])){
					$campaign_body			= $imageModel->replaceCampaignImage($campaign_contents['body_normal'], $members_data['site_cd']);
				}
			}
			*/

		}



		/************************************************
		**
		**	COMMIT
		**	============================================
		**	ここまでの処理でCOMMITする必要があれば
		**
		************************************************/

		# COMMIT
		if(!empty($commit_exection)){
			$database->commit();
		}


		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}



		/************************************************
		**
		**	バナー取得
		**	============================================
		**
		**
		**
		************************************************/

		$banner_conditions					= array();
		$banner_conditions					= array(
			'file_type'						=> 4,
			'category'						=> $banner_image_category,
			'device'						=> $device_number,
			'site_cd'						=> $members_data['site_cd'],
			'target_id'						=> 0,
			'display_check'					=> 1,
			'status'						=> 0
		);

		$banner_rtn							= $imageModel->getImageList($banner_conditions);

		$i=0;
		while($banner_data = $database->fetchAssoc($banner_rtn)){

			$banner_list['id'][$i]			= $banner_data['id'];
			$banner_list['image'][$i]		= $banner_data['img_name'];
			$banner_list['link'][$i]		= $banner_data['img_key'];

			$i++;

		}

		$database->freeResult($banner_rtn);



		/************************************************
		**
		**	新着未読メール取得
		**	============================================
		**
		**	getUserReceiveMailJoinOnMailusers
		**	直近の未読メールのリスト取得
		**
		**	============================================
		**
		**	mailuserとJOIN
		**	やりとりが存在するキャラとのみ
		**
		**
		**
		************************************************/

		$mails_conditions					= array();
		$mails_conditions					= array(
			'user_id'						=> $members_data['id'],
			'status'						=> 0,
			'del_flg'						=> 0,
			'last_flg'						=> 0,
			'recv_flg'						=> 1,
			'type'							=> 1,
			'order'							=> "send_date DESC",
			'limit'							=> 5
		);

		$mails_column						= NULL;

		$mails_rtn							= $mailModel->getMailList($mails_conditions,$mails_column);

		$i=0;
		while($mails_data = $database->fetchAssoc($mails_rtn)){


			/******************************************************
			**
			**	$mails_dataにmailusersの情報も格納済み
			**
			******************************************************/

			# 親だったら
			if($mails_data['naruto'] == 0){

				$parent_id					= $mails_data['send_id'];
				$check_parent				= 1;

			# 子だったら
			}else{

				$parent_id					= $mails_data['naruto'];
				$check_parent				= NULL;

			}

			# CHARA DATA 取得
			$character_data					= $memberModel->getMemberDataById($parent_id,NULL,"id,nickname,age,pref,city,chikuwa,media_flg,status");

			# サムネ画像取得
			$attaches_conditions			= array();
			$attaches_conditions			= array(
				'user_id'					=> $parent_id,
				'category'					=> $thumbnail_image_category,
				'use_flg'					=> 1,
				'pay_count'					=> 0,
				'device'					=> $device_number,
				'status'					=> 1,
				'limit'						=> 1,
				'group'						=> NULL
			);
			$attaches_data					= $attachModel->getAttachData($attaches_conditions);

			# 絵文字セット タイトル
			$display_title					= $emoji_obj->emj_decode($mails_data['title']);

			# 絵文字セット ネーム
			if(!empty($mails_data['virtual_name'])){
				$display_name				= $emoji_obj->emj_decode($mails_data['virtual_name']);
			}else{
				$display_name				= $emoji_obj->emj_decode($character_data['nickname']);
			}

			# 鍵チェック
			$secret_key						= NULL;
			$key_name						= NULL;
			$key_image						= NULL;
			$key_result						= NULL;

			# 鍵付きキャラだったら
			if($character_data['media_flg'] == 1){

				$secret_key								= 1;

				# まず鍵を確認
				$items_conditions						= array(
					'character_id'						=> $character_data['id']
				);
				$items_data								= $itemModel->getItemData($items_conditions);

				# 鍵アイテムあり
				if(!empty($items_data['id'])){

					# 鍵アイテム名
					$key_name							= $items_data['name'];

					# 鍵アイテム画像
					$key_image							= HTTP_ITEM_IMAGE."/".$items_data['image'];

					# ユーザーがそのアイテム持ってるかチェック
					$itembox_conditions					= array();
					$itembox_conditions					= array(
						'user_id'						=> $members_data['id'],
						'item_id'						=> $items_data['id'],
						'status'						=> 0
					);

					$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

					# 持ってる
					if(!empty($itembox_data['id'])){

						$secret_key						= 2;

						# 鍵使って開放してるかチェック
						$itemuse_conditions				= array();
						$itemuse_conditions				= array(
							'item_id'					=> $items_data['id'],
							'user_id'					=> $members_data['id'],
							'character_id'				=> $character_data['id'],
							'status'					=> 0
						);

						$itemuse_rows					= $itemuseModel->getItemuseCount($itemuse_conditions);

						# 開放済み
						if($itemuse_rows > 0){
							$secret_key					= NULL;
							$key_result					= 1;
						}

					}

				}

			}


			# 結果セット
			$mail_list['id'][$i]					= $mails_data['id'];
			$mail_list['character_id'][$i]			= $mails_data['send_id'];
			$mail_list['title'][$i]					= $display_title['web'];
			$mail_list['name'][$i]					= $display_name['web'];
			$mail_list['recv_flg'][$i]				= $mails_data['recv_flg'];
			$mail_list['send_date'][$i]				= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));

			# サムネイル画像
			if(!empty($attaches_data)){
				$mail_list['image'][$i]				= $attaches_data['attached'];
			}

			$mail_list['age'][$i]					= $mails_data['age'];

            # 添付系セット
            if ($mails_data['media_flg'] == 1 || $mails_data['media_flg'] == 3) {
                $mail_list['media'][$i]				= "画像アリ";
            } else if($mails_data['media_flg'] == 2 || $mails_data['media_flg'] == 4) {
                $mail_list['media'][$i]				= "動画アリ";
            }

			# 鍵
			$mail_list['secret_key'][$i]	= $secret_key;
			$mail_list['key_name'][$i]		= $key_name;
			$mail_list['key_image'][$i]		= $key_image;

			$i++;

		}

		$database->freeResult($mails_rtn,1);


	}

}


################################## FILE END #####################################
?>