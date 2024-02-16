<?php
/********************************************************************************
**	
**	Database.php
**	=============================================================================
**
**	■PAGE / 
**	DATABASE MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	DATABASE 接続CLASS 
**	ROOT内共通
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

/************************************************
**
**	PLUGIN FILE
**	---------------------------------------------
**	HTML TAG SPRIT & ALLOW
**	必須項目
**
************************************************/
namespace Libs;
use PDO;
use Exception;
require_once(DOCUMENT_SYSTEM_PLUGINS."/Kses/kses.php");

/*********************************************************************************/

class Database{
	private static $connect;
	private	static $result;
	private	static $freeresult;
	private static $transaction;
	private	static $multi_sql;
	private	static $multi_data;
	private	static $multi_array;
	private	static $multi_where;
	private	static $multi_check;
	private	static $multi_type;
	private static $multi_count;
	private	static $query_start_time;
	private	static $query_start_secd;
	private	static $while_start_time;
	private	static $while_start_secd;
	private	static $debug_level;
	public static $debug_query;
	public static $debuq_sql;
	public static $debug_array;
	public static $while_query;
	public static $while_sql;
	public static $while_array;
	public static $database;
	public static $authority;

	// Connect to database
	public static function connectDb($grant=NULL) {
		if(DEBUG_MODE == "ON"){
			if(DEBUG_SQL == "ALL" || DEBUG_SQL == "NORMAL"){
				self::$debug_level	= 1;
			}
		}

		if($grant === MASTER_ACCESS_KEY){
			$db_ip				= DATABASE_IP;
			$db_user			= DATABASE_USER;
			$db_pass			= DATABASE_PASS;
			$db_name			= DATABASE_NAME;
			self::$debug_query .= "【DATABASE SELECT】&nbsp;→&nbsp;<span style=\"color: #00CCFF;\">MASTER</span>&nbsp;&nbsp;:&nbsp;&nbsp;<span style=\"color: #FFFFFF;\">{$db_ip}</span>";
			self::$debug_query .= "\n<hr class=\"query_line\" />\n";
			self::$authority	= 1;
		}else{
			$db_ip				= DATABASE_IP_S;
			$db_user			= DATABASE_USER_S;
			$db_pass			= DATABASE_PASS_S;
			$db_name			= DATABASE_NAME_S;
			self::$debug_query .= "【DATABASE SELECT】&nbsp;→&nbsp;<span style=\"color: #FF0000;\">SLAVE</span>&nbsp;&nbsp;:&nbsp;&nbsp;<span style=\"color: #777777;\">{$db_ip}</span>";
			self::$debug_query .= "\n<hr class=\"query_line\" />\n";
			self::$authority	= NULL;
		}

		try {
			self::$database = new PDO("mysql:host={$db_ip};dbname={$db_name};charset=utf8",$db_user,$db_pass,
			[PDO::ATTR_EMULATE_PREPARES => false]);
		} catch (PDOException $e) {
			self::$database	= NULL;
			return ("DB_ERROR:<br />Can not connect {$db_ip}<br />".mysqli_error());
		}

		return true;
	}

	// Check connect to master
	public static function checkAuthority(){
		return	self::$authority;
	}

	// Close to database connection
	public static function closeDb() {
		self::$database	= NULL;
	}

	/**************************************************
	**
	**	closeStmt
	**	----------------------------------------------
	**	DATABASE STMT CLOSE
	**
	**************************************************/

	public static function closeStmt() {
		if(!empty(self::$freeresult)){
			$count	= count(self::$freeresult);
			if($count > 0){
				for($i=0;$i<$count;$i++){
					self::$freeresult[$i]->closeCursor();
					self::$freeresult[$i]	= NULL;
					unset(self::$freeresult[$i]);
				}
			}
		}
	}



	/**************************************************
	**
	**	destructStmt
	**	----------------------------------------------
	**	DATABASE STMT DESTRUCT
	**
	**************************************************/

	public static function destructStmt($stmt) {
		self::$freeresult[]	= $stmt;
	}



	/**************************************************
	**
	**	errorDb
	**	----------------------------------------------
	**	DATABASE MYSQL ERROR
	**
	**************************************************/

