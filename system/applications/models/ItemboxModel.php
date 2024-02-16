<?php
/********************************************************************************
**	
**	ItemboxModel.php
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
class ItemboxModel{


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
		$this->table		= "itembox";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getItemboxList
	**	----------------------------------------------
	**	所持アイテムリスト
	**
	**************************************************/

	public function getItemboxList($post_data,$column=NULL){


		if(empty($post_data['user_id'])){
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
		$array[':user_id']		 = $post_data['user_id'];
		

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "user_id = :user_id ";

		if(isset($post_data['item_id'])){
			$where				.= "AND item_id = :item_id ";
			$array[':item_id']	 = $post_data['item_id'];
		}

		if(isset($post_data['unit'])){
			$where				.= "AND unit = :unit ";
			$array[':unit']		 = $post_data['unit'];
		}elseif(isset($post_data['unit_over'])){
			$sql				.= "AND b.unit > :unit ";
			$array[':unit']		 = $post_data['unit_over'];
		}

		if(isset($post_data['status'])){
			$where				.= "AND status = :status ";
			$array[':status']	 = $post_data['status'];
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
		$error					 = $database->errorDb("getItemboxList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getItemboxCount
	**	----------------------------------------------
	**	所持アイテムカウント
	**
	**************************************************/

	public function getItemboxCount($post_data){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		$column					 = "id";

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':user_id']		 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "user_id = :user_id ";

		if(isset($post_data['item_id'])){
			$where				.= "AND item_id = :item_id ";
			$array[':item_id']	 = $post_data['item_id'];
		}

		if(isset($post_data['unit'])){
			$where				.= "AND unit = :unit ";
			$array[':unit']		 = $post_data['unit'];
		}elseif(isset($post_data['unit_over'])){
			$sql				.= "AND b.unit > :unit ";
			$array[':unit']		 = $post_data['unit_over'];
		}

		if(isset($post_data['status'])){
			$where				.= "AND status = :status ";
			$array[':status']	 = $post_data['status'];
		}

		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getItemboxCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows					= $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;


	}



	/**************************************************
	**
	**	getItemboxData
	**	----------------------------------------------
	**	所持アイテム取得
	**
	**************************************************/

	public function getItemboxData($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
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
		$array[':user_id']		 = $post_data['user_id'];

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "user_id = :user_id ";

		if(isset($post_data['item_id'])){
			$where				.= "AND item_id = :item_id ";
			$array[':item_id']	 = $post_data['item_id'];
		}

		if(isset($post_data['unit'])){
			$where				.= "AND unit = :unit ";
			$array[':unit']		 = $post_data['unit'];
		}elseif(isset($post_data['unit_over'])){
			$sql				.= "AND b.unit > :unit ";
			$array[':unit']		 = $post_data['unit_over'];
		}

		if(isset($post_data['status'])){
			$where				.= "AND status = :status ";
			$array[':status']	 = $post_data['status'];
		}

		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getItemboxData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getItemboxDataById
	**	----------------------------------------------
	**	所持アイテム取得
	**
	**************************************************/

	public function getItemboxDataById($id,$column=NULL){


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
		$where					.= "AND status = :status ";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getItemboxDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getItemBoxListJoinOnItems
	**	----------------------------------------------
	**	ユーザー所持アイテム一括取得
	**	----------------------------------------------
	**	itemsとJOIN
	**	ユーザー所持専用
	**	-----------------------------------------------
	** 【発行SQLサンプル】
	**	SELECT b.id, b.item_id, b.user_id, b.unit as item_unit, i.id as items_id, i.name, i.image, i.description FROM itembox b INNER JOIN items i ON b.item_id = i.id AND b.user_id = 1 AND b.unit > 0 AND b.status = 0 ORDER BY i.name
	**
	**************************************************/

	public function getItemboxListJoinOnItems($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				 = "b.id as itembox_id, b.item_id, b.user_id, b.unit, ";
			$column				.= "i.name, i.image, i.description, i.category, i.character_id, i.exchange";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':user_id']		 = $post_data['user_id'];

		# SELECT
		$sql					 = "SELECT ".$column." FROM ".$this->table." b ";

		# JOIN
		$sql					.= "INNER JOIN items i ";

		# ON
		$sql					.= "ON b.item_id = i.id ";

		# WHERE
		$sql					.= "WHERE b.user_id = :user_id ";

		# ITEM ID
		if(isset($post_data['item_id'])){
			$sql				.= "AND b.item_id = :item_id ";
			$array[':item_id']	 = $post_data['item_id'];
		}

		# CHARACTER ID
		if(isset($post_data['character_id'])){
			$sql				.= "AND i.character_id > :character_id ";
			$array[':character_id']		 = 0;
		}

		# UNIT
		if(isset($post_data['unit'])){
			$sql				.= "AND b.unit = :unit ";
			$array[':unit']		 = $post_data['unit'];
		}elseif(isset($post_data['unit_over'])){
			$sql				.= "AND b.unit > :unit ";
			$array[':unit']		 = $post_data['unit_over'];
		}

		# STATUS
		if(!empty($post_data['status'])){
			$sql				.= "AND b.status = :status ";
			$array[':status']	 = $post_data['status'];
		}else{
			$sql				.= "AND b.status = :status ";
			$array[':status']	 = 0;
		}

		# GROUP
		if(!empty($post_data['group'])){
			$sql				.= " GROUP BY ".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql				.= " ORDER BY ".$post_data['order'];
		}

		# LIMIT
		if(!empty($post_data['limit'])){
			$sql				.= " LIMIT ".$post_data['limit'];
		}

		$rtn					 = NULL;
		$result					 = NULL;

		# REMOVE TAGS
		$array					 = $database->removeTags($array);

		try{
			$rtn				 = $database->prepare($sql,$array,$debug=1);
	 		$result				 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}

		return $rtn;

	}


}

?>