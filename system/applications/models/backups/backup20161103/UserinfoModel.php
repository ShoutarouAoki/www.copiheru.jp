<?php
/********************************************************************************
**	
**	UserinfoModel.php
**	=============================================================================
**
**	■PAGE / 
**	USERINFO MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	USERINFO CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	USERINFO CLASS
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
class UserinfoModel{


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
		$this->table		= "userinfos";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getUserinfoDataByUserId
	**	----------------------------------------------
	**	ユーザー掲示板情報取得
	**
	**************************************************/

	public function getUserinfoDataByUserId($user_id,$column=NULL){

		if(empty($user_id)){
			return FALSE;
		}

		if(empty($column)){
			$column					 = "*";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;
		$array[':user_id']			 = $user_id;

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "user_id = :user_id ";
		$where						.= "AND site_cd = :site_cd";

		$order						 = NULL;
		$limit						 = 1;
		$group						 = NULL;

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getUserinfoDataByUserId",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data						= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getUserinfoDataById
	**	----------------------------------------------
	**	ユーザー掲示板情報取得
	**
	**************************************************/

	public function getUserinfoDataById($id,$column=NULL){

		if(empty($id)){
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
		$array[':id']			 = $id;

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "id = :id";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getUserinfoDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>