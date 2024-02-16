<?php
################################ FILE MANAGEMENT ################################
##
##	mailController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	MAIL PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	MAIL PAGE 各種処理
##
##	page : index	-> キャラ一覧
##	page : detail	-> 返信画面
##	page : more		-> もっと読む処理
##	page : check	-> 自動リロード / キャラからの送信確認
##	page : image	-> 画像読み込み処理
##	page : read		-> 既読処理
##	page : send		-> 送信処理
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

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** PRESENT MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PresentModel.php");

/** DEGREE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/DegreeModel.php");

/** MAGICLE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MagicleModel.php");

/** ATTACH MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AttachModel.php");

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");

/** RANKING MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/RankingModel.php");

/** SHOP MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ShopModel.php");

/** EMOJI PLUGINS **/
require_once(DOCUMENT_SYSTEM_PLUGINS."/Emoji/lib/mobile_class_8.php");

/** PAY MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PayModel.php");	//20180615 add by Acos

################################# RELEASE SESSION LOCK ####################################
//セッションの排他的ロックを解除
//（普通はそんなに気にする場所ではないが、読み込みに時間がかかってニ回押してしまうユーザがいる）
session_write_close();

################################# POST ARRAY ####################################

$value_array				= ['page','id','set'];
$data						= $mainClass->getArrayContents($value_array,$values);

############################## INDIVIDUAL SETTING ###############################


/************************************************
**
**	THIS PAGE INDIVIDUAL SETTING
**	---------------------------------------------
**	DATABASE / PATH / CATEGORY ...etc
**
************************************************/

# ERROR
$error						= NULL;

# PAGE
if(empty($data['page'])){
	$data['page']			= "index";
}

# ID
if(!empty($_POST['id'])){
	$data['id']				= $_POST['id'];
}

# ID
if(empty($data['id']) || !is_numeric($data['id'])){
	$error					= 1;
}

# ERROR MESSAGE
$mailerror_array			= [
	['0',	'DEFAULT', ''],
	['1',	'エラー', '不正なアクセスです。'],
	['2',	'エラー', 'キャラが存在しません。'],
];

# メールの表示件数
$list						= MAIL_LIST_UNIT;

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

$next_list					= $set + $list;

# MORE PATH
$more_path					= $page_path."more/".$data['id']."/".$next_list."/";

# PAGE PATH
$page_path					= $page_path.$data['page']."/".$data['id']."/".$set."/";


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

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMUSE MODEL
$itemuseModel				= new ItemuseModel($database,$mainClass);

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);

# PRESENT MODEL
$presentModel				= new PresentModel($database,$mainClass);

# DEGREE MODEL
$degreeModel				= new DegreeModel($database,$mainClass);

# MAGICLE MODEL
$magicleModel				= new MagicleModel($database,$mainClass);

# ATTACH MODEL
$attachModel				= new AttachModel($database,$mainClass);

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);

# RANKING MODEL
$rankingModel				= new RankingModel($database,$mainClass);

# CAMPAIGNSET MODEL
$campaignsetModel			= new CampaignsetModel($database,$mainClass);

# SHOP MODEL
$shopModel					= new ShopModel($database,$mainClass);

# PAY MODEL
$payModel					= new PayModel($database,$mainClass);	//20180615 add by A.cos

##################################### FUNCTIONS ######################################

