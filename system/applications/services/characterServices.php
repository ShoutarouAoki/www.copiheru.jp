<?php
namespace Services;

use Libs\Database;

// キャラクター一覧取得用関数群
class character{

    // 親キャラ子キャラで処理を分ける場合のif
    public function addParentId($character_data){
		// 親だったら
        if ($character_data['naruto'] == 0) {
            $character_data['parent_id'] = $character_data['send_id'];
        }
		// 子だったら
        else {
            $character_data['parent_id'] = $character_data['naruto'];
        }
        return $character_data;
    }

    // mailusersとmembersのjoinにより、ユーザーIDからやりとりのあるキャラを抽出
    // 開発環境であれば非公開キャラや終了しているイベントキャラも表示される
    public function getHasSeenList($user_id,$is_debug){
		$column ="mu.send_id, m.nickname, mu.naruto, mu.upd_date, mu.favorite, mu.favorite_level, m.op_id, m.owner_id, m.media_flg";
        $table = "mailusers mu inner join members m on m.id = mu.send_id";
        $array = [
            ':user_id' => $user_id
        ];
        if ( $is_debug ) {
            $where = "mu.user_id = :user_id and m.open_flg  in (1,2)";
        } else {
            $where = "mu.user_id = :user_id and m.open_flg = :open_flg";
            $array['open_flg'] = 1;
        }

		$order = "mu.upd_date desc";

        return Database::fetchAll(
            Database::selectDb($table,$column,$where,$array,$order)
        );
    }

    // 親キャラキャラを抽出
    // やりとりのあるキャラリストに存在する親キャラIDは返さない
    public function getHasNotSeenList($character_list){
		$column ="id as send_id, nickname, naruto, op_id, owner_id, media_flg, id as parent_id, 0 as favorite_level";
        $table = "members";
        $array = [
            ':site_cd' => SITE_CD,
            ':open_flg' => 1,
            ':op_id' => 0,
            ':naruto' => 0,
        ];
        $where = "site_cd = :site_cd AND op_id > :op_id AND naruto = :naruto AND open_flg = :open_flg AND status != 9";  
		$order = "id desc";

        $parent_list = Database::fetchAll(Database::selectDb($table,$column,$where,$array,$order));

        foreach($character_list as $value){
            foreach($parent_list as $parent_key => $parent_value){
                if($value['parent_id'] === $parent_value['parent_id']){
                    array_splice($parent_list,$parent_key,1);
                }
            }
        }
        return $parent_list;
    }

    // ユーザーが持っている未読メールをsend_date順に並べ替えて件数を取得する
	public function getNoReadCharacter($user_id){
        $column = "send_id,count(*) as no_read";
        $table = "mails";
        $where = 
        "recv_id = :recv_id AND recv_flg = :recv_flg AND site_cd = :site_cd AND del_flg = :del_flg";
		$array = [
            ':recv_id' => $user_id,
            ':recv_flg' => 1,
            ':site_cd' => SITE_CD,
            ':del_flg' => 0
        ];
        $order = "send_date desc";
        $group = "send_id";

        return Database::fetchAll(
            Database::selectDb($table,$column,$where,$array,$order,null,$group)
        );
    }
    
    // サムネイル画像一覧取得用関数
    public function getThumbnail(){
        $column = "user_id,attached";
        $table = "attaches";
        $where = "site_cd = :site_cd AND category = :category AND use_flg = :use_flg AND status = :status";
        // TODO: fix hard code [category=14] 
		$array = [
			':site_cd' => 1,
			':category' => 14,
			':use_flg' => 1,
			':status' => 1
        ];

        return Database::fetchAllByUnique(
            Database::selectDb($table,$column,$where,$array)
        );
    }
}

?>
