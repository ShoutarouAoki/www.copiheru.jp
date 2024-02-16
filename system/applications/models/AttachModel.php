<?php
/********************************************************************************
**	
**	AttachModel.php
**	=============================================================================
**
**	■PAGE / 
**	ATTACH MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	ATTACH CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	ATTACH CLASS
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
class AttachModel{


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
		$this->table		= "attaches";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getAttachList
	**	----------------------------------------------
	**
	**
	**************************************************/

	public function getAttachList($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column	= "*";
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

		if(isset($post_data['category'])){
			$where				.= "AND category = :category ";
			$array[':category']	 = $post_data['category'];
		}

		if(isset($post_data['use_flg'])){
			$where				.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = $post_data['use_flg'];
		}

		if(isset($post_data['pay_count'])){
			$where					.= "AND pay_count = :pay_count ";
			$array[':pay_count']	 = $post_data['pay_count'];
		}

		if(!empty($post_data['level'])){
			$where					.= "AND level_s <= :level_s ";
			$array[':level_s']		 = $post_data['level'];
			$where					.= "AND level_e >= :level_e ";
			$array[':level_e']		 = $post_data['level'];
		}elseif(isset($post_data['level']) && $post_data['level'] == 0){
			$where					.= "AND level_s = :level_s ";
			$array[':level_s']		 = $post_data['level'];
			$where					.= "AND level_e = :level_e ";
			$array[':level_e']		 = $post_data['level'];
		}

		if(isset($post_data['device'])){
			$where					.= "AND device = :device ";
			$array[':device']	 	= $post_data['device'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
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

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getAttachList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getAttachData
	**	----------------------------------------------
	**
	**
	**************************************************/

	public function getAttachData($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column	= "*";
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

		if(isset($post_data['category'])){
			$where				.= "AND category = :category ";
			$array[':category']	 = $post_data['category'];
		}

		if(isset($post_data['use_flg'])){
			$where				.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = $post_data['use_flg'];
		}

		if(isset($post_data['pay_count'])){
			$where					.= "AND pay_count = :pay_count ";
			$array[':pay_count']	 = $post_data['pay_count'];
		}

		if(!empty($post_data['level'])){
			$where					.= "AND level_s <= :level_s ";
			$array[':level_s']		 = $post_data['level'];
			$where					.= "AND level_e >= :level_e ";
			$array[':level_e']		 = $post_data['level'];
		}elseif(isset($post_data['level']) && $post_data['level'] == 0){
			$where					.= "AND level_s = :level_s ";
			$array[':level_s']		 = $post_data['level'];
			$where					.= "AND level_e = :level_e ";
			$array[':level_e']		 = $post_data['level'];
		}

		if(isset($post_data['device'])){
			$where					.= "AND device = :device ";
			$array[':device']	 	= $post_data['device'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
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

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getAttachData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	checkAttachByLevel
	**	----------------------------------------------
	**
	**
	**************************************************/

	public function checkAttachByLevel($post_data){

		if(empty($post_data['user_id'])){
			return FALSE;
		}


		$column					 = "id";

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

		if(isset($post_data['category'])){
			$where				.= "AND category = :category ";
			$array[':category']	 = $post_data['category'];
		}

		if(isset($post_data['use_flg'])){
			$where				.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = $post_data['use_flg'];
		}

		if(isset($post_data['pay_count'])){
			$where					.= "AND pay_count = :pay_count ";
			$array[':pay_count']	 = $post_data['pay_count'];
		}

		if(!empty($post_data['level_s'])){
			$where					.= "AND level_s = :level_s ";
			$array[':level_s']		 = $post_data['level_s'];
		}

		if(!empty($post_data['level_e'])){
			$where					.= "AND level_e = :level_e ";
			$array[':level_e']		 = $post_data['level_e'];
		}

		if(isset($post_data['device'])){
			$where					.= "AND device = :device ";
			$array[':device']	 	= $post_data['device'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
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

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("checkAttachByLevel",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows					= $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;


	}



}

?>