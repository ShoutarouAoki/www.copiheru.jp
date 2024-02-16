<?php
/********************************************************************************
**	
**	ImageModel.php
**	=============================================================================
**
**	■PAGE / 
**	IMAGE MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	IMAGE CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	IMAGE CLASS
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
class ImageModel{


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
		$this->table		= "images";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getImageList
	**	----------------------------------------------
	**	画像リスト
	**
	**************************************************/

	public function getImageList($post_data,$column=NULL){

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

		if(isset($post_data['file_type'])){
			$where					.= "AND file_type = :file_type ";
			$array[':file_type']	 = $post_data['file_type'];
		}

		$where						.= "AND category = :category ";

		if(isset($post_data['device'])){
			$where					.= "AND device = :device ";
			$array[':device']		 = $post_data['device'];
		}

		if(isset($post_data['target_id'])){
			$where					.= "AND target_id = :target_id ";
			$array[':target_id']	 = $post_data['target_id'];
		}

		if(isset($post_data['display_check'])){
			$where					.= "AND start_date <= :date_s ";
			$array[':date_s']		 = date("YmdHis");
			$where					.= "AND end_date >= :date_e ";
			$array[':date_e']		 = date("YmdHis");
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
		$error						 = $database->errorDb("getImageList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;

	}



	/**************************************************
	**
	**	getImageCount
	**	----------------------------------------------
	**	画像カウント
	**
	**************************************************/

	public function getImageCount($post_data){

		if(empty($post_data['category'])){
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
		$array[':category']			 = $post_data['category'];

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['file_type'])){
			$where					.= "AND file_type = :file_type ";
			$array[':file_type']	 = $post_data['file_type'];
		}

		$where						.= "AND category = :category ";

		if(isset($post_data['device'])){
			$where					.= "AND device = :device ";
			$array[':device']		 = $post_data['device'];
		}

		if(isset($post_data['target_id'])){
			$where					.= "AND target_id = :target_id ";
			$array[':target_id']	 = $post_data['target_id'];
		}

		if(isset($post_data['display_check'])){
			$where					.= "AND start_date <= :date_s ";
			$array[':date_s']		 = date("YmdHis");
			$where					.= "AND end_date >= :date_e ";
			$array[':date_e']		 = date("YmdHis");
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

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getImageCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows						 = $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;

	}



	/**************************************************
	**
	**	getImageData
	**	----------------------------------------------
	**	画像データ
	**
	**************************************************/

	public function getImageData($post_data,$column=NULL){

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

		if(isset($post_data['file_type'])){
			$where					.= "AND file_type = :file_type ";
			$array[':file_type']	 = $post_data['file_type'];
		}

		$where						.= "AND category = :category ";

		if(isset($post_data['device'])){
			$where					.= "AND device = :device ";
			$array[':device']		 = $post_data['device'];
		}

		if(isset($post_data['target_id'])){
			$where					.= "AND target_id = :target_id ";
			$array[':target_id']	 = $post_data['target_id'];
		}

		if(isset($post_data['display_check'])){
			$where					.= "AND start_date <= :date_s ";
			$array[':date_s']		 = date("YmdHis");
			$where					.= "AND end_date >= :date_e ";
			$array[':date_e']		 = date("YmdHis");
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status < :status ";
			$array[':status']		 = 8;
		}

		$order						 = NULL;
		$limit						 = 1;
		$group						 = NULL;

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getImageData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data						 = $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;

	}



	/**************************************************
	**
	**	getImageDataById
	**	----------------------------------------------
	**	画像情報取得
	**
	**************************************************/

	public function getImageDataById($id,$column=NULL){

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
		$error					 = $database->errorDb("getImageDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	replaceContentsImage
	**	----------------------------------------------
	**	コンテンツ内画像置換
	**
	**************************************************/

	public function replaceContentsImage($text,$site_cd,$category,$path=NULL){

		if(empty($text)){
			return FALSE;
		}

		if(!preg_match('/\#(.*)\#/',$text)){
			return FALSE;
		}

		if(empty($category)){
			return FALSE;
		}

		if(empty($site_cd)){
			$site_cd			 = SITE_CD;
		}

		$column					 = "img_name,img_key";

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = $site_cd;

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND category = ".$category." ";
		$where					.= "AND status = 0";
		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("replaceCampaignImage",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		if(empty($path)){
			$path				 = HTTP_CAMPAIGN_IMAGE;
		}

		while($data	= $database->fetchAssoc($rtn)){

			if(preg_match("/".$data['img_key']."/",$text)){
				$text			 = preg_replace("/".$data['img_key']."/", "<img src=".$path."/".$data['img_name'].">", $text);
			}

		}

		$database->freeResult($rtn);

		return $text;

	}

}

?>