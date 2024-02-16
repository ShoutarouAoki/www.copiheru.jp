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



	/**************************************************
	**
	**	multiInsertRanking
	**	----------------------------------------------
	**	ランキングカウント
	**
	**************************************************/

	function multiInsertRanking($post_data){

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

		# MULTI QUERY
		$database->multiQueryDb($sql);


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

			# MULTI QUERY
			$database->multiQueryDb($event_sql);

		}

		return TRUE;


	}




}

?>