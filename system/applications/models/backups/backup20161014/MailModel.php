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

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']		 = $post_data['last_flg'];
		}else{
			$array[':last_flg']		 = 0;
		}

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

			$where					.= "AND recv_id = :user_id ";
			if(!empty($post_data['character_id'])){
				$where				.= "AND send_id = :character_id ";
			}

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

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND send_date >= :send_date ";
		}

		$where					.= "AND last_flg = :last_flg ";
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

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']	 = $post_data['last_flg'];
		}else{
			$array[':last_flg']	 = 0;
		}

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

			$where				.= "AND recv_id = :user_id ";
			if(!empty($post_data['character_id'])){
				$where			.= "AND send_id = :character_id ";
			}

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

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND send_date >= :send_date ";
		}

		$where					.= "AND last_flg = :last_flg ";
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

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']	 = $post_data['last_flg'];
		}else{
			$array[':last_flg']	 = 0;
		}

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

			$where				.= "AND recv_id = :user_id ";
			if(!empty($post_data['character_id'])){
				$where			.= "AND send_id = :character_id ";
			}

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

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND send_date >= :send_date ";
		}

		$where					.= "AND last_flg = :last_flg ";
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
		$array[':last_flg']		 = 0;
		$array[':del_flg']		 = 0;

		$where					 = "site_cd = :site_cd ";
		$where					.= "AND id = :id ";
		$where					.= "AND last_flg = :last_flg ";
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

		# LAST FLG
		if(!empty($post_data['last_flg'])){
			$array[':last_flg']	 = $post_data['last_flg'];
		}else{
			$array[':last_flg']	 = 0;
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

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$where				.= "AND m.send_date >= :send_date ";
		}

		$sql					.= "AND m.last_flg = :last_flg ";
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
				'last_flg'			=> 0,
				'group'				=> NULL,
				'type'				=> 1 // ユーザー受け取り
			);

		}else{

			$array					= array(
				'user_id'			=> $user_id,
				'recv_flg'			=> 1,
				'last_flg'			=> 0,
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


}

?>