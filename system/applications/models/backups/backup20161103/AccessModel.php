<?php
/********************************************************************************
**	
**	AccessModel.php
**	=============================================================================
**
**	■PAGE / 
**	ACCESSES MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	ACCESSES CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	ACCESS COUNTER CLASS
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
class AccessModel{


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
		$this->table		= "accesses";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	insertAccess
	**	----------------------------------------------
	**	アクセスカウント
	**
	**************************************************/

	function insertAccess($site_cd,$user_id=NULL,$directory,$page=NULL,$device,$os,$session=NULL){

		global	$access_page_array;

		if(empty($site_cd) || empty($directory)){
			return FALSE;
		}

		if(!is_numeric($site_cd)){
			return FALSE;
		}

		if(empty($user_id)){
			$user_id	 = 0;
		}

		# DB / MAIN CLASS
		$database		 = NULL;
		$database		 = $this->database;
		$output	 		 = NULL;
		$output	 		 = $this->output;

		/**************************************************
		**
		**	VISITOR
		**
		**************************************************/

		$visitor_array[':site_cd']		 = SITE_CD;
		$visitor_array[':access_date']	 = date("Y-m-d");
		$visitor_array[':user_id']		 = $user_id;
		$visitor_array[':device']		 = $device;
		$visitor_array[':os']			 = $os;
		$visitor_array[':status']		 = 1;

		$visitor_table					 = "visitors";
		$visitor_column					 = "id";
		$visitor_where					 = "site_cd = :site_cd ";
		$visitor_where					.= "AND access_date = :access_date ";
		$visitor_where					.= "AND user_id = :user_id ";
		$visitor_where					.= "AND device = :device ";
		$visitor_where					.= "AND os = :os ";
		$visitor_where					.= "AND status = :status";

		$visitor_rtn					 = $database->selectDb($visitor_table,$visitor_column,$visitor_where,$visitor_array,NULL,1,NULL);
		$error							 = $database->errorDb("VISITORS",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$visitor_data					 = $database->fetchAssoc($visitor_rtn);

		# AUTHORITY
		$db_auth						 = $database->checkAuthority();
		$db_check						 = NULL;

		# DATABASE CHANGE
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check		 = 1;

		}

		if(empty($visitor_data['id'])){

			$visitor_edit['site_cd']		= SITE_CD;
			$visitor_edit['access_date']	= date("Y-m-d");
			$visitor_edit['user_id']		= $user_id;
			$visitor_edit['device']			= $device;
			$visitor_edit['os']				= $os;
			$visitor_edit['status']			= 1;

			# INSERT
			$insert_id	= $database->insertDb($visitor_table,$visitor_edit);
			$error		= $database->errorDb("INSERT VISITORS",NULL,__FILE__,__LINE__);
			if(!empty($error)){ $mainClass->outputError($error); }

		}


		/**************************************************
		**
		**	ACCESS
		**
		**************************************************/

		# UNIQUE ACCESS
		if(!empty($session)){

			$access_edit['site_cd']			= SITE_CD;
			$access_edit['access_date']		= date("Y-m-d H:i:s");
			$access_edit['user_id']			= $user_id;
			$access_edit['device']			= $device;
			$access_edit['os']				= $os;
			$access_edit['status']			= 1;

			# INSERT
			$insert_id	= $database->insertDb($this->table,$access_edit);
			$error		= $database->errorDb("INSERT ACCESSES",NULL,__FILE__,__LINE__);
			if(!empty($error)){ $mainClass->outputError($error); }

		}


		/**************************************************
		**
		**	PAGEVIEWS
		**
		**************************************************/

		/*
		$count	= 1;

		# INSERT
		$sql	 = "INSERT INTO pageviews (site_cd,access_date,device,os,directory,page,count,status) ";
		$sql	.= "VALUES (".SITE_ID.",'".date("Y-m-d")."',".$device.",".$os.",".$directory.",".$page.",".$count.",1) ";

		# UPDATE
		$sql	.= "ON DUPLICATE KEY UPDATE count = count + 1";

		$result	 = $database->query($sql);
		$error	 = $database->errorDb("PAGEVIEW COUNTER",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }
		*/


		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}

		return $result;


	}




}

?>