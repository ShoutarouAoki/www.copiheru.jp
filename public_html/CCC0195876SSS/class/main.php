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
require_once(dirname(__FILE__).'/../class/html_class.php');


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

	function adminCertify($op_id,$op_pass,$index=NULL){

		$db = new accessDb;
		$db->connectDb();

		$select = "SELECT op_pass,op_group FROM optbls WHERE status = 0 AND id = '".$op_id."'";
		$rtn    = $db->query($select);
		$db->errorDb($select,$db->errno,__FILE__,__LINE__);
		$data   = $db->fetchAssoc($rtn);
		$op_ps  = $data['op_pass'];

		$db->closeDb();

		if($op_pass !== md5($op_ps)){
			/*
			header("Location:".ADMIN_HTTP."error.php");
			exit;
			*/

			if($index){

				$data['error'] .= "<span style=\"color: #FFFFFF; font-size: 13px; font-weight: bold;\">認証できませんでした</span><br />\n";

			}else{

				$html_class	= new htmlClass();

				$html_class->htmlHeader("main");
				print("<p align=\"center\">\n");
				print("<span class=\"style_pink\">認証できませんでした。</span><br />\n");
				print("<a href=\"".ADMIN_HTTP."\" target=\"_top\">戻る</a>\n");
				print("</p>\n");
				print("</body>\n");
				print("</html>\n");
				exit;

			}

		}

		return $data;

	}


	/*********************************************
	**
	**	特殊パスワード認証
	**
	*********************************************/


	function adminPassWord($site_cd,$password,$type){

		if(!$site_cd || !$password || !$type){
			header("Location:".ADMIN_HTTP."option/error.php");
			exit;
		}

		$db = new accessDb;
		$db->connectDb();

		$select = "SELECT normal_pass,admin_pass FROM siteinfos WHERE status = 0 AND id = '".$site_cd."'";
		$rtn    = $db->query($select);
		$db->errorDb($select,$db->errno,__FILE__,__LINE__);
		$data   = $db->fetchAssoc($rtn);
		$db->closeDb();

		# 配信パスワード
		if($type == 1){
			if($password !== md5($data['normal_pass'])){
				header("Location:".ADMIN_HTTP."option/error.php");
				exit;
			}
		}elseif($type == 2){
			if($password !== md5($data['admin_pass'])){
				header("Location:".ADMIN_HTTP."option/error.php");
				exit;
			}
		}

		return TRUE;

	}


	/*********************************************
	**
	**	文字列チェック
	**
	*********************************************/

	function msgCheck($str,$type){

		switch($type){
			case 'nickname':	$max_len = 8; break;
			case 'subject':		$max_len = 50; break;
			case 'contents':	$max_len = 150; break;
			case 'title':		$max_len = 50; break;
			case 'message':		$max_len = 150; break;
			default : return("msgcheck no type Error");
		}

		$chk_str = trim($str);
		$chk_str = strip_tags($chk_str);
		$chk_str = preg_replace("/;/","",$chk_str);
		$chk_str = preg_replace("/　/","",$chk_str);
		$chk_str = preg_replace("/'/","",$chk_str);
		$chk_str = mysql_escape_string($chk_str);

		if($chk_str == ""){
			return($type." empty");
		}elseif(mb_strlen($chk_str,"UTF-8") > $max_len){
			return($type." size over");
		}

		return("ok");

	}


	/*********************************************
	**
	**	日付チェック
	**
	*********************************************/

	function CheckDateTime($datetime){

	    if(!isset($datetime)){ $error_msg = "error"; return $error_msg ; }
		if(!is_numeric($datetime)){ print("Time_err1"); return false; }

		$del_kigo = array("/","-"," ",":","　");
		$rep_data = array("","","","","");
		$datetime = str_replace($del_kigo, $rep_data, $datetime);

		$yy = substr($datetime,0,4);
		$mm = substr($datetime,4,2);
		$dd = substr($datetime,6,2);
		$hh = substr($datetime,8,2);
		$ii = substr($datetime,10,2);
		$ss = substr($datetime,12,2);

		if (!checkdate($mm,$dd,$yy) ) { $error_msg = "error"; return $error_msg ; }
		if($ss < 0 || $ii < 0 || $hh < 0){ $error_msg = "error"; return $error_msg ; }
		if($hh > 23 || $ii > 59 || $ss > 59){ $error_msg = "error"; return $error_msg ; }

		$return_time = $yy.$mm.$dd.$hh.$ii.$ss;
		return $return_time;
	}



	/*********************************************
	**
	**	配列をurlエンコード
	**	------------------------------------------
	**	hiddenで配列を渡したいときに使用
	**
	*********************************************/

	function hidden_encode($string_array){
	  return rawurlencode(serialize($string_array));
	}

	function hidden_decode($string){
	  return unserialize(rawurldecode($string));
	}


	/*********************************************
	**
	**	get_magic_quotes_gpcのエスケープ処理を削除
	**	------------------------------------------
	**
	**
	*********************************************/
	function Magic_Quotes($str) {

		##文字列変換
		$str = trim($str);
		//$str = strip_tags($str); 顔文字が食われる為一時外します
		//$str = preg_replace("/;/","",$str);
		//$str = preg_replace("/　/","",$str);
		//$str = preg_replace("/'/","",$str);

		## stripslashes クォートされた文字列を元に戻します(デフォルトonの場合)
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return $str;
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
	**	writeTextFile
	**	---------------------------------------------
	**	ファイル書き込み処理
	**
	************************************************/
	function writeTextFile($file_name,$data){

		$file	= LOG_ROOT.$file_name;
		$fp = fopen($file,"a");
		if ($fp == FALSE) { return FALSE; }
		fwrite($fp, $data);
		fclose($fp);

		return FALSE;

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
			#'219.111.1.78',
			#'219.111.12.197',
		);

		for($system_count=0;$system_count<count($global_ip_array);$system_count++){

			if($global_ip_array[$system_count] == $_SERVER['REMOTE_ADDR']){
				return TRUE;
				break;
			}

		}

		return FALSE;

	}



	/************************************************
	**
	**	aTagCheck
	**	---------------------------------------------
	**	Aタグダブルコーテチェック
	**
	************************************************/

	function aTagCheck($str){

		$str	= str_replace('\"','&quot;',$str);

		# TAG数チェック
		$pattern1 = "/a href=/";
		preg_match_all($pattern1, $str, $matches1);

		foreach ($matches1 as $val1) {
			$count1	= count($val1);
		}

		# ダブルコーテ数チェック
		$pattern2 = "/a href=&quot;/";
		preg_match_all($pattern2, $str, $matches2);

		foreach ($matches2 as $val2) {
			$count2	= count($val2);
		}

		if($count1 == $count2){

			return TRUE;

		}else{

			return FALSE;

		}

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
