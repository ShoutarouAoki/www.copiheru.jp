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

# STOP
if(!defined("SYSTEM_CHECK") || $data['page'] != "trafic"){
	exit();
}

exit();

$exection					= NULL;


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




	# TRAFIC TEST
	}elseif($data['page'] == "trafic"){


		# 強制
		$data['id']						= 12;
		$members_data					= $memberModel->getMemberDataById(1);
		$mail_list						= array();



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
				$items_conditions						= array(
					'character_id'						=> $parent_id
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

						# 鍵使って開放してるかチェック
						$itemuse_conditions				= array();
						$itemuse_conditions				= array(
							'item_id'					=> $items_data['id'],
							'user_id'					=> $members_data['id'],
							'character_id'				=> $parent_id,
							'status'					=> 0
						);

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

				# POINT NO
				$point_no_send						= $point_no_array[$point_name_array['send']][2];
				$point_no_read						= $point_no_array[$point_name_array['read']][2];
				$point_no_image						= $point_no_array[$point_name_array['image']][2];
				$point_no_id						= $point_no_send.",".$point_no_read.",".$point_no_image;

				# pointsets
				$pointsets_data						= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

				# 初期化
				$point_data['send']					= DEFAULT_SEND_POINT;
				$point_data['read']					= DEFAULT_READ_POINT;
				$point_data['image']				= DEFAULT_IMAGE_POINT;

				if(!empty($pointsets_data)){

					$count							= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# 送信
						if($pointsets_data[$i]['point_no_id'] == $point_no_send){
							$point_data['send']		= $pointsets_data[$i]['point'];
						}

						# 開封
						if($pointsets_data[$i]['point_no_id'] == $point_no_read){
							$point_data['read']		= $pointsets_data[$i]['point'];
						}

						# 画像閲覧
						if($pointsets_data[$i]['point_no_id'] == $point_no_image){
							$point_data['image']	= $pointsets_data[$i]['point'];
						}

					}

				}

				# 送信無料上書き
				if(!empty($mail_status['send'])){

					$point_data['send']				= 0;

				# 受信無料上書き
				}elseif(!empty($mail_status['read'])){

					$point_data['read']				= 0;
					$point_data['image']			= 0;

				}

				# $mail_status 再設定 / キャンペーンでゼロ設定がある為
				if($point_data['send'] == 0 && empty($mail_status['send'])){
					$mail_status['send']			= 1;
				}

				if($point_data['read'] == 0 && empty($mail_status['read'])){
					$mail_status['read']			= 1;
					$point_data['image']			= 1;
				}

				if(!empty($mail_status['send']) && !empty($mail_status['read']) && empty($mail_status['all'])){
					$mail_status['all']				= 1;
				}


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
				$children_conditions				= array();
				$children_conditions				= array(
					'user_id'						=> $members_data['id'],
					'character_id'					=> $parent_id,
					'status'						=> 0

				);
				$children_check						= $mailuserModel->getMailuserDataByNaruto($children_conditions,"id,send_id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

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
					$mails_r_conditions					= array();
					$mails_r_conditions					= array(
						'user_id'						=> $members_data['id'],
						'character_id'					=> $parent_id,
						'type'							=> 1
					);

					$mails_r_count						= $mailModel->getMailCount($mails_r_conditions);

					# 親とやりとりあれば
					if($mails_r_count > 0){

						$mails_r_update					= array();
						$mails_r_update					= array(
							'send_id'					=> $children_check['send_id'],
							'naruto'					=> $parent_id
						);
						$mails_r_update_where						= "send_id = :character_id AND recv_id = :user_id";
						$mails_r_update_conditions[':character_id']	= $parent_id;
						$mails_r_update_conditions[':user_id']		= $members_data['id'];

						# 【UPDATE】mails
						$database->updateDb("mails",$mails_r_update,$mails_r_update_where,$mails_r_update_conditions);

					}

					# ここで親キャラへの送信をを子キャラにアップデート
					$mails_s_conditions					= array();
					$mails_s_conditions					= array(
						'user_id'						=> $members_data['id'],
						'character_id'					=> $parent_id,
						'type'							=> 2
					);

					$mails_s_count						= $mailModel->getMailCount($mails_s_conditions);

					# 親とやりとりあれば
					if($mails_s_count > 0){

						$mails_s_update					= array();
						$mails_s_update					= array(
							'recv_id'					=> $children_check['send_id'],
							'naruto'					=> $parent_id
						);
						$mails_s_update_where						= "send_id = :user_id AND recv_id = :character_id";
						$mails_s_update_conditions[':user_id']		= $members_data['id'];
						$mails_s_update_conditions[':character_id']	= $parent_id;

						# 【UPDATE】mails
						$database->updateDb("mails",$mails_s_update,$mails_s_update_where,$mails_s_update_conditions);

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

					$parent_conditions				= array();
					$parent_conditions				= array(
						'user_id'					=> $members_data['id'],
						'character_id'				=> $parent_id,
						'status'					=> 0

					);
					$parent_check					= $mailuserModel->getMailuserData($parent_conditions,"id,send_id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

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
						$first_conditions			= array();
						$first_conditions			= array(
							'naruto'				=> $parent_id,
							'allocation'			=> 0,
							'order'					=> 'user_count',
							'limit'					=> 1
						);

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

			# MAIL USER DATA (子キャラのみ)
			if(!empty($children_id) && empty($first_children_id)){

				# 既にあったら取らない
				if(empty($mailusers_data['id'])){

					$mailusers_conditions		= array();
					$mailusers_conditions		= array(
						'user_id'				=> $members_data['id'],
						'character_id'			=> $children_id,
						'status'				=> 0
					);

					$mailusers_data				= $mailuserModel->getMailuserData($mailusers_conditions,"id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

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

						$degrees_conditions			= array();
						$degrees_conditions			= array(
							'character_id'			=> $parent_id,
							'level'					=> $mailusers_data['favorite_level']
						);

						$degrees_data				= $degreeModel->getDegreeData($degrees_conditions,"name");

						# 称号格納
						if(!empty($degrees_data['name'])){
							$degree_name			= $degrees_data['name'];
						}

					}

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
				$mails_conditions				= array();
				$mails_conditions				= array(
					'user_id'					=> $members_data['id'],
					'character_id'				=> $character_id,
					'start_id'					=> $start_id,
					'status'					=> 0,
					'order'						=> 'send_date DESC',
					'limit'						=> $list,
					'type'						=> 3
				);

			# 通常 初回読み込み
			}else{

				# MAILS ARRY
				$mails_conditions				= array();
				$mails_conditions				= array(
					'user_id'					=> $members_data['id'],
					'character_id'				=> $character_id,
					'status'					=> 0,
					'order'						=> 'send_date DESC',
					'list'						=> $list,
					'set'						=> $set,
					'type'						=> 3
				);

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


			# 表示上キャラからのメールが無かったら過去にキャラからのメールがあるかチェックし、キャラからの最終送信メールIDを取る
			if($character_send_count == 0){

				$last_mail_conditions				= array();
				$last_mail_conditions				= array(
					'user_id'						=> $members_data['id'],
					'character_id'					=> $character_id,
					'order'							=> "send_date desc",
					'type'							=> 1
				);

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

				$attaches_conditions				= array();
				$attaches_conditions				= array(
					'user_id'						=> $parent_id,
					'category'						=> $attaches_category,
					'use_flg'						=> 1,
					'level'							=> $attaches_level,
					'device'						=> $device_number,
					'status'						=> 1,
					'order'							=> 'pay_count',
					'limit'							=> NULL,
					'group'							=> NULL
				);
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

					$attaches_conditions			= array();
					$attaches_conditions			= array(
						'user_id'					=> $parent_id,
						'category'					=> $attaches_category,
						'use_flg'					=> 1,
						'level'						=> 0,
						'device'					=> $device_number,
						'status'					=> 1,
						'order'						=> 'pay_count',
						'limit'						=> NULL,
						'group'						=> NULL
					);
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

				$itemuse_list										= NULL;
				$itemuse_list										= array();
				$itemuse_check										= array();

				$itemuse_conditions									= array(
					'user_id'										=> $members_data['id'],
					'character_id'									=> $parent_id,
					'status'										=> 0,
					'campaign_id'									=> $campaign_id,
					'campaign_type'									=> $campaign_data['campaign_type'],
					'order'											=> 'i.id',
					'page'											=> 'mail'
				);

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

				$item_list											= NULL;
				$item_list											= array();
				$item_list_nouse									= NULL;
				$item_list_nouse									= array();
				$item_using											= 0;

				$itembox_conditions									= array(
					'user_id'										=> $members_data['id'],
					'status'										=> 0,
					'order'											=> 'i.name'
				);
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

							$items_campaign_conditions				= array();
							$items_campaign_conditions				= array(
								'item_id'							=> $itembox_data['itembox_id'],
								'campaign_id'						=> $campaign_id,
								'status'							=> 0,
								'order'								=> 'id'
							);

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
				$count_conditions						= array();
				$count_conditions						= array(
					'user_id'							=> $members_data['id'],
					'character_id'						=> $character_id,
					'status'							=> 0,
					'type'								=> 3
				);

				# メールのやり取り総数
				$mail_count								= $mailModel->getMailCount($count_conditions);


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
				$check_count							= $mail_count - ($i + $set);


				# $check_countが1件以上あればボタン表示
				if($check_count > 0){

					$more_button						= 1;

				}


			}


			ml("TRAFIC TEST OK",SERVER_HOST,$members_data);

		/************************************************
		**
		**	キャラ存在なし
		**
		************************************************/

		}else{

			$error										= 2;

		}


		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();

		pr($mail_list);

		exit();


	}


}


################################## FILE END #####################################
?>