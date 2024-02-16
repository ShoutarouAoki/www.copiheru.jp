<?php
/********************************************************************************
**	
**	DegreeModel.php
**	=============================================================================
**
**	■PAGE / 
**	DEGREE MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	DEGREE CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	DEGREE CLASS
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
class DegreeModel{


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
		$this->table		= "degrees";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getDegreeData
	**	----------------------------------------------
	**	称号取得
	**
	**************************************************/

	public function getDegreeData($post_data,$column=NULL){

		if(empty($post_data['character_id']) || !isset($post_data['level'])){
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
		$array[':character_id']	 = $post_data['character_id'];
		$array[':level']		 = $post_data['level'];
		$array[':status']		 = 0;

		# DB / MAIN CLASS
		$database				= NULL;
		$database				= $this->database;
		$output					= NULL;
		$output					= $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND character_id = :character_id ";
		$where					.= "AND level >= :level ";
		$where					.= "AND status = :status";
		$order					 = "level";
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getDegreeData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



}

?>