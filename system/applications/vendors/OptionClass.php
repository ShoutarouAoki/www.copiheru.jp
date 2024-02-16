<?php
/********************************************************************************
**	
**	OptionClass.php
**	=============================================================================
**
**	■PAGE / 
**	OPTION MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	OPTION CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	OPTION機能呼び出し
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
class OptionClass{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR

	# CONSTRUCT
	function __construct(){

    }

	# DESTRUCT
	function __destruct(){
		
    }



	/************************************************
	**
	**	getFileData
	**	---------------------------------------------
	**	指定ファイル化情報取得
	**
	************************************************/

	public function getFileData($file_dir,$file_name){

		$fp = fopen(DOCUMENT_ROOT_LOCAL."/".$file_dir."/".$file_name,"r");

		while (!feof($fp)) {
			$line = fgets($fp);
			$text = array($line);
			foreach($text as $write){
				if($write == ""){
					continue;
				}
				$file_data .= $write;
			}
		}

		fclose($fp);

		return $file_data;

	}



	/************************************************
	**
	**	getDirectoryData
	**	---------------------------------------------
	**	指定ディレクトリ内情報取得
	**
	************************************************/

	public function getDirectoryData($file_dir){

		# FULL PATH
		$file_directory_path = $file_dir;

		# CHECK DIRECTORY
		if($file_directory = opendir($file_directory_path)){

			$i=0;
		    while(FALSE !== ($file_name = readdir($file_directory))){

				$file_path	= $file_directory_path."/".$file_name;
				$path_info	= pathinfo($file_path);

				# DIRECTORY階層ならCONTINUE
				if(is_dir($file_path) && ($file_name != ".." && $file_name != ".")){

					continue;

				# FILE DATA
				}elseif($file_name != ".." && $file_name != "."){

					# FILE NAME
		        	$file_list['name'][$i]	= $file_name;

					# FILE SIZE
					$file_list['size'][$i]	= filesize($file_path);

					# UPLOAD DATE
					$file_list['date'][$i]	= date("Y/m/d",filemtime($file_path));

					$i++;

				}

		    }

		    closedir($file_directory);

		}

		return $file_list;

	}



	/************************************************
	**
	**	deleteFileData
	**	---------------------------------------------
	**	指定ディレクトリ内個別情報削除
	**
	************************************************/

	public function deleteFileData($file_name,$file_dir){

		$file_path		= $file_dir."/".$file_name;
		$thumb_path		= $file_dir."/thumb/".$file_name;

		if(file_exists($file_path)){

			if(is_dir($file_path)){
				return FALSE;
			}

			if(!is_writable($file_path)){
				return FALSE;
			}

			if(is_link($file_path)){
				fileDelete(realpath($file_path));
			}

			if(!@unlink($file_path)){
				return FALSE;
			}

			# THUMB
			if(file_exists($thumb_path)){

				if(is_dir($thumb_path)){
					return FALSE;
				}

				if(!is_writable($thumb_path)){
					return FALSE;
				}

				if(is_link($thumb_path)){
					fileDelete(realpath($thumb_path));
				}

				if(!@unlink($thumb_path)){
					return FALSE;
				}

			}


		}else{

			return FALSE;

		}

		return TRUE;

	}



	/************************************************
	**
	**	deleteFileMulti
	**	---------------------------------------------
	**	指定ディレクトリ内情報一括削除
	**
	************************************************/

	public function deleteFileMulti($file_data,$file_dir){

		$count	= count($file_data);

		for($i=0;$i<$count;$i++){

			$object_name	= $file_data[$i];

			# GET DIRECOTRY DATA
			$file_list	= $this->getDirectoryData($file_dir);

			$file_count	= count($file_list['name']);

			for($j=0;$j<$file_count;$j++){

				$file_name	= $file_list['name'][$j];

				if($object_name == $file_name){

					# DELETE DIRECOTRY DATA
					$result	= $this->deleteFileData($object_name,$file_dir);

					# OUT PUT ERROR
					if(!$result){
						return FALSE;
					}else{
						break;
					}

				}

			}

		}

		return TRUE;

	}



	/************************************************
	**
	**	deleteCache
	**	---------------------------------------------
	**	キャッシュ一括削除
	**
	************************************************/

	public function deleteCache($directory=NULL){

		# PATH
		$cache_directory	= DOCUMENT_ROOT_CACHE.$directory;

		# GET DIRECOTRY DATA
		$file_list	= $this->getDirectoryData($cache_directory);

		# FALSE
		if(empty($file_list)){
			return FALSE;
		}

		$file_count	= count($file_list['name']);

		for($i=0;$i<$file_count;$i++){

			$file_name	= $file_list['name'][$i];

			# DELETE DIRECOTRY DATA
			$result	= $this->deleteFileData($file_name,$cache_directory);

			# OUT PUT ERROR
			if(empty($result)){
				continue;
			}

		}

		return TRUE;

	}



	/************************************************
	**
	**	getTwitterRss
	**	---------------------------------------------
	**	RSS 読み込み
	**
	************************************************/

	public function getTwitterRss($id,$count=NULL,$check=NULL){

		if(empty($id)){
			return FALSE;
		}

		if(empty($count)){
			$count	= 5;
		}

		# TWITTER URL
		$url				= "https://twitter.com/statuses/user_timeline.xml?id=".$id."&count=10";

		# RSS CONNECT
		ini_set('user_agent', 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)'); 
		$context	= stream_context_create(
			array(
				'http' => array(
				'method' => 'GET',
				'header' => 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
				)
			)
		);

		$rss				= @simplexml_load_string(file_get_contents($url,false,$context));

		# ERROR
		if(empty($rss)){
			return FALSE;
		}

		# USER IMAGE
		$result['image']	= "<img src=\"" . $rss->status->user->profile_image_url . "\">";

		# USER PROFILE
		$result['profile']	= $rss->status->user->description;

		# TEXT VALUE
		$line=0;
		foreach ($rss->status as $i) {

			$value	= $i->text;
			$time	= $i->created_at;
			$uri	= "https://twitter.com/".$id."/statuses/".$i->id;

			if(empty($check)){
				if(preg_match("/^@/",$value)){ continue; }
			}

			#$time	 = mb_convert_encoding($i->created_at,"SJIS","UTF-8");
			$time	 = $i->created_at;
			$time	 = $this->showSinceTime($time,"english");

			$value	 = $this->replaceLinkUrl($value);
			#$value	 = mb_convert_encoding($value,"SJIS","UTF-8");

			$values	.= "<li><span>".$value."</span>&nbsp;&nbsp;&nbsp;&nbsp;";
			$values	.= "<a href=\"".$uri."\" target=\"_blank\">".$time."</a></li>\n";

			$line++;

			if($line >= $count){ break; }

		}

		$result['text']		= $values;

		return $result;

	}



	/************************************************
	**
	**	showSinceTime
	**	---------------------------------------------
	**	TIME ZONE
	**
	************************************************/

	private function showSinceTime($tweetTime,$language=NULL) {

		$tweetTime = strtotime($tweetTime);

		$nowTime = strtotime('now');

		$sinceTime = abs($tweetTime - $nowTime);

		$day = floor($sinceTime/(24*60*60));

		$hour = floor($sinceTime/(60*60));

		$min = floor($sinceTime/60) - ($hour*60);

		$sec = $sinceTime % 60;

		if(!empty($language)){

			if($day > 0) return $day." days ago";

			if($hour > 0) return "about ".$hour." hours ago";

			if($min > 0) return $min." minuts ago";

			if($sec > 0) return $sec." second ago";

		}else{

			if($day > 0) return $day."日前";

			if($hour > 0) return "約".$hour."時間前";

			if($min > 0) return $min."分前";

			if($sec > 0) return $sec."秒前";

		}

	}



	/**************************************************
	**
	**	rewriteData
	**	----------------------------------------------
	**	データ上書き 変数生成
	**
	**************************************************/

	public function rewriteData($data,$post){

		if(empty($data) || empty($post)){
			return FALSE;
		}

		# GET TABLE COLUMN
		foreach($data as $column => $value){

			$result[$column]	= $value;

			# $_REQUEST値を配列で格納
			foreach($post as $name => $content){

				if($column == $name){
					$result[$name]	= NULL;
					$result[$name]	= $content;
					break;
				}

			}

		}

		return $result;

	}



	/************************************************
	**
	**	replaceLinkUrl
	**	---------------------------------------------
	**	記事用
	**
	************************************************/

	public function replaceLinkUrl($str){

		# URLの正規表現パターン
		$pattern = '/https:\/\/[0-9a-z_,.:;&=+*%$#!?@()~\'\/-]+/i';

		# 置き換え文字列
		$replace = '<a href="$0" target="_blank">$0</a>';

	    $str = preg_replace($pattern,$replace,$str);

		return $str;

	}



	/************************************************
	**
	**	replaceLinkUrlByApp
	**	---------------------------------------------
	**	アプリ外リンク用
	**
	************************************************/

	public function replaceLinkUrlByApp($str,$url){

		# URLの正規表現パターン
		$pattern = '/https:\/\/[0-9a-z_,.:;&=+*%$#!?@()~\'\/-]+/i';

		# 置き換え文字列
		$replace = '<a onclick=\'sp.call("'.$url.'","")\'>$0</a>';

	    $str = preg_replace($pattern,$replace,$str);

		return $str;

	}



	/************************************************
	**
	**	hiddenEncode / hiddenDecode
	**	---------------------------------------------
	**	配列をurlエンコード
	**	hiddenで配列を渡したいときに使用
	**
	************************************************/

	public function hiddenEncode($string_array){
	  return rawurlencode(serialize($string_array));
	}

	public function hiddenDecode($string){
	  return unserialize(rawurldecode($string));
	}



	/************************************************
	**
	**	writeLogFile
	**	---------------------------------------------
	**	ログ書き込み処理
	**
	************************************************/
	public function writeLogFile($file_name,$data){

		$file	= DOCUMENT_ROOT_LOGS."/".$file_name;
		$fp = fopen($file,"a");
		if ($fp == FALSE) { return FALSE; }
		fwrite($fp, $data);
		fclose($fp);

		return TRUE;

	}



	/************************************************
	**
	**	writeXml
	**	---------------------------------------------
	**	XML書き出し
	**	
	**
	************************************************/

	public function writeXml($data,$filepath){

		if(empty($data) || empty($filepath)){
			return FALSE;
		}

		# COUNT
		$count		= count($data);

		$rootNode	= new SimpleXMLElement( "<?xml version='1.0' encoding='UTF-8' standalone='yes'?><resources></resources>" );

		for($i=0;$i<$count;$i++){
			$itemNode	= $rootNode->addChild('item');
			foreach($data[$i] as $key => $value){
				$itemNode->addChild( $key, $value );
			}

		}

		$dom	= new DOMDocument( '1.0' );
		$dom->loadXML( $rootNode->asXML() );
		$dom->formatOutput	= TRUE;
		$dom->save($filepath);

		return TRUE;

	}



	/************************************************
	**
	**	getGoogleMap
	**	---------------------------------------------
	**	Google MAP API
	**
	************************************************/

	public function getGoogleMap($address){

		$address_url	= rawurlencode($address); // $mapの内容は、岩手県盛岡市大通2-6-12

		# Geocoding APIを利用し、住所から緯度情報を取得する
		$api_url		 = "https://www.geocoding.jp/api/";
		$api_url		.= "?v=1.1&q=".$address_url;

		$fp	= fopen($api_url, "r");
		while(!feof($fp)) {
			$line.= fgets($fp);
		}
		fclose($fp);

		$xml	 = simplexml_load_string($line);
		$lat	 = $xml->coordinate->lat;
		$lng	 = $xml->coordinate->lng;

		$result	 = "<img src=\"https://maps.google.com/staticmap";
		$result	.= "?markers=".$lat.",".$lng.",red";
		$result	.= "&zoom=".GOOGLE_MAP_ZOOM;
		$result	.= "&size=".GOOGLE_MAP_WIDTH."x".GOOGLE_MAP_HEIGHT;
		$result	.= "&sensor=false&format=gif";
		$result	.= "&key=".GOOGLE_MAP_KEY;
		$result	.= "\" />\n";

		print($result);

	}



	/**************************************************
	**
	**	getSelectNumber
	**	----------------------------------------------
	**	注文個数タグ作成
	**
	**************************************************/

	public function getSelectNumber($count,$check=NULL){

		$result	 = "<select name=\"number\">\n";

		for($i=1;$i<=$count;$i++){
			if($check == $i){
				$result	.= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>\n";
			}else{
				$result	.= "<option value=\"".$i."\">".$i."</option>\n";
			}
		}

		$result	.= "</select>\n";

		return $result;

	}



	/**************************************************
	**
	**	removeTagProtocols
	**	----------------------------------------------
	**	TAG -> PROTOCOLS
	**	----------------------------------------------
	**	文字列から余分なタグ属性を取り除く
	**
	**************************************************/

	public function removeTagProtocols($contents,$tags=NULL,$image_url=NULL){

		if(empty($contents)){
			return FALSE;
		}

		# 画像除外処理
		if(!empty($image_url)){

			$imagelist	= array();
			if(preg_match_all("/<img(.+?)>/", $contents, $images) !== FALSE){   
				foreach ($images[0] as $value){
					$imagelist[]	= $value;
				}
			}


			if(!empty($imagelist)){

				# 除外画像URLリスト
				global	$remove_image_url;

				$urllist		= array();
				if(preg_match_all('/https?:\/\/[a-zA-Z0-9\-\.\/\?\@&=:~#]+/', $image_url, $matchs) !== FALSE){   
					foreach ($matchs[0] as $line){
						$urllist[]	= $line;
					}
				}

				# REMOVE URL
				$remove_url		= $remove_image_url;

				# 初期化
				$remove_image	= NULL;

				# 除外する画像を抽出
				foreach($imagelist as $key => $data){
					foreach($remove_url as $k => $d){
						if(preg_match("{".$d."}",$data)){
							$remove_image[]	= $data;
						}
					}
				}

				# 除外画像削除
				if(!empty($remove_image)){
					foreach($remove_image as $key => $remove){
						$contents	= preg_replace('{'.$remove.'}', '', $contents);
					}
				}

			}


			# TAG REMOVE
			if(!empty($tags)){

				foreach($tags as $key => $tag){
					$pattern	= "/".$tag.":.*?;/i";
					$contents	= preg_replace($pattern,"",$contents);
					$pattern	= "/".$tag.":.*?/i";
					$contents	= preg_replace($pattern,"",$contents);
					$pattern	= "/".$tag."=\".*?\"|".$tag."='.*?'/i";
					$contents	= preg_replace($pattern,"",$contents);
				}


			}else{

				# ALL
				$pattern	= "/style=\".*?\"|style='.*?'/i";
				$contents	= preg_replace($pattern,"",$contents);
				$pattern	= "/width=\".*?\"|width='.*?'/i";
				$contents	= preg_replace($pattern,"",$contents);
				$pattern	= "/height=\".*?\"|height='.*?'/i";
				$contents	= preg_replace($pattern,"",$contents);

			}

		}else{

			# TAG REMOVE
			if(!empty($tags)){

				foreach($tags as $key => $tag){
					$pattern	= "/".$tag.":.*?;/i";
					$contents	= preg_replace($pattern,"",$contents);
					$pattern	= "/".$tag.":.*?/i";
					$contents	= preg_replace($pattern,"",$contents);
					$pattern	= "/".$tag."=\".*?\"|".$tag."='.*?'/i";
					$contents	= preg_replace($pattern,"",$contents);
				}


			}else{

				# ALL
				$pattern	= "/style=\".*?\"|style='.*?'/i";
				$contents	= preg_replace($pattern,"",$contents);
				$pattern	= "/width=\".*?\"|width='.*?'/i";
				$contents	= preg_replace($pattern,"",$contents);
				$pattern	= "/height=\".*?\"|height='.*?'/i";
				$contents	= preg_replace($pattern,"",$contents);

			}

		}

		return $contents;

	}



	/**************************************************
	**
	**	removeStrings
	**	----------------------------------------------
	**	必要ない文字列を削除
	**
	**************************************************/

	public function removeStrings($data,$array){

		if(empty($data) || empty($array)){
			return FALSE;
		}

		foreach($array as $key => $value){
			if(preg_match("{".$value."}",$data)){
				$data	= str_replace($value,"",$data);
			}
		}

		# オマケ
		if(preg_match("/clear: both;/",$data)){
			$data	= str_replace("clear: both;","clear: both; display: none;",$data);
		}elseif(preg_match("/clear:both;/",$data)){
			$data	= str_replace("clear:both;","clear: both; display: none;",$data);
		}

		return $data;

	}



	/**************************************************
	**
	**	weightedRandom
	**	----------------------------------------------
	**	ランダムに
	**
	**************************************************/

	public function weightedRandom($weights){
		list($lookup, $total_weight) = $this->calcLookups($weights);
		$r	= mt_rand(0, $total_weight*100)/100;
		return $this->binarySearch($r, $lookup);
	}



	/**************************************************
	**
	**	calcLookups
	**	----------------------------------------------
	**	$lookup：$weights を加算していった配列
	**	たとえば$weights={5,2,8,10}だったら、$lookup={5,7,15,25}となる
	**	こうすることで、weghts 配列をソートすることなく、昇順の配列が得られる
	**
	**************************************************/

	private function calcLookups($weights){

		$lookup			= array();
		$total_weight	= 0;
		$pos			= 0;

		foreach($weights as $val){
			$total_weight += $val;
			$lookup[$pos++] = $total_weight;
		}

		return array($lookup, $total_weight-1);

	}



	/**************************************************
	**
	**	binarySearch
	**	----------------------------------------------
	**	二分探索法
	**	なので、オーダーがO(n)からO(log2(n))になる
	**
	**************************************************/

	private function binarySearch($needle, $haystack){

		# ちょっとNOTICE消すよ
		error_reporting(E_ERROR | E_WARNING | E_PARSE);

		$right		= count($haystack) - 1;
		$left		= 0;

		// 左を示すポインタ($left)が右を示すポインタ($high)と同じ値か、
		// 大きい値となったとき、配列に対する$needleの相対位置が特定される
		while ( $left  < $right ){
			// (int)をつけることで$midを整数値にする
			$mid = (int)(($right + $left ) / 2);

			if ($needle >= $haystack[$mid]){
				// 右半分へ
				$left  = $mid + 1;

			} else if ($needle >= $haystack[$mid-1]) {
				// ぴったり
				return $mid;
			} else {
				// 左半分へ
				$right = $mid - 1;
			}
			// $haystackの中に$needleがない場合は、
			// この時点で$left > $rightになるのでループから抜ける
		}

		return $left;

	}



	/**************************************************
	**
	**	getMixiButton
	**	----------------------------------------------
	**	mixi イイネボタン
	**
	**************************************************/

	public function getMixiButton($url,$device=NULL,$title=NULL){

		if(empty($url)){
			return FALSE;
		}

		#$check_url	= urlencode($url);
		$check_url	= $url;

		if($device == "mobile"){

			#$title	= mb_convert_encoding($title,'SJIS','UTF-8');

			print("<form action=\"https://m.mixi.jp/create_favorite.pl?guid=ON\" method=\"POST\">\n");
			print("<input type=\"hidden\" name=\"service_key\" value=\"".MIXI_KEY."\" />\n");
			print("<input type=\"hidden\" name=\"title\" value=\"".$title."\" />\n");
			print("<input type=\"hidden\" name=\"primary_url\" value=\"".$url."\" />\n");
			print("<input type=\"hidden\" name=\"mobile_url\" value=\"".$url."\" />\n");
			print("<input type=\"submit\" value=\"mixiイイネ！\" />\n");
			print("</form>\n");

		}elseif($device == "smart"){
			#print("<iframe scrolling=\"no\" frameborder=\"0\" width=\"58\" height=\"20\" allowTransparency=\"false\" ");
			#print("style=\"border:none; width:58px; height:20px; overflow:hidden;\"");
			#print("src=\"https://plugins.mixi.jp/favorite.pl?href=".$check_url);
			#print("&service_key=".MIXI_KEY."&show_faces=false&width=58&height=20\"></iframe>");
			print("<div data-plugins-type=\"mixi-favorite\" data-service-key=\"".MIXI_KEY."\" data-href=\"".$check_url."\" ");
			print("data-show-faces=\"false\" data-show-count=\"true\" data-show-comment=\"false\" data-width=\"85\" class=\"wph mixi_iine\"></div>");
			print("<script type=\"text/javascript\">(function(d) {var s = d.createElement('script'); s.type = 'text/javascript'; ");
			print("s.async = true;s.src = 'https://static.mixi.jp/js/plugins.js#lang=ja';d.getElementsByTagName('head')[0].appendChild(s);})(document);</script>");
		}else{
			#print("<iframe scrolling=\"no\" frameborder=\"0\" width=\"58\" height=\"20\" allowTransparency=\"true\" ");
			#print("style=\"border:none; width:58px; height:20px;\"");
			#print("src=\"https://plugins.mixi.jp/favorite.pl?href=".$check_url);
			#print("&service_key=".MIXI_KEY."&data-show-count=true&show_faces=false&width=58\"></iframe>");
			print("<div data-plugins-type=\"mixi-favorite\" data-service-key=\"".MIXI_KEY."\" data-href=\"".$check_url."\" ");
			print("data-show-faces=\"false\" data-show-count=\"true\" data-show-comment=\"false\" data-width=\"85\" class=\"wph mixi_iine\"></div>");
			print("<script type=\"text/javascript\">(function(d) {var s = d.createElement('script'); s.type = 'text/javascript'; ");
			print("s.async = true;s.src = 'https://static.mixi.jp/js/plugins.js#lang=ja';d.getElementsByTagName('head')[0].appendChild(s);})(document);</script>");
		}

	}



	/**************************************************
	**
	**	getMixiCheckButton
	**	----------------------------------------------
	**	mixi チェックボタン
	**
	**************************************************/

	public function getMixiCheckButton($url,$device=NULL,$title=NULL){

		if(empty($url)){
			return FALSE;
		}

		#$check_url	= urlencode($url);
		$check_url	= $url;

		if($device == "mobile"){
			print("<a href=\"https://mixi.jp/share.pl\" class=\"mixi-check-button\" data-key=\"".MIXI_KEY."\" data-url=\"".$check_url."\" data-button=\"button-2\">mixiチェック</a>");
			print("<script type=\"text/javascript\" src=\"https://static.mixi.jp/js/share.js\"></script>");
		}elseif($device == "smart"){
			print("<a href=\"https://mixi.jp/share.pl\" class=\"mixi-check-button wph mixi_check\" data-key=\"".MIXI_KEY."\" data-url=\"".$check_url."\" data-button=\"button-2\">mixiチェック</a>");
			print("<script type=\"text/javascript\" src=\"https://static.mixi.jp/js/share.js\"></script>");
		}else{
			print("<a href=\"https://mixi.jp/share.pl\" class=\"mixi-check-button wph mixi_check\" data-key=\"".MIXI_KEY."\" data-url=\"".$check_url."\" data-button=\"button-2\">mixiチェック</a>");
			print("<script type=\"text/javascript\" src=\"https://static.mixi.jp/js/share.js\"></script>");
		}


	}



	/**************************************************
	**
	**	getTwiteButton
	**	----------------------------------------------
	**	twitter ツイートボタン
	**
	**************************************************/

	public function getTwiteButton($url,$title,$account=NULL,$device=NULL){

		if(empty($url)){
			return FALSE;
		}

		if($device == "mobile"){
			print("<a href=\"https://twitter.com/share\" class=\"twitter-share-button\" data-url=\"".$url."\" data-text=\"".$title."\" data-count=\"horizontal\" data-lang=\"ja\">");
			print("<img src=\"".HTTP_IMAGES."/icon_tweet.gif\" border=\"0\" /></a>");
			print("<script type=\"text/javascript\" src=\"https://platform.twitter.com/widgets.js\"></script>");
		}else{
			print("<a href=\"https://twitter.com/share\" class=\"twitter-share-button wph tweet\" data-url=\"".$url."\" data-text=\"".$title."\" data-count=\"horizontal\" data-lang=\"ja\">Tweet</a>");
			print("<script type=\"text/javascript\" src=\"https://platform.twitter.com/widgets.js\"></script>");
		}

	}



	/**************************************************
	**
	**	getFaceBookButton
	**	----------------------------------------------
	**	facebook イイネボタン
	**
	**************************************************/

	public function getFaceBookButton($url,$device=NULL){

		if(empty($url)){
			return FALSE;
		}

		$url	= urlencode($url);

		if($device == "mobile"){


		}else{

			print("<iframe src=\"https://www.facebook.com/plugins/like.php?href=".$url."&amp;layout=button_count&amp;show_faces=false");
			print("&amp;width=75&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21\" scrolling=\"no\" frameborder=\"0\" ");
			print("style=\"border:none; width:75px; height:21px;\" allowTransparency=\"true\" class=\"wph facebook\"></iframe>");

		}

	}



	/**************************************************
	**
	**	getFaceBookShareButton
	**	----------------------------------------------
	**	facebook シェアボタン
	**
	**************************************************/

	public function getFaceBookShareButton($url,$device=NULL){

		if(empty($url)){
			return FALSE;
		}

		#$url	= urlencode($url);

		if($device == "mobile"){


		}else{

			#print("<a name=\"fb_share\" share_url=\"".$url."\" type=\"button_count\"></a>");
			#print("<script src=\"https://static.ak.fbcdn.net/connect.php/js/FB.Share\" type=\"text/javascript\"></script>");
			print("<a href=\"https://www.facebook.com/sharer.php\" class=\"wph fcbk_share\" target=\"_blank\" title=\"シェア\" ");
			print("expr:share_url=\"data:post.url\" name=\"fb_share\" type=\"button_count\" share_url=\"".$url."\">シェア</a>");
			print("<script src=\"https://static.ak.fbcdn.net/connect.php/js/FB.Share\" type=\"text/javascript\"></script>");

		}

	}



	/************************************************
	**
	**	checkHttpUrl
	**	---------------------------------------------
	**	URLをチェック
	**
	************************************************/

	public function getFacebookScript($appid=NULL){

		if(!empty($appid)){
			$url	= "&appId=".$appid;
		}else{
			$url	= NULL;
		}

		print("<div id=\"fb-root\"></div>\n");
		print("<script>\n");
		print("(function(d, s, id) {\n");
		print("var js, fjs = d.getElementsByTagName(s)[0];\n");
		print("if (d.getElementById(id)) return;\n");
		print("js = d.createElement(s); js.id = id;\n");
		print("js.src = \"//connect.facebook.net/ja_JP/all.js#xfbml=1".$url."\";\n");
		print("fjs.parentNode.insertBefore(js, fjs);\n");
		print("}(document, 'script', 'facebook-jssdk'));\n");
		print("</script>\n");

	}



	/**************************************************
	**
	**	getMovieContents
	**	----------------------------------------------
	**	MOVIE CONTENTS 生成
	**
	**************************************************/

	public function getMovieContents($url){

		if(empty($url)){
			return FALSE;
		}

		if(preg_match("/feature=player_embedded&/",$url)){
			$url	= str_replace("feature=player_embedded&","",$url);
		}elseif(preg_match("/feature=youtu.be&/",$url)){
			$url	= str_replace("feature=youtu.be&","",$url);
		}

		$result		= NULL;

		# YOUTUBE 1
		if(preg_match("/^https:\/\/www.youtube.com\//",$url)){

			# URL REPLACE
			$contents	= str_replace("https://www.youtube.com/watch?v=","",$url);
			$check		= strstr($contents,"&");
			$contents 	= str_replace($check,"",$contents);
			$display	= "https://www.youtube.com/embed/".$contents."?rel=0&wmode=transparent";
			$object		= "https://www.youtube.com/v/".$contents."?fs=1&amp;hl=ja_JP";

			$result['movie_id']	= $contents;
			$result['display']	= $display;
			$result['object']	= $object;
			$result['type']		= 1;

		# YOUTUBE 2
		}elseif(preg_match("/^https:\/\/www.youtube.com\//",$url)){

			# URL REPLACE
			$contents	= str_replace("https://www.youtube.com/watch?v=","",$url);
			$check		= strstr($contents,"&");
			$contents 	= str_replace($check,"",$contents);
			$display	= "https://www.youtube.com/embed/".$contents."?rel=0&wmode=transparent";
			$object		= "https://www.youtube.com/v/".$contents."?fs=1&amp;hl=ja_JP";

			$result['movie_id']	= $contents;
			$result['display']	= $display;
			$result['object']	= $object;
			$result['type']		= 1;

		# YOUTUBE 3
		}elseif(preg_match("/^https:\/\/youtu.be\//",$url)){

			# URL REPLACE
			$contents	= str_replace("https://youtu.be/","",$url);
			$check		= strstr($contents,"&");
			$contents 	= str_replace($check,"",$contents);
			$check		= strstr($contents,"?");
			$contents 	= str_replace($check,"",$contents);
			$display	= "https://www.youtube.com/embed/".$contents."?rel=0&wmode=transparent";
			$object		= "https://www.youtube.com/v/".$contents."?fs=1&amp;hl=ja_JP";

			$result['movie_id']	= $contents;
			$result['display']	= $display;
			$result['object']	= $object;
			$result['type']		= 1;

		# YOUTUBE 4
		}elseif(preg_match("/^https:\/\/youtu.be\//",$url)){

			# URL REPLACE
			$contents	= str_replace("https://youtu.be/","",$url);
			$check		= strstr($contents,"&");
			$contents 	= str_replace($check,"",$contents);
			$check		= strstr($contents,"?");
			$contents 	= str_replace($check,"",$contents);
			$display	= "https://www.youtube.com/embed/".$contents."?rel=0&wmode=transparent";
			$object		= "https://www.youtube.com/v/".$contents."?fs=1&amp;hl=ja_JP";

			$result['movie_id']	= $contents;
			$result['display']	= $display;
			$result['object']	= $object;
			$result['type']		= 1;

		}

		return	$result;

	}



	/*********************************************
	**
	**	getMovieThumbnailById
	**	-----------------------------------------
	**	MOVIE API XML-> GET THUMBNAIL
	**
	*********************************************/

	public function getMovieThumbnailById($movie_id,$type){

		if(empty($movie_id) || empty($type)){
			return FALSE;
		}

		# YOUTUBE
		if($type == 1){

			$image	= "https://i.ytimg.com/vi/".$movie_id."/0.jpg";
			return $image;
			//$xml		= @simplexml_load_file("https://gdata.youtube.com/feeds/api/videos/" . $movie_id);
			//return ($xml !== false) ? (string) $xml->children('https://search.yahoo.com/mrss/')->group->thumbnail[0]->attributes()->url : null;

		}

	}



	/*********************************************
	**
	**	makeMovieIframe
	**	-----------------------------------------
	**	MOVIEタグ生成
	**
	*********************************************/

	public function makeMovieDisplay($url,$width,$height,$type,$return=NULL,$object=NULL){

		if(empty($url) || empty($width)){
			return FALSE;
		}

		if($type == 1){

			# IFRAME
			if(empty($object)){
				$result	 = "<iframe title=\"YouTube video player\" class=\"youtube-player\" type=\"text/html\" ";
				$result	.= "width=\"".$width."\" height=\"".$height."\" src=\"".$url."\" frameborder=\"0\">";
				$result	.= "</iframe>";
			# FLASH
			}else{
				$result	 = "<object width=\"".$width."\" height=\"".$height."\">";
				$result	.= "<param name=\"movie\" value=\"".$object."\"></param>";
				$result	.= "<param name=\"allowFullScreen\" value=\"true\"></param>";
				$result	.= "<param name=\"allowscriptaccess\" value=\"always\"></param>";
				$result	.= "<embed src=\"".$object."\" ";
				$result	.= "type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" ";
				$result	.= "allowfullscreen=\"true\" width=\"".$width."\" height=\"".$height."\"></embed>";
				$result	.= "</object>";
			}

		}

		if(!empty($return)){
			return $result;
		}else{
			print($result);
		}

	}



	/************************************************
	**
	**	checkHttpUrl
	**	---------------------------------------------
	**	URLをチェック
	**
	************************************************/

	public function checkHttpUrl($url){

		if(preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/",$url)){
			return TRUE;
		}else{
			return FALSE;
		}

	}



	/**************************************************
	**
	**	stripTag
	**	----------------------------------------------
	**	指定タグの除去 : 正規表現
	**
	**************************************************/

	public function stripTag($str,$tag){

		$pattern1	= sprintf("!<%s.*?>!ims",$tag,$tag);
		$pattern2	= sprintf("!</%s>!ims",$tag,$tag);
		$replace	= "";
	 	$str		= preg_replace($pattern1,$replace,$str);
	 	$str		= preg_replace($pattern2,$replace,$str);

	 	return $str;

	}



	/************************************************
	**
	**	getTodayDisplay
	**	---------------------------------------------
	**	本日のデータ
	**
	************************************************/

	public function getTodayDisplay(){

		global	$days_array;

		$year				= date("Y");
		$month				= date("m");
		$day				= date("d");

		if($month < 10){
			$month			= substr($month,-1);
		}

		if($day < 10){
			$day			= substr($day,-1);
		}

		$result				= NULL;
		$result				= $month."月".$day."日";

		# CALENDAR TIME
	    $time				= mktime(0, 0, 0, $month, $day, $year);
	    $days				= date("w", $time);

		# CHECK DATE / 曜日
		$check_date			= date("d", $time);
	    $disp_days			= $days_array[$days][2];

		# FUNCTION ktHolidayName
		$holiday 			= $this->ktHolidayName($time);

		# FONT COLOR
		if($disp_days == "土"){
			$disp_date		= "<span style=\"color:#0099FF;\">(".$disp_days.")</span>";
		}elseif($disp_days == "日"){
			$disp_date		= "<span style=\"color:#FF0000;\">(".$disp_days.")</span>";
		}elseif($holiday != ""){
			$disp_date		= "<span style=\"color:#FF0000;\">(".$disp_days.")</span>";
		}else{
			$disp_date		= "(".$disp_days.")";
		}

		$result				.= $disp_date;

		return $result;

	}



	/**************************************************
	**
	**	ktHolidayName
	**	----------------------------------------------
	**	休日抽出
	**
	**************************************************/

	public function ktHolidayName($MyDate){

		if(!defined("MONDAY")){ define("MONDAY","2"); }

		# 振替休日施行
		$cstImplementHoliday	= mktime(0,0,0,4,12,1973);
		$HolidayName			= $this->prvHolidayChk($MyDate);

		$result					= NULL;

		if($HolidayName == ""){

			if($this->Weekday($MyDate) == MONDAY){

				# 月曜以外は振替休日判定不要 / 5/6(火,水)の判定はprvHolidayChkで処理済 / 5/6(月)はここで判定する
				if($MyDate >= $cstImplementHoliday){

					$YesterDay		= mktime(0,0,0,$this->Month($MyDate),
					($this->Day($MyDate) - 1),$this->Year($MyDate));
					$HolidayName	= $this->prvHolidayChk($YesterDay);

					if($HolidayName != ""){
						$result = "振替休日";
					}

				}

			}

		}else{

			$result = $HolidayName;

		}

		return $result;

	}



	/**************************************************
	**
	**	prvHolidayChk
	**	----------------------------------------------
	**	休日チェック
	**
	**************************************************/

	public function prvHolidayChk($MyDate){

		if(!defined("MONDAY")){ define("MONDAY","2"); }
		if(!defined("TUESDAY")){ define("TUESDAY","3"); }
		if(!defined("WEDNESDAY")){ define("WEDNESDAY","4"); }

		$cstImplementTheLawOfHoliday	= mktime(0,0,0,7,20,1948);	# 祝日法施行
		$cstShowaTaiso		= mktime(0,0,0,2,24,1989);				# 昭和天皇大喪の礼
		$cstAkihitoKekkon	= mktime(0,0,0,4,10,1959);				# 明仁親王の結婚の儀
		$cstNorihitoKekkon	= mktime(0,0,0,6,9,1993);				# 徳仁親王の結婚の儀
		$cstSokuireiseiden	= mktime(0,0,0,11,12,1990);				# 即位礼正殿の儀

		$MyYear		= $this->Year($MyDate);
		$MyMonth	= $this->Month($MyDate);
		$MyDay		= $this->Day($MyDate);

		# 祝日法施行以前
		if($MyDate < $cstImplementTheLawOfHoliday)
			return NULL;
		else;

		# 結果初期化
		$result = NULL;

		switch($MyMonth){

			# 1月
			case 1:

				if($MyDay == 1){
					$result = "元日";
				}else{
					if($MyYear >= 2000){
						$strNumberOfWeek = (floor(($MyDay - 1) / 7) + 1) . $this->Weekday($MyDate);
						# Monday:2
						if($strNumberOfWeek == "22"){
		                  $result = "成人の日";
						}else;
					}else{
						if($MyDay == 15){
							$result = "成人の日";
						}else;
					}
				}

				break;

			# 2月
			case 2:

				if($MyDay == 11){
					if($MyYear >= 1967){
						$result = "建国記念の日";
					}else;
				}elseif($MyDate == $cstShowaTaiso){
					$result = "昭和天皇の大喪の礼";
				}else;

				break;


			# 3月
			case 3:

				if($MyDay == $this->prvDayOfSpringEquinox($MyYear)){    # 1948～2150以外は[99]
					$result = "春分の日";                        # が返るので､必ず≠になる
				}else;

				break;


			# 4月
			case 4:

				if($MyDay == 29){

					if($MyYear >= 2007){
						$result = "昭和の日";
					}elseif($MyYear >= 1989){
						$result = "みどりの日";
					}else{
						$result = "天皇誕生日";
					}

				}elseif($MyDate == $cstAkihitoKekkon){

					$result = "皇太子明仁親王の結婚の儀";

				}else;

				break;


			# 5月
			case 5:

				if($MyDay == 3){

					$result = "憲法記念日";

				}elseif($MyDay == 4){

					if($MyYear >= 2007){

						$result = "みどりの日";

					}elseif($MyYear >= 1986){

						if($this->Weekday($MyDate) > MONDAY){
							# 5/4が日曜日は『只の日曜』､月曜日は『憲法記念日の振替休日』(～2006年)
							$result = "国民の休日";
						}else;

					}else;

				}elseif($MyDay == 5){

					$result = "こどもの日";

				}elseif($MyDay == 6){

					if($MyYear >= 2007){

						# 07/5/26 if条件の一番外側のカッコ不足を修正
						if(($this->Weekday($MyDate) == TUESDAY) || ($this->Weekday($MyDate) == WEDNESDAY)){
							$result = "振替休日";    # [5/3,5/4が日曜]ケースのみ、ここで判定
						}else;

					}else;

				}else;

				break;

			#  6月
			case 6:

				if($MyDate == $cstNorihitoKekkon){
					$result = "皇太子徳仁親王の結婚の儀";
				}else;

				break;


		  # 7月
			case 7:

				if($MyYear >= 2003){

					$strNumberOfWeek = (floor(($MyDay - 1) / 7) + 1) . $this->Weekday($MyDate);

					# Monday:2
					if($strNumberOfWeek == "32"){
						$result = "海の日";
					}else;

				}elseif($MyYear >= 1996){

					if($MyDay == 20){
						$result = "海の日";
					}else;

				}else;

				break;


		  # 9月
			case 9:

				# 第３月曜日(15～21)と秋分日(22～24)が重なる事はない
				$MyAutumnEquinox = $this->prvDayOfAutumnEquinox($MyYear);

				# 1948～2150以外は[99]
				if($MyDay == $MyAutumnEquinox){

					# が返るので､必ず≠になる
					$result = "秋分の日";

				}else{

					if($MyYear >= 2003){

						$strNumberOfWeek = (floor(($MyDay - 1) / 7) + 1) . $this->Weekday($MyDate);

						# Monday:2
						if($strNumberOfWeek == "32"){

							$result = "敬老の日";

						}elseif($this->Weekday($MyDate) == TUESDAY){

							if($MyDay == ($MyAutumnEquinox - 1)){
								$result = "国民の休日";
							}else;

						}else;

					}elseif($MyYear >= 1966){

						if($MyDay == 15){
							$result = "敬老の日";
						}else;

					}else;

				}

				break;

			# 10月
			case 10:

				if($MyYear >= 2000){

					$strNumberOfWeek = (floor(( $MyDay - 1) / 7) + 1) . $this->Weekday($MyDate);

					if($strNumberOfWeek == "22"){    # Monday:2
						$result = "体育の日";
					}else;

				}elseif($MyYear >= 1966){

					if($MyDay == 10){
						$result = "体育の日";
					}else;

				}else;

				break;


			# 11月
			case 11:

				if($MyDay == 3){
					$result = "文化の日";
				}elseif($MyDay == 23){
					$result = "勤労感謝の日";
				}elseif($MyDate == $cstSokuireiseiden){    # 07/04/11 $抜け修正
					$result = "即位礼正殿の儀";
				}else;

				break;

			# 12月
			case 12:

			if($MyDay == 23){

				if($MyYear >= 1989){
					$result = "天皇誕生日";
				}else;

			}else;

			break;

		# SWICH END
		}


		return $result;

	}



	/**************************************************
	**
	**	prvDayOfSpringEquinox
	**
	**************************************************/

	private function prvDayOfSpringEquinox($MyYear){

	  if($MyYear <= 1947)
	      $result = 99; #祝日法施行前
	  elseif($MyYear <= 1979)
	      # floor 関数は[VBAのInt関数]に相当
	      $result = floor(20.8357 + (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
	  elseif($MyYear <= 2099)
	      $result = floor(20.8431 + (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
	  elseif($MyYear <= 2150)
	      $result = floor(21.851 + (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
	  else
	      $result = 99; #2151年以降は略算式が無いので不明

	  return $result;

	}


	/**************************************************
	**
	**	prvDayOfAutumnEquinox
	**
	**************************************************/

	private function prvDayOfAutumnEquinox($MyYear){

	  if($MyYear <= 1947)
	      $result = 99; #祝日法施行前
	  elseif($MyYear <= 1979)
	      # floor 関数は[VBAのInt関数]に相当
	      $result = floor(23.2588 + (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
	  elseif($MyYear <= 2099)
	      $result = floor(23.2488 + (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
	  elseif($MyYear <= 2150)
	      $result = floor(24.2488 + (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
	  else
	      $result = 99; #2151年以降は略算式が無いので不明

	  return $result;

	}


	private function Weekday($MyDate){
	  return strftime("%w",$MyDate) + 1;  # 日(1),月(2)‥‥土(7)
	}


	private function Year($MyDate){
	  return strftime("%Y",$MyDate) - 0;  # 数値で返す
	}


	private function Month($MyDate){
	  return strftime("%m",$MyDate) - 0;  # 数値で返す
	}

	private function Day($MyDate){
	  return strftime("%d",$MyDate) - 0;  # 数値で返す
	}


	public function getMarginTime($from,$now){

		$result	= NULL;

		if(empty($now)){
			$now	= date("Y-m-d H:i:s");
		}

		$marginSec	= strtotime($now) - strtotime($from);
		$marginMin	= intval( $marginSec / (60));

		$fromDay	= date("Y-m-d", strtotime($from));
		$nowDay		= date("Y-m-d", strtotime($now));
		$marginDay	= abs(strtotime($nowDay) - strtotime($fromDay));
	    $marginDay	= $marginDay / (60 * 60 * 24);

		if($marginMin == 0){
			$resultTime		= gmdate("s", $marginSec);
			$result			= $resultTime."秒前";
		}elseif($marginMin > 0 && $marginMin < 60){
			$resultTime		= gmdate("i", $marginSec);
			if($resultTime < 10){
				$resultTime	= str_replace("0","",$resultTime);
			}
			$result			= $resultTime."分前";
		}elseif($marginMin >= 60 && $marginMin < 1440){
			$resultTime		= gmdate("H", $marginSec);
			if($resultTime < 10){
				$resultTime	= str_replace("0","",$resultTime);
			}
			$result			= $resultTime."時間前";
		}elseif($marginDay > 0 && $marginDay < 7){
			$resultTime		= $marginDay;
			$result			= $resultTime."日前";
		}elseif($marginDay >= 7){
			$resultTime		= NULL;
			$result			= "一週間以上前";
		}

	    return $result;

	}


	public function getLimitTime($start_date,$end_date){

		if(empty($end_date)){
			return FALSE;
		}

		if(empty($start_date)){
			$start_date		= date("YmdHis");
		}


		$start_date			= strtotime($start_date);
		$end_date			= strtotime($end_date);

		$time				= ($end_date - $start_date);

		$hour				= (int)($time /3600);
		$minutes			= (int)($time % 3600 / 60);

		if($hour > 0){
			$hour_minutes	= $hour * 60;
		}else{
			$hour_minutes	= 0;
		}


		$result				= $minutes + $hour_minutes;

		return $result;

	}

}

?>