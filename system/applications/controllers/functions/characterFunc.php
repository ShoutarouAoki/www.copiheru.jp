<?php
## characterController用関数
/********************************************************
**
**	characterFun.php
**	-----------------------------------------------------
**	controllers/characterController.php用関数群
**	-----------------------------------------------------
**	2017.10.19 A.cos
*********************************************************/

use Libs\Database;

## ※関数を作る際の注意
## - なるべく単一の役割を担わせること（DB問い合わせとビュー部分の作成、とかは同一関数内ではなるべくやってはならない）
## -
# 鍵付キャラの鍵解放
# $main:mainオブジェクト
# $search：問い合わせ格納連想配列
function checkItemLock($itemModel, $itemboxModel, $itemuseModel, $character_data, $member_data){
	// 鍵無しキャラだったら
	if($character_data['media_flg'] != 1){
		return;
	} 
	$secret_key	= 1;

	// まず鍵を確認
	$items_data = $itemModel->getItemData([
		'character_id' => $character_data['parent_id']
	]);

	// 鍵アイテムなし
	if(!isset($items_data['id'])){
		return [$secret_key, null, null];
	}
	$key_name = $items_data['name'];
	$key_image = HTTP_ITEM_IMAGE."/".$items_data['image'];

	// ユーザーがそのアイテム持ってるかチェック
	$itembox_data = $itemboxModel->getItemboxData([
		'user_id' => $member_data['id'],
		'item_id' => $items_data['id'],
		'status' => 0
	]);

	// 持っていない
	if(!isset($itembox_data['id'])) return [$secret_key, $key_name, $key_image];
	$secret_key = 2;

	// 鍵使って開放してるかチェック
	$itemuse_rows = $itemuseModel->getItemuseCount([
		'item_id' => $items_data['id'],
		'user_id' => $member_data['id'],
		'character_id' => $character_data['parent_id'],
		'status' => 0
	]);

	// 開放済み
	if($itemuse_rows > 0){
		$secret_key	= null;
	}

	return [$secret_key, $key_name, $key_image];
}

# レベルアンロック一覧条件取得と、アンロック済みのチェック
# 返し値は、-1が設定がない、1の場合はアンロック済み
# $db:DBオブジェクト
# $main:mainオブジェクト
# $search：問い合わせ格納連想配列
function checkLevelLock(&$main, $characters, $search){

	# 親情報
	$unlock_table	= "conditions_unlock";
	$unlock_column	= "*";
	
	$unlock_array = array();
	$unlock_array[':site_cd'] = $search["site_cd"];
	$unlock_array[':target_id'] = $search["target_id"];
	$unlock_array[':status'] = 0;

	$unlock_where = "site_cd = :site_cd ";
	$unlock_where .= "AND target_userid = :target_id ";
	$unlock_where .= "AND status = :status ";
	
	$unlock_order = "id";
	$unlock_limit = NULL;
	$unlock_group = NULL;

	$unlock_rtn = Database::selectDb($unlock_table, $unlock_column, $unlock_where, 
								$unlock_array, $unlock_order, $unlock_limit, $unlock_group);
	$error = Database::errorDb("checkLevelLock:".$unlock_table,$unlock_rtn->errorCode(),__FILE__,__LINE__);
	if(!empty($error)){ $main->outputError($error); }
	
	$numrows = Database::numRows($unlock_rtn);
	if($numrows == 0){//レベルアンロック設定がない
		return -1;
	}
	
	# レベルアンロック設定があって、既にアンロックしているか？
	$table	= "unlocked";
	$column	= "id";
	
	$array = array();
	$array[':site_cd'] = $search["site_cd"];
	$array[':user_id'] = $search["user_id"];
	$array[':target_userid'] = $search["target_id"];

	$where = "site_cd = :site_cd ";
	$where .= "AND user_id = :user_id ";
	$where .= "AND target_userid = :target_userid ";
	
	$order = NULL;
	$limit = 1;
	$group = NULL;

	$rtn = Database::selectDb($table, $column, $where, $array, $order, $limit, $group);
	$error = Database::errorDb("checkLevelLock:".$table,$rtn->errorCode(),__FILE__,__LINE__);
	if(!empty($error)){ $main->outputError($error); }
	
	$data = Database::fetchAssoc($rtn);
	if(!empty($data["id"])){
		return 1;
	}	

		
	# 子情報
	$unlock_child_table	= "conditions_unlock_child";
	$unlock_child_column	= "*";
	
	$unlocks = array();
	while($unlock_data   = Database::fetchAssoc($unlock_rtn)){
		//子情報
		$unlock_child_array = array();
		$unlock_child_array[':site_cd'] = $search["site_cd"];
		$unlock_child_array[':parent_id'] = $unlock_data["id"];
		$unlock_child_array[':status'] = 0;

		$unlock_child_where = "site_cd = :site_cd ";
		$unlock_child_where .= "AND parent_id = :parent_id ";
		$unlock_child_where .= "AND status = :status ";
		$unlock_child_where .= "AND level_unlock > 0 ";
		
		$unlock_child_order = "id";
		$unlock_child_limit = NULL;
		$unlock_child_group = NULL;
		
		$unlock_child_rtn = Database::selectDb($unlock_child_table, $unlock_child_column, $unlock_child_where, 
									$unlock_child_array, $unlock_child_order, $unlock_child_limit, $unlock_child_group);
		$error = Database::errorDb("checkLevelLock:".$unlock_child_table, $unlock_child_rtn->errorCode(), __FILE__,__LINE__);
		$unlocks_child = array();
		while($unlock_child_data   = Database::fetchAssoc($unlock_child_rtn)){
			$unlocks_child[$unlock_child_data["id"]] = array(
														"unlock_userid"=>$unlock_child_data["unlock_userid"],
														"unlock_username" => $characters[$unlock_child_data["unlock_userid"]], 
														"level_unlock"=>$unlock_child_data["level_unlock"]);
		}
		$unlocks[] = $unlocks_child;
	}
	
	if(empty($unlocks)){
		return array(-1,"");//レベルアンロック設定されていない
	}else{
		//JavaScriptに渡す配列にする
		$str_level_unlock = "";
		if(count($unlocks)>0 && is_array($unlocks)){
			//$str_level_unlock = json_encode($level_unlock, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
			
			$str_level_unlock .= "[";
			$i=0;
			$j=0;
			foreach($unlocks as $val){
				if($i)
					$str_level_unlock .= ",[";
				else
					$str_level_unlock .= "[";
				
				$j=0;
				foreach($val as $key2 => $val2){
					if($j)
						$str_level_unlock .=  ",{'unlock_username':'".$val2["unlock_username"]."',  'level_unlock':".$val2["level_unlock"]." }";
					else
						$str_level_unlock .=  "{'unlock_username':'".$val2["unlock_username"]."',  'level_unlock':".$val2["level_unlock"]." }";
					$j++;
				}
				$str_level_unlock .= "]";
				$i++;
			}
			$str_level_unlock .= "]";
			
		}
		$main->debug($unlocks,"LEVEL UNLOCK OPTION");
		
		return array($unlocks,$str_level_unlock);//レベルアンロック設定を返す。
	}
	

}

