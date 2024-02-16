<?php
/********************************************************************************
**	
**	PayModel.php
**	=============================================================================
**
**	■PAGE / 
**	PAY MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	PAY CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	PAY CLASS
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
class PayModel{


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
		$this->table		= "pays";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getPayList
	**	----------------------------------------------
	**	支払データリスト
	**
	**************************************************/

	public function getPayList($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column						 = "*";
		}

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		# PARAMETER
		$array							 = array();
		$array[':site_cd']				 = SITE_CD;
		$array[':user_id']				 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		$where							 = "site_cd = :site_cd ";
		$where							.= "AND user_id = :user_id ";

		if(isset($post_data['pay_date'])){
			$where						.= "AND pay_date = :pay_date ";
			$array[':pay_date']			 = $post_data['pay_date'];
		}

		if(isset($post_data['pay_amount'])){
			$where						.= "AND pay_amount = :pay_amount ";
			$array[':pay_amount']		 = $post_data['pay_amount'];
		}

		if(isset($post_data['settlement_id'])){
			$where						.= "AND settlement_id = :settlement_id ";
			$array[':settlement_id']	 = $post_data['settlement_id'];
		}

		if(!empty($post_data['sid'])){
			$where						.= "AND sid = :sid ";
			$array[':sid']				 = $post_data['sid'];
		}

		if(!empty($post_data['limit_time'])){
			$where						.= "AND limit_time >= :limit_time ";
			$array[':limit_time']		 = $post_data['limit_time'];
		}

		if(isset($post_data['clear'])){
			$where						.= "AND clear = :clear ";
			$array[':clear']			 = $post_data['clear'];
		}else{
			$where						.= "AND clear = :clear ";
			$array[':clear']			 = 0;
		}

		if(isset($post_data['finish'])){
			$where						.= "AND finish = :finish ";
			$array[':finish']			 = $post_data['finish'];
		}

		if(isset($post_data['error'])){
			$where						.= "AND error = :error ";
			$array[':error']			 = $post_data['error'];
		}else{
			$where						.= "AND error = :error ";
			$array[':error']			 = 0;
		}

		if(isset($post_data['status'])){
			$where						.= "AND status = :status ";
			$array[':status']			 = $post_data['status'];
		}else{
			$where						.= "AND status = :status ";
			$array[':status']			 = 0;
		}

		$order							 = NULL;
		$limit							 = NULL;
		$group							 = NULL;

		if(!empty($post_data['order'])){
			$order						 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit						 = $post_data['limit'];
		}

		if(!empty($post_data['list'])){
			$limit						 = $post_data['set'].",".$post_data['list'];
		}

		if(!empty($post_data['group'])){
			$group						 = $post_data['group'];
		}

		$rtn							 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error							 = $database->errorDb("getPayList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;

	}



	/**************************************************
	**
	**	getPayData
	**	----------------------------------------------
	**	支払データ取得
	**
	**************************************************/

	public function getPayData($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column						 = "*";
		}

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		# PARAMETER
		$array							 = array();
		$array[':site_cd']				 = SITE_CD;
		$array[':user_id']				 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		$where							 = "site_cd = :site_cd ";
		$where							.= "AND user_id = :user_id ";

		if(isset($post_data['pay_date'])){
			$where						.= "AND pay_date = :pay_date ";
			$array[':pay_date']			 = $post_data['pay_date'];
		}

		if(isset($post_data['pay_amount'])){
			$where						.= "AND pay_amount = :pay_amount ";
			$array[':pay_amount']		 = $post_data['pay_amount'];
		}

		if(isset($post_data['settlement_id'])){
			$where						.= "AND settlement_id = :settlement_id ";
			$array[':settlement_id']	 = $post_data['settlement_id'];
		}

		if(!empty($post_data['sid'])){
			$where						.= "AND sid = :sid ";
			$array[':sid']				 = $post_data['sid'];
		}

		if(!empty($post_data['limit_time'])){
			$where						.= "AND limit_time >= :limit_time ";
			$array[':limit_time']		 = $post_data['limit_time'];
		}

		if(isset($post_data['clear'])){
			$where						.= "AND clear = :clear ";
			$array[':clear']			 = $post_data['clear'];
		}else{
			$where						.= "AND clear = :clear ";
			$array[':clear']			 = 0;
		}

		if(isset($post_data['finish'])){
			$where						.= "AND finish = :finish ";
			$array[':finish']			 = $post_data['finish'];
		}

		if(isset($post_data['error'])){
			$where						.= "AND error = :error ";
			$array[':error']			 = $post_data['error'];
		}else{
			$where						.= "AND error = :error ";
			$array[':error']			 = 0;
		}

		if(isset($post_data['status'])){
			$where						.= "AND status = :status ";
			$array[':status']			 = $post_data['status'];
		}else{
			$where						.= "AND status = :status ";
			$array[':status']			 = 0;
		}

		$order							 = NULL;
		$limit							 = 1;
		$group							 = NULL;

		$rtn							 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error							 = $database->errorDb("getPayData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;

	}



	/**************************************************
	**
	**	getPayDataById
	**	----------------------------------------------
	**	支払情報取得
	**
	**************************************************/

	public function getPayDataById($id,$column=NULL){

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
		$error					 = $database->errorDb("getPayDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>