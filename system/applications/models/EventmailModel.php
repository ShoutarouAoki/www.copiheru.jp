<?php
/********************************************************************************
**	
**	EventmailModel.php
**	=============================================================================
**
**	■PAGE / 
**	EVENTMAIL MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	EVENTMAIL CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	EVENTMAIL CLASS
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: KARAT SYSTEM
**	CREATE DATE : 2017/07/13
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
namespace Models;

use Libs\Database;

class Eventmail{
	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	const TABLE = "event_mails";
	private	$output;

	# CONSTRUCT
	function __construct($main=NULL){
		$this->output		= $main;
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getEventMailList
	**	----------------------------------------------
	**	メール一覧
	**	----------------------------------------------
	**  $event_id : イベントID
	**  
	**	$post_data['type'] : 1
	**	ユーザーが受け取り
	**
	**	$post_data['type'] : 2
	**	キャラが受け取り
	**
	**************************************************/

	public function getEventMailList($event_id, $post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id']) || empty($post_data['type'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column					= "*";
		}

		# DB / MAIN CLASS
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array = [
			':site_cd' => SITE_CD,
			':user_id' => $post_data['user_id'],
			':del_flg' => 0,
			':event_id' => $event_id
		];

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

		$where					.= "AND del_flg = :del_flg ";

		# イベントID
		$where					.= "AND event_id = :event_id ";
		
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

		$rtn					 = Database::selectDb(self::TABLE,$column,$where,$array,$order,$limit,$group,1);
		$error					 = Database::errorDb("GET EVENTMAIL LIST",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getEventMailCount
	**	----------------------------------------------
	**	メールカウント
	**	----------------------------------------------
	**  $event_id : イベントID
	**  
	**	$post_data['type'] : 1
	**	ユーザーが受け取り
	**
	**	$post_data['type'] : 2
	**	キャラが受け取り
	**
	**************************************************/

	public function getEventMailCount($event_id,$post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id']) || empty($post_data['type'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				= "id";
		}

		# DB / MAIN CLASS
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];
		$array[':del_flg']		 = 0;
		$array[':event_id']		 = $event_id;
		
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

		$where					.= "AND del_flg = :del_flg ";

		# イベントID
		$where					.= "AND event_id = :event_id ";
		
		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;

		$rtn					 = Database::selectDb(self::TABLE,$column,$where,$array,$order,$limit,$group,1);
		$error					 = Database::errorDb("GET EVENTMAIL COUNT",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows					 = Database::numRows($rtn);

		Database::freeResult($rtn);

		return $rows;


	}



	/**************************************************
	**
	**	getEventMailData
	**	----------------------------------------------
	**	メール取得
	**	----------------------------------------------
	**  $event_id : イベントID
	**  
	**	$post_data['type'] : 1
	**	ユーザーが受け取り
	**
	**	$post_data['type'] : 2
	**	キャラが受け取り
	**
	**************************************************/

	public function getEventMailData($event_id,$post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id']) || empty($post_data['type'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				= "*";
		}

		# DB / MAIN CLASS
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];
		$array[':del_flg']		 = 0;
		$array[':event_id']		 = $event_id;

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

		$where					.= "AND del_flg = :del_flg ";

		# イベントID
		$where					.= "AND event_id = :event_id ";
		
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;

		if(!empty($post_data['order'])){
			$order				 = $post_data['order'];
		}

		if(!empty($post_data['group'])){
			$group				 = $post_data['group'];
		}

		$rtn					 = Database::selectDb(self::TABLE,$column,$where,$array,$order,$limit,$group,1);
		$error					 = Database::errorDb("GET EVENTMAIL DATA",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= Database::fetchAssoc($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getEventMailDataById
	**	----------------------------------------------
	**	メール取得
	**
	**  $event_id : イベントID
	**  
	**************************************************/

	public function getEventMailDataById($event_id,$post_data,$column=NULL){
		
		# ERROR
		if(empty($post_data['id'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column				= "*";
		}

		# DB / MAIN CLASS
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':id']			 = $post_data['id'];
		$array[':del_flg']		 = 0;
		$array[':event_id']		 = $event_id;
		
		$where					 = "site_cd = :site_cd ";
		$where					.= "AND id = :id ";
		$where					.= "AND del_flg = :del_flg ";
		$where					.= "AND event_id = :event_id ";
		
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;

//		mail("eikoshi@k-arat.co.jp","EVENTMAIL2-c[READ]",var_export($array, true),"From:info@mailanime.net");

		$rtn					 = Database::selectDb(self::TABLE,$column,$where,$array,$order,$limit,$group,1);
		$error					 = Database::errorDb("getEventMailDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= Database::fetchAssoc($rtn);

		//mail("eikoshi@k-arat.co.jp","EVENTMAIL2-d[READ]",var_export($data, true),"From:info@mailanime.net");
		return $data;


	}



	/**************************************************
	**
	**	getUserReceiveEventMailListJoinOnMailusers
	**	----------------------------------------------
	**	ユーザー受信メール一括取得
	**	----------------------------------------------
	**	mailusersとJOIN
	**	ユーザーの受け取り専用
	**
	**	$event_id : イベントID
	**  
	**************************************************/

	public function getUserReceiveEventMailListJoinOnMailusers(
		$event_id,$post_data,$column=NULL){

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
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':user_id']		 = $post_data['user_id'];
		$array[':event_id']		 = $event_id;

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
		$sql					.= "INNER JOIN ".self::TABLE." m ";

		# ON
		$sql					.= "ON u.user_id = m.recv_id ";
		$sql					.= "AND u.site_cd = :site_cd ";
		$sql					.= "AND u.send_id = m.send_id ";

		# WHERE
		$sql					.= "WHERE u.status = :status ";
		$sql					.= "AND u.user_id = :user_id ";

		if(!empty($post_data['character_id'])){
			$array[':character_id']	 = $post_data['character_id'];
			$sql				.= "AND u.send_id = :character_id ";
		}

		if(!empty($post_data['send_date'])){
			$array[':send_date']	 = $post_data['send_date'];
			$sql				.= "AND m.send_date >= :send_date ";
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

		$sql					.= "AND m.del_flg = :del_flg ";

		# イベントID
		$sql					.= "AND event_id = :event_id ";
		
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
		$array					 = Database::removeTags($array);

		try{
			$rtn				 = Database::prepare($sql,$array,$debug=1);
	 		$result				 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				Database::$debug_query	.= print_r($e->getTrace());
				Database::$debug_query	.= "\n<hr class=\"query_line\" />\n";
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
	**  $event_id : イベントID
	**  
	**************************************************/

	public function getNoReadCount($event_id,$user_id,$character_id=NULL){

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
				'event_id'			=> $event_id,//イベントID
				'type'				=> 1 // ユーザー受け取り
			);

		}else{

			$array					= array(
				'user_id'			=> $user_id,
				'recv_flg'			=> 1,
				//'last_flg'			=> 0,
				'group'				=> NULL,
				'event_id'			=> $event_id,//イベントID
				'type'				=> 1 // ユーザー受け取り
			);

		}

		$column						= "id";
		$rtn						= $this->getEventMailList($event_id,$array,$column);

		# DB / MAIN CLASS
		$output						= NULL;
		$output						= $this->output;

		$rows						= 0;
		$rows						= Database::numRows($rtn);

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
		# USER NAME
		$name_pattern	= array ('/%1ちゃん/','/%1さん/','/%1くん/');
		$name_replace	= array ('%1','%1','%1');

		if(preg_match("/%1さん|%1ちゃん|%1くん/",$str) && preg_match("/さん|ちゃん|くん/",$name)){
			$str = preg_replace($name_pattern,$name_replace,$str);
		}

		if(preg_match("/%1/",$str)){ $str = str_replace("%1",$name,$str); }

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

	# イベントメール画面で一件もメールがない場合、デフォで用意したメールを送信する
	# 返し値は、-1が設定がない、1の場合はアンロック済み
	# $members_data:ユーザデータ
	# $list_data:キャラデータ
	# $event_mail_data:イベントメールデータ
	# $event_mail_settings:イベントメールのキャラ設定
	function sendFirstmail($members_data, $list_data, $event_mail_settings){
		$insert_subject = $this->sendAllReplace($event_mail_settings['title_1st'],$members_data['nickname'],$members_data,NULL);
		$insert_message = $this->sendAllReplace($event_mail_settings['message_1st'],$members_data['nickname'],$members_data,NULL);

		# 【INSERT】event_mails
		Database::insertDb("event_mails",[
			'site_cd'					=> SITE_CD,
			'event_id'					=> $event_mail_settings['event_id'],
			'send_id'					=> $list_data['send_id'],
			'recv_id'					=> $members_data['id'],
			'send_date'					=> date("YmdHis"),
			'title'						=> $insert_subject,
			'message'					=> $insert_message,
			'recv_flg'					=> 1,
			'naruto'						=> $list_data['naruto'],
			'op_id'						=> $list_data['op_id'],
			'owner_id'					=> $list_data['owner_id']
		]);
	}

	function event_adjustment($tmp_eventdata,$members_data,$list_data){
		// 未読メール件数確認
		$eventmail_recv_rtn = $this->getEventMailList($tmp_eventdata['event_id'],[
			'type' => 1,
			'character_id' => $list_data['send_id'],
			'user_id' => $members_data["id"]
		],'count(*)');
		$eventmail_recv_rows = Database::fetchAssoc($eventmail_recv_rtn);

		// イベントメールが存在すれば未読数のみ返却
		if($eventmail_recv_rows['count(*)'] > 0){
			$list_data['no_read_eventmail'] = $this->getNoReadCount($tmp_eventdata['event_id'],$members_data['id'],$list_data['send_id']);
			return $list_data;
		}

		// キャラクターがまだ1回も送信していない場合、ここで送信してしまう
		// INSERTを行うのでMASTERに切り替え
		if(empty(Database::checkAuthority())){
			Database::closeDb();
			Database::connectDb(MASTER_ACCESS_KEY);
			$db_check = 1;
		}
		
		// HTMLタグ調整
		$tmp_eventdata['title_1st'] = set3pSystem_Otsu($tmp_eventdata['title_1st']);
		$tmp_eventdata['message_1st'] = set3pSystem_Otsu($tmp_eventdata['message_1st']);
		$list_data['no_read_eventmail'] = 1;
		
		// 初回イベントメール送信
		$this->sendFirstmail($members_data, $list_data, $tmp_eventdata);

		// SLAVEに接続し直す
		if(!empty($db_check)){
			Database::closeDb();
			Database::connectDb();
		}

		return $list_data;
	}
}

?>