	public static function errorDb($sql=NULL,$errno,$filename,$line){

		$error	= NULL;

		if($errno > 0){

			if(defined("SYSTEM_CHECK")){

				# DISPLAY
				$error .= "SYSTEM ERROR<br />\n";
				
				$error .= "<div id=\"db_error\">\n";
				$error .= "<hr />\n";
				$error .= "<span>DB_ERROR</span><br />".$errno.":".mysqli_error()."<hr />\n";
				if($sql != ''){
				$error .= "<span>QUERY</span><br />".$sql."<hr />\n";
				}
				$error .= "<span>FILENAME</span><br />".$filename."<hr />\n";
				$error .= "<span>LINE</span><br />".$line."\n<hr />\n</div>\n";

			}

			# MAIL
			$body	 = "[TIME]\n";
			$body	.= date("Y/m/d H:i:s")."\n\n";
			$body	.= "[DB ERROR]\n";
			$body	.= $errno."\n";
			$body	.= mysqli_error()."\n\n";
			$body	.= "[QUERY]\n";
			$body	.= $sql."\n\n";
			$body	.= "[FILENAME]\n";
			$body	.= $filename."\n\n";
			$body	.= "[LINE]\n";
			$body	.= $line."\n\n";
			$body	.= "[SERVER]\n";
			$body	.= $_SERVER['SERVER_NAME']."\n\n";
			$body	.= "[CLIENT]\n";
			$body	.= $_SERVER['REMOTE_ADDR']."\n\n";

			mail(MAIL_SYSTEM,"APP SYSTEM ERROR [".SITE_NAME."]",$body,"From:".MAIL_INFO);

		}

		return $error;

	}



	/**************************************************
	**
	**	freeResult
	**	----------------------------------------------
	**	DATABASE MYSQL FREE RESULT
	**
	**************************************************/

	public static function freeResult($result,$debug=NULL){

		if(defined("SYSTEM_CHECK")){

			if(self::$debug_level == 1){

				# QUERY END TIME
				if(!empty($debug)){
					$debug_time		= self::getWhileEndTime();
				}else{
					$debug_time		= self::getQueryEndTime();
				}

				# QUERY OUTPUT
				if(self::$debug_level == 1){
					
					if(!empty($debug)){
						self::$debug_query		.= self::$while_sql."<br />\n";
						if(!empty(self::$while_array)){
							ob_start();
							var_dump(self::$while_array);
							$buffer				 = ob_get_contents();
							ob_end_clean();
							self::$debug_query	.= $buffer."<br />\n";
						}
						self::$debug_query		.= "(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
					}else{
						self::$debug_query		.= self::$debuq_sql."<br />\n";
						if(!empty(self::$debug_array)){
							ob_start();
							var_dump(self::$debug_array);
							$buffer				 = ob_get_contents();
							ob_end_clean();
							self::$debug_query	.= $buffer."<br />\n";
						}
						self::$debug_query		.= "(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
					}


				}

			}

		}

		return FALSE;

	}



	/**************************************************
	**
	**	selectDb
	**	----------------------------------------------
	**	DATABASE MYSQL SELECT / PDO
	**
	**************************************************/

	public static function selectDb($table,$column,$where=NULL,$array=NULL,$order=NULL,$limit=NULL,$group=NULL,$debug=NULL){

		$sql  = "SELECT {$column} FROM {$table} ";
		if(!empty($where)){ $sql .= "WHERE {$where} "; }
		if(!empty($group)){ $sql .= "GROUP BY {$group} "; }
		if(!empty($order)){ $sql .= "ORDER BY {$order} "; }
		if(!empty($limit)){ $sql .= "LIMIT {$limit}"; }

		$stmt						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = self::removeTags($array);

		if(DEBUG_SQL == "ALL"){
			$check_array			= NULL;
			foreach($array as $check_key => $check_value){
				$check_array		.= "[{$check_key}] => ".$check_value."&nbsp;";
			}
			self::$debug_query		.= "<span style=\"color: #666666;\"> CHECK : ".$sql."<br />".$check_array."</span>\n<hr class=\"query_line\" />\n";
		}

		try{

			# PREPARE
			$stmt					 = self::prepare($sql,$array,$debug);
	 		$result					 = $stmt->execute($array);
			if(empty($result)){ throw new Exception(); }

		}catch(Exception $e){

			if(defined("SYSTEM_CHECK")){
				print("<pre>");
				print_r($e->getTrace());
				print("</pre>");
			}

			return FALSE;

		}

		return $stmt;

	}



	/**************************************************
	**
	**	insertDb
	**	----------------------------------------------
	**	DATABASE MYSQL INSERT
	**
	**************************************************/

