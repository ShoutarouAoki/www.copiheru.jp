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
require_once(DOCUMENT_ROOT_MODELS."/MailUserModel.php");

/** ATTACH MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AttachModel.php");

/** SHOP MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ShopModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** BONUS MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/BonusModel.php");

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
$mailuserModel				= new MailUserModel($database,$mainClass);

# ATTACH MODEL
$attachModel				= new AttachModel($database,$mainClass);

# SHOP MODEL
$shopModel					= new ShopModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# BONUS MODEL
$bonusModel					= new BonusModel($database,$mainClass);

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
		**	ログインボーナス
		**	============================================
		**	受け取り処理
		**
		************************************************/

		# 初期化
		$login_bonus							= NULL;
		$login_bonus_contents					= NULL;

		# ログインボーナスならcategory = 1
		$login_bonus_category					= 1;

		# ログインボーナス受け取りチェック
		$login_bonus_count						= 0;
		$presentbox_conditions					= array();
		$presentbox_conditions					= array(
			'site_cd'							=> $members_data['site_cd'],
			'user_id'							=> $members_data['id'],
			'acceptance_date'					=> date("Y-m-d"),
			'category'							=> $login_bonus_category,
		);

		$login_bonus_count						= $presentboxModel->getPresentboxCount($presentbox_conditions);

		# まだ受け取って無い
		if($login_bonus_count == 0){

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
			$error								= NULL;

			$bonuses_conditions					= array();
			$bonuses_conditions					= array(
				'distribution_date'				=> date("Y-m-d"),
				'status'						=> 0
			);

			$bonuses_column						= "id,type,target_id,unit,receive_limit";
			$bonuses_rtn						= $bonusModel->getBonusList($bonuses_conditions,$bonuses_column);

			$i=0;
			while($bonuses_data = $database->fetchAssoc($bonuses_rtn)){

				# TICKET
				if($bonuses_data['type'] == $number_ticket){

					$bonus_data					= $shopModel->getShopDataById($bonuses_data['target_id'],"id,name,image");

				# ITEM
				}elseif($bonuses_data['type'] == $number_item){

					$bonus_data					= $itemModel->getItemDataById($bonuses_data['target_id'],"id,name,image");

				# IMAGE
				}elseif($bonuses_data['type'] == $number_image){

					$image_data					= $imageModel->getImageDataById($bonuses_data['target_id'],"id,img_name,img_key");
					$bonus_data['id']			= $image_data['id'];
					$bonus_data['name']			= $image_data['img_key'];
					$bonus_data['image']		= $image_data['img_name'];

				}

				$bonus_list['id'][$i]			= $bonuses_data['id'];
				$bonus_list['unit'][$i]			= $bonuses_data['unit'];
				$bonus_list['name'][$i]			= $bonus_data['name'];
				$bonus_list['image'][$i]		= $bonus_data['image'];

				# プレゼントBOXにINSERT
				$limit_date						= date("Y-m-d",strtotime("+".$bonuses_data['receive_limit']." day"));

				$present_insert					= array(
					'site_cd'					=> $members_data['site_cd'],
					'user_id'					=> $members_data['id'],
					'acceptance_date'			=> date("Y-m-d"),
					'category'					=> $login_bonus_category,
					'type'						=> $bonuses_data['type'],
					'target_id'					=> $bonuses_data['target_id'],
					'unit'						=> $bonuses_data['unit'],
					'limit_date'				=> $limit_date,
					'status'					=> 0
				);

				$insert_id						= $database->insertDb("presentbox",$present_insert);

				# ERROR 吐いたら処理止め
				if(empty($insert_id)){
					$error						= 1;
					$login_bonus				= NULL;
					$login_bonus_contents		= NULL;
					break;
				}

				# LOGIN BONUS 表示生成
				$login_bonus_contents			.= $bonus_data['name']." × ".$bonuses_data['unit']."<br />";

				$i++;

			}

			# FREE RESULT
			$database->freeResult($bonuses_rtn);

			# INSERT 処理
			if(empty($error)){

				if($i > 0){

					# COMMIT
					$database->commit();

					$login_bonus				= 1;

					$login_bonus_contents		.= "<br />をプレゼントBOXにお届けしました！";

				}

			}

			# 初回ログインフラグ削除
			if(!empty($_SESSION['access'])){
				$_SESSION['access']				= NULL;
				unset($_SESSION['access']);
			}

			# DATABASE CHANGE
			if(!empty($db_check)){

				# CLOSE DATABASE MASTER
				$database->closeDb();

				# CONNECT DATABASE SLAVE
				$database->connectDb();

			}

		}



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

			# 表示文言 / 画像 取得
			if(!empty($campaign_data['filesets_id'])){
				$campaign_contents		= $filesetModel->getFilesetDataById($campaign_data['filesets_id']);
				if(!empty($campaign_contents['id']) && !empty($campaign_contents['body_normal'])){
					$campaign_body		= $imageModel->replaceCampaignImage($campaign_contents['body_normal'], $members_data['site_cd']);
				}
			}

		}


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

		$mails_conditions				= array();
		$mails_conditions				= array(
			'user_id'					=> $members_data['id'],
			'status'					=> 0,
			'del_flg'					=> 0,
			'last_flg'					=> 0,
			'recv_flg'					=> 1,
			'group'						=> NULL,
			'order'						=> 'send_date DESC',
			'limit'						=> 5
		);

		$mails_column					= NULL;

		$mails_rtn						= $mailModel->getUserReceiveMailListJoinOnMailusers($mails_conditions,$mails_column);

		$i=0;
		while($mails_data = $database->fetchAssoc($mails_rtn)){


			/******************************************************
			**
			**	$mails_dataにmailusersの情報も格納済み
			**
			******************************************************/

			# 親だったら
			if($mails_data['naruto'] == 0){

				$parent_id						= $mails_data['send_id'];
				$check_parent					= 1;

			# 子だったら
			}else{

				$parent_id						= $mails_data['naruto'];
				$check_parent					= NULL;

			}

			# CHARA DATA 取得
			$character_data					= $memberModel->getMemberById($mails_data['send_id'],NULL,"id,nickname,age,pref,city,chikuwa,status");

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

			# 結果セット
			$mail_list['id'][$i]			= $mails_data['mail_id'];
			$mail_list['character_id'][$i]	= $mails_data['send_id'];
			$mail_list['title'][$i]			= $display_title['web'];
			$mail_list['name'][$i]			= $display_name['web'];
			$mail_list['recv_flg'][$i]		= $mails_data['recv_flg'];
			$mail_list['send_date'][$i]		= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));

			# サムネイル画像
			if(!empty($attaches_data)){
				$mail_list['image'][$i]		= $attaches_data['attached'];
			}

			# 年齢セット
			if($mails_data['virtual_age'] > 0){
				$mail_list['age'][$i]		= $mails_data['virtual_age'];
			}else{
				$mail_list['age'][$i]		= $mails_data['age'];
			}

            # 添付系セット
            if ($mails_data['media_flg'] == 1 || $mails_data['media_flg'] == 3) {
                $mail_list['media'][$i]		= "画像アリ";
            } else if($mails_data['media_flg'] == 2 || $mails_data['media_flg'] == 4) {
                $mail_list['media'][$i]		= "動画アリ";
            }

			$i++;

		}

		$database->freeResult($mails_rtn,1);


	}

}


################################## FILE END #####################################
?>