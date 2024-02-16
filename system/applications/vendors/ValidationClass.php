<?php
/********************************************************************************
**	
**	ValidationClass.php
**	=============================================================================
**
**	■PAGE / 
**	VALIDATION MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	VALIDATION CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	ERROR CHECK MODULES
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

/************************************************
**
**	PLUGIN FILE
**	---------------------------------------------
**	HTML TAG SPRIT & ALLOW
**	必須項目
**
************************************************/

require_once(DOCUMENT_SYSTEM_PLUGINS."/Kses/kses.php");

/*********************************************************************************/


# CLASS DEFINE
class ValidationClass{


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
	**	validateBbs
	**	---------------------------------------------
	**	CHECK BBS
	**
	************************************************/

	public function validateBbs($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;


		# REMOVE TAGS
		$data				= $this->removeTags($data);

		/*
		# USER NAME
		if(empty($data['name'])){

			//$error		.= "ニックネームを記入して下さい<br />\n";

		}else{

			# NG WORD
			if(!$this->checkNgWord($data['name'])){
				$error	.= "ニックネームに使用できない文字が含まれております<br />\n";
			}

		}
		*/

		# CONTENT
		if(empty($data['content'])){

			$error		.= "コメントを記入して下さい<br />\n";

		}else{

			# GET CHAR LENGTH
			$char_length = mb_strlen($data['content'], "UTF-8");

			if($char_length < MIN_LENGTH || $char_length > MAX_LENGTH){
				$error 	.= "コメントは".MIN_LENGTH."～".MAX_LENGTH."文字以内で入力して下さい<br />\n";
			}

			# NG WORD
			if(!$this->checkNgWord($data['content'])){
				$error	.= "コメントに使用できない文字が含まれております<br />\n";
			}

		}

		# APPROVAL
		if (empty($data['approval'])) {
			//$error 		.= "投稿するには利用規約への同意が必要です<br />\n";
		}

		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}