	public static function insertDb($table,$data){

		# 初期化
		$insert_column		 = NULL;
		$insert_object		 = NULL;

		foreach( $data as $column => $value ){
			$insert_column	.= $column.",";
			$insert_object	.= ":".$column.",";
			$check[]		 = $value;
		}

		$insert_column		 = substr_replace($insert_column,'', -1,1);
		$insert_object		 = substr_replace($insert_object,'', -1,1);

		# SQL
		$sql				 = "INSERT INTO ".$table." (".$insert_column.") VALUES (".$insert_object.")";

		if(DEBUG_SQL == "ALL"){
			self::$debug_query	.= "<span style=\"color: #666666;\"> CHECK : ".$sql."</span>\n<hr class=\"query_line\" />\n";
		}

		$insert_id					 = NULL;

		try{

			# PREPARE
			$stmt					 = self::prepare($sql);

			# ERROR
			if(empty($stmt)){
				return FALSE;
			}

			$i=0;
			foreach( $data as $column => $value ){
				#print($column."----".$check[$i]."<br />");
				$stmt->bindParam(":".$column."", $check[$i], PDO::PARAM_STR);
				$i++;
			}

	 		$result					 = $stmt->execute();
			if(empty($result)){ throw new Exception(); }

			$insert_id				 = self::$database->lastInsertId();

		}catch(Exception $e){

			if(defined("SYSTEM_CHECK")){
				print("<pre>");
				print_r($e->getTrace());
				print("</pre>");
			}

			self::rollBack();

			return FALSE;

		}

		self::destructStmt($stmt);

		return $insert_id;

	}



	/**************************************************
	**
	**	bulkInsertDb
	**	----------------------------------------------
	**	DATABASE MYSQL BULK INSERT
	**
	**************************************************/

	public static function bulkInsertDb($table,$column,$data){

		if(empty($table) || empty($column) || empty($data)){
			return FALSE;
		}

		# 初期化
		$insert_obj		 = NULL;

		$insert_obj		 = substr_replace($data,'', -1,1);

		$sql			 = "INSERT INTO ".$table." (".$column.") VALUES ";
		$sql			.= $insert_obj;

		$result			 = self::query($sql);

		return $insert_id;

	}



	/**************************************************
	**
	**	updateDb
	**	----------------------------------------------
	**	DATABASE MYSQL UPDATE
	**
	**************************************************/

	public static function updateDb($table,$data,$where,$array=NULL){

		if(empty($table) || empty($data) || empty($where) || empty($array)){
			return FALSE;
		}

		# 初期化
		$update_column			 = NULL;
		$update_where			 = NULL;
		$column					 = NULL;
		$value					 = NULL;

		# REMOVE TAGS
		$data					 = self::removeTags($data);

		foreach( $data as $column => $value ){
			$update_column		.= $column." = ";
			$update_column		.= ":".$column.",";
			$check[]			 = $value;
		}

		$column					 = NULL;
		$value					 = NULL;
		foreach( $array as $column => $value ){
			$update_where[]		 = $value;
		}

		$update_column	 		 = substr_replace($update_column,'', -1,1);

		# SQL
		$sql					 = "UPDATE ".$table." SET ".$update_column." ";
		$sql					.= "WHERE ".$where;

		if(DEBUG_SQL == "ALL"){
			$check_array			= NULL;
			foreach($array as $check_key => $check_value){
				$check_array		.= "[".$check_key."] => ".$check_value."&nbsp;";
			}
			self::$debug_query		.= "<span style=\"color: #666666;\"> CHECK : ".$sql."<br />".$check_array."</span>\n<hr class=\"query_line\" />\n";
		}


		try{

			# PREPARE
			$stmt					 = self::prepare($sql,$array);

			# ERROR
			if(empty($stmt)){
				return FALSE;
			}

			$column					 = NULL;
			$value					 = NULL;
			$i=0;
			foreach( $data as $column => $value ){
				$stmt->bindParam(":".$column."", $check[$i], PDO::PARAM_STR);
				$i++;
			}

			$column					 = NULL;
			$value					 = NULL;
			$i=0;
			foreach( $array as $column => $value ){
				$stmt->bindParam($column."", $update_where[$i], PDO::PARAM_STR);
				$i++;
			}

			$result					 = $stmt->execute();
			if(empty($result)){ throw new Exception(); }

		}catch(Exception $e){

			if(defined("SYSTEM_CHECK")){
				print("<pre>");
				print_r($e->getTrace());
				print("</pre>");
			}

			self::rollBack();

			return FALSE;

		}

		self::destructStmt($stmt);

		return TRUE;

	}



	/**************************************************
	**
	**	deleteDb
	**	----------------------------------------------
	**	DATABASE MYSQL DELETE
	**
	**************************************************/

