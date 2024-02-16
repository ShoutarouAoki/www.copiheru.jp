<?php
/********************************************************************************
**	
**	GachaModel.php
**	=============================================================================
**
**	■PAGE / 
**	GACHA MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	GACHA CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	GACHA CLASS
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
class GachaModel{


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
	private	$table;
	
	//ステップアップガチャ用
	//20180314 add by A.cos
	private	$table_stepup;
	private	$table_stepup_setting;
	private	$table_stepup_users;

	# CONSTRUCT
	function __construct($database=NULL,$main=NULL){
		$this->database		= $database;
		$this->output		= $main;
		$this->table		= "gachas";

		//ステップアップガチャ用
		//20180314 add by A.cos
		$this->table_stepup = "gachas_stepup";
		$this->table_stepup_setting = "gachas_stepup_setting";
		$this->table_stepup_users = "gachas_stepup_user";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getGachaList
	**	----------------------------------------------
	**	ショップデータリスト
	**
	**************************************************/

	public function getGachaList($post_data,$column=NULL){

		if(empty($column)){
			$column					 = "*";
		}

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}else{
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = 0;
		}

		if(isset($post_data['type'])){
			$where					.= "AND type = :type ";
			$array[':type']			 = $post_data['type'];
		}

		# 0%は絶対出さない
		$where						.= "AND percent > :percent ";
		$array[':percent']			 = 0;

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status = :status ";
			$array[':status']		 = 0;
		}

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		if(!empty($post_data['order'])){
			$order					 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit					 = $post_data['limit'];
		}

		if(!empty($post_data['list'])){
			$limit					 = $post_data['set'].",".$post_data['list'];
		}

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getGachaList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getGachaDataById
	**	----------------------------------------------
	**	ガチャ情報取得
	**
	**************************************************/

	public function getGachaDataById($id,$column=NULL){

		if(empty($id)){
			return FALSE;
		}

		if(empty($column)){
			$column				 = "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':id']			 = $id;

		$where					 = "id = :id";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getGachaDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}

//******** ステップアップガチャ用 ********
//20180314 add by A.cos

	/**************************************************
	**
	**	getStepupGachaList
	**	----------------------------------------------
	**	ステップアップガチャデータリスト
	**
	**************************************************/
	public function getStepupGachaList($post_data,$column=NULL){

		if(empty($column)){
			$column					 = "*";
		}

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		//キャンペーンID
		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}else{
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = 0;
		}

		//限定orステップアップ
		if(isset($post_data['use_flg'])){
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = $post_data['use_flg'];
		}else{
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = 0;
		}

		//ノーマルor確定
		if(isset($post_data['absolute'])){
			$where					.= "AND absolute = :absolute ";
			$array[':absolute']	 = $post_data['absolute'];
		}else{
			$where					.= "AND absolute = :absolute ";
			$array[':absolute']	 = 0;
		}

		//景品タイプ
		if(isset($post_data['type'])){
			$where					.= "AND type = :type ";
			$array[':type']			 = $post_data['type'];
		}

		# 0%は絶対出さない
		$where						.= "AND percent > :percent ";
		$array[':percent']			 = 0;

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$where					.= "AND status = :status ";
			$array[':status']		 = 0;
		}

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		if(!empty($post_data['order'])){
			$order					 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit					 = $post_data['limit'];
		}

		if(!empty($post_data['list'])){
			$limit					 = $post_data['set'].",".$post_data['list'];
		}

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table_stepup,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getStepupGachaList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getStepupGachaDataById
	**	----------------------------------------------
	**	ステップアップガチャ情報取得
	**
	**************************************************/
	public function getStepupGachaDataById($id,$column=NULL){

		if(empty($id)){
			return FALSE;
		}

		if(empty($column)){
			$column				 = "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':id']			 = $id;

		$where					 = "id = :id";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table_stepup,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getStepupGachaDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;
	}

	/**************************************************
	**
	**	getStepupGachaSetting
	**	----------------------------------------------
	**	ステップアップガチャ、設定の取得
	**
	**************************************************/
	public function getStepupGachaSetting($post_data,$column=NULL){
		
		# PARAMETER
		if(empty($column)){
			$column				 = "*";
		}

		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		$where = "site_cd = :site_cd ";

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		//キャンペーンID
		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}else{
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = 0;
		}
		//限定orステップアップ
		if(isset($post_data['use_flg'])){
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = $post_data['use_flg'];
		}else{
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = 0;
		}
		//段階
		if(isset($post_data['phase'])){
			$where					.= "AND phase = :phase ";
			$array[':phase']	 = $post_data['phase']+1;
		}else{
			$where					.= "AND phase = :phase ";
			$array[':phase']	 = 0;
		}

		$where					.= "AND status = 0 ";
		$order = NULL;
		$limit = NULL;
		$group = NULL;

		$rtn						 = $database->selectDb($this->table_stepup_setting,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getStepupGachaSetting",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }
		$data					= $database->fetchAssoc($rtn);
		$database->freeResult($rtn);

		return $data;


	}

	
	/**************************************************
	**
	**	getStepupGachaPhaseMax
	**	----------------------------------------------
	**	ステップアップガチャ、チャレンジ限度数取得
	**	- 指定したキャンペーンIDと使用タイプのチャレンジ限度数を返す
	**
	**************************************************/
	public function getStepupGachaPhaseMax($post_data){
		# PARAMETER
		if(empty($column)){
			$column				 = "count(*) as gachamax";
		}
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		//サイトコード
		$where = "site_cd = :site_cd ";

		//キャンペーンID
		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}else{
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = 0;
		}
		//限定orステップアップ
		if(isset($post_data['use_flg'])){
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = $post_data['use_flg'];
		}else{
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = 0;
		}
		$where					.= "AND status = 0 ";
		$order = NULL;
		$limit = NULL;
		$group = NULL;

		$rtn						 = $database->selectDb($this->table_stepup_setting,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getStepupGachaPhaseMax",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }
		$data					= $database->fetchAssoc($rtn);
		$database->freeResult($rtn);

		return $data["gachamax"];//該当ガチャを引ける回数
}

	/**************************************************
	**
	**	checkUserOnStepupGacha
	**	----------------------------------------------
	**	ステップアップガチャ、ユーザ情報のチェック
	**	各ユーザの使用回数が使用限度まで達してないか
	**	そもそもユーザ情報があるかないか、なければ作成
	**	->使用限度がオーバーしていなかったらユーザの回数とガチャ限度数を返す(新規でユーザ情報を作った場合は)
	**	->使用限度をオーバーしていたら-1とガチャ限度数を返す
	**
	**************************************************/
	public function checkUserOnStepupGacha($user_id, $post_data){

		//該当ガチャのチャレンジ限度数を取得
		$gachamax = $this->getStepupGachaPhaseMax($post_data);
		
/*
		######## 該当ガチャを引ける回数を取得 ########
		$sql_inner = "SELECT count(*) as gachamax FROM ".$this->table_stepup_setting." ";
		$sql_inner .= "WHERE site_cd = ".SITE_CD." ";
		$sql_inner .= "AND campaign_id = ".$post_data['campaign_id']." ";
		$sql_inner .= "AND use_flg = ".$post_data['use_flg']." ";
		$sql_inner .= "AND status = 0 ";
*/

		######## ユーザが該当ガチャを引いた回数を取得 ########
		# PARAMETER(リセット)
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$column = "*";
		$where = "site_cd = :site_cd ";

		//キャンペーンID
		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}else{
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = 0;
		}
		//限定orステップアップ
		if(isset($post_data['use_flg'])){
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = $post_data['use_flg'];
		}else{
			$where					.= "AND use_flg = :use_flg ";
			$array[':use_flg']	 = 0;
		}
		$where					.= "AND user_id = ".$user_id;
		$order = NULL;
		$limit = NULL;
		$group = NULL;

		$rtn						 = $database->selectDb($this->table_stepup_users,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("checkUserOnStepupGacha",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }
		$data					= $database->fetchAssoc($rtn);
		$database->freeResult($rtn);

		$userphase = 0;
		if(isset($data["id"])){
			$userphase = $data["phase"];
		}else{
			# DATABASE CHANGE / スレーブだったらマスターに接続
			$db_auth = $database->checkAuthority();
			$slave_flg=0;
			if(empty($db_auth)){
				# CLOSE DATABASE SLAVE
				$database->closeDb();
				# CONNECT DATABASE MASTER
				$database->connectDb(MASTER_ACCESS_KEY);
				$slave_flg=1;
			}
			# トランザクションスタート
			$database->beginTransaction();

			# 当ユーザの当ステップアップガチャの使用データ作成
			$userdata['site_cd']			= SITE_CD;
			$userdata['campaign_id']		= $post_data['campaign_id'];
			$userdata['use_flg']			= $post_data['use_flg'];
			$userdata['user_id']			= $user_id;
			$database->insertDb($this->table_stepup_users,$userdata);
			$error		= $database->errorDb("checkUserOnStepupGacha(Insert User)",NULL,__FILE__,__LINE__);
			if(!empty($error)){ $output->outputError($error); }
			$userphase = 0;

			# コミット
			$database->commit();

			if($slave_flg){
				# CLOSE DATABASE
				$database->closeDb();
				# CONNECT DATABASE SLAVE
				$database->connectDb();
			}
		}

		# 使用限度がオーバーしていなかったらユーザの回数を返す
		if($userphase<$gachamax){
			return array($userphase, $gachamax);
		}else{
		# 使用限度をオーバーしていたら-1を返す
			return array(-1, $gachamax);
		}
	}

	
	/**************************************************
	**
	**	countupUserPhaseOnStepupGacha
	**	----------------------------------------------
	**	ステップアップガチャ、ユーザ情報のガチャ回数カウントアップ
	**	返し値はカウントアップ後のガチャ回数
	**
	**************************************************/
	public function countupUserPhaseOnStepupGacha($user_id, $post_data){
		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;
		
		# カウントアップ
		$sql	 = "UPDATE ".$this->table_stepup_users." SET ";
		$sql	.= "phase = phase + 1 ";
		$sql	.= "WHERE site_cd = ".SITE_CD." AND campaign_id = ".$post_data['campaign_id']." ";
		$sql	.= "AND use_flg = ".$post_data['use_flg']." AND user_id = ".$user_id;
		$rtn	= $database->query($sql);
		//$error	 = $database->errorDb("VISITORS COUNTER",$rtn->errorCode(),__FILE__,__LINE__);
		//if(!empty($error)){ $output->outputError($error); }
		
		//カウントアップ後のガチャ回数を返す
		return $this->checkUserOnStepupGacha($user_id, $post_data);
	}

	/**************************************************
	**
	**	resetUserPhaseOnStepupGacha
	**	----------------------------------------------
	**	ステップアップガチャ、ユーザ情報のガチャ回数リセット（＝０）
	**	返し値なし
	**
	**************************************************/
	public function resetUserPhaseOnStepupGacha($user_id, $post_data){
		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;
		
		if(!($post_data['use_flg']==201 || $post_data['use_flg']==202 || $post_data['use_flg']==201)){
			//ステップアップ以外は以下の処理をしない
			return;
		}
		# カウントアップ
		$sql	 = "UPDATE ".$this->table_stepup_users." SET ";
		$sql	.= "phase = 0 ";
		$sql	.= "WHERE site_cd = ".SITE_CD." AND campaign_id = ".$post_data['campaign_id']." ";
		$sql	.= "AND use_flg = ".$post_data['use_flg']." AND user_id = ".$user_id;
		$rtn	= $database->query($sql);
		//$error	 = $database->errorDb("VISITORS COUNTER",$rtn->errorCode(),__FILE__,__LINE__);
		//if(!empty($error)){ $output->outputError($error); }
		
		return;
	}
}
?>