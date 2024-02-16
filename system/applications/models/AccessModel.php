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

	function insertAccess($site_cd,$user_id=NULL,$device,$os){

		if(empty($site_cd)){
			return FALSE;
		}

		if(!is_numeric($site_cd)){
			return FALSE;
		}

		if(empty($user_id)){
			$user_id	 = 0;
		}

		# アクセス判別 / これは元々のシステムの定義に沿って。option.config.phpに$system_device_arrayで定義

		# PC
		if($device == 1){

			$access_device		= 4;

		# スマフォ
		}elseif($device == 2){

			# iPhone
			if($os == 1){

				$access_device	= 5;

			# Android
			}elseif($os == 2){

				$access_device	= 7;

			#その他
			}else{

				$access_device	= 9;

			}


		# その他は
		}else{

			$access_device		= 9;

		}

		# DB / MAIN CLASS
		$database		 = NULL;
		$database		 = $this->database;
		$output	 		 = NULL;
		$output	 		 = $this->output;

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


		/**************************************************
		**
		**	ACCESS
		**
		**************************************************/

		$access_edit['site_cd']			= $site_cd;
		$access_edit['access_date']		= date("YmdHis");
		$access_edit['user_id']			= $user_id;
		$access_edit['device']			= $access_device;

		# INSERT
		$database->insertDb($this->table,$access_edit);
		$error		= $database->errorDb("INSERT ACCESSES",NULL,__FILE__,__LINE__);
		if(!empty($error)){ $mainClass->outputError($error); }


		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}

		return TRUE;


	}




}

?>