	public static function deleteDb($table,$where,$limit=NULL){

		if(!defined("SYSTEM_CHECK")){
			return FALSE;
		}

		$sql  = "DELETE FROM ".$table." WHERE ".$where." ";
		if(!empty($limit)){ $sql .= "LIMIT ".$limit; }

		$result = self::query($sql);

		return TRUE;

	}



	/**************************************************
	**
	**	showDb
	**	----------------------------------------------
	**	DATABASE MYSQL SHOW TABLE
	**
	**************************************************/

	public static function showDb($table){

		$field_array	= array();

		$sql			= "SHOW fields FROM ".$table."";
		$result			= self::query($sql);

		$error			= self::errorDb("SHOW FIELDS : ARRANGE DATA",$result->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ FALSE; }

		while($fields_data = self::fetchAssoc($result)){
			foreach($fields_data as $key => $fields){
				$field_array[$key][] = $fields;
			}
		}

		return $field_array;

	}



	/**************************************************
	**
	**	query
	**	----------------------------------------------
	**	DATABASE MYSQL QUERY
	**
	**************************************************/

	public static function query($sql,$debug=NULL){

		# QUERY START TIME
		if(defined("SYSTEM_CHECK")){
			if(self::$debug_level == 1){
				if(!empty($debug)){
					self::getWhileStartTime();
				}else{
					self::getQueryStartTime();
				}
			}
		}

		# ERROR チェック
		$valifation	= self::checkSqlStrings($sql);
		if(empty($valifation)){
			exit();
		}

		$sql	.= ";";

		$stmt	 = self::$database->query($sql);

		# QUERY OUTPUT
		if(defined("SYSTEM_CHECK")){
			if(self::$debug_level == 1){
				if(preg_match("/SELECT/",$sql)){
					if(!empty($debug)){
						self::$while_sql	 = $sql;
					}else{
						self::$debuq_sql	 = $sql;
					}
				}else{
					$debug_time				 = self::getQueryEndTime();
					self::$debug_query		.= $sql."<br />\n(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
				}
			}
		}

		return $stmt;

	}


	/**************************************************
	**
	**	prepare
	**	----------------------------------------------
	**	DATABASE PDO QUERY
	**
	**************************************************/

	public static function prepare($sql,$array=NULL,$debug=NULL){

		# QUERY START TIME
		if(defined("SYSTEM_CHECK")){
			if(self::$debug_level == 1){
				if(!empty($debug)){
					self::getWhileStartTime();
				}else{
					self::getQueryStartTime();
				}
			}
		}

		# ERROR チェック
		$valifation	= self::checkSqlStrings($sql);
		if(empty($valifation)){
			exit();
		}


		try{

			# PREPARE
			$stmt = self::$database->prepare($sql);
			if(empty($stmt)){ throw new Exception(); }

		}catch(Exception $e){

			if(defined("SYSTEM_CHECK")){
				print("<pre>");
				print_r($e->getTrace());
				print("</pre>");
			}

			self::rollBack();

			return FALSE;

		}

		# QUERY OUTPUT
		if(defined("SYSTEM_CHECK")){
			if(self::$debug_level == 1){
				if(preg_match("/SELECT/",$sql)){
					if(!empty($debug)){
						self::$while_sql	 = $sql;
						self::$while_array	 = $array;
					}else{
						self::$debuq_sql	 = $sql;
						self::$debug_array	 = $array;
					}
				}else{
					$debug_time				 = self::getQueryEndTime();
					self::$debug_query		.= $sql."<br />\n";
					if(!empty($array)){
						ob_start();
						var_dump($array);
						$buffer				 = ob_get_contents();
						ob_end_clean();
						self::$debug_query	.= $buffer."<br />\n";
					}
					self::$debug_query		.= "(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
				}
			}
		}

		return $stmt;

	}



	/**************************************************
	**
	**	multiInsertDb
	**	----------------------------------------------
	**	DATABASE MYSQL INSERT
	**	----------------------------------------------
	**	複数SQLを一気にINSERT
	**	トランザクション処理
	**
	**************************************************/

