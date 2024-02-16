<?php
## mailController用関数
/********************************************************
**
**	mailFun.php
**	-----------------------------------------------------
**	controllers/mailController.php用関数群
**	-----------------------------------------------------
**	2017.10.23 A.cos
*********************************************************/

## ※関数を作る際の注意
## - なるべく単一の役割を担わせること（DB問い合わせとビュー部分の作成、とかは同一関数内ではなるべくやってはならない）
## -

/************************************************
**
**	マニーのリセット処理
**	============================================
**	
**
************************************************/
# $schedules_data:勤務データ
# $mailusers_data:ユーザ＆キャラクター間データ
# $warn:マニー投げ銭時の警告を送ったか
function resetManii($schedules_data, $mailusers_data, $warn=0){
	global $database;
	global $mailuserModel;

    $error = 0;
    $errormessage = "";

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
    
    # UPDATE / mailusers
    $mailusers_table					= "mailusers";
    $mailusers_update					= array();
    $mailusers_update					= array(
        'manii' => "0",
        "reset_date" => $schedules_data["reset_date"],
        "manii_resets_warned" => "0"
    );
    $mailusers_update_where				= "id = :id";
    $mailusers_update_conditions[':id']	= $mailusers_data['id'];
    $database->updateDb($mailusers_table,$mailusers_update,$mailusers_update_where,$mailusers_update_conditions);

    # リセットログ、INSERT
    $maniilog_table					= "manii_logs";
    $maniilog_insert					= array();
    $maniilog_insert					= array(
        'site_cd' => $schedules_data["site_cd"],
        'user_id' => $mailusers_data["user_id"],
        'character_id' => $mailusers_data["send_id"],
        'reset_date' => $mailusers_data["reset_date"],
        'manii' => -1*$mailusers_data["manii"],
        'resets_warned' => $warn,
        'reg_date' => date("YmdHis")
    );
    $maniilog_id						= $database->insertDb($maniilog_table, $maniilog_insert);

    # COMMIT : 一括処理
    if(empty($error)){
        $database->commit();
    # ROLLBACK : 巻き戻し
    }else{
        $database->rollBack();
        $error										= 1;
        $errormessage								= "正常に処理できませんでした。";
    }

    $result = array(
        "error" => $error,
        "errormessage" => $errormessage
    );
	return $result;
}

?>