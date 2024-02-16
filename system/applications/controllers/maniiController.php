<?php
################################ FILE MANAGEMENT ################################
##
##	maniiController.php
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
##	page : index	-> ユーザーに貢いマニーの累積値を返す。
##	page : pay		-> マニーする
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



################################# POST ARRAY ####################################

$value_array				= array('page','chara_id');
$data						= $mainClass->getArrayContents($value_array,$values);

############################## INDIVIDUAL SETTING ###############################


/************************************************
**
**	THIS PAGE INDIVIDUAL SETTING
**	---------------------------------------------
**	DATABASE / PATH / CATEGORY ...etc
**
************************************************/


/************************************************
**
**	# 返すデータを初期化
**
************************************************/
$result = array();

# マニーアイテム名
$manii_name = "";
# マニーアイテム画像
$manii_image = "";
# アイテムボックスデータ（マニー）
$itembox_data = NULL;

# キャラクターのID
$character_id = 0;
# キャラクターの親ID
$parent_id = 0;

# キャラクターの勤務状況
$schedules_data = NULL;

# 今まで貢いだ勤務期間中の累積マニー値
$paid_manii = 0;
# ユーザの所有しているマニー枚数
$having_manii = 0;
# 貢いだマニーの閾値
$threshould = 0;

# メッセージ（エラー以外）
$message = "";

# リセット
$reset = 0;

# ERROR
$error = 0;
$error_message = "";


# PAGE
if(empty($data['page'])){
	$data['page']			= "index";
}

# キャラID
if(!empty($_POST['chara_id'])){
	$data['chara_id']				= $_POST['chara_id'];
}

if(empty($data['chara_id']) || !is_numeric($data['chara_id'])){
	$error					= 1;
}

# ユーザID
if(!empty($members_data['id'])){
	$data['user_id']				= $members_data['id'];
}

if(empty($data['user_id']) || !is_numeric($data['user_id'])){
	$error					= 2;
}


# ERROR MESSAGE
$manii_error_array			= array();
$manii_error_array			= array(
	array('0',	'DEFAULT', ''),
	array('1',	'エラー', '入室しなおしてください（キャラクターIDエラー）'),
	array('2',	'エラー', 'マニーを貢ぐのはキャラクターとお話ししてから可能となります（ユーザIDエラー）'),
	array('3',	'エラー', 'マニーが足りません'),
	array('4',	'エラー', '勤務データエラー'),
	array('5',	'エラー', 'マニーリセット中にエラーが起きました'),
	array('6',	'エラー', 'システムエラー。運営にお問い合わせください。'),
);


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

##################################### FUNCTIONS ######################################

/** FUNCTIONS FILE **/
require_once(dirname(__FILE__)."/functions/maniiFunc.php");

################################## MAIN SQL #####################################


/************************************************
**
**	ユーザのマニー枚数取得
**
************************************************/
# まずマニーを確認
$item_table	= "items";
$item_select	= "*";
$item_where	= "site_cd = :site_cd AND effect = :effect AND status = :status";
$item_array = array();
$item_array[':site_cd'] = $members_data["site_cd"];
$item_array[':effect'] = 3;
$item_array[':status'] = 0;
$item_rtn		= $database->selectDb($item_table,$item_select,$item_where,$item_array,$item_order=NULL,1);
$database->errorDb($item_table, $item_rtn->errorCode(),__FILE__,__LINE__);
$manii_data = $database->fetchAssoc($item_rtn);

# マニーアイテムあり
if(!empty($manii_data['id'])){

	# マニーアイテム名
	$manii_name							= $manii_data['name'];

	# マニーアイテム画像
	$manii_image							= HTTP_ITEM_IMAGE."/".$manii_data['image'];

	# ユーザーがそのアイテム持ってるかチェック
	$itembox_conditions					= array();
	$itembox_conditions					= array(
		'user_id'						=> $members_data['id'],
		'item_id'						=> $manii_data['id'],
		'status'						=> 0
	);

	$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions);

	$having_manii = intval($itembox_data["unit"]);//マニー数
}else{
	$error = 6;//エラー
	$error_message = $manii_error_array[$error][2];//エラーメッセージ
}



/************************************************
**
**	# キャラデータ取得
**
************************************************/
$character_data		= $memberModel->getMemberDataById($data['chara_id'],NULL,"*");
if(empty($character_data['id'])){
//キャラクターがいない場合はエラーを返す
	$error = 1;//エラー
	$error_message = $manii_error_array[$error][2];//エラーメッセージ
}else{
	$character_id = $character_data['id'];
	if(empty($character_data['naruto'])){
		$parent_id = $character_data['id'];
	}else{
		$parent_id = $character_data['naruto'];
	}
	//マニーの閾値
	$threshould = $character_data['manii_threshould'];
}


