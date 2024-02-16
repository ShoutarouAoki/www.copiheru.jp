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

/** MAGICLE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MagicleModel.php");

/** ATTACHE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AttacheModel.php");

/** POINTSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/PointsetModel.php");

/** CAMPAIGNSET MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/CampaignsetModel.php");

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

# MAGICLE MODEL
$magicleModel				= new MagicleModel($database,$mainClass);

# ATTACHE MODEL
$attacheModel				= new AttacheModel($database,$mainClass);

# POINTSET MODEL
$pointsetModel				= new PointsetModel($database,$mainClass);

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
			**	親子存在確認
			**	============================================
			**	$parent_id			= 親キャラID
			**	$children_id		= 子キャラID
			**	$first_children_id	= 振り分けられた子キャラID
			**	$parent_flg			= 親キャラとやりとりしてるかのチェック
			**
			************************************************/

			$parent_id							= NULL;
			$children_id						= NULL;
			$first_children_id					= 0;
			$parent_flg							= 0;


			# 親キャラだったら
			if($character_data['naruto'] == 0){

				# 親キャラID
				$parent_id						= $character_data['id'];

				# 子キャラID
				$children_id					= NULL;

				# CHECK
				$mainClass->debug("CHARACTER TYPE : Parent<br />parent_id : ".$parent_id."<br >children_id : NULL");

			# 子キャラだったら
			}else{

				# 親キャラID
				$parent_id						= $character_data['naruto'];

				# 子キャラID
				$children_id					= $character_data['id'];

				# CHECK
				$mainClass->debug("CHARACTER TYPE : Children<br />parent_id : ".$parent_id."<br >children_id : ".$children_id);

			}



			/************************************************
			**
			**	キャラメイン画像抽出
			**	============================================
			**	子キャラだったら必ず必ず親キャラIDで取得
			**	MOREフレームの時は処理しない
			**
			************************************************/

			# 返信画面のみ
			if($data['page'] == "detail"){

				$attaches_conditions				= array();
				$attaches_conditions				= array(
					'user_id'						=> $parent_id,
					'category'						=> $main_image_category,
					'use_flg'						=> 1,
					'status'						=> 1,
					'order'							=> 'pay_count',
					'limit'							=> NULL,
					'group'							=> NULL
				);
				$attaches_rtn						= $attacheModel->getAttacheList($attaches_conditions);

				$i=0;
				while($attaches_data = $database->fetchAssoc($attaches_rtn)){

					$display_image['id'][$i]		= $attaches_data['id'];
					$display_image['image'][$i]		= $attaches_data['attached'];

					$i++;

				}

				# 画像枚数
				$attaches_count						= $i;

				$database->freeResult($attaches_rtn);

			}



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
					'naruto'						=> $parent_id,
					'status'						=> 0

				);
				$children_check						= $mailuserModel->getMailUserDataByNaruto($children_conditions,"id,send_id,favorite,favorite_level,virtual_name,virtual_age");

				# あったら 子キャラID格納して処理抜け
				if(!empty($children_check['id'])){

					# キャラID上書き
					$children_id					= $children_check['send_id'];

					# $mailusers_dataに格納
					$mailusers_data					= $children_check;

					# CHECK
					$mainClass->debug("子キャラいたから上書き : Parent<br />parent_id : ".$parent_id."<br >children_id : ".$children_id);


				# もしかしたら親キャラとやってるヤツもいるかも知れない
				}else{

					$parent_conditions				= array();
					$parent_conditions				= array(
						'user_id'					=> $members_data['id'],
						'chara_id'					=> $parent_id,
						'status'					=> 0

					);
					$parent_check					= $mailuserModel->getMailUserData($parent_conditions,"id,send_id,favorite,favorite_level,virtual_name,virtual_age");

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

			# MAIL USER DATA (子キャラのみ)
			if(!empty($children_id) && empty($first_children_id)){

				# 既にあったら取らない
				if(empty($mailusers_data['id'])){

					$mailusers_conditions			= array();
					$mailusers_conditions			= array(
						'user_id'					=> $members_data['id'],
						'chara_id'					=> $children_id,
						'status'					=> 0
					);

					$mailusers_data					= $mailuserModel->getMailUserData($mailusers_conditions,"id,favorite,favorite_level,virtual_name,virtual_age");

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

				}

			}


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
				$character_id			= $parent_id;

				# 送信 / 自動読み込み対象キャラID -> 振り分けられた子
				$post_send_id			= $first_children_id;

				$mainClass->debug("MAIL CHARACTER : Parent -> ID : ".$character_id);

			# 子 = やりとりあり
			}else{

				# mails : 子
				$character_id			= $children_id;

				# 送信 / 自動読み込み対象キャラID -> そのまま子ID
				$post_send_id			= $children_id;

				$mainClass->debug("MAIL CHARACTER : Children -> ID : ".$character_id);

			}



			/************************************************
			**
			**	やり取り抽出
			**
			************************************************/

			# 次の読み込みの為
			$next_before_id			= 0;

			# 読み込み開始ID
			$start_id				= NULL;

			if(!empty($_POST['next_before_id'])){
				$start_id			= $_POST['next_before_id'];
			}

			# MOREページ読み込み
			if($data['page'] == "more" && !empty($start_id)){

				# MAILS ARRY
				$mails_conditions		= array();
				$mails_conditions		= array(
					'user_id'			=> $members_data['id'],
					'chara_id'			=> $character_id,
					'start_id'			=> $start_id,
					'status'			=> 0,
					'last_flg'			=> 0,
					'order'				=> 'send_date DESC',
					'limit'				=> $list,
					'type'				=> 3
				);

			# 通常 初回読み込み
			}else{

				# MAILS ARRY
				$mails_conditions		= array();
				$mails_conditions		= array(
					'user_id'			=> $members_data['id'],
					'chara_id'			=> $character_id,
					'status'			=> 0,
					'last_flg'			=> 0,
					'order'				=> 'send_date DESC',
					'list'				=> $list,
					'set'				=> $set,
					'type'				=> 3
				);

			}

			$mails_rtn				= $mailModel->getMailList($mails_conditions,"*");

			$i=0;
			while($mails_data = $database->fetchAssoc($mails_rtn)){

				# キャラからの送信だったら
				if($mails_data['send_id'] == $character_id){

					$send_type						= 1;

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


				$mail_list['id'][$i]				= $mails_data['id'];
				$mail_list['title'][$i]				= $display_title['web'];
				$mail_list['message'][$i]			= $display_message['web'];
				$mail_list['send_date'][$i]			= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));
				$mail_list['media'][$i]				= $mails_data['media'];
				$mail_list['media_flg'][$i]			= $mails_data['media_flg'];
				$mail_list['recv_flg'][$i]			= $mails_data['recv_flg'];
				$mail_list['last_flg'][$i]			= $mails_data['last_flg'];
				$mail_list['send_type'][$i]			= $send_type;
				$next_before_id						= $mails_data['id'];

				$i++;

			}

			$database->freeResult($mails_rtn,1);

			$check_mail_count						= $i;



			/************************************************
			**
			**	アイテム情報
			**	===========================================
			**	itemsから一旦アイテム基本情報を持ってきて配列に格納
			**
			************************************************/

			$items_conditions						= array();
			$items_conditions						= array(
				'start_date'						=> date("YmdHis"),
				'item_id'							=> 0,
				'campaign_id'						=> 0,
				'status'							=> 0,
				'order'								=> 'id'
			);

			$items_rtn								= $itemModel->getItemList($items_conditions);

			$i=0;
			while($items_data = $database->fetchAssoc($items_rtn)){

				$index								= $items_data['id'];

				$item_info[$index]['id']			= $items_data['id'];
				$item_info[$index]['name']			= $items_data['name'];
				$item_info[$index]['image']			= $items_data['image'];
				$item_info[$index]['description']	= $items_data['description'];
				$item_info[$index]['category']		= $items_data['category'];

				$i++;


			}

			$database->freeResult($items_rtn);


			/************************************************
			**
			**	所持アイテム
			**	===========================================
			**	ItemBox
			**	===========================================
			**	取得したitem_idから$item_infoに格納された情報を取り出す
			**
			************************************************/

			$itembox_conditions						= array();
			$itembox_conditions						= array(
				'user_id'							=> $members_data['id'],
				'status'							=> 0,
				'order'								=> 'id'
			);

			$itembox_rtn							= $itemboxModel->getItemBoxList($itembox_conditions);

			$i=0;
			$j=0;
			while($itembox_data = $database->fetchAssoc($itembox_rtn)){


				# 返信画面用しか使えません
				if($item_info[$itembox_data['item_id']]['category'] == 0 || $item_info[$itembox_data['item_id']]['category'] == 1){

					$item_list['id'][$i]						= $itembox_data['id'];
					$item_list['unit'][$i]						= $itembox_data['unit'];

					if(isset($item_info[$itembox_data['item_id']]['name'])){
						$item_list['name'][$i]					= $item_info[$itembox_data['item_id']]['name'];
					}

					if(isset($item_info[$itembox_data['item_id']]['image'])){
						$item_list['image'][$i]					= $item_info[$itembox_data['item_id']]['image'];
					}

					if(isset($item_info[$itembox_data['item_id']]['description'])){
						$item_list['description'][$i]			= $item_info[$itembox_data['item_id']]['description'];
					}

					$i++;

				# 使えないアイテム
				}else{

					$item_list_nouse['id'][$j]					= $itembox_data['id'];
					$item_list_nouse['unit'][$j]				= $itembox_data['unit'];

					if(isset($item_info[$itembox_data['item_id']]['name'])){
						$item_list_nouse['name'][$j]			= $item_info[$itembox_data['item_id']]['name'];
					}

					if(isset($item_info[$itembox_data['item_id']]['image'])){
						$item_list_nouse['image'][$j]			= $item_info[$itembox_data['item_id']]['image'];
					}

					if(isset($item_info[$itembox_data['item_id']]['description'])){
						$item_list_nouse['description'][$j]	= $item_info[$itembox_data['item_id']]['description'];
					}

					$j++;

				}

				

			}

			$database->freeResult($itembox_rtn);





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
					'chara_id'							=> $character_id,
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
			$data['id']							= 5;
			$_POST['last_mail_id']				= "78";
		}

		# CHARACTER ID
		$character_id				= $data['id'];

		# LAST MAIL ID
		$last_mail_id				= $_POST['last_mail_id'];

		# RESULT
		$result['result']			= NULL;

		# NULL CHECK
		if(!empty($last_mail_id)){

			# MAILS ARRY
			$mails_conditions		= array();
			$mails_conditions		= array(
				'user_id'			=> $members_data['id'],
				'chara_id'			=> $character_id,
				'last_mail_id'		=> $last_mail_id,
				'status'			=> 0,
				'last_flg'			=> 0,
				'order'				=> 'send_date',
				'limit'				=> 1,
				'type'				=> 1
			);

			$mails_rtn				= $mailModel->getMailList($mails_conditions,"*");
			$mails_data				= $database->fetchAssoc($mails_rtn);
			if(!empty($mails_data['id'])){

				# キャラからのメールにはタイトルがある(絵文字コンバート)
				$display_title				= NULL;
				if(!empty($mails_data['title'])){
					$display_title			= $emoji_obj->emj_decode($mails_data['title']);
				}

				# 内容絵文字コンバート
				$display_message			= $emoji_obj->emj_decode($mails_data['message']);

				$result['id']				= $mails_data['id'];
				$result['title']			= $display_title['web'];
				$result['message']			= $display_message['web'];
				$result['send_date']		= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));
				$result['media']			= $mails_data['media'];
				$result['media_flg']		= $mails_data['media_flg'];
				$result['recv_flg']			= $mails_data['recv_flg'];
				$result['last_flg']			= $mails_data['last_flg'];

				$result['result']			= 1;

			}

			$database->freeResult($mails_rtn);

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
		if(!empty($result['result'])){

			$view_directory						= $mainClass->getViewDirectory($directory,$data['page'],$device_file);

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
			$_POST['mails_id']					= "mails-id-35";
			$_POST['confirmation']				= 0;
		}

		# キャラクターID
		$character_id							= $data['id'];

		# mails ID (分解するよ)
		$mails_parameter						= explode("mails-id-", $_POST['mails_id']);
		$mails_id								= $mails_parameter[1];

		# チケット消費確認フラグ
		$confirmation							= $_POST['confirmation'];

		# 送信消費ポイント(チケット)ナンバー // 今回は複数は一旦なし(複数の場合はカンマ区切りで$point_no_idに代入)
		$point_index							= $point_name_array[$data['page']];
		$point_no_id							= $point_no_array[$point_index][2];

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
			$campaign_id							= NULL;

			# キャンペーンチェック
			$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);
			if(!empty($campaign_data['id'])){
				$campaign_id						= $campaign_data['id'];
			}


			# 持ちポイント : 消費ポイント チェック(status 0 or 1)
	        if($members_data['status'] <= 1){

	            # チェック
				$point_data							= $pointsetModel->checkPointConsume($point_no_id,$members_data,$character_data,$campaign_id);

				# ERROR
	    		if(empty($point_data) || empty($point_data[0])){
					$error							= 2;
					$errormessage					= TICKET_NAME."が足りません。";
			    }

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
							$database->updateDb("mails",$mails_update,$mails_update_where,$mails_update_conditions);


							/************************************************
							**
							**	ポイント消費
							**
							************************************************/

							if(!empty($point_result['points'])){

								# pay_flg 判定
								if($members_data['pay_count'] != 0){
									$pay_flg						= 1;
								}elseif($members_data['pay_count'] == 0 || $members_data['status'] == 3) {
									$pay_flg						= 2;
								}elseif($members_data['status'] == 2) {
									$pay_flg						= 3;
								}else{
									$pay_flg						= 0;
								}

								foreach($point_result['points'] as $points_key => $points_array){

									if(!empty($points_array['point'])){

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
								                "point_type"		=> $key,
												"log_date"			=> date("YmdHis"),
												"chara_id"			=> $mails_data['send_id'],
												"op_id"				=> $mails_data['op_id'],
												"owner_id"			=> $mails_data['owner_id'],
								                "pay_flg"			=> $pay_flg

											);

											# 【insert】points
											$database->insertDb("points",$points_insert);

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
								$members_update_where				= "id = :id";
								$members_update_conditions[':id']	= $members_data['id'];

								# 【UPDATE】 / members
								$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

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



						$result['id']					= $mails_data['id'];
						$result['title']				= $display_title['web'];
						$result['message']				= $display_message['web'];
						$result['send_date']			= date("Y年m月d日 H時i分",strtotime($mails_data['send_date']));
						$result['media']				= $mails_data['media'];
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

		# キャンペーン ID 初期化
		$campaign_id							= NULL;

		# キャンペーンチェック
		$campaign_data							= $campaignsetModel->getCampaignsetData($members_data);

		if(!empty($campaign_data['id'])){
			$campaign_id						= $campaign_data['id'];
		}


		# 持ちポイント : 消費ポイント チェック(status 0 or 1)
        if($members_data['status'] <= 1){

            # チェック
			$point_data							= $pointsetModel->checkPointConsume($point_no_id,$members_data,$character_data,$campaign_id);

			# ERROR
    		if(empty($point_data) || empty($point_data[0])){
				$error							= 2;
				$errormessage					= TICKET_NAME."が足りません。";
		    }

        }


		/************************************************
		**
		**	処理開始
		**
		************************************************/

		if(empty($error)){


			# デフォルト 好感度ポイント / ランキングポイント 取得(初期化したものを上書き)
			$favorite_point_no						= $point_no_array[$point_name_array['favorite']][2];
			$ranking_point_no						= $point_no_array[$point_name_array['ranking']][2];
			$giving_point_no_set					= $favorite_point_no.",".$ranking_point_no;

			$pointset_data							= $pointsetModel->getPointset($giving_point_no_set,$members_data,$campaign_id);

			# OK
			if(!empty($pointset_data)){

				foreach($pointset_data as $point_key => $point_value){

					# 付与好感度ポイント あれば上書き
					if($point_value['point_no_id'] == $favorite_point_no && !empty($point_value['point'])){
						$consumption_favorite	= $point_value['point'];
					# 付与ランキングポイント あれば上書き
					}elseif($point_value['point_no_id'] == $favorite_point_no && !empty($point_value['point'])){
						$consumption_ranking	= $point_value['point'];
					}

				}

			}


			# アイテム使用チェック
			// アイテムあれば好感度ポイントやランキングポイントに乗算


			# emojiエンコード
			$send_message						= $emoji_obj->emj_encode($_POST['message']);

			# unicode6.0 絵文字のバリデート
			if (preg_match("/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/",$send_message)) {
				$send_message					= preg_replace('/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '〓', $send_message);
			}


			/************************************************
			**
			**	初メールだったら / age代入
			**
			************************************************/

			if(!empty($first_mail)){

				$mails_age								= $character_data['age'];


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
					'chara_id'						=> $character_data['id'],
					'status'						=> 0
				);

				$mailusers_data						= $mailuserModel->getMailUserData($mailusers_conditions,"id,favorite,favorite_level,virtual_name,virtual_age");

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

			$mails_insert						= array();
			$mails_insert						= array(
				'site_cd'						=> $members_data['site_cd'],
				'send_id'						=> $members_data['id'],
				'recv_id'						=> $character_data['id'],
				'send_date'						=> date("YmdHis"),
				'message'						=> $send_message,
				'recv_flg'						=> 1,
				'pref'							=> $members_data['pref'],
				'city'							=> $members_data['city'],
				'age'							=> $mails_age,
				'op_id'							=> $character_data['op_id'],
				'owner_id'						=> $character_data['owner_id'],
				'naruto'						=> $character_data['naruto']
			);

			# 【INSERT】mails
			$insert_id							= $database->insertDb("mails",$mails_insert);


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
			**	ポイント計算 / INSERT
			**
			************************************************/

			if(empty($error)){


				/************************************************
				**
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
							$favorite_level					= $favorite_level + $consumption_level;

							# 付与好感度ポイント計算
							$favorite_percent				= $consumption_favorite - ($consumption_level * 100);

							# レベルアップフラグ
							$level_up						= 1;

							# レベルアップ時表示タイトル
							$level_up_title					= "好感度レベルアップ！！";

							# レベルアップ時表示メッセージ
							$level_up_message				= "好感度100％達成！！<br />好感度レベルが<br /><span style=\"color: #FF0000;\">【Lv.".$favorite_level."】→【Lv.".$favorite_level."】</span><br />になりました！";

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

						# 【INSERT】mailusers
						$mailusers_id						= $database->insertDb("mailusers",$mailusers_insert);


						/************************************************
						**
						**	キャラクターアップデート
						**	===========================================
						**	ランキングポイント加算
						**	ユーザーカウント加算
						**
						************************************************/

						# UPDATE CHARACTER DATA
						$character_update					= array();
						$character_update					= array(
							'ranking_point'					=> $character_data['ranking_point'] + $consumption_ranking,
							'user_count'					=> $character_data['user_count'] + 1
						);
						$character_update_where				= "id = :id";
						$character_update_conditions[':id']	= $character_data['id'];

						# 【UPDATE】members
						$database->updateDb("members",$character_update,$character_update_where,$character_update_conditions);


						# 親キャラからのメールがあれば全て子キャラにUPDATE







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
							$favorite_level					= $mailusers_data['favorite_level'] + $consumption_level;

							# 付与好感度ポイント
							$favorite_percent				= $favorite_percent - ($consumption_level * 100);

							# レベルアップフラグ
							$level_up						= 1;

							# レベルアップ時表示タイトル
							$level_up_title					= "好感度レベルアップ！！";

							# レベルアップ時表示メッセージ
							$level_up_message				= "好感度100％達成！！<br />好感度レベルが<br /><span style=\"color: #FF0000;\">【Lv.".$mailusers_data['favorite_level']."】→【Lv.".$favorite_level."】</span><br />になりました！";

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
						$database->updateDb("mailusers",$mailusers_update,$mailusers_update_where,$mailusers_update_conditions);


						/************************************************
						**
						**	キャラクターアップデート
						**	===========================================
						**	ランキングポイント加算
						**	ユーザーカウント加算
						**
						************************************************/

						# UPDATE CHARACTER DATA
						$character_update					= array();
						$character_update					= array(
							'ranking_point'					=> $character_data['ranking_point'] + $consumption_ranking
						);
						$character_update_where				= "id = :id";
						$character_update_conditions[':id']	= $character_data['id'];

						# 【UPDATE】members
						$database->updateDb("members",$character_update,$character_update_where,$character_update_conditions);


					}


					/************************************************
					**
					**	共通処理 : pointsにインサート
					**
					************************************************/

					if(!empty($point_result['points'])){

						# pay_flg 判定
						if($members_data['pay_count'] != 0){
							$pay_flg						= 1;
						}elseif($members_data['pay_count'] == 0 || $members_data['status'] == 3) {
							$pay_flg						= 2;
						}elseif($members_data['status'] == 2) {
							$pay_flg						= 3;
						}else{
							$pay_flg						= 0;
						}

						foreach($point_result['points'] as $points_key => $points_array){

							if(!empty($points_array['point'])){

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
						                "point_type"		=> $key,
										"log_date"			=> date("YmdHis"),
										"chara_id"			=> $character_data['id'],
										"op_id"				=> $character_data['op_id'],
										"owner_id"			=> $character_data['owner_id'],
						                "pay_flg"			=> $pay_flg

									);

									# 【insert】points
									$database->insertDb("points",$points_insert);

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
						$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

					}

				# ERROR
				}else{

					$error									= $point_result['error'];
					$errormessage							= $point_result['errormessage'];

				}

			}

			# 念のため ここでポイントエラー吐いたら insertしたmailsを削除する
			if(!empty($error) && $error == 2){

				# mails update
				$mails_update								= array();
				$mails_update								= array(
					'del_flg'								=> 9
				);

				# UPDATE WHERE
				$mails_update_where							= "id = :id";
				$mails_update_conditions[':id']				= $insert_id;

				# 【UPDATE】 / mails
				$database->updateDb("mails",$mails_update,$mails_update_where,$mails_update_conditions);

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