		return	$data;

	}



	/************************************************
	**
	**	validateBbs
	**	---------------------------------------------
	**	CHECK BBS
	**
	************************************************/

	public function validateMultiBbs($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;


		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# CONTENT
		if(empty($data['content'])){

			$error		.= "コメントを記入して下さい<br />\n";

		}else{

			# GET CHAR LENGTH
			$char_length = mb_strlen($data['content'], "UTF-8");

			if($char_length > 20){
				$error 	.= "コメントは20文字以内で入力して下さい<br />\n";
			}

			# NG WORD
			if(!$this->checkNgWord($data['content'])){
				$error	.= "コメントに使用できない文字が含まれております<br />\n";
			}

		}

		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}


		return	$data;

	}



	/************************************************
	**
	**	validateMulties
	**	---------------------------------------------
	**	CHECK BBS / CAPTURES
	**
	************************************************/

	public function validateMulties($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;

		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# COMMENT
		if($data['purpose'] == 1){

			$data			= $this->validateBbs($data);

		# THREAD
		}else if ($data['purpose'] == 2){

			global	$options_array;

			# USER NAME
			if(MULTI_NICKNAME == "ON"){

				if(empty($data['name'])){

					$error		.= "ニックネームを記入して下さい<br />\n";

				}else{

					# NG WORD
					if(!$this->checkNgWord($data['name'])){
						$error	.= "ニックネームに使用できない文字が含まれております<br />\n";
					}

				}

			}

			# OPTION 1
			if(MULTI_STAGE == "ON" && MULTI_EVENT == "OFF"){
				if(empty($data['option1'])){
					$error 	.= $options_array[4][3]."を選択して下さい<br />\n";
				}
			}

			# OPTION 2
			if(empty($data['option2'])){
				$error 		.= $options_array[5][3]."を選択して下さい<br />\n";
			}

			# MULTI No
			if(empty($data['detail1'])){
				$error 		.= MULTI_NAME."を入力して下さい<br />\n";
			}else{

				if(MULTI_TEXT == "ON"){

					$url_check	 = NULL;
					if(preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $data['detail1'], $result) !== false){
					    foreach ($result[0] as $value){
							$check	= $this->checkHttpUrl($value);
					        if(!empty($check)){
								$url_check	 = 1;
								break;
							}
					    }
					}

					if(empty($url_check)){
						$error 		.= MULTI_NAME."を入力して下さい<br />\n";
					}

				}

			}

			# CONTENT
			if(empty($data['content'])){

				$error		.= "募集内容を記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['content'])){
					$error	.= "募集内容に使用できない文字が含まれております<br />\n";
				}
			}

			# APPROVAL
			if(empty($data['approval'])){
				$error 		.= "投稿するには利用規約への同意が必要です<br />\n";
			}

		}


		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}

		return	$data;


	}



	/************************************************
	**
	**	validateFriends
	**	---------------------------------------------
	**	CHECK BBS / FRIENDS
	**
	************************************************/

	public function validateFriends($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;

		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# COMMENT
		if($data['purpose'] == 1){

			$data			= $this->validateBbs($data);

		# THREAD
		}else if ($data['purpose'] == 2){

			global	$type_name_array;

			# USER NAME
			if(empty($data['name'])){

				$error		.= "ニックネームを記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['name'])){
					$error	.= "ニックネームに使用できない文字が含まれております<br />\n";
				}

			}

			# LEADER CHARACTER
			if(empty($data['character_id'])){
				$error 		 .= MAIN_ITEM_NAME."を選択して下さい<br />\n";
			}

			# RARITY
			if(empty($data['option1']) && !empty($type_name_array[1][1])){
				$error 		 .= $type_name_array[1][1]."を選択して下さい<br />\n";
			}

			# RANK
			if(!empty($data['parameter2_check']) && empty($data['parameter2'])){
				$error		.= LEVEL_NAME."を記入して下さい<br />";
			}

			# ID
			if(empty($data['parameter1'])){

				$error 		 .= "IDを記入して下さい<br />\n";

			} else {

				 if (!preg_match("/^[a-zA-Z0-9]+$/", $data['parameter1'])) {
				 	$error 	.= "IDは半角英数字で入力して下さい<br />\n";
				 }

			}

			# CONTENT
			if(empty($data['content'])){

				$error		.= "コメントを記入して下さい<br />\n";

			}else{

				# GET CHAR LENGTH
				$char_length = mb_strlen($data['content'], "UTF-8");
				
				if($char_length < MIN_LENGTH || $char_length > MAX_LENGTH){
					$error 	.= "コメントは".MIN_LENGTH."～".MAX_LENGTH."文字以内で入力して下さい<br />\n";
				}

				# NG WORD
				if(!$this->checkNgWord($data['content'])){
					$error	.= "コメントに使用できない文字が含まれております<br />\n";
				}
			}

			# APPROVAL
			if(empty($data['approval'])){
				$error 		.= "投稿するには利用規約への同意が必要です<br />\n";
			}

		}


		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}

		return	$data;


	}



	/************************************************
	**
	**	validateFaqs
	**	---------------------------------------------
	**	CHECK BBS / FAQ
	**
	************************************************/


	public function validateFaqs($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;

		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# COMMENT
		if($data['purpose'] == 1){

			$data			= $this->validateBbs($data);

		# THREAD
		}elseif($data['purpose'] == 2){

			global	$type_name_array;


			# USER NAME
			if(empty($data['name'])){

				$error		.= "ニックネームを記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['name'])){
					$error	.= "ニックネームに使用できない文字が含まれております<br />\n";
				}

			}

			# OPTION
			if(empty($data['option1'])){

				$error 		 .= "質問のジャンルを選択して下さい<br />\n";

			}

			# TITLE
			if(empty($data['title'])){

				$error 		 .= "質問タイトルを入力して下さい<br />\n"; 

			}

			# CONTENT
			if(empty($data['content'])){

				$error		.= "質問本文を記入して下さい<br />\n";

			}else{

				# GET CHAR LENGTH
				$char_length = mb_strlen($data['content'], "UTF-8");
				
				if($char_length < MIN_LENGTH || $char_length > MAX_LENGTH){
					$error 	.= "コメントは".MIN_LENGTH."～".MAX_LENGTH."文字以内で入力して下さい<br />\n";
				}

				# NG WORD
				if(!$this->checkNgWord($data['content'])){
					$error	.= "コメントに使用できない文字が含まれております<br />\n";
				}
			}

			# APPROVAL
			if(empty($data['approval'])){
				$error 		.= "投稿するには利用規約への同意が必要です<br />\n";
			}

		}


		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}

		return	$data;


	}


	/************************************************
	**
	**	validateInvitations
	**	---------------------------------------------
	**	CHECK BBS / INVITATIONS
	**
	************************************************/

	public function validateInvitations($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;

		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# COMMENT
		if($data['purpose'] == 1){

			$data			= $this->validateBbs($data);

		# THREAD
		}else if ($data['purpose'] == 2){

			global	$type_name_array;



			# USER NAME
			if(empty($data['name'])){

				$error		.= "ニックネームを記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['name'])){
					$error	.= "ニックネームに使用できない文字が含まれております<br />\n";
				}

			}

			# LEADER CHARACTER
			if(empty($data['character_id'])){
				$error 		 .= MAIN_ITEM_NAME."を選択して下さい<br />\n";
			}

			# RARITY
			if(empty($data['option1'])){
				$error 		 .= $type_name_array[1][1]."を選択して下さい<br />\n";
			}

			# RANK
			if(!empty($data['parameter2_check'])){

				if(empty($data['parameter2'])){
					$error		.= LEVEL_NAME."を記入して下さい";
				}

			}else{

				# LEVEL
				if(empty($data['option2'])){
					$error 		 .= "レベルを入力して下さい<br />\n";
				}

			}

			# INVITATION CODE
			if(empty($data['invitation'])){
				if(empty($data['parameter1'])){

					$error 		 .= "招待コードを記入して下さい<br />\n";

				}else{

					 if (!preg_match("/^[a-zA-Z0-9]+$/", $data['parameter1'])) {
					 	$error 	.= "招待コードは半角英数字で入力して下さい<br />\n";
					 }

				}
			}else{
				if(empty($data['detail1'])){
					$error 		 .= "招待コードを記入して下さい<br />\n";
				}
			}

			# MESSAGE
			if(empty($data['content'])){

				$error		.= "メッセージを記入して下さい<br />\n";

			}else{

				# GET CHAR LENGTH
				$char_length = mb_strlen($data['content'], "UTF-8");
				
				if($char_length < MIN_LENGTH || $char_length > MAX_LENGTH){
					$error 	.= "メッセージは".MIN_LENGTH."～".MAX_LENGTH."文字以内で入力して下さい<br />\n";
				}

				# NG WORD
				if(!$this->checkNgWord($data['content'])){
					$error	.= "メッセージに使用できない文字が含まれております<br />\n";
				}
			}

			# APPROVAL
			if(empty($data['approval'])){
				$error 		.= "投稿するには利用規約への同意が必要です<br />\n";
			}

		}


		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}

		return	$data;


	}



	/************************************************
	**
	**	validateCaptures
	**	---------------------------------------------
	**	CHECK BBS / CAPTURES
	**
	************************************************/

	public function validateCaptures($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;

		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# COMMENT
		if($data['purpose'] == 1){

			$data			= $this->validateBbs($data);

		# THREAD
		}else if ($data['purpose'] == 2){

			global	$options_array;
			global	$stage_type_array;

			# USER NAME
			if(empty($data['name'])){

				$error		.= "ニックネームを記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['name'])){
					$error	.= "ニックネームに使用できない文字が含まれております<br />\n";
				}

			}

			if(SITE_ID == 1){

				# OPTION 1
				if(empty($data['option1'])){
					$error 		.= $options_array[1][3]."を選択して下さい<br />\n";
				}

				# OPTION 2
				if(empty($data['option2'])){
					$error 		.= $options_array[2][3]."を選択して下さい<br />\n";
				}

			}else{

				# OPTION 1
				if(empty($data['option1'])){
					$error 		.= $stage_type_array[0][1]."を選択して下さい<br />\n";
				}

				# OPTION 2
				if(empty($data['option2'])){
					$error 		.= $stage_type_array[0][1]."一覧を選択して下さい<br />\n";
				}

			}

			if(empty($data['title'])){
				$error 		.= "記事のタイトルを入力して下さい<br />\n";
			}


			# CONTENT
			if(empty($data['content'])){

				$error		.= "メッセージを記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['content'])){
					$error	.= "メッセージに使用できない文字が含まれております<br />\n";
				}
			}

			# APPROVAL
			if(empty($data['approval'])){
				$error 		.= "投稿するには利用規約への同意が必要です<br />\n";
			}

		}


		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}

		return	$data;


	}



	/************************************************
	**
	**	validateEvents
	**	---------------------------------------------
	**	CHECK EVENTS / EVENTS
	**
	************************************************/

	public function validateEvents($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;

		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# COMMENT
		if($data['purpose'] == 1){

			$data			= $this->validateBbs($data);

		# THREAD
		}else if ($data['purpose'] == 2){

			global	$options_array;

			# USER NAME
			if(empty($data['name'])){

				$error		.= "ニックネームを記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['name'])){
					$error	.= "ニックネームに使用できない文字が含まれております<br />\n";
				}

			}

			# MAIL ADDRESS
			if(empty($data['email'])){
				$error		.= "当選連絡用のメールアドレスを記入して下さい<br />\n";
			}else{
				if(!$check = $this->checkMailAddress($data['email'])){
					$error	.= "当選連絡用のメールアドレスの形式が不正です<br />\n";
				}
			}

			# CONTENT
			if(empty($data['content'])){

				$error		.= "コメントを記入して下さい<br />\n";

			}else{

				# NG WORD
				if(!$this->checkNgWord($data['content'])){
					$error	.= "コメントに使用できない文字が含まれております<br />\n";
				}
			}

			# APPROVAL
			if(empty($data['approval'])){
				$error 		.= "投稿するには利用規約への同意が必要です<br />\n";
			}

		}


		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}

		return	$data;


	}



	/************************************************
	**
	**	validateContacts
	**	---------------------------------------------
	**	CHECK Contact / FORM
	**
	************************************************/

	public function validateContacts($data){

		# ERROR
		$error		 		= NULL;
		$post_data['error']	= NULL;

		# REMOVE TAGS
		$data				= $this->removeTags($data);

		# CONTACT
		if($data['purpose'] == 1){

			global	$options_array;

			# USER NAME
			if(empty($data['name'])){

				$error		.= "お名前を記入して下さい<br />\n";

			}

			# MAIL ADDRESS
			if(empty($data['email'])){
				$error		.= "メールアドレスを記入して下さい<br />\n";
			}else{
				if(!$check = $this->checkMailAddress($data['email'])){
					$error	.= "メールアドレスの形式が不正です<br />\n";
				}
			}

			# CONTENT
			if(empty($data['content'])){

				$error		.= "お問い合わせ内容を記入して下さい<br />\n";

			}

		}


		if(!empty($error)){
			$data['error']			= $error;
			$data['name']			= $this->replaceDoubleQuotes($data['name']);
		}

		return	$data;


	}



	/**************************************************
	**
	**	deleteBom
	**	----------------------------------------------
	**	DELETE BOMB
	**
	**************************************************/

	public function deleteBom($str){

	    if (($str == NULL) || (mb_strlen($str) == 0)) {
	        return $str;
	    }

	    if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
	        $str = substr($str, 3);
	    }

		if(preg_match("/<(?=[a-z_0-9]+=)/m",$str)){
			$str		= str_replace("/<(?=[a-z_0-9]+=)/m","",$str);
		}

		if(preg_match("/>(?=[a-z_0-9]+=)/m",$str)){
			$str		= str_replace("/>(?=[a-z_0-9]+=)/m","",$str);
		}

	    return $str;

	}



	/**************************************************
	**
	**	stringEscape
	**	----------------------------------------------
	**	STRING ESPACE
	**
	**************************************************/

	public function stringEscape($str){

		#$str			= preg_replace('/<(?=[a-z_0-9]+=)/m','&lt;',$str);
		#$str			= preg_replace('/>(?=[a-z_0-9]+=)/m','&gt;',$str);
		#$str			= preg_replace('/&(?=[a-z_0-9]+=)/m','&amp;',$str);
		#$str			= preg_replace('/"(?=[a-z_0-9]+=)/m','&quot;',$str);
	    #$str			= preg_replace("/'(?=[a-z_0-9]+=)/m",'&apos;',$str);
		#$str			= addslashes($str);

	    return $str;

	}



	/**************************************************
	**
	**	makeErrorMessage
	**	----------------------------------------------
	**	MAKE ERROR MESSAGE
	**
	**************************************************/

	public function makeErrorMessage($array,$error){

		# EMPTY
		if(empty($error)){

			return FALSE;

		# NO ERROR
		}else{

			$error_message	= $array[$error][1];

		}

		return $error_message;

	}



	/**************************************************
	**
	**	checkDateTime
	**	----------------------------------------------
	**	日付整合性取得 年月日時分秒
	**
	**************************************************/

	public function checkDateTime($datetime){

	    if(!isset($datetime)){ $error_msg = "error"; return $error_msg ; }
		if(!is_numeric($datetime)){ print("error"); return false; }

		$del_kigo = array("/","-"," ",":","　");
		$rep_data = array("","","","","");
		$datetime = str_replace($del_kigo, $rep_data, $datetime);

		$yy = substr($datetime,0,4);
		$mm = substr($datetime,4,2);
		$dd = substr($datetime,6,2);
		$hh = substr($datetime,8,2);
		$ii = substr($datetime,10,2);
		$ss = substr($datetime,12,2);

		if (!checkdate($mm,$dd,$yy) ) { $error_msg = "error"; return $error_msg ; }
		if($ss < 0 || $ii < 0 || $hh < 0){ $error_msg = "error"; return $error_msg ; }
		if($hh > 23 || $ii > 59 || $ss > 59){ $error_msg = "error"; return $error_msg ; }

		$return_time = $yy.$mm.$dd.$hh.$ii.$ss;

		$result		 = date("Y-m-d H:i:s", strtotime($return_time));

		return $result;

	}



	/**************************************************
	**
	**	checkDate
	**	----------------------------------------------
	**	日付整合性取得 年月日
	**
	**************************************************/

	public function checkDate($date){

	    if(!isset($date)){ $error_msg = "error"; return $error_msg ; }
		if(!is_numeric($date)){ print("error"); return false; }

		$del_kigo = array("/","-"," ",":","　");
		$rep_data = array("","","","","");
		$datetime = str_replace($del_kigo, $rep_data, $datetime);

		$yy = substr($date,0,4);
		$mm = substr($date,4,2);
		$dd = substr($date,6,2);

		if (!checkdate($mm,$dd,$yy) ) { $error_msg = "error"; return $error_msg ; }

		$return_time = $yy.$mm.$dd;

		$result		 = date("Y-m-d", strtotime($return_time));

		return $result;

	}



	/************************************************
	**
	**	checkWord
	**	---------------------------------------------
	**	文字列をチェック
	**
	************************************************/

	function checkNgWord($str){

		if(empty($str)){
			return $str;
		}

		# FILE PATH
		$file_path		= DOCUMENT_SYSTEM_FILES."/txt/word.txt";

		# OPEN
		$fp				= fopen($file_path,'r');

		# CHECK
		$i=0;
		while (!feof($fp)){

		    $ng_word	= fgets($fp);
			$ng_word	= ereg_replace("\r|\n","",$ng_word);
			$ng_word	= trim($ng_word);

			if(empty($ng_word)){
				continue;
			}

			if(strpos($str,$ng_word) !== FALSE){
				return FALSE;
			}

			$i++;

			# LOOP STOP
			if($i >= 1000){
				return FALSE;
				exit();
			}

		}

		# CLOSE
		fclose($fp);

		return TRUE;

	}



	/************************************************
	**
	**	checkMailAddress
	**	---------------------------------------------
	**	メールアドレスか判別
	**
	************************************************/

	public function checkMailAddress($mail_address){

	    if (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'. 
	    '@'. 
	    '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'. 
	    '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $mail_address)) {
			return FALSE;
		}

		return TRUE;

	}



	/************************************************
	**
	**	checkHttpUrl
	**	---------------------------------------------
	**	URLをチェック
	**
	************************************************/

	function checkHttpUrl($url){

		if(preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/",$url)){
			return TRUE;
		}else{
			return FALSE;
		}

	}



	/************************************************
	**
	**	checkHttpUrlMulti
	**	---------------------------------------------
	**	複数のURLをチェック
	**
	************************************************/

	function checkHttpUrlMulti($url){

		if(empty($url)){
			return TRUE;
		}

		$count	= count($url);

		for($i=0;$i<=$count;$i++){

			if(empty($url[$i])){ continue; }

			if(!$this->checkHttpUrl($url[$i])){
				return FALSE;
				break;
			}

			if($i > $count){ breask; }

		}

		return TRUE;

	}



	/************************************************
	**
	**	checkCarrier
	**	---------------------------------------------
	**	メールアドレスからキャリア判別
	**
	************************************************/

	public function checkCarrier($mail_address){

		global	$mail_address_array;

		$count		= count($mail_address_array);

		$carrier	= NULL;

		for($i=1;$i<$count;$i++){
			if(preg_match("/".$mail_address_array[$i][1]."/",$mail_address)){
				$carrier	= $mail_address_array[$i][2];
				break;
			}
		}

		# PC
		if(empty($carrier)){
			$carrier	= 9;
		}

		return $carrier;

	}



	/************************************************
	**
	**	replaceDoubleQuotes
	**	---------------------------------------------
	**	$str内ダブルコーテーション置換
	**
	************************************************/

	public function replaceDoubleQuotes($str){

		# QUOTES REPLACE
		if(preg_match("/\"/",$str)){
			$str	= str_replace("\"","&quot;",$str);
		}

		return $str;

	}



	/**************************************************
	**
	**	removeTags
	**	----------------------------------------------
	**	ksesを使ったタグ除去ファンクション
	**
	**************************************************/

	public function removeTags($data){

		if(empty($data)){
			return $data;
		}

		$result				= NULL;
		$result				= array();

		foreach($data as $key => $value){
			if(!empty($value)){
				$value		= kses($value);
			}
			$result[$key]	= $value;
		}

		return $result;

	}



	/*********************************************
	**
	**	magicQuotes
	**	------------------------------------------
	**	get_magic_quotes_gpcのエスケープ処理を削除
	**	------------------------------------------
	**	stripslashes クォートされた文字列を元に戻します(デフォルトonの場合)
	**
	*********************************************/

	public function magicQuotes($str) {

		# 文字列変換
		$str = trim($str);
		/*
		$str = strip_tags($str); 顔文字が食われる為一時外します
		$str = preg_replace("/;/","",$str);
		$str = preg_replace("/　/","",$str);
		$str = preg_replace("/'/","",$str);
		*/

		# stripslashes
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}

		return $str;

	}



}

?>