<?php
/********************************************************************************
**	
**	AlbumModel.php
**	=============================================================================
**
**	■PAGE / 
**	ALBUM BOX MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	ALBUM BOX CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	ALBUM BOX CLASS
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
class AlbumModel{


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
		$this->table		= "albums";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getAlbumList
	**	----------------------------------------------
	**	アルバムリスト
	**
	**************************************************/

	public function getAlbumList($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
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
		$array[':user_id']			 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "user_id = :user_id ";

		if(isset($post_data['acceptance_date'])){
			$where					.= "AND acceptance_date = :date ";
			$array[':date']			 = $post_data['acceptance_date'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status < :status ";
			$array[':status']		 = 9;
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

		if(!empty($post_data['list'])){
			$limit					 = $post_data['set'].",".$post_data['list'];
		}

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getAlbumList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getAlbumCount
	**	----------------------------------------------
	**	アルバムカウント
	**
	**************************************************/

	public function getAlbumCount($post_data){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		$column						 = "id";

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':user_id']			 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "user_id = :user_id ";

		if(isset($post_data['acceptance_date'])){
			$where					.= "AND acceptance_date = :date ";
			$array[':date']			 = $post_data['acceptance_date'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status < :status ";
			$array[':status']		 = 9;
		}

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getAlbumCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows						 = $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;

	}



	/**************************************************
	**
	**	getAlbumDataById
	**	----------------------------------------------
	**	ログインボーナス情報取得
	**
	**************************************************/

	public function getAlbumDataById($id,$column=NULL){

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
		$array[':status']		 = 0;

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "id = :id ";
		$where					.= "AND status = :status";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getAlbumDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>