/************************************************
**
**	キャラとユーザ間の関係データ取得
**
************************************************/
if(!$error){
	$mailusers_conditions	= array();
	$mailusers_conditions	= array(
		'user_id'		=> $members_data['id'],
		'character_id'	=> $character_id,
		'status'		=> 0
	);
	$mailusers_data = $mailuserModel->getMailuserData(
								$mailusers_conditions,
								"id,user_id,send_id,favorite,favorite_level,manii,reset_date,manii_resets_warned,virtual_age,virtual_name,degree_name");
	if(empty($mailusers_data['id'])){
	//ユーザがいない場合はエラーを返す
		$error = 2;//エラー
		$error_message = $manii_error_array[$error][2];//エラーメッセージ
	}
	
	// 支払ったマニー
	$paid_manii = intval($mailusers_data["manii"]);
}

/************************************************
**
**	キャラの現在の勤務状況を取得
**
************************************************/
if(!$error){
	# 現在のキャラクターのスケジュールデータ（キャラクターID、消費ポイントID）
	$schedules_table	= "schedules";
	$schedules_select	= "id,site_cd, character_id, reset_date";
	$schedules_where	= "site_cd = :site_cd AND character_id = :character_id AND del_flg = :del_flg AND presense<2";
	$schedules_array = array();
	$schedules_array[':site_cd'] = $members_data["site_cd"];
	$schedules_array[':character_id'] = $parent_id;
	$schedules_array[':del_flg'] = 0;
	$schedules_rtn		= $database->selectDb($schedules_table,$schedules_select,$schedules_where,$schedules_array,$schedules_order=NULL,1);
	$database->errorDb($schedules_table, $schedules_rtn->errorCode(),__FILE__,__LINE__);
	$schedules_data = $database->fetchAssoc($schedules_rtn);

	if(empty($schedules_data['id'])){
	//勤務データいない場合はエラーを返す
		$error = 4;//エラー
		$error_message = $manii_error_array[$error][2];//エラーメッセージ
	}
}

/************************************************
**
**	リセットのチェック
**
************************************************/
if(!$error){
	if($schedules_data["reset_date"]>$mailusers_data["reset_date"]){
		//リセットメッセージを返す。以下の処理は行わない。
		$reset_result = resetManii($schedules_data, $mailusers_data);

		if($reset_result["error"]>0){
		//エラーが起きた
			$error = 5;//エラー
			$error_message = $manii_error_array[$error][2];//エラーメッセージ
			
		}else{
		//リセットの通知
			$message = $character_data["nickname"]."へのマニーがリセットされました。";
			$reset = 1;//リセット

			$result['message'] = $message;
			$result['reset'] = $reset;//リセット
			$result['paid_manii'] = 0;//今まで貢いだ勤務期間中の累積マニー値
			$result['having_manii'] = $having_manii*1000;//ユーザの所有しているマニー枚数
			$result['threshould'] = $threshould;//貢いだマニーの閾値
			$result['error'] = $error;
			$result['error_message'] = $error_message;
//mail("eikoshi@k-arat.co.jp","MANII[reset]",var_export($result, true),"From:info@kyabaheru.net");
			header('Content-Type: application/json; charset=utf-8');
			print(json_encode($result));
			exit();
		}
	}
}
//mail("eikoshi@k-arat.co.jp","MANII[e]",$error,"From:info@kyabaheru.net");
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