# 【3Pシステム乙】(管理画面側のmail/functions/individuals_exe.incからコピペ)
# メール本文（一言もOK）内、文字修飾タグ抽出
# $contents:検索対象の文字列変数(参照渡し)->文字修飾タグをspanタグに変換。末尾を除いて変換した際はspan閉じタグに改行タグbrも加える
# 返値:
# $converted:HTML修飾しなおした本文
function set3pSystem_Otsu($contents){
	global $font_color_array;

	//変換した内容
	$converted = $contents;

	//返す配列とそのカウント
	$ftags_in_content = array();
	$ftags_in_count = 0;

	//正規表現を用いて本文の中から文字修飾タグを抽出し配列にセット
	if(preg_match_all("/\[\[F[BI]*-[A-Z]+-[0-9]+%\]\]/",$contents, $matches, PREG_OFFSET_CAPTURE)){
		foreach($matches[0] as $key=>$val){
			$ftags_in_content[] = $val[0];
			$ftags_in_count++;
		}
	}

	if($ftags_in_count > 0){
		//タグ毎の処理
		$count = 0;
		foreach($ftags_in_content as $val){
			//置換用にタグ文字列を取っておく
			$tmp = $val;

			//タグ情報から接頭詞・接尾詞を消す
			$val = str_replace("[[","",$val);
			$val = str_replace("%]]","",$val);

			//各タグの情報を抜き出す
			$factor = explode("-", $val);
			//print_r($factor);

			//
			$color = "";
			$fontsize = "100";
			$fontweight = "";
			$fontstyle = "";

			##「F」「FB」「FI」「FBI」以外は受け付けない
			## 「B」があれば太字、「I」があれば斜字を指定する（併用可能）
			switch($factor[0]){
				case "F":
					break;
				case "FB":
					$fontweight = "font-weight: bold;";
					break;
				case "FI":
					$fontstyle = "font-style: italic;";
					break;
				case "FBI":
				case "FIB":
					$fontweight = "font-weight: bold;";
					$fontstyle = "font-style: italic;";
					break;
				default:
					break;
			}

			//色指定
			$color = $font_color_array[strtoupper($factor[1])];

			//サイズ指定
			$fontsize = $factor[2];

			## タグ作成
			$tag = "";
			if($count>0){//一つ目は普通にspanタグをつけて、二つ目以降はspanの閉じタグと連結したspanタグをつける
				$tag = "</span><span style=\"color: ".$color."; font-size: ".$fontsize."%; ".$fontweight.$fontstyle."\">";
				//$tag = "</span><br/><span style=\"color: ".$color."; font-size: ".$fontsize."%; ".$fontweight.$fontstyle."\">";
			}else{
				$tag = "<span style=\"color: ".$color."; font-size: ".$fontsize."%; ".$fontweight.$fontstyle."\">";
			}

			//タグとspanタグを置換する
			$converted = str_replace($tmp,$tag,$converted);

			$count++;
		}
		//文末に閉じタグ『</span>』を追加
		$converted .= "</span>";
	}

	//変換した本文内容、エラー内容
	return $converted;
	
}

?>