<?php
/********************************************************************************
**	
**	PresentboxModel.php
**	=============================================================================
**
**	■PAGE / 
**	PRESENTBOX BOX MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	PRESENTBOX BOX CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	PRESENTBOX BOX CLASS
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
class PresentboxModel{


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
		$this->table		= "presentbox";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getPresentboxList
	**	----------------------------------------------
	**	プレゼントボックスリスト
	**
	**************************************************/

	public function getPresentboxList($post_data,$column=NULL){

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
		$array[':site_cd']			 = SITE_CD;
		$array[':user_id']			 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";
		$where						.= "AND user_id = :user_id ";

		if(!empty($post_data['present_id'])){
			$where					.= "AND present_id = :present_id ";
			$array[':present_id']	 = $post_data['present_id'];
		}

		if(isset($post_data['acceptance_date'])){
			$where					.= "AND acceptance_date = :date ";
			$array[':date']			 = $post_data['acceptance_date'];
		}

		if(isset($post_data['category'])){
			$where					.= "AND category = :category ";
			$array[':category']		 = $post_data['category'];
		}

		if(isset($post_data['type'])){
			$where					.= "AND type = :type ";
			$array[':type']			 = $post_data['type'];
		}

		if(isset($post_data['limit_date'])){
			$where					.= "AND limit_date = :limit_date ";
			$array[':limit_date']	 = $post_data['limit_date'];
		}

		if(isset($post_data['limit_over'])){
			$where					.= "AND limit_date >= :limit_over ";
			$array[':limit_over']	 = $post_data['limit_over'];
		}

		if(isset($post_data['limit_under'])){
			$where					.= "AND limit_date <= :limit_under ";
			$array[':limit_under']	 = $post_data['limit_under'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status < :status ";
			$array[':status']		 = 8;
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
		$error						 = $database->errorDb("getPresentboxList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getPresentboxCount
	**	----------------------------------------------
	**	プレゼントボックスカウント
	**
	**************************************************/

	public function getPresentboxCount($post_data){

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
		$array[':site_cd']			 = SITE_CD;
		$array[':user_id']			 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";
		$where						.= "AND user_id = :user_id ";

		if(!empty($post_data['present_id'])){
			$where					.= "AND present_id = :present_id ";
			$array[':present_id']	 = $post_data['present_id'];
		}

		if(isset($post_data['acceptance_date'])){
			$where					.= "AND acceptance_date = :date ";
			$array[':date']			 = $post_data['acceptance_date'];
		}

		if(isset($post_data['category'])){
			$where					.= "AND category = :category ";
			$array[':category']		 = $post_data['category'];
		}

		if(isset($post_data['type'])){
			$where					.= "AND type = :type ";
			$array[':type']			 = $post_data['type'];
		}

		if(isset($post_data['limit_date'])){
			$where					.= "AND limit_date = :limit_date ";
			$array[':limit_date']	 = $post_data['limit_date'];
		}

		if(isset($post_data['limit_over'])){
			$where					.= "AND limit_date >= :limit_over ";
			$array[':limit_over']	 = $post_data['limit_over'];
		}

		if(isset($post_data['limit_under'])){
			$where					.= "AND limit_date <= :limit_under ";
			$array[':limit_under']	 = $post_data['limit_under'];
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

		if(!empty($post_data['limit'])){
			$limit					 = $post_data['limit'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getPresentboxCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows						 = $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;

	}



	/**************************************************
	**
	**	getUserPresentboxCount
	**	----------------------------------------------
	**	プレゼントボックスカウント
	**
	**************************************************/

	public function getUserPresentboxCount($user_id){

		if(empty($user_id)){
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
		$array[':site_cd']			 = SITE_CD;
		$array[':user_id']			 = $user_id;
		$array[':limit_date']		 = date("YmdHis");
		$array[':status']			 = 0;

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";
		$where						.= "AND user_id = :user_id ";
		$where						.= "AND limit_date >= :limit_date ";
		$where						.= "AND status = :status ";

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getUserPresentboxCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows						 = $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;

	}



	/**************************************************
	**
	**	getPresentboxDataById
	**	----------------------------------------------
	**	ログインボーナス情報取得
	**
	**************************************************/

	public function getPresentboxDataById($id,$column=NULL){

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
		$error					 = $database->errorDb("getPresentboxDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>