//if(empty($exection) && empty($error)){

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
	**	貢いだマニーの量を返す
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){
		# エラーがあってもなくても、JSONで送る
		$result['message'] = $message;
		$result['reset'] = $reset;//リセット
		$result['paid_manii'] = $paid_manii;//今まで貢いだ勤務期間中の累積マニー値
		$result['having_manii'] = $having_manii*1000;//ユーザの所有しているマニー枚数
		$result['threshould'] = $threshould;//貢いだマニーの閾値
		$result['error'] = $error;
		$result['error_message'] = $error_message;
//mail("eikoshi@k-arat.co.jp","MANII[result]",var_export($result, true),"From:info@kyabaheru.net");
		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
//			$mainClass->debug($result);
//			$mainClass->outputDebugSystem();
			exit();

		}

		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();

	/************************************************
	**
	**	PAY
	**	============================================
	**	マニー処理
	**	============================================
	**	ajaxにて通信
	**	jsonで結果を返す
	**
	************************************************/

	# SEND
	}elseif($data['page'] == "pay"){
		//$error = 0;
		//$errormessage = "";
		$warn = 0;

		# エラーがある場合はエラー用のJSONを送る
		if($error){
			$result['message'] = $message;
			$result['reset'] = $reset;//リセット
			$result['paid_manii'] = $paid_manii;//今まで貢いだ勤務期間中の累積マニー値
			$result['having_manii'] = $having_manii*1000;//ユーザの所有しているマニー枚数
			$result['threshould'] = $threshould;//貢いだマニーの閾値
			$result['error'] = $error;
			$result['error_message'] = $error_message;
//mail("eikoshi@k-arat.co.jp","MANII[error]",var_export($result, true),"From:info@kyabaheru.net");
			header('Content-Type: application/json; charset=utf-8');
			print(json_encode($result));
			exit();
		}

		# マニー持ってる？
		if(empty($itembox_data['id']) || !$itembox_data['unit']){
			//所持していないのでエラーを返して処理は終了
			$error = 3;//エラー
			$error_message = $manii_error_array[$error][2];//エラーメッセージ
			
			$result['message'] = $message;
			$result['reset'] = $reset;//リセット
			$result['paid_manii'] = $paid_manii;//今まで貢いだ勤務期間中の累積マニー値
			$result['having_manii'] = $having_manii*1000;//ユーザの所有しているマニー枚数
			$result['threshould'] = $threshould;//貢いだマニーの閾値
			$result['error'] = $error;
			$result['error_message'] = $error_message;

			header('Content-Type: application/json; charset=utf-8');
			print(json_encode($result));
			exit();
		}
		
		/************************************************
		**
		**	MASTER DATABASE切り替え
		**
		************************************************/
		$db_auth								 = $database->checkAuthority();
		if(empty($db_auth)){// DATABASE CHANGE / スレーブだったら
			# CLOSE DATABASE SLAVE
			$database->closeDb();
			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);
		}
		
		# トランザクションスタート
		$database->beginTransaction();
		
		/************************************************
		**
		**	ユーザ＆キャラ間のマニー数加算
		**
		************************************************/
		$paid_manii += MANII_PAY_POINT;//支払数加算
		$having_manii -= MANII_CONSUMPTION;//持ち数減算

		# UPDATE / mailusers
		$mailusers_table					= "mailusers";
		$mailusers_update					= array();
		$mailusers_update					= array(
			'manii' => $paid_manii,
			"manii_resets_warned" => "0"
		);
		$mailusers_update_where				= "id = :id";
		$mailusers_update_conditions[':id']	= $mailusers_data['id'];
		$database->updateDb($mailusers_table,$mailusers_update,$mailusers_update_where,$mailusers_update_conditions);
	
		/************************************************
		**
		**	ユーザ所有マニー数減算
		**
		************************************************/
		# UPDATE / itembox
		$itembox_table					= "itembox";
		$itembox_update					= array();
		$itembox_update					= array(
			'unit' => $having_manii
		);
		$itembox_update_where				= "id = :id";
		$itembox_update_conditions[':id']	= $itembox_data['id'];
		$database->updateDb($itembox_table,$itembox_update,$itembox_update_where,$itembox_update_conditions);

		/************************************************
		**
		**	マニーログ追加
		**
		************************************************/
		# リセットログ、INSERT
		$maniilog_table					= "manii_logs";
		$maniilog_insert					= array();
		$maniilog_insert					= array(
			'site_cd' => $schedules_data["site_cd"],
			'user_id' => $mailusers_data["user_id"],
			'character_id' => $mailusers_data["send_id"],
			'reset_date' => $mailusers_data["reset_date"],
			'manii' => MANII_PAY_POINT,
			'resets_warned' => $warn,
			'reg_date' => date("YmdHis")
		);
		$maniilog_id						= $database->insertDb($maniilog_table, $maniilog_insert);

		/************************************************
		**
		**	マニーメッセージ作成
		**
		************************************************/
		$message = $character_data["nickname"]."に".MANII_PAY_POINT."マニーを送りました。";

		# COMMIT : 一括処理
		if(empty($error)){
			$database->commit();

			//JSONで送り返す値の設置
			$result['message'] = $message;
			$result['reset'] = $reset;//リセット
			$result['paid_manii'] = $paid_manii;//今まで貢いだ勤務期間中の累積マニー値
			$result['having_manii'] = $having_manii*1000;//ユーザの所有しているマニー枚数
			$result['threshould'] = $threshould;//貢いだマニーの閾値
			$result['itembox_data_id'] = $itembox_data['id'];//アイテムボックスID
			$result['error'] = $error;
			$result['error_message'] = $error_message;
			
		# ROLLBACK : 巻き戻し
		}else{
			$database->rollBack();
			$error										= 5;
			$errormessage								= "正常に処理できませんでした。";

			//JSONで送り返す値の設置
			$result['message'] = $message;
			$result['reset'] = $reset;//リセット
			$result['paid_manii'] = $mailusers_data["manii"];//今まで貢いだ勤務期間中の累積マニー値
			$result['having_manii'] = $itembox_data["unit"]*1000;//ユーザの所有しているマニー枚数
			$result['threshould'] = $threshould;//貢いだマニーの閾値
			$result['itembox_data_id'] = $itembox_data['id'];//アイテムボックスID
			$result['error'] = $error;
			$result['error_message'] = $error_message;
		}
		//mail("eikoshi@k-arat.co.jp","MANII[result]",var_export($result, true),"From:info@kyabaheru.net");
		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();

		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
//			$mainClass->debug($result);
//			$mainClass->outputDebugSystem();
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
	}

}

################################## VERSION ######################################

//$mainClass->debug("Version : 1.02");

################################## FILE END #####################################
?>