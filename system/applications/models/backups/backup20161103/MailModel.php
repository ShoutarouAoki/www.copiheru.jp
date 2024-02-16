<?php
/********************************************************************************
**	
**	MailModel.php
**	=============================================================================
**
**	■PAGE / 
**	MAIL MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	MAIL CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	MAIL CLASS
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
class MailModel{


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
		$this->table		= "mails";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getMailList
	**	----------------------------------------------
	**	メール一覧
	**	----------------------------------------------
	**	$post_data['type'] : 1
	**	ユーザーが受け取り
	**
	**	$post_data['type'] : 2
	**	キャラが受け取り
	**
	**************************************************/

	public function getMailList($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id']) || empty($post_data['type'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column					= "*";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;
		$array[':user_id']			 = $post_data['user_id'];
		$array[':del_flg']			 = 0;

		# SEND ID
		if(!empty($post_data['character_id'])){
			$array[':character_id']		 = $post_data['character_id'];
		}

		$where						 = "site_cd = :site_cd ";

		if(!empty($post_data['last_mail_id'])){
			$array[':id']			 = $post_data['last_mail_id'];
			$where					.= "AND id > :id ";
		}

		if(!empty($post_data['start_id'])){
			$array[':id']			 = $post_data['start_id'];
			$where					.= "AND id < :id ";
		}

		# RECEIVE ONLY
		if($post_data['type'] == 1){

			if(!empty($post_data['character_id'])){
				$where				.= "AND send_id = :character_id ";
			}

			$where					.= "AND recv_id = :user_id ";

		# SEND ONLY
		}elseif($post_data['type'] == 2){

			$where					.= "AND send_id = :user_id ";
			if(!empty($post_data['character_id'])){
				$where				.= "AND recv_id = :character_id ";
			}

		# RECEIVE & SEND ALL
		}elseif($post_data['type'] == 3){

			if(empty($post_data['character_id'])){
				return FALSE;
			}

			$array[':user_id_r']		 = $post_data['user_id'];
			$array[':character_id_r']	 = $post_data['character_id'];

			$where				.= "AND (send_id = :user_id OR send_id = :character_id) ";
			$where				.= "AND (recv_id = :user_id_r OR recv_id = :character_id_r) ";

		}

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND send_date >= :send_date ";
		}

		if(!empty($post_data['recv_flg'])){
			$array[':recv_flg']	 = $post_data['recv_flg'];
			$where				.= "AND recv_flg = :recv_flg ";
		}else{
			$array[':recv_flg']	 = 9;
			$where				.= "AND recv_flg < :recv_flg ";
		}

		if(!empty($post_data['send_flg'])){
			$array[':send_flg']	 = $post_data['send_flg'];
			$where				.= "AND send_flg = :send_flg ";
		}

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']	 = $post_data['last_flg'];
			$where				.= "AND last_flg = :last_flg ";
		}

		$where					.= "AND del_flg = :del_flg";

		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;

		if(!empty($post_data['order'])){
			$order				 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit				 = $post_data['limit'];
		}

		if(!empty($post_data['list'])){
			$limit				 = $post_data['set'].",".$post_data['list'];
		}

		if(!empty($post_data['group'])){
			$group				 = $post_data['group'];
		}

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error					 = $database->errorDb("GET MAIL LIST",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getMailCount
	**	----------------------------------------------
	**	メールカウント
	**	----------------------------------------------
	**	$post_data['type'] : 1
	**	ユーザーが受け取り
	**
	**	$post_data['type'] : 2
	**	キャラが受け取り
	**
	**************************************************/

	public function getMailCount($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id']) || empty($post_data['type'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				= "id";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];
		$array[':del_flg']		 = 0;

		# SEND ID
		if(!empty($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
		}

		$where					 = "site_cd = :site_cd ";

		if(!empty($post_data['last_mail_id'])){
			$array[':id']		 = $post_data['last_mail_id'];
			$where				.= "AND id > :id ";
		}

		# RECEIVE ONLY
		if($post_data['type'] == 1){

			if(!empty($post_data['character_id'])){
				$where			.= "AND send_id = :character_id ";
			}

			$where				.= "AND recv_id = :user_id ";

		# SEND ONLY
		}elseif($post_data['type'] == 2){

			$where				.= "AND send_id = :user_id ";
			if(!empty($post_data['character_id'])){
				$where			.= "AND recv_id = :character_id ";
			}

		# RECEIVE & SEND ALL
		}elseif($post_data['type'] == 3){

			if(empty($post_data['character_id'])){
				return FALSE;
			}

			$array[':user_id_r']		 = $post_data['user_id'];
			$array[':character_id_r']	 = $post_data['character_id'];

			$where				.= "AND (send_id = :user_id OR send_id = :character_id) ";
			$where				.= "AND (recv_id = :user_id_r OR recv_id = :character_id_r) ";

		}

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND send_date >= :send_date ";
		}

		if(!empty($post_data['recv_flg'])){
			$array[':recv_flg']	 = $post_data['recv_flg'];
			$where				.= "AND recv_flg = :recv_flg ";
		}else{
			$array[':recv_flg']	 = 9;
			$where				.= "AND recv_flg < :recv_flg ";
		}

		if(!empty($post_data['send_flg'])){
			$array[':send_flg']	 = $post_data['send_flg'];
			$where				.= "AND send_flg = :send_flg ";
		}

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']	 = $post_data['last_flg'];
			$where				.= "AND last_flg = :last_flg ";
		}

		$where					.= "AND del_flg = :del_flg";

		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error					 = $database->errorDb("GET MAIL COUNT",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows					 = $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;


	}



	/**************************************************
	**
	**	getMailData
	**	----------------------------------------------
	**	メール取得
	**	----------------------------------------------
	**	$post_data['type'] : 1
	**	ユーザーが受け取り
	**
	**	$post_data['type'] : 2
	**	キャラが受け取り
	**
	**************************************************/

	public function getMailData($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id']) || empty($post_data['type'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				= "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];
		$array[':del_flg']		 = 0;

		# SEND ID
		if(!empty($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
		}

		$where					 = "site_cd = :site_cd ";

		if(!empty($post_data['last_mail_id'])){
			$array[':id']		 = $post_data['last_mail_id'];
			$where				.= "AND id > :id ";
		}

		# RECEIVE ONLY
		if($post_data['type'] == 1){

			if(!empty($post_data['character_id'])){
				$where			.= "AND send_id = :character_id ";
			}

			$where				.= "AND recv_id = :user_id ";

		# SEND ONLY
		}elseif($post_data['type'] == 2){

			$where				.= "AND send_id = :user_id ";
			if(!empty($post_data['character_id'])){
				$where			.= "AND recv_id = :character_id ";
			}

		# RECEIVE & SEND ALL
		}elseif($post_data['type'] == 3){

			if(empty($post_data['character_id'])){
				return FALSE;
			}

			$array[':user_id_r']		 = $post_data['user_id'];
			$array[':character_id_r']	 = $post_data['character_id'];

			$where				.= "AND (send_id = :user_id OR send_id = :character_id) ";
			$where				.= "AND (recv_id = :user_id_r OR recv_id = :character_id_r) ";

		}

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND send_date >= :send_date ";
		}

		if(!empty($post_data['recv_flg'])){
			$array[':recv_flg']	 = $post_data['recv_flg'];
			$where				.= "AND recv_flg = :recv_flg ";
		}else{
			$array[':recv_flg']	 = 9;
			$where				.= "AND recv_flg < :recv_flg ";
		}

		if(!empty($post_data['send_flg'])){
			$array[':send_flg']	 = $post_data['send_flg'];
			$where				.= "AND send_flg = :send_flg ";
		}

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']	 = $post_data['last_flg'];
			$where				.= "AND last_flg = :last_flg ";
		}

		$where					.= "AND del_flg = :del_flg";

		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;

		if(!empty($post_data['order'])){
			$order				 = $post_data['order'];
		}

		if(!empty($post_data['group'])){
			$group				 = $post_data['group'];
		}

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error					 = $database->errorDb("GET MAIL DATA",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getMailDataById
	**	----------------------------------------------
	**	メール取得
	**
	**************************************************/

	public function getMailDataById($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['id'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				= "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':id']			 = $post_data['id'];
		$array[':del_flg']		 = 0;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND id = :id ";
		$where					.= "AND del_flg = :del_flg";

		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error					 = $database->errorDb("getMailDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getUserReceiveMailListJoinOnMailusers
	**	----------------------------------------------
	**	ユーザー受信メール一括取得
	**	----------------------------------------------
	**	mailusersとJOIN
	**	ユーザーの受け取り専用
	**
	**************************************************/

	public function getUserReceiveMailListJoinOnMailusers($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				 = "u.id as mailusers_id, u.virtual_age, u.virtual_name, u.status, ";
			$column				.= "m.id as mail_id, m.send_id, m.recv_id, m.title, m.recv_flg, m.age, m.send_date, m.media_flg, m.naruto ";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];

		# DELETE FLG
		if(!empty($post_data['del_flg'])){
			$array[':del_flg']	 = $post_data['del_flg'];
		}else{
			$array[':del_flg']	 = 0;
		}

		# STATUS
		if(!empty($post_data['status'])){
			$array[':status']	 = $post_data['status'];
		}else{
			$array[':status']	 = 0;
		}

		# SELECT
		$sql					 = "SELECT ".$column." FROM mailusers u ";

		# JOIN
		$sql					.= "INNER JOIN ".$this->table." m ";

		# ON
		$sql					.= "ON u.user_id = m.recv_id ";
		$sql					.= "AND u.site_cd = :site_cd AND ";
		$sql					.= "u.send_id = m.send_id ";

		# WHERE
		$sql					.= "WHERE u.status = :status ";
		$sql					.= "AND u.user_id = :user_id ";

		if(!empty($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
			$sql				.= "AND u.send_id = :character_id ";
		}

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND m.send_date >= :send_date ";
		}

		if(!empty($post_data['recv_flg'])){
			$array[':recv_flg']	 = $post_data['recv_flg'];
			$sql				.= "AND m.recv_flg = :recv_flg ";
		}else{
			$array[':recv_flg']	 = 2;
			$sql				.= "AND m.recv_flg <= :recv_flg ";
		}

		if(!empty($post_data['send_flg'])){
			$array[':send_flg']	 = $post_data['send_flg'];
			$sql				.= "AND m.send_flg = :send_flg ";
		}

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']	 = $post_data['last_flg'];
			$sql				.= "AND m.last_flg = :last_flg ";
		}

		$sql					.= "AND m.del_flg = :del_flg";

		# GROUP
		if(!empty($post_data['group'])){
			$sql				.= " GROUP BY m.".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql				.= " ORDER BY m.".$post_data['order'];
		}

		# LIMIT
		if(!empty($post_data['limit'])){
			$sql				.= " LIMIT ".$post_data['limit'];
		}

		if(!empty($post_data['list'])){
			$limit				 = $post_data['set'].",".$post_data['list'];
		}


		$rtn					 = NULL;
		$result					 = NULL;

		# REMOVE TAGS
		$array					 = $database->removeTags($array);

		try{
			$rtn				 = $database->prepare($sql,$array,$debug=1);
	 		$result				 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}

		return $rtn;

	}



	/**************************************************
	**
	**	getNoReadCount
	**	----------------------------------------------
	**	未読メールカウント
	**
	**************************************************/

	public function getNoReadCount($user_id,$character_id=NULL){

		# ERROR
		if(empty($user_id)){
			return FALSE;
		}

		$array						= array();

		if(!empty($character_id)){

			$array					= array(
				'user_id'			=> $user_id,
				'character_id'		=> $character_id,
				'recv_flg'			=> 1,
				//'last_flg'			=> 0,
				'group'				=> NULL,
				'type'				=> 1 // ユーザー受け取り
			);

		}else{

			$array					= array(
				'user_id'			=> $user_id,
				'recv_flg'			=> 1,
				//'last_flg'			=> 0,
				'group'				=> NULL,
				'type'				=> 1 // ユーザー受け取り
			);

		}

		$column						= "id";
		$rtn						= $this->getMailList($array,$column);

		# DB / MAIN CLASS
		$database					= NULL;
		$database					= $this->database;
		$output						= NULL;
		$output						= $this->output;

		$rows						= 0;
		$rows						= $database->numRows($rtn);

		return $rows;


	}



	/*********************************************
	**
	**	MAIL 送信時 %変換 処理の統合
	**	------------------------------------------
	**	sendUserReplace
	**	sendCityReplace
	**	sendMiracleReplace
	**
	*********************************************/

	public function sendAllReplace($str,$name,$user_data,$mailusers_data) {

		/*
		$this->user_data['id']			= $user_data['id'];
		$this->user_data['site_cd']		= $user_data['site_cd'];
		$this->user_data['pref']		= $user_data['pref'];
		$this->user_data['city']		= $user_data['city'];
		*/

		# チェック
		if(preg_match("/％１/",$str)){ $str = str_replace("％１","%1",$str); }
		if(preg_match("/％1/",$str)){ $str = str_replace("％1","%1",$str); }
		if(preg_match("/%１/",$str)){ $str = str_replace("%１","%1",$str); }

		if(preg_match("/％２/",$str)){ $str = str_replace("％２","%2",$str); }
		if(preg_match("/％2/",$str)){ $str = str_replace("％2","%2",$str); }
		if(preg_match("/%２/",$str)){ $str = str_replace("%２","%2",$str); }

		if(preg_match("/％３/",$str)){ $str = str_replace("％３","%3",$str); }
		if(preg_match("/％3/",$str)){ $str = str_replace("％3","%3",$str); }
		if(preg_match("/%３/",$str)){ $str = str_replace("%３","%3",$str); }

		if(preg_match("/％４/",$str)){ $str = str_replace("％４","%4",$str); }
		if(preg_match("/％4/",$str)){ $str = str_replace("％4","%4",$str); }
		if(preg_match("/%４/",$str)){ $str = str_replace("%４","%4",$str); }

		if(preg_match("/％５/",$str)){ $str = str_replace("％５","%5",$str); }
		if(preg_match("/％5/",$str)){ $str = str_replace("％5","%5",$str); }
		if(preg_match("/%５/",$str)){ $str = str_replace("%５","%5",$str); }

		if(preg_match("/％６/",$str)){ $str = str_replace("％６","%6",$str); }
		if(preg_match("/％6/",$str)){ $str = str_replace("％6","%6",$str); }
		if(preg_match("/%６/",$str)){ $str = str_replace("%６","%6",$str); }

		if(preg_match("/％７/",$str)){ $str = str_replace("％７","%7",$str); }
		if(preg_match("/％7/",$str)){ $str = str_replace("％7","%7",$str); }
		if(preg_match("/%７/",$str)){ $str = str_replace("%７","%7",$str); }

		if(preg_match("/％８/",$str)){ $str = str_replace("％８","%8",$str); }
		if(preg_match("/％8/",$str)){ $str = str_replace("％8","%8",$str); }
		if(preg_match("/%８/",$str)){ $str = str_replace("%８","%8",$str); }

		if(preg_match("/％９/",$str)){ $str = str_replace("％９","%9",$str); }
		if(preg_match("/％9/",$str)){ $str = str_replace("％9","%9",$str); }
		if(preg_match("/%９/",$str)){ $str = str_replace("%９","%9",$str); }

		if(preg_match("/％ｒ/",$str)){ $str = str_replace("％ｒ","%r",$str); }
		if(preg_match("/％r/",$str)){ $str = str_replace("％r","%r",$str); }
		if(preg_match("/%ｒ/",$str)){ $str = str_replace("%ｒ","%r",$str); }

		if(preg_match("/％ｍ/",$str)){ $str = str_replace("％ｍ","%m",$str); }
		if(preg_match("/％m/",$str)){ $str = str_replace("％r","%m",$str); }
		if(preg_match("/%ｍ/",$str)){ $str = str_replace("%ｍ","%m",$str); }
		

		/*
		if(preg_match("/%r/",$str)){ 
			$str = $this->sendCityReplace($str,$user_data['city'],$user_data);
		}
		if(preg_match("/%m/",$str)){ 
			$str = $this->sendMiracleReplace($str,$user_data['city'],$mailusers_data,$user_data);
		}
		*/

		if(preg_match("/%/",$str)){ 
			$str = $this->sendUserReplace($str,$name,$user_data,$mailusers_data);
		}

		return $str;

	}


	/*********************************************
	**
	**	MAIL USER情報 %変換 処理
	**
	*********************************************/

	function sendUserReplace($str,$name,$user_data,$mailusers_data) {

		//global $db,$pref_array;

		# USER NAME
		$name_pattern	= array ('/%1ちゃん/','/%1さん/','/%1くん/');
		$name_replace	= array ('%1','%1','%1');

		if(preg_match("/%1さん|%1ちゃん|%1くん/",$str) && preg_match("/さん|ちゃん|くん/",$name)){
			$str = preg_replace($name_pattern,$name_replace,$str);
		}

		if(preg_match("/%1/",$str)){ $str = str_replace("%1",$name,$str); }

		/*
		# USER PREF
		if(preg_match("/%(2|4)/",$str)){

			# MAILINFOS PREF
			if($mailusers_data['pref']){

				# 都道府県
				$str = str_replace("%2",$mailusers_data['pref'],$str);

				# PREF ARRAY
				$rep_pref = $pref_array[(int)$user_data['pref']][1];

				#お知らせ用の％変換に渡す用	20141128takeuchi
				$this->user_data['str_pref']	= $rep_pref;

				# 都道府県抜き
				$data_pref_cnt = mb_strlen($rep_pref,"UTF-8");

				# 北海道はスルー
				if((int)$user_data['pref'] != 1){

					if($data_pref_cnt == 3 ){
						$rep_pref = mb_substr($rep_pref,0,2,"UTF-8");
					}elseif($data_pref_cnt == 4 ){
						$rep_pref = mb_substr($rep_pref,0,3,"UTF-8");
					}

				}

				$str = str_replace("%4",$rep_pref,$str);


			# NO PREF -> DEFAULT PREF
			}elseif($user_data['pref'] == "0"){

				$magicles		= new magicles();
				$default_magic	= $magicles->getDefaultPrefCity($user_data['site_cd']);

				#お知らせ用の％変換に渡す用	20141128takeuchi
				$this->user_data['str_pref']	= $default_magic['per_name'];

				# 都道府県
				$str = str_replace("%2",$default_magic['per_name'],$str);
				$str = str_replace("%4",$default_magic['per_name'],$str);


			# NORMAL PREF
			}elseif(isset($user_data['pref'])){

				# PREF ARRAY
				$rep_pref = $pref_array[(int)$user_data['pref']][1];

				#お知らせ用の％変換に渡す用	20141128takeuchi
				$this->user_data['str_pref']	= $rep_pref;

				# 都道府県
				$str = str_replace("%2",$rep_pref,$str);

				# 都道府県抜き
				$data_pref_cnt = mb_strlen($rep_pref,"UTF-8");

				# 北海道はスルー
				if((int)$user_data['pref'] != 1){

					if($data_pref_cnt == 3 ){
						$rep_pref = mb_substr($rep_pref,0,2,"UTF-8");
					}elseif($data_pref_cnt == 4 ){
						$rep_pref = mb_substr($rep_pref,0,3,"UTF-8");
					}

				}

				$str = str_replace("%4",$rep_pref,$str);

			}else{
				return "error";
			}
		}


		# USER CITY
		if(preg_match("/%(3|5)/",$str)){

			# USER MAILINFOS CITY
			if($mailusers_data['city']){

				$city_name	= $mailusers_data['city'];
				$per_name	= $mailusers_data['city'];

			# NORMAL CITY
			}elseif($user_data['city'] == "0"){

				$magicles		= new magicles();
				$default_magic	= $magicles->getDefaultPrefCity($user_data['site_cd']);

				$city_name	= $default_magic['magic_0'];
				$per_name	= $default_magic['magic_0'];

				#お知らせ用の％変換に渡す用	20141128takeuchi
				$this->user_data['str_city']	= $city_name;

			# NORMAL CITY
			}elseif(isset($user_data['city'])){

				$sql	= "SELECT magic_0,per_name FROM magicles WHERE city = '".$user_data['city']."' AND site_cd = '".$user_data['site_cd']."'";

				$rtn	= $db->Query($sql);
				$db->errorDb($sql,$db->errno,__FILE__,__LINE__);

				$data		= $db->fetchObj($rtn);
				$city_name	= $data->magic_0;
				$per_name	= $data->per_name;

				#お知らせ用の％変換に渡す用	20141128takeuchi
				$this->user_data['str_city']	= $city_name;

			}else{
				return "error";
			}


			# mailusersにmagicが設定されていたら %5はmailusers優先
			if(!empty($mailusers_data['magic'])){
				$city_name2 = $mailusers_data['magic'];
			}else{
				#市区町村抜き
				$city_name2	= str_replace("区","",$per_name);
			}

			$str = str_replace("%3",$city_name,$str);
			$str = str_replace("%5",$city_name2,$str);

		}
		*/


		# 年齢
		if(preg_match("/%6/",$str)){ $str = str_replace("%6",$user_data['age'],$str); }
		
		# ID
		if(preg_match("/%7/",$str) && strlen($user_data['id'])){ $str = str_replace("%7",$user_data['id'],$str); }

		# PASS
		if(preg_match("/%8/",$str) && strlen($user_data['user_ps'])){ $str = str_replace("%8",$user_data['user_ps'],$str); }

		# SEC PASS
		if(preg_match("/%9/",$str) && strlen($user_data['id']) && strlen($user_data['user_ps'])){
			$sec_pass	= substr(md5($user_data['id'].$user_data['user_ps']),7,16);
			$str		= str_replace("%9",$sec_pass,$str);
		}

		# ERROR CHECK
		if(preg_match("/%(1|2|3|4|5|6|7|8|9)/", $str)){ return "error"; }

		return $str;

	}


}

?>