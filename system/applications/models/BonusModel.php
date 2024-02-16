<?php
/********************************************************************************
**	
**	BonusModel.php
**	=============================================================================
**
**	■PAGE / 
**	BONUS MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	BONUS CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	BONUS CLASS
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
class BonusModel{


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
		$this->table		= "bonuses";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getBonusList
	**	----------------------------------------------
	**	ログインボーナスリスト
	**
	**************************************************/

	public function getBonusList($post_data,$column=NULL){

		if(empty($post_data['distribution_date'])){
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
		$array[':date']			 = $post_data['distribution_date'];

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND distribution_date = :date ";

		if(isset($post_data['type'])){
			$where				.= "AND type = :type ";
			$array[':type']		 = $post_data['type'];
		}

		if(isset($post_data['status'])){
			$where				.= "AND status = :status ";
			$array[':status']	 = $post_data['status'];
		}else{
			$where				.= "AND status = :status ";
			$array[':status']	 = 0;
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

		if(!empty($post_data['list'])){
			$limit				 = $post_data['set'].",".$post_data['list'];
		}

		if(!empty($post_data['group'])){
			$group				 = $post_data['group'];
		}

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getBonusList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getBonusDataById
	**	----------------------------------------------
	**	ログインボーナス情報取得
	**
	**************************************************/

	public function getBonusDataById($id,$column=NULL){

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
		$error					 = $database->errorDb("getBonusDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>