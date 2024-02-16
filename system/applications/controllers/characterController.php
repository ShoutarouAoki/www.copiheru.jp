<?php
################################ FILE MANAGEMENT ################################
##
##	characterController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	CHARACTER PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	CHARACTER PAGE 各種処理
##
##	page : index	-> 表示可能親キャラキャラ一覧
##	page : profile	-> キャラ詳細
##
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

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");

/** ITEMUSE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemuseModel.php");

/** EMOJI PLUGINS **/
require_once(DOCUMENT_SYSTEM_PLUGINS."/Emoji/lib/mobile_class_8.php");

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** EVENTMAIL MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/EventmailModel.php");

require_once(DOCUMENT_ROOT_MODELS."/SchedulesModel.php");
require_once(DOCUMENT_ROOT_SERVICES."/characterServices.php");

use Libs\Database;
use Models\Eventmail;

##################################### FUNCTIONS ######################################

/** FUNCTIONS FILE **/
require_once(dirname(__FILE__)."/functions/characterFunc.php");

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

if($data['page'] == "list"){
	$data['page']			= "index";
}

# ID
if(!empty($_POST['id'])){
	$data['id']				= $_POST['id'];
}

# RETIREMENT
$retirement					= NULL;

if($data['page'] == "index" && !empty($data['id']) && $data['id'] == "retirement"){
	$retirement				= 1;
}

# 一覧の表示件数
$list						= 10;

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

# MAILUSER MODEL
$mailuserModel				= new MailuserModel($database,$mainClass);

# ATTACH MODEL
$attachModel				= new AttachModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);

# ITEMUSE MODEL
$itemuseModel				= new ItemuseModel($database,$mainClass);

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);

# EVENTMAIL MODEL
$eventmailModel	= new Models\Eventmail($mainClass);

$schedulesModel = new Models\Schedules($mainClass);

