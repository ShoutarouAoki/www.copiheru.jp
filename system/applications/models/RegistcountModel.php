<?php
/********************************************************************************
**	
**	RegistcountModel.php
**	=============================================================================
**
**	■PAGE / 
**	REGISTCOUNTS MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	REGISTCOUNTS CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	REGIST COUNTER CLASS
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
class RegistcountModel{


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
		$this->table		= "registcounts";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	insertRegistcount
	**	----------------------------------------------
	**	insertRegistcount
	**
	**************************************************/

	function insertRegistcount($site_cd,$device,$os){

		# CHECK
		if(empty($site_cd)){
			return FALSE;
		}

		if(!is_numeric($site_cd)){
			return FALSE;
		}

		if(empty($device)){
			return FALSE;
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
		$database				 = NULL;
		$database				 = $this->database;
		$output	 				 = NULL;
		$output	 				 = $this->output;

		# AUTHORITY
		$db_auth				 = $database->checkAuthority();
		$db_check				 = NULL;

		# DATABASE CHANGE
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check			 = 1;

		}


		/**************************************************
		**
		**	ACCESS
		**
		**************************************************/

		$count					 = 1;

		# INSERT
		$sql					 = "INSERT INTO ".$this->table." (site_cd,regist_date,device,count) ";
		$sql					.= "VALUES (".$site_cd.",'".date("Ymd")."',".$access_device.",".$count.") ";

		# UPDATE
		$sql					.= "ON DUPLICATE KEY UPDATE count = count + 1";

		$rtn					 = $database->query($sql);
		//$error					 = $database->errorDb("REGISTCOUNTS COUNTER",$rtn->errorCode(),__FILE__,__LINE__);
		//if(!empty($error)){ $output->outputError($error); }

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