<?php
/********************************************************************************
**	
**	RankingModel.php
**	=============================================================================
**
**	■PAGE / 
**	RANKING MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	RANKING CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	ACCESS COUNTER CLASS
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: KARAT SYSTEM
**	CREATE DATE : 2014/10/31
**	CREATER		:
**
**	=============================================================================
**
**	■ REWRITE (改修履歴)
**
**
**
**
**
**
**
**
**
**
**
**
*********************************************************************************/


# CLASS DEFINE
class RankingModel{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	private	$database;
	private	$output;

	# CONSTRUCT
	function __construct($database=NULL,$main=NULL){
		$this->database		= $database;
		$this->output		= $main;
		$this->table		= "rankings";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getRankingList
	**	----------------------------------------------
	**	ランキング
	**
	**************************************************/

	public function getRankingList($post_data,$column=NULL){

		# COLUMN
		if(empty($column)){
			$column					= "*";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['user_id'])){
			$array[':user_id']	 	 = $post_data['user_id'];
			$where					.= "AND user_id = :user_id ";
		}

		if(isset($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
			$where					.= "AND character_id = :character_id ";
		}

		if(isset($post_data['rank_check'])){
			$array[':rank_check']	 = $post_data['rank_check'];
			$where					.= "AND point > :rank_check ";
		}

		if(isset($post_data['event_id'])){
			$array[':event_id']	 	 = $post_data['event_id'];
			$where					.= "AND event_id = :event_id ";
		}

		if(isset($post_data['status'])){
			$array[':status']	 	 = $post_data['status'];
			$where					.= "AND status = :status ";
		}else{
			$array[':status']	 	 = 0;
			$where					.= "AND status = :status ";
		}

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		if(!empty($post_data['order'])){
			$order					 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit					 = $post_data['limit'];
		}

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error						 = $database->errorDb("RANKING LIST",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getRankingList
	**	----------------------------------------------
	**	ランキング
	**
	**************************************************/

	public function getRankingData($post_data,$column=NULL){

		# COLUMN
		if(empty($column)){
			$column					= "*";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['user_id'])){
			$array[':user_id']	 	 = $post_data['user_id'];
			$where					.= "AND user_id = :user_id ";
		}

		if(isset($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
			$where					.= "AND character_id = :character_id ";
		}

		if(isset($post_data['rank_check'])){
			$array[':rank_check']	 = $post_data['rank_check'];
			$where					.= "AND point > :rank_check ";
		}

		if(isset($post_data['event_id'])){
			$array[':event_id']	 	 = $post_data['event_id'];
			$where					.= "AND event_id = :event_id ";
		}

		if(isset($post_data['status'])){
			$array[':status']	 	 = $post_data['status'];
			$where					.= "AND status = :status ";
		}else{
			$array[':status']	 	 = 0;
			$where					.= "AND status = :status ";
		}

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		if(!empty($post_data['order'])){
			$order					 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit					 = $post_data['limit'];
		}

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error						 = $database->errorDb("RANKING DATA",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data						 = $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getRankingList
	**	----------------------------------------------
	**	ランキング
	**
	**************************************************/

	public function getRankingListJoinOnMembers($post_data,$column=NULL){

		# COLUMN
		if(empty($column)){
			$column					 = "SUM(r.point) as total_point, r.character_id, ";
			$column					.= "m.id as members_id, m.nickname, m.naruto";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		# SELECT
		$sql						 = "SELECT ".$column." FROM ".$this->table." r ";

		# JOIN
		$sql						.= "INNER JOIN members m ";

		# ON
		$sql						.= "ON r.character_id = m.id ";

		$sql						.= "WHERE r.site_cd = :site_cd ";

		if(isset($post_data['user_id'])){
			$array[':user_id']	 	 = $post_data['user_id'];
			$sql					.= "AND r.user_id = :user_id ";
		}

		if(isset($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
			$sql					.= "AND r.character_id = :character_id ";
		}

		if(isset($post_data['rank_check'])){
			$array[':rank_check']	 = $post_data['rank_check'];
			$sql					.= "AND r.point > :rank_check ";
		}

		if(isset($post_data['event_id'])){
			$array[':event_id']	 	 = $post_data['event_id'];
			$sql					.= "AND r.event_id = :event_id ";
		}

		if(isset($post_data['status'])){
			$array[':status']	 	 = $post_data['status'];
			$sql					.= "AND r.status = :status ";
		}else{
			$array[':status']	 	 = 0;
			$sql					.= "AND r.status = :status ";
		}

		if(isset($post_data['open_flg'])){
			if(!empty($post_data['open_in'])){
				$sql				.= "AND m.open_flg IN (".$post_data['open_flg'].") ";
			}else{
				$array[':open_flg']	  = $post_data['open_flg'];
				$sql				.= "AND m.open_flg = :open_flg ";
			}
		}

		# GROUP
		if(!empty($post_data['group'])){
			$sql					.= " GROUP BY ".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql					.= " ORDER BY ".$post_data['order'];
		}

		# LIMIT
		if(!empty($post_data['limit'])){
			$sql					.= " LIMIT ".$post_data['limit'];
		}


		$rtn						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = $database->removeTags($array);

		try{
			$rtn					 = $database->prepare($sql,$array,$debug=1);
	 		$result					 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}

		return $rtn;


	}



	/**************************************************
	**
	**	getRankingDataJoinOnMembers
	**	----------------------------------------------
	**	ランキング
	**
	**************************************************/

	public function getRankingDataJoinOnMembers($post_data,$column=NULL){

		# COLUMN
		if(empty($column)){
			$column					 = "SUM(r.point) as total_point, r.character_id, ";
			$column					.= "m.id as members_id, m.nickname, m.naruto";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		# SELECT
		$sql						 = "SELECT ".$column." FROM ".$this->table." r ";

		# JOIN
		$sql						.= "INNER JOIN members m ";

		# ON
		$sql						.= "ON r.character_id = m.id ";

		$sql						.= "WHERE r.site_cd = :site_cd ";

		if(isset($post_data['user_id'])){
			$array[':user_id']	 	 = $post_data['user_id'];
			$sql					.= "AND r.user_id = :user_id ";
		}

		if(isset($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
			$sql					.= "AND r.character_id = :character_id ";
		}

		if(isset($post_data['rank_check'])){
			$array[':rank_check']	 = $post_data['rank_check'];
			$sql					.= "AND r.point > :rank_check ";
		}

		if(isset($post_data['event_id'])){
			$array[':event_id']	 	 = $post_data['event_id'];
			$sql					.= "AND r.event_id = :event_id ";
		}

		if(isset($post_data['status'])){
			$array[':status']	 	 = $post_data['status'];
			$sql					.= "AND r.status = :status ";
		}else{
			$array[':status']	 	 = 0;
			$sql					.= "AND r.status = :status ";
		}

		if(isset($post_data['open_flg'])){
			if(!empty($post_data['open_in'])){
				$sql				.= "AND m.open_flg IN (".$post_data['open_flg'].") ";
			}else{
				$array[':open_flg']	  = $post_data['open_flg'];
				$sql				.= "AND m.open_flg = :open_flg ";
			}
		}

		# GROUP
		if(!empty($post_data['group'])){
			$sql					.= " GROUP BY ".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql					.= " ORDER BY ".$post_data['order'];
		}

		# LIMIT
		if(!empty($post_data['limit'])){
			$sql					.= " LIMIT ".$post_data['limit'];
		}


		$rtn						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = $database->removeTags($array);

		try{
			$rtn					 = $database->prepare($sql,$array,$debug=1);
	 		$result					 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}

		$data						 = $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	insertRanking
	**	----------------------------------------------
	**	ランキングカウント
	**
	**************************************************/

	function insertRanking($post_data){

		# ERROR ID
		if(empty($post_data['user_id']) || empty($post_data['character_id'])){
			return FALSE;
		}

		# ERROR POINT
		if(empty($post_data['point']) || $post_data['point'] == 0){
			return FALSE;
		}

		# DB / MAIN CLASS
		$database			 = NULL;
		$database			 = $this->database;
		$output	 			 = NULL;
		$output	 			 = $this->output;

		# AUTHORITY
		$db_auth			 = $database->checkAuthority();
		$db_check			 = NULL;

		# DATABASE CHANGE
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check		 = 1;

		}


		/**************************************************
		**
		**	デフォルトランキングポイント加算
		**	----------------------------------------------
		**	event_id = 0
		**
		**************************************************/

		# INSERT
		$sql				 = "INSERT INTO ".$this->table." (site_cd,user_id,character_id,point,event_id,status) ";
		$sql				.= "VALUES (".SITE_CD.",".$post_data['user_id'].",".$post_data['character_id'].",".$post_data['point'].",0,0) ";

		# UPDATE
		$sql				.= "ON DUPLICATE KEY UPDATE point = point + ".$post_data['point'];

		$result				 = $database->query($sql);
		$error				 = $database->errorDb("insertRanking 【DEFAULT】",$result->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }


		/**************************************************
		**
		**	イベント時ランキングポイント加算
		**	----------------------------------------------
		**	event_id = 0
		**
		**************************************************/

		if(!empty($post_data['event_id'])){

			# INSERT
			$event_sql		 = "INSERT INTO ".$this->table." (site_cd,user_id,character_id,point,event_id,status) ";
			$event_sql		.= "VALUES (".SITE_CD.",".$post_data['user_id'].",".$post_data['character_id'].",".$post_data['point'].",".$post_data['event_id'].",0) ";

			# UPDATE
			$event_sql		.= "ON DUPLICATE KEY UPDATE point = point + ".$post_data['point'];

			$event_result	 = $database->query($event_sql);
			$event_error	 = $database->errorDb("insertRanking 【EVENT】",$result->errorCode(),__FILE__,__LINE__);
			if(!empty($event_error)){ $output->outputError($event_error); }

		}


		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}

		return $result;


	}




}

?>