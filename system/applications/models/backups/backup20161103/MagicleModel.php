<?php
/********************************************************************************
**	
**	MagicleModel.php
**	=============================================================================
**
**	■PAGE / 
**	MAGICLE MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	MAGICLE CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	MAGICLE CLASS
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
class MagicleModel{


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
		$this->table		= "magicles";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getMagicleData
	**	----------------------------------------------
	**	隣接取得
	**
	**************************************************/

	public function getMagicleData($post_data){


		if(empty($post_data['pref']) || empty($post_data['city'])){
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
		$array[':pref']			 = $post_data['pref'];
		$array[':city']			 = $post_data['city'];

		# DB / MAIN CLASS
		$database				= NULL;
		$database				= $this->database;
		$output					= NULL;
		$output					= $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND pref = :pref ";
		$where					.= "AND city = :city";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getMagicleData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;


	}



}

?>