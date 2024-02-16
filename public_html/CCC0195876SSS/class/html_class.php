<?
/********************************************************
**
**	html_class.php
**	-----------------------------------------------------
**	管理画面のHTML設定クラス
**	-----------------------------------------------------
**	2010.05.31 TAKAI
*********************************************************/


/*********************************************
**
**	サイトHTML設定 CLASS
**
*********************************************/

class htmlClass
{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**	@database接続クラス読み込み
	**
	**************************************************/

	# CONSTRUCT
	public function __construct(){

		global	$sec_data;
		global	$form_sec_data;

		$this->sec			= $sec_data;
		$this->sec_form		= $form_sec_data;

    }

	# DESTRUCT
	function __destruct(){

    }


	/**************************************************
	**
	**	html_header
	**	----------------------------------------------
	**	HTML HEADER部分呼び出し
	**
	**************************************************/

	function htmlHeader(){

		ob_start();
		print("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
		print("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");
		print("<head>\n");
		print("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n");
		print("<meta name=\"viewport\" content=\"width=device-width, minimum-scale=1, maximum-scale=1\">\n");
		print("<meta http-equiv=\"Content-Language\" content=\"ja\">\n");
		print("<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n");
		print("<meta http-equiv=\"Content-Script-Type\" content=\"text/javascrip\" />\n");
		print("<title>".SITE_NAME."</title>\n");
		print("</head>\n\n");

		print("<body>\n");

	}


	/**************************************************
	**
	**	html_footer
	**	----------------------------------------------
	**	HTML FOOTER部分呼び出し
	**
	**************************************************/

	function htmlFooter(){

		print("</body>\n");
		print("</html>\n");
		ob_end_flush();

	}


	/*********************************************
	**
	**	outputError
	**	------------------------------------------
	**	ERROR 出力
	**
	*********************************************/

	function outputError($str){

		$this->htmlHeader("sub");
		print("<div id=\"error_contents\">\n");
		print("<p>ERROR！！</p>\n");
		print("<div>\n");
		print($str."\n");
		print("</div>\n");
		print("</div>\n\n");
		$this->htmlFooter();

	}


	/*********************************************
	**
	**	outputExection
	**	------------------------------------------
	**	EXE 出力
	**
	*********************************************/

	function outputExection($str){

		$result	 = "<div id=\"exe_contents\">\n";
		$result	.= "<p>COMPLETED！！</p>\n";
		$result	.= "<div>\n";
		$result	.= $str."<br />\n";
		$result	.= "</div>\n";
		$result	.= "</div>\n\n";

		return $result;

	}


	/**************************************************
	**
	**	getSelectTags
	**	----------------------------------------------
	**	SELECT TAG 生成
	**
	**	@$select		: select name
	**	@$option_array	: array
	**	@$value			: value
	**	@$display		: contents
	**	@$count			: 初期$i値
	**	@$check			: cheked値
	**	@$opt_index		: 選択して下さい表示
	**
	**************************************************/

	function getSelectTags($select_name,$option_array,$value,$display,$count,$check="",$opt_index){

		$array_count	= count($option_array);

		if($value == ""){ $value = 0; }
		if($display == ""){ $display = 1; }
		if($count == ""){ $count = 0; }

		$result  = "<select name=\"".$select_name."\">\n";
		if($opt_index){
			$result .= "<option value=\"\">選択して下さい</option>\n";
		}

		for($i=$count;$i<$array_count;$i++){
			if($check != "" && $option_array[$i][$value] == $check){
				$result .= "<option value=\"".$option_array[$i][$value]."\" selected>".$option_array[$i][$display]."</option>\n";
			}else{
				$result .= "<option value=\"".$option_array[$i][$value]."\">".$option_array[$i][$display]."</option>\n";
			}
		}

		$result .= "</select>\n";

		return $result;

	}



	/**************************************************
	**
	**	getRadioTags
	**	----------------------------------------------
	**	RADIO TAG 生成
	**	@$select		: radio name
	**	@$option_array	: array
	**	@$value			: value
	**	@$display		: contents
	**	@$count			: 初期$i値
	**	@$check			: checked
	**
	**************************************************/

	function getRadioTags($select_name,$option_array,$value,$display,$count,$check=""){

		$array_count	= count($option_array);

		if($value == ""){ $value = 0; }
		if($display == ""){ $display = 1; }
		if($count == ""){ $count = 0; }

		for($i=$count;$i<$array_count;$i++){
			if($check != "" && $option_array[$i][$value] == $check){
				$result .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][$value]."\" checked>";
				$result .= $option_array[$i][$display]."\n";
			}else{
				$result .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][$value]."\">";
				$result .= $option_array[$i][$display]."\n";
			}
		}

