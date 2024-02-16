<?php
/********************************************************************************
**	
**	AutomailModel.php
**	=============================================================================
**
**	■PAGE / 
**	AUTOMAIL MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	AUTOMAIL CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	AUTOMAIL CLASS
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
class AutomailModel{


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
		$this->table		= "automails";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getAutomailList
	**	----------------------------------------------
	**	automails LIST
	**
	**************************************************/

	public function getAutomailList($post_data,$column=NULL){

		if(empty($post_data['auto_type'])){
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
		$array[':auto_type']	 = $post_data['auto_type'];

		# DB / MAIN CLASS
		$database				= NULL;
		$database				= $this->database;
		$output					= NULL;
		$output					= $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND auto_type = :auto_type ";

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
		$error					 = $database->errorDb("getAutomailList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAll($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getAutomailData
	**	----------------------------------------------
	**
	**
	**************************************************/

	public function getAutomailData($post_data,$column=NULL){

		if(empty($post_data['auto_type'])){
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
		$array[':auto_type']	 = $post_data['auto_type'];

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
		$where					.= "AND auto_type = :auto_type ";
		$where					.= "AND status = :status";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getAutomailData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;
	}





}

?>