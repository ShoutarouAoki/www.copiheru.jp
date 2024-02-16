<?
/********************************************************
**
**	main.php
**	-----------------------------------------------------
**	管理画面でよく使うCLASS群
**	-----------------------------------------------------
**	2010.03.03 kuma
*********************************************************/


# CONF読み込み
require_once(dirname(__FILE__).'/../CONF/config.php');
require_once(dirname(__FILE__).'/../class/database.php');


/*********************************************
**
**	管理画面認証 CLASS
**
*********************************************/

class adminMain {


	/*********************************************
	**
	**	ID / PASS 認証
	**
	*********************************************/

	function adminCertify($op_id,$op_pass){

		$db = new accessDb;
		$db->connectDb();

		$select = "SELECT op_pass FROM optbls WHERE status = 0 AND id = '".$op_id."'";
		$rtn    = $db->query($select);
		$db->errorDb($select,$db->errno,__FILE__,__LINE__);
		$data   = $db->fetchAssoc($rtn);
		$op_ps  = $data['op_pass'];

		$db->closeDb();

		if($op_pass !== md5($op_ps)){
			header("Location:".ADMIN_HTTP."error.php");
			exit;
		}

		return TRUE;

	}




	function fgetcsv_reg(&$handle,$length,$d,$e){

		$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = "";
		while($eof != true){
			$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
			$itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
			if ($itemcnt % 2 == 0) $eof = true;
		}
		$_csv_line = preg_replace('/(?:\\r\\n|[\\r\\n])?$/', $d, trim($_line));
		$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
		preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
		$_csv_data = $_csv_matches[1];
		for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
			$_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
			$_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
		}

		return empty($_line) ? false : $_csv_data;

	}


	/************************************************
	**
	**	debugSystem
	**	---------------------------------------------
	**	指定IPのみのデバッグ関数
	**
	************************************************/

	function debugSystem(){

		# SYSTEM GLOBAL IP
		$global_ip_array	= array(
			'221.184.239.9',
			'219.111.12.197',
		);

		for($system_count=0;$system_count<count($global_ip_array);$system_count++){

			if($global_ip_array[$system_count] == $_SERVER['REMOTE_ADDR']){
				return TRUE;
				break;
			}

		}

		return FALSE;

	}


}



/*********************************************
**
**	デバッグ用 CLASS
**
*********************************************/

class adminDebug {

	var $level;

	function debugLevel($level){

		$this->level = $level;

		switch($this->level){
			case 0: error_reporting(0); break;
			case 1: error_reporting(E_ERROR | E_WARNING | E_PARSE); break;
			case 2: error_reporting(E_ALL & ~E_NOTICE); break;
			case 3: error_reporting(E_ALL); break;
			case 4: error_reporting(E_ALL | E_STRICT); break;
			default : error_reporting(0);
		}

	}
	function debugtrace($str){

		if($this->level > 2){
			/*
			$trace = var_export(debug_backtrace(),TRUE);
			print_r($trace);
			*/
			debug_print_backtrace();
		}

	}

}



/*********************************************
**
**	処理時間計測 CLASS
**
*********************************************/

class benchMark {

	var $start;
	var $end;
	var $score;

	function benchmark(){}

	function start(){
		$this->start=$this->_now();
	}

	function end(){
		$this->end=$this->_now();
		$this->score=round($this->end-$this->start,5);
	}

	function _now(){ 
		list($msec,$sec)=explode(' ',microtime()); 
		return((float)$msec+(float)$sec);
	}

}




?>
