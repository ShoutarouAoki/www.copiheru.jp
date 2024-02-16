<?php
################################ FILE MANAGEMENT ################################
##
##	indexController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	INDEX SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	INDEX 各種処理
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2014/10/31
##	CREATER		:
##
##	=============================================================================
##
##	■ REWRITE (改修履歴)
##
##
##
##
##
##
##
##
##
##
################################# REQUIRE MODEL #################################


/************************************************
**
**	MODEL FILE REQUIRE
**	---------------------------------------------
**	MODEL CLASS FILE READING
**
************************************************/

/** ARTIUCLE MODEL **/
//require_once(DOCUMENT_ROOT_MODELS."/ArticleModel.php");


################################# POST ARRAY ####################################

$value_array				= array('page','id');
$data						= $mainClass->getArrayContents($value_array,$values);

############################## INDIVIDUAL SETTING ###############################


/************************************************
**
**	THIS PAGE INDIVIDUAL SETTING
**	---------------------------------------------
**	DATABASE / PATH / CATEGORY ...etc
**
************************************************/

# PAGE
if(empty($data['page'])){
	$data['page']			= "index";
}

# EVENT BANNER DATA
$banner_array				= array();
$banner_array				= array(
	'index'					=> '1',
	'thread_id'				=> '0',
	'device'				=> $device_number,
	'os'					=> $os_number
);


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/

# ARTICLE MODEL
//$articleModel				= new ArticleModel($database,$mainClass);


################################## MAIN SQL #####################################


/************************************************
**
**	PAGE SEPALATE
**	---------------------------------------------
**	DISPLAY
**	---------------------------------------------
**	PAGE CONTROLL
**
**	$exectionがNULLなら
**	表示処理開始
**	---------------------------------------------
**	PAGE :	
**
************************************************/

if(empty($exection)){


	/************************************************
	**
	**	ページ毎にif文で処理分岐
	**
	************************************************/

	# INDEX
	if($data['page'] == "index"){

		//require_once(DOCUMENT_SYSTEM_PLUGINS."/Oauth/OAuth.php");
		require_once(DOCUMENT_SYSTEM_PLUGINS."/Oauth/OauthConsumer.php");


		foreach($_REQUEST as $key => $value){
			if($key == "oauth_signature"){ continue; }
			$parameter[$key]	= $value;
		}

		ksort($parameter);

		$request_params = http_build_query($parameter,"","&");

		$method			= rawurlencode($_SERVER['REQUEST_METHOD']);
		$url			= rawurlencode("http://".HTTP_HOST);
		$request_data	= rawurlencode($request_params);

		$basestring		= $method."&".$url."&".$request_data;

		//$consumersecret	= rawurlencode(NIJIYOME_OAUTH_CONSUMER_SECRET);
		//$tokensecret	= rawurlencode($_REQUEST['oauth_token_secret']);

		$consumersecret	= NIJIYOME_OAUTH_CONSUMER_SECRET;
		$tokensecret	= $_REQUEST['oauth_token_secret'];

		$signature_key	= $consumersecret."&".$tokensecret;

		$hash			= hash_hmac("sha1",$basestring,$signature_key,TRUE);
		$signature		= base64_encode($hash);



		print("base_string : ".$basestring."<br /><br />");
		print("key : ".$signature_key."<br /><br />");
		print("signature : ".$signature."<br /><br />");

		print("answer : ".$_REQUEST['oauth_signature']."<br /><br />");

		/************************************
		**
		**	SEND
		**
		*************************************/
		/*

		$consumer_key		= NIJIYOME_OAUTH_CONSUMER_KEY;
		$consumer_secret	= NIJIYOME_OAUTH_CONSUMER_SECRET;
		$cons				= new keyOAuth($consumer_key, $consumer_secret);

		$headers			= apache_request_headers();

		$headers			= explode("," , $headers['Authorization']);
		for($i=1; $i<count($headers); $i++ ) {
			list($key, $val) = explode("=" , $headers[$i]);
			preg_match('/"(.*?)"/', $val, $matches);
			$param_data[$key] = $matches[1];
		}

		$org_array = array(
		                'hogehoge',
		                'piyo' => 'piyopiyo',
		                'fruits' => array(
		                            'apple',
		                            'orange',
		                            'mellon',
		                        ),
		                'city' => array(
		                            'tokyo' => 'meguro',
		                            'osaka' => 'kyobashi',
		                            'hukuoka' => 'hakata',
		                        ),
		                'huga' => array(
		                            array('happy', 'bad', 'lucky'),
		                            array(100,200,400,800)
		                        ),
		                'game_history' => array(
		                            array('name' => 'famicon', 'start' => 1993, 'end'=>2000),
		                            array('name' => 'playstation', 'start' => 2000, 'end'=>2006),
		                            array('name' => 'wii', 'start' => 2006, 'end'=>2007),
		                        ),
		                    );

		$put_data = json_encode($org_array);

		$get_params = array(
			"oauth_consumer_key"		=> $parameter['oauth_consumer_key'],
			"oauth_nonce" 				=> $parameter['oauth_nonce'],
			"oauth_signature_method"	=> $parameter['oauth_signature_method'],
			"oauth_timestamp"			=> $parameter['oauth_timestamp'],
			"oauth_token"				=> $parameter['oauth_token'],
			"oauth_token_secret"		=> $parameter['oauth_token_secret'],
			"oauth_version"				=> $parameter['oauth_version'],
			"opensocial_app_id"			=> $_GET['opensocial_app_id'],
			"opensocial_owner_id"		=> $_GET['opensocial_owner_id'],
			"opensocial_viewer_id"		=> $_GET['opensocial_viewer_id']
		);

		$end_point			= NIJIYOME_API_ENDPOINT."people";
		$req				= OauthRequest::set( $cons, 'GET', $end_point, $parameter, $put_data);
		$req->createSignature( $cons , $parameter['oauth_token_secret'] );
		$req->execCurl();
		*/



		/*
		print("<pre>");
		print_r($_REQUEST);
		print("</pre>");
		*/

		/*
		[oauth_consumer_key] => ef2fb22e423fef126043685ad41e49
		[oauth_nonce] => 604091fb993f2abbdd41030354ee648a
		[oauth_signature_method] => HMAC-SHA1
		[oauth_timestamp] => 1463984734
		[oauth_token] => a2535a6c7d4980f83bc3
		[oauth_token_secret] => 01bc773d72
		[oauth_version] => 1.0
		[opensocial_app_id] => 445
		[opensocial_owner_id] => 3962
		[opensocial_viewer_id] => 3962

		oauth_consumer_key=f7b5fb0d9cfbcff45f8eedf32e2a3b&
		oauth_nonce=52ac5918165ee7e49c9c7e47b41b0af4&
		oauth_signature_method=HMAC-SHA1&
		oauth_timestamp=1375348570&
		oauth_token=f5c97ea78640302f7b35&
		oauth_token_secret=027c1a963b&
		oauth_version=1.0&
		opensocial_app_id=1&
		opensocial_owner_id=0123456&
		opensocial_viewer_id=0123456
		*/

		/*
		$headers = apache_request_headers();
		print("<pre>");
		var_dump($headers);
		print_r($_SERVER);
		print("</pre>");
		*/

	}

}


################################## FILE END #####################################
?>