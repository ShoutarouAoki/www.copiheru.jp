<?php
namespace Models;

use Libs\Database;

class Schedules{

	const TABLE = "schedules";
	private	$output;

	# CONSTRUCT
	function __construct($main=NULL){
		$this->output		= $main;
    }

	# DESTRUCT
	function __destruct(){
		
	}
	
	public function getOperatingList(){
		// 出勤チェック取得
		$schedules_select = "character_id";
		$schedules_where = "site_cd = :site_cd AND presense < :presense_no AND del_flg = :del_flg";
		$schedules_array = [
			':site_cd' => SITE_CD,
			':presense_no' => 2,
			':del_flg' => 0
		];
		$schedules_order = "id";
		$schedules_limit = NULL;
		$schedules_rtn = Database::selectDb(self::TABLE,$schedules_select,$schedules_where,$schedules_array,$schedules_order,$schedules_limit);
		Database::errorDb(self::TABLE, $schedules_rtn->errorCode(),__FILE__,__LINE__);

		return $schedules_rtn;
	}
}

?>