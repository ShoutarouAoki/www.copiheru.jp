<?php
/********************************************************************************
**	
**	PointsetModel.php
**	=============================================================================
**
**	■PAGE / 
**	POINTSET MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	POINTSET CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	POINTSET CLASS
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
class PointsetModel{


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
		$this->table		= "pointsets";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getPointset
	**	----------------------------------------------
	**	指定したpoint_no_idを取得
	**
	**************************************************/

	public function getPointset($point_no_id,$members_data,$campaign_id=NULL){

		# ERROR
		if(empty($point_no_id)){
			return FALSE;
		}

		# RESULT
		$result					= NULL;

		# 入金有り
        if($members_data['pay_count'] > 0){
            $pay_flg			= 1;
		# 入金無し
        }else if ($members_data['pay_count'] == 0){
            $pay_flg			= 2;
		# その他
        }else{
            $pay_flg			= 3;
        }

		$point_no_count			= explode(',',$point_no_id);

        for($i=0;$i<count($point_no_count);$i++){

            # キャンペーン
            if (!empty($campaign_id)) {
        		$campaign_conditions			= array(
                    'point_no_id'				=> $point_no_count[$i],
        			'site_cd'					=> $members_data['site_cd'], 
        			'status'					=> 0,
                    'campaign_id'				=> $campaign_id
                );
                $point_data[$i]					= $this->getPointsetData($campaign_conditions,"point_no_id,point");
            }

            # ユーザ個別
            if (empty($point_data[$i]) && !empty($members_data['id'])) {
                $user_conditions				= array(
                    'point_no_id'				=> $point_no_count[$i],
        			'site_cd'					=> $members_data['site_cd'], 
        			'domain_flg'				=> 0,
        			'status'					=> 0,
                    'user_id'					=> $members_data['id']
                );
                $point_data[$i] 				= $this->getPointsetData($user_conditions,"point_no_id,point");
            }

            # domain_flg : 今回は使わないので一旦コメントアウト
			/*
            if (empty($point_data[$i])) {
                $domain_conditions				= array(
                     'point_no_id'				=> $point_no_count[$i],
        			'site_cd'					=> $members_data['site_cd'], 
        			'domain_flg'				=> $members_data['domain_flg'],
        			'status'					=> 0,
                    'sex'						=> $members_data['sex'],
                    'pay_flg'					=> $pay_flg
                );
                $point_data[$i]					= $this->getPointsetData($domain_conditions,"point_no_id,point");
            }
			*/

            # DEFAULT
            if (empty($point_data[$i])) {

                $default_conditions				= array(
                    'point_no_id'				=> $point_no_count[$i],
        			'site_cd'					=> $members_data['site_cd'], 
        			'domain_flg'				=> 0,
        			'status'					=> 0,
                    'sex'						=> $members_data['sex'],
                    'pay_flg'					=> $pay_flg
                );
                $point_data[$i]					= $this->getPointsetData($default_conditions, "point_no_id,point");

            }

        }

		# RESULT
		if(!empty($point_data)){
			$result								= $point_data;
		}

		return $result;


	}



	/**************************************************
	**
	**	checkPointConsume
	**	----------------------------------------------
	**	持ちポイント足りるかチェック
	**
	**************************************************/

	public function checkPointConsume($point_no_id,$members_data,$character_data,$campaign_id=NULL){

		# ERROR
		if(empty($point_no_id)){
			return FALSE;
		}

		# POINT NUMBER / NAME ARRAY
		global $point_no_array;
		global $point_name_array;

		# RESULT
		$result					= NULL;

		# CHECK POINT 初期化
        $check_point			= 0;

		# 送信NO
		$point_no_send			= $point_no_array[$point_name_array['send']][2];

		# 開封NO
		$point_no_read			= $point_no_array[$point_name_array['read']][2];

		# 入金有り
        if($members_data['pay_count'] > 0){
            $pay_flg			= 1;
		# 入金無し
        }else if ($members_data['pay_count'] == 0){
            $pay_flg			= 2;
		# その他
        }else{
            $pay_flg			= 3;
        }

		$point_no_count			= explode(',',$point_no_id);

        for($i=0;$i<count($point_no_count);$i++){

			# SQL FLAG
            $sql_flg			= 1;

            # 受信無料キャラ
            if ($point_no_count[$i] == $point_no_read) {
                if ($character_data['status'] == 5 || $character_data['status'] == 6) {
					$point_data[$i]['point_no_id']	= $point_no_read;
					$point_data[$i]['point']		= 0;
					$sql_flg						= NULL;
                }
            }

            # 送信無料キャラ
            if ($point_no_count[$i] == $point_no_send) {
                if ($character_data['status'] == 4 || $character_data['status'] == 6) {
					$point_data[$i]['point_no_id']	= $point_no_send;
					$point_data[$i]['point']		= 0;
					$sql_flg						= NULL;
                }
            }

            # 全て無料( ユーザー定額(status : 2) / ユーザー無料(status : 3) / キャラ完全無料 (status : 7) )
            if ($members_data['status'] == 2 || $members_data['status'] == 3 || $character_data['status'] == 7) {
                $point_data[$i]['point_no_id']		= $point_no_count[$i];
				$point_data[$i]['point']			= 0;
				$sql_flg							= NULL;
            }

			# SQL CHECK
            if (!empty($sql_flg)) {

                # キャンペーン
                if (!empty($campaign_id)) {
            		$campaign_conditions			= array(
                        'point_no_id'				=> $point_no_count[$i],
            			'site_cd'					=> $members_data['site_cd'], 
            			'status'					=> 0,
                        'campaign_id'				=> $campaign_id
                    );
                    $point_data[$i]					= $this->getPointsetData($campaign_conditions,"point_no_id,point");
                }

                # ユーザ個別
                if (empty($point_data[$i])) {
                    $user_conditions				= array(
                        'point_no_id'				=> $point_no_count[$i],
            			'site_cd'					=> $members_data['site_cd'], 
            			'domain_flg'				=> 0,
            			'status'					=> 0,
                        'user_id'					=> $members_data['id']
                    );
                    $point_data[$i] 				= $this->getPointsetData($user_conditions,"point_no_id,point");
                }

                # domain_flg : 今回は使わないので一旦コメントアウト
				/*
                if (empty($point_data[$i])) {
                    $domain_conditions				= array(
                         'point_no_id'				=> $point_no_count[$i],
            			'site_cd'					=> $members_data['site_cd'], 
            			'domain_flg'				=> $members_data['domain_flg'],
            			'status'					=> 0,
                        'sex'						=> $members_data['sex'],
                        'pay_flg'					=> $pay_flg
                    );
                    $point_data[$i]					= $this->getPointsetData($domain_conditions,"point_no_id,point");
                }
				*/

                # DEFAULT
                if (empty($point_data[$i])) {

                    $default_conditions				= array(
                        'point_no_id'				=> $point_no_count[$i],
            			'site_cd'					=> $members_data['site_cd'], 
            			'domain_flg'				=> 0,
            			'status'					=> 0,
                        'sex'						=> $members_data['sex'],
                        'pay_flg'					=> $pay_flg
                    );
                    $point_data[$i]					= $this->getPointsetData($default_conditions, "point_no_id,point");

					# 設定がなかったら
					if(empty($point_data[$i])){

			            # 受信
			            if ($point_no_count[$i] == $point_no_read) {
							$point_data[$i]['point_no_id']	= $point_no_read;
							$point_data[$i]['point']		= DEFAULT_READ_POINT;
			            }

			            # 送信
			            if ($point_no_count[$i] == $point_no_send) {
							$point_data[$i]['point_no_id']	= $point_no_send;
							$point_data[$i]['point']		= DEFAULT_SEND_POINT;
			            }

					}

                }

            }

			# 消費POINT加算
			if($point_data[$i]['point']){
				$check_point				+= $point_data[$i]['point'];
			}

        }

		# 持ちポイントが消費ポイントより多ければOK
		if($members_data['total_point'] >= $check_point){
			$result							= $point_data;
		}

		return $result;


	}



	/**************************************************
	**
	**	makePointConsume
	**	----------------------------------------------
	**	ポイント消費計算メソッド
	**
	**************************************************/

	public function makePointConsume($point_data,$members_data){

		# ERROR
		if(empty($point_data) || empty($members_data)){
			return FALSE;
		}

		# ERROR 初期化
		$error												= NULL;
		$errormessage										= NULL;

		# ユーザー持ちポイント(課金)
		$user_point											= $members_data['point'];

		# ユーザー持ちポイント(サービス配布)
		$user_s_point										= $members_data['s_point'];

		# ユーザー持ちポイント(ログイン無料配布)
		$user_f_point										= $members_data['f_point'];

		# 繰越サービスポイント初期化
		$takeover_s_point									= 0;

		# 繰越課金ポイント初期化
		$takeover_point										= 0;

		# トータル消費ポイント初期化
		$consumption_point									= 0;

		# UPDATE CHECK FLG
		$update_point_flg									= NULL;
		$update_s_point_flg									= NULL;
		$update_f_point_flg									= NULL;

		# リザルト初期化
		$result												= array();


		/************************************************
		**
		**	消費ポイント計算
		**
		************************************************/

		$i=0;
		foreach($point_data as $key => $value){

			# トータル消費ポイント計算
			$consumption_point								+= $value['point'];

			# point_no_id
			$point_no_check									= $value['point_no_id'];

			# points INSERT用
			$result['points'][$i]['point_no_id']			= $point_no_check;

			# 消費ポイント0ならpoints用のデータだけ作ってcontinue / 無料ポイント扱いなので point_type は 2
			if($value['point'] == 0 || empty($value['point'])){
				$result['points'][$i]['point'][2]			= 0;
				continue;
			}

            # 持ち無料ポイントが消費ポイントより少なかった場合
            if ($user_f_point < $value['point']) {


				# 元々f_pointがゼロならここの処理はしない
				if($user_f_point == 0){

					$takeover_s_point						= $value['point'];

				# 差分処理
				}else{

					# 繰越サービス配布ポイント
					$takeover_s_point						= $value['point'] - $user_f_point;

					# points INSERT用
					$result['points'][$i]['point'][2]		= $user_f_point;

					# f_point
					$user_f_point							= 0;

					# UPDATE FLG
					$update_f_point_flg						= 1;

				}


				# 持ちサービス配布ポイントが繰越サービス配布ポイントより少なかった場合
				if($user_s_point < $takeover_s_point){

					# 繰越課金ポイント
					$takeover_point							= $takeover_s_point - $user_s_point;

					# 元々s_pointがゼロならここの処理はしない
					if($user_s_point == 0){

						# 繰越課金ポイント
						$takeover_point						= $takeover_s_point;

					# 差分処理
					}else{

						# 繰越課金ポイント
						$takeover_point						= $takeover_s_point - $user_s_point;

						# points INSERT用
						$result['points'][$i]['point'][1]	= $user_s_point;

						# s_point
						$user_s_point						= 0;

						# UPDATE FLG
						$update_s_point_flg					= 1;

					}


					# 持ち課金ポイントが繰越課金ポイントより少なかった場合(基本有り得ないけど念のため)
					if($user_point < $takeover_point){

						$error								= 2;
						$errormessage						= TICKET_NAME."が足りません。";
						break;

					# 課金ポイント & サービス配布ポイント & ログイン無料配布ポイント 処理
					}else{

						# point
						$user_point							= $user_point - $takeover_point;

						# points INSERT用
						$result['points'][$i]['point'][0]	= $takeover_point;

						# UPDATE FLG
						$update_point_flg					= 1;

					}

				# サービス配布ポイント & ログイン無料配布ポイント 処理
				}else{

					# s_point
					$user_s_point							= $user_s_point - $takeover_s_point;

					# points INSERT用
					$result['points'][$i]['point'][1]		= $takeover_s_point;

					# UPDATE FLG
					$update_s_point_flg						= 1;

				}

            # ログイン無料配布ポイントのみで処理
            }else{

				# f_point
				$user_f_point								= $user_f_point - $value['point'];

				# UPDATE FLG
				$update_f_point_flg							= 1;

				# points INSERT用
				$result['points'][$i]['point'][2]			= $value['point'];

            }

			$i++;

		}


		# ERROR
		if(!empty($error)){

			$result['error']									= $error;
			$result['errormessage']								= $errormessage;

		}else{

			if(!empty($update_point_flg)){
				$result['members']['point']						= $user_point;
			}

			if(!empty($update_s_point_flg)){
				$result['members']['s_point']					= $user_s_point;
			}

			if(!empty($update_f_point_flg)){
				$result['members']['f_point']					= $user_f_point;
			}

			# TOTAL
			$result['consumption_point']						= $consumption_point;

		}


		return $result;


	}



	/**************************************************
	**
	**	getPointsetList
	**	----------------------------------------------
	**	ポイントセットリスト取得
	**
	**************************************************/

	public function getPointsetList($post_data,$column=NULL){

		if(empty($post_data['point_no_id'])){
			return FALSE;
		}

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
		$array[':site_cd']			 = $post_data['site_cd'];


		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['domain_flg'])){
			$where					.= "AND domain_flg = :domain_flg ";
			$array[':domain_flg']	 = $post_data['domain_flg'];
		}

		if(isset($post_data['point_no_id'])){
			$where					.= "AND point_no_id = :point_no_id ";
			$array[':point_no_id']	 = $post_data['point_no_id'];
		}

		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}

		if(isset($post_data['user_id'])){
			$where					.= "AND user_id = :user_id ";
			$array[':user_id']		 = $post_data['user_id'];
		}

		if(isset($post_data['sex'])){
			$where					.= "AND sex = :sex ";
			$array[':sex']			 = $post_data['sex'];
		}

		if(isset($post_data['pay_flg'])){
			$where					.= "AND pay_flg = :pay_flg ";
			$array[':pay_flg']		 = $post_data['pay_flg'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}

		$order					 = NULL;
		$limit					 = NULL;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getPointsetList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getPointsetData
	**	----------------------------------------------
	**	ポイントセットデータ取得
	**
	**************************************************/

	public function getPointsetData($post_data,$column=NULL){


		if(empty($post_data['site_cd'])){
			return FALSE;
		}

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
		$array[':site_cd']			 = $post_data['site_cd'];


		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['domain_flg'])){
			$where					.= "AND domain_flg = :domain_flg ";
			$array[':domain_flg']	 = $post_data['domain_flg'];
		}

		if(isset($post_data['point_no_id'])){
			$where					.= "AND point_no_id = :point_no_id ";
			$array[':point_no_id']	 = $post_data['point_no_id'];
		}

		if(isset($post_data['campaign_id'])){
			$where					.= "AND campaign_id = :campaign_id ";
			$array[':campaign_id']	 = $post_data['campaign_id'];
		}

		if(isset($post_data['user_id'])){
			$where					.= "AND user_id = :user_id ";
			$array[':user_id']		 = $post_data['user_id'];
		}

		if(isset($post_data['sex'])){
			$where					.= "AND sex = :sex ";
			$array[':sex']			 = $post_data['sex'];
		}

		if(isset($post_data['pay_flg'])){
			$where					.= "AND pay_flg = :pay_flg ";
			$array[':pay_flg']		 = $post_data['pay_flg'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}

		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getPointsetData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		return $data;


	}



}

?>