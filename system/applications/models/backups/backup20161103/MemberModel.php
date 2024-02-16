<?php
/********************************************************************************
**	
**	MemberModel.php
**	=============================================================================
**
**	■PAGE / 
**	MEMBER MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	MEMBER CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	MEMBER CLASS
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
class MemberModel{


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
		$this->table		= "members";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	checkAdminCertify
	**	----------------------------------------------
	**	常時ログイン認証
	**
	**************************************************/

	public function checkMemberCertify($account){


		/*********************************************
		**
		**	THROUGH DIRECTORY
		**	-----------------------------------------
		**	認証回避
		**
		*********************************************/

		$result		= NULL;
		$result		= array();

		# NULL ERROR
		if(empty($account['member_id']) && empty($account['user_id']) && empty($account['user_pass'])){
			return FALSE;
		}

		# NULL ERROR
		if(empty($account['member_id']) || empty($account['user_id']) || empty($account['user_pass'])){
			$result['error']	= 5;
		}

		if(preg_match("/<.*?>/",$account['member_id'])){
			$result['error']	= 4;
		}

		if(preg_match("/<.*?>/",$account['user_id'])){
			$result['error']	= 4;
		}

		if(preg_match("/<.*?>/",$account['user_pass'])){
			$result['error']	= 4;
		}

		if(preg_match("/;/",$account['member_id']) || preg_match("/&gt/",$account['member_id']) || preg_match("/&lt/",$account['member_id'])){
			$result['error']	= 4;
		}

		if(preg_match("/;/",$account['user_id']) || preg_match("/&gt/",$account['user_id']) || preg_match("/&lt/",$account['user_id'])){
			$result['error']	= 4;
		}

		if(preg_match("/;/",$account['user_pass']) || preg_match("/&gt/",$account['user_pass']) || preg_match("/&lt/",$account['user_pass'])){
			$result['error']	= 4;
		}

		# TIME OUT ERROR
		if(!empty($result['error'])){
			return $result;
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;
		$array[':member_id']	 = $account['member_id'];
		$array[':user_id']		 = $account['user_id'];
		$array[':status']		 = 8;

		$column					 = "*";
		$where					 = "id = :member_id ";
		$where					.= "AND site_cd = :site_cd ";
		$where					.= "AND user_id = :user_id ";
		$where					.= "AND status < :status";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;

		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("CHECK USER CERTIFY",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$result					 = $database->fetchAssoc($rtn);


		/*********************************************
		**
		**	ACCOUNT CHECK OK
		**	-----------------------------------------
		**	データ取得
		**
		*********************************************/

		if(!empty($result)){


			/*********************************************
			**
			**	PASSWORD CHECK
			**	-----------------------------------------
			**	パスワードチェック
			**
			*********************************************/

			$check_pass	 = $this->makePass($result['id'],$result['user_ps']);

			if($account['user_pass'] !== $check_pass){

				$result['error']						= 1;


			/*********************************************
			**
			**	USER ACCOUNT DATA CREATE
			**	-----------------------------------------
			**	ユーザー情報生成
			**
			*********************************************/

			}else{

				# 総持ちポイント(チケット)
				$result['total_point']					= $result['point'] + $result['s_point'] + $result['f_point'];

				# 持ちポイント　有料+インセンポイント
				$result['user_point']					= $result['point'] + $result['s_point'];


				/************************************************
				**
				**	初回ログインから何日目か
				**	============================================
				**	
				**
				************************************************/

				# 今日
				$today									= date("Ymd");

				# 初回ログインから何日目か
				$first_login_date						= date("Ymd",strtotime($result['first_login_date']));
				$first_login_timestamp					= strtotime($first_login_date);
				$today_timestamp						= strtotime($today);

				# 何秒離れているかを計算
				$seconddiff								= abs($today_timestamp - $first_login_timestamp);

				# 日数に変換
				$login_day								= $seconddiff / (60 * 60 * 24);

				# 何日目かだと1日ずれるので
				$result['login_day']					= $login_day + 1;



				/************************************************
				**
				**	最終アクセスチェック
				**	============================================
				**	サイト滞在中に日付が変わる場合を考慮して
				**
				************************************************/

				# 最終アクセスチェック
				$last_access_date						= date("Ymd",strtotime($result['access_date']));

				if($today != $last_access_date){

					# AUTHORITY
					$db_auth							= $database->checkAuthority();
					$db_check							= NULL;

					# DATABASE CHANGE
					if(empty($db_auth)){

						# CLOSE DATABASE SLAVE
						$database->closeDb();

						# CONNECT DATABASE MASTER
						$database->connectDb(MASTER_ACCESS_KEY);

						$db_check						= 1;

					}

					# 日付が変わっても無料ポイント持ってたらリセット
					if($result['f_point'] > 0){
						$members_update['f_point']		= 0;
					}

					# 最終アクセス
					$members_update['access_date']		= date("YmdHis");

					# ログインカウント
					$members_update['login_count']		= $result['login_count'] + 1;

					$members_update_conditions[':id']	= $result['id'];
					$members_update_where				= "id = :id";
					$database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

					if(empty($_SESSION['access'])){
						$_SESSION['access']				= 1;
						$result['day_change']			= 1;
					}

					# ログインカウント
					$result['login_count']				= $members_update['login_count'];

					# DATABASE CHANGE
					if(!empty($db_check)){

						# CLOSE DATABASE MASTER
						$database->closeDb();

						# CONNECT DATABASE SLAVE
						$database->connectDb();

					}

				}


			}


		/*********************************************
		**
		**	ACCOUNT CHECK ERROR
		**	-----------------------------------------
		**	データ該当なし
		**
		*********************************************/

		}else{

			$result['error']							= 3;

		}

		return $result;


	}



	/**************************************************
	**
	**	checkFirstCertify
	**	----------------------------------------------
	**	初回ログイン認証
	**
	**************************************************/

	function checkFirstCertify($user_id,$device=NULL){

		# 初期化
		$result											 = NULL;
		$result											 = array();
		$error											 = NULL;

		if(empty($user_id)){
			$error										 = "お客様情報が取得できませんでした<br />\n";
		}elseif(!preg_match('/^[a-zA-Z0-9]+$/', $user_id)){
			$error										 = "お客様情報が取得できませんでした<br />\n";
		}

		if(empty($error)){

			# DB / MAIN CLASS
			$database									 = NULL;
			$database									 = $this->database;
			$output										 = NULL;
			$output										 = $this->output;

			# PARAMETER
			$array										 = array();
			$array[':site_cd']							 = SITE_CD;
			$array[':user_id']							 = $user_id;
			$array[':status']							 = 99;

			$column										 = "*";

			$where										 = "user_id = :user_id ";
			$where										.= "AND site_cd = :site_cd ";
			$where										.= "AND status < :status";
			$order										 = NULL;
			$limit										 = 1;
			$group										 = NULL;

			$rtn										 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
			$error										 = $database->errorDb("CHECK FIRST CERTIFY",$rtn->errorCode(),__FILE__,__LINE__);
			if(!empty($error)){ $output->outputError($error,$return_path,$return_target='1'); }

			$rows										 = $database->numRows($rtn);

			if($rows == 0){

				$result['error']						 = 2;
				$result['message']						 = "お客様情報が取得できませんでした<br />\n";

			}else{

				# 情報取得
				$result					 				 = $database->fetchAssoc($rtn);

				# 削除ユーザーは除外
				if($result['status'] == 9 || $result['status'] == 19 || $result['status'] == 29 || $result['status'] == 39){

					$result['error']					 = 2;
					$result['message']					 = "お客様情報が取得できませんでした<br />\n";

				# 処理OK
				}else{

					# AUTHORITY
					$db_auth							 = $database->checkAuthority();
					$db_check							 = NULL;

					# DATABASE CHANGE
					if(empty($db_auth)){

						# CLOSE DATABASE SLAVE
						$database->closeDb();

						# CONNECT DATABASE MASTER
						$database->connectDb(MASTER_ACCESS_KEY);

						$db_check						 = 1;

					}

					$result['access']					= NULL;
					$result['regist']					= NULL;
					$today								= date("Ymd");
					$last_access_date					= date("Ymd",strtotime($result['access_date']));

					if($today != $last_access_date){

						$result['access']				= 1;

						# 日付が変わっても無料ポイント持ってたらリセット
						if($result['f_point'] > 0){
							$members_update['f_point']	= 0;
						}

						# ログインカウント
						$members_update['login_count']	= $result['login_count'] + 1;

					}

					# アクセスデバイス
					if(!empty($device)){

						# スマフォ
						if($device['access_device'] == "smart"){

							# iPhone
							if($device['access_os'] == 1){
								$user_device			= 5;
							}elseif($device['access_os'] == 2){
								$user_device			= 7;
							}else{
								$user_device			= 8;
							}

						# PC
						}else{
							$user_device				= 4;
						}

						# 違うデバイスなら
						if($result['device'] != $user_device){
							$members_update['device']	= $user_device;
						}

					}

					# 退会ユーザー
					if($result['status'] == 8){
						$members_update['status']		= 1;
						$members_update['leave_date']	= "0000-00-00";
					}elseif($result['status'] == 18 || $result['status'] == 28 || $result['status'] == 38){
						$members_update['status']	 	= substr_replace($result['status'],"", -1,1);
						$members_update['leave_date']	= "0000-00-00";
					}

					# 新規ユーザー　初ログイン
					if(empty($result['reg_date'])){
						//$members_update['reg_date']		= date("YmdHis");
						$result['regist']				= 1;
					}

					if(empty($result['first_login_date'])){
						$members_update['first_login_date']	= date("YmdHis");
						$result['regist']				= 1;
					}

					# 最終アクセス
					$members_update['access_date']		= date("YmdHis");

					# UPDATE WHERE
					$members_update_where				= "id = :id";
					$members_update_conditions[':id']	= $result['id'];

					# 【UPDATE】 / members
					$database->updateDb($this->table,$members_update,$members_update_where,$members_update_conditions);

					# DATABASE CHANGE
					if(!empty($db_check)){

						# CLOSE DATABASE MASTER
						$database->closeDb();

						# CONNECT DATABASE SLAVE
						$database->connectDb();

					}

					# CERTIFY OK
					$result['user_pass']				= $this->makePass($result['id'],$result['user_ps']);
					$result['error']					= NULL;
					$result['message']					= NULL;

				}

			}

		}else{

			$result['error']							 = 1;
			$result['message']							 = $error;

		}

		return $result;

	}



	/**************************************************
	**
	**	getMemberList
	**	----------------------------------------------
	**	membersリスト取得
	**	
	**
	**************************************************/

	public function getMemberList($post_data,$exclusion_id=NULL,$column=NULL){

		if(empty($column)){
			$column				 		 = "*";
		}

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		# PARAMETER
		$array							 = array();
		$array[':site_cd']				 = SITE_CD;

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		$where							 = "site_cd = :site_cd ";

		# 除外ID
		if(!empty($exclusion_id)){

			$exclusion					 = NULL;

			foreach($exclusion_id as $key => $value){
				$array[":exclusion_".$key]	= $value;
				$exclusion				.= ":exclusion_".$key.",";
			}
			$exclusion					 = substr_replace($exclusion,'', -1,1);

			$where						.= "AND id NOT IN (".$exclusion.") ";

		}

		# OPEN FLG
		if(!empty($post_data['open_flg'])){
			if($post_data['open_flg'] == "special"){
				$array[':open_flg']		 = 2;
				$where					.= "AND open_flg <= :open_flg ";
			}else{
				$array[':open_flg']		 = $post_data['open_flg'];
				$where					.= "AND open_flg = :open_flg ";
			}
		}else{
			$array[':open_flg']			 = 1;
			$where						.= "AND open_flg = :open_flg ";
		}

		# OP ID
		if(isset($post_data['op_id'])){
			$array[':op_id']			 = $post_data['op_id'];
			$where						.= "AND op_id = :op_id ";
		}else{
			$array[':op_id']			 = 0;
			$where						.= "AND op_id > :op_id ";
		}

		# NARUTO
		if(isset($post_data['naruto'])){
			$array[':naruto']			 = $post_data['naruto'];
			$where						.= "AND naruto = :naruto ";
		}else{
			$array[':naruto']			 = 0;
			$where						.= "AND naruto = :naruto ";
		}

		# STATUS
		if(isset($post_data['status'])){
			$array[':status']			 = $post_data['status'];
			$where						.= "AND status = :status";
		}else{
			$array[':status']			 = 8;
			$where						.= "AND status < :status";
		}

		$order							 = NULL;
		$limit							 = NULL;
		$group							 = NULL;

		if(!empty($post_data['order'])){
			$order						 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit						 = $post_data['limit'];
		}

		if(!empty($post_data['group'])){
			$group						 = $post_data['group'];
		}

		$rtn							 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error							 = $database->errorDb("getMemberList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getMemberDataById
	**	----------------------------------------------
	**	MEMBERS DATA 抽出
	**
	**************************************************/

	public function getMemberDataById($id,$status=NULL,$column=NULL){

		if(empty($id)){
			return FALSE;
		}

		if(empty($column)){
			$column	= "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "id = :id";
		$array[':id']			 = $id;

		if(isset($status)){
			$where				.= " AND status = :status";
			$array[':status']	 = $status;
		}else{
			$where				.= " AND status > :status1 AND status < :status2";
			$array[':status1']	 = 0;
			$array[':status2']	 = 99;
		}

		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getMemberDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					 = $database->fetchAssoc($rtn);

		return $data;

	}



	/**************************************************
	**
	**	getMemberDataByUserId
	**	----------------------------------------------
	**	MEMBERS DATA 抽出
	**
	**************************************************/

	public function getMemberDataByUserId($user_id,$status=NULL,$column=NULL){

		if(empty($user_id)){
			return FALSE;
		}

		if(empty($column)){
			$column	= "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "user_id = :user_id";
		$array[':user_id']		 = $user_id;

		if(isset($status)){
			if(is_numeric($status)){
				$where			.= " AND status = :status";
				$array[':status'] = $status;
			}elseif($status == "all"){

			}
		}else{
			$where				.= " AND status > :status1 AND status < :status2";
			$array[':status1']	 = 0;
			$array[':status2']	 = 99;
		}

		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getMemberDataByUserId",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					 = $database->fetchAssoc($rtn);

		return $data;

	}



	/**************************************************
	**
	**	getReleaseMemberId
	**	----------------------------------------------
	**	空いてる子キャラのMEMBERS IDを取得
	**
	**************************************************/

	public function getReleaseMemberId($post_data){

		if(empty($post_data['naruto'])){
			return FALSE;
		}

		# PARAMETER
		$array							 = array();
		$array[':site_cd']				 = SITE_CD;

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		# COLUMN
		$column							 = "id";

		$where							 = "site_cd = :site_cd ";

		# OPEN FLG
		if(!empty($post_data['open_flg'])){
			$array[':open_flg']			 = $post_data['open_flg'];
			$where						.= "AND open_flg = :open_flg ";
		}else{
			$array[':open_flg']			 = 1;
			$where						.= "AND open_flg = :open_flg ";
		}

		# OP ID
		if(isset($post_data['op_id'])){
			$array[':op_id']			 = $post_data['op_id'];
			$where						.= "AND op_id = :op_id ";
		}else{
			$array[':op_id']			 = 0;
			$where						.= "AND op_id > :op_id ";
		}

		# NARUTO
		if(isset($post_data['naruto'])){
			$array[':naruto']			 = $post_data['naruto'];
			$where						.= "AND naruto = :naruto ";
		}else{
			$array[':naruto']			 = 0;
			$where						.= "AND naruto = :naruto ";
		}

		# STATUS
		if(isset($post_data['status'])){
			$array[':status']			 = $post_data['status'];
			$where						.= "AND status = :status ";
		}else{
			$array[':status']			 = 9;
			$where						.= "AND status < :status ";
		}

		# ALLOCATOIN
		if(isset($post_data['allocation'])){
			$array[':allocation']		 = $post_data['allocation'];
			$where						.= "AND allocation = :allocation";
		}

		$order							 = NULL;
		$limit							 = NULL;
		$group							 = NULL;

		if(!empty($post_data['order'])){
			$order						 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit						 = $post_data['limit'];
		}

		if(!empty($post_data['group'])){
			$group						 = $post_data['group'];
		}


		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getReleaseMemberId",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;


	}


	/**************************************************
	**
	**	checkCampaignUpdate
	**	----------------------------------------------
	**	キャンペーン該当チェック & アップデート
	**
	**************************************************/

	public function checkCampaignUpdate($members_data,$campaign_data){

		# ERROR
		if(empty($members_data['id'])){
			return FALSE;
		}

		# 初期化
		$result			= 0;

		# キャンペーンあれば
		if(!empty($campaign_data['id'])){

			# キャンペーンステータス(キャンペーンあり)
			$result							= 1;

			# キャンペーン維持タイプならmembersのcampaignをアップデート
			if($campaign_data['campaign_stay'] == 0 || empty($campaign_data['campaign_stay'])){

				# members に設定されていなかったら
				if($campaign_data['id'] != $members_data['campaign']){

					$members_update			= array();
					$members_update			= array(
						'campaign'			=> $campaign_data['id']
					);

					# UPDATE WHERE
					$members_update_where				= "id = :id";
					$members_update_conditions[':id']	= $members_data['id'];

					# マスター / スレーブ切り替え面倒なので updateMembers の methodで処理
					$this->updateMembers($members_update,$members_update_where,$members_update_conditions);

					# キャンペーンステータス(キャンペーンあり / members update)
					$result					= 2;

				}

			}

		# キャンペーンなければ
		}else{

			# campaignカラムにcampaign_idがあった場合 campaignカラムをリセット
			if($members_data['campaign'] > 0){

				$members_update				= array();
				$members_update				= array(
					'campaign'				=> 0
				);

				# UPDATE WHERE
				$members_update_where				= "id = :id";
				$members_update_conditions[':id']	= $members_data['id'];

				# マスター / スレーブ切り替え面倒なので updateMembers の methodで処理
				$this->updateMembers($members_update,$members_update_where,$members_update_conditions);

				# # キャンペーンステータス(キャンペーンなし / 解除)
				$result						= 3;

			}

		}

		return $result;


	}



	/**************************************************
	**
	**	updateMembers
	**	----------------------------------------------
	**	UPDATE / 色々書くの面倒だからここに集約
	**
	**************************************************/

	public function updateMembers($update,$where,$conditions){

		if(empty($update) || empty($update) || empty($conditions)){
			return FALSE;
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output	 				 = NULL;
		$output	 				 = $this->output;

		# AUTHORITY
		$db_auth				 = $database->checkAuthority();
		$db_check				 = NULL;

		# DATABASE CHANGE
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check			 = 1;

		}

		# 【UPDATE】 / members
		$database->updateDb($this->table,$update,$where,$conditions);

		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}

		return TRUE;

	}



	/**************************************************
	**
	**	makePass
	**	----------------------------------------------
	**	パスワード生成
	**
	**************************************************/

	function makePass($id,$pass) {
        $result	= substr(sha1($id.$pass),7,16);
        return $result;
    }


}

?>