/** FUNCTIONS FILE **/
require_once(dirname(__FILE__)."/functions/mailFunc.php");

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
	**	ページ毎にif文で処理分岐
	**
	************************************************/


	/************************************************
	**
	**	DETAIL or MORE
	**	============================================
	**	返信画面 / 続きを読むフレーム部分
	**
	************************************************/

	# DETAIL or MORE
	if($data['page'] == "detail" || $data['page'] == "more"){

		## 20180218 add by A.cos
		/*************************************************
		**
		**	過去テーブルの存在する"年"の配列
		**
		**************************************************/
		$array_exit = [];
		$array_exit = checkMailsPastExist($members_data);

		## 20180615 add by A.cos(START)
		/************************************************
		**
		**	PC環境のみガチャリトライ
		**
		************************************************/
		if($default_device === "pc" && $data['page'] == "detail"){
		
			# 引き終わってないガチャチェック
			##################
			# ノーマル
			##################
			$pays_conditions						= [
				'user_id'							=> $members_data['id'],
				'settlement_id'						=> $point_no_array[$point_name_array['gacha_coin']][2],
				'clear'								=> 1,
				'finish'							=> 0,
				'error'								=> 0,
				'status'							=> 0
			];

			$check_retry_data						= $payModel->getPayData($pays_conditions);

			# ガチャリトライ
			if(!empty($check_retry_data['id'])){

				# キャンペーンあれば
				if(!empty($check_retry_data['campaign_id'])){

					$pointsets_conditions			= [
						'site_cd'					=> $members_data['site_cd'],
						'point_no_id'				=> $point_no_array[$point_name_array['gacha_coin']][2],
						'campaign_id'				=> $check_retry_data['campaign_id'],
					];

				# 通常
				}else{

					# 無課金
					if($members_data['pay_count'] == 0){
						$pay_flg					= 2;
					# 課金
					}else{
						$pay_flg					= 1;
					}

					$pointsets_conditions			= [
						'site_cd'					=> $members_data['site_cd'],
						'point_no_id'				=> $point_no_array[$point_name_array['gacha_coin']][2],
						'sex'						=> $members_data['sex'],
						'pay_flg'					=> $pay_flg
					];

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
			$pays_conditions_limitted				= [
				'user_id'							=> $members_data['id'],
				'settlement_id'						=> $point_no_array[$point_name_array['limitted_gacha_coin']][2],
				'clear'								=> 1,
				'finish'							=> 0,
				'error'								=> 0,
				'status'							=> 0
			];

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
			$pays_conditions_stepup				= [
				'user_id'							=> $members_data['id'],
				'settlement_id'						=> $point_no_array[$point_name_array['stepup_gacha_coin']][2],
				'clear'								=> 1,
				'finish'							=> 0,
				'error'								=> 0,
				'status'							=> 0
			];

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
		## 20180615 add by A.cos(END)

		/************************************************
		**
		**	やり取り中キャラ一覧取得
		**	============================================
		**
		**	getMailuserList
		**
		**	============================================
		**
		**
		************************************************/

		# キャラデータ
		$character_data					= $memberModel->getMemberDataById($data['id'],NULL,"*");


		/************************************************
		**
		**	キャラ存在OK
		**
		************************************************/

		if(!empty($character_data['id'])){


			# 非公開キャラはシステム及びデバッグユーザー以外リダイレクト
			if($character_data['open_flg'] == 2){

				if(defined("SYSTEM_CHECK") || $members_data['debug'] == 1){

				}else{

					# REDIRECT
					$mainClass->redirect("/character/");
					exit();

				}


			}


			/************************************************
			**
			**	やり取りフラグ
			**	============================================
			**	$contact_flg
			**
			**	NULL	= やり取りなし
			**	1		= キャラから送信のみ
			**	2		= 送受信やりとりあり
			**
			************************************************/

			# CONTACT FLG
			$contact_flg						= NULL;

			/************************************************
			**
			**	マニーを扱えるか否か。
			**	============================================
			**	
			**
			************************************************/

			# 無料キャラではマニーを取り扱わない
			$manii_use = 1;
			if($character_data['status'] >1){
				$manii_use = 0;
			}

			/************************************************
			**
			**	親子存在確認 / 各種変数初期化
			**	============================================
			**	$parent_id				= 親キャラID
			**	$children_id			= 子キャラID
			**	$first_children_id		= 振り分けられた子キャラID
			**	$post_parent_id			= ajaxに渡す親ID
			**	$parent_flg				= 親キャラとやりとりしてるかのチェック
			**	$last_mail_id			= キャラ最終送信メールID
			**	$secret_key				= 鍵付きキャラかどうか
			**	$key_result				= 鍵開放アイテム使用の有無
			**	$mail_status['send']	= ユーザー・キャラのステータスで決まるメール送信処理フラグ 0 : 有料 / 1 : 無料
			**	$mail_status['read']	= ユーザー・キャラのステータスで決まるメール開封処理フラグ 0 : 有料 / 1 : 無料
			**	$mail_status['all']		= ユーザー・キャラのステータスで決まるメール送信・開封処理フラグ 0 : 有料 / 1 : 無料
			**	$mail_status['free']	= ユーザーの無料ステータス
			**
			************************************************/

			$parent_id								= NULL;
			$children_id							= NULL;
			$first_children_id						= 0;
			$post_parent_id							= 0;
			$parent_flg								= 0;
			$last_mail_id							= 0;
			$secret_key								= 0;
			$key_result								= 0;
			$mail_status['send']					= 0;
			$mail_status['read']					= 0;
			$mail_status['all']						= 0;
			$mail_status['free']					= 0;


			# 親キャラだったら
			if($character_data['naruto'] == 0){

				# 親キャラID
				$parent_id							= $character_data['id'];

				# 子キャラID
				$children_id						= NULL;

				# CHECK
				$mainClass->debug("CHARACTER TYPE : Parent<br />parent_id : ".$parent_id."<br >children_id : NULL");

			# 子キャラだったら
			}else{

				# 親キャラID
				$parent_id							= $character_data['naruto'];

				# 子キャラID
				$children_id						= $character_data['id'];

				# CHECK
				$mainClass->debug("CHARACTER TYPE : Children<br />parent_id : ".$parent_id."<br >children_id : ".$children_id);


			}


			/************************************************
			**
			**	鍵付きキャラチェック
			**	============================================
			**
			**
			************************************************/

			# 鍵付きかどうか
			$secret_key								= $character_data['media_flg'];

			# 鍵付きキャラだったらアイテム使用中かチェック
			if($secret_key == 1){

				# まず鍵を確認
				$items_conditions						= [
					'character_id'						=> $parent_id
				];
				$items_data								= $itemModel->getItemData($items_conditions);

				# 鍵アイテムあり
				if(!empty($items_data['id'])){

					# 鍵アイテム名
					$key_name							= $items_data['name'];

					# 鍵アイテム画像
					$key_image							= HTTP_ITEM_IMAGE."/".$items_data['image'];

					# ユーザーがそのアイテム持ってるかチェック
					$itembox_conditions					= [
						'user_id'						=> $members_data['id'],
						'item_id'						=> $items_data['id'],
						'status'						=> 0
					];

					$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

					# 持ってる
					if(!empty($itembox_data['id'])){

						# 鍵使って開放してるかチェック
						$itemuse_conditions				= [
							'item_id'					=> $items_data['id'],
							'user_id'					=> $members_data['id'],
							'character_id'				=> $parent_id,
							'status'					=> 0
						];

						$itemuse_rows					= $itemuseModel->getItemuseCount($itemuse_conditions);

						# 開放済み
						if($itemuse_rows > 0){
							$key_result					= 1;
						}

					}

				}

				# 鍵付きキャラなのにアイテム使ってなかったら終了
				if(empty($key_result) || $key_result == 0){

					# CLOSE DATABASE
					$database->closeDb();
					$database->closeStmt();

					# 戻り
					$return_path						= "/character/";

					# ERROR MESSAGE
					$errormessage						= $character_data['nickname']."とやり取りするには<br />";
					$errormessage						.= "<img src=\"".$key_image."\" /><br /><span style=\"color: #FF0000;\">『".$key_name."』</span>が必要です";

					# VIEW FILE チェック & パス生成
					$view_directory						= $mainClass->getViewDirectory($directory,"key_error",$device_file);

					# VIEW FILE 読み込み
					include_once(DOCUMENT_ROOT_VIEWS."/".$device_file."/templates/layout.inc");

					# HTML FOOTER
					$htmlClass->htmlFooter();

					# 終了
					exit();

				}

			}

			/************************************************
			**
			**	キャンペーン
			**	============================================
			**
			**
			************************************************/

			# CAMPAIGN
			$campaign_id							= 0;
			$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);
			$campaign_check							= $memberModel->checkCampaignUpdate($members_data,$campaign_data);

			# campaign_type が2か3か5だったら(消費ポイントキャンペーン)
			if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2 || $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
				$campaign_id						= $campaign_data['id'];
			# それ以外はチェックも外す
			}elseif(empty($campaign_check)){
				$campaign_check						= 0;
			}


			/************************************************
			**
			**	送受信メールの取り扱い
			**	============================================
			**	
			**
			************************************************/

			# 定額・無料ユーザー
			if($members_data['status'] == 2 || $members_data['status'] == 3){

				$mail_status['send']				= 1;
				$mail_status['read']				= 1;
				$mail_status['all']					= 1;
				$mail_status['free']				= 1;

			# 全無料キャラ
			}elseif($character_data['status'] == 7){

				$mail_status['send']				= 1;
				$mail_status['read']				= 1;
				$mail_status['all']					= 1;

			# 送受信無料キャラ
			}elseif($character_data['status'] == 6){

				$mail_status['send']				= 1;
				$mail_status['read']				= 1;
				$mail_status['all']					= 1;

			# 送信無料キャラ
			}elseif($character_data['status'] == 4){

				$mail_status['send']				= 1;

			# 受信無料キャラ
			}elseif($character_data['status'] == 5){

				$mail_status['read']				= 1;

			}



			/************************************************
			**
			**	各種消費ポイントチェック
			**	============================================
			**	これにより表示部分の変更を行う
			**
			************************************************/

			# 完全無料の場合は全てゼロ
			if(!empty($mail_status['all'])){

				$point_data['send']					= 0;
				$point_data['read']					= 0;
				$point_data['image']				= 0;

			# それ以外は計算
			}else{
				$point_data = checkPointData_Kyabaheru($members_data,$character_data);
				//list($mail_status,$point_data) = checkPointData($mail_status, $members_data, $campaign_id);
				
			}


			$mainClass->debug($point_data);


			/************************************************
			**
			**	アクセスが親キャラだったらこの時点で子キャラに差し替え
			**	============================================
			**	mailusersの数で振り分け
			**
			************************************************/

			# 親キャラだったら
			if(empty($children_id)){

				# まず念のため子キャラでやり取りがあるかチェック
				$children_conditions				= [
					'user_id'						=> $members_data['id'],
					'character_id'					=> $parent_id,
					'status'						=> 0

				];
				$children_check		= $mailuserModel->getMailuserDataByNaruto($children_conditions,
													"id,send_id,favorite,favorite_level,manii,reset_date,manii_resets_warned,virtual_age,virtual_name,degree_name");

				# あったら 子キャラID格納&アップデートして処理抜け
				if(!empty($children_check['id'])){

					# キャラID上書き
					$children_id						= $children_check['send_id'];

					# $mailusers_dataに格納
					$mailusers_data						= $children_check;


					/************************************************
					**
					**	MASTER DATABASE切り替え
					**
					************************************************/

					# AUTHORITY / 既にマスターに接続してるかチェック
					$db_auth							 = $database->checkAuthority();

					# DATABASE CHANGE / スレーブだったら
					if(empty($db_auth)){

						# CLOSE DATABASE SLAVE
						$database->closeDb();

						# CONNECT DATABASE MASTER
						$database->connectDb(MASTER_ACCESS_KEY);

						$db_check						= 1;

					}

					# ここで親キャラからの送信をを子キャラにアップデート
					$mails_r_conditions					= [
						'user_id'						=> $members_data['id'],
						'character_id'					=> $parent_id,
						'type'							=> 1
					];

					$mails_r_count						= $mailModel->getMailCount($mails_r_conditions);

					//20190218 add by A.cos
					## 過去メールから(受信)
					$mails_r_count = countFromMailsPast($mails_r_count, $members_data, $parent_id, $array_exit, 1);

					# 親とやりとりあれば
					if($mails_r_count > 0){

						$mails_r_update					= [
							'send_id'					=> $children_check['send_id'],
							'naruto'					=> $parent_id
						];
						$mails_r_update_where						= "send_id = :character_id AND recv_id = :user_id";
						$mails_r_update_conditions[':character_id']	= $parent_id;
						$mails_r_update_conditions[':user_id']		= $members_data['id'];

						# 【UPDATE】mails
						$database->updateDb("mails",$mails_r_update,$mails_r_update_where,$mails_r_update_conditions);

						//20190218 add by A.cos
						## 過去メールから
						foreach($array_exit as $key=>$val){
							if($val){
								$database->updateDb("mails_past_".$key,$mails_r_update,$mails_r_update_where,$mails_r_update_conditions);
							}
						}
					}

					# ここで親キャラへの送信をを子キャラにアップデート
					$mails_s_conditions					= [
						'user_id'						=> $members_data['id'],
						'character_id'					=> $parent_id,
						'type'							=> 2
					];

					$mails_s_count						= $mailModel->getMailCount($mails_s_conditions);

					//20190218 add by A.cos
					## 過去メールから(送信)
					$mails_s_count = countFromMailsPast($mails_s_count, $members_data, $parent_id, $array_exit, 0);

					# 親とやりとりあれば
					if($mails_s_count > 0){
						$mails_s_update					= [
							'recv_id'					=> $children_check['send_id'],
							'naruto'					=> $parent_id
						];
						$mails_s_update_where						= "send_id = :user_id AND recv_id = :character_id";
						$mails_s_update_conditions[':user_id']		= $members_data['id'];
						$mails_s_update_conditions[':character_id']	= $parent_id;

						# 【UPDATE】mails
						$database->updateDb("mails",$mails_s_update,$mails_s_update_where,$mails_s_update_conditions);

						//20190111 add by A.cos
						## 過去メールから
						foreach($array_exit as $key=>$val){
							if($val){
								$database->updateDb("mails_past_".$key,$mails_s_update,$mails_s_update_where,$mails_s_update_conditions);
							}
						}
					}

					# DATABASE CHANGE
					if(!empty($db_check)){

						# CLOSE DATABASE MASTER
						$database->closeDb();

						# CONNECT DATABASE SLAVE
						$database->connectDb();

					}

					# CHECK
					$mainClass->debug("子キャラいたから上書き : Parent<br />parent_id : ".$parent_id."<br >children_id : ".$children_id);


				# もしかしたら親キャラとやってるヤツもいるかも知れない
				}else{
					$parent_conditions				= [
						'user_id'					=> $members_data['id'],
						'character_id'				=> $parent_id,
						'status'					=> 0

					];
					$parent_check			= $mailuserModel->getMailuserData($parent_conditions,
											"id,send_id,favorite,favorite_level,manii,reset_date,manii_resets_warned,virtual_age,virtual_name,degree_name");

					# あったら 一旦子キャラIDに親キャラID格納して処理抜け
					if(!empty($parent_check['id'])){

						# キャラID上書き
						$children_id				= $parent_check['send_id'];

						# $mailusers_dataに格納
						$mailusers_data				= $parent_check;

						# $parent_flgを立てる
						$parent_flg					= 1;

						# CHECK
						$mainClass->debug("親キャラとやりとりしてる : Parent<br />parent_id : ".$parent_id."<br >children_id : ".$children_id);


					# 何もなければ空いてる子キャラに振り分け
					}else{

						# $children_id は NULL のまま
						$children_id				= NULL;

						# membersのuser_countカラムから一番ユーザー数が少ない子キャラを取得
						$first_conditions			= [
							'naruto'				=> $parent_id,
							'allocation'			=> 0,
							'order'					=> 'user_count',
							'limit'					=> 1
						];

						$children_data				= $memberModel->getReleaseMemberId($first_conditions);

						# 子キャラHIT
						if(!empty($children_data['id'])){

							# 親キャラID 格納
							$first_children_id		= $children_data['id'];

							# その上で同報はまだ親から届くため、自動受信チェックは親IDで渡す
							$post_parent_id			= $parent_id;

						# 子キャラ作られてない場合は仕方ないから親キャラとやりとり
						}else{

							# 親キャラID 格納
							$first_children_id		= $parent_id;

						}

					}

				}

			}


			/************************************************
			**
			**	mailusers取得
			**
			************************************************/

			# キャラネーム
			$character_name						= NULL;

			# 好感度パーセント
			$favorite_percent					= 0;

			# 好感度レベル 初期値 : 1
			$favorite_level						= 1;

			# 好感度ゲージ / MAX 100% から差分で表示高さ調整
			$favorite_gauge						= 100;

			# 称号
			$degree_name						= NULL;

			# マニー
			$manii_value	= 0;

			# マニー閾値
			$manii_threshould = $character_data['manii_threshould'];
			//$manii_threshould = 10000;

			//マニーID
			$manii_id = NULL;

			// マニーアイテム名
			$manii_name = NULL;
			
			// マニーアイテム画像
			$manii_image = NULL;

			# ユーザのマニー枚数取得
			$manii_having = 0;

			// まずマニーを確認
			$item_table	= "items";
			$item_select	= "*";
			$item_where	= "site_cd = :site_cd AND effect = :effect AND status = :status";
			$item_array = [
				':site_cd' => $members_data["site_cd"],
				':effect' => 3,
				':status' => 0
			];
			$item_rtn		= $database->selectDb($item_table,$item_select,$item_where,$item_array,$item_order=NULL,1);
			$database->errorDb($item_table, $item_rtn->errorCode(),__FILE__,__LINE__);
			$manii_data = $database->fetchAssoc($item_rtn);

			// マニーアイテムあり
			if(!empty($manii_data['id'])){
				//マニーID
				$manii_id							= $manii_data['id'];
				// マニーアイテム名
				$manii_name							= $manii_data['name'];
				// マニーアイテム画像
				$manii_image							= HTTP_ITEM_IMAGE."/".$manii_data['image'];
				// ユーザーがそのアイテム持ってるかチェック
				$itembox_conditions					= [
					'user_id'						=> $members_data['id'],
					'item_id'						=> $manii_data['id'],
					'status'						=> 0
				];
				$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);
				// 持ってる
				if(!empty($itembox_data['id']) && $itembox_data['unit']){
					$manii_having = intval($itembox_data["unit"])*1000;//マニー数
				}
			}

			# MAIL USER DATA (子キャラのみ)
			if(!empty($children_id) && empty($first_children_id)){

				# 既にあったら取らない
				if(empty($mailusers_data['id'])){
					$mailusers_conditions		= [
						'user_id'				=> $members_data['id'],
						'character_id'			=> $children_id,
						'status'				=> 0
					];

					$mailusers_data				= $mailuserModel->getMailuserData(
													$mailusers_conditions,
													"id,favorite,favorite_level,manii,reset_date,manii_resets_warned,virtual_age,virtual_name,degree_name");
					

				}

				# やり取りあり
				if(!empty($mailusers_data['id'])){

					$contact_flg				= 2;

					# 絵文字セット ネーム
					if(!empty($mailusers_data['virtual_name'])){
						$display_name			= $emoji_obj->emj_decode($mailusers_data['virtual_name']);
						$character_name			= $display_name['web'];
					}else{
						$display_name			= $emoji_obj->emj_decode($character_data['nickname']);
						$character_name			= $display_name['web'];
					}

					$favorite_percent			= $mailusers_data['favorite'];

					$favorite_level				= $mailusers_data['favorite_level'];

					# 差分で%表示
					$favorite_gauge				= $favorite_gauge - $mailusers_data['favorite'];


					/************************************************
					**
					**	称号取得
					**
					************************************************/

					# mailusersに個別称号があればそれ
					if(!empty($mailusers_data['degree_name'])){

						$degree_name				= $mailusers_data['degree_name'];

					# なければdegreesから取得
					}else{
						$degrees_conditions			= [
							'character_id'			=> $parent_id,
							'level'					=> $mailusers_data['favorite_level']
						];

						$degrees_data				= $degreeModel->getDegreeData($degrees_conditions,"name");

						# 称号格納
						if(!empty($degrees_data['name'])){
							$degree_name			= $degrees_data['name'];
						}

					}

					/************************************************
					**
					**	マニーデータ取得
					**
					************************************************/
					# マニー
					$manii_value	= $mailusers_data['manii'];

					# マニー閾値
					$manii_threshould = $character_data['manii_threshould'];
				}

			}

			$mainClass->debug($degree_name);

			# キャラネーム
			if(empty($character_name)){
				$display_name					= $emoji_obj->emj_decode($character_data['nickname']);
				$character_name					= $display_name['web'];
			}



			/************************************************
			**
			**	mailsの抽出ID振り分け
			**	============================================
			**	$character_id			: 画面開いた時に表示するmailsデータのキャラID
			**	$post_character_id		: WEB画面で自動で読み込みにいくmailsデータのキャラID
			**	メールしたことないユーザーは
			**	$character_idが親キャラID
			**	$post_send_idが振り分けられた子キャラID
			**
			**
			************************************************/


			# 親 = やりとり無し
			if(empty($children_id) && !empty($first_children_id)){

				# mails : 親
				$character_id						= $parent_id;

				# 送信 / 自動読み込み対象キャラID -> 振り分けられた子
				$post_send_id						= $first_children_id;

				$mainClass->debug("MAIL CHARACTER : Parent -> ID : ".$character_id);

			# 子 = やりとりあり
			}else{

				# mails : 子
				$character_id						= $children_id;

				# 送信 / 自動読み込み対象キャラID -> そのまま子ID
				$post_send_id						= $children_id;

				$mainClass->debug("MAIL CHARACTER : Children -> ID : ".$character_id);

			}


			/************************************************
			**
			**	やり取り抽出
			**
			************************************************/

			# 次の読み込みの為
			$next_before_id						= 0;

			# 読み込み開始ID
			$start_id							= NULL;

			if(!empty($_POST['next_before_id'])){
				$start_id						= $_POST['next_before_id'];
			}

			# MOREページ読み込み
			if($data['page'] == "more" && !empty($start_id)){

				# MAILS ARRY
				$mails_conditions				= [
					'user_id'					=> $members_data['id'],
					'character_id'				=> $character_id,
					'start_id'					=> $start_id,
					'status'					=> 0,
					'order'						=> 'send_date DESC',
					'limit'						=> $list,
					'type'						=> 3
				];

			# 通常 初回読み込み
			}else{

				# MAILS ARRY
				$mails_conditions				= [
					'user_id'					=> $members_data['id'],
					'character_id'				=> $character_id,
					'status'					=> 0,
					'order'						=> 'send_date DESC',
					'list'						=> $list,
					'set'						=> $set,
					'type'						=> 3
				];

			}

			# キャラから何回送信したかカウント
			$character_send_count				= 0;

			$mails_rtn							= $mailModel->getMailList($mails_conditions,"*");

			$i=0;
			while($mails_data = $database->fetchAssoc($mails_rtn)){

				# キャラからの送信だったら
				if($mails_data['send_id'] == $character_id){

					$send_type						= 1;

					# キャラからの送信カウントアップ
					$character_send_count++;

				# ユーザーからの送信だったら
				}elseif($mails_data['send_id'] == $members_data['id']){

					$send_type						= 2;

				# それ以外は有り得ないから
				}else{

					continue;

				}

				# キャラからのメールにはタイトルがある(絵文字コンバート)
				$display_title						= NULL;
				if(!empty($mails_data['title'])){
					$display_title					= $emoji_obj->emj_decode($mails_data['title']);
				}

				# 内容絵文字コンバート
				$display_message					= $emoji_obj->emj_decode($mails_data['message']);

				# 画像抽出(複数)
				$image_list							= NULL;
				if(!empty($mails_data['media'])){
					$image_list						= explode(",", $mails_data['media']);
					$mainClass->debug($image_list);
				}

				# PRESENT POINT
				$present_point						= NULL;
				if(!empty($mails_data['point'])){
					$present_point					= 1;
				}

				$mail_list['id'][$i]				= $mails_data['id'];
				$mail_list['title'][$i]				= $display_title['web'];
				$mail_list['message'][$i]			= $display_message['web'];
				$mail_list['send_date'][$i]			= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));
				$mail_list['media'][$i]				= $image_list;
				$mail_list['media_flg'][$i]			= $mails_data['media_flg'];
				$mail_list['recv_flg'][$i]			= $mails_data['recv_flg'];
				$mail_list['last_flg'][$i]			= $mails_data['last_flg'];
				$mail_list['send_type'][$i]			= $send_type;
				$mail_list['present'][$i]			= $present_point;
				$next_before_id						= $mails_data['id'];

				$i++;

			}

			$database->freeResult($mails_rtn,1);

			$check_mail_count						= $i;

			## 過去メールから
			$array_mails_past_num = [];
			foreach($array_exit as $key=>$val){
				if($val){
					if(($list - $check_mail_count)>0){//表示を10件にするまであと何件？
						# PARAMETER & WHERE sentence
						$where_past = "";
						$array_past						 = [
							':site_cd' => SITE_CD,
							':del_flg' => 0,
							':recv_flg' => 9,
							':user_id' => $members_data['id'],
							':character_id' => $character_id,
							':user_id_r' => $members_data['id'],
							':character_id_r' => $character_id
						];
						if($next_before_id){
							$array_past[':next_before_id']	 = $next_before_id;
							$where_past .= "site_cd = :site_cd AND id < :next_before_id ";
						}else if(!is_null($start_id)){
							$array_past[':next_before_id']	 = $start_id;
							$where_past .= "site_cd = :site_cd AND id < :next_before_id ";
						}else{
							$where_past .= "site_cd = :site_cd ";
						}
						$where_past .= "AND (send_id = :user_id OR send_id = :character_id) ";
						$where_past .= "AND (recv_id = :user_id_r OR recv_id = :character_id_r) ";
						$where_past .= "AND recv_flg < :recv_flg AND del_flg = :del_flg ";
						//$where_past .= "AND recv_flg > :recv_flg_no_read AND recv_flg < :recv_flg AND del_flg = :del_flg ";
						$order_past = "send_date DESC";
						$limit_past = $list - $check_mail_count;
						$group_past = NULL;
						
						$mails_past_rtn	=	$database->selectDb("mails_past_".$key,"*",$where_past,$array_past,$order_past,$limit_past,$group_past,1);
						$error_past	=	$database->errorDb("GET MAIL PAST LIST",$mails_past_rtn->errorCode(),__FILE__,__LINE__);


						while($mails_past_data = $database->fetchAssoc($mails_past_rtn)){
							
							# キャラからの送信だったら
							if($mails_past_data['send_id'] == $character_id){
			
								$send_type						= 1;
								
								/*
								# 過去はキャラからの未読メールはユーザに見せない
								if($mails_past_data["recv_flg"]<2){
									$i++;//10件に足りないと途中を飛ばして、次の過去テーブルにいくので。ごまかし・・・
									continue;
								}
								*/
								
								# キャラからの送信カウントアップ
								$character_send_count++;
			
							# ユーザーからの送信だったら
							}elseif($mails_past_data['send_id'] == $members_data['id']){
			
								$send_type						= 2;
			
							# それ以外は有り得ないから
							}else{
			
								continue;
			
							}
			
							# キャラからのメールにはタイトルがある(絵文字コンバート)
							$display_title						= NULL;
							if(!empty($mails_past_data['title'])){
								$display_title					= $emoji_obj->emj_decode($mails_past_data['title']);
							}
			
							# 内容絵文字コンバート
							$display_message					= $emoji_obj->emj_decode($mails_past_data['message']);
			
							# 画像抽出(複数)
							$image_list							= NULL;
							if(!empty($mails_past_data['media'])){
								$image_list						= explode(",", $mails_past_data['media']);
								$mainClass->debug($image_list);
							}
			
							# PRESENT POINT
							$present_point						= NULL;
							if(!empty($mails_past_data['point'])){
								$present_point					= 1;
							}
			
							$mail_list['id'][$i]				= $mails_past_data['id'];
							$mail_list['title'][$i]				= $display_title['web'];
							$mail_list['message'][$i]			= $display_message['web'];
							$mail_list['send_date'][$i]			= date("Y年m月d日 H時i分",strtotime($mails_past_data['send_date']));
							$mail_list['media'][$i]				= $image_list;
							$mail_list['media_flg'][$i]			= $mails_past_data['media_flg'];
							$mail_list['recv_flg'][$i]			= $mails_past_data['recv_flg'];
							$mail_list['last_flg'][$i]			= $mails_past_data['last_flg'];
							$mail_list['send_type'][$i]			= $send_type;
							$mail_list['present'][$i]			= $present_point;
							$next_before_id						= $mails_past_data['id'];

							$i++;
						}
						$database->freeResult($mails_past_rtn,1);

						$check_mail_count	=	$i;

						//echo $key."-".($list - $check_mail_count)."<br>";
					}
					
					//各年の過去メール総数をとっておく。
					$array_past2 = [
						':site_cd' => SITE_CD,
						':del_flg' => 0,
						':recv_flg' => 9,
						':user_id' => $members_data['id'],
						':character_id' => $character_id,
						':user_id_r' => $members_data['id'],
						':character_id_r' => $character_id
					];
					
					$where_past2 = "site_cd = :site_cd ";
					$where_past2 .= "AND (send_id = :user_id OR send_id = :character_id) ";
					$where_past2 .= "AND (recv_id = :user_id_r OR recv_id = :character_id_r) ";
					$where_past2 .= "AND recv_flg < :recv_flg AND del_flg = :del_flg ";
					
					$order_past2 = "send_date DESC";
					$limit_past2 = NULL;
					$group_past2 = NULL;
					
					$mails_past_rtn2	=	$database->selectDb("mails_past_".$key,"id",$where_past2,$array_past2,$order_past2,$limit_past2,$group_past2,1);
					$error_past2	=	$database->errorDb("GET MAIL PAST NUM",$mails_past_rtn2->errorCode(),__FILE__,__LINE__);
					
					$array_mails_past_num[$key] = $database->numRows($mails_past_rtn2);
					
				}else{//無い場合はループ続行
					//各年の過去メール総数はゼロ
					$array_mails_past_num[$key] = 0;
					continue;
				}
			}

			# 表示上キャラからのメールが無かったら過去にキャラからのメールがあるかチェックし、キャラからの最終送信メールIDを取る
			if($character_send_count == 0){
				$last_mail_conditions				= [
					'user_id'						=> $members_data['id'],
					'character_id'					=> $character_id,
					'order'							=> "send_date desc",
					'type'							=> 1
				];

				$last_mail_data						= $mailModel->getMailData($last_mail_conditions,"id");

				# あれば
				if(!empty($last_mail_data['id'])){
					$last_mail_id					= $last_mail_data['id'];
				# なければ
				}else{
					$last_mail_id					= 1;
				}

			}



			/************************************************
			**
			**	アイテム関連
			**	============================================
			**	使用中アイテム / 所持アイテム
			**
			************************************************/

			# 返信画面のみ
			if($data['page'] == "detail"){


				/************************************************
				**
				**	キャラのモードチェック
				**	============================================
				**	word = 0 : 平常モード
				**	word = 1 : おやすみモード
				**
				************************************************/

				if($character_data['word'] == 0){
					$attaches_category				= $main_image_category;
				}else{
					$attaches_category				= $sleep_image_category;
				}


				/************************************************
				**
				**	キャラメイン画像抽出
				**	============================================
				**	子キャラだったら必ず必ず親キャラIDで取得
				**	MOREフレームの時は処理しない
				**
				************************************************/

				# 好感度レベル
				$attaches_level						= NULL;

				# mailusersがあれば
				if(!empty($mailusers_data['id'])){

					$attaches_level					= $mailusers_data['favorite_level'];

				# mailusersなければ 初期レベル1
				}else{

					$attaches_level					= 1;

				}
				$attaches_conditions				= [
					'user_id'						=> $parent_id,
					'category'						=> $attaches_category,
					'use_flg'						=> 1,
					'level'							=> $attaches_level,
					'device'						=> $device_number,
					'status'						=> 1,
					'order'							=> 'pay_count',
					'limit'							=> NULL,
					'group'							=> NULL
				];
				$attaches_rtn						= $attachModel->getAttachList($attaches_conditions);

				$i=0;
				while($attaches_data = $database->fetchAssoc($attaches_rtn)){

					$display_image['id'][$i]		= $attaches_data['id'];
					$display_image['image'][$i]		= $attaches_data['attached'];

					$i++;

				}

				# 画像枚数
				$attaches_count						= $i;

				$database->freeResult($attaches_rtn);


				# 設定画像がなければデフォルト画像(level_s = 0 / level_e = 0)を呼び出し
				if($attaches_count == 0){

					$attaches_conditions			= [
						'user_id'					=> $parent_id,
						'category'					=> $attaches_category,
						'use_flg'					=> 1,
						'level'						=> 0,
						'device'					=> $device_number,
						'status'					=> 1,
						'order'						=> 'pay_count',
						'limit'						=> NULL,
						'group'						=> NULL
					];
					$attaches_rtn						= $attachModel->getAttachList($attaches_conditions);

					$i=0;
					while($attaches_data = $database->fetchAssoc($attaches_rtn)){

						$display_image['id'][$i]	= $attaches_data['id'];
						$display_image['image'][$i]	= $attaches_data['attached'];

						$i++;

					}

					# 画像枚数
					$attaches_count					= $i;

					$database->freeResult($attaches_rtn);

				}



				/************************************************
				**
				**	使用中アイテム
				**	===========================================
				**	itemuse_list
				**	===========================================
				**	itemsとJOINして使用中アイテム情報取得
				**
				************************************************/
				$itemuse_check										= [];

				$itemuse_conditions									= [
					'user_id'										=> $members_data['id'],
					'character_id'									=> $parent_id,
					'status'										=> 0,
					'campaign_id'									=> $campaign_id,
					'campaign_type'									=> $campaign_data['campaign_type'],
					'order'											=> 'i.id',
					'page'											=> 'mail'
				];

				$itemuse_list										= $itemuseModel->getItemuseListJoinOnItems($itemuse_conditions);
				$mainClass->debug($itemuse_list);


				/************************************************
				**
				**	所持アイテム
				**	===========================================
				**	ItemBox
				**	===========================================
				**	itemsとJOINして所持アイテム情報取得
				**
				************************************************/

				$item_list											= [];
				$item_list_nouse									= [];
				$item_using											= 0;

				$itembox_conditions									= [
					'user_id'										=> $members_data['id'],
					'status'										=> 0,
					'order'											=> 'i.name'
				];
				$itembox_rtn										= $itemboxModel->getItemboxListJoinOnItems($itembox_conditions);

				$i=0;
				$j=0;
				while($itembox_data = $database->fetchAssoc($itembox_rtn)){

					# 使用中チェック
					$use_check										= NULL;

					if(isset($itemuse_list[$itembox_data['item_id']]['id'])){
						$use_check									= 1;
						$item_using									= 1;
					}

					# 残り数ゼロで使用中データもなければ非表示
					if($itembox_data['unit'] == 0 && empty($use_check)){
						continue;
					}

					# 返信画面用しか使えません
					if($itembox_data['category'] == 0 || $itembox_data['category'] == 1){

						# ここでアイテム効果変動キャンペーンがあるかチェック(アイテム効果変動はcampaign_type = 3)
						if(!empty($campaign_id) && $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
							$items_campaign_conditions				= [
								'item_id'							=> $itembox_data['itembox_id'],
								'campaign_id'						=> $campaign_id,
								'status'							=> 0,
								'order'								=> 'id'
							];

							$items_campaign_data					= $itemModel->getItemData($items_campaign_conditions,"id,description");

							# あれば設定上書き
							if(!empty($items_campaign_data['id'])){

								if(!empty($items_campaign_data['description'])){
									$itembox_data['description']	= $items_campaign_data['description'];
								}

							}

						}


						$item_list['id'][$i]						= $itembox_data['itembox_id'];
						$item_list['item_id'][$i]					= $itembox_data['item_id'];
						$item_list['unit'][$i]						= $itembox_data['unit'];

						if(!empty($itembox_data['name'])){
							$item_list['name'][$i]					= $itembox_data['name'];
						}

						if(!empty($itembox_data['image'])){
							$item_list['image'][$i]					= $itembox_data['image'];
						}

						if(!empty($itembox_data['description'])){
							$item_list['description'][$i]			= $itembox_data['description'];
						}

						$item_list['use_check'][$i]					= $use_check;

						$i++;

					# 使えないアイテム
					}else{

						$item_list_nouse['id'][$j]					= $itembox_data['itembox_id'];
						$item_list_nouse['item_id'][$j]				= $itembox_data['item_id'];
						$item_list_nouse['unit'][$j]				= $itembox_data['unit'];

						if(!empty($itembox_data['name'])){
							$item_list_nouse['name'][$j]			= $itembox_data['name'];
						}

						if(!empty($itembox_data['image'])){
							$item_list_nouse['image'][$j]			= $itembox_data['image'];
						}

						if(!empty($itembox_data['description'])){
							$item_list_nouse['description'][$j]		= $itembox_data['description'];
						}

						$item_list_nouse['use_check'][$j]			= $use_check;

						$j++;

					}

				}

				$database->freeResult($itembox_rtn);

			}



			/************************************************
			**
			**	やり取り総数チェック
			**	===========================================
			**	$listより多ければ『もっと見る』ボタンを表示
			**
			************************************************/

			$more_button								= NULL;

			if($check_mail_count >= $list){


				# COUNT ATTAY
				$count_conditions						= [
					'user_id'							=> $members_data['id'],
					'character_id'						=> $character_id,
					'status'							=> 0,
					'type'								=> 3
				];

				# メールのやり取り総数
				$mail_count								= $mailModel->getMailCount($count_conditions);

				foreach($array_mails_past_num as $val_pastnum){
					$mail_count += $val_pastnum;
				}

				/************************************************
				**
				**	表示番号を計算
				**	やりとり総数から現在の表示set(何メール目か)
				**	を差し引いて計算
				**
				************************************************/

				$start_count							= $mail_count - $set;


				/************************************************
				**
				**	残り非表示メール数を計算
				**	takai
				**
				************************************************/

				# CHECK COUNT
				$check_count							= $mail_count - ($check_mail_count + $set);


				# $check_countが1件以上あればボタン表示
				if($check_count > 0){

					$more_button						= 1;

				}


			}

			/************************************************
			**
			**	MOREページだったらmore.incをここで読んで終了
			**
			************************************************/

			if($data['page'] == "more"){

				# CLOSE DATABASE
				$database->closeDb();
				$database->closeStmt();

				# VIEW FILE チェック & パス生成
				$view_directory							= $mainClass->getViewDirectory($directory,$data['page'],$device_file);

				# 読み込み
				include_once($view_directory);

				# 終了
				exit();

			}


		/************************************************
		**
		**	キャラ存在なし
		**
		************************************************/

		}else{

			$error										= 2;

		}


		# HEADER HIDE
		$header_hide									= 1;

		# FOTTER HIDE
		$footer_hide									= 1;
		//$sub_footer										= 1;




	/************************************************
	**
	**	ITEM
	**	============================================
	**	アイテム使用処理
	**
	************************************************/

	# ITEM
	}elseif($data['page'] == "item"){


		/* ここはitemContollerに移植 */



	/************************************************
	**
	**	CHECK
	**	============================================
	**	最新メールチェック
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# CHECK
	}elseif($data['page'] == "check"){

		/************************************************
		**
		**	最新受信メールチェック
		**	============================================
		**
		**
		************************************************/

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']									= 5;
			$_POST['last_mail_id']						= NULL;
			$_POST['sleep']								= 1;
		}

		# CHARACTER ID
		$character_id									= $data['id'];

		# LAST MAIL ID
		$last_mail_id									= $_POST['last_mail_id'];

		# RESULT
		$result['result']								= NULL;

		# キャラデータ
		$character_data									= $memberModel->getMemberDataById($character_id,NULL,"*");

		# NULL CHECK
		if(!empty($last_mail_id)){


			/************************************************
			**
			**	メール読み込み
			**	============================================
			**	
			**
			************************************************/

			# MAILS ARRY
			$mails_conditions							= [
				'user_id'								=> $members_data['id'],
				'character_id'							=> $character_id,
				'last_mail_id'							=> $last_mail_id,
				'status'								=> 0,
				'order'									=> 'send_date',
				'limit'									=> 1,
				'type'									=> 1
			];

			$mails_rtn									= $mailModel->getMailList($mails_conditions,"*");
			$mails_data									= $database->fetchAssoc($mails_rtn);

			if(!empty($mails_data['id'])){

				# キャラからのメールにはタイトルがある(絵文字コンバート)
				$display_title							= NULL;
				if(!empty($mails_data['title'])){
					$display_title						= $emoji_obj->emj_decode($mails_data['title']);
				}

				# 内容絵文字コンバート
				$display_message						= $emoji_obj->emj_decode($mails_data['message']);

				$result['id']							= $mails_data['id'];
				$result['title']						= $display_title['web'];
				$result['message']						= $display_message['web'];
				$result['send_date']					= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));
				$result['media']						= $mails_data['media'];
				$result['media_flg']					= $mails_data['media_flg'];
				$result['recv_flg']						= $mails_data['recv_flg'];
				$result['last_flg']						= $mails_data['last_flg'];

				$result['result']						= 1;

			}

			$database->freeResult($mails_rtn);

		}



		/************************************************
		**
		**	キャンペーン
		**	============================================
		**	チェック & members UPDATE
		**
		************************************************/

		# CAMPAIGN
		$campaign_id									= 0;
		$campaign_data									= $campaignsetModel->getCampaignsetData($members_data);
		$campaign_check									= $memberModel->checkCampaignUpdate($members_data,$campaign_data);

		# campaign_type が2か3か5だったら(消費ポイント・アイテム効果変動キャンペーン)
		if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2 || $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
			$campaign_id								= $campaign_data['id'];
		# それ以外はチェックも外す
		}elseif(empty($campaign_check)){
			$campaign_check								= 0;
		}


		/************************************************
		**
		**	送信ポイント / 受信ポイントチェック
		**	============================================
		**	
		**
		************************************************/

		if(!empty($result['result']) || $campaign_check >= 2){


			/************************************************
			**
			**	送受信メールの取り扱い
			**	============================================
			**	
			**
			************************************************/

			$mail_status['send']							= 0;
			$mail_status['read']							= 0;
			$mail_status['all']								= 0;
			$mail_status['free']							= 0;
			$mail_status['status']							= 0;

			# 通常ユーザー・通常キャラフラグ
			$normal_status									= 0;

			# 定額・無料ユーザー
			if($members_data['status'] == 2 || $members_data['status'] == 3){

				$mail_status['send']						= 1;
				$mail_status['read']						= 1;
				$mail_status['all']							= 1;
				$mail_status['free']						= 1;

			# 全無料キャラ
			}elseif($character_data['status'] == 7){

				$mail_status['send']						= 1;
				$mail_status['read']						= 1;
				$mail_status['all']							= 1;

			# 送受信無料キャラ
			}elseif($character_data['status'] == 6){

				$mail_status['send']						= 1;
				$mail_status['read']						= 1;
				$mail_status['all']							= 1;

			# 送信無料キャラ
			}elseif($character_data['status'] == 4){

				$mail_status['send']						= 1;

			# 受信無料キャラ
			}elseif($character_data['status'] == 5){

				$mail_status['read']						= 1;

			# 通常キャラ・ユーザー
			}else{

				$normal_status								= 1;

			}


			/************************************************
			**
			**	各種消費ポイントチェック
			**	============================================
			**	これにより表示部分の変更を行う
			**
			************************************************/
			
			# 完全無料の場合は全てゼロ
			if(!empty($mail_status['all'])){

				$point_data['send']							= 0;
				$point_data['read']							= 0;
				$point_data['image']						= 0;

			# それ以外は計算
			}else{
				$point_data = checkPointData_Kyabaheru($members_data,$character_data);
				//list($mail_status,$point_data) = checkPointData($mail_status, $members_data, $campaign_id);
				//mail("eikoshi@k-arat.co.jp","MAIL[CHECK]-POSTDATA",var_export($point_data, true),"From:info@kyabaheru.net");
			}

		}


		/************************************************
		**
		**	アイテム有効期限チェック
		**	============================================
		**	有効期限切れはメソッド内でアップデート
		**
		************************************************/

		# 子キャラだったら親キャラIDでチェック
		if(!empty($character_data['naruto']) || $character_data['naruto'] > 0){
			$itemuse_character_id						= $character_data['naruto'];
		# 親キャラだったらそのまま
		}else{
			$itemuse_character_id						= $character_data['id'];
		}

		$itemuse_list									= [];
		$itemuse_check									= [];

		$itemuse_conditions								= [
			'user_id'									=> $members_data['id'],
			'character_id'								=> $itemuse_character_id,
			'status'									=> 0,
			'order'										=> 'i.id'
		];

		$itemuse_list									= $itemuseModel->checkItemUseLimit($itemuse_conditions);

		# アップデートしたカウントを格納
		$result['itemuse_end_count']					= $itemuse_list['count'];

		# 終了メッセージ
		$result['itemuse_end_message']					= NULL;
		if(!empty($itemuse_list['end_name'])){
			$result['itemuse_end_message']				= $itemuse_list['end_name']."の有効期限が終了しました。";
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
		**	jsonでリザルトを返す
		**
		************************************************/

		//header('Content-Type: application/json; charset=utf-8');
		//print(json_encode($result));
		//exit();


		/************************************************
		**
		**	check.incをここで読んで終了
		**
		************************************************/

		# VIEW FILE チェック & パス生成
		if(!empty($result['result']) || $result['itemuse_end_count'] > 0 || !empty($campaign_check)){
			$view_directory								= $mainClass->getViewDirectory($directory,$data['page'],$device_file);

			# 読み込み
			include_once($view_directory);

		}

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();

		}

		# 終了
		exit();



	/************************************************
	**
	**	SLEEP
	**	============================================
	**	背景切り替えチェック
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# SLEEP
	}elseif($data['page'] == "sleep"){

		/************************************************
		**
		**	最新受信メールチェック
		**	============================================
		**
		**
		************************************************/

		$error											= 0;
		$errormessage									= NULL;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$_POST['sleep']								= 1;
		}

		# CHARACTER ID
		$character_id									= $data['id'];

		# RESULT
		$result['result']								= NULL;

		# キャラデータ
		$character_data									= $memberModel->getMemberDataById($character_id,NULL,"*");


		/************************************************
		**
		**	おやすみチェック
		**	============================================
		**	画像切り替え
		**
		************************************************/

		$sleep_check									= NULL;
		$image_title									= NULL;
		$image_message									= NULL;
		$animation_image								= NULL;
		$display_image									= NULL;
		$attaches_count									= 0;


		if(isset($_POST['sleep']) && $_POST['sleep'] != $character_data['word']){

			/************************************************
			**
			**	キャラのモードチェック
			**	============================================
			**	word = 0 : 平常モード
			**	word = 1 : おやすみモード
			**
			************************************************/

			if($character_data['word'] == 0){
				$attaches_category						= $main_image_category;
			}else{
				$attaches_category						= $sleep_image_category;
			}

			# 好感度レベル
			$attaches_level								= NULL;

			# mailusers check
			$mailusers_conditions						= [
				'user_id'								=> $members_data['id'],
				'character_id'							=> $character_data['id'],
				'status'								=> 0
			];

			$mailusers_data						= $mailuserModel->getMailuserData($mailusers_conditions,
													"id,favorite,favorite_level,manii,reset_date,manii_resets_warned,virtual_age,virtual_name,degree_name");

			# mailusersがあれば
			if(!empty($mailusers_data['id'])){

				$attaches_level							= $mailusers_data['favorite_level'];

			# mailusersなければ 初期レベル1
			}else{

				$attaches_level							= 1;

			}

			# 子キャラなら
			if(!empty($character_data['naruto'])){

				$attaches_user_id						= $character_data['naruto'];

			# 親キャラなら
			}else{

				$attaches_user_id						= $character_data['id'];

			}

			$attaches_conditions						= [
				'user_id'								=> $attaches_user_id,
				'category'								=> $attaches_category,
				'use_flg'								=> 1,
				'level'									=> $attaches_level,
				'device'								=> $device_number,
				'status'								=> 1,
				'order'									=> 'pay_count',
				'limit'									=> NULL,
				'group'									=> NULL
			];
			$attaches_rtn								= $attachModel->getAttachList($attaches_conditions);

			$i=0;
			while($attaches_data = $database->fetchAssoc($attaches_rtn)){

				if($i == 0){
					$animation_image					= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";
				}

				$display_image							.= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";

				$i++;

			}

			# 画像枚数
			$attaches_count								= $i;

			$database->freeResult($attaches_rtn);


			# 設定画像がなければデフォルト画像(level_s = 0 / level_e = 0)を呼び出し
			if($attaches_count == 0){

				$attaches_conditions					= [
					'user_id'							=> $attaches_user_id,
					'category'							=> $attaches_category,
					'use_flg'							=> 1,
					'level'								=> 0,
					'device'							=> $device_number,
					'status'							=> 1,
					'order'								=> 'pay_count',
					'limit'								=> NULL,
					'group'								=> NULL
				];
				$attaches_rtn							= $attachModel->getAttachList($attaches_conditions);

				$i=0;
				while($attaches_data = $database->fetchAssoc($attaches_rtn)){

					if($i == 0){
						$animation_image				= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";
					}

					$display_image						.= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";

					$i++;

				}

				# 画像枚数
				$attaches_count							= $i;

				$database->freeResult($attaches_rtn);

			}


			# 画像抽出OK
			if($attaches_count > 0){

				# おやすみぃ
				if($_POST['sleep'] == 0 && $character_data['word'] > 0){
					$sleep_check						= 1;
					$image_title						= "おやすみなさい";
					$image_message						= $character_data['nickname']."はベッドに入りました";
				}

				# おはよぅ
				if($_POST['sleep'] > 0 && $character_data['word'] == 0){
					$sleep_check						= 2;
					$image_title						= "おはよう";
					$image_message						= $character_data['nickname']."が目を覚ましました";
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
		**	エラー
		**
		************************************************/

		$result['error']								= $error;
		$result['errormessage']							= $errormessage;



		/************************************************
		**
		**	RESULT
		**
		************************************************/

		$result['sleep']								= $character_data['word'];
		$result['title']								= $image_title;
		$result['message']								= $image_message;
		$result['animation']							= $animation_image;
		$result['media']								= $display_image;


		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();
			exit();

		}


		/************************************************
		**
		**	jsonでリザルトを返す
		**
		************************************************/

		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();



	/************************************************
	**
	**	IMAGE
	**	============================================
	**	添付画像取得処理(既読用)
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# READ
	}elseif($data['page'] == "image"){

		$error									= 0;
		$errormessage							= NULL;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']							= 5;
			$_POST['mails_image_id']			= "mails-image-232";
			$_POST['confirmation']				= 0;
		}

		# キャラクターID
		$character_id							= $data['id'];

		# mails ID (分解するよ)
		$mails_parameter						= explode("mails-image-", $_POST['mails_image_id']);
		$mails_id								= $mails_parameter[1];

		# MAILS ID OK
		if(!empty($mails_id) && is_numeric($mails_id)){

			## 20180218 add by A.cos
			/*************************************************
			**
			**	過去テーブルの存在する"年"の配列
			**
			**************************************************/
			$array_exit = checkMailsPastExist($members_data);


			/************************************************
			**
			**	処理開始
			**
			************************************************/

			# MAILS DATA 取得
			$mails_conditions					= [
				'id'							=> $mails_id,
			];
			$mails_column						= "id,media,media_flg";
			$mails_data							= $mailModel->getMailDataById($mails_conditions,$mails_column);

			## 20180218 add by A.cos
			## 見つからない場合は過去メールから
			$mails_data = getImagesFromMailsPast($array_exit, $mails_id);

			# MAILS DATA レコードOK
			if(!empty($mails_data['id'])){

				/************************************************
				**
				**	表示部分生成
				**
				************************************************/

				# 画像抽出(複数)
				$image_list						= NULL;
				$display_image					= NULL;
				$animation_image				= NULL;
				if(!empty($mails_data['media'])){
					$image_list					= explode(",", $mails_data['media']);
					if(!empty($image_list)){
						$animation_image		= "<img src=\"".HTTP_ATTACHES."/".$image_list[0]."\" />";
						foreach($image_list as $key => $value){
							if(empty($value)){ continue; }
							$display_image		.= "<img src=\"".HTTP_ATTACHES."/".$value."\" />";
						}
					}
				}

				$result['id']					= $mails_data['id'];
				$result['animation']			= $animation_image;
				$result['media']				= $display_image;
				$result['media_flg']			= $mails_data['media_flg'];


			# MAILS DATA レコードNG
			}else{

				$error							= 1;
				$errormessage					= "正常に取得できませんでした。";

			}


			/************************************************
			**
			**	DATABASE 切断
			**
			************************************************/

			# CLOSE DATABASE
			$database->closeDb();
			$database->closeStmt();


		# ERROR
		}else{

			$error								= 1;
			$errormessage						= "正常に取得できませんでした。";

		}


		/************************************************
		**
		**	エラー
		**
		************************************************/

		$result['error']							= $error;
		$result['errormessage']						= $errormessage;


		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();
			exit();

		}


		/************************************************
		**
		**	jsonでリザルトを返す
		**
		************************************************/

		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();



	/************************************************
	**
	**	READ
	**	============================================
	**	閲覧処理
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# READ
	}elseif($data['page'] == "read"){

		$error									= 0;
		$errormessage							= NULL;
		$present_point							= NULL;
		$present_title							= NULL;
		$present_message						= NULL;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']							= 12;
			$_POST['mails_id']					= "mails-id-21733";
			$_POST['image_check']				= 1;
			$_POST['confirmation']				= 0;
		}

		# キャラクターID
		$character_id							= $data['id'];

		# mails ID (分解するよ)
		$mails_parameter						= explode("mails-id-", $_POST['mails_id']);
		$mails_id								= $mails_parameter[1];

		# 画像閲覧
		$image_check							= $_POST['image_check'];

		# チケット消費確認フラグ
		$confirmation							= $_POST['confirmation'];

		# 開封消費ポイント / 一応画像閲覧も
		$point_index							= $point_name_array[$data['page']];
		$point_no_id							= $point_no_array[$point_index][2];

		if(!empty($image_check)){
			$point_photo						= $point_name_array['image'];
			$point_no_id						.= ",".$point_no_array[$point_photo][2];
		}

		# 初回フラグ
		$first_mail								= 0;
		
		# 初回メール
		if(!empty($_POST['first_mail'])){
			$first_mail							= $_POST['first_mail'];
			if($first_mail != $character_id){
				$character_id					= $first_mail;
			}
		}

		# MAILS ID OK
		if(!empty($mails_id) && is_numeric($mails_id)){


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

			# キャラデータ
			$character_data							= $memberModel->getMemberDataById($character_id,NULL,"*");

			# 好感度ゲージ初期値
			$favorite_gauge							= 100;

			# 計算ポイント初期化
			$consumption_point						= 0;

			# アップするランキングポイント 初期化 (ここはデフォルト値じゃなくゼロ)
			$consumption_ranking					= 0;

			# アップする好感度ポイント 初期化 (ここはデフォルト値じゃなくゼロ)
			$consumption_favorite					= 0;

			# アイテム終了カウント
			$item_end_count							= 0;

			# アイテム残りカウント
			$item_using_count						= 0;

			//レベルアップ
			# レベルアップフラグ初期化
			$level_up								= NULL;
		
			//レベルアップ時プレゼント
			$present_dialog							= NULL;
			$present_check							= NULL;

			# レベルアップ時表示タイトル初期化
			$level_up_title							= NULL;
	
			# レベルアップ時表示メッセージ初期化
			$level_up_message						= NULL;
			
			# 称号アップ初期化
			$degree_up_name							= NULL;
			
			# 称号アップメッセージ初期化
			$degree_up_message						= NULL;
	
			# レベルアップ時切り替え画像初期化
			$display_image							= NULL;
	
			# レベルアップ時切り替え画像初期化
			$animation_image						= NULL;
	
			# レベルアップ時切り替え画像メッセージ
			$image_up_message						= NULL;
			
			# キャンペーン ID 初期化
			$campaign_id							= 0;

			# イベント ID 初期化
			$event_id								= 0;

			# ランキングイベントチェック(type = 1)
			$events_data							= $eventModel->getEventData($members_data,1);

			# イベント ID 代入 (ランキングイベントがあれば)
			if(!empty($events_data['id']) && $events_data['type'] == 1){
				$event_id							= $events_data['id'];
			}

			# キャンペーンチェック
			$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);

			# campaign_type が2か3か5だったら(消費ポイント・アイテム効果変動キャンペーン)
			if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2 || $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
				$campaign_id						= $campaign_data['id'];
			}

			# 持ちポイント : 消費ポイント チェック(status 0 or 1)
			$point_data								= checkPointConsume_kyabaheru_sp($point_no_id,$members_data,$character_data);
			//$point_data								= $pointsetModel->checkPointConsume($point_no_id,$members_data,$character_data,$campaign_id);

			# ERROR
    		if(empty($point_data) || empty($point_data[0])){
				$error								= 2;
				$errormessage						= TICKET_NAME."が足りません。";
		    }


			/************************************************
			**
			**	処理開始
			**
			************************************************/

			if(empty($error)){

				# 親ID
				if($character_data['naruto'] == 0){
					$parent_id							= $character_data['id'];
				}else{
					$parent_id							= $character_data['naruto'];
				}

				# MAILS DATA 取得
				$mails_conditions					= [
					'id'							=> $mails_id,
				];
				$mails_data							= $mailModel->getMailDataById($mails_conditions);

				# MAILS DATA レコードOK
				if(!empty($mails_data['id'])){


					# 未読の場合のみ ポイント減算 / 既読処理
					if($mails_data['recv_flg'] == 1){


						/************************************************
						**
						**	持ちポイントから消費ポイントの計算メソッド
						**
						************************************************/
						$point_result								 = makePointConsume_kyabaheru_sp($point_data,$members_data);
						//$point_result								 = $pointsetModel->makePointConsume($point_data,$members_data);

						# OK
						if(empty($point_result['error'])){


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


							# 既読処理
							$mails_update							= [
								'recv_flg'							=> 2,
								'point'								=> 0
							];

							# UPDATE WHERE
							$mails_update_where						= "id = :id";
							$mails_update_conditions[':id']			= $mails_data['id'];

							# 【UPDATE】 / mails
							$database->updateDb("mails",$mails_update,$mails_update_where,$mails_update_conditions);



							/************************************************
							**
							**	デフォルトの消費ポイント(チケット)取得
							**
							************************************************/

							# デフォルト 好感度ポイント / ランキングポイント 取得(初期化したものを上書き)
							$favorite_point_no						= $point_no_array[$point_name_array['favorite_read']][2];

							# 無料ポイント
							if(isset($point_result['members']['f_point'])){

								$ranking_point_no					= $point_no_array[$point_name_array['ranking_read_free']][2];

							# 有料ポイント
							}elseif(isset($point_result['members']['point']) || isset($point_result['members']['s_point'])){

								$ranking_point_no					= $point_no_array[$point_name_array['ranking_read_point']][2];

							# ポイント消費なし
							}else{

								//$ranking_point_no					= $point_no_array[$point_name_array['ranking_read_free']][2];
								$ranking_point_no					= NULL;

							}

							$giving_point_no_set					= $favorite_point_no;

							if(!empty($ranking_point_no)){
								$giving_point_no_set				.= ",".$ranking_point_no;
							}

							$pointsets_data							= $pointsetModel->getPointset($giving_point_no_set,$members_data,$campaign_id);

							# OK
							if(!empty($pointsets_data)){

								foreach($pointsets_data as $point_key => $point_value){

									# 付与好感度ポイント あれば上書き
									if($point_value['point_no_id'] == $favorite_point_no && $point_value['point'] != ""){
										$consumption_favorite		= $point_value['point'];
									# 付与ランキングポイント あれば上書き
									}elseif($point_value['point_no_id'] == $ranking_point_no && $point_value['point'] != "" && !empty($ranking_point_no)){
										$consumption_ranking		= $point_value['point'];
									}

								}

							}


							/************************************************
							**
							**	アイテム使用チェック
							**	--------------------------------------------
							**	開封処理では【応援ポイント効果】のみ
							**	$items_data['effect']	= 2
							**
							************************************************/

							//$item_using_count, $itemend_list, $consumption_favorite, $consumption_rankingは
							//JavaScript側のダイアログに表示する内容に関わる
							list($item_using_count, $item_end_count, $itemend_list, $consumption_favorite, $consumption_ranking)
								= checkItemUse($members_data, $parent_id, $campaign_id, $campaign_data,
											$consumption_favorite, $consumption_ranking, $item_end_count);

							/************************************************
							**
							**	初メール 各種処理
							**
							************************************************/
							if(!empty($first_mail)){
								# 好感度計算
								$favorite_percent					= $consumption_favorite;
								
								# アップ後が100超えてたら
								if($favorite_percent >= 100){

									# 100からどれだけオーバーしてるか計算 (下二桁を切り捨て)
									$consumption_level				= substr($favorite_percent,0,strlen($favorite_percent) - 2);

									# 好感度レベル UP
									$before_level					= 1;
									$favorite_level					= 1 + $consumption_level;

									# 付与好感度ポイント
									$favorite_percent				= $favorite_percent - ($consumption_level * 100);

									# レベルアップフラグ
									$level_up						= 1;

									# レベルアップ時表示タイトル
									$level_up_title					= "好感度レベルアップ！！";

									# レベルアップ時表示メッセージ
									$level_up_message				= "好感度100％達成！！<br />好感度レベルが<br /><span class=\"style-red\">【Lv.".$mailusers_data['favorite_level']."】→【Lv.".$favorite_level."】</span><br />になりました！<br /><br />";

								# 100未満
								}else{

									# 好感度レベル据え置き
									$favorite_level					= 1;
								}

								# ここは親キャラからの同報のみなので親キャラで検索
								if(!empty($character_data['naruto']) || $character_data['naruto'] > 0){
									$check_receive_id				= $character_data['naruto'];
								}else{
									$check_receive_id				= $character_data['id'];
								}

								# 過去にキャラから何回メールがあったか
								$receive_conditions					= [
									'character_id'					=> $check_receive_id,
									'user_id'						=> $members_data['id'],
									'type'							=> 1
								];
								$receive_count						= 0;
								$receive_count						= $mailModel->getMailCount($receive_conditions);
								
								/************************************************
								**
								**	キャラの現在の勤務状況を取得
								**
								************************************************/
								# 現在のキャラクターのスケジュールデータ（対象：キャラクターID、消費ポイントID）
								$schedules_data = getCharacterSchedule($members_data,$parent_id);

								# INSERT DATA / mailusers
								$mailusers_insert					= [
									'send_id'						=> $character_data['id'],
									'user_id'						=> $members_data['id'],
									'site_cd'						=> $members_data['site_cd'],
									'pref'							=> $pref_array[$members_data['pref']][1],
									'age'							=> $character_data['age'],
									'favorite'						=> $favorite_percent,
									'favorite_level'				=> $favorite_level,
									'send_cnt'						=> 1,
									'recv_cnt'						=> $receive_count,
									'naruto'						=> $character_data['naruto'],
									//20181022 add by A.cos
									'manii'							=> 0,
									'reset_date'					=> $schedules_data["reset_date"],
									'upd_date'						=> date("YmdHis")
								];

								# MAGICLES
								if(!empty($magicles_data['id'])){
									$mailusers_insert				= [
										'miracle_1'					=> $mailuser['Magicle']['miracle_1'],
										'miracle_2'					=> $mailuser['Magicle']['miracle_2'],
										'miracle_3'					=> $mailuser['Magicle']['miracle_3'],
										'miracle_4'					=> $mailuser['Magicle']['miracle_4'],
										'miracle_5'					=> $mailuser['Magicle']['miracle_5'],
										'miracle_6'					=> $mailuser['Magicle']['miracle_6'],
										'miracle_7'					=> $mailuser['Magicle']['miracle_7'],
										'miracle_8'					=> $mailuser['Magicle']['miracle_8'],
										'miracle_9'					=> $mailuser['Magicle']['miracle_9'],
										'miracle_10'				=> $mailuser['Magicle']['miracle_10'],
										'miracle_11'				=> $mailuser['Magicle']['miracle_11'],
										'miracle_12'				=> $mailuser['Magicle']['miracle_12'],
										'miracle_13'				=> $mailuser['Magicle']['miracle_13'],
										'miracle_14'				=> $mailuser['Magicle']['miracle_14'],
										'miracle_15'				=> $mailuser['Magicle']['miracle_15'],
										'miracle_16'				=> $mailuser['Magicle']['miracle_16'],
										'miracle_17'				=> $mailuser['Magicle']['miracle_17'],
										'miracle_18'				=> $mailuser['Magicle']['miracle_18'],
										'miracle_19'				=> $mailuser['Magicle']['miracle_19'],
										'miracle_20'				=> $mailuser['Magicle']['miracle_20']
									];
								}

								# 【INSERT】mailusers
								$mailusers_id						= $database->insertDb("mailusers",$mailusers_insert);
						
								/************************************************
								**
								**	子キャラ扱いに
								**
								************************************************/
								//# 親とやりとりあれば
								if($receive_count > 0){
									
									# UPDATE MAILS DATA
									$mails_update					= [
										'send_id'					=> $character_data['id'],
										'naruto'					=> $character_data['naruto']
									];
									$mails_update_where				= "send_id = :character_id AND recv_id = :user_id";
									$mails_update_conditions					= [
										':character_id' => $character_data['naruto'],
										':user_id' => $members_data['id']
									];

									# 【UPDATE】mails
									$database->updateDb("mails",$mails_update,$mails_update_where,$mails_update_conditions);

								}
								
								
							}else{
								/************************************************
								**
								**	好感度計算
								**
								************************************************/
								# mailusers取得
								$mailusers_conditions				= [
									'user_id'						=> $members_data['id'],
									'character_id'					=> $character_data['id'],
									'status'						=> 0
								];

								$mailusers_data = $mailuserModel->getMailuserData($mailusers_conditions,
																"id,favorite,favorite_level,manii,reset_date,manii_resets_warned,virtual_age,virtual_name,degree_name,send_cnt,recv_cnt");
								/*
								if(!empty($mailusers_data['age'])){
									$mails_age						= $mailusers_data['age'];
								}else{
									$mails_age						= $character_data['age'];
								}
								*/
								
								# 好感度計算
								$favorite_percent					= $mailusers_data['favorite'] + $consumption_favorite;
								
								# アップ後が100超えてたら
								if($favorite_percent >= 100){

									# 100からどれだけオーバーしてるか計算 (下二桁を切り捨て)
									$consumption_level				= substr($favorite_percent,0,strlen($favorite_percent) - 2);

									# 好感度レベル UP
									$before_level					= $mailusers_data['favorite_level'];
									$favorite_level					= $mailusers_data['favorite_level'] + $consumption_level;

									# 付与好感度ポイント
									$favorite_percent				= $favorite_percent - ($consumption_level * 100);

									# レベルアップフラグ
									$level_up						= 1;

									# レベルアップ時表示タイトル
									$level_up_title					= "好感度レベルアップ！！";

									# レベルアップ時表示メッセージ
									$level_up_message				= "好感度100％達成！！<br />好感度レベルが<br /><span class=\"style-red\">【Lv.".$mailusers_data['favorite_level']."】→【Lv.".$favorite_level."】</span><br />になりました！<br /><br />";

								# 100未満
								}else{

									# 好感度レベル据え置き
									$favorite_level					= $mailusers_data['favorite_level'];


								}

								//$send_countup						= $mailusers_data['send_cnt'] + 1;

								# UPDATE / mailusers
								$mailusers_update					= [
									'favorite'						=> $favorite_percent,
									'favorite_level'				=> $favorite_level,
									'upd_date' => date("YmdHis")
									//'send_cnt'						=> $send_countup
								];

								# UPDATE WHERE
								$mailusers_update_where				= "id = :id";
								$mailusers_update_conditions[':id']	= $mailusers_data['id'];

								# 【UPDATE】 / members
								$database->updateDb("mailusers",$mailusers_update,$mailusers_update_where,$mailusers_update_conditions);
							}

							/************************************************
							**
							**	キャラクターアップデート
							**	===========================================
							**	ランキングポイント加算
							**
							************************************************/

							if($consumption_ranking > 0){

								# 親キャラいたら親キャラに加算
								if(!empty($character_data['naruto'])){

									$parent_data							= $memberModel->getMemberDataById($character_data['naruto'],NULL,"*");

									if(!empty($parent_data['id'])){
										addRankingPoint($event_id, $consumption_ranking, $members_data, $parent_data);
									}

								# こいつが親キャラだったらそのまま加算
								}else{
									addRankingPoint($event_id, $consumption_ranking, $members_data, $character_data);
								}

							}


							/************************************************
							**
							**	ポイント消費
							**
							************************************************/

							if(isset($point_result['points'])){

								# pay_flg 判定 無料ユーザー
								if($members_data['status'] == 3) {
									$pay_flg						= 2;
								# 定額ユーザー
								}elseif($members_data['status'] == 2) {
									$pay_flg						= 3;
								# 通常ユーザー
								}elseif($members_data['status'] != 0){

									# 無課金
									if($members_data['pay_count'] == 0){
										$pay_flg					= 2;
									# 課金
									}else{
										$pay_flg					= 1;
									}

								# その他
								}else{
									$pay_flg						= 0;
								}

								foreach($point_result['points'] as $points_key => $points_array){

									if(isset($points_array['point'])){

										$i=0;
										foreach($points_array['point'] as $key => $value){

											# 消費ポイントレコード1列目に付与好感度ポイント、付与ランキングポイントを入れるよ
											if($i == 0){
												$favorite_point		= $consumption_favorite;
												$ranking_point		= $consumption_ranking;
											}else{
												$favorite_point		= 0;
												$ranking_point		= 0;
											}

											$points_insert			= [
												'user_id'			=> $members_data['id'],
												'site_cd'			=> $members_data['site_cd'],
												'sex'				=> $members_data['sex'],
								                'ad_code'			=> $members_data['ad_code'],
								                'domain_flg'		=> $members_data['domain_flg'],
												'point'				=> $value,
												'favorite_point'	=> $favorite_point,
												'ranking_point'		=> $ranking_point,
												'point_no_id'		=> $points_array['point_no_id'],
												'campaign_id'		=> $campaign_id,
								                'point_type'		=> $key,
												'log_date'			=> date("YmdHis"),
												'chara_id'			=> $mails_data['send_id'],
												'op_id'				=> $mails_data['op_id'],
												'owner_id'			=> $mails_data['owner_id'],
								                'pay_flg'			=> $pay_flg
											];

											# 【insert】points
											$database->insertDb("points",$points_insert);

											$i++;

										}

									}

								}

								# 消費ポイントTOTAL
								$consumption_point					= $point_result['consumption_point'];

								# プレゼントポイントがあったらついでにpointsに入れる
								if(!empty($mails_data['point']) || $mails_data['point'] > 0){

									$mail_recv_point_no_id			= $point_no_array[$point_name_array['mail_recv']][2];
									$point_type						= 1;

									$points_insert					= [
										"user_id"					=> $members_data['id'],
										"site_cd"					=> $members_data['site_cd'],
										"sex"						=> $members_data['sex'],
						                "ad_code"					=> $members_data['ad_code'],
						                "domain_flg"				=> $members_data['domain_flg'],
										"point"						=> $mails_data['point'],
										"point_no_id"				=> $mail_recv_point_no_id,
										"campaign_id"				=> 0,
						                "point_type"				=> $point_type,
										"log_date"					=> date("YmdHis"),
										"chara_id"					=> $mails_data['send_id'],
										"op_id"						=> $mails_data['op_id'],
										"owner_id"					=> $mails_data['owner_id'],
						                "pay_flg"					=> $pay_flg
									];

									# 【insert】points
									$insert_id						= $database->insertDb("points",$points_insert);

									if(!empty($insert_id)){
										$present_point				= $mails_data['point'];
										$present_title				= $character_data['nickname']."からプレゼント！";
										$present_message			= $character_data['nickname']."から<br /><br />";
										$present_message			.= TICKET_NAME." : ".$mails_data['point'].TICKET_UNIT_NAME."<br /><br />";
										$present_message			.= "をプレゼントされました！";
										$members_data['total_point'] += $present_point;
									}

								}


							}


							/************************************************
							**
							**	members point / s_point / f_point アップデート
							**
							************************************************/

							# 通常ユーザーのみ (無料・定額は除外)
							if($members_data['status'] <= 1){

								$members_update								= [];
								if(!empty($point_result['members'])){

									$members_update							= $point_result['members'];

									# チケット消費確認
									if(!empty($confirmation)){

										# confirmation => 0 -> 1 : 送信のみチェックしない
										if($members_data['confirmation'] == 0){
											$members_update['confirmation']	= 1;
										# confirmation => 0 -> 1 : 既に受信はチェックしない場合は全てチェックしないに
										}elseif($members_data['confirmation'] == 2){
											$members_update['confirmation']	= 3;
										}

									}

									# PRESENT POINT
									if(!empty($present_point)){

										# s_pointからポイント消費があったら
										if(isset($members_update['s_point'])){

											$members_update['s_point']		= $members_update['s_point'] + $present_point;

										# s_pointからポイント消費なかったら
										}else{

											$members_update['s_point']		= $members_data['s_point'] + $present_point;

										}

									}


									# UPDATE WHERE
									$members_update_where					= "id = :id";
									$members_update_conditions[':id']		= $members_data['id'];

									# 【UPDATE】 / members
									$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

								}

							}

							/************************************************
							**
							**	貢献度報酬
							**
							************************************************/
							$support_rewards = getSupportRewards($members_data['site_cd'], $event_id, $members_data["id"], $parent_id, $members_data);
							//mail("eikoshi@k-arat.co.jp","SUPPORT_REWARDS[READ]",var_export($support_rewards, true),"From:info@kyabaheru.net");

							/************************************************
							**
							**	レベルアップ時の処理
							**
							************************************************/

							if(!empty($level_up)){
							
								/************************************************
								**
								**	レベルアップ時のプレゼントチェック
								**
								************************************************/
								//$present_check, $present_dialogはJavaScript側のダイアログに表示する内容に関わる
								list($present_check, $present_dialog)
									= checkPresentAtLevelUp($parent_id,$members_data,$present_category_array['levelup'],
															$favorite_level, $number_ticket, $number_item, $number_image);
		
								/************************************************
								**
								**	レベルアップ時の称号チェック
								**
								************************************************/
								//$degree_up_name, $degree_up_messageはJavaScript側のダイアログに表示する内容に関わる
								list($degree_up_name, $degree_up_message)
									= checkDegreeAtLevelUp($parent_id, $before_level, $favorite_level, $mailusers_data);
		
								/************************************************
								**
								**	レベルアップ時の画像チェック
								**	============================================
								**	attachesのlevel_sに該当画像があれば差し替え
								**
								************************************************/
								//$animation_image, $display_image, $attaches_count, $image_up_messageは
								//JavaScript側のダイアログに表示する内容に関わる
								list($animation_image, $display_image, $attaches_count, $image_up_message)
									= checkImageAtLevelUp($character_data, $main_image_category, 
															$before_level, $consumption_level, $device_number,$display_image);
							}

							/***********************************************
							**	特別プレゼント
							**	20180501 add by A.cos
							**	============================================
							**	[パラメータ]
							**	present01_type/present01_targetid/present01_unit 
							**	present02_type/present02_targetid/present02_unit 
							**	present03_type/present03_targetid/present03_unit
							**	
							**	type=1:チケット
							**	type=2:アイテム
							**	type=3:ライブチケット
							**	type=それ以外：なし
							**
							************************************************/
							## プレゼントタグからのプレゼント
							//ダイアログに出力する
							$dialog_mail_present_title = "";
							$dialog_mail_present_message = "";
							$dialog_mail_present_count = 0;

							//1番目
							if($mails_data['present01_type']){
								$dialog_mail_present_title = $character_data['nickname']."から特別プレゼント！";//キャラ名
								$dialog_mail_present_message = sendMailPresent($dialog_mail_present_message, $members_data, 
																	$mails_data['present01_type'], $mails_data['present01_targetid'], $mails_data['present01_unit']);
								$dialog_mail_present_count++;
							}

							//2番目
							if($mails_data['present02_type']){
								$dialog_mail_present_message = sendMailPresent($dialog_mail_present_message, $members_data, 
																	$mails_data['present02_type'], $mails_data['present02_targetid'], $mails_data['present02_unit']);
								$dialog_mail_present_count++;
							}
							
							//3番目
							if($mails_data['present03_type']){
								$dialog_mail_present_message = sendMailPresent($dialog_mail_present_message, $members_data, 
																	$mails_data['present03_type'], $mails_data['present03_targetid'], $mails_data['present03_unit']);
								$dialog_mail_present_count++;
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
								$database->commit();
							# ROLLBACK : 巻き戻し
							}else{
								$database->rollBack();
								$error										= 1;
								$errormessage								= "正常に処理できませんでした。";
							}

						# ERROR
						}else{

							$error											= $point_result['error'];
							$errormessage									= $point_result['errormessage'];

						}

					}


					/************************************************
					**
					**	表示部分生成
					**
					************************************************/

					if(empty($error)){

						$display_title					= NULL;
						if(!empty($mails_data['title'])){
							$display_title				= $emoji_obj->emj_decode($mails_data['title']);
						}

						# 内容絵文字コンバート
						$display_message				= $emoji_obj->emj_decode($mails_data['message']);

						# 画像抽出(複数)
						$image_list						= NULL;
						$display_image					= NULL;
						$animation_image				= NULL;
						if(!empty($mails_data['media'])){
							$image_list					= explode(",", $mails_data['media']);
							if(!empty($image_list)){
								$animation_image		= "<img src=\"".HTTP_ATTACHES."/".$image_list[0]."\" />";
								foreach($image_list as $key => $value){
									if(empty($value)){ continue; }
									$display_image		.= "<img src=\"".HTTP_ATTACHES."/".$value."\" />";
								}
							}
						}

						# RESULT
						$result['id']					= $mails_data['id'];
						$result['title']				= $display_title['web'];
						$result['message']				= $display_message['web'];
						$result['send_date']			= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));
						$result['animation']			= $animation_image;
						$result['media']				= $display_image;
						$result['media_flg']			= $mails_data['media_flg'];
						$result['recv_flg']				= $mails_data['recv_flg'];
						$result['last_flg']				= $mails_data['last_flg'];
						$result['ticket']				= $members_data['total_point'] - $consumption_point;
						$result['present_point']		= $present_point;
						$result['present_title']		= $present_title;
						$result['present_message']		= $present_message;

						$result['item_using_count']		= $item_using_count;
						$result['item_end_count']		= $item_end_count;

						//好感度データ
						$result['favorite_percent']				= $favorite_percent;
						$result['favorite_level']				= $favorite_level;
						$result['favorite_gauge']				= $favorite_gauge - $favorite_percent;

						//レベルアップ時フラグ＆メッセージ
						$result['level_up']						= $level_up;
						$result['level_up_title']				= $level_up_title;
						$result['level_up_message']				= $level_up_message;

						//レベルアップ時称号
						$result['degree_up_name']				= $degree_up_name;
						$result['degree_up_message']			= $degree_up_message;
						
						//レベルアップ時プレゼント
						$result['present_check']				= $present_check;
						$result['present_dialog']				= $present_dialog;

						# レベルアップ時切り替え画像メッセージ
						$result['image_up_message']				= $image_up_message;

						if($item_end_count == 0){
							$result['item_end']			= NULL;
							$result['item_end_id']		= NULL;
						}elseif($item_end_count == 1){
							$result['item_end']			= $itemend_list['end'][0];
							$result['item_end_id']		= $itemend_list['end_id'][0];
						}else{
							$result['item_end']			= $itemend_list['end'];
							$result['item_end_id']		= $itemend_list['end_id'];
						}

						if(isset($point_result['members'])){

							if(isset($point_result['members']['point'])){
								$members_data['point']		= $point_result['members']['point'];
							}

							if(isset($point_result['members']['s_point'])){
								$members_data['s_point']	= $point_result['members']['s_point'];
							}

							if(isset($point_result['members']['f_point'])){
								$members_data['f_point']	= $point_result['members']['f_point'];
							}

							if(!empty($present_point)){
								$members_data['s_point']	+= $present_point;
							}

							$result['user_point']			= $members_data['point'] + $members_data['s_point'];
							$result['free_point']			= $members_data['f_point'];

						}else{
							$result['user_point']			= NULL;
							$result['free_point']			= NULL;
						}


						//20180109 add by A.cos
						//プレゼントタグからのプレゼント
						$result['dialog_mail_present_title'] = $dialog_mail_present_title;
						$result['dialog_mail_present_message'] = $dialog_mail_present_message;
						$result['dialog_mail_present_count'] = $dialog_mail_present_count;

					}

					#	貢献度報酬
					$result['support_rewards_title'] = NULL;
					$result['support_rewards_message'] = NULL;
					$result['support_rewards_count'] = NULL;
					if(!empty($support_rewards)){//"Error"か連想配列(id,name,image,point,unit)
						if($support_rewards!=="Error"){
							$result['support_rewards_title'] = $character_data["nickname"]."、貢献度報酬";
							$result['support_rewards_message'] = "";
							$result['support_rewards_count'] = 0;
							foreach($support_rewards as $key=>$val){
								$result['support_rewards_message'] .= "LP".$val["point"]."ポイント突破報酬！！<br>";
								$result['support_rewards_message'] .= "<img src=\"".HTTP_ITEM_IMAGE."/".$val["image"]."\" height=40 /><br>";
								if($val["unit"]>1)
									$result['support_rewards_message'] .= "『".$val["name"]."』,".$val["unit"]."個ゲット<br>";
								else
									$result['support_rewards_message'] .= "『".$val["name"]."』ゲット<br>";
								$result['support_rewards_message'] .= "<br>";
								$result['support_rewards_count']++;
							}
						}
					}
					//mail("eikoshi@k-arat.co.jp","RESULT[READ]",var_export($result, true),"From:info@kyabaheru.net");
					//mail("eikoshi@k-arat.co.jp","RESULT[READ]",json_encode($result),"From:info@kyabaheru.net");
					$result['error']					= $error;
					$result['errormessage']				= $errormessage;


				# MAILS DATA レコードNG
				}else{

					$error							= 1;
					$errormessage					= "正常に取得できませんでした。";

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


		# ERROR
		}else{

			$error								= 1;
			$errormessage						= "正常に取得できませんでした。";

		}


		/************************************************
		**
		**	エラー
		**
		************************************************/

		$result['error']							= $error;
		$result['errormessage']						= $errormessage;


		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();
			exit();

		}


		/************************************************
		**
		**	jsonでリザルトを返す
		**
		************************************************/

		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();





	/************************************************
	**
	**	SEND
	**	============================================
	**	送信処理
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# SEND
	}elseif($data['page'] == "send"){

		$error									= 0;
		$errormessage							= NULL;

		$present_dialog							= NULL;
		$present_check							= NULL;
		$present_message						= NULL;

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']							= 17;
			//$_POST['first_mail']				= 15;
			$_POST['confirmation']				= 0;
			$_POST['message']					= "TEST";
		}

		# キャラクターID
		$character_id							= $data['id'];

		# チケット消費確認フラグ
		$confirmation							= $_POST['confirmation'];

		# 初回フラグ
		$first_mail								= 0;

		# 初回メール
		if(!empty($_POST['first_mail'])){
			$first_mail							= $_POST['first_mail'];
			if($first_mail != $character_id){
				$character_id					= $first_mail;
			}
		}

		# 送信消費ポイント(チケット)ナンバー // 今回は複数は一旦なし(複数の場合はカンマ区切りで$point_no_idに代入)
		$point_index							= $point_name_array[$data['page']];
		$point_no_id							= $point_no_array[$point_index][2];


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

		# キャラデータ
		$character_data							= $memberModel->getMemberDataById($character_id,NULL,"*");

		# アップするランキングポイント 初期化(デフォルト値)
		//$consumption_ranking					= DEFAULT_RANKING_POINT;

		# アップする好感度ポイント 初期化(デフォルト値)
		//$consumption_favorite					= DEFAULT_FAVORITE_POINT;

		# アップするランキングポイント 初期化 (ここはデフォルト値じゃなくゼロ)
		$consumption_ranking					= 0;

		# アップする好感度ポイント 初期化 (ここはデフォルト値じゃなくゼロ)
		$consumption_favorite					= 0;

		# 好感度ポイント(計算用)初期化
		$favorite_percent						= NULL;

		# 好感度レベル初期化
		$favorite_level							= NULL;

		# レベルアップフラグ初期化
		$level_up								= NULL;

		# レベルアップ時表示タイトル初期化
		$level_up_title							= NULL;

		# レベルアップ時表示メッセージ初期化
		$level_up_message						= NULL;

		# 好感度ゲージ初期値
		$favorite_gauge							= 100;

		# 計算ポイント初期化
		$consumption_point						= 0;

		# 送信メッセージ初期化
		$send_message							= NULL;

		# 称号アップ初期化
		$degree_up_name							= NULL;

		# 称号アップメッセージ初期化
		$degree_up_message						= NULL;

		# レベルアップ時切り替え画像初期化
		$display_image							= NULL;

		# レベルアップ時切り替え画像初期化
		$animation_image						= NULL;

		# レベルアップ時切り替え画像メッセージ
		$image_up_message						= NULL;

		# アイテム終了カウント
		$item_end_count							= 0;

		# アイテム残りカウント
		$item_using_count						= 0;

		# キャンペーン ID 初期化
		$campaign_id							= 0;

		# イベント ID 初期化
		$event_id								= 0;

		# ランキングイベントチェック(type = 1)
		$events_data							= $eventModel->getEventData($members_data,1);

		# イベント ID 代入 (ランキングイベントがあれば)
		if(!empty($events_data['id']) && $events_data['type'] == 1){
			$event_id							= $events_data['id'];
		}

		# キャンペーンチェック
		$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);

		# campaign_type が2か3か5だったら(消費ポイント・アイテム効果変動キャンペーン)
		if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2 || $campaign_data['campaign_type'] == 3 || $campaign_data['campaign_type'] == 5){
			$campaign_id						= $campaign_data['id'];
		}

		# 持ちポイント : 消費ポイントチェック
		$point_data								= checkPointConsume_kyabaheru_sp($point_no_id,$members_data,$character_data);
		//$point_data								= $pointsetModel->checkPointConsume($point_no_id,$members_data,$character_data,$campaign_id);

		# ERROR
		if(empty($point_data) || empty($point_data[0])){
			$error								= 2;
			$errormessage						= TICKET_NAME."が足りません。";
	    }


		/************************************************
		**
		**	処理開始
		**
		************************************************/

		if(empty($error)){

			# 親ID
			if($character_data['naruto'] == 0){
				$parent_id							= $character_data['id'];
			}else{
				$parent_id							= $character_data['naruto'];
			}


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


			# emojiエンコード
			$send_message							= $emoji_obj->emj_encode($_POST['message']);

			# unicode6.0 絵文字のバリデート
			if (preg_match("/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/",$send_message)) {
				$send_message						= preg_replace('/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '〓', $send_message);
			}
			// HTML特殊文字変換
			$send_message = htmlspecialchars($send_message, ENT_QUOTES);

			/************************************************
			**
			**	初メールだったら / age代入
			**
			************************************************/

			if(!empty($first_mail)){

				$mails_age							= $character_data['age'];


			/************************************************
			**
			**	通常送信 / mailusersからage取得
			**
			************************************************/

			}else{


				/************************************************
				**
				**	mailusers取得
				**
				************************************************/
				$mailusers_conditions				= [
					'user_id'						=> $members_data['id'],
					'character_id'					=> $character_data['id'],
					'status'						=> 0
				];

				$mailusers_data				= $mailuserModel->getMailuserData($mailusers_conditions,
												"id,favorite,favorite_level,manii,reset_date,manii_resets_warned,virtual_age,virtual_name,degree_name,send_cnt,recv_cnt");

				if(!empty($mailusers_data['age'])){
					$mails_age						= $mailusers_data['age'];
				}else{
					$mails_age						= $character_data['age'];
				}

			}


			/************************************************
			**
			**	mails insert / array
			**
			************************************************/
			$mails_insert							= [
				'site_cd'							=> $members_data['site_cd'],
				'send_id'							=> $members_data['id'],
				'recv_id'							=> $character_data['id'],
				'send_date'							=> date("YmdHis"),
				'message'							=> $send_message,
				'recv_flg'							=> 1,
				'pref'								=> $members_data['pref'],
				'city'								=> $members_data['city'],
				'age'								=> $mails_age,
				'op_id'								=> $character_data['op_id'],
				'owner_id'							=> $character_data['owner_id'],
				'naruto'							=> $character_data['naruto']
			];

			# 【INSERT】mails
			$insert_id								= $database->insertDb("mails",$mails_insert);


			/************************************************
			**
			**	mails insert ERROR
			**
			************************************************/

			if(empty($insert_id)){

				$error								= 1;
				$errormssage						= "正常に送信できませんでした。";

			}



			/************************************************
			**
			**	ポイント(所持チケット)計算 / INSERT
			**
			************************************************/

			if(empty($error)){


				/************************************************
				**
				**	持ちポイントから消費ポイントの計算メソッド
				**
				************************************************/
				$point_result							= makePointConsume_kyabaheru_sp($point_data,$members_data);
				//$point_result							= $pointsetModel->makePointConsume($point_data,$members_data);

				# OK
				if(empty($point_result['error'])){



					/************************************************
					**
					**	デフォルトの消費ポイント(チケット)取得
					**
					************************************************/

					# デフォルト 好感度ポイント / ランキングポイント 取得(初期化したものを上書き)
					$favorite_point_no						= $point_no_array[$point_name_array['favorite']][2];

					# 無料ポイント
					if(isset($point_result['members']['f_point'])){

						$ranking_point_no					= $point_no_array[$point_name_array['ranking_send_free']][2];

					# 有料ポイント
					}elseif(isset($point_result['members']['point']) || isset($point_result['members']['s_point'])){

						$ranking_point_no					= $point_no_array[$point_name_array['ranking_send_point']][2];

					# ポイント消費なし
					}else{

						//$ranking_point_no					= $point_no_array[$point_name_array['ranking_send_free']][2];
						$ranking_point_no					= NULL;

					}

					$giving_point_no_set					= $favorite_point_no;

					if(!empty($ranking_point_no)){
						$giving_point_no_set				.= ",".$ranking_point_no;
					}

					$pointsets_data							= $pointsetModel->getPointset($giving_point_no_set,$members_data,$campaign_id);

					# OK
					if(!empty($pointsets_data)){

						foreach($pointsets_data as $point_key => $point_value){

							# 付与好感度ポイント あれば上書き
							if($point_value['point_no_id'] == $favorite_point_no && !empty($point_value['point'])){
								$consumption_favorite		= $point_value['point'];
							# 付与ランキングポイント あれば上書き
							}elseif($point_value['point_no_id'] == $ranking_point_no && !empty($point_value['point']) && !empty($ranking_point_no)){
								$consumption_ranking		= $point_value['point'];
							}

						}

					}


					/************************************************
					**
					**	アイテム使用チェック
					**
					************************************************/
					//$item_using_count, $itemend_list, $consumption_favorite, $consumption_rankingは
					//JavaScript側のダイアログに表示する内容に関わる
					list($item_using_count, $item_end_count, $itemend_list, $consumption_favorite, $consumption_ranking)
						= checkItemUse($members_data, $parent_id, $campaign_id, $campaign_data,
									$consumption_favorite, $consumption_ranking, $item_end_count);

					/************************************************
					**
					**	初メール 各種処理
					**
					************************************************/

					if(!empty($first_mail)){

						## 20180218 add by A.cos
						/*************************************************
						**
						**	過去テーブルの存在する"年"の配列
						**
						**************************************************/
						$array_exit = [];
						$array_exit = checkMailsPastExist($members_data);
						
						/************************************************
						**
						**	MAGICLE取得
						**
						************************************************/
						$magicles_conditions				= [
							'pref'							=> $members_data['pref'],
							'city'							=> $members_data['city']
						];
						$magicles_data						= $magicleModel->getMagicleData($magicles_conditions);


						/************************************************
						**
						**	好感度計算
						**
						************************************************/

						# デフォルト好感度レベル
						$favorite_level						= 1;

						# この時点で好感度付与ポイントが100超えてたら計算
						if($consumption_favorite >= 100){

							# 100からどれだけオーバーしてるか計算 (下二桁を切り捨て)
							$consumption_level				= substr( $consumption_favorite,0,strlen($consumption_favorite) - 2);

							# 好感度レベル UP
							$before_level					= $favorite_level;
							$favorite_level					= $favorite_level + $consumption_level;

							# 付与好感度ポイント計算
							$favorite_percent				= $consumption_favorite - ($consumption_level * 100);

							# レベルアップフラグ
							$level_up						= 1;

							# レベルアップ時表示タイトル
							$level_up_title					= "好感度レベルアップ！！";

							# レベルアップ時表示メッセージ
							$level_up_message				= "好感度100％達成！！<br />好感度レベルが<br /><span style=\"color: #FF0000;\">【Lv.".$favorite_level."】→【Lv.".$favorite_level."】</span><br />になりました！<br /><br />";

						# 好感度ポイント100未満
						}else{

							# 付与好感度ポイント代入
							$favorite_percent				= $consumption_favorite;

						}

						# ここは親キャラからの同報のみなので親キャラで検索
						if(!empty($character_data['naruto']) || $character_data['naruto'] > 0){
							$check_receive_id				= $character_data['naruto'];
						}else{
							$check_receive_id				= $character_data['id'];
						}

						# 過去にキャラから何回メールがあったか
						$receive_conditions					= [
							'character_id'					=> $check_receive_id,
							'user_id'						=> $members_data['id'],
							'type'							=> 1
						];
						$receive_count						= 0;
						$receive_count						= $mailModel->getMailCount($receive_conditions);

						/************************************************
						**
						**	キャラの現在の勤務状況を取得
						**
						************************************************/
						//20181022 add by A.cos
						# 現在のキャラクターのスケジュールデータ（対象：キャラクターID、消費ポイントID）
						$schedules_data = getCharacterSchedule($members_data,$parent_id);

						# INSERT DATA / mailusers
			            $mailusers_insert					= [
			                'send_id'						=> $character_data['id'],
			                'user_id'						=> $members_data['id'],
			                'site_cd'						=> $members_data['site_cd'],
			                'pref'							=> $pref_array[$members_data['pref']][1],
			                'age'							=> $character_data['age'],
			                'favorite'						=> $favorite_percent,
			                'favorite_level'				=> $favorite_level,
							'send_cnt'						=> 1,
							'recv_cnt'						=> $receive_count,
							'naruto'						=> $character_data['naruto'],
							//20181022 add by A.cos
							'manii'							=> 0,
							'reset_date'					=> $schedules_data["reset_date"],
			                'upd_date'						=> date("YmdHis")
						];

						# MAGICLES
						if(!empty($magicles_data['id'])){

			                $mailusers_insert				= [
			                    'miracle_1'					=> $mailuser['Magicle']['miracle_1'],
			                    'miracle_2'					=> $mailuser['Magicle']['miracle_2'],
			                    'miracle_3'					=> $mailuser['Magicle']['miracle_3'],
			                    'miracle_4'					=> $mailuser['Magicle']['miracle_4'],
			                    'miracle_5'					=> $mailuser['Magicle']['miracle_5'],
			                    'miracle_6'					=> $mailuser['Magicle']['miracle_6'],
			                    'miracle_7'					=> $mailuser['Magicle']['miracle_7'],
			                    'miracle_8'					=> $mailuser['Magicle']['miracle_8'],
			                    'miracle_9'					=> $mailuser['Magicle']['miracle_9'],
			                    'miracle_10'				=> $mailuser['Magicle']['miracle_10'],
			                    'miracle_11'				=> $mailuser['Magicle']['miracle_11'],
			                    'miracle_12'				=> $mailuser['Magicle']['miracle_12'],
			                    'miracle_13'				=> $mailuser['Magicle']['miracle_13'],
			                    'miracle_14'				=> $mailuser['Magicle']['miracle_14'],
			                    'miracle_15'				=> $mailuser['Magicle']['miracle_15'],
			                    'miracle_16'				=> $mailuser['Magicle']['miracle_16'],
			                    'miracle_17'				=> $mailuser['Magicle']['miracle_17'],
			                    'miracle_18'				=> $mailuser['Magicle']['miracle_18'],
			                    'miracle_19'				=> $mailuser['Magicle']['miracle_19'],
			                    'miracle_20'				=> $mailuser['Magicle']['miracle_20']
							];

						}

						# 【INSERT】mailusers
						$mailusers_id						= $database->insertDb("mailusers",$mailusers_insert);



						/************************************************
						**
						**	各種処理
						**	===========================================
						**	ランキングポイント加算
						**	ユーザーカウント加算
						**	mails アップデート
						**
						************************************************/

						# 親キャラいればランキングポイントは親キャラに、ユーザーカウントはそのまま子キャラに。親キャラからのメールがあれば子キャラ扱いに
						if(!empty($character_data['naruto'])){

							/************************************************
							**
							**	ランキングポイント付与	: 親キャラ
							**
							************************************************/

							$parent_data							= $memberModel->getMemberDataById($character_data['naruto'],NULL,"*");

							if(!empty($parent_data['id'])){
								# ランキングテーブルに親でUPDATE
								addRankingPoint($event_id, $consumption_ranking, $members_data, $parent_data);
							}


							/************************************************
							**
							**	ユーザーカウント処理	: 子キャラ
							**
							************************************************/

							# UPDATE CHILD CHARACTER DATA
							$character_update					= [
								'user_count'					=> $character_data['user_count'] + 1
							];
							$character_update_where				= "id = :id";
							$character_update_conditions[':id']	= $character_data['id'];

							# 【UPDATE】members
							$database->updateDb("members",$character_update,$character_update_where,$character_update_conditions);


							/************************************************
							**
							**	親キャラからの送信があれば全て子キャラ扱い
							**
							************************************************/
							$mails_conditions					= [
								'user_id'						=> $members_data['id'],
								'character_id'					=> $character_data['naruto'],
								'type'							=> 1
							];

							$mails_count						= $mailModel->getMailCount($mails_conditions);

							//20190218 add by A.cos
							## 過去メールから(受信)
							$mails_count = countFromMailsPast($mails_count, $members_data, $character_data['naruto'], $array_exit, 1);
							
							# 親とやりとりあれば
							if($mails_count > 0){

								# UPDATE MAILS DATA
								$mails_update					= [
									'send_id'					=> $character_data['id'],
									'naruto'					=> $character_data['naruto']
								];
								$mails_update_where				= "send_id = :character_id AND recv_id = :user_id";
								$mails_update_conditions					= [
									':character_id' => $character_data['naruto'],
									':user_id' => $members_data['id']
								];

								# 【UPDATE】mails
								$database->updateDb("mails",$mails_update,$mails_update_where,$mails_update_conditions);

								//20190111 add by A.cos
								## 過去メールから
								foreach($array_exit as $key=>$val){
									if($val){
										$database->updateDb("mails_past_".$key,$mails_update,$mails_update_where,$mails_update_conditions);
									}
								}
							}


						# こいつが親キャラだったらそのままカウントアップのみ
						}else{

							/************************************************
							**
							**	ランキングポイント付与	: 親キャラ
							**	ユーザーカウント処理	: 親キャラ
							**
							************************************************/

							# ランキングテーブルに親でUPDATE
							addRankingPoint($event_id, $consumption_ranking, $members_data, $character_data, 1);
							
						}




					/************************************************
					**
					**	通常送信 各種処理
					**
					************************************************/

					}else{


						/************************************************
						**
						**	好感度計算
						**
						************************************************/

						# 好感度計算
						$favorite_percent					= $mailusers_data['favorite'] + $consumption_favorite;

						# アップ後が100超えてたら
						if($favorite_percent >= 100){

							# 100からどれだけオーバーしてるか計算 (下二桁を切り捨て)
							$consumption_level				= substr($favorite_percent,0,strlen($favorite_percent) - 2);

							# 好感度レベル UP
							$before_level					= $mailusers_data['favorite_level'];
							$favorite_level					= $mailusers_data['favorite_level'] + $consumption_level;

							# 付与好感度ポイント
							$favorite_percent				= $favorite_percent - ($consumption_level * 100);

							# レベルアップフラグ
							$level_up						= 1;

							# レベルアップ時表示タイトル
							$level_up_title					= "好感度レベルアップ！！";

							# レベルアップ時表示メッセージ
							$level_up_message				= "好感度100％達成！！<br />好感度レベルが<br /><span class=\"style-red\">【Lv.".$mailusers_data['favorite_level']."】→【Lv.".$favorite_level."】</span><br />になりました！<br /><br />";

						# 100未満
						}else{

							# 好感度レベル据え置き
							$favorite_level					= $mailusers_data['favorite_level'];


						}

						$send_countup						= $mailusers_data['send_cnt'] + 1;

						# UPDATE / mailusers
			            $mailusers_update					= [
			                'favorite'						=> $favorite_percent,
			                'favorite_level'				=> $favorite_level,
							'send_cnt'						=> $send_countup,
							'upd_date' => date("YmdHis")
						];

						# UPDATE WHERE
						$mailusers_update_where				= "id = :id";
						$mailusers_update_conditions[':id']	= $mailusers_data['id'];

						# 【UPDATE】 / members
						$database->updateDb("mailusers",$mailusers_update,$mailusers_update_where,$mailusers_update_conditions);


						/************************************************
						**
						**	キャラクターアップデート
						**	===========================================
						**	ランキングポイント加算
						**
						************************************************/

						# 親キャラいたら親キャラに加算
						if(!empty($character_data['naruto'])){

							$parent_data							= $memberModel->getMemberDataById($character_data['naruto'],NULL,"*");

							if(!empty($parent_data['id'])){

								# ランキングテーブルに親でUPDATE
								addRankingPoint($event_id, $consumption_ranking, $members_data, $parent_data);

							}

						# こいつが親キャラだったらそのまま加算
						}else{

							# ランキングテーブルに親でUPDATE
							addRankingPoint($event_id, $consumption_ranking, $members_data, $character_data);

						}


					}


					/************************************************
					**
					**	共通処理 : pointsにインサート
					**
					************************************************/

					if(isset($point_result['points'])){

						# pay_flg 判定 無料ユーザー
						if($members_data['status'] == 3) {
							$pay_flg						= 2;
						# 定額ユーザー
						}elseif($members_data['status'] == 2) {
							$pay_flg						= 3;
						# 通常ユーザー
						}elseif($members_data['status'] != 0){

							# 無課金
							if($members_data['pay_count'] == 0){
								$pay_flg					= 2;
							# 課金
							}else{
								$pay_flg					= 1;
							}

						# その他
						}else{
							$pay_flg						= 0;
						}

						foreach($point_result['points'] as $points_key => $points_array){

							if(isset($points_array['point'])){

								$i=0;
								foreach($points_array['point'] as $key => $value){

									# 消費ポイントレコード1列目に付与好感度ポイント、付与ランキングポイントを入れるよ
									if($i == 0){
										$favorite_point		= $consumption_favorite;
										$ranking_point		= $consumption_ranking;
									}else{
										$favorite_point		= 0;
										$ranking_point		= 0;
									}

									$points_insert			= [
										'user_id'			=> $members_data['id'],
										'site_cd'			=> $members_data['site_cd'],
										'sex'				=> $members_data['sex'],
						                'ad_code'			=> $members_data['ad_code'],
						                'domain_flg'		=> $members_data['domain_flg'],
										'point'				=> $value,
										'favorite_point'	=> $favorite_point,
										'ranking_point'		=> $ranking_point,
										'point_no_id'		=> $points_array['point_no_id'],
										'campaign_id'		=> $campaign_id,
						                'point_type'		=> $key,
										'log_date'			=> date("YmdHis"),
										'chara_id'			=> $character_data['id'],
										'op_id'				=> $character_data['op_id'],
										'owner_id'			=> $character_data['owner_id'],
						                'pay_flg'			=> $pay_flg
									];

									# 【insert】points
									$database->insertDb("points",$points_insert);

									$i++;

								}

							}

						}

						# 消費ポイントTOTAL
						$consumption_point					= $point_result['consumption_point'];

					}


					/************************************************
					**
					**	共通処理 : members point / s_point / f_point アップデート
					**
					************************************************/

					# 通常ユーザーのみ ( 定額・無料ユーザーはスルー )
					if($members_data['status'] <= 1){

						$members_update							= [];
						if(!empty($point_result['members'])){
							$members_update						= $point_result['members'];
						}

						# チケット消費確認
						if(!empty($confirmation)){

							# confirmation => 0 -> 1 : 送信のみチェックしない
							if($members_data['confirmation'] == 0){
								$members_update['confirmation']	= 1;
							# confirmation => 0 -> 1 : 既に受信はチェックしない場合は全てチェックしないに
							}elseif($members_data['confirmation'] == 2){
								$members_update['confirmation']	= 3;
							}

						}

						# UPDATE
						if(!empty($members_update)){

							# UPDATE WHERE
							$members_update_where				= "id = :id";
							$members_update_conditions[':id']	= $members_data['id'];

							# 【UPDATE】 / members
							$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

						}

					}

					/************************************************
					**
					**	貢献度報酬
					**
					************************************************/
					$support_rewards = getSupportRewards($members_data['site_cd'], $event_id, $members_data["id"], $parent_id, $members_data);
					//mail("eikoshi@k-arat.co.jp","SUPPORT_REWARDS[SEND]",var_export($support_rewards, true),"From:info@kyabaheru.net");

					/************************************************
					**
					**	レベルアップ時の処理
					**
					************************************************/

					if(!empty($level_up)){

						/************************************************
						**
						**	レベルアップ時のプレゼントチェック
						**
						************************************************/
						//$present_check, $present_dialogはJavaScript側のダイアログに表示する内容に関わる
						list($present_check, $present_dialog)
							= checkPresentAtLevelUp($parent_id,$members_data,$present_category_array['levelup'],
													$favorite_level,$number_ticket, $number_item, $number_image);

						/************************************************
						**
						**	レベルアップ時の称号チェック
						**
						************************************************/
						//$present_check, $present_dialogはJavaScript側のダイアログに表示する内容に関わる
						list($degree_up_name, $degree_up_message)
							= checkDegreeAtLevelUp($parent_id, $before_level, $favorite_level, $mailusers_data);

						/************************************************
						**
						**	レベルアップ時の画像チェック
						**	============================================
						**	attachesのlevel_sに該当画像があれば差し替え
						**
						************************************************/
						//$animation_image, $display_image, $attaches_count, $image_up_messageは
						//JavaScript側のダイアログに表示する内容に関わる
						list($animation_image, $display_image, $attaches_count, $image_up_message)
							= checkImageAtLevelUp($character_data, $main_image_category, 
													$before_level, $consumption_level, $device_number,$display_image);
						
					}


				# ERROR
				}else{

					$error									= $point_result['error'];
					$errormessage							= $point_result['errormessage'];

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
				$database->commit();
			# ROLLBACK : 巻き戻し
			}else{
				$database->rollBack();
				$error										= 1;
				$errormessage								= "正常に処理できませんでした。";
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
		**	RESULT
		**
		************************************************/

		$result['error']						= $error;
		$result['errormessage']					= $errormessage;

		$result['character_id']					= $character_data['id'];
		$result['first']						= $first_mail;
		$result['message']						= htmlspecialchars($_POST['message'], ENT_QUOTES);//20170921 updated by A.cos
		//$result['message']						= $_POST['message'];
		$result['ticket']						= $members_data['total_point'] - $consumption_point;

		if(isset($point_result['members'])){

			if(isset($point_result['members']['point'])){
				$members_data['point']			= $point_result['members']['point'];
			}

			if(isset($point_result['members']['s_point'])){
				$members_data['s_point']		= $point_result['members']['s_point'];
			}

			if(isset($point_result['members']['f_point'])){
				$members_data['f_point']		= $point_result['members']['f_point'];
			}

			$result['user_point']				= $members_data['point'] + $members_data['s_point'];
			$result['free_point']				= $members_data['f_point'];

		}else{
			$result['user_point']				= NULL;
			$result['free_point']				= NULL;
		}

		$result['favorite_percent']				= $favorite_percent;
		$result['favorite_level']				= $favorite_level;
		$result['favorite_gauge']				= $favorite_gauge - $favorite_percent;
		$result['level_up']						= $level_up;
		$result['level_up_title']				= $level_up_title;
		$result['level_up_message']				= $level_up_message;

		$result['degree_up_name']				= $degree_up_name;
		$result['degree_up_message']			= $degree_up_message;

		$result['present_check']				= $present_check;
		$result['present_dialog']				= $present_dialog;

		$result['animation']					= $animation_image;
		$result['media']						= $display_image;
		$result['image_up_message']				= $image_up_message;

		$result['item_using_count']				= $item_using_count;
		$result['item_end_count']				= $item_end_count;

		if($item_end_count == 0){
			$result['item_end']					= NULL;
			$result['item_end_id']				= NULL;
		}elseif($item_end_count == 1){
			$result['item_end']					= $itemend_list['end'][0];
			$result['item_end_id']				= $itemend_list['end_id'][0];
		}else{
			$result['item_end']					= $itemend_list['end'];
			$result['item_end_id']				= $itemend_list['end_id'];
		}

		## プレゼントタグからのプレゼント
		//ダイアログに出力する(JavaScript側の都合で一応)
		$result['dialog_mail_present_title'] = "";
		$result['dialog_mail_present_message'] = "";
		$result['dialog_mail_present_count'] = 0;

		#	貢献度報酬
		$result['support_rewards_title'] = NULL;
		$result['support_rewards_message'] = NULL;
		$result['support_rewards_count'] = NULL;
		if(!empty($support_rewards)){//"Error"か連想配列(id,name,image,point,unit)
			if($support_rewards!=="Error"){
				$result['support_rewards_title'] = $character_data["nickname"]."、貢献度報酬";
				$result['support_rewards_message'] = "";
				$result['support_rewards_count'] = 0;//20180412 add by A.cos
				foreach($support_rewards as $key=>$val){
					$result['support_rewards_message'] .= "応援ポイントが".$val["point"]."ポイント突破しました！！<br>";
					$result['support_rewards_message'] .= "<img src=\"".HTTP_ITEM_IMAGE."/".$val["image"]."\" height=40 /><br>";
					if($val["unit"]>1)
						$result['support_rewards_message'] .= "『".$val["name"]."』,".$val["unit"]."個をプレゼントBOXにお届けしました！<br>";
					else
						$result['support_rewards_message'] .= "『".$val["name"]."』をプレゼントBOXにお届けしました！<br>";
					$result['support_rewards_message'] .= "<br>";
					$result['support_rewards_count']++;//20180412 add by A.cos
				}
				
			}
		}
		//mail("eikoshi@k-arat.co.jp","RESULT[SEND]",var_export($result, true),"From:info@kyabaheru.net");
		//mail("eikoshi@k-arat.co.jp","RESULT[SEND]",json_encode($result),"From:info@kyabaheru.net");
		# MAIL CHECK
		//mail("eikoshi@k-arat.co.jp","test",$_POST['message'],"From:info@kyabaheru.net");


		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();
			exit();

		}


		/************************************************
		**
		**	jsonでリザルトを返す
		**
		************************************************/

		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();



	/************************************************
	**
	**	TEST
	**	============================================
	**	TEST
	**
	************************************************/

	# TEST
	}elseif($data['page'] == "test"){




	}

}



/************************************************
**
**	ERROR
**	============================================
**
**	エラー処理
**
************************************************/

# ERROR
if(!empty($error)){

	if(empty($error)){
		$error					= 1;
	}

	# ERROR TITLE
	$error_title				= $mailerror_array[$error][1];

	# ERROR MESSAGE
	$error_message				= $mailerror_array[$error][2];

	# ERROR PAGE
	$data['page']				= "error";


}


################################## VERSION ######################################

$mainClass->debug("Version : 1.02");

################################## FILE END #####################################
?>