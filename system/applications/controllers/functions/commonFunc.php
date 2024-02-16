<?php
## mailController用関数
/********************************************************
**
**	commonFunc.php
**	-----------------------------------------------------
**	controllers/共有関数群
**	-----------------------------------------------------
**	2018.10.19 A.cos
*********************************************************/

## ※関数を作る際の注意
## - なるべく単一の役割を担わせること（DB問い合わせとビュー部分の作成、とかは同一関数内ではなるべくやってはならない）
## -

/************************************************
**
**	ポイント購入のチェック
**	============================================
**	
**
************************************************/
# $members_data:ユーザデータ
function checkMemberBuyFlag($members_data){
	global $database;
    global $mainClass;
    
    $error = 0;

    # ここでpaysをチェック
    $pays_where						 = "site_cd = :site_cd ";
    $pays_where						.= "AND user_id = :user_id ";
    $pays_where						.= "AND settlement_id = :settlement_id ";
    $pays_where						.= "AND clear = :clear ";
    $pays_where						.= "AND finish = :finish ";
    $pays_where						.= "AND status = :status ";

    # ポイント購入はsettlement_id = 12
    $pays_array[':site_cd']			 = $members_data['site_cd'];
    $pays_array[':user_id']			 = $members_data['id'];
    $pays_array[':settlement_id']	 = 12;
    $pays_array[':clear']			 = 1;
    $pays_array[':finish']			 = 1;
    $pays_array[':status']			 = 0;
    $pays_order						 = NULL;
    $pays_limit						 = 1;
    $pays_group						 = NULL;

    $pays_column					 = "id";

    $pays_rtn						 = $database->selectDb("pays",$pays_column,$pays_where,$pays_array,$pays_order,$pays_limit,$pays_group);
    $error							 = $database->errorDb("checkMemberBuyFlag",$pays_rtn->errorCode(),__FILE__,__LINE__);
    if(!empty($error)){ $mainClass->outputError($error); }

    $pays_data						 = $database->fetchAssoc($pays_rtn);

    return $pays_data;
}

?>