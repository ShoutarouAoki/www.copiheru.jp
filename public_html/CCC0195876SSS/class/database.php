<?

class accessDb {

	var $db_no;
	var $db_ip;
	var $db_user;
	var $db_pass;
	var $db_name;
	var $connect;
	var $result;
	var $debuglevel = 1; # 0:表示なし 1:SELECT文表示
	var $debug_sql_msg;


	function accessDb($db_no = 0) {

		global $db_list_ary;

		$this->db_ip   = $db_list_ary[$db_no][1];	# 接続IP
		$this->db_user = $db_list_ary[$db_no][2];	# ﾕｰｻﾞｰ
		$this->db_pass = $db_list_ary[$db_no][3];	# ﾊﾟｽﾜｰﾄﾞ
		$this->db_name = $db_list_ary[$db_no][4];	# ﾃﾞｰﾀﾍﾞｰｽ名

	}

	# 接続
	function connectDb() {

		$this->connect = mysqli_connect($this->db_ip,$this->db_user,$this->db_pass,$this->db_name) or die(mysqli_connedct_error());
		mysqli_set_charset($this->connect,"utf8");

		return ($db_no);

	}

	# 切断
	function closeDb() {
		mysqli_close($this->connect);
	}

	# ｴﾗｰ処理
	function errorDb($sql='',$errno,$filename,$line){
		if($errno>0){
			print("DB_ERROR:".$errno.":".mysqli_error($this->connect)."<br />");
			if($sql != ''){
			print("QUERY:".$sql."<br />");
			}
			print("FILENAME:".$filename."<br />");
			print("LINE:".$line);
			# TAKAI 140228
			$mail	 = "DB_ERROR:".$errno.":".mysqli_error($this->connect)."\n";
			if($sql != ''){
			$mail	.= "QUERY:".$sql."\n";
			}
			$mail	.= "FILENAME:".$filename."\n";
			$mail	.= "LINE:".$line."\n";
			$mail	.= "REMOTE:".$_SERVER['REMOTE_ADDR'];
			#mail("takai@k-arat.co.jp","DB ERROR",$mail,"FROM:info@0177.jp");
			#mail("okuma@k-arat.co.jp","DB ERROR",$mail,"FROM:info@0177.jp");
		}
	}

	# SELECT文
	function selectDb($table,$select,$where,$order='',$limit='',$query_select=NULL){

		global $adminMain;

		$sql  = "SELECT ".$select." FROM ".$table." WHERE ".$where." ";
		if($order){ $sql .= "ORDER BY ".$order." "; }
		if($limit){ $sql .= "LIMIT ".$limit; }

		/*
		$this->result = $this->query($sql);
		if($db->errno > 0){
			print($db->errno.":".mysqli_error($this->connect)."<br />");
		}
		*/

		try { 
			if(!empty($query_select)){
				$this->result = $this->query2($sql);
			}else{
				$this->result = $this->query($sql);
			}
			if(!$this->result){ throw new Exception(); }
		}catch(Exception $e){
			print("<pre>");
			print_r( $e->getTrace() );
			print("</pre>");
		}

		if($adminMain->debugSystem()){
			if($this->debuglevel == 1){
				#print($sql."<br />");
			}
		}

		return($this->result);

	}

	# 文
	function insertDb($table,$insert){

		foreach( $insert as $colum => $value ){
			$ins_obj .= $colum." = '".$value."',";
		}
		$ins_obj = substr_replace($ins_obj,'', -1,1);

		$sql  = "INSERT INTO ".$table." SET ";
		$sql .= $ins_obj;

		$this->result = $this->query($sql);
		$ins_id  = mysqli_insert_id($this->connect);

		return($ins_id);

	}

	# UPDATE文
	function updateDb($table,$update,$where){

		foreach( $update as $colum => $value ){
			$upd_obj .= $colum." = '".$value."',";
		}
		$upd_obj = substr_replace($upd_obj,'', -1,1);

		#$sql  = "UPDATE ".$table." SET ";
		#$sql .= $upd_obj." WHERE ".$where;

		$sql  = "update ".$table." set ";
		$sql .= $upd_obj." where ".$where;

		$this->result = $this->query($sql);

		return(true);

	}

