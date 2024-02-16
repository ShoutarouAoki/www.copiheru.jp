<?
/********************************************************
**
**	pointsets.php
**	-----------------------------------------------------
**	消費ポイント設定に関するCLASS
**	-----------------------------------------------------
**	inc.ファイルへ出力するhtmlタグも生成してます。
**	キャンペーンが絡んで部分部分他のファイルで呼び出す為
**	-----------------------------------------------------
**	2010.06.06 TAKAI
**
*********************************************************/


class pointsets
{

	# VAR
	private $check_box;
	private $page_title;

	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**	@database接続クラス	読み込み
	**	@HTMLクラス			読み込み
	**	@site_cd			読み込み
	**	@$sec_data			読み込み
	**	@$form_sec_data		読み込み
	**
	**************************************************/

	# CONSTRUCT
	public function __construct($database,$html_class,$site_cd,$post_data=NULL){

		global	$sec_data;
		global	$form_sec_data;

		$this->db			= $database;
		$this->html			= $html_class;
		$this->site_cd		= $site_cd;
		$this->post_data	= $post_data;
		$this->table		= "pointsets";
		$this->sec			= $sec_data;
		$this->sec_form		= $form_sec_data;


		if(!$this->post_data['campaign_id']){
			$this->post_data['campaign_id']	= 0;
		}

		if($this->post_data['sex'] == ""){
			$this->post_data['sex']			= 1;
		}

		if($this->post_data['pay_flg'] == ""){
			$this->post_data['pay_flg']		= 1;
		}

		if($this->post_data['sex'] == 0 && $this->post_data['pay_flg'] == 0){
			$this->page_title	= "デフォルト";
			$this->default_flg	= 1;
		}

		# DOMAIN FLG 引き回し
		if($this->post_data['domain_flg']){
			$this->sec		.= "&domain_flg=".$this->post_data['domain_flg'];
			$this->sec_form .= "<input type=\"hidden\" name=\"domain_flg\" value=\"".$this->post_data['domain_flg']."\" />\n";
		}

    }

	# DESTRUCT
	function __destruct(){

    }


	/*********************************************
	**
	**	checkUserPointUse
	**	-----------------------------------------
	**	消費POINTCHECK
	**
	*********************************************/

	public function checkUserPointUse($point_no_id,$column=NULL){

		if(!$point_no_id || !$this->post_data['user_id']){
			return FALSE;
		}

		# POINT LIST
		if(!$column){
			$column	 = "point";
		}

		# CAMPAIGN
		if($this->post_data['campaign_id']){

			$campaign_column = $column;
			$campaign_where	.= "site_cd = '".$this->site_cd."' ";
			$campaign_where	.= "AND campaign_id = '".$this->post_data['campaign_id']."' ";
			$campaign_where	.= "AND point_no_id = '".$point_no_id."' ";
			$campaign_where	.= "AND status = 0";
			$campaign_rtn		 = $this->db->selectDb($this->table,$campaign_column,$campaign_where,$order,$limit);
			$this->db->errorDb("CHECK USER CAMPAIGN POINT : ",$db->errno,__FILE__,__LINE__);

			$campaign_rows	= $this->db->numRows($campaign_rtn);

			# キャンペーン設定
			if($campaign_rows != 0){
				$campaign_data	= $this->db->fetchAssoc($campaign_rtn);
				$campaign_point	= $campaign_data['point'];
			}

		}

		# 個別設定
		$indiv_column	 = $column;
		$indiv_where	.= "site_cd = '".$this->site_cd."' ";
		$indiv_where	.= "AND user_id = '".$this->post_data['user_id']."' ";
		$indiv_where	.= "AND point_no_id = '".$point_no_id."' ";
		$indiv_where	.= "AND status = 0";
		$indiv_rtn		 = $this->db->selectDb($this->table,$indiv_column,$indiv_where,$order,$limit);
		$this->db->errorDb("CHECK USER INDIV POINT : ",$db->errno,__FILE__,__LINE__);

		$indiv_rows	= $this->db->numRows($indiv_rtn);

		if($indiv_rows != 0){
			$indiv_data		= $this->db->fetchAssoc($indiv_rtn);
			$indiv_point	= $indiv_data['point'];
		}

		if($campaign_point != "" && $indiv_point != ""){

			if($campaign_point > $indiv_point){
				return $indiv_point;
			}elseif($campaign_point < $indiv_point){
				return $campaign_point;
			}else{
				return $campaign_point;
			}

		}elseif($campaign_point != "" && $indiv_point == ""){
			return $campaign_point;
		}elseif($campaign_point == "" && $indiv_point != ""){
			return $indiv_point;
		}

		# DOMAIN FLG
		if($this->post_data['domain_flg'] != "0"){

			$domain_column	 = $column;
			$domain_where	 = "site_cd = '".$this->site_cd."' ";
			$domain_where	.= "AND domain_flg = '".$this->post_data['domain_flg'] ."' ";
			$domain_where	.= "AND point_no_id = '".$point_no_id."' ";
			$domain_where	.= "AND sex = '".$this->post_data['sex']."' ";
			$domain_where	.= "AND pay_flg = '".$this->post_data['pay_flg']."' ";
			$domain_where	.= "AND user_id = '0' ";
			$domain_where	.= "AND campaign_id = '0' ";
			$domain_where	.= "AND status = 0";
			$domain_rtn		 = $this->db->selectDb($this->table,$domain_column,$domain_where,$order,$limit);
			$this->db->errorDb("CHECK USER DOMAIN LIST POINT : ",$db->errno,__FILE__,__LINE__);

			$domain_rows	= $this->db->numRows($domain_rtn);

			if($domain_rows != 0){
				$data	= $this->db->fetchAssoc($domain_rtn);
				return $data['point'];
			}

		}

		# DEFAULT
		$where	 = "site_cd = '".$this->site_cd."' ";
		$where	.= "AND point_no_id = '".$point_no_id."' ";
		$where	.= "AND sex = '".$this->post_data['sex']."' ";
		$where	.= "AND pay_flg = '".$this->post_data['pay_flg']."' ";
		$where	.= "AND user_id = '0' ";
		$where	.= "AND campaign_id = '0' ";
		$where	.= "AND domain_flg = '0' ";
		$where	.= "AND status = 0";
		$order	 = "";
		$limit	 = "";

		$rtn	= $this->db->selectDb($this->table,$column,$where,$order,$limit);
		$this->db->errorDb("CHECK USER DEFAULT POINT: ",$db->errno,__FILE__,__LINE__);

		$data	= $this->db->fetchAssoc($rtn);

		return $data['point'];


	}


}

?>