	public static function multiInsertDb($table,$data){

		# 初期化
		$insert_column		 = NULL;
		$insert_object		 = NULL;

		foreach( $data as $column => $value ){
			$insert_column	.= $column.",";
			$insert_object	.= ":".$column.",";
			$check[]		 = $value;
		}

		$insert_column		 = substr_replace($insert_column,'', -1,1);
		$insert_object		 = substr_replace($insert_object,'', -1,1);

		# SQL
		$sql				 = "INSERT INTO ".$table." (".$insert_column.") VALUES (".$insert_object.")";

		if(DEBUG_SQL == "ALL"){
			self::$debug_query	.= "<span style=\"color: #666666;\"> CHECK : ".$sql."</span>\n<hr class=\"query_line\" />\n";
		}

		self::$multi_sql[self::$multi_count]		= $sql;
		self::$multi_data[self::$multi_count]		= $data;
		self::$multi_array[self::$multi_count]		= NULL;
		self::$multi_where[self::$multi_count]		= NULL;
		self::$multi_check[self::$multi_count]		= $check;
		self::$multi_type[self::$multi_count]		= 1;

		self::$multi_count++;

	}



	/**************************************************
	**
	**	multiUpdateDb
	**	----------------------------------------------
	**	DATABASE MYSQL UPDATE
	**	----------------------------------------------
	**	複数SQLを一気にUPDATE
	**	トランザクション処理
	**
	**************************************************/

	public static function multiUpdateDb($table,$data,$where,$array=NULL){

		if(empty($table) || empty($data) || empty($where) || empty($array)){
			return FALSE;
		}

		# 初期化
		$update_column			 = NULL;
		$update_where			 = NULL;
		$column					 = NULL;
		$value					 = NULL;

		# REMOVE TAGS
		$data					 = self::removeTags($data);

		foreach( $data as $column => $value ){
			$update_column		.= $column." = ";
			$update_column		.= ":".$column.",";
			$check[]			 = $value;
		}

		$column					 = NULL;
		$value					 = NULL;
		foreach( $array as $column => $value ){
			$update_where[]		 = $value;
		}

		$update_column	 		 = substr_replace($update_column,'', -1,1);

		# SQL
		$sql					 = "UPDATE ".$table." SET ".$update_column." ";
		$sql					.= "WHERE ".$where;

		if(DEBUG_SQL == "ALL"){
			$check_array			= NULL;
			foreach($array as $check_key => $check_value){
				$check_array		.= "[".$check_key."] => ".$check_value."&nbsp;";
			}
			self::$debug_query		.= "<span style=\"color: #666666;\"> CHECK : ".$sql."<br />".$check_array."</span>\n<hr class=\"query_line\" />\n";
		}

		self::$multi_sql[self::$multi_count]		= $sql;
		self::$multi_data[self::$multi_count]		= $data;
		self::$multi_array[self::$multi_count]		= $array;
		self::$multi_where[self::$multi_count]		= $update_where;
		self::$multi_check[self::$multi_count]		= $check;
		self::$multi_type[self::$multi_count]		= 2;

		self::$multi_count++;

	}



	/**************************************************
	**
	**	multiQueryDb
	**	----------------------------------------------
	**	DATABASE MYSQL QUERY
	**	----------------------------------------------
	**	直接クエリ処理
	**	トランザクション処理
	**
	**************************************************/

	public static function multiQueryDb($sql){

		self::$multi_sql[self::$multi_count]		= $sql;
		self::$multi_data[self::$multi_count]		= NULL;
		self::$multi_array[self::$multi_count]		= NULL;
		self::$multi_where[self::$multi_count]		= NULL;
		self::$multi_check[self::$multi_count]		= NULL;
		self::$multi_type[self::$multi_count]		= 3;

		self::$multi_count++;

	}



	/**************************************************
	**
	**	multiExection
	**	----------------------------------------------
	**	DATABASE MYSQL EXECTION
	**	----------------------------------------------
	**	複数SQLを一気に処理
	**	トランザクション / ロールバック処理
	**
	**************************************************/

