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

require_once(DOCUMENT_SYSTEM_PLUGINS."/Kses/kses.php");

/*********************************************************************************/


# CLASS DEFINE
class Database{


	/**************************************************
	**
	**	SETTING
	**	----------------------------------------------
	**	PUBLIC / PROTECTED / PRIVATE
	**
	**************************************************/

	private	$connect;
	private	$result;
	private	$freeresult;
	private $transaction;
	private	$multi_sql;
	private	$multi_data;
	private	$multi_array;
	private	$multi_where;
	private	$multi_check;
	private	$multi_type;
	private $multi_count;
	private	$query_start_time;
	private	$query_start_secd;
	private	$while_start_time;
	private	$while_start_secd;
	private	$debug_level;
	public	$debug_query;
	public	$debuq_sql;
	public	$debug_array;
	public	$while_query;
	public	$while_sql;
	public	$while_array;
	public	$database;
	public	$authority;



	/**************************************************
	**
	**	CONSTRUCT / DESTRUCT
	**	----------------------------------------------
	**	DATABASES CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# CONSTRUCT
	function __construct(){

		$this->transaction			= 0;
		$this->debug_query			= NULL;
		$this->debug_level			= 0;
		$this->multi_count			= 0;
		if(DEBUG_MODE == "ON"){
			if(DEBUG_SQL == "ALL" || DEBUG_SQL == "NORMAL"){
				$this->debug_level	= 1;
			}
		}

    }

	# DESTRUCT
	function __destruct(){

    }



	/**************************************************
	**
	**	connectDb
	**	----------------------------------------------
	**	DATABASE MYSQL CONNECT
	**
	**************************************************/

	public function connectDb($grant=NULL) {

		if($grant === MASTER_ACCESS_KEY){
			$db_ip				= DATABASE_IP;
			$db_user			= DATABASE_USER;
			$db_pass			= DATABASE_PASS;
			$db_name			= DATABASE_NAME;
			$this->debug_query .= "【DATABASE SELECT】&nbsp;→&nbsp;<span style=\"color: #00CCFF;\">MASTER</span>&nbsp;&nbsp;:&nbsp;&nbsp;<span style=\"color: #FFFFFF;\">".$db_ip."</span>";
			$this->debug_query .= "\n<hr class=\"query_line\" />\n";
			$this->authority	= 1;
		}else{
			$db_ip				= DATABASE_IP_S;
			$db_user			= DATABASE_USER_S;
			$db_pass			= DATABASE_PASS_S;
			$db_name			= DATABASE_NAME_S;
			$this->debug_query .= "【DATABASE SELECT】&nbsp;→&nbsp;<span style=\"color: #FF0000;\">SLAVE</span>&nbsp;&nbsp;:&nbsp;&nbsp;<span style=\"color: #777777;\">".$db_ip."</span>";
			$this->debug_query .= "\n<hr class=\"query_line\" />\n";
			$this->authority	= NULL;
		}

		try {
			$this->database = new PDO("mysql:host=".$db_ip.";dbname=".$db_name.";charset=utf8",$db_user,$db_pass,
			array(PDO::ATTR_EMULATE_PREPARES => false));
		} catch (PDOException $e) {
			$this->database	= NULL;
			return ("DB_ERROR:<br />Can not connect ".$db_ip."<br />".mysqli_error());
		}

		return TRUE;
	}



	/**************************************************
	**
	**	closeDb
	**	----------------------------------------------
	**	DATABASE MYSQL CLOSE
	**
	**************************************************/

	public function checkAuthority() {
		return	$this->authority;
	}



	/**************************************************
	**
	**	closeDb
	**	----------------------------------------------
	**	DATABASE MYSQL CLOSE
	**
	**************************************************/

	public function closeDb() {
		$this->database	= NULL;
	}



	/**************************************************
	**
	**	closeStmt
	**	----------------------------------------------
	**	DATABASE STMT CLOSE
	**
	**************************************************/

