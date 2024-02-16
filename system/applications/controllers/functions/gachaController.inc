<?
/********************************************************
**
**	gachaController.inc
**	-----------------------------------------------------
**	controllers/gachaController.php向け、関数群
**	-----------------------------------------------------
**	2017.09.15 A.cos
*********************************************************/

## ※関数を作る際の注意
## - なるべく単一の役割を担わせること（DB問い合わせとビュー部分の作成、とかは同一関数内ではなるべくやってはならない）
## -

# キャンペーンデータ取得（index用）
# $campaignsetModel:キャンペーンデータモデル
# $members_data:会員データ
# 返値:キャンペーンID（$campaign_id）、キャンペーンポイント（$campaign_point）、キャンペーンデータ（$campaign_data）
function getCampaignDataForIndex(&$campaignsetModel, $members_data){

	# CAMPAIGN
	$campaign_id = 0;
	$campaign_point = NULL;
	$campaign_data = $campaignsetModel->getCampaignsetData($members_data);

	# campaign_type が2だったら(消費ポイント)
	if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2){
		$campaign_point = $campaign_data['id'];
	# campaign_type が4だったら(ガチャキャンペーン)
	}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 4){
		$campaign_id = $campaign_data['id'];
	# 両方
	}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 5){
		$campaign_point = $campaign_data['id'];
		$campaign_id = $campaign_data['id'];
	}
	
	return array($campaign_id, $campaign_point, $campaign_data);
}

# キャンペーンデータ取得（index用）
# $campaignsetModel:キャンペーンデータモデル
# $data:会員データ
# 返値:キャンペーンID（$campaign_id）、キャンペーンポイント（$campaign_point）、キャンペーンデータ（$campaign_data）
function getCampaignData(&$campaignsetModel, $data){

	# CAMPAIGN
	$campaign_id = 0;
	$campaign_point	= NULL;
	$campaign_data = $campaignsetModel->checkCampaign($data['campaign_id']);

	# campaign_type が2だったら(消費ポイント)
	if(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 2){
		$campaign_point  = $campaign_data['id'];
	# campaign_type が4だったら(ガチャキャンペーン)
	}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 4){
		$campaign_id = $campaign_data['id'];
	# 両方
	}elseif(!empty($campaign_data['id']) && $campaign_data['campaign_type'] == 5){
		$campaign_point = $campaign_data['id'];
		$campaign_id = $campaign_data['id'];
	}
	
	return array($campaign_id, $campaign_point, $campaign_data);
}

# ステップアップガチャの設定を取得（キャンペーン）
# $gachaModel:ガチャクラスインスタンス
# $user_id:ユーザID
# $campaign_id:キャンペーンID
# $use_flg:使用用途（limitted1,stepup1...limitted2,3やstepup2,3もありうる）
# 返値:ステップアップガチャのデータと、該当ユーザがひける残り回数を返す、該当ガチャの引ける最大回数
function getStepupGachaSettingData(&$gachaModel, $user_id, $campaign_id, $use_flg){
	//変数初期化
	$post_data = array();
	$stepup_data = NULL;

	//ユーザのガチャ回数を取得
	$post_data["campaign_id"] = $campaign_id;
	$post_data["use_flg"] = $use_flg;
	list($gacha_use_phase,$gacha_use_max) = $gachaModel->checkUserOnStepupGacha($user_id, $post_data);
	//mail("eikoshi@k-arat.co.jp","getStepupGachaSettingData1",$gacha_use_phase."/".$gacha_use_max,"From:info@kyabaheru.net");
	//ステップアップガチャのn回目の回転数をゲット（post_dataのパラメータは↑のを使いまわす）
	if($gacha_use_phase>=0){
		$post_data["phase"] = $gacha_use_phase;
		//mail("eikoshi@k-arat.co.jp","getStepupGachaSettingData2",var_export($gachaModel->getStepupGachaSetting($post_data), true)."\n".($gacha_use_max-$gacha_use_phase),"From:info@kyabaheru.net");
		return array($gachaModel->getStepupGachaSetting($post_data), ($gacha_use_max-$gacha_use_phase), $gacha_use_max);
	}

	return array(NULL, ($gacha_use_max-$gacha_use_phase), $gacha_use_max);
}

# ガチャでチケットをひいた場合の処理
# $get_data:チケットデータ
# $gacha_data:ガチャデータ
# $update_f_point:無料ポイント増減
# $update_s_point:有料ポイント増減
# $point_recv:pointsのレコード更新用データ
# 返値:無料ポイント増減、有料ポイント増減、pointsのレコード更新用データ
function receiveTicketPointOnGacha($get_data, $gacha_data, $update_f_point, $update_s_point, $point_recv){
	# ログイン無料配布
	if($get_data['type'] == 1){
		# f_pointに加算
		$update_f_point					+= $gacha_data['unit'];
		$point_recv[2][]				 = $gacha_data['unit'];

	# プレゼント配布
	}elseif($get_data['type'] == 2){

		# s_pointに加算
		$update_s_point					+= $gacha_data['unit'];
		$point_recv[1][]				 = $gacha_data['unit'];

	# もしそれ以外があったらs_pointに加算
	}else{

		# s_pointに加算
		$update_s_point					+= $gacha_data['unit'];
		$point_recv[1][]				 = $gacha_data['unit'];

	}

	return array($update_f_point, $update_s_point, $point_recv);
}

# ガチャでアイテムをひいた場合の処理
# $database:データベースインスタンス
# $itemboxModel:アイテムボックスインスタンス
# $members_data:会員データ
# $gacha_data:ガチャデータ
# 返値:景品受け取りフラグ（1 or NULL）
function receiveItemOnGacha($database, $itemboxModel, $members_data, $gacha_data){

	# 所持確認
	$itembox_conditions					= array();
	$itembox_conditions					= array(
		'user_id'						=> $members_data['id'],
		'item_id'						=> $gacha_data['target_id'],
		'status'						=> 0
	);
	$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions,"id,unit");

	# 持ってれば加算
	if(!empty($itembox_data['id'])){

		$update_unit					= $itembox_data['unit'] + $gacha_data['unit'];

		$itembox_update['unit']			= $update_unit;
		$itembox_update_where			= "id = :id";
		$itembox_update_conditions[':id']	= $itembox_data['id'];

		# 【UPDATE】 / itembox
		$return							= $database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

		if(!empty($return)){
			$acceptance					= 1;
			return $acceptance;
		}

	# なければ追加
	}else{

		$itembox_insert					= array();
		$itembox_insert					= array(
			'user_id'					=> $members_data['id'],
			'item_id'					=> $gacha_data['target_id'],
			'unit'						=> $gacha_data['unit'],
			'status'					=> 0
		);

		# 【INSERT】 / itembox
		$acceptance						= $database->insertDb("itembox",$itembox_insert);
		return $acceptance;
	}
	return NULL;
}


?>