$services = new Services\character();

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
	**	開催イベントチェック（$event_mail_data)がtype=3で、該当キャラクターを取得しておく->$event_mail_settings_character_id
	**
	************************************************/
	# 開催イベントチェック(専用メール画面アリ)
	//$event_mail_data						= $eventModel->getEventData($members_data, 3);
	//現在実施中のtype=3のイベントを1件取得
	$event_sql  = "SELECT id FROM events ";
	$event_sql .= "WHERE site_cd = ".$members_data['site_cd']." AND type=3 ";
	$event_sql .= "AND date_s < ".date("YmdHis")." ";
	$event_sql .= "AND date_e >= ".date("YmdHis")." ";
	$event_sql .= "AND status != 9";
	$event_sql .= " ORDER BY id";
	$event_rtn = Database::query($event_sql);
	Database::errorDb("", $event_rtn->errorCode(),__FILE__,__LINE__);

	$eventmail_data = [];

	while($event_data = Database::fetchAssoc($event_rtn)){
		$eventmail_settings_sql  = "SELECT * FROM event_mail_settings";
		$eventmail_settings_sql .= " WHERE site_cd = {$members_data['site_cd']} AND event_id = {$event_data['id']}";
		$eventmail_settings_sql .= " ORDER BY id";
		$eventmail_settings_rtn = Database::query($eventmail_settings_sql);
		
		while($settings = Database::fetchAssoc($eventmail_settings_rtn)){
			$eventmail_data[$settings['character_id']] = $settings;
		}
	}

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
	**	表示可能親キャラ一覧
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){
		// 除外親ID
		$exclusion_id = [];
		// 表示キャラリスト(SRC)
		$temp_list = [];
		// 表示キャラリスト
		$character_list = [];
		
		// 稼働中キャラリスト取得
		$schedules_rtn = $schedulesModel->getOperatingList();
		$schedules_list = Database::fetchAllByColumn($schedules_rtn);

		// セレクトページバナー取得
		$banner_rtn							= $imageModel->getImageList([
			'file_type'						=> 11,
			'category'						=> $banner_image_category,
			'site_cd'						=> SITE_CD,
			'target_id'						=> 0,
			'display_check'					=> 1,
			'status'						=> 0,
			'order'							=> "id DESC",
		],'img_name');
		$banner_list = Database::fetchAllByColumn($banner_rtn);
		Database::freeResult($banner_rtn);

		// やり取りのあったキャラクター一覧を取得
		$has_seen_list = $services->getHasSeenList($members_data['id'],defined("SYSTEM_CHECK"));

		// 未読数のあるキャラクターIDをsend_dateが大きい順に取得
		$no_read_list = $services->getNoReadCharacter($members_data['id']);

		// サムネ画像取得
		$attaches_data = $services->getThumbnail();

		// character_listに未読新しい順にデータを代入
		foreach($no_read_list as $read_value){
			foreach($has_seen_list as $seen_key => $seen_value) {
				if($read_value['send_id'] === $seen_value['send_id']){
					// character_listに代入した後、該当のキャラデータはリストから削除
					$seen_value['no_read'] = $read_value['no_read'];
					$temp_list[] = $seen_value;
					array_splice($has_seen_list,$seen_key,1);
				}
			}
		}
		$temp_list = array_merge($temp_list,$has_seen_list);

		// 親キャラID設定用コールバック
		$temp_list = array_map([$services, 'addParentId'],$temp_list);

		// やり取りのないキャラクターを取得してキャラリストに追加
		$temp_list = array_merge($temp_list,$services->getHasNotSeenList($temp_list));

		// 取得して並べ替えが終わったキャラリストに必要なデータを設定
		foreach($temp_list as $list_index => $list_value){
			//出勤していなければ以下の処理は行わない。
			if(!in_array($list_value['parent_id'], $schedules_list)){
				continue;
			}

			// サムネイル画像を設定
			$list_value['image'] = $attaches_data[$list_value['parent_id']]['attached'] ?? null;

			# 鍵付きキャラだったら
			list($list_value['secret_key'], $list_value['key_name'], $list_value['key_image']) 
				= checkItemLock($itemModel, $itemboxModel, $itemuseModel, $list_value, $members_data);

			// イベントキャラなら
			$tmp_eventdata = [];
			if(array_key_exists($list_value['parent_id'],$eventmail_data)){
				$tmp_eventdata = $eventmail_data[$list_value['parent_id']];

				// 必要好感度レベルを満たしていればイベントメールチェックを行う
				if($list_value["favorite_level"] >= $tmp_eventdata['character_level']){
					$list_value['eventmail']  = $tmp_eventdata['character_id'];
					$list_value['eventid'] = $tmp_eventdata['event_id'];
					$list_value = $eventmailModel->event_adjustment($tmp_eventdata,$members_data,$list_value);
				}
			}
			$character_list[] = $list_value;
		}

		$total_count = count($character_list);

		$next_previous = $htmlClass->makeNextPreviousLink($next_previous_path,$total_count,$list,$set,$hidden_data);

		$start = $set;
		$stop = $set + $list;
		if($stop > $total_count){
			$stop = $total_count;
		}

	/************************************************
	**
	**	PROFILE
	**	============================================
	**	キャラプロフィール
	**
	************************************************/

	# PROFILE
	}elseif($data['page'] == "profile"){

		$error											= NULL;
		$errormessage									= NULL;
		$standby										= NULL;
		$image											= NULL;

		$images_otherinfo = array();// 20180620 add by A.cos

		if(empty($data['id']) || !is_numeric($data['id'])){

			$error										= 1;
			$errormessage								= "不正なアクセスです";

		}

		# 処理
		if(empty($error)){

			# CHARACTER DATA
			$character_data								= $memberModel->getMemberDataById($data['id'],NULL,"id,nickname,status,open_flg,media_flg,naruto");

			if(!empty($character_data['id'])){

				if($character_data['naruto'] == 0){
					$parent_id							= $character_data['id'];
				}else{
					$parent_id							= $character_data['naruto'];
				}

				# プロフィール画像取得
				$attaches_conditions					= [
					'user_id'							=> $parent_id,
					'category'							=> $profile_image_category,
					'use_flg'							=> 1,
					'pay_count'							=> 0,
					'device'							=> $device_number,
					'status'							=> 1,
					'order'								=> 'pay_count,reg_date DESC',
					'limit'								=> 1,
					'group'								=> NULL
				];
				$attaches_data							= $attachModel->getAttachData($attaches_conditions);

				if(!empty($attaches_data['id'])){

					$image								= $attaches_data['attached'];

				}else{

					$standby							= 1;
					$errormessage						= "プロフィール準備中です<br />今しばらくお待ち下さい";

				}

				# 20180620 add by A.cos
				# その他補足情報画像（レベルアップ報酬など）取得
				$attaches_other_conditions					= [
					'user_id'							=> $parent_id,
					'category'							=> $profile_other_image_category,
					'use_flg'							=> 1,
					'pay_count'							=> 0,
					'device'							=> $device_number,
					'status'							=> 1,
					'order'								=> 'pay_count,reg_date DESC',
					'limit'								=> 1,
					'group'								=> NULL
				];
				$attaches_other_rtn							= $attachModel->getAttachList($attaches_other_conditions);

				
				while($attaches_other_data = Database::fetchAssoc($attaches_other_rtn)){
					$images_otherinfo[] = $attaches_other_data;
				}

			}else{

				$error									= 2;
				$errormessage							= "キャラクターのデータが見つかりません";

			}


		}




	/************************************************
	**
	**	OPEN
	**	============================================
	**	鍵付きキャラ開放処理
	**
	************************************************/

	# OPEN
	}elseif($data['page'] == "open"){
		if(empty($data['id'])){
			errorProcessing(1,"不正なアクセスです");
		}

		# キャラ情報確認
		$character_data								= $memberModel->getMemberDataById($data['id'],NULL,"id,nickname,status,open_flg,media_flg,naruto");

		# 親キャラで鍵付きキャラかチェック
		if(!empty($character_data['id']) && $character_data['naruto'] == 0 && $character_data['media_flg'] == 1){

			$character_id = $character_data['id'];

			// 鍵を確認
			$items_data	= $itemModel -> getItemData([
				'character_id' => $character_id
			]);

			// エラー処理
			if(empty($items_data['id'])){
				errorProcessing(3,"アイテム情報に不備があります");
			}

			// 鍵アイテム名
			$key_name = $items_data['name'];

			// 鍵アイテム画像
			$key_image = HTTP_ITEM_IMAGE."/".$items_data['image'];

			// ユーザーがそのアイテム持ってるかチェック
			$itembox_data = $itemboxModel->getItemboxData([
				'user_id' => $members_data['id'],
				'item_id' => $items_data['id'],
				'status' => 0
			]);

			// エラー処理
			if(empty($itembox_data['id'])){
				errorProcessing(4,"アイテムをお持ちではありません");
			}

			// 鍵使って開放してるかチェック
			$itemuse_rows = $itemuseModel->getItemuseCount([
				'item_id' => $items_data['id'],
				'user_id' => $members_data['id'],
				'character_id' => $character_id,
				'status' => 0
			]);

			// エラー処理
			if($itemuse_rows != 0){
				errorProcessing(5,"既にアイテムを使用済みです");
			}

			//	MASTER DATABASE切り替え
			// AUTHORITY / 既にマスターに接続してるかチェック
			$db_auth					 = Database::checkAuthority();

			// DATABASE CHANGE / スレーブだったら
			if(empty($db_auth)){
				# CLOSE DATABASE SLAVE
				Database::closeDb();
				# CONNECT DATABASE MASTER
				Database::connectDb(MASTER_ACCESS_KEY);
			}

			# 【insert】itemuse
			$insert_id					= Database::insertDb("itemuse",[
				'site_cd'				=> $members_data['site_cd'],
				'item_id'				=> $items_data['id'],
				'user_id'				=> $members_data['id'],
				'character_id'			=> $character_id,
				'reg_date'				=> date("YmdHis")	//20180920 add by A.cos
			]);

			if(empty($insert_id)){
				errorProcessing(6,"正常に処理ができませんでした");

			}
			# itembox 使用不可にする
			$itembox_update			= [
				'unit'				=> (($itembox_data['unit']>0)?($itembox_data['unit']-1):0)
			];

			# UPDATE WHERE
			$itembox_update_where					= "id = :id";
			$itembox_update_conditions[':id']		= $itembox_data['id'];

			# 【UPDATE】 / mails
			Database::updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

			$key_check				= 1;
			$message				= "<div style=\"text-align: center;\"><br >".$character_data['nickname']."を開放しました！</div><br />";
		}else{
			errorProcessing(2,"キャラクター情報に不備があります");
		}

		// quit database connect
		Database::closeDb();
		Database::closeStmt();

		// success result
		$result['send_id']								= $character_id;
		$result['message']								= $message;
		$result['key_check']							= $key_check;
		$result['key_name']								= $key_name;
		$result['key_image']							= $key_image;
		$result['error']								= 0;
		$result['errormessage']							= "";

		// debug
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();
			exit();
		}

		// return result process
		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();
	}

}

function errorProcessing($errorCode,$errorMessage){
	// quit database connect
	Database::closeDb();
	Database::closeStmt();

	// error result
	$result['error']								= $errorCode;
	$result['errormessage']							= $errorMessage;

	// debug
	if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){
		# SYSTEM DEBUG
		$mainClass->debug($result);
		$mainClass->outputDebugSystem();
		exit();
	}

	// return result process
	header('Content-Type: application/json; charset=utf-8');
	print(json_encode($result));
	exit();
}
################################## FILE END #####################################
?>