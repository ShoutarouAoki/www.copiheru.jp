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
require_once(DOCUMENT_ROOT_MODELS."/MailUserModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMBOX MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemboxModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

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

/** EMOJI PLUGINS **/
require_once(DOCUMENT_SYSTEM_PLUGINS."/Emoji/lib/mobile_class_8.php");


################################# POST ARRAY ####################################

$value_array				= array('page','id','set');
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
$mailerror_array			= array();
$mailerror_array			= array(
	array('0',	'DEFAULT', ''),
	array('1',	'エラー', '不正なアクセスです。'),
	array('2',	'エラー', 'キャラが存在しません。'),
);

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
$mailuserModel				= new MailUserModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMBOX MODEL
$itemboxModel				= new ItemboxModel($database,$mainClass);

# ITEMUSE MODEL
$itemuseModel				= new ItemuseModel($database,$mainClass);

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

		/************************************************
		**
		**	やり取り中キャラ一覧取得
		**	============================================
		**
		**	getMailUserList
		**
		**	============================================
		**
		**
		************************************************/

		# キャラデータ
		$character_data					= $memberModel->getMemberById($data['id'],NULL,"*");


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
			**	キャンペーン
			**	============================================
			**
			**
			************************************************/

			# CAMPAIGN
			$campaign_id							= 0;
			$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);
			$campaign_check							= $memberModel->checkCampaignUpdate($members_data,$campaign_data);

			# campaign_type が2か3か4だったら(消費ポイントキャンペーン)
			if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] >= 2){
				$campaign_id						= $campaign_data['id'];
			# それ以外はチェックも外す
			}else{
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
				$children_check						= $mailuserModel->getMailUserDataByNaruto($children_conditions,"id,send_id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

				# あったら 子キャラID格納&アップデートして処理抜け
				if(!empty($children_check['id'])){

					# キャラID上書き
					$children_id					= $children_check['send_id'];

					# $mailusers_dataに格納
					$mailusers_data					= $children_check;


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
					$parent_check					= $mailuserModel->getMailUserData($parent_conditions,"id,send_id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

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

					$mailusers_data				= $mailuserModel->getMailUserData($mailusers_conditions,"id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

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
					'last_flg'					=> 0,
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
					'last_flg'					=> 0,
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

				$mail_list['id'][$i]				= $mails_data['id'];
				$mail_list['title'][$i]				= $display_title['web'];
				$mail_list['message'][$i]			= $display_message['web'];
				$mail_list['send_date'][$i]			= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));
				$mail_list['media'][$i]				= $image_list;
				$mail_list['media_flg'][$i]			= $mails_data['media_flg'];
				$mail_list['recv_flg'][$i]			= $mails_data['recv_flg'];
				$mail_list['last_flg'][$i]			= $mails_data['last_flg'];
				$mail_list['send_type'][$i]			= $send_type;
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
					'character_id'									=> $post_send_id,
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
						if(!empty($campaign_id) && $campaign_data['campaign_type'] >= 3){

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
					'last_flg'							=> 0,
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

			$error					= 2;

		}


		# HEADER HIDE
		$header_hide				= 1;

		# FOTTER HIDE
		$footer_hide				= 1;




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
		$character_data									= $memberModel->getMemberById($character_id,NULL,"*");


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
			$mails_conditions							= array();
			$mails_conditions							= array(
				'user_id'								=> $members_data['id'],
				'character_id'							=> $character_id,
				'last_mail_id'							=> $last_mail_id,
				'status'								=> 0,
				'last_flg'								=> 0,
				'order'									=> 'send_date',
				'limit'									=> 1,
				'type'									=> 1
			);

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

		# campaign_type が2か3か4だったら(消費ポイント・アイテム効果変動キャンペーン)
		if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] >= 2){
			$campaign_id								= $campaign_data['id'];
		# それ以外はチェックも外す
		}else{
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

				# POINT NO
				$point_no_send								= $point_no_array[$point_name_array['send']][2];
				$point_no_read								= $point_no_array[$point_name_array['read']][2];
				$point_no_image								= $point_no_array[$point_name_array['image']][2];
				$point_no_id								= $point_no_send.",".$point_no_read.",".$point_no_image;

				# pointsets
				$pointsets_data								= $pointsetModel->getPointset($point_no_id,$members_data,$campaign_id);

				# 初期化
				$point_data['send']							= DEFAULT_SEND_POINT;
				$point_data['read']							= DEFAULT_READ_POINT;
				$point_data['image']						= DEFAULT_IMAGE_POINT;

				if(!empty($pointsets_data)){

					$count									= count($pointsets_data);
					for($i=0;$i<$count;$i++){

						# 送信
						if($pointsets_data[$i]['point_no_id'] == $point_no_send){
							$point_data['send']				= $pointsets_data[$i]['point'];
						}

						# 開封
						if($pointsets_data[$i]['point_no_id'] == $point_no_read){
							$point_data['read']				= $pointsets_data[$i]['point'];
						}

						# 画像閲覧
						if($pointsets_data[$i]['point_no_id'] == $point_no_image){
							$point_data['image']			= $pointsets_data[$i]['point'];
						}

					}

				}

				# 送信無料上書き
				if(!empty($mail_status['send'])){

					$point_data['send']						= 0;

				# 受信無料上書き
				}elseif(!empty($mail_status['read'])){

					$point_data['read']						= 0;
					$point_data['image']					= 0;

				}

				# $mail_status 再設定 / キャンペーンでゼロ設定がある為
				if($point_data['send'] == 0 && empty($mail_status['send'])){
					$mail_status['send']					= 1;
				}

				if($point_data['read'] == 0 && empty($mail_status['read'])){
					$mail_status['read']					= 1;
					$point_data['image']					= 1;
				}

				if(!empty($mail_status['send']) && !empty($mail_status['read']) && empty($mail_status['all'])){
					$mail_status['all']						= 1;
				}

			}

		}


		/************************************************
		**
		**	アイテム有効期限チェック
		**	============================================
		**	有効期限切れはメソッド内でアップデート
		**
		************************************************/

		$itemuse_list									= NULL;
		$itemuse_list									= array();
		$itemuse_check									= array();

		$itemuse_conditions								= array(
			'user_id'									=> $members_data['id'],
			'character_id'								=> $character_id,
			'status'									=> 0,
			'order'										=> 'i.id'
		);

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
		$character_data									= $memberModel->getMemberById($character_id,NULL,"*");


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
			$mailusers_conditions						= array();
			$mailusers_conditions						= array(
				'user_id'								=> $members_data['id'],
				'character_id'							=> $character_data['id'],
				'status'								=> 0
			);

			$mailusers_data								= $mailuserModel->getMailUserData($mailusers_conditions,"id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

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

			$attaches_conditions						= array();
			$attaches_conditions						= array(
				'user_id'								=> $attaches_user_id,
				'category'								=> $attaches_category,
				'use_flg'								=> 1,
				'level'									=> $attaches_level,
				'device'								=> $device_number,
				'status'								=> 1,
				'order'									=> 'pay_count',
				'limit'									=> NULL,
				'group'									=> NULL
			);
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

				$attaches_conditions					= array();
				$attaches_conditions					= array(
					'user_id'							=> $attaches_user_id,
					'category'							=> $attaches_category,
					'use_flg'							=> 1,
					'level'								=> 0,
					'device'							=> $device_number,
					'status'							=> 1,
					'order'								=> 'pay_count',
					'limit'								=> NULL,
					'group'								=> NULL
				);
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


			/************************************************
			**
			**	処理開始
			**
			************************************************/

			# MAILS DATA 取得
			$mails_conditions					= array();
			$mails_conditions					= array(
				'id'							=> $mails_id,
			);
			$mails_column						= "id,media,media_flg";
			$mails_data							= $mailModel->getMailDataById($mails_conditions,$mails_column);

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

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']							= 5;
			$_POST['mails_id']					= "mails-id-303";
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
			$character_data							= $memberModel->getMemberById($character_id,NULL,"*");

			# 計算ポイント初期化
			$consumption_point						= 0;

			# キャンペーン ID 初期化
			$campaign_id							= 0;

			# キャンペーンチェック
			$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);

			# campaign_type が2か3か4だったら(消費ポイント・アイテム効果変動キャンペーン)
			if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] >= 2){
				$campaign_id						= $campaign_data['id'];
			}


			# 持ちポイント : 消費ポイント チェック(status 0 or 1)
			$point_data								= $pointsetModel->checkPointConsume($point_no_id,$members_data,$character_data,$campaign_id);

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

				# MAILS DATA 取得
				$mails_conditions					= array();
				$mails_conditions					= array(
					'id'							=> $mails_id,
				);
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

						$point_result				= $pointsetModel->makePointConsume($point_data,$members_data);

						# OK
						if(empty($point_result['error'])){


							# 既読処理
							$mails_update								= array();
							$mails_update								= array(
								'recv_flg'								=> 2
							);

							# UPDATE WHERE
							$mails_update_where							= "id = :id";
							$mails_update_conditions[':id']				= $mails_data['id'];

							# 【UPDATE】 / mails
							$database->multiUpdateDb("mails",$mails_update,$mails_update_where,$mails_update_conditions);


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
								}elseif($members_data['pay_count'] != 0){

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

										foreach($points_array['point'] as $key => $value){

											$points_insert			= array();
											$points_insert			= array(
												"user_id"			=> $members_data['id'],
												"site_cd"			=> $members_data['site_cd'],
												"sex"				=> $members_data['sex'],
								                "ad_code"			=> $members_data['ad_code'],
								                "domain_flg"		=> $members_data['domain_flg'],
												"point"				=> $value,
												"point_no_id"		=> $points_array['point_no_id'],
												"campaign_id"		=> $campaign_id,
								                "point_type"		=> $key,
												"log_date"			=> date("YmdHis"),
												"chara_id"			=> $mails_data['send_id'],
												"op_id"				=> $mails_data['op_id'],
												"owner_id"			=> $mails_data['owner_id'],
								                "pay_flg"			=> $pay_flg
											);

											# 【insert】points
											$database->multiInsertDb("points",$points_insert);

										}

									}

								}

								# 消費ポイントTOTAL
								$consumption_point						= $point_result['consumption_point'];

							}


							/************************************************
							**
							**	members point / s_point / f_point アップデート
							**
							************************************************/

							# 通常ユーザーのみ (無料・定額は除外)
							if($members_data['status'] <= 1){

								$members_update								= array();
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

									# UPDATE WHERE
									$members_update_where					= "id = :id";
									$members_update_conditions[':id']		= $members_data['id'];

									# 【UPDATE】 / members
									$database->multiUpdateDb("members",$members_update,$members_update_where,$members_update_conditions);

								}

							}

							# 全てのINSERT / UPDATE処理をここで一気に実行。トランザクション/ロールバック
							$exection	= $database->multiExection();
							if(empty($exection)){
								$error									= 1;
								$errormessage							= "正常に処理できませんでした。";
							}

						# ERROR
						}else{

							$error										= $point_result['error'];
							$errormessage								= $point_result['errormessage'];

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

					}

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

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			$data['id']							= 5;
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
		$character_data							= $memberModel->getMemberById($character_id,NULL,"*");

		# アップするランキングポイント 初期化(デフォルト値)
		$consumption_ranking					= DEFAULT_RANKING_POINT;

		# アップする好感度ポイント 初期化(デフォルト値)
		$consumption_favorite					= DEFAULT_FAVORITE_POINT;

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

		# キャンペーンチェック
		$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);

		# campaign_type が2か3か4だったら(消費ポイント・アイテム効果変動キャンペーン)
		if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] >= 2){
			$campaign_id						= $campaign_data['id'];
		}


		# 持ちポイント : 消費ポイントチェック
		$point_data								= $pointsetModel->checkPointConsume($point_no_id,$members_data,$character_data,$campaign_id);

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


			/************************************************
			**
			**	デフォルトの消費ポイント(チケット)取得
			**
			************************************************/

			# デフォルト 好感度ポイント / ランキングポイント 取得(初期化したものを上書き)
			$favorite_point_no						= $point_no_array[$point_name_array['favorite']][2];
			$ranking_point_no						= $point_no_array[$point_name_array['ranking']][2];
			$giving_point_no_set					= $favorite_point_no.",".$ranking_point_no;

			$pointsets_data							= $pointsetModel->getPointset($giving_point_no_set,$members_data,$campaign_id);

			# OK
			if(!empty($pointsets_data)){

				foreach($pointsets_data as $point_key => $point_value){

					# 付与好感度ポイント あれば上書き
					if($point_value['point_no_id'] == $favorite_point_no && !empty($point_value['point'])){
						$consumption_favorite		= $point_value['point'];
					# 付与ランキングポイント あれば上書き
					}elseif($point_value['point_no_id'] == $ranking_point_no && !empty($point_value['point'])){
						$consumption_ranking		= $point_value['point'];
					}

				}

			}


			/************************************************
			**
			**	アイテム使用チェック
			**
			************************************************/

			$itemend_list							= array();

			$itemuse_list							= NULL;
			$itemuse_list							= array();

			$itemuse_conditions						= array();
			$itemuse_conditions						= array(
				'user_id'							=> $members_data['id'],
				'character_id'						=> $character_id,
				'status'							=> 0
			);
			$itemuse_rtn							= $itemuseModel->getItemuseList($itemuse_conditions);

			$i=0;
			while($itemuse_data = $database->fetchAssoc($itemuse_rtn)){

				# 初期化
				$calculation						= NULL;
				$end								= NULL;

				/************************************************
				**
				**	itemsから効果取得 / ひとつだけ取得(複数設定は基本ない)
				**
				************************************************/

				# 初期化
				$items_data							= NULL;
				$items_data							= array();

				#アイテム情報取得
				$items_data							= $itemModel->getItemDataById($itemuse_data['item_id']);

				# キャンペーン中に効果変動設定してあるアイテムがあるか(アイテム効果変動はcampaign_type = 3)
				if(!empty($campaign_id) && $campaign_data['campaign_type'] >= 3){

					$items_campaign_conditions		= array();
					$items_campaign_conditions		= array(
						'item_id'					=> $itemuse_data['item_id'],
						'campaign_id'				=> $campaign_id,
						'status'					=> 0,
						'order'						=> 'id'
					);

					$items_campaign_data			= $itemModel->getItemData($items_campaign_conditions);

					# キャンペーンアイテムがあれば情報上書き
					if(!empty($items_campaign_data['id'])){

						if(!empty($items_campaign_data['description'])){
							$items_data['description']		= $items_campaign_data['description'];
						}

						if(!empty($items_campaign_data['message'])){
							$items_data['message']			= $items_campaign_data['message'];
						}

						if(!empty($items_campaign_data['word'])){
							$items_data['word']				= $items_campaign_data['word'];
						}

						if(!empty($items_campaign_data['count'])){
							$items_data['count']			= $items_campaign_data['count'];
						}

						if(!empty($items_campaign_data['limit_date'])){
							$items_data['limit_date']		= $items_campaign_data['limit_date'];
						}

						if(!empty($items_campaign_data['magnification'])){
							$items_data['magnification']	= $items_campaign_data['magnification'];
						}

					}

				}


				# アイテムデータHITしたら
				if(!empty($items_data['id'])){

					# 返信画面使用アイテムじゃなかったら除外
					if($items_data['category'] != 0 && $items_data['category'] != 1){
						continue;
					}

					# 計算データ
					$calculation_array				= array();
					$calculation_array				= array(
						'favorite'					=> $consumption_favorite,
						'ranking'					=> $consumption_ranking,
						'end'						=> NULL
					);

					# itemuseModel内にて計算
					$calculation_data				= $itemuseModel->calculatePoint($itemuse_data,$items_data,$calculation_array);
					$consumption_favorite			= $calculation_data['favorite'];
					$consumption_ranking			= $calculation_data['ranking'];

					# 使い終わったアイテム
					if(!empty($calculation_data['end'])){
						$itemend_list['end'][$item_end_count]		= $calculation_data['end'];
						$itemend_list['end_id'][$item_end_count]	= $calculation_data['end_id'];
						$item_end_count++;
					}

				}

				$i++;

			}

			$database->freeResult($itemuse_rtn);


			# まだ使用中のアイテムがあるかチェック
			$item_using_count						= $itemuseModel->getItemuseCount($itemuse_conditions);

			# emojiエンコード
			$send_message							= $emoji_obj->emj_encode($_POST['message']);

			# unicode6.0 絵文字のバリデート
			if (preg_match("/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/",$send_message)) {
				$send_message						= preg_replace('/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '〓', $send_message);
			}


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

				$mailusers_conditions				= array();
				$mailusers_conditions				= array(
					'user_id'						=> $members_data['id'],
					'character_id'					=> $character_data['id'],
					'status'						=> 0
				);

				$mailusers_data						= $mailuserModel->getMailUserData($mailusers_conditions,"id,favorite,favorite_level,virtual_age,virtual_name,degree_name");

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

			$mails_insert							= array();
			$mails_insert							= array(
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
			);

			# 【MULTI INSERT】mails
			$database->multiInsertDb("mails",$mails_insert);



			/************************************************
			**
			**	ポイント(所持チケット)計算 / INSERT
			**	==========================================
			**	持ちポイントから消費ポイントの計算メソッド
			**
			************************************************/

			$point_result							= $pointsetModel->makePointConsume($point_data,$members_data);

			# OK
			if(empty($point_result['error'])){

				/************************************************
				**
				**	初メール 各種処理
				**
				************************************************/

				if(!empty($first_mail)){

					/************************************************
					**
					**	MAGICLE取得
					**
					************************************************/

					$magicles_conditions				= array();
					$magicles_conditions				= array(
						'pref'							=> $members_data['pref'],
						'city'							=> $members_data['city']
					);
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

					# INSERT DATA / mailusers
		            $mailusers_insert					= array(
		                'send_id'						=> $character_data['id'],
		                'user_id'						=> $members_data['id'],
		                'site_cd'						=> $members_data['site_cd'],
		                'pref'							=> $pref_array[$members_data['pref']][1],
		                'age'							=> $character_data['age'],
		                'favorite'						=> $favorite_percent,
		                'favorite_level'				=> $favorite_level,
		                'naruto'						=> $character_data['naruto'],
		                'upd_date'						=> date("YmdHis")
		            );

					# MAGICLES
					if(!empty($magicles_data['id'])){

		                $mailusers_insert				= array(
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
		                );

					}

					# 【MULTI INSERT】mailusers
					$database->multiInsertDb("mailusers",$mailusers_insert);


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

						$parent_data							= $memberModel->getMemberById($character_data['naruto'],NULL,"*");

						if(!empty($parent_data['id'])){

							# まずランキングテーブルに親でINSERT
							$ranking_conditions					= array();
							$ranking_conditions					= array(
								'user_id'						=> $members_data['id'],
								'character_id'					=> $parent_data['id'],
								'point'							=> $consumption_ranking,
								'event_id'						=> $event_id
							);

							#【INSERT】rankings takai
							$rankingModel->multiInsertRanking($ranking_conditions);

							# UPDATE PARENT CHARACTER DATA
							$parent_update						= array();
							$parent_update						= array(
								'ranking_point'					=> $parent_data['ranking_point'] + $consumption_ranking,
							);
							$parent_update_where				= "id = :id";
							$parent_update_conditions[':id']	= $parent_data['id'];

							#【UPDATE】members
							$database->multiUpdateDb("members",$parent_update,$parent_update_where,$parent_update_conditions);

						}


						/************************************************
						**
						**	ユーザーカウント処理	: 子キャラ
						**
						************************************************/

						# UPDATE CHILD CHARACTER DATA
						$character_update					= array();
						$character_update					= array(
							'user_count'					=> $character_data['user_count'] + 1
						);
						$character_update_where				= "id = :id";
						$character_update_conditions[':id']	= $character_data['id'];

						# 【UPDATE】members
						$database->multiUpdateDb("members",$character_update,$character_update_where,$character_update_conditions);


						/************************************************
						**
						**	親キャラからの送信があれば全て子キャラ扱い
						**
						************************************************/

						$mails_conditions					= array();
						$mails_conditions					= array(
							'user_id'						=> $members_data['id'],
							'character_id'					=> $character_data['naruto'],
							'type'							=> 1
						);

						$mails_count						= $mailModel->getMailCount($mails_conditions);

						# 親とやりとりあれば
						if($mails_count > 0){

							# UPDATE MAILS DATA
							$mails_update					= array();
							$mails_update					= array(
								'send_id'					=> $character_data['id'],
								'naruto'					=> $character_data['naruto']
							);
							$mails_update_where				= "send_id = :character_id AND recv_id = :user_id";
							$mails_update_conditions[':character_id']	= $character_data['naruto'];
							$mails_update_conditions[':user_id']		= $members_data['id'];

							# 【UPDATE】mails
							$database->multiUpdateDb("mails",$mails_update,$mails_update_where,$mails_update_conditions);

						}


					# こいつが親キャラだったらそのままカウントアップのみ
					}else{

						/************************************************
						**
						**	ランキングポイント付与	: 親キャラ
						**	ユーザーカウント処理	: 親キャラ
						**
						************************************************/

						# まずランキングテーブルに親でINSERT
						$ranking_conditions					= array();
						$ranking_conditions					= array(
							'user_id'						=> $members_data['id'],
							'character_id'					=> $character_data['id'],
							'point'							=> $consumption_ranking,
							'event_id'						=> $event_id
						);

						#【INSERT】rankings takai
						$rankingModel->multiInsertRanking($ranking_conditions);

						# UPDATE CHARACTER DATA
						$character_update					= array();
						$character_update					= array(
							'ranking_point'					=> $character_data['ranking_point'] + $consumption_ranking,
							'user_count'					=> $character_data['user_count'] + 1
						);
						$character_update_where				= "id = :id";
						$character_update_conditions[':id']	= $character_data['id'];

						# 【UPDATE】members
						$database->multiUpdateDb("members",$character_update,$character_update_where,$character_update_conditions);

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

					# UPDATE / mailusers
					$mailusers_update					= array();
		            $mailusers_update					= array(
		                'favorite'						=> $favorite_percent,
		                'favorite_level'				=> $favorite_level
		            );

					# UPDATE WHERE
					$mailusers_update_where				= "id = :id";
					$mailusers_update_conditions[':id']	= $mailusers_data['id'];

					# 【UPDATE】 / members
					$database->multiUpdateDb("mailusers",$mailusers_update,$mailusers_update_where,$mailusers_update_conditions);


					/************************************************
					**
					**	キャラクターアップデート
					**	===========================================
					**	ランキングポイント加算
					**
					************************************************/

					# 親キャラいたら親キャラに加算
					if(!empty($character_data['naruto'])){

						$parent_data							= $memberModel->getMemberById($character_data['naruto'],NULL,"*");

						if(!empty($parent_data['id'])){

							# ランキングテーブルに親でUPDATE
							$ranking_conditions					= array();
							$ranking_conditions					= array(
								'user_id'						=> $members_data['id'],
								'character_id'					=> $parent_data['id'],
								'point'							=> $consumption_ranking,
								'event_id'						=> $event_id
							);

							#【INSERT】rankings takai
							$rankingModel->multiInsertRanking($ranking_conditions);

							# UPDATE PARENT CHARACTER DATA
							$parent_update						= array();
							$parent_update						= array(
								'ranking_point'					=> $parent_data['ranking_point'] + $consumption_ranking,
							);
							$parent_update_where				= "id = :id";
							$parent_update_conditions[':id']	= $parent_data['id'];

							# 【UPDATE】members
							$database->multiUpdateDb("members",$parent_update,$parent_update_where,$parent_update_conditions);

						}

					# こいつが親キャラだったらそのまま加算
					}else{

						# ランキングテーブルに親でUPDATE
						$ranking_conditions					= array();
						$ranking_conditions					= array(
							'user_id'						=> $members_data['id'],
							'character_id'					=> $character_data['id'],
							'point'							=> $consumption_ranking,
							'event_id'						=> $event_id
						);

						#【INSERT】rankings takai
						$rankingModel->multiInsertRanking($ranking_conditions);

						# UPDATE CHARACTER DATA
						$character_update					= array();
						$character_update					= array(
							'ranking_point'					=> $character_data['ranking_point'] + $consumption_ranking
						);
						$character_update_where				= "id = :id";
						$character_update_conditions[':id']	= $character_data['id'];

						# 【UPDATE】members
						$database->multiUpdateDb("members",$character_update,$character_update_where,$character_update_conditions);

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
					}elseif($members_data['pay_count'] != 0){

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

								$points_insert			= array();
								$points_insert			= array(
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
								);

								# 【insert】points
								$database->multiInsertDb("points",$points_insert);

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

					$members_update							= array();
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
						$database->multiUpdateDb("members",$members_update,$members_update_where,$members_update_conditions);

					}

				}


				/************************************************
				**
				**	レベルアップ時の処理
				**
				************************************************/

				if(!empty($level_up)){


					/************************************************
					**
					**	レベルアップ時の称号チェック
					**
					************************************************/

					# mailusersに個別称号がない場合のみ処理
					if(empty($mailusers_data['degree_name'])){

						# まずレベルアップ前の称号を取得
						$before_conditions					= array();
						$before_conditions					= array(
							'character_id'					=> $character_data['id'],
							'level'							=> $before_level
						);

						$before_data						= $degreeModel->getDegreeData($before_conditions,"id,name");

						# OK
						if(!empty($before_data['id'])){

							# レベルアップ後の称号を取得
							$after_conditions				= array();
							$after_conditions				= array(
								'character_id'				=> $character_data['id'],
								'level'						=> $favorite_level
							);

							$after_data						= $degreeModel->getDegreeData($after_conditions,"id,name");

							# OK
							if(!empty($after_data['id'])){

								# 比較
								if($before_data['id'] != $after_data['id']){

									$degree_up_name			 = $after_data['name'];
									$degree_up_message		 = DEGREE_NAME."がアップしました！<br />";
									$degree_up_message		.= "<span class=\"style-red\">【".$before_data['name']."】→【".$after_data['name']."】</span><br /><br />";

								}

							}

						}

					}


					/************************************************
					**
					**	レベルアップ時の画像チェック
					**	============================================
					**	attachesのlevel_sに該当画像があれば差し替え
					**
					************************************************/

					# 平常モード時のみ切り替え
					if($character_data['word'] == 0){

						# 子キャラなら
						if(!empty($character_data['naruto'])){

							$attaches_user_id				= $character_data['naruto'];

						# 親キャラなら
						}else{

							$attaches_user_id				= $character_data['id'];

						}

						# メイン画像カテゴリ
						$attaches_category					= $main_image_category;

						# レベルの上がり幅分ひとつずつ該当画像があるかチェック
						for($i=0;$i<$consumption_level;$i++){

							# 好感度レベル
							$before_level++;

							# レベルアップ後のfavirite_levelでattachesのlevel_sが設定されてたら
							$attaches_check_conditions		= array();
							$attaches_check_conditions		= array(
								'user_id'					=> $attaches_user_id,
								'category'					=> $attaches_category,
								'use_flg'					=> 1,
								'level_s'					=> $before_level,
								'device'					=> $device_number,
								'status'					=> 1,
								'order'						=> 'pay_count',
								'limit'						=> 1,
								'group'						=> NULL
							);
							$check_attaches					= $attachModel->checkAttachByLevel($attaches_check_conditions);

							# 新しい画像あれば
							if($check_attaches > 0){

								# ATTACHES
								$attaches_conditions		= array();
								$attaches_conditions		= array(
									'user_id'				=> $attaches_user_id,
									'category'				=> $attaches_category,
									'use_flg'				=> 1,
									'level'					=> $before_level,
									'device'				=> $device_number,
									'status'				=> 1,
									'order'					=> 'pay_count',
									'limit'					=> NULL,
									'group'					=> NULL
								);
								$attaches_rtn				= $attachModel->getAttachList($attaches_conditions);

								$i=0;
								while($attaches_data = $database->fetchAssoc($attaches_rtn)){

									if($i == 0){
										$animation_image	= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";
									}

									$display_image			.= "<img src=\"".HTTP_ATTACHES."/".$attaches_data['attached']."\" />";

									$i++;

								}

								# 画像枚数
								$attaches_count				= $i;

								$database->freeResult($attaches_rtn);

								# 画像ランクアップメッセージ
								$image_up_message			 = "キャラクター画像がランクアップ！<br /><br />";

								# ループストップ
								break;

							}

						}

					}

				}


			# ERROR
			}else{

				$error									= $point_result['error'];
				$errormessage							= $point_result['errormessage'];

			}


			# 全てのINSERT / UPDATE処理をここで一気に実行。トランザクション/ロールバック
			if(empty($error)){

				$exection	= $database->multiExection();
				if(empty($exection)){
					$error								= 1;
					$errormessage						= "正常に処理できませんでした。";
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
		**	RESULT
		**
		************************************************/

		$result['error']						= $error;
		$result['errormessage']					= $errormessage;

		$result['character_id']					= $character_data['id'];
		$result['first']						= $first_mail;
		$result['message']						= $_POST['message'];
		$result['ticket']						= $members_data['total_point'] - $consumption_point;

		$result['favorite_percent']				= $favorite_percent;
		$result['favorite_level']				= $favorite_level;
		$result['favorite_gauge']				= $favorite_gauge - $favorite_percent;
		$result['level_up']						= $level_up;
		$result['level_up_title']				= $level_up_title;
		$result['level_up_message']				= $level_up_message;

		$result['degree_up_name']				= $degree_up_name;
		$result['degree_up_message']			= $degree_up_message;

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

		# MAIL CHECK
		//mail("takai@k-arat.co.jp","test",$_POST['message'],"From:info@mailanime.net");


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





################################## FILE END #####################################
?>