	public static function multiExection(){

		$error			= NULL;

		if(self::$multi_count > 0){

			# AUTHORITY
			$db_auth			 = self::checkAuthority();
			$db_check			 = NULL;

			# DATABASE CHANGE
			if(empty($db_auth)){

				# CLOSE DATABASE SLAVE
				self::closeDb();

				# CONNECT DATABASE MASTER
				self::connectDb(MASTER_ACCESS_KEY);

				$db_check		 = 1;

			}

			# TRY
			try{

				# ROLLBACK START
				self::$database->beginTransaction();

				# LOOP
				for($i=0;$i<self::$multi_count;$i++){

					# INSERT
					if(!empty(self::$multi_type[$i]) && self::$multi_type[$i] == 1){

						if(!empty(self::$multi_sql[$i]) && !empty(self::$multi_data[$i])){

							# PREPARE
							$stmt					 = self::prepare(self::$multi_sql[$i]);

							# ERROR
							if(empty($stmt)){
								$error				 = 1;
								break;
							}

							$column					 = NULL;
							$value					 = NULL;
							$j=0;
							foreach( self::$multi_data[$i] as $column => $value ){
								$stmt->bindParam(":".$column."", self::$multi_check[$i][$j], PDO::PARAM_STR);
								$j++;
							}

					 		$result					 = $stmt->execute();
							if(empty($result)){ throw new Exception(); }

							$stmt->closeCursor();

						}

					# UPDATE
					}elseif(!empty(self::$multi_type[$i]) && self::$multi_type[$i] == 2){

						if(!empty(self::$multi_sql[$i]) && !empty(self::$multi_data[$i]) && !empty(self::$multi_array[$i])){

							# PREPARE
							$stmt					 = self::prepare(self::$multi_sql[$i],self::$multi_array[$i]);

							# ERROR
							if(empty($stmt)){
								$error				 = 1;
								break;
							}

							$column					 = NULL;
							$value					 = NULL;
							$j=0;
							foreach( self::$multi_data[$i] as $column => $value ){
								$stmt->bindParam(":".$column."", self::$multi_check[$i][$j], PDO::PARAM_STR);
								$j++;
							}

							$column					 = NULL;
							$value					 = NULL;
							$j=0;
							foreach( self::$multi_array[$i] as $column => $value ){
								$stmt->bindParam($column."", self::$multi_where[$i][$j], PDO::PARAM_STR);
								$j++;
							}

							$result					 = $stmt->execute();
							if(empty($result)){ throw new Exception(); }


						}


					# QUERY
					}elseif(!empty(self::$multi_type[$i]) && self::$multi_type[$i] == 3){

						if(!empty(self::$multi_sql[$i])){

							$stmt	 = self::$database->query(self::$multi_sql[$i]);
							$stmt->closeCursor();

						}

					}

				}

				# COMMIT
				if(empty($error)){
					self::$database->commit();
				}

			# CATCH ERROR
			}catch(PDOException $e){

				if(defined("SYSTEM_CHECK")){
					print("<pre>");
					print_r($e->getTrace());
					print($e->getMessage());
					print("</pre>");
				}

				# ROLLBACK
				self::$database->rollBack();

				mail(MAIL_SYSTEM,"SQL MULTI EXECTION ERROR",$e->getMessage(),"From:".MAIL_INFO);

				$error					= 1;

			}


			# DATABASE CHANGE
			if(!empty($db_check)){

				# CLOSE DATABASE MASTER
				self::closeDb();

				# CONNECT DATABASE SLAVE
				self::connectDb();

			}


		}

		# RETURN
		if(empty($error)){
			return TRUE;
		}else{
			return FALSE;
		}

	}



	/**************************************************
	**
	**	fetchObject
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH OBJECT
	**
	**************************************************/

	public static function fetchObject($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetch(PDO::FETCH_OBJ);
		self::destructStmt($stmt);
		return $data;

	}



	/**************************************************
	**
	**	fetchAssoc
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ASSOC
	**
	**************************************************/

	public static function fetchAssoc($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		self::destructStmt($stmt);
		return $data;

	}



	/**************************************************
	**
	**	fetchArray
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ARRAY
	**
	**************************************************/

	public static function fetchArray($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetch(PDO::FETCH_NUM);
		self::destructStmt($stmt);
		return $data;

	}

	/**************************************************
	**
	**	fetchAll
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ALL
	**
	**************************************************/

	public static function fetchAll($stmt){
		if(empty($stmt)){
			return false;
		}

		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		self::destructStmt($stmt);
		return $data;
	}

	/**************************************************
	**
	**	fetchAll
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ALL
	**
	**************************************************/

	public static function fetchAllByColumn($stmt){
		if(empty($stmt)){
			return false;
		}

		$data = $stmt->fetchAll(PDO::FETCH_COLUMN);
		self::destructStmt($stmt);
		return $data;
	}

