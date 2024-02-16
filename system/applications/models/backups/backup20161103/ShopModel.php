<?php
/********************************************************************************
**	
**	ShopModel.php
**	=============================================================================
**
**	■PAGE / 
**	SHOP MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	SHOP CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	SHOP CLASS
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
class ShopModel{


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
		$this->table		= "shops";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getShopList
	**	----------------------------------------------
	**	ショップデータリスト
	**
	**************************************************/

	public function getShopList($post_data,$column=NULL){

		if(empty($post_data['category'])){
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
		$array[':category']			 = $post_data['category'];

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['shop_id'])){
			$where					.= "AND shop_id = :shop_id ";
			$array[':shop_id']		 = $post_data['shop_id'];
		}else{
			$where					.= "AND shop_id = :shop_id ";
			$array[':shop_id']		 = 0;
		}

		if(isset($post_data['item_id'])){
			$where					.= "AND item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}

		if(!empty($post_data['date'])){
			$where					.= "AND start_date <= :start_date ";
			$array[':start_date']	 = $post_data['date'];
			$where					.= "AND end_date >= :end_date ";
			$array[':end_date']		 = $post_data['date'];
		}

		$where						.= "AND category = :category ";

		if(isset($post_data['type'])){
			$where					.= "AND type = :type ";
			$array[':type']			 = $post_data['type'];
		}

		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status = :status ";
			$array[':status']		 = 0;
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
		$error						 = $database->errorDb("getShopList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getShopDataById
	**	----------------------------------------------
	**	ショップ情報取得
	**
	**************************************************/

	public function getShopDataById($id,$column=NULL){

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
		$error					 = $database->errorDb("getShopDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>