		return $result;

	}


	/**************************************************
	**
	**	getParaSelect
	**	----------------------------------------------
	**	SELECT TAG 生成
	**	パラメーター用
	**
	**************************************************/

	function getParaSelect($select_name,$option_array,$site_cd,$sex,$check=""){

		$array_count	= count($option_array);

		$result  = "<select name=\"".$select_name."\">\n";

		for($i=0;$i<$array_count;$i++){

			if($option_array[$i][4] != $site_cd){ continue; }

			if($check != "" && $option_array[$i][0] == $check){
					$result .= "<option value=\"".$option_array[$i][0]."\" selected>".$option_array[$i][$sex]."</option>\n";
			}else{
					$result .= "<option value=\"".$option_array[$i][0]."\">".$option_array[$i][$sex]."</option>\n";
			}
		}

		$result .= "</select>\n";

		return $result;

	}


	/**************************************************
	**
	**	getParaRadio
	**	----------------------------------------------
	**	RADIO TAG 生成
	**	パラメーター用
	**
	**************************************************/

	function getParaRadio($select_name,$option_array,$site_cd,$sex,$check=""){

		$array_count	= count($option_array);

		for($i=0;$i<$array_count;$i++){

			if($option_array[$i][3] != $site_cd){ continue; }

			if($check != "" && $option_array[$i][0] == $check){
					$result .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][0]."\" checked>";
					$result .= $option_array[$i][$sex]."\n";
			}else{
					$result .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][0]."\">";
					$result .= $option_array[$i][$sex]."\n";
			}
		}

		return $result;

	}


	/**************************************************
	**
	**	getParaSelectName
	**	----------------------------------------------
	**	NAME 生成
	**	パラメーター用
	**
	**************************************************/

	function getParaSelectName($select_name,$option_array,$site_cd,$sex,$check=""){

		$array_count	= count($option_array);

		for($i=0;$i<$array_count;$i++){
			if($option_array[$i][4] != $site_cd){ continue; }
			if($check != "" && $option_array[$i][0] == $check){
				$result .= $option_array[$i][$sex];
				break;
			}
		}

		return $result;

	}



	/**************************************************
	**
	**	htmlSubmit
	**	----------------------------------------------
	**	SUBMIT TAG 生成
	**
	**************************************************/

	function htmlSubmit($str,$click_type){

		if($click_type){
			$on_click	= " onClick=\"return confirm('".$str."しますか？')\"";
		}

		$result	= "<input type=\"submit\" class=\"button\" value=\"".$str."\"".$on_click." />\n";

		return $result;

	}


	/**************************************************
	**
	**	getSegmentSelectIndex
	**	----------------------------------------------
	**	通常検索用SELECT INDEX
	**
	**************************************************/

	function getSegmentSelectIndex(){

		global	$select_index_array;

		print($this->getSelectTags("select_index",$select_index_array,2,NULL,NULL,NULL,NULL));

	}


	/**************************************************
	**
	**	getSegmentStatus
	**	----------------------------------------------
	**	通常検索用ステータス
	**
	**************************************************/

	function getSegmentStatus($count=NULL){

		global	$status_array;

		print($this->getSelectTags("status",$status_array,NULL,NULL,$count,NULL,NULL));

	}


	/**************************************************
	**
	**	getOpeSegmentStatus
	**	----------------------------------------------
	**	キャラ用ステータス
	**
	**************************************************/

	function getOpeSegmentStatus(){

		global	$ope_status_array;

		print($this->getSelectTags("status",$ope_status_array,2,NULL,1,NULL,NULL));

	}


	/**************************************************
	**
	**	getSegmentMailStatus
	**	----------------------------------------------
	**	同報検索用ステータス
	**
	**************************************************/

	function getSegmentMailStatus(){

		global	$send_status_array;

		print($this->getSelectTags("status",$send_status_array,NULL,NULL,NULL,NULL,NULL));

	}


	/**************************************************
	**
	**	getSegmentSex
	**	----------------------------------------------
	**	検索用性別
	**
	**************************************************/

	function getSegmentSex($check="0",$index="0"){

		global	$sex_array;

		print($this->getRadioTags("sex",$sex_array,NULL,NULL,$index,$check));

	}


	/**************************************************
	**
	**	getSegmentPoint
	**	----------------------------------------------
	**	検索用ポイント
	**
	**************************************************/

	function getSegmentPoint($start=NULL,$end=NULL){

		print("<input type=\"text\" name=\"point_s\" size=\"4\" maxlength=\"6\" class=\"text_num\" value=\"".$start."\" />ポイント以上\n");
		print("<input type=\"text\" name=\"point_e\" size=\"4\" maxlength=\"6\" class=\"text_num\" value=\"".$end."\" />ポイント以下\n");

	}


	/**************************************************
	**
	**	getSegmentSpoint
	**	----------------------------------------------
	**	検索用ポイント
	**
	**************************************************/

	function getSegmentSpoint($start=NULL,$end=NULL){

		print("<input type=\"text\" name=\"s_point_s\" size=\"4\" maxlength=\"6\" class=\"text_num\" value=\"".$start."\" />ポイント以上\n");
		print("<input type=\"text\" name=\"s_point_e\" size=\"4\" maxlength=\"6\" class=\"text_num\" value=\"".$end."\" />ポイント以下\n");

	}


	/**************************************************
	**
	**	getSegmentAge
	**	----------------------------------------------
	**	検索用年齢
	**
	**************************************************/

	function getSegmentAge($start=NULL,$end=NULL){

		print("<input type=\"text\" name=\"age_s\" size=\"4\" maxlength=\"2\" class=\"text_num\" value=\"".$start."\" />歳～\n");
		print("<input type=\"text\" name=\"age_e\" size=\"4\" maxlength=\"2\" class=\"text_num\" value=\"".$end."\" />歳\n");

	}


	/**************************************************
	**
	**	getSegmentDate
	**	----------------------------------------------
	**	検索用本 日時指定
	**
	**************************************************/

	function getSegmentDate($name,$date=NULL){

		if($date['y1']){ $y1 = $date['y1']; }else{ $y1 = date("Y"); }
		if($date['m1']){ $m1 = $date['m1']; }else{ $m1 = date("m"); }
		if($date['d1']){ $d1 = $date['d1']; }else{ $d1 = date("d"); }
		if($date['h1']){ $h1 = $date['h1']; }else{ $h1 = date("H"); }
		if($date['i1']){ $i1 = $date['i1']; }else{ $i1 = date("i"); }
		if($date['d1']){ $s1 = $date['s1']; }else{ $s1 = date("s"); }

		if($date['y2']){ $y2 = $date['y2']; }else{ $y2 = date("Y"); }
		if($date['m2']){ $m2 = $date['m2']; }else{ $m2 = date("m"); }
		if($date['d2']){ $d2 = $date['d2']; }else{ $d2 = date("d"); }
		if($date['h2']){ $h2 = $date['h2']; }else{ $h2 = date("H"); }
		if($date['i2']){ $i2 = $date['i2']; }else{ $i2 = date("i"); }
		if($date['d2']){ $s2 = $date['s2']; }else{ $s2 = date("s"); }


		print("<input type=\"text\" name=\"".$name."_y1\" class=\"text_year\" maxlength=\"4\" value=\"".$y1."\" />年\n");
		print("<input type=\"text\" name=\"".$name."_m1\" class=\"text_month\" maxlength=\"2\" value=\"".$m1."\" />月\n");
		print("<input type=\"text\" name=\"".$name."_d1\" class=\"text_month\" maxlength=\"2\" value=\"".$d1."\" />日\n");
		print("<input type=\"text\" name=\"".$name."_h1\" class=\"text_month\" maxlength=\"2\" value=\"".$h1."\" />時\n");
		print("<input type=\"text\" name=\"".$name."_i1\" class=\"text_month\" maxlength=\"2\" value=\"".$i1."\" />分\n");
		print("<input type=\"text\" name=\"".$name."_s1\" class=\"text_month\" maxlength=\"2\" value=\"".$s1."\" />秒\n");
		print("から<br />\n");
		print("<input type=\"text\" name=\"".$name."_y2\" class=\"text_year\" maxlength=\"4\" value=\"".$y2."\" />年\n");
		print("<input type=\"text\" name=\"".$name."_m2\" class=\"text_month\" maxlength=\"2\" value=\"".$m2."\" />月\n");
		print("<input type=\"text\" name=\"".$name."_d2\" class=\"text_month\" maxlength=\"2\" value=\"".$d2."\" />日\n");
		print("<input type=\"text\" name=\"".$name."_h2\" class=\"text_month\" maxlength=\"2\" value=\"".$h2."\" />時\n");
		print("<input type=\"text\" name=\"".$name."_i2\" class=\"text_month\" maxlength=\"2\" value=\"".$i2."\" />分\n");
		print("<input type=\"text\" name=\"".$name."_s2\" class=\"text_month\" maxlength=\"2\" value=\"".$s2."\" />秒\n");
		print("まで\n");

	}


	/**************************************************
	**
	**	getSegmentPref
	**	----------------------------------------------
	**	検索用都道府県 CHECKBOX
	**
	**************************************************/

	function getSegmentPref(){

		global	$pref_array;

		$array_count	= count($pref_array);

		for($pref_cnt=1;$pref_cnt<$array_count;$pref_cnt++){
			print("<input type=\"checkbox\" name=\"pref[]\" value=\"".$pref_array[$pref_cnt][0]."\" />".$pref_array[$pref_cnt][1]."\n");
		}

	}


	/**************************************************
	**
	**	getSegmentPrefSelect
	**	----------------------------------------------
	**	検索用都道府県 SELECT
	**
	**************************************************/

	function getSegmentPrefSelect(){

		global	$pref_array;

		$array_count	= count($pref_array);

		print("<select name=\"pref\">\n");
		for($pref_cnt=1;$pref_cnt<$array_count;$pref_cnt++){
			print("<option value=\"".$pref_array[$pref_cnt][0]."\">".$pref_array[$pref_cnt][1]."</option>\n");
		}
		print("</select>\n\n");


	}


	/**************************************************
	**
	**	getSegmentDef
	**	----------------------------------------------
	**	検索用後払い
	**
	**************************************************/

	function getSegmentDef($check="0",$index="0"){

		global	$def_array2;

		print($this->getRadioTags("def_flg",$def_array2,NULL,NULL,$index,$check));

	}


	/**************************************************
	**
	**	getSegmentPay
	**	----------------------------------------------
	**	検索用入金タイプ
	**
	**************************************************/

	function getSegmentPay($check="2",$index="0"){

		global	$pay_count_array;

		print $this->getRadioTags("pay_flg",$pay_count_array,NULL,NULL,$index,$check);

	}


	/**************************************************
	**
	**	getSegmentPayType
	**	----------------------------------------------
	**	検索用入金種別
	**
	**************************************************/

	function getSegmentPayType($settlement=NULL){

		global	$settlement_array;

		$array_count	= count($settlement_array);

		for($pay_cnt=1;$pay_cnt<count($settlement_array);$pay_cnt++){

			if($settlement['use_bank'] == 0 && $settlement_array[$pay_cnt][0] == 1){
				continue;
			}elseif($settlement['use_credit'] == 0 && $settlement_array[$pay_cnt][0] == 2){
				continue;
			}elseif($settlement['use_bit'] == 0 && $settlement_array[$pay_cnt][0] == 3){
				continue;
			}elseif($settlement['use_direct'] == 0 && $settlement_array[$pay_cnt][0] == 4){
				continue;
			}elseif($settlement['use_ccheck'] == 0 && $settlement_array[$pay_cnt][0] == 5){
				continue;
			}elseif($settlement['use_fregi'] == 0 && $settlement_array[$pay_cnt][0] == 6){
				continue;
			}

			print("<input type=\"checkbox\" name=\"pay_type[]\" value=\"".$settlement_array[$pay_cnt][2]."\">".$settlement_array[$pay_cnt][1]."\n");


		}

	}


	/**************************************************
	**
	**	getSegmentPayCount
	**	----------------------------------------------
	**	検索用入金回数
	**
	**************************************************/

	function getSegmentPayCount($start=NULL,$end=NULL){

		print("<input type=\"text\" name=\"pay_count_s\" size=\"3\" class=\"text_num\" value=\"".$start."\" />回～\n");
		print("<input type=\"text\" name=\"pay_count_e\" size=\"3\" class=\"text_num\" value=\"".$end."\" />回\n");

	}


	/**************************************************
	**
	**	getSegmentPayAmount
	**	----------------------------------------------
	**	検索用入金総額
	**
	**************************************************/

	function getSegmentPayAmount($start=NULL,$end=NULL){

		print("<input type=\"text\" name=\"pay_amount_s\" size=\"10\" class=\"text_num\" value=\"".$start."\" />円～\n");
		print("<input type=\"text\" name=\"pay_amount_e\" size=\"10\" class=\"text_num\" value=\"".$end."\" />円\n");

	}


	/**************************************************
	**
	**	getSegmentNoSex
	**	----------------------------------------------
	**	検索用性別不明ユーザー
	**
	**************************************************/

	function getSegmentNoSex($sex){

		if($sex == 1){
			$disp_sex	= "女性ユーザー";
		}elseif($sex == 2){
			$disp_sex	= "男性ユーザー";
		}

		print("<input type=\"radio\" name=\"send_sex\" value=\"0\" checked />".$disp_sex."\n");
		print("<input type=\"radio\" name=\"send_sex\" value=\"1\" />性別不明ユーザー\n");

	}


	/**************************************************
	**
	**	getSegmentMailFlg
	**	----------------------------------------------
	**	検索用メールタイプ
	**
	**************************************************/

	function getSegmentMailFlg($check=NULL,$index='0'){

		global $mailflg_array;

		print $this->getSelectTags("mail_flg",$mailflg_array,NULL,NULL,$index,$check,NULL);

	}


	/**************************************************
	**
	**	getSegmentDevice
	**	----------------------------------------------
	**	検索用デバイス
	**
	**************************************************/

	function getSegmentDevice($check=NULL,$index="1"){

		global	$send_device_array;

		print $this->getSelectTags("device",$send_device_array,NULL,NULL,$index,$check,NULL);

	}


	/**************************************************
	**
	**	getSegmentMedia
	**	----------------------------------------------
	**	検索用メディア
	**
	**************************************************/

	function getSegmentMedia($check=NULL){

		global	$media_array;

		print $this->getSelectTags("media_flg",$media_array,NULL,NULL,NULL,$check,NULL);

	}


	/**************************************************
	**
	**	getSegmentAdCode
	**	----------------------------------------------
	**	検索用アドコード
	**
	**************************************************/

	function getSegmentAdCode($check=NULL,$value=NULL,$count=NULL){

		global	$ad_code_array;

		print $this->getSelectTags("ad_code_type",$ad_code_array,NULL,NULL,$count,$check,NULL);
		print("<input type=\"text\" name=\"ad_code\" size=\"25\" value=\"".$value."\" />\n");

	}


	/**************************************************
	**
	**	getSegmentSendType
	**	----------------------------------------------
	**	送信タイプ選択
	**
	**************************************************/

	function getSegmentSendType($send_array){

		print $this->getRadioTags("send_type",$send_array,NULL,NULL,"1","1");

	}


	/**************************************************
	**
	**	getSegmentFromName
	**	----------------------------------------------
	**	送信FROM NAME選択
	**
	**************************************************/

	function getSegmentFromName($check=NULL,$index="1"){

		global	$send_fromname_array;

		print $this->getRadioTags("from_name",$send_fromname_array,NULL,NULL,$index,$check);

	}


	/**************************************************
	**
	**	getSendReplace
	**	----------------------------------------------
	**	％変換表示 -> 送信用
	**
	**************************************************/

	function getSendReplace($select_array,$title){

		$array_count	= count($select_array);

		print("<div class=\"title_sub\">".$title." %変換</div>\n");
		print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n");

		$i=1;
		for($replace_cnt=0;$replace_cnt<$array_count;$replace_cnt++){

			$tr_cnt = $i/2;
			$chk = is_int($tr_cnt);
			if($chk == "1"){
				$tr1 = "";
				$tr2 = "</tr>\n\n";
			}else{
				$tr1 = "<tr>\n";
				$tr2 = "";
			}

			print($tr1);
			print("<td class=\"table_title\" width=\"80\">".$select_array[$replace_cnt][1]."</td>\n");
			print("<td class=\"table_contents\">".$select_array[$replace_cnt][2]."</td>\n");
			print($tr2);

			$i++;

		}

		print("</table>\n");

	}


	/**************************************************
	**
	**	getDeliveryReplace
	**	----------------------------------------------
	**	％変換表示 -> 配信用
	**
	**************************************************/

	function getDeliveryReplace($select_array,$title){

		$array_count	= count($select_array);

		print("<div class=\"title_sub\">".$title." %変換</div>\n");
		print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n");

		for($replace_cnt=0;$replace_cnt<$array_count;$replace_cnt++){

			print("<tr>\n");
			print("<td class=\"table_title\" width=\"80\">".$select_array[$replace_cnt][1]."</td>\n");
			print("<td class=\"table_contents\">".$select_array[$replace_cnt][2]."</td>\n");
			print("</tr>\n");

		}

		print("</table>\n");

	}



	/**************************************************
	**
	**	make_date
	**	----------------------------------------------
	**	日付 生成
	**
	**************************************************/

	function makeDate($date){

		$data['y']	= date("Y", strtotime($date));
		$data['m']	= date("m", strtotime($date));
		$data['d']	= date("d", strtotime($date));
		$data['h']	= date("H", strtotime($date));
		$data['i']	= date("i", strtotime($date));
		$data['s']	= date("s", strtotime($date));

		return $data;

	}


}


?>