	# UPDATE文
	function updateDb2($table,$update,$where){

		foreach( $update as $colum => $value ){
			$upd_obj .= $colum." = '".$value."',";
		}
		$upd_obj = substr_replace($upd_obj,'', -1,1);

		$sql  = "update ".$table." set ";
		$sql .= $upd_obj." where ".$where;

		print($sql);

		$this->result = $this->query($sql);

		return(true);

	}

	# DELETE文
	function deleteDb($table,$where,$limit=''){

		$sql  = "DELETE FROM ".$table." WHERE ".$where." ";
		if($limit){ $sql .= "LIMIT ".$limit; }

		$this->result = $this->query($sql);

		return(true);

	}

	# 結果を開放
	function free_result($rtn){
		mysqli_free_result($rtn);
	}

	# ｸｴﾘ送信
	# 12/04/26 SELECT文はslaveを見に行くよう変更
	/*
	function query($sql){

		global $adminMain;

		if($adminMain->debugSystem()){
			if($this->debuglevel == 1){
				$this->debug_sql_msg .= $sql."<hr class=\"hr_dotted_black\" />\n";
			}
		}

		$rtn = mysql_query($sql,$this->connect);

		return $rtn;
	}
	*/
	function query($sql){

		global $adminMain;

		if($adminMain->debugSystem()){
			if($this->debuglevel == 1){
				$this->debug_sql_msg .= $sql."<hr class=\"hr_dotted_black\" />\n";
			}
		}


		if(preg_match("/^SELECT/i",$sql)){
			$this->accessDb(1);	# slave
		}else{
			$this->accessDb(0);	# master
		}

		$this->connectDb();

		$rtn = $this->connect->query($sql);

		return $rtn;

	}

	# MASTERを強制的に読む
	function query2($sql){

		global $adminMain;

		if($adminMain->debugSystem()){
			if($this->debuglevel == 1){
				$this->debug_sql_msg .= $sql."<hr class=\"hr_dotted_black\" />\n";
			}
		}

		$rtn = $this->connect->query($sql);

		return $rtn;
	}

	# ｸｴﾘTEST
	function query_test($sql){

		global $adminMain;

		if($adminMain->debugSystem()){
			if($this->debuglevel == 1){
				$this->debug_sql_msg .= $sql."<hr class=\"hr_dotted_black\" />\n";
			}
		}

	#	print($this->db_ip."<br>");
	#	print($this->db_name."<br>");

		$rtn = $this->connect->query($sql);

		return $rtn;
	}



	# ｸｴﾘとﾌｪｯﾁ
	function queryEx($sql='') {
 		if($sql != ''){
			$this->result = $this->Query($sql);
			if (!$this->result){
				return FALSE;
			}
			return $this->fetchAssoc($this->result);
		}else{
			return $this->fetchAssoc($this->result);
		}
	}

	# mysql_fetch_object
	function fetchObj($rtn){
		$data = mysqli_fetch_object($rtn);
		return $data;
	}

	# mysql_fetch_assoc
	function fetchAssoc($rtn){
		$data = mysqli_fetch_assoc($rtn);
		return $data;
	}

	# mysql_fetch_array
	function fetchArray($rtn){
		$data = mysqli_fetch_array($rtn);
		return $data;
	}

	# mysql_num_rows
	function numRows($rtn){
		$data = mysqli_num_rows($rtn);
		return $data;
	}

	# ﾄﾗﾝｻﾞｸｼｮﾝ開始
	function tran_begin(){
		$rtn = $this->query('begin');
	}

	# ﾛｰﾙﾊﾞｯｸ
	function rollback(){
		$rtn = $this->query('rollback');
	}

	# ｺﾐｯﾄ
	function commit(){
		$rtn = $this->query('commit');
		if(!$rtn){
			$this->rollback();
		}
		return $rtn;
	}




}
?>