	public static function fetchAllByUnique($stmt){
		if(empty($stmt)){
			return false;
		}

		$data = $stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);
		self::destructStmt($stmt);
		return $data;
	}


	
	/**************************************************
	**
	**	numRows
	**	----------------------------------------------
	**	DATABASE MYSQL NUM ROWS
	**
	**************************************************/

	public static function numRows($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data	= $stmt->rowCount();
		self::destructStmt($stmt);
		return $data;

	}



	/**************************************************
	**
	**	beginTransaction
	**	----------------------------------------------
	**	DATABASE TRANSACTION
	**
	**************************************************/

	public static function beginTransaction(){
		self::$database->beginTransaction();
		self::$transaction				= 1;
	}



	/**************************************************
	**
	**	rollback
	**	----------------------------------------------
	**	DATABASE ROLL BACK
	**
	**************************************************/

	public static function rollBack(){
		if(!empty(self::$transaction)){
			self::$database->rollBack();
			self::$transaction			= 0;
		}
	}



	/**************************************************
	**
	**	commit
	**	----------------------------------------------
	**	DATABASE COMMIT
	**
	**************************************************/

	public static function commit(){
		if(!empty(self::$transaction)){
			self::$database->commit();
		}
	}



	/**************************************************
	**
	**	checkTransaction
	**	----------------------------------------------
	**	DATABASE TRANSACTION ACTIVE CHECK
	**
	**************************************************/

	public static function checkTransaction(){

		if(self::$database->isTransactionActive()){
			return TRUE;
		}else{
			return FALSE;
		}

	}



	/**************************************************
	**
	**	getTableFields
	**	----------------------------------------------
	**	TABLE FIELD GET
	**
	**************************************************/

	public static function getTableFields($table){

		$sql 	= "SHOW fields FROM ".$table."";
		$rtn	= self::query($sql);

		$result	= NULL;

		while($fields_data = self::fetchAssoc($rtn)){
			foreach($fields_data as $key => $fields){
				$result[$fields] = NULL;
			}
		}

		return $result;

	}



	/**************************************************
	**
	**	arrangeData
	**	----------------------------------------------
	**	INSERT / UPDATE 用に渡された値をSQL用に自動生成
	**	----------------------------------------------
	**	$table		: 対象table
	**
	**	$data		: $_POST値
	**
	**	$purpose	:  1 -> INSERT
	**				:  2 -> UPDATE
	**				:  3 -> DELETE
	**	----------------------------------------------
	**	処理概要	:
	**	showDbにて対象テーブルのFIELDS DATA取得
	**	FIELDS DATAのカラム名を配列から取り出し、
	**	渡された$dataのKEY値と照合。
	**	＝ならreturn値生成。！＝ならCONTINUE
	**************************************************/

	public static function arrangeData($table,$data,$purpose,$link=NULL){

		if(empty($table) || empty($data)){
			return FALSE;
		}

		# UNSET
		if(!empty($data['year1'])){ unset($data['year1']); }
		if(!empty($data['month1'])){ unset($data['month1']); }
		if(!empty($data['day1'])){ unset($data['day1']); }
		if(!empty($data['hour1'])){ unset($data['hour1']); }
		if(!empty($data['minutes1'])){ unset($data['minutes1']); }

		# NOT INSERT / ID UNSET ( UPDATE / DELETE )
		if(!empty($data['id'])){ unset($data['id']); }

		# SHOW FIELDS
		$fields_data = self::showDb($table);

		# COLUMN COUNT
		$count	= count($fields_data['Field']);

		# GET TABLE COLUMN
		for($i=0;$i<$count;$i++){

			$fields_column	= $fields_data['Field'][$i];
			$fields_type	= $fields_data['Type'][$i];

			# $_REQUEST値を配列で格納
			foreach($data as $column => $value){

				if($column != $fields_column){
					continue;
				}

				# REMOVE TAG
				$value			= kses($value);

				if($fields_type != "text"){
					//$value		= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
				}

				# STRIP TAGS
				if(empty($link)){
					$value		= self::stripTag($value,"a");
				}

				# QUOTES REPLACE
				if(preg_match("/&quot;/",$value)){
					$value		= str_replace("&quot;","\"",$value);
				}

				# ADDSLASHES
				if(preg_match("/'/",$value)){
					$value		= addslashes($value);
				}

				# MAGIC QUOTES
				if(get_magic_quotes_gpc()){
					$value		= stripslashes($value);
				}

				$arrange_data[$column] = $value;

			}

			if($fields_column == "updated_date"){
				$arrange_data['updated_date']	= date("Y-m-d H:i:s");
			}

			if($fields_column == "site_id" && defined("SITE_ID")){
				$arrange_data['site_id']	= SITE_ID;
			}

			if($fields_column == "admin_id"){
				global	$account_data;
				if(!empty($account_data['id'])){
					$arrange_data['admin_id']	= $account_data['id'];
				}
			}

		}

		return $arrange_data;

	}



	/**************************************************
	**
	**	stripTag
	**	----------------------------------------------
	**	指定タグの除去 : 正規表現
	**
	**************************************************/

	private static function stripTag($str,$tag){

		$pattern1	= sprintf("!<%s.*?>!ims",$tag,$tag);
		$pattern2	= sprintf("!</%s>!ims",$tag,$tag);
		$replace	= "";
	 	$str		= preg_replace($pattern1,$replace,$str);
	 	$str		= preg_replace($pattern2,$replace,$str);

	 	return $str;

	}



	/**************************************************
	**
	**	getMicroTime
	**	----------------------------------------------
	**	QUERY TIME
	**
	**************************************************/

	private static function getMicroTime($time){

		if(empty($time)){
			return FALSE;
		}

		list($usec, $sec) = explode(" ",$time);
		return ((float)$sec + (float)$usec);

	}



	/**************************************************
	**
	**	getQueryStartTime
	**	----------------------------------------------
	**	QUERY START TIME
	**
	**************************************************/

	private static function getQueryStartTime(){

		# QUERY START TIME
		self::$query_start_time	= microtime();
		self::$query_start_secd	= date("i");

	}



	/**************************************************
	**
	**	getQueryEndTime
	**	----------------------------------------------
	**	QUERY END TIME
	**
	**************************************************/

	private static function getQueryEndTime(){

		# QUERY END TIME
		$query_end_time	= microtime();
		$query_end_secd	= date("i");

		# TIME CALCURATION
		$query_time		= self::getMicroTime($query_end_time) - self::getMicroTime(self::$query_start_time);
		$query_time		= $query_time + ( $query_end_secd - self::$query_start_secd );
		$debug_time		= round($query_time,5);

		return $debug_time;

	}



	/**************************************************
	**
	**	getWhileStartTime
	**	----------------------------------------------
	**	WHILE START TIME
	**
	**************************************************/

	private static function getWhileStartTime(){

		# WHILE START TIME
		self::$while_start_time	= microtime();
		self::$while_start_secd	= date("i");

	}



	/**************************************************
	**
	**	getWhileEndTime
	**	----------------------------------------------
	**	WHILE END TIME
	**
	**************************************************/

	private static function getWhileEndTime(){

		# WHILE END TIME
		$while_end_time	= microtime();
		$while_end_secd	= date("i");

		# TIME CALCURATION
		$while_time		= self::getMicroTime($while_end_time) - self::getMicroTime(self::$while_start_time);
		$while_time		= $while_time + ( $while_end_secd - self::$while_start_secd );
		$debug_time		= round($while_time,5);

		return $debug_time;

	}



	/************************************************
	**
	**	sendSlaveData
	**	---------------------------------------------
	**	SLAVE DATA SEND BY CURL
	**
	************************************************/

	public static function sendSlaveData($sql){

		# STOP
		return FALSE;

		if(empty($sql)){
			return FALSE;
		}

		# PARAMETER
		$parameter	= "sql=".$sql;

		# CURL SEND
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, HTTP_SLAVE );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $parameter );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
		$res = curl_exec( $ch ); 
		curl_close( $ch );

		return TRUE;

	}


	/************************************************
	**
	**	sendSlaveData
	**	---------------------------------------------
	**	SLAVE DATA SEND BY CURL
	**
	************************************************/

	public static function checkSqlStrings($sql){

		if(preg_match("/;/",$sql)){
			return FALSE;
		}

		$pattern = '/.asp/i';
		if(preg_match($pattern, $sql, $matches)){
			return FALSE;
		}

		$pattern = '/.txt/i';
		if(preg_match($pattern, $sql, $matches)){
			return FALSE;
		}


		$pattern = '/javascript/i';
		if(preg_match($pattern, $sql, $matches)){
			return FALSE;
		}

		return TRUE;

	}


	/**************************************************
	**
	**	removeTags
	**	----------------------------------------------
	**	ksesを使ったタグ除去ファンクション
	**
	**************************************************/

	public static function removeTags($data){

		if(empty($data)){
			return $data;
		}

		$result				= NULL;
		$result				= array();

		foreach($data as $key => $value){
			if(!empty($value)){
				$value		= kses($value);
			}
			$result[$key]	= $value;
		}

		return $result;

	}



	/*********************************************
	**
	**	outputError
	**	-----------------------------------------
	**	ERROR 出力
	**
	*********************************************/

	public static function outputError($str){

		mail("takai@k-arat.co.jp","DATABASE ERROR【NIJIYOME】",$str,"info@mailanime.net");

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


}

?>