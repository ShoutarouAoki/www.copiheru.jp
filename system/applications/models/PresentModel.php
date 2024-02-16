<?php
/********************************************************************************
**	
**	PresentModel.php
**	=============================================================================
**
**	■PAGE / 
**	PRESENT MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	PRESENT CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	PRESENT CLASS
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
class PresentModel{


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
		$this->table		= "presents";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getPresentList
	**	----------------------------------------------
	**	プレゼントリスト
	**
	**************************************************/

	public function getPresentList($post_data,$column=NULL){

		if(empty($column)){
			$column					 = "*";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;
		

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(!empty($post_data['distribution_date_s'])){
			$where					.= "AND distribution_date_s = :date_s ";
			$array[':date_s']		 = $post_data['distribution_date_s'];
		}elseif(!empty($post_data['distribution_under_s'])){
			$where					.= "AND distribution_date_s <= :date_s ";
			$array[':date_s']		 = $post_data['distribution_under_s'];
		}

		if(!empty($post_data['distribution_date_e'])){
			$where					.= "AND distribution_date_e = :date_e ";
			$array[':date_e']		 = $post_data['distribution_date_e'];
		}elseif(!empty($post_data['distribution_over_e'])){
			$where					.= "AND distribution_date_e >= :date_e ";
			$array[':date_e']		 = $post_data['distribution_over_e'];
		}

		# ログイン時のプレゼント受け取りチェック
		if(!empty($post_data['check_distribution_date'])){
			$where					.= "AND distribution_date_s <= :date_s ";
			$array['date_s']		 = date("YmdHis");
			$where					.= "AND distribution_date_e >= :date_e ";
			$array['date_e']		 = date("YmdHis");
			$where					.= "AND limit_date >= :limit ";
			$array['limit']	 		 = date("YmdHis");
		}

		# 登録日より新しいデータのみ
		if(!empty($post_data['reg_date'])){
			$where					.= "AND distribution_date_s >= :reg_s ";
			$array[':reg_s']		 = $post_data['reg_date'];
		}

		# 最終プレゼント受け取り日より新しいデータのみ(受け取り済みは除外)
		if(!empty($post_data['present_recv_date'])){
			$where					.= "AND distribution_date_s > :recv_s ";
			$array[':recv_s']		 = $post_data['present_recv_date'];
		}

		# ログインボーナス（毎日）用パラメータ　受け取り期限が今日より前なら付与
		if(!empty($post_data['daily'])){
			$where					.= "AND DATE_FORMAT(CURRENT_TIME,'%Y%m%d') > :recv_s ";
			$array[':recv_s']		= $post_data['daily_recv_date'];
		}
		
		if(isset($post_data['category'])){
			$where					.= "AND category = :category ";
			$array[':category']		 = $post_data['category'];
		}

		# ログインボーナス・配布ボーナス系 1～10まで
		if(isset($post_data['bonus'])){
			$where					.= "AND category <= :category ";
			$array[':category']		 = 10;
		}

		if(isset($post_data['type'])){
			$where					.= "AND type = :type ";
			$array[':type']			 = $post_data['type'];
		}

		if(isset($post_data['character_id'])){
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		if(isset($post_data['level'])){
			$where					.= "AND level = :level ";
			$array[':level']		 = $post_data['level'];
		}

		if(isset($post_data['payment'])){

			# 入有り
			if($post_data['payment'] == 1){

				$where					.= "AND payment IN (:pay1,:pay2) ";
				$array[':pay1']			 = 0;
				$array[':pay2']			 = 1;

			# 入無し
			}elseif($post_data['payment'] == 2){

				$where					.= "AND payment IN (:pay1,:pay2) ";
				$array[':pay1']			 = 0;
				$array[':pay2']			 = 2;

			}

		}

		//20181010 add by A.cos
		if(isset($post_data['buy'])){

			# 入有り
			if($post_data['buy'] == 1){

				$where					.= "AND buy IN (:buy1,:buy2) ";
				$array[':buy1']			 = 0;
				$array[':buy2']			 = 1;

			# 入無し
			}elseif($post_data['buy'] == 2){

				$where					.= "AND buy IN (:buy1,:buy2) ";
				$array[':buy1']			 = 0;
				$array[':buy2']			 = 2;

			}

		}

		if(isset($post_data['limit_date'])){
			$where					.= "AND limit_date = :limit ";
			$array[':limit']		 = $post_data['limit_date'];
		}

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
		$error						 = $database->errorDb("getPresentList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;

	}



	/**************************************************
	**
	**	getPresentCount
	**	----------------------------------------------
	**	プレゼントカウント
	**
	**************************************************/

	public function getPresentCount($post_data){

		$column						 = "id";

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;
		

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(!empty($post_data['distribution_date_s'])){
			$where					.= "AND distribution_date_s = :date_s ";
			$array[':date_s']		 = $post_data['distribution_date_s'];
		}elseif(!empty($post_data['distribution_under_s'])){
			$where					.= "AND distribution_date_s <= :date_s ";
			$array[':date_s']		 = $post_data['distribution_under_s'];
		}

		if(!empty($post_data['distribution_date_e'])){
			$where					.= "AND distribution_date_e = :date_e ";
			$array[':date_e']		 = $post_data['distribution_date_e'];
		}elseif(!empty($post_data['distribution_over_e'])){
			$where					.= "AND distribution_date_e >= :date_e ";
			$array[':date_e']		 = $post_data['distribution_over_e'];
		}

		# ログイン時のプレゼント受け取りチェック
		if(!empty($post_data['check_distribution_date'])){
			$where					.= "AND distribution_date_s <= :date_s ";
			$array['date_s']		 = date("YmdHis");
			$where					.= "AND distribution_date_e >= :date_e ";
			$array['date_e']		 = date("YmdHis");
			$where					.= "AND limit_date >= :limit ";
			$array['limit']	 		 = date("YmdHis");
		}

		# 登録日より新しいデータのみ
		if(!empty($post_data['reg_date'])){
			$where					.= "AND distribution_date_s >= :reg_s ";
			$array[':reg_s']		 = $post_data['reg_date'];
		}

		# 最終プレゼント受け取り日より新しいデータのみ(受け取り済みは除外)
		if(!empty($post_data['present_recv_date'])){
			$where					.= "AND distribution_date_s > :recv_s ";
			$array[':recv_s']		 = $post_data['present_recv_date'];
		}

		if(isset($post_data['category'])){
			$where					.= "AND category = :category ";
			$array[':category']		 = $post_data['category'];
		}

		# ログインボーナス・配布ボーナス系 1～10まで
		if(isset($post_data['bonus'])){
			$where					.= "AND category <= :category ";
			$array[':category']		 = 10;
		}

		if(isset($post_data['type'])){
			$where					.= "AND type = :type ";
			$array[':type']			 = $post_data['type'];
		}

		if(isset($post_data['character_id'])){
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		if(isset($post_data['level'])){
			$where					.= "AND level = :level ";
			$array[':level']		 = $post_data['level'];
		}

		if(isset($post_data['payment'])){

			# 入有り
			if($post_data['payment'] == 1){

				$where					.= "AND payment IN (:pay1,:pay2) ";
				$array[':pay1']			 = 0;
				$array[':pay2']			 = 1;

			# 入無し
			}elseif($post_data['payment'] == 2){

				$where					.= "AND payment IN (:pay1,:pay2) ";
				$array[':pay1']			 = 0;
				$array[':pay2']			 = 2;

			}

		}

		if(isset($post_data['limit_date'])){
			$where					.= "AND limit_date = :limit ";
			$array[':limit']		 = $post_data['limit_date'];
		}

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

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getPresentCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows						 = $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;

	}



	/**************************************************
	**
	**	getPresentDataById
	**	----------------------------------------------
	**	プレゼント情報取得
	**
	**************************************************/

	public function getPresentDataById($id,$column=NULL){

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

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "id = :id";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getPresentDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}


}

?>