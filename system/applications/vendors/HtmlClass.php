<?php
/********************************************************************************
**	
**	HtmlClass.php
**	=============================================================================
**
**	■PAGE / 
**	HTML MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	HTML CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	HTML HELPER
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: AKITOSHI TAKAI
**	CREATE DATE : 2012/12/01
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
class HtmlClass{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	private	$browser;

	# CONSTRUCT
	function __construct(){
		$this->browser	= $_SERVER['HTTP_USER_AGENT'];
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	obStartHeader
	**	----------------------------------------------
	**	HEADER
	**
	**************************************************/

	public function obStartHeader($device,$type,$check=NULL,$account=NULL){

		# MOBILE
		if($device == "mobile"){

			$this->callXml($check,$type);

			if(!defined("SYSTEM_CHECK")){ ob_start("mb_output_handler"); }

		# OTHER
		}else{

			if(!defined("SYSTEM_CHECK")){ ob_start(); }

		}

		/************************************************
		**
		**	OUTPUT ADD REWRITE VAR
		**	---------------------------------------
		**	PREVIEW用変数受け渡し
		**	上記 obStartHeader / ob_start
		**	バッティングする為ここで出力
		**
		************************************************/

		# SITE ID -> PREVIEW
		if(!empty($account['preview'])){
			output_add_rewrite_var("preview",$account['preview']);
		}

		# DEVICE / VIEW ID
		if(!empty($account['d'])){
			output_add_rewrite_var("d",$account['d']);
		}

	}



	/**************************************************
	**
	**	callXml
	**	----------------------------------------------
	**	HEADER
	**
	**************************************************/

	public function callXml($device,$type){

		# DOCOMO
		if($device == "mobile" && $type == "DoCoMo"){
			header("Content-Type: text/xml; charset=".SITE_CHARSET."");
			print("<?xml version=\"1.0\" encoding=\"".SITE_CHARSET."\"?>");
			output_add_rewrite_var("device",$type);
		}

	}



	/**************************************************
	**
	**	htmlFooter
	**	----------------------------------------------
	**	HTML FOOTER部分呼び出し
	**
	**************************************************/

	public function htmlFooter(){

		print("</body>\n");
		print("</html>\n");

	}



	/**************************************************
	**
	**	linkPath
	**	----------------------------------------------
	**	LINK PATH 生成
	**
	**************************************************/

	public function linkPath($page){

		# URLの正規表現パターン
		$pattern = '/https:\/\/[0-9a-z_,.:;&=+*%$#!?@()~\'\/-]+/i';

	    if(preg_match($pattern,$page)){
			$result		= "<a href=\"".$page."\" target=\"_blank\">";
		}elseif(defined("DIR")){
			$result		= "<a href=\"".DIR.$page."\">";
		}else{
			$result		= "<a href=\"".$page."\">";
		}

		print($result);

	}



	/**************************************************
	**
	**	rootPath
	**	----------------------------------------------
	**	ROOT PATH 生成
	**
	**************************************************/

	public function rootPath($page){

		if(defined("DIR")){
			$result		= DIR.$page;
		}else{
			$result		= $page;
		}

		print($result);

	}



	/**************************************************
	**
	**	getRequestData
	**	----------------------------------------------
	**	PHP $_REQUEST SET
	**
	**************************************************/

	public function getRequestData($data,$null_value){

		if(!empty($data)){
			$result	= $data;
		}else{
			$result	= $null_value;
		}

		return $result;

	}



	/**************************************************
	**
	**	getHiddenTags
	**	----------------------------------------------
	**	HTML RETURN BUTTON 生成
	**
	**************************************************/

	public function getHiddenTags($hidden_data,$unset=NULL){

		$result		= NULL;

		# HIDDEN DATA 生成
		if(!empty($hidden_data)){
			foreach($hidden_data as $key => $value){
				if(!empty($unset) && $key == $unset){ continue; }
				if($key == "token"){ continue; }
				$result .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\" />\n";
			}
		}

		return $result;

	}



	/**************************************************
	**
	**	getFormTags
	**	----------------------------------------------
	**	HTML FORM 生成
	**
	**************************************************/

	public function getFormTags($str,$form_path,$click_type,$hidden_data){

		# POST DATA 生成
		$post_data	= $this->getHiddenTags($hidden_data);

		$form_data	 = "\n<form action=\"".$form_path."\" method=\"post\">\n";
		$form_data	.= $post_data;
		$form_data	.= $this->getSubmitTags($str,$click_type);
		$form_data	.= "</form>\n";

		return $form_data;

	}



	/**************************************************
	**
	**	getSelectTags
	**	----------------------------------------------
	**	SELECT TAG 生成
	**
	**************************************************/

	public function getSelectTags($select_name,$option_array,$check=NULL,$option_type=NULL,$start=NULL){

		if($select_name == "pref"){
			$java		= " onChange=\"getCity(this.options[this.selectedIndex].value);return false;\"";
		}elseif($select_name == "genre"){
			$java		= " onChange=\"getCategory(this.options[this.selectedIndex].value);return false;\"";
		}else{
			$java		= NULL;
		}

		$select_option	= NULL;
		$select_option  = "<select name=\"".$select_name."\"".$java.">\n";

		if(!empty($option_type)){
			$select_option .= "<option value=\"\">選択して下さい</option>\n";
		}

		$count = count($option_array);

		if(!empty($start)){
			$j	= "0";
		}else{
			$j	= "1";
		}

		for($i=$j;$i<$count;$i++){
			if($check != "" && $option_array[$i][0] == $check){
				$select_option .= "<option value=\"".$option_array[$i][0]."\" selected=\"selected\">".$option_array[$i][1]."</option>\n";
			}else{
				$select_option .= "<option value=\"".$option_array[$i][0]."\">".$option_array[$i][1]."</option>\n";
			}
		}

		$select_option .= "</select>\n";

		return $select_option;

	}



	/**************************************************
	**
	**	getRadioTags
	**	----------------------------------------------
	**	RADIO TAG 生成
	**
	**************************************************/

	public function getRadioTags($select_name,$option_array,$check=NULL,$onclick=NULL,$start=NULL,$label=NULL){

		if($onclick == 1){
			$java	= " onClick=\"showContents(this.value);\"";
		}elseif($onclick == 2){
			$java	= " onClick=\"showHideContents(this.value);\"";
		}else{
			$java	= NULL;
		}

		$count = count($option_array);

		if(!empty($start)){
			$j	= "0";
		}else{
			$j	= "1";
		}

		$select_radio			= NULL;

		for($i=$j;$i<$count;$i++){

			if(!empty($label)){
				$label_name		= $label.$option_array[$i][0].$i;
			}else{
				$label_name		= $select_name.$option_array[$i][0].$i;
			}

			if(isset($check)){

				if($option_array[$i][0] == $check){
					$select_radio .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][0]."\"".$java." checked=\"checked\" id=\"".$label_name."\" />";
				}else{
					$select_radio .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][0]."\"".$java." id=\"".$label_name."\" />";
				}

			}else{

				if($start && $i == 0){
					$select_radio .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][0]."\"".$java." checked=\"checked\" id=\"".$label_name."\" />";
				}elseif(!$start && $i == 1){
					$select_radio .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][0]."\"".$java." checked=\"checked\" id=\"".$label_name."\" />";
				}else{
					$select_radio .= "<input type=\"radio\" name=\"".$select_name."\" value=\"".$option_array[$i][0]."\"".$java." id=\"".$label_name."\" />";
				}

			}

			$select_radio .= "<label for=\"".$label_name."\">";
			$select_radio .= $option_array[$i][1];
			$select_radio .= "</label>\n";

		}

		return $select_radio;

	}



	/**************************************************
	**
	**	getSubmitTags
	**	----------------------------------------------
	**	SUBMIT TAG 生成
	**
	**************************************************/

	public function getSubmitTags($str,$click_type){

		if($click_type == 1){
			$on_click	= " onClick=\"return confirm('".$str."しますか？'); return false;\"";
		}elseif($click_type == 2){
			$on_click	= " class=\"submit\"";
		}elseif($click_type == 3){
			$on_click	= " class=\"submit\" onClick=\"return confirm('".$str."しますか？'); return false;\"";
		}

		$submit	= "<input type=\"submit\" value=\"".$str."\"".$on_click." />\n";

		return $submit;

	}



	/**************************************************
	**
	**	getSubmitButton
	**	----------------------------------------------
	**	SUBMIT BUTTON 生成
	**
	**************************************************/

	public function getSubmitButton($id,$value){

		if(!(preg_match("/Windows/",$this->browser) && preg_match("/MSIE 6/",$this->browser))){
			$submit	= "<input type=\"submit\" id=\"".$id."\" value=\"&nbsp;\" />\n";
		}else{
			$submit	= "<input type=\"submit\" class=\"submit_main\" value=\"".$value."\" />\n";
		}

		print($submit);

	}



	/**************************************************
	**
	**	getSubmitImages
	**	----------------------------------------------
	**	SUBMIT IMAGE 生成
	**
	**************************************************/

	public function getSubmitImages($str,$click_type,$purpose){

		if(isset($click_type)){
			$on_click	= " onClick=\"return confirm('".$str."しますか？'); return false;\"";
		}else{
			$on_click	= NULL;
		}

		# BROWSER NORMAL
		if(!(preg_match("/Windows/",$this->browser) && preg_match("/MSIE 6/",$this->browser))){

			# ENTRY
			if($purpose == 1){
				$button	= "<input type=\"submit\" id=\"entry\" value=\"&nbsp;\"".$on_click." />\n";
			# ACCOUNT
			}elseif($purpose == 2){
				$button	= "<input type=\"submit\" id=\"account\" value=\"&nbsp;\"".$on_click." />\n";
			# CHECK
			}elseif($purpose == 3){
				$button	= "<input type=\"submit\" id=\"check\" value=\"&nbsp;\"".$on_click." />\n";
			# RETURN
			}elseif($purpose == 4){
				$button	= "<input type=\"submit\" id=\"return\" value=\"&nbsp;\"".$on_click." />\n";
			# SEND
			}elseif($purpose == 5){
				$button	= "<input type=\"submit\" id=\"send\" value=\"&nbsp;\"".$on_click." />\n";
			# SEARCH
			}elseif($purpose == 6){
				$button	= "<input type=\"submit\" id=\"search\" value=\"&nbsp;\"".$on_click." />\n";
			# LOGIN
			}elseif($purpose == 7){
				$button	= "<input type=\"submit\" id=\"certify\" value=\"&nbsp;\"".$on_click." />\n";
			# CONTRACT
			}elseif($purpose == 8){
				$button	= "<input type=\"submit\" id=\"contract\" value=\"&nbsp;\"".$on_click." />\n";
			}

		# BROWSER IE 6
		}else{

			# ENTRY
			if($purpose == 1){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" 申 請 \"".$on_click." />\n";
			# ACCOUNT
			}elseif($purpose == 2){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" アカウント発行 \"".$on_click." />\n";
			# CHECK
			}elseif($purpose == 3){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" 確 認 \"".$on_click." />\n";
			# RETURN
			}elseif($purpose == 4){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" 戻 る \"".$on_click." />\n";
			# SEND
			}elseif($purpose == 5){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" 送 信 \"".$on_click." />\n";
			# SEARCH
			}elseif($purpose == 6){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" 検 索 \"".$on_click." />\n";
			# LOGIN
			}elseif($purpose == 7){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" LOGIN \"".$on_click." />\n";
			# CONTRACT
			}elseif($purpose == 8){
				$button	= "<input type=\"submit\" class=\"submit_main\" value=\" 契約更新 \"".$on_click." />\n";
			}

		}

		# SUBMIT
		$submit	 = "<div id=\"submit\">\n";
		$submit	.= $button;
		$submit	.= "</div>\n";

		print($submit);

	}



	/**************************************************
	**
	**	getMobileTitle
	**	----------------------------------------------
	**	MOBILE HTMO HELPER
	**
	**************************************************/

	public function getMobileTitle($str,$background,$text,$size){

		$result	 = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\" style=\"".$background."\">\n";
		$result	.= "<tr>\n";
		$result	.= "<td style=\"".$background."\"><div style=\"".$text.$size." text-align: left;\">".$str."</div></td>\n";
		$result	.= "</tr>\n";
		$result	.= "</table>\n";

		print($result);

	}



	/************************************************
	**
	**	getDisplayDate
	**	---------------------------------------------
	**	曜日取得
	**
	************************************************/

	public function getDisplayDate($date,$type){

		global	$days_array;

		$year		= date("Y", strtotime($date));
		$month		= date("m", strtotime($date));
		$day		= date("d", strtotime($date));

		$timestamp	= mktime(0, 0, 0, $month, $day, $year);

		$day_number	= date('w', $timestamp);

		$result		= $days_array[$day_number][$type];

		return	$result;

	}



	/************************************************
	**
	**	replaceYearMonthDay
	**	---------------------------------------------
	**	年月日時分変換
	**
	************************************************/

	public function replaceYearMonthDay($date,$type){

		$year	= date("Y", strtotime($date));
		$month	= date("m", strtotime($date));
		$day	= date("d", strtotime($date));

		$disp_date	= $year."年".$month."月".$day."日 ";

		if($type == 1){
			$hour		 = date("H", strtotime($date));
			$minutes	 = date("i", strtotime($date));
			$disp_date	.= $hour."時".$minutes."分";
		}

		return $disp_date;

	}



	/************************************************
	**
	**	replaceLinkTags
	**	---------------------------------------------
	**	$str内 https:// URLをリンクに変換
	**
	************************************************/

	public function replaceLinkTags($str){

		# URLの正規表現パターン
		$pattern_url	= '/https:\/\/[0-9a-z_,.:;&=+*%$#!?@()~\'\/-]+/i';
		$pattern_url2	= '/https:\/\/[0-9a-z_,.:;&=+*%$#!?@()~\'\/-]+/i';

		# 置き換え文字列
		#$replace_url	= '<a href="$0" target="_blank">$0</a>';
		$replace_url	= '<a href="$0">$0</a>';

		# REPLACE
	    $str			= preg_replace($pattern_url,$replace_url,$str);
	    $str			= preg_replace($pattern_url2,$replace_url,$str);

		# メールアドレスの正規表現パターン
		$pattern_mail	= '/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i';

		# 置き換え文字列
		$replace_mail	= '$1<a href="mailto:$2@$3">$2@$3</a>';

		# REPLACE
	    $str			= preg_replace($pattern_mail,$replace_mail,$str);

		return $str;

		/*********** 参考
	    $str = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" >$3</a>", $str);
	    $str = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"https://$3\" >$3</a>", $str);
	    $str = preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $str);
		***********/

	}



	/************************************************
	**
	**	replaceLinkImages
	**	---------------------------------------------
	**	IMAGEに https:// URLをリンクを付与
	**
	************************************************/

	public function replaceLinkImages($image,$url=NULL,$target=NULL,$check=NULL){

		if(empty($image)){
			return FALSE;
		}

		if(empty($url)){
			return	$image;
		}

		if($target == 1){
			$link_target	= " target=\"_blank\"";
		}

		if(!empty($check) && empty($url)){

			$image	= "<a href=\"#\">".$image."</a>";

		}else{

			$image	= "<a href=\"".$url."\"".$link_target.">".$image."</a>";

		}

		return $image;

	}



	/************************************************
	**
	**	makeNextPreviousLink
	**	---------------------------------------------
	**	NEXT / PREVIEW LINK
	**
	************************************************/

	function makeNextPreviousLink($file_name,$rows,$limit,$set_num=NULL,$hidden_data=NULL,$ancor=NULL,$stop=NULL){

		if(empty($file_name) || !isset($rows) || !isset($limit)){
			return FALSE;
		}

		# 初期化
		$result			= NULL;
		$path_hidden	= NULL;
		$link			= NULL;
		$previous		= NULL;
		$next			= NULL;

		# HIDDEN DATA 生成
		if(!empty($hidden_data)){
			foreach($hidden_data as $key => $value){
				if($key == "set"){ continue; }
				if($key == "token"){ continue; }
				$path_hidden	.= $value."/";
			}
		}

		if(!empty($ancor)){
			$path_hidden	.= "#".$ancor;
		}

		if($rows > $limit){

			$number	= ceil($rows/$limit);

			$set	= 0;
			$loop	= 0;
			for($i=0;$i<$number;$i++){

				$display	 = $i+1;

				if($display != 1){
					#$link	.= "&nbsp;|&nbsp;";
					#$link	.= "&nbsp;";
				}

				if($set == $set_num){
					$link	 	.= "<div class=\"number_box\" id=\"current\">".$display."</div>\n";
				}else{
					if(!empty($stop)){
						$display_check	= $number - $display;
						if($set < $set_num){
							if($display_check >= $stop){
								$set	= $set + $limit;
								continue;
							}
						}
					}
					$link	 	.= "<div class=\"number_box\"><a href=\"".$file_name.$set."/".$path_hidden."\"><span>".$display."</span></a></div>\n";
				}

				$set		 = $set + $limit;

				$loop++;

				if(!empty($stop) && $loop >= $stop){
					break;
				}


			}

			if(!empty($set_num)){
				$previous_set	= $set_num - $limit;
				$previous		= "<div class=\"prev_box\"><a href=\"".$file_name.$previous_set."/".$path_hidden."\">前へ</a></div>\n";
			}

			$check	= $rows - $limit;
			if($check > $set_num){
				$next_set	= $set_num + $limit;
				$next		= "<div class=\"next_box\"><a href=\"".$file_name.$next_set."/".$path_hidden."\">次へ</a></div>\n";
			}

			$result	= $previous.$link.$next;


		}else{
			return FALSE;
		}

		return $result;

	}



	/************************************************
	**
	**	makeAnchorTag
	**	---------------------------------------------
	**	ANCHOR TAG
	**
	************************************************/

	function makeAnchorTag($data,$name=NULL){

		if(empty($data)){
			return FALSE;
		}

		$data	=	preg_replace('/&gt;&gt;([0-9]+)/','<a href="#'.$name.'\\1">\\0</a>',$data);

		return $data;

	}



	/************************************************
	**
	**	replacePrice
	**	---------------------------------------------
	**	$price 小数点を挿入
	**
	************************************************/

	function replacePrice($price){

		$lengh	= strlen($price);

		if($lengh == 4){
			$check		= substr($price,0,1);
			$replace	= $check.",";
			$price		= substr_replace($price,$replace,0,1);
		}elseif($lengh == 5){
			$check		= substr($price,0,2);
			$replace	= $check.",";
			$price		= substr_replace($price,$replace,0,2);
		}elseif($lengh == 6){
			$check		= substr($price,0,3);
			$replace	= $check.",";
			$price		= substr_replace($price,$replace,0,3);
		}

		return $price;

	}


}

?>