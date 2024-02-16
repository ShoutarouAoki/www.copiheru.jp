<?php
/********************************************************************************
**	
**	CampaignsetModel.php
**	=============================================================================
**
**	■PAGE / 
**	CAMPAIGNSET MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	CAMPAIGNSET CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	CAMPAIGNSET CLASS
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
class CampaignsetModel{


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
		$this->table		= "campaignsets";
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	getCampaignsetList
	**	----------------------------------------------
	**	キャンペーン取得
	**
	**************************************************/

	public function getCampaignsetList($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['campaign_date_s']) || empty($post_data['campaign_date_e'])){
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


		$where						 = "site_cd = :site_cd ";

		if(!empty($post_data['campaign_date_s'])){
			$array[':start']		 = $post_data['campaign_date_s'];
			$where					.= "AND campaign_date_s <= :start ";
		}

		if(!empty($post_data['campaign_date_e'])){
			$array[':end']			 = $post_data['campaign_date_e'];
			$where					.= "AND campaign_date_e >= :end ";
		}

		if(isset($post_data['status'])){
			$array[':status']	 	 = $post_data['status'];
			$where					.= "AND status = :status ";
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

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error						 = $database->errorDb("GET CAMPAIGNSET LIST",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }


		return $rtn;


	}



	/**************************************************
	**
	**	getCampaignDataById
	**	----------------------------------------------
	**	キャンペーンデータ取得
	**
	**************************************************/

	public function getCampaignDataById($id,$column=NULL){

		if(empty($id)){
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
		$array[':id']				 = $id;

		$where						 = "id = :id ";

		$where						.= "AND status = :status";
		$array[':status']		 	 = 0;

		$order						 = NULL;
		$limit						 = 1;
		$group						 = NULL;


		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error						 = $database->errorDb("GET CAMPAIGNSET",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data						 = $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	checkCampaign
	**	----------------------------------------------
	**	キャンペーンチェック
	**
	**************************************************/

	public function checkCampaign($id,$column=NULL){

		if(empty($id)){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column					= "id,campaign_type,status";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':id']				 = $id;

		$where						 = "id = :id ";

		$where						.= "AND campaign_date_s <= :start ";
		$array[':start']			 = date("YmdHis");

		$where						.= "AND campaign_date_e >= :end ";
		$array[':end']				 = date("YmdHis");

		$where						.= "AND status = :status";
		$array[':status']		 	 = 0;

		$order						 = NULL;
		$limit						 = 1;
		$group						 = NULL;


		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group,1);
		$error						 = $database->errorDb("CHECK CAMPAIGNSET",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data						 = $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getCampaignsetData
	**	----------------------------------------------
	**	キャンペーンデータ取得
	**
	**************************************************/

	public function getCampaignsetData($members_data){

		# RESULT初期化
		$result     					= array();
	    $result['id']					= NULL;
	    $result['filesets_id']			= NULL;
	    $result['pay_flg']				= NULL;
	    $result['campaign_type']		= NULL;
	    $result['campaign_stay']		= NULL;

		# CONTINUE
		$continue_flg					= NULL;

		$pays_result					= NULL;

		# 入有・無判別
		if($members_data['pay_count'] > 0){
			$pay_flg					= 1;
		}else{
		    $pay_flg					= 2;
		}


		$timestamp						= date('YmdHis');

		$campaign_conditions			= array();
		$campaign_conditions			= array(
		    'campaign_date_s'			=> $timestamp,
		    'campaign_date_e'			=> $timestamp,
		    'status'					=> 0
		);

		# DB / MAIN CLASS
		$database						 = NULL;
		$database						 = $this->database;
		$output							 = NULL;
		$output							 = $this->output;

		# CAMPAIGN DATA
		$campaign_rtn					= $this->getCampaignsetList($campaign_conditions);

		# ユーザデータとの判別開始
		$i=0;
		while($campaign_data = $database->fetchAssoc($campaign_rtn)){

			# 100個もないでしょ
		    if($i >= 100){
				break;
			}

			$campaign_data['campaign_date_s'] = mb_ereg_replace("[^0-9]", "", $campaign_data['campaign_date_s']);
			$campaign_data['campaign_date_e'] = mb_ereg_replace("[^0-9]", "", $campaign_data['campaign_date_e']);

		    # キャンペーン開始～終了に該当しているか
		    if($campaign_data['campaign_date_s'] > $timestamp || $campaign_data['campaign_date_e'] < $timestamp){
				$i++;
				continue;
		    }

			# 開催中キャンペーンのIDとmembersのcampaignカラムに格納されているIDが一致した場合はそのままスルー
			if($campaign_data['id'] == $members_data['campaign']){



			# 通常のキャンペーン処理
			}else{

				# domain_flg 判定
				if($campaign_data['domain_flg'] != 99999){
					if ($campaign_data['domain_flg'] != $members_data['domain_flg']) {
						$i++;
						continue;
					}
				}

		        # 入金関係
				if($campaign_data['pay_flg'] > 0){

					# 定額
					if($members_data['status'] == 2){

						$pay_flg				= 4;

					# 無料
					}elseif($members_data['status'] == 3){

						$pay_flg				= 3;

					# 通常(0 or 1)
					}else{

						# 入有り
						if($members_data['pay_count'] > 0){

							$pay_flg			= 1;

						# 入無し
						}else{

							$pay_flg			= 2;

						}

					}

					if($campaign_data['pay_flg'] != $pay_flg){
						$i++;
						continue;
					}

				}


		        # 入金回数
		        if(!empty($campaign_data['pay_count_s']) && $campaign_data['pay_count_s'] != '0'){
		            if($campaign_data['pay_count_s'] > $members_data['pay_count']){
		                $i++;
		                continue;
		            }
		        }

		        if(!empty($campaign_data['pay_count_e']) && $campaign_data['pay_count_e'] != '0'){
		            if($campaign_data['pay_count_e'] < $members_data['pay_count']){
		                $i++;
		                continue;
		            }
		        }

		        # 入金金額
		        if(!empty($campaign_data['pay_amount_s']) && $campaign_data['pay_amount_s'] != '0'){
		            if($campaign_data['pay_amount_s'] > $members_data['pay_amount']){
		                $i++;
		                continue;
		            }
		        }

		        if(!empty($campaign_data['pay_amount_e']) && $campaign_data['pay_amount_e'] != '0'){
		            if($campaign_data['pay_amount_e'] < $members_data['pay_amount']){
		                $i++;
		                continue;
		            }
		        }

				# 初期化
				$pays_data['id']					= NULL;

				# ポイント購入あり
				if(!empty($campaign_data['buy']) && $campaign_data['buy'] == 1){

					# 入無しはここで除外
					if($members_data['pay_count'] == 0){
						$i++;
						continue;
					}

					# ここでpaysをチェック
					$pays_where						 = "site_cd = :site_cd ";
					$pays_where						.= "AND user_id = :user_id ";
					$pays_where						.= "AND settlement_id = :settlement_id ";
					$pays_where						.= "AND clear = :clear ";
					$pays_where						.= "AND finish = :finish ";
					$pays_where						.= "AND status = :status ";

					# ポイント購入はsettlement_id = 12
					$pays_array[':site_cd']			 = $members_data['site_cd'];
					$pays_array[':user_id']			 = $members_data['id'];
					$pays_array[':settlement_id']	 = 12;
					$pays_array[':clear']			 = 1;
					$pays_array[':finish']			 = 1;
					$pays_array[':status']			 = 0;
					$pays_order						 = NULL;
					$pays_limit						 = 1;
					$pays_group						 = NULL;

					$pays_column					 = "id";

					$pays_rtn						 = $database->selectDb("pays",$pays_column,$pays_where,$pays_array,$pays_order,$pays_limit,$pays_group);
					$error							 = $database->errorDb("getPayData",$pays_rtn->errorCode(),__FILE__,__LINE__);
					if(!empty($error)){ $output->outputError($error); }

					$pays_data						 = $database->fetchAssoc($pays_rtn);

					# pays result
					$pays_result					 = 1;

					# ポイント購入なかったら除外
					if(empty($pays_data['id'])){
						$i++;
						continue;
					}

				}

				# ポイント購入なし
				if(!empty($campaign_data['buy']) && $campaign_data['buy'] == 2){

					# ここでpaysをチェック
					$pays_where						 = "site_cd = :site_cd ";
					$pays_where						.= "AND user_id = :user_id ";
					$pays_where						.= "AND settlement_id = :settlement_id ";
					$pays_where						.= "AND clear = :clear ";
					$pays_where						.= "AND finish = :finish ";
					$pays_where						.= "AND status = :status ";

					# ポイント購入はsettlement_id = 12
					$pays_array[':site_cd']			 = $members_data['site_cd'];
					$pays_array[':user_id']			 = $members_data['id'];
					$pays_array[':settlement_id']	 = 12;
					$pays_array[':clear']			 = 1;
					$pays_array[':finish']			 = 1;
					$pays_array[':status']			 = 0;
					$pays_order						 = NULL;
					$pays_limit						 = 1;
					$pays_group						 = NULL;

					$pays_column					 = "id";

					$pays_rtn						 = $database->selectDb("pays",$pays_column,$pays_where,$pays_array,$pays_order,$pays_limit,$pays_group);
					$error							 = $database->errorDb("getPayData",$pays_rtn->errorCode(),__FILE__,__LINE__);
					if(!empty($error)){ $output->outputError($error); }

					$pays_data						 = $database->fetchAssoc($pays_rtn);

					# pays result
					$pays_result					 = 1;

					# ポイント購入あったら除外
					if(!empty($pays_data['id'])){
						$i++;
						continue;
					}

				}

		        # 登録日
		        if($campaign_data['reg_date_s'] > 0){
		            if($campaign_data['reg_date_s'] > $members_data['reg_date']){
		                $i++;
		                continue;
		            }
		        }
		        if($campaign_data['reg_date_e'] > 0){
		            if($campaign_data['reg_date_e'] < $members_data['reg_date']){
		                $i++;
		                continue;
		            }
		        }

		        # 初回入金日	//20140227takeuchi追加
		        if($campaign_data['first_pay_s'] > 0){
		            if($campaign_data['first_pay_s'] > $members_data['first_pay_date']){
		                $i++;
		                continue;
		            }
		        }

		        if($campaign_data['first_pay_e'] > 0){
		            if($campaign_data['first_pay_e'] < $members_data['first_pay_date']){
		                $i++;
		                continue;
		            }
		        }

		        # 最終入金日
		        if($campaign_data['pay_date_s'] > 0){
		            if($campaign_data['pay_date_s'] > $members_data['last_pay_date']){
		                $i++;
		                continue;
		            }
		        }

		        if($campaign_data['pay_date_e'] > 0){
		            if($campaign_data['pay_date_e'] < $members_data['last_pay_date']){
		                $i++;
		                continue;
		            }
		        }

		        # 持ちポイント
		        if(!empty($campaign_data['point_s']) && $campaign_data['point_s'] != '0'){
		            if($campaign_data['point_s'] > $members_data['point']){
		                $i++;
		                continue;
		            }
		        }

		        if(!empty($campaign_data['point_e']) && $campaign_data['point_e'] != '0'){
		            if($campaign_data['point_e'] < $members_data['point']){
		                $i++;
		                continue;
		            }
		        }

		        # 後払い 1 利用のみ 2 利用なしのみ
		        if($campaign_data['def_flg'] > 0){
		            if($campaign_data['def_flg'] == 1 && $members_data['def_flg'] == 0){
		                $i++;
		                continue;
		            }
		            if($campaign_data['def_flg'] == 2 && $members_data['def_flg'] == 1){
		                $i++;
		                continue;
		            }
		        }

		        # 性別
		        if($campaign_data['sex'] > 0){
		            if($campaign_data['sex'] != $members_data['sex']){
		                $i++;
		                continue;
		            }
		        }

		        # アドコード
		        if(!empty($campaign_data['ad_code'])){

		            switch($campaign_data['ad_code_type']){
		                # アドコード関係なく
		                case 0:

		                    break;
		                # 完全一致
		                case 1:
		                    if($campaign_data['ad_code'] != $members_data['ad_code']){
		                        $continue_flg			= 1;
		                    }
		                    break;
		                # 前方一致
		                case 2:
		                    if(!preg_match("/^".$campaign_data['ad_code']."/", $members_data['ad_code'])){

		                        $continue_flg			= 1;
		                    }
		                    break;
		                # 中方一致
		                case 3:
		                    if(!preg_match("/".$campaign_data['ad_code']."/", $members_data['ad_code'])){
		                        $continue_flg			= 1;
		                    }
		                    break;
		                # 後方一致
		                case 4:
		                    if(!preg_match("/".$campaign_data['ad_code']."$/", $members_data['ad_code'])){
		                        $continue_flg			= 1;
		                    }
		                    break;
		                # 完全一致否定
		                case 5:
		                    if($campaign_data['ad_code'] == $members_data['ad_code']){
		                        $continue_flg			= 1;
		                    }
		                    break;
		                # 前方一致否定
		                case 6:
		                    if(preg_match("/^".$campaign_data['ad_code']."/", $members_data['ad_code'])){
		                        $continue_flg			= 1;
		                    }
		                    break;
		                # 中方一致否定
		                case 7:
		                    if(preg_match("/".$campaign_data['ad_code']."/", $members_data['ad_code'])){
		                        $continue_flg			= 1;
		                    }
		                    break;
		                # 後方一致否定
		                case 8:
		                    if(preg_match("/".$campaign_data['ad_code']."$/", $members_data['ad_code'])){
		                        $continue_flg			= 1;
		                    }
		                    break;
		            }
		        }


				# CONTINUE
		        if(!empty($continue_flg)){
					$continue_flg						= NULL;
		            $i++;
		            continue;
		        }


			}

			# RESULT
		    $result['id']								= $campaign_data['id'];
		    $result['filesets_id']						= $campaign_data['file_html_id'];
		    $result['pay_flg']							= $pay_flg;
		    $result['campaign_type']					= $campaign_data['campaign_type'];
		    $result['campaign_stay']					= $campaign_data['stay_flg'];

			# 一件でも該当したら処理終了
			break;

		}

		if(!empty($pays_result)){
			$database->freeResult($pays_rtn);
		}

		$database->freeResult($campaign_rtn);

		return $result;


	}


}

?>