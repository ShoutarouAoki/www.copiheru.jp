<?
/********************************************************
**
**	campaignsets.php
**	-----------------------------------------------------
**	campaignsets CLASS
**	-----------------------------------------------------
**	inc.ファイルへ出力するhtmlタグも生成してます。
**	キャンペーンが絡んで部分部分他のファイルで呼び出す為
**	-----------------------------------------------------
**	2010.06.02 takai
**
*********************************************************/

/* REQUIRE CLASS FILE */
require_once(dirname(__FILE__).'/../class/members.php');

class campaignsets
{

	# VAR
	private $name;
	private $image_list;
	private $category_select;
	private $image_dir;
	private $textarea_rows;
	private $sex;


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
	public function __construct($database=NULL,$html_class=NULL,$site_cd=NULL,$post_data=NULL){

		global	$sec_data;
		global	$form_sec_data;
		global	$campaign_type_array;
		global	$pay_count_array;
		global	$def_array2;
		global	$ad_code_array;
		global	$sex_array;

		$this->db			= $database;
		$this->html			= $html_class;
		$this->site_cd		= $site_cd;
		$this->post_data	= $post_data;
		$this->table		= "campaignsets";
		$this->category		= 99;
		$this->sec			= $sec_data;
		$this->sec_form		= $form_sec_data;

		# 配列
		$this->campaign_type_array	= $campaign_type_array;
		$this->pay_count_array		= $pay_count_array;
		$this->def_array2			= $def_array2;
		$this->ad_code_array		= $ad_code_array;
		$this->sex_array			= $sex_array;

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
	**	checkUserCampaign
	**	------------------------------------------
	**	ユーザーがキャンペーンにHITしているかCHECK
	**	------------------------------------------
	**	$campaign_type	= 1 : サービスポイント
	**	$campaign_type	= 2 : 消費ポイント
	**
	*********************************************/

	public function checkUserCampaign($user_id,$campaign_type=NULL) {

		if(!$user_id){
			return FALSE;
		}

		# USER DATA
		$members	= new members();
		$user_data	= $members->getUserData($user_id);

		$check_date	= date("Y-m-d H:i:s");

		$table	 = "campaignsets";
		$column	 = "*";
		$where	.= "site_cd = ".$user_data['site_cd']." ";
		$where	.= "AND campaign_date_s <= '".$check_date."' ";
		$where	.= "AND campaign_date_e >= '".$check_date."' ";
		if($campaign_type){
		$where	.= "AND campaign_type = ".$campaign_type." ";
		}
		$where	.= "AND status = 0";
		$order	 = "";
		$limit	 = "";

		$rtn	= $this->db->selectDb($table,$column,$where,$order,$limit);
		$this->db->errorDb("",$db->errno,___,__LINE__);
		if($this->db_err){ print($this->db_err); exit; }

		$rows	= $this->db->numRows($rtn);

		if($rows != 0){

			while($data	= $this->db->fetchAssoc($rtn)){

				# 性別
				if($data['sex'] == 1 && $user_data['sex'] != 1){
					continue;
				}elseif($data['sex'] == 2 && $user_data['sex'] != 2){
					continue;
				}else{

				}

				# 入金有無
				if($data['pay_flg'] == 1 && $user_data['pay_count'] == "0"){
					continue;
				}elseif($data['pay_flg'] == 2 && $user_data['pay_count'] != "0"){
					continue;
				}else{

				}

				# 入金回数
				if($data['pay_count_s'] && $data['pay_count_e']){

					if($user_data['pay_count'] >= $data['pay_count_s'] && $user_data['pay_count'] <= $data['pay_count_e']){
						# OK
					}else{
						continue;
					}

				}

				# 入金総額
				if($data['pay_amount_s'] && $data['pay_amount_e']){

					if($user_data['pay_amount'] >= $data['pay_amount_s'] && $user_data['pay_amount'] <= $data['pay_amount_e']){
						# OK
					}else{
						continue;
					}

				}

				# 本登録日
				if($data['reg_date_s'] != '0000-00-00 00:00:00' && $data['reg_date_e'] != '0000-00-00 00:00:00'){

					if($user_data['reg_date'] >= $data['reg_date_s'] && $user_data['reg_date'] <= $data['reg_date_e']){
						# OK
					}else{
						continue;
					}

				}



				# 最終入金日
				if($data['pay_date_s'] != '0000-00-00 00:00:00' && $data['pay_date_e'] != '0000-00-00 00:00:00'){

					if($user_data['last_pay_date'] >= $data['pay_date_s'] && $user_data['last_pay_date'] <= $data['pay_date_e']){
						# OK
					}else{
						continue;
					}

				}

				# 持ちポイント
				if($data['point_s'] && $data['point_e']){

					if($user_data['point'] >= $data['point_s'] && $user_data['point'] <= $data['point_e']){
						# OK
					}else{
						continue;
					}

				}

				# 後払い有無
				if($data['def_flg'] == "1" && $user_data['def_flg'] == "0"){
					continue;
				}elseif($data['def_flg'] == "2" && $user_data['def_flg'] != "0"){
					continue;
				}else{

				}

				# アドコード
				if($data['ad_code'] && $data['ad_code_type'] != 0){

					# 完全一致
					if($data['ad_code_type'] == 1){
						if($data['ad_code'] != $user_data['ad_code']){ continue; }
					# 前方一致
					}elseif($data['ad_code_type'] == 2){
						if(!preg_match("/^".$data['ad_code']."/",$user_data['ad_code'])){ continue; }
					# 中間一致
					}elseif($data['ad_code_type'] == 3){
						if(!preg_match("/".$data['ad_code']."/",$user_data['ad_code'])){ continue; }
					# 後方一致
					}elseif($data['ad_code_type'] == 4){
						if(!preg_match("/".$data['ad_code']."$/",$user_data['ad_code'])){ continue; }
					# 完全一致(除外)
					}elseif($data['ad_code_type'] == 5){
						if($data['ad_code'] == $user_data['ad_code']){ continue; }
					# 前方一致(除外)
					}elseif($data['ad_code_type'] == 6){
						if(preg_match("/^".$data['ad_code']."/",$user_data['ad_code'])){ continue; }
					# 中間一致(除外)
					}elseif($data['ad_code_type'] == 7){
						if(preg_match("/".$data['ad_code']."/",$user_data['ad_code'])){ continue; }
					# 後方一致(除外)
					}elseif($data['ad_code_type'] == 8){
						if(preg_match("/".$data['ad_code']."$/",$user_data['ad_code'])){ continue; }
					}

				}

				$campaign_id	= $data['id'];
				break;

			}

		}

		if($campaign_id){
			return $campaign_id;
		}else{
			return FALSE;
		}

	}


}

?>
