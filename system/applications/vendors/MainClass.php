<?php
/********************************************************************************
**	
**	MainClass.php
**	=============================================================================
**
**	■PAGE / 
**	MAIN MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	MAIN CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	MAIN機能呼び出し
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: AKITOSHI TAKAI
**	CREATE DATE : 2012/12/01
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
class MainClass{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	private $debugline;

	# CONSTRUCT
	function __construct(){
		
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/************************************************
	**
	**	getControllerDirectory
	**	---------------------------------------------
	**	GET CONTROLLER DIRECTORY
	**
	************************************************/

	public function getControllerDirectory($directory=NULL,$device_file=NULL){

		# DIRECTORY SCRIPT FILE
		if(!empty($directory)){

			# FULL PATH
			$file_directory_path		= DOCUMENT_ROOT_CONTROLLERS."/".$directory."Controller.php";

			# CHECK DEFAULT DIRECTORY
			if(file_exists($file_directory_path)){

				# DIRECTORY
				$result					= $directory;

			}else{

				# DIRECTORY
				$result					= "notfound";

			}

		# INDEX
		}else{

			# DIRECTORY
			$result						= "index";

		}

		return $result;

	}



	/************************************************
	**
	**	getPrivateDirectory
	**	---------------------------------------------
	**	GET PRIVATE CONTROLLER / HTML DIRECTORY
	**
	************************************************/

	public function getPrivateDirectory($root,$directory,$device_file,$private_dir=NULL){

		if(empty($root) || empty($directory) || empty($device_file)){
			return FALSE;
		}

		$result								= NULL;
		$result['private_controller']		= NULL;
		$result['private_html']				= NULL;

		# FULL PATH
		$controller_directory_path			= DOCUMENT_ROOT_PRIVATES_CONTROLLERS."/".$root."/".$directory."Controller.php";

		# NORMAL
		if(empty($private_dir)){
			$html_directory_path			= DOCUMENT_ROOT_PRIVATES_HTML."/".$root."/".$device_file."/".$directory.".html";
		# PRIVATE DIR
		}else{
			$html_directory_path			= DOCUMENT_ROOT_PRIVATES_HTML."/".$root."/".$device_file."/".$private_dir."/".$directory.".html";
		}

		# CHECK DIRECTORY
		if(file_exists($controller_directory_path)){
			$result['private_controller']	= $controller_directory_path;
		}

		# CHECK DIRECTORY
		if(file_exists($html_directory_path)){
			$result['private_html']			= $html_directory_path;
		}

		$this->debug($controller_directory_path);
		$this->debug($html_directory_path);


		return $result;

	}



	/************************************************
	**
	**	getViewDirectory
	**	---------------------------------------------
	**	GET CONTROLLER DIRECTORY
	**
	************************************************/

	public function getViewDirectory($directory=NULL,$page=NULL,$device_file=NULL){
		# DIRECTORY SCRIPT FILE
		if(!empty($directory)){

			if(empty($page)){
				$page					= "index";
			}

			# FULL PATH
			$view_directory_path		= DOCUMENT_ROOT_VIEWS."/".$device_file."/".$directory."/".$page.".inc";
			$view_file_path				= DOCUMENT_ROOT_VIEWS."/".$device_file."/".$directory.".inc";
			$notfound_path				= DOCUMENT_ROOT_VIEWS."/".$device_file."/templates/notfound.inc";


			# CHECK DEFAULT DIRECTORY
			if(file_exists($view_directory_path)){

				# DIRECTORY
				$result					= $view_directory_path;

			# CHECK PRIVATE DIRECTORY
			}elseif(file_exists($view_file_path)){

				# DIRECTORY
				$result					= $view_file_path;

			}else{

				# DIRECTORY
				$result					= $notfound_path;

			}

		# INDEX
		}else{

			# DIRECTORY
			$result			= $notfound_path;

		}
		return $result;

	}




	/**************************************************
	**
	**	getPagePath
	**	----------------------------------------------
	**	ROOT PATH 生成
	**
	**************************************************/

	public function getPagePath($directory){

		if(THIS_DOCUMENT == DOCUMENT_ROOT_USER_WEB){

			if($directory == "index"){
				$result		= "/";
			}else{
				$result		= "/".$directory."/";
			}

		}else{

			if($directory == "index"){
				$result		= "";
			}else{
				$result		= "/".$directory."/";
			}

		}

		return	$result;

	}



	/************************************************
	**
	**	getArrayValue
	**	---------------------------------------------
	**	ARRAY VALUE 取得
	**
	************************************************/

	public function getHttpValues($values=NULL){

		# RESULT
		$result	= NULL;

		# VALUES
		if(!empty($values)){
			$result	= explode("/",$values);
		}

		return $result;

	}



	/************************************************
	**
	**	getRequestData
	**	---------------------------------------------
	**	ARRAY CONTENTS 生成
	**
	************************************************/

	public function getRequestData($data){

		if(empty($data)){
			return FALSE;
		}

		global	$request;

		$result	= $request;

		foreach($data as $key => $value){
			$request = isset($value) ? $value : NULL;
			$result[$key]	= $request;
		}

		$this->debug($result);

		return $result;

	}



	/************************************************
	**
	**	getArrayContents
	**	---------------------------------------------
	**	ARRAY CONTENTS 生成
	**
	************************************************/

	public function getArrayContents($array,$data){

		if(empty($array) || empty($data)){
			return FALSE;
		}

		$result	= NULL;
		$column	= NULL;
		$error	= NULL;

		foreach($data as $key => $value){

			if(isset($array[$key])){
				$column	= $array[$key];
			}else{
				continue;
			}

			if(preg_match("/,/",$value)){
				$error	= 1;
			}elseif(preg_match("/;/",$value)){
				$error	= 1;
			}elseif(preg_match("/:/",$value)){
				$error	= 1;
			}

			if(!empty($error)){
				break;
			}

			$result[$column]	= $value;
		}

		if(!empty($error)){
			return FALSE;
		}

		return $result;

	}



	/************************************************
	**
	**	makeHiddenData
	**	---------------------------------------------
	**	$_POST HIDDEN 生成
	**
	************************************************/

	public function makeHiddenData($data,$checkout=NULL){

		if(empty($data)){
			return FALSE;
		}

		$result	= NULL;

		foreach($data as $key => $value){

			# TOKEN CONTINUE
			if($key == "token"){ continue; }

			$checkcontinue	= NULL;
			if(!empty($checkout)){
				foreach($checkout as $checkkey => $checkvalue){
					if($key == $checkvalue){
						$checkcontinue	= 1;
						break;
					}
				}
			}

			if($value == "" || !empty($checkcontinue)){
				continue;
			}

			$result .=	"<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\" />\n";

		}

		return $result;

	}



	/************************************************
	**
	**	makePostData
	**	---------------------------------------------
	**	$_POST DATA 生成
	**
	************************************************/

	public function makePostData($data,$checkout=NULL){

		if(empty($data)){
			return FALSE;
		}

		$result	= NULL;
		$result	= array();

		foreach($data as $key => $value){

			$checkcontinue	= NULL;
			if(!empty($checkout)){
				foreach($checkout as $checkkey => $checkvalue){
					if($key == $checkvalue){
						$checkcontinue	= 1;
						break;
					}
				}
			}

			if($value == "" || !empty($checkcontinue)){
				continue;
			}

			$result[$key]	= $value;

		}

		return $result;

	}



	/************************************************
	**
	**	getPostRoot
	**	---------------------------------------------
	**	$_POST RETURN 生成
	**
	************************************************/

	public function getPostRoot($data=NULL){

		if(empty($data)){
			return FALSE;
		}

		$result	= NULL;

		foreach($data as $key => $value){
			if($key == "token"){ continue; }
			if($key == "uuid"){ continue; }
			if(is_array($value)){ continue; }
			$result .=	"/".$key."~".$value;
		}

		return $result;

	}



	/************************************************
	**
	**	getPostData
	**	---------------------------------------------
	**	$_POST RETURN 生成
	**
	************************************************/

	public function getPostData($category=NULL,$type=NULL,$status=NULL,$set=NULL,$list=NULL,$post_data=NULL){

		# CATEGORY
		if(isset($category)){
			$result['category']	= $category;
		}

		# TYPE
		if(isset($type)){
			$result['type']		= $type;
		}

		# DISPLAY
		if(!isset($status)){
			$result['status']	= 1;
		}else{
			$result['status']	= $status;
		}

		# LIST
		if(empty($list)){
			$result['list']		= 10;
		}else{
			$result['list']		= $list;
		}

		# SET
		if(empty($set)){
			$result['set']		= 0;
		}else{
			if(is_numeric($set)){
				$result['set']	= $set;
			}else{
				$result['set']	= 0;
			}
		}

		# POST DATA
		if(isset($post_data)){

			foreach($post_data as $key => $value){

				if($key == "category" || $key == "type" || $key == "status" ||  $key == "set" || $key == "list"){
					continue;
				}

				if($key == "id" || $key == "ranking" || $key == "rank" || $key == "rank_id"){
					continue;
				}

				$result[$key] = $value;

			}

		}

		return $result;

	}



	/************************************************
	**
	**	redirect
	**	---------------------------------------------
	**	リダイレクト
	**
	************************************************/

	public function redirect($url){

		if(empty($url)){
			return FALSE;
		}

		global $database;

		if(!empty($database)){

			# CLOSE DATABASE
			$database->closeDb();
			$database->closeStmt();

		}

		header("Location: ".$url);
		exit();

	}



	/**************************************************
	**
	**	writeAccessLog
	**	----------------------------------------------
	**	アクセスカウンター
	**
	**************************************************/

	public function writeAccessLog($site_id,$user=NULL,$directory,$page,$device,$os,$session=NULL){

		global	$access_page_array;

		if(empty($site_id) || empty($directory)){
			return FALSE;
		}

		if(!is_numeric($site_id)){
			return FALSE;
		}

		# CHECK スマフォなのにUUIDないやつ
		if(empty($user) && $device == 2 && $os == 1){
			$device		 = 1;
			$os			 = 0;
		}

		if($device != 2 && empty($user)){
			$user		 = $_SERVER['REMOTE_ADDR'];
		}


		/**************************************************
		**
		**	VISITOR
		**
		**************************************************/

		# VISITOR LOG DIRECTORY COPY
		if(!file_exists(DOCUMENT_ROOT_VISITORLOG.$site_id)){
			$cpCommandLog	= "cp -a ".DOCUMENT_ROOT_VISITORLOG."default ".DOCUMENT_ROOT_VISITORLOG.$site_id;
			exec($cpCommandLog,$output, $return_var);
		}

		# VISITOR LOG FILE
		$visitorlogfile_path			= DOCUMENT_ROOT_VISITORLOG.$site_id."/".date("Ymd").".txt";

		# VISITOR LOG FILE CHECK
		if(!file_exists($visitorlogfile_path)){
			$create = fopen($visitorlogfile_path,'w');
			if ($create == FALSE) { return FALSE; }
			fclose($create);
		}

		$visitor_write					= NULL;

		if(!empty($user) && !empty($session)){

			# TEXT BODY
			$line		= NULL;
			$text_body	= NULL;
			$log_line	= NULL;
			$visitor	= NULL;
			$accesses	= NULL;

			# FILE OPEN
			$fp = fopen($visitorlogfile_path,'r');
			if ($fp == FALSE) { return FALSE; }
			while (!feof($fp)) {

				$line		 = fgets($fp);
				if(empty($line)){ break; }

				$log_content    	= explode(",",$line);
				$user_id			= $log_content[0];
				$device_log			= $log_content[1];
				$os_log				= $log_content[2];

				if(preg_match("/\n/",$os_log)){
					$os_log			= str_replace("\n","",$os_log);
				}

				# VISITOR
				if($user == $user_id){
					$visitor_write	 = 1;
				}

			}

			fclose($fp);

		}


		/**************************************************
		**
		**	ACCESS
		**
		**************************************************/

		# ACCESS LOG DIRECTORY COPY
		if(!file_exists(DOCUMENT_ROOT_ACCESSLOG.$site_id)){
			$cpCommandLog	= "cp -a ".DOCUMENT_ROOT_ACCESSLOG."default ".DOCUMENT_ROOT_ACCESSLOG.$site_id;
			exec($cpCommandLog,$output, $return_var);
		}

		# ACCESS LOG FILE
		$accesslogfile_path			= DOCUMENT_ROOT_ACCESSLOG.$site_id."/".date("Ymd").".txt";

		# ACCESS LOG FILE CHECK
		if(!file_exists($accesslogfile_path)){
			$create = fopen($accesslogfile_path,'w');
			if ($create == FALSE) { return FALSE; }
			fclose($create);
		}


		/**************************************************
		**
		**	PAGE VIEW
		**
		**************************************************/

		# PAGEVIEW LOG DIRECTORY COPY
		if(!file_exists(DOCUMENT_ROOT_PAGEVIEWLOG.$site_id)){
			$cpCommandLog2	= "cp -a ".DOCUMENT_ROOT_PAGEVIEWLOG."default ".DOCUMENT_ROOT_PAGEVIEWLOG.$site_id;
			exec($cpCommandLog2,$output, $return_var);
		}

		# PAGE VIEW LOG FILE
		$pageviewlogfile_path		= DOCUMENT_ROOT_PAGEVIEWLOG.$site_id."/".date("Ymd").".txt";

		# PAGE VIEW LOG FILE CHECK
		if(!file_exists($pageviewlogfile_path)){
			$create = fopen($pageviewlogfile_path,'w');
			if ($create == FALSE) { return FALSE; }
			fclose($create);
		}

		# TEXT BODY
		$count		= 1;
		$total		= 0;
		$line		= NULL;
		$text_body	= NULL;
		$log_line	= NULL;
		$update		= NULL;
		$visitor	= NULL;
		$accesses	= NULL;

		# FILE OPEN
		$fp = fopen($pageviewlogfile_path,'r');
		if ($fp == FALSE) { return FALSE; }
		while (!feof($fp)) {

			$line		 = fgets($fp);
			if(empty($line)){ break; }

			$log_content    	= explode(",",$line);
			$device_log			= $log_content[0];
			$os_log				= $log_content[1];
			$directory_log		= $log_content[2];
			$page_log			= $log_content[3];
			$pageview_log		= $log_content[4];

			if(preg_match("/\n/",$pageview_log)){
				$pageview_log	= str_replace("\n","",$pageview_log);
			}

			# ACCESS
			if($device_log == $device && $os_log == $os && $directory_log == $directory && $page_log == $page){
				$pageview_log	+= $count;
				$update			 = 1;
			}

			# VISITOR
			if(!empty($session) && $device_log == $device && $os_log == $os && $directory_log == 0 && $page_log == 0){
				$pageview_log	+= $count;
				$visitor		 = 1;
			}

			$log_line			 = $device_log.",".$os_log.",".$directory_log.",".$page_log.",".$pageview_log."\n";

			$text_body			.= $log_line;

			# TOTAL COUNT
			if($page_log > 0){
				$total			+= $pageview_log;
			}

		}
		fclose($fp);

		# INSERT PAGE VIEW ACCESS
		if(empty($update)){
			$text_body	.= $device.",".$os.",".$directory.",".$page.",".$count."\n";
		}

		# INSERT VISITOR ACCESS
		if(!empty($session) && empty($visitor)){
			$text_body	.= $device.",".$os.",0,0,".$count."\n";
			$visitor	 = 1;
		}

		# PAGE VIEW WRITE
		$fp	= fopen($pageviewlogfile_path, "w+");
		if($fp == FALSE){
			return FALSE;
		}

		$write_content	= stripslashes($text_body);
		$fputs			= fputs ($fp,$write_content);
		fclose($fp);

		# INSERT VISITOR LOG
		if(!empty($visitor) && empty($visitor_write)){

			$visitors	 = $user.",".$device.",".$os."\n";

			# WRITE
			$fp	= fopen($visitorlogfile_path, "a");
			if($fp == FALSE){
				return FALSE;
			}

			$visitor_content	= stripslashes($visitors);
			fwrite($fp,$visitor_content);
			fclose($fp);

		}

		# INSERT ACCESS LOG
		if(!empty($visitor)){

			$accesses	 = $user.",".date("YmdHis").",".$device.",".$os."\n";

			# WRITE
			$fp	= fopen($accesslogfile_path, "a");
			if($fp == FALSE){
				return FALSE;
			}

			$access_content	= stripslashes($accesses);
			fwrite($fp,$access_content);
			fclose($fp);

		}

		return TRUE;

	}



	/**************************************************
	**
	**	writeAccessLogByApp
	**	----------------------------------------------
	**	アクセスカウンター アプリ用
	**
	**************************************************/

	public function writeAccessLogByApp($site_id,$user,$device,$os){

		if(empty($site_id) || empty($user)){
			return FALSE;
		}

		if(!is_numeric($site_id)){
			return FALSE;
		}


		/**************************************************
		**
		**	ACCESS
		**
		**************************************************/

		# ACCESS LOG DIRECTORY COPY
		if(!file_exists(DOCUMENT_ROOT_ACCESSLOG.$site_id)){
			$cpCommandLog	= "cp -a ".DOCUMENT_ROOT_ACCESSLOG."default ".DOCUMENT_ROOT_ACCESSLOG.$site_id;
			exec($cpCommandLog,$output, $return_var);
		}

		# ACCESS LOG FILE
		$accesslogfile_path			= DOCUMENT_ROOT_ACCESSLOG.$site_id."/".date("Ymd").".txt";

		# ACCESS LOG FILE CHECK
		if(!file_exists($accesslogfile_path)){
			$create = fopen($accesslogfile_path,'w');
			if ($create == FALSE) { return FALSE; }
			fclose($create);
		}

		# INSERT VISITOR ACCESS
		$accesses	 = $user.",".date("YmdHis").",".$device.",".$os."\n";

		# WRITE
		$fp	= fopen($accesslogfile_path, "a");
		if($fp == FALSE){
			return FALSE;
		}

		$access_content	= stripslashes($accesses);
		fwrite($fp,$access_content);
		fclose($fp);

		return TRUE;

	}



	/**************************************************
	**
	**	writeRegistLog
	**	----------------------------------------------
	**	登録カウンター
	**
	**************************************************/

	public function writeRegistLog($site_id,$device,$os){

		if(empty($site_id) || empty($device)){
			return FALSE;
		}

		if(!is_numeric($site_id)){
			return FALSE;
		}

		/**************************************************
		**
		**	REGIST COUNT
		**
		**************************************************/

		# REGIST LOG DIRECTORY COPY
		if(!file_exists(DOCUMENT_ROOT_REGISTLOG.$site_id)){
			$cpCommandLog2		= "cp -a ".DOCUMENT_ROOT_REGISTLOG."default ".DOCUMENT_ROOT_REGISTLOG.$site_id;
			exec($cpCommandLog2,$output, $return_var);
		}

		# PAGE VIEW LOG FILE
		$registlogfile_path		= DOCUMENT_ROOT_REGISTLOG.$site_id."/".date("Ymd").".txt";

		# PAGE VIEW LOG FILE CHECK
		if(!file_exists($registlogfile_path)){
			$create = fopen($registlogfile_path,'w');
			if ($create == FALSE) { return FALSE; }
			fclose($create);
		}

		# TEXT BODY
		$count		= 1;
		$total		= 0;
		$line		= NULL;
		$text_body	= NULL;
		$log_line	= NULL;
		$update		= NULL;

		# FILE OPEN
		$fp = fopen($registlogfile_path,'r');
		if ($fp == FALSE) { return FALSE; }
		while (!feof($fp)) {

			$line		 = fgets($fp);
			if(empty($line)){ break; }

			$log_content    	= explode(",",$line);
			$device_log			= $log_content[0];
			$os_log				= $log_content[1];
			$count_log			= $log_content[2];

			if(preg_match("/\n/",$count_log)){
				$count_log		= str_replace("\n","",$count_log);
			}

			# ACCESS
			if($device_log == $device && $os_log == $os){
				$count_log		+= $count;
				$update			 = 1;
			}

			$log_line			 = $device_log.",".$os_log.",".$count_log."\n";

			$text_body			.= $log_line;

			# TOTAL COUNT
			$total				+= $count_log;


		}
		fclose($fp);

		# INSERT REGIST COUNT
		if(empty($update)){
			$text_body	.= $device.",".$os.",".$count."\n";
		}

		# REGIST COUNT WRITE
		$fp	= fopen($registlogfile_path, "w+");
		if($fp == FALSE){
			return FALSE;
		}

		$write_content	= stripslashes($text_body);
		$fputs			= fputs ($fp,$write_content);
		fclose($fp);

		return TRUE;

	}




	/**************************************************
	**
	**	setInitialization
	**	----------------------------------------------
	**	POST DATA MAKE OR NULL
	**
	**************************************************/

	public function setInitialization($default_data,$post_data=NULL){

		# ERROR
		if(empty($default_data)){
			return FALSE;
		}

		# 初期化
		$result		= array();

		# MAKE
		foreach($default_data as $value){
			if(!empty($post_data)){
				$result[$value]	= isset($post_data[$value]) ? $post_data[$value] : NULL;
			}else{
				$result[$value]	= NULL;
			}
		}

		return	$result;

	}



	/************************************************
	**
	**	rewritePostValue
	**	---------------------------------------------
	**	$_POST VALUE 書き換え
	**
	************************************************/

	public function rewritePostValue($data,$default=NULL){

		if(isset($data)){
			$result	= $data;
		}else{
			$result	= $default;
		}

		return $result;

	}



	/************************************************
	**
	**	checkPreviewData
	**	---------------------------------------------
	**	PREVIEW DATA CHECK
	**
	************************************************/

	public function checkPreviewData($data){

		$result['preview']	= $data['preview'];

		# DEVICE - VIEW ID
		if(!empty($data['d'])){

			# VIEW ID
			if(preg_match("/-/",$data['d'])){

				$views		= explode("-",$data['d']);

				# 認証の為ここはベタ
				if($views[0] == "p"){
					$result['device']	= "pc";
				}elseif($views[0] == "s"){
					$result['device']	= "smart";
				}elseif($views[0] == "m"){
					$result['device']	= "mobile";
				}

				$result['view_id']		= $views[1];

			# NO DESIGN
			}else{

				# 認証の為ここはベタ
				if($data['d'] == "p"){
					$result['device']	= "pc";
				}elseif($data['d'] == "s"){
					$result['device']	= "smart";
				}elseif($data['d'] == "m"){
					$result['device']	= "mobile";
				}

				$result['view_id']		= NULL;

			}

			$result['d']			= $data['d'];

		}else{
			$result['device']	= "pc";
		}

		return $result;

	}



	/************************************************
	**
	**	checkCacheOn
	**	---------------------------------------------
	**	CACHE CHECK
	**
	************************************************/

	public function checkCacheOn($preview,$cache_check_directory,$cache_check_page,$purpose=NULL,$return=NULL){

		if(empty($preview) && empty($cache_check_directory) && empty($cache_check_page) && !defined("SYSTEM_CHECK") && CACHE_SYSTEM == "ON" && empty($purpose) && empty($return)){
		#if(empty($preview) && empty($cache_check_directory) && empty($cache_check_page) && CACHE_SYSTEM == "ON" && empty($purpose) && empty($return)){
			return TRUE;
		}else{
			return FALSE;
		}

	}



	/**************************************************
	**
	**	getMicroTime
	**	----------------------------------------------
	**	TIME CALCRATION
	**
	**************************************************/

	public function getMicroEndTime($start_time,$start_secd){

		if(!defined("SYSTEM_CHECK")){
			return FALSE;
		}

		if(DEBUG_MODE !== "ON"){
			return FALSE;
		}

		# QUERY END TIME
		$end_time	= microtime();
		$end_secd	= date("i");

		# TIME CALCURATION
		$time	= $this->getMicroTime($end_time) - $this->getMicroTime($start_time);
		$time	= $time + ( $end_secd - $start_secd );

		$result	= round( $time , 5 );

		return $result;

	}



	/**************************************************
	**
	**	getMicroTime
	**	----------------------------------------------
	**	TIME
	**
	**************************************************/

	public function getMicroTime($time){

		list($usec, $sec) = explode(" ",$time);
		return ((float)$sec + (float)$usec);

	}



	/**************************************************
	**
	**	getStartTime
	**	----------------------------------------------
	**	TIME
	**
	**************************************************/

	public function getStartTime(){

		if(!defined("SYSTEM_CHECK")){
			return FALSE;
		}

		if(DEBUG_MODE !== "ON"){
			return FALSE;
		}

		$result['time']	= microtime();
		$result['secd']	= date("i");

		return $result;

	}



	/*********************************************
	**
	**	outputError
	**	-----------------------------------------
	**	ERROR 出力
	**
	*********************************************/

	public function outputError($str){

		if(DEBUG_MODE !== "ON"){
			return FALSE;
		}

		# ERROR CONTENTS
		if(defined("SYSTEM_CHECK")){
			print("<div id=\"end_contents\">\n");
			print("<p>ERROR！！</p>\n");
			print("<div>\n");
			print($str."<br />\n");
			print("</div>\n");
			print("</div>\n\n");
		}

	}



	/************************************************
	**
	**	debug
	**	---------------------------------------------
	**	指定IPのみのデバッグ関数
	**
	************************************************/

	public function debug($data,$title=NULL){

		if(DEBUG_MODE !== "ON"){
			return FALSE;
		}

		if(defined("SYSTEM_CHECK")){

			if(empty($title)){
				$title		 = "SYSTEM DEBUG";
			}

			$this->debugline	.= "<div class=\"system_debug\">\n";
			$this->debugline	.= "<div class=\"system_debug_title\">".$title."</div>\n";
			$this->debugline	.= "<div class=\"system_debug_contents\">\n";
			$this->debugline	.= "<pre>\n";
			$this->debugline	.= print_r($data,TRUE);
			$this->debugline	.= "</pre>\n";
			$this->debugline	.= "<hr class=\"query_line\" />\n";
			$this->debugline	.= "<div style=\"color: #333333; font-size: 10px; text-align: right;\">BY SYSTEM</div>\n";
			$this->debugline	.= "</div>\n";
			$this->debugline	.= "</div>\n\n";

		}

	}



	/************************************************
	**
	**	debugSystem
	**	---------------------------------------------
	**	指定IPのみのデバッグ関数
	**
	************************************************/

	public function debugSystem($str){

		if(DEBUG_MODE !== "ON"){
			return FALSE;
		}

		if(defined("SYSTEM_CHECK")){
			print($str);
		}

	}



	/**************************************************
	**
	**	outputDebugSystem
	**	----------------------------------------------
	**	指定IPのみのデバッグ関数
	**
	**************************************************/

	public function outputDebugSystem(){

		if(DEBUG_MODE !== "ON"){
			return FALSE;
		}

		if(defined("SYSTEM_CHECK")){

			global $database;

			# SQL
			if(!empty($database->debug_query)){
				print("<div class=\"system_debug\">\n");
				print("<div class=\"system_debug_title\">SQL CHECK</div>\n");
				print("<div class=\"system_debug_contents\">\n");
				if(!empty($database->debug_query)){
				print($database->debug_query);
				}
				print("<div style=\"color: #333333; font-size: 10px; text-align: right;\">BY SYSTEM</div>\n");
				print("</div>\n");
				print("</div>\n\n");
			}

			# HEADER
			/*
			print("<div class=\"system_debug\">\n");
			print("<div class=\"system_debug_title\">RESPONSE HEADERS</div>\n");
			print("<div class=\"system_debug_contents\">\n");
			print("<div style=\"color: #FF0000;\">\n");
			print("<pre>\n");
			print_r(apache_response_headers());
			print("</pre>\n");
			print("</div>\n");
			print("<hr class=\"query_line\" />\n");
			print("<div style=\"color: #333333; font-size: 10px; text-align: right;\">BY SYSTEM</div>\n");
			print("</div>\n");
			print("</div>\n\n");
			*/

			# DEBUG DATA
			if(!empty($this->debugline)){
				echo $this->debugline;
			}

		}

	}



	/**************************************************
	**
	**	basicCertify
	**	----------------------------------------------
	**	ベーシック認証
	**
	**************************************************/

	public function basicCertify(){

		if(empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])){

			header('WWW-Authenticate: Basic realm="Administrator autherntication area."');
			header('HTTP/1.0 401 Unauthorized');
			exit();

		}else{

			if($_SERVER['PHP_AUTH_USER'] == TEST_USER && $_SERVER['PHP_AUTH_PW'] == TEST_PASS){

				return TRUE;

			}else{

				header('WWW-Authenticate: Basic realm="Administrator autherntication area."');
				header('HTTP/1.0 401 Unauthorized');

				return FALSE;

			}

		}

	}


}

?>