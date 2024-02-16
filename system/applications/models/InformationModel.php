<?php
/********************************************************************************
**	
**	InformationModel.php
**	=============================================================================
**
**	■PAGE / 
**	INFORMATIONS MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	INFORMATIONS CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	INFORMATIONS CLASS
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: KARAT SYSTEM
**	CREATE DATE : 2014/10/31
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
class InformationModel{


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
		$this->table		= "informations";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getInformationList
	**	----------------------------------------------
	**	INFORMATION LIST 抽出
	**
	**************************************************/

	public function getInformationList($page_data,$column=NULL){

		if(empty($page_data['category']) && empty($page_data['id'])){
			return FALSE;
		}

		if(empty($column)){
			$column					 = "*";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output	 					 = NULL;
		$output	 					 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		if(!empty($page_data['id'])){

			$where		 			 = "id = :id ";
			$array[':id']			 = $page_data['id'];

			$where					.= "AND status = :status";
			$array[':status']		 = 1;

			$order					 = NULL;
			$limit					 = 1;

		}else{

			$where					 = "site_cd = :site_cd ";

			$where		 			.= "AND category = :category ";
			$array[':category']		 = $page_data['category'];


			if(isset($page_data['type'])){
			$where		 			.= "AND type = :type ";
			$array[':type']			 = $page_data['type'];
			}

			$where					.= "AND display_date < :display_date ";
			$array[':display_date']	 = date("Y-m-d H:i:s");

			if(!isset($page_data['status'])){
			$where					.= "AND status = :status";
			$array[':status']		 = 1;
			}else{
			$where					.= "AND status = :status";
			$array[':status']		 = $page_data['status'];
			}

			$order					 = NULL;
			$limit					 = NULL;

			$order					 = "display_date DESC , id DESC";
			if(!empty($page_data['list'])){
			$limit					 = $page_data['set'].",".$page_data['list'];
			}

		}

		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getInformationList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;

	}



	/**************************************************
	**
	**	getInformationCount
	**	----------------------------------------------
	**	INFORMATION LIST 抽出
	**
	**************************************************/

	public function getInformationCount($page_data){

		if(empty($page_data['category'])){
			return FALSE;
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output	 				 = NULL;
		$output	 				 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;


		$column 				 = "id";

		$where					 = "site_cd = :site_cd ";

		$where		 			.= "AND category = :category ";
		$array[':category']		 = $page_data['category'];

		if(isset($page_data['type'])){
		$where		 			.= "AND type = :type ";
		$array[':type']			 = $page_data['type'];
		}

		$where					.= "AND display_date < :display_date ";
		$array[':display_date']	 = date("Y-m-d H:i:s");

		if(!isset($page_data['status'])){
		$where					.= "AND status = :status";
		$array[':status']		 = 1;
		}else{
		$where					.= "AND status = :status";
		$array[':status']		 = $page_data['status'];
		}

		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getInformationCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows					 = $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;

	}



	/**************************************************
	**
	**	getInformationById
	**	----------------------------------------------
	**	INFORMATION DATA 抽出
	**
	**************************************************/

	public function getInformationById($id,$column=NULL){

		if(empty($id)){
			return FALSE;
		}

		if(empty($column)){
			$column		= "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "id = :id ";
		$array[':id']			 = $id;
		$where					.= "AND display_date < :display_date ";
		$array[':display_date']	 = date("Y-m-d H:i:s");
		$where					.= "AND status = :status";
		$array[':status']		 = 1;
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getInformationById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					 = $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;

	}


}

?>