	public function closeStmt() {
		if(!empty($this->freeresult)){
			$count	= count($this->freeresult);
			if($count > 0){
				for($i=0;$i<$count;$i++){
					$this->freeresult[$i]->closeCursor();
					$this->freeresult[$i]	= NULL;
					unset($this->freeresult[$i]);
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

	public function destructStmt($stmt) {
		$this->freeresult[]	= $stmt;
	}



	/**************************************************
	**
	**	errorDb
	**	----------------------------------------------
	**	DATABASE MYSQL ERROR
	**
	**************************************************/

	public function errorDb($sql=NULL,$errno,$filename,$line){

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

	public function freeResult($result,$debug=NULL){

		if(defined("SYSTEM_CHECK")){

			if($this->debug_level == 1){

				# QUERY END TIME
				if(!empty($debug)){
					$debug_time		= $this->getWhileEndTime();
				}else{
					$debug_time		= $this->getQueryEndTime();
				}

				# QUERY OUTPUT
				if($this->debug_level == 1){
					
					if(!empty($debug)){
						$this->debug_query		.= $this->while_sql."<br />\n";
						if(!empty($this->while_array)){
							ob_start();
							var_dump($this->while_array);
							$buffer				 = ob_get_contents();
							ob_end_clean();
							$this->debug_query	.= $buffer."<br />\n";
						}
						$this->debug_query		.= "(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
					}else{
						$this->debug_query		.= $this->debuq_sql."<br />\n";
						if(!empty($this->debug_array)){
							ob_start();
							var_dump($this->debug_array);
							$buffer				 = ob_get_contents();
							ob_end_clean();
							$this->debug_query	.= $buffer."<br />\n";
						}
						$this->debug_query		.= "(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
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

	public function selectDb($table,$column,$where=NULL,$array=NULL,$order=NULL,$limit=NULL,$group=NULL,$debug=NULL){

		$sql  = "SELECT ".$column." FROM ".$table." ";
		if(!empty($where)){ $sql .= "WHERE ".$where." "; }
		if(!empty($group)){ $sql .= "GROUP BY ".$group." "; }
		if(!empty($order)){ $sql .= "ORDER BY ".$order." "; }
		if(!empty($limit)){ $sql .= "LIMIT ".$limit; }

		$stmt						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = $this->removeTags($array);

		if(DEBUG_SQL == "ALL"){
			$check_array			= NULL;
			foreach($array as $check_key => $check_value){
				$check_array		.= "[".$check_key."] => ".$check_value."&nbsp;";
			}
			$this->debug_query		.= "<span style=\"color: #666666;\"> CHECK : ".$sql."<br />".$check_array."</span>\n<hr class=\"query_line\" />\n";
		}

		try{

			# PREPARE
			$stmt					 = $this->prepare($sql,$array,$debug);

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

	public function insertDb($table,$data){

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
			$this->debug_query	.= "<span style=\"color: #666666;\"> CHECK : ".$sql."</span>\n<hr class=\"query_line\" />\n";
		}

		$insert_id					 = NULL;

		try{

			# PREPARE
			$stmt					 = $this->prepare($sql);
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

			$insert_id				 = $this->database->lastInsertId();

		}catch(Exception $e){

			if(defined("SYSTEM_CHECK")){
				print("<pre>");
				print_r($e->getTrace());
				print("</pre>");
			}

			$this->rollBack();

			return FALSE;

		}

		$this->destructStmt($stmt);

		return $insert_id;

	}



	/**************************************************
	**
	**	bulkInsertDb
	**	----------------------------------------------
	**	DATABASE MYSQL BULK INSERT
	**
	**************************************************/

	public function bulkInsertDb($table,$column,$data){

		if(empty($table) || empty($column) || empty($data)){
			return FALSE;
		}

		# 初期化
		$insert_obj		 = NULL;

		$insert_obj		 = substr_replace($data,'', -1,1);

		$sql			 = "INSERT INTO ".$table." (".$column.") VALUES ";
		$sql			.= $insert_obj;

		$result			 = $this->query($sql);

		return $insert_id;

	}



	/**************************************************
	**
	**	updateDb
	**	----------------------------------------------
	**	DATABASE MYSQL UPDATE
	**
	**************************************************/

	public function updateDb($table,$data,$where,$array=NULL){

		if(empty($table) || empty($data) || empty($where) || empty($array)){
			return FALSE;
		}

		# 初期化
		$update_column			 = NULL;
		$update_where			 = NULL;
		$column					 = NULL;
		$value					 = NULL;

		# REMOVE TAGS
		$data					 = $this->removeTags($data);

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
			$this->debug_query		.= "<span style=\"color: #666666;\"> CHECK : ".$sql."<br />".$check_array."</span>\n<hr class=\"query_line\" />\n";
		}


		try{

			# PREPARE
			$stmt					 = $this->prepare($sql,$array);

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

			$this->rollBack();

			return FALSE;

		}

		$this->destructStmt($stmt);

		return TRUE;

	}



	/**************************************************
	**
	**	deleteDb
	**	----------------------------------------------
	**	DATABASE MYSQL DELETE
	**
	**************************************************/

	public function deleteDb($table,$where,$limit=NULL){

		if(!defined("SYSTEM_CHECK")){
			return FALSE;
		}

		$sql  = "DELETE FROM ".$table." WHERE ".$where." ";
		if(!empty($limit)){ $sql .= "LIMIT ".$limit; }

		$result = $this->query($sql);

		return TRUE;

	}



	/**************************************************
	**
	**	showDb
	**	----------------------------------------------
	**	DATABASE MYSQL SHOW TABLE
	**
	**************************************************/

	public function showDb($table){

		$field_array	= array();

		$sql			= "SHOW fields FROM ".$table."";
		$result			= $this->query($sql);

		$error			= $this->errorDb("SHOW FIELDS : ARRANGE DATA",$result->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ FALSE; }

		while($fields_data = $this->fetchAssoc($result)){
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

	public function query($sql,$debug=NULL){

		# QUERY START TIME
		if(defined("SYSTEM_CHECK")){
			if($this->debug_level == 1){
				if(!empty($debug)){
					$this->getWhileStartTime();
				}else{
					$this->getQueryStartTime();
				}
			}
		}

		# ERROR チェック
		$valifation	= $this->checkSqlStrings($sql);
		if(empty($valifation)){
			exit();
		}

		$sql	.= ";";

		$stmt	 = $this->database->query($sql);

		# QUERY OUTPUT
		if(defined("SYSTEM_CHECK")){
			if($this->debug_level == 1){
				if(preg_match("/SELECT/",$sql)){
					if(!empty($debug)){
						$this->while_sql	 = $sql;
					}else{
						$this->debuq_sql	 = $sql;
					}
				}else{
					$debug_time				 = $this->getQueryEndTime();
					$this->debug_query		.= $sql."<br />\n(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
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

	public function prepare($sql,$array=NULL,$debug=NULL){
		# QUERY START TIME
		if(defined("SYSTEM_CHECK")){
			if($this->debug_level == 1){
				if(!empty($debug)){
					$this->getWhileStartTime();
				}else{
					$this->getQueryStartTime();
				}
			}
		}

		# ERROR チェック
		$valifation	= $this->checkSqlStrings($sql);
		if(empty($valifation)){
			exit();
		}

		try{
			# PREPARE
			$stmt = $this->database->prepare($sql);
			if(empty($stmt)){ throw new Exception(); }

		}catch(Exception $e){

			if(defined("SYSTEM_CHECK")){
				print("<pre>");
				print_r($e->getTrace());
				print("</pre>");
			}
			$this->rollBack();

			return FALSE;

		}

		# QUERY OUTPUT
		if(defined("SYSTEM_CHECK")){
			if($this->debug_level == 1){
				if(preg_match("/SELECT/",$sql)){
					if(!empty($debug)){
						$this->while_sql	 = $sql;
						$this->while_array	 = $array;
					}else{
						$this->debug_sql	 = $sql;
						$this->debug_array	 = $array;
					}
				}else{
					$debug_time				 = $this->getQueryEndTime();
					$this->debug_query		.= $sql."<br />\n";
					if(!empty($array)){
						ob_start();
						var_dump($array);
						$buffer				 = ob_get_contents();
						ob_end_clean();
						$this->debug_query	.= $buffer."<br />\n";
					}
					$this->debug_query		.= "(".$debug_time." sec)\n<hr class=\"query_line\" />\n";
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

	public function multiInsertDb($table,$data){

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
			$this->debug_query	.= "<span style=\"color: #666666;\"> CHECK : ".$sql."</span>\n<hr class=\"query_line\" />\n";
		}

		$this->multi_sql[$this->multi_count]		= $sql;
		$this->multi_data[$this->multi_count]		= $data;
		$this->multi_array[$this->multi_count]		= NULL;
		$this->multi_where[$this->multi_count]		= NULL;
		$this->multi_check[$this->multi_count]		= $check;
		$this->multi_type[$this->multi_count]		= 1;

		$this->multi_count++;

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

	public function multiUpdateDb($table,$data,$where,$array=NULL){

		if(empty($table) || empty($data) || empty($where) || empty($array)){
			return FALSE;
		}

		# 初期化
		$update_column			 = NULL;
		$update_where			 = NULL;
		$column					 = NULL;
		$value					 = NULL;

		# REMOVE TAGS
		$data					 = $this->removeTags($data);

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
			$this->debug_query		.= "<span style=\"color: #666666;\"> CHECK : ".$sql."<br />".$check_array."</span>\n<hr class=\"query_line\" />\n";
		}

		$this->multi_sql[$this->multi_count]		= $sql;
		$this->multi_data[$this->multi_count]		= $data;
		$this->multi_array[$this->multi_count]		= $array;
		$this->multi_where[$this->multi_count]		= $update_where;
		$this->multi_check[$this->multi_count]		= $check;
		$this->multi_type[$this->multi_count]		= 2;

		$this->multi_count++;

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

	public function multiQueryDb($sql){

		$this->multi_sql[$this->multi_count]		= $sql;
		$this->multi_data[$this->multi_count]		= NULL;
		$this->multi_array[$this->multi_count]		= NULL;
		$this->multi_where[$this->multi_count]		= NULL;
		$this->multi_check[$this->multi_count]		= NULL;
		$this->multi_type[$this->multi_count]		= 3;

		$this->multi_count++;

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

	public function multiExection(){

		$error			= NULL;

		if($this->multi_count > 0){

			# AUTHORITY
			$db_auth			 = $this->checkAuthority();
			$db_check			 = NULL;

			# DATABASE CHANGE
			if(empty($db_auth)){

				# CLOSE DATABASE SLAVE
				$this->closeDb();

				# CONNECT DATABASE MASTER
				$this->connectDb(MASTER_ACCESS_KEY);

				$db_check		 = 1;

			}

			# TRY
			try{

				# ROLLBACK START
				$this->database->beginTransaction();

				# LOOP
				for($i=0;$i<$this->multi_count;$i++){

					# INSERT
					if(!empty($this->multi_type[$i]) && $this->multi_type[$i] == 1){

						if(!empty($this->multi_sql[$i]) && !empty($this->multi_data[$i])){

							# PREPARE
							$stmt					 = $this->prepare($this->multi_sql[$i]);

							# ERROR
							if(empty($stmt)){
								$error				 = 1;
								break;
							}

							$column					 = NULL;
							$value					 = NULL;
							$j=0;
							foreach( $this->multi_data[$i] as $column => $value ){
								//print($column."----".$this->multi_check[$i][$j]."<br />");
								$stmt->bindParam(":".$column."", $this->multi_check[$i][$j], PDO::PARAM_STR);
								$j++;
							}

					 		$result					 = $stmt->execute();
							if(empty($result)){ throw new Exception(); }

							$stmt->closeCursor();

						}

					# UPDATE
					}elseif(!empty($this->multi_type[$i]) && $this->multi_type[$i] == 2){

						if(!empty($this->multi_sql[$i]) && !empty($this->multi_data[$i]) && !empty($this->multi_array[$i])){

							# PREPARE
							$stmt					 = $this->prepare($this->multi_sql[$i],$this->multi_array[$i]);

							# ERROR
							if(empty($stmt)){
								$error				 = 1;
								break;
							}

							$column					 = NULL;
							$value					 = NULL;
							$j=0;
							foreach( $this->multi_data[$i] as $column => $value ){
								$stmt->bindParam(":".$column."", $this->multi_check[$i][$j], PDO::PARAM_STR);
								$j++;
							}

							$column					 = NULL;
							$value					 = NULL;
							$j=0;
							foreach( $this->multi_array[$i] as $column => $value ){
								$stmt->bindParam($column."", $this->multi_where[$i][$j], PDO::PARAM_STR);
								$j++;
							}

							$result					 = $stmt->execute();
							if(empty($result)){ throw new Exception(); }


						}


					# QUERY
					}elseif(!empty($this->multi_type[$i]) && $this->multi_type[$i] == 3){

						if(!empty($this->multi_sql[$i])){

							$stmt	 = $this->database->query($this->multi_sql[$i]);
							$stmt->closeCursor();

						}

					}

				}

				# COMMIT
				if(empty($error)){
					$this->database->commit();
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
				$this->database->rollBack();

				mail(MAIL_SYSTEM,"SQL MULTI EXECTION ERROR",$e->getMessage(),"From:".MAIL_INFO);

				$error					= 1;

			}


			# DATABASE CHANGE
			if(!empty($db_check)){

				# CLOSE DATABASE MASTER
				$this->closeDb();

				# CONNECT DATABASE SLAVE
				$this->connectDb();

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

	public function fetchObject($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetch(PDO::FETCH_OBJ);
		$this->destructStmt($stmt);
		return $data;

	}



	/**************************************************
	**
	**	fetchAssoc
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ASSOC
	**
	**************************************************/

	public function fetchAssoc($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->destructStmt($stmt);
		return $data;

	}



	/**************************************************
	**
	**	fetchArray
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ARRAY
	**
	**************************************************/

	public function fetchArray($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetch(PDO::FETCH_NUM);
		$this->destructStmt($stmt);
		return $data;

	}

	/**************************************************
	**
	**	fetchAll
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ALL
	**
	**************************************************/

	public function fetchAll($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$this->destructStmt($stmt);
		return $data;

	}

	/**************************************************
	**
	**	fetchAll
	**	----------------------------------------------
	**	DATABASE MYSQL FETCH ALL
	**
	**************************************************/

	public function fetchAllByColumn($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data = $stmt->fetchAll(PDO::FETCH_COLUMN);
		$this->destructStmt($stmt);
		return $data;

	}


	
	/**************************************************
	**
	**	numRows
	**	----------------------------------------------
	**	DATABASE MYSQL NUM ROWS
	**
	**************************************************/

	public function numRows($stmt){

		if(empty($stmt)){
			return FALSE;
		}

		$data	= $stmt->rowCount();
		$this->destructStmt($stmt);
		return $data;

	}



	/**************************************************
	**
	**	beginTransaction
	**	----------------------------------------------
	**	DATABASE TRANSACTION
	**
	**************************************************/

	public function beginTransaction(){
		$this->database->beginTransaction();
		$this->transaction				= 1;
	}



	/**************************************************
	**
	**	rollback
	**	----------------------------------------------
	**	DATABASE ROLL BACK
	**
	**************************************************/

	public function rollBack(){
		if(!empty($this->transaction)){
			$this->database->rollBack();
			$this->transaction			= 0;
		}
	}



	/**************************************************
	**
	**	commit
	**	----------------------------------------------
	**	DATABASE COMMIT
	**
	**************************************************/

	public function commit(){
		if(!empty($this->transaction)){
			$this->database->commit();
		}
	}



	/**************************************************
	**
	**	checkTransaction
	**	----------------------------------------------
	**	DATABASE TRANSACTION ACTIVE CHECK
	**
	**************************************************/

	public function checkTransaction(){

		if($this->database->isTransactionActive()){
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

	public function getTableFields($table){

		$sql 	= "SHOW fields FROM ".$table."";
		$rtn	= $this->query($sql);

		$result	= NULL;

		while($fields_data = $this->fetchAssoc($rtn)){
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

	public function arrangeData($table,$data,$purpose,$link=NULL){

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
		$fields_data = $this->showDb($table);

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
					$value		= $this->stripTag($value,"a");
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

	private function stripTag($str,$tag){

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

	private function getMicroTime($time){

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

	private function getQueryStartTime(){

		# QUERY START TIME
		$this->query_start_time	= microtime();
		$this->query_start_secd	= date("i");

	}



	/**************************************************
	**
	**	getQueryEndTime
	**	----------------------------------------------
	**	QUERY END TIME
	**
	**************************************************/

	private function getQueryEndTime(){

		# QUERY END TIME
		$query_end_time	= microtime();
		$query_end_secd	= date("i");

		# TIME CALCURATION
		$query_time		= $this->getMicroTime($query_end_time) - $this->getMicroTime($this->query_start_time);
		$query_time		= $query_time + ( $query_end_secd - $this->query_start_secd );
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

	private function getWhileStartTime(){

		# WHILE START TIME
		$this->while_start_time	= microtime();
		$this->while_start_secd	= date("i");

	}



	/**************************************************
	**
	**	getWhileEndTime
	**	----------------------------------------------
	**	WHILE END TIME
	**
	**************************************************/

	private function getWhileEndTime(){

		# WHILE END TIME
		$while_end_time	= microtime();
		$while_end_secd	= date("i");

		# TIME CALCURATION
		$while_time		= $this->getMicroTime($while_end_time) - $this->getMicroTime($this->while_start_time);
		$while_time		= $while_time + ( $while_end_secd - $this->while_start_secd );
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

	public function sendSlaveData($sql){

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

	public function checkSqlStrings($sql){

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

	public function removeTags($data){

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

	public function outputError($str){

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