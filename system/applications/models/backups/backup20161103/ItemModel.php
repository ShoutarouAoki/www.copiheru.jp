<?php
/********************************************************************************
**	
**	ItemModel.php
**	=============================================================================
**
**	■PAGE / 
**	ITEM BOX MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	ITEM BOX CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	ITEM BOX CLASS
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
class ItemModel{


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
		$this->table		= "items";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getItemList
	**	----------------------------------------------
	**	アイテムリスト
	**
	**************************************************/

	public function getItemList($post_data,$column=NULL){

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

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['item_id'])){
			$where					.= "AND item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}else{
			$where					.= "AND item_id = :item_id ";
			$array[':item_id']		 = 0;
		}

		if(isset($post_data['character_id'])){
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}else{
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = 0;
		}

		if(isset($post_data['category'])){
			$where					.= "AND category = :category ";
			$array[':category']		 = $post_data['category'];
		}

		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}else{
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = 0;
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
			$order						 = $post_data['order'];
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
		$error						 = $database->errorDb("getItemList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getItemData
	**	----------------------------------------------
	**	アイテムデータ
	**
	**************************************************/

	public function getItemData($post_data,$column=NULL){

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

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['item_id'])){
			$where					.= "AND item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}else{
			$where					.= "AND item_id = :item_id ";
			$array[':item_id']		 = 0;
		}

		if(isset($post_data['character_id'])){
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}else{
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = 0;
		}

		if(isset($post_data['category'])){
			$where					.= "AND category = :category ";
			$array[':category']		 = $post_data['category'];
		}

		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}else{
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = 0;
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status = :status ";
			$array[':status']		 = 0;
		}

		$order						 = NULL;
		$limit						 = 1;
		$group						 = NULL;

		if(!empty($post_data['order'])){
			$order					 = $post_data['order'];
		}

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getItemData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data						= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getItemDataById
	**	----------------------------------------------
	**	アイテム取得
	**
	**************************************************/

	public function getItemDataById($id,$column=NULL){


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
		$error					 = $database->errorDb("getItemDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>