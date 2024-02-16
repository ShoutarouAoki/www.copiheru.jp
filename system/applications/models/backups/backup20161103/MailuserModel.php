<?php
/********************************************************************************
**	
**	MailuserModel.php
**	=============================================================================
**
**	■PAGE / 
**	MAIL USER MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	MAIL USER CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	MAIL USER CLASS
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: KARAT SYSTEM
**	CREATE DATE : 2015/05/31
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
class MailuserModel{


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
		$this->table		= "mailusers";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getMailuserList
	**	----------------------------------------------
	**	mailusersリスト取得
	**	ユーザー受け取り
	**
	**************************************************/

	public function getMailuserList($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column				 = "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database				= NULL;
		$database				= $this->database;
		$output					= NULL;
		$output					= $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND user_id = :user_id ";

		if(!empty($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
			$where				.= "AND send_id = :character_id ";
		}

		# STATUS
		if(isset($post_data['status'])){
			$array[':status']	 = $post_data['status'];
			$where				.= "AND status = :status";
		}else{
			$array[':status']	 = 9;
			$where				.= "AND status <= :status";
		}

		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;

		if(!empty($post_data['order'])){
			$order				 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit				 = $post_data['limit'];
		}

		if(!empty($post_data['group'])){
			$group				 = $post_data['group'];
		}

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error					 = $database->errorDb("getMailuserList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getMailuserData
	**	----------------------------------------------
	**
	**
	**************************************************/

	public function getMailuserData($post_data,$column=NULL){

		if(empty($post_data['user_id']) || empty($post_data['character_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column				 = "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];
		$array[':character_id']	 = $post_data['character_id'];

		# STATUS
		if(!empty($post_data['status'])){
			$array[':status']	 = $post_data['status'];
		}else{
			$array[':status']	 = 0;
		}

		# DB / MAIN CLASS
		$database				= NULL;
		$database				= $this->database;
		$output					= NULL;
		$output					= $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND user_id = :user_id ";
		$where					.= "AND send_id = :character_id ";
		$where					.= "AND status = :status";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getMailuserData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getMailuserDataByNaruto
	**	----------------------------------------------
	**
	**
	**************************************************/

	public function getMailuserDataByNaruto($post_data,$column=NULL){

		if(empty($post_data['user_id']) || empty($post_data['character_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column				 = "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];
		$array[':character_id']	 = $post_data['character_id'];

		# STATUS
		if(!empty($post_data['status'])){
			$array[':status']	 = $post_data['status'];
		}else{
			$array[':status']	 = 0;
		}

		# DB / MAIN CLASS
		$database				= NULL;
		$database				= $this->database;
		$output					= NULL;
		$output					= $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND user_id = :user_id ";
		$where					.= "AND naruto = :character_id ";
		$where					.= "AND status = :status";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getMailuserDataByNaruto",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;


	}


}

?>