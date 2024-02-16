<?php
/********************************************************************************
**	
**	AuthClass.php
**	=============================================================================
**
**	■PAGE / 
**	AUTH MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	AUTH CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	各AUTH処理 認証・データ取得
**
**	プラットフォーム側とのOAUTH認証で使うよ
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

# OAuth ライブラリ
require_once(DOCUMENT_SYSTEM_PLUGINS."/Oauth/OAuth.php");


# CLASS DEFINE
class AuthClass{


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

		# SESSION START
		//session_start();
		//session_regenerate_id(TRUE);

    }

	# DESTRUCT
	function __destruct(){
		
    }



	/************************************************
	**
	**	checkOauthApiFromNijiyome
	**	---------------------------------------------
	**	初回時OAuth認証
	**
	************************************************/

	public function checkOauthApiFromNijiyome($data){

		$result			= NULL;

		/**/
		## にじよめAPI使えないとき用  //20190220 add by A.cos
		$result['error'] = "";
		$result['basestring']			= "xxx";//$basestring;
		$result['signature_key']		= "yyy";//$signature_key;
		$result['signature']			= "zzz";//$signature;
		$result['oauth_signature']		= "xyz";//$data['oauth_signature'];
		return $result;
		/**/

		if(empty($data)){
			return FALSE;
		}

		foreach($data as $key => $value){
			if($key == "oauth_signature"){ continue; }
			$parameter[$key]	= $value;
		}

		ksort($parameter);

		$request_params = http_build_query($parameter,"","&");

		$method			= rawurlencode("GET");

		$url			= rawurlencode(SITE_DOMAIN."/");
		$request_data	= rawurlencode($request_params);

		$basestring		= $method."&".$url."&".$request_data;

		$consumersecret	= OAUTH_CONSUMER_SECRET;
		$tokensecret	= $data['oauth_token_secret'];

		$signature_key	= $consumersecret."&".$tokensecret;

//		mail("eikoshi@k-arat.co.jp","test",$data['oauth_signature']."\n".$request_params."\n".$basestring."\n".$signature_key,"From:info@kyabaheru.net");

		$hash			= hash_hmac("sha1",$basestring,$signature_key,TRUE);
		$signature		= base64_encode($hash);

//		mail("eikoshi@k-arat.co.jp","test2",$signature,"From:info@kyabaheru.net");

		if($data['oauth_signature'] == $signature){
			$result['basestring']			= $basestring;
			$result['signature_key']		= $signature_key;
			$result['signature']			= $signature;
			$result['oauth_signature']		= $data['oauth_signature'];
		}else{
			$result							= FALSE;
		}

		return $result;

	}



	/************************************************
	**
	**	getUserDataFromNijiyome
	**	---------------------------------------------
	**	ユーザー認証
	**
	************************************************/

	public function getUserDataFromNijiyome(){

		/**/
		## にじよめAPI使えないとき用 //20190220 add by A.cos
		$result['error'] = "";
		$result['entry']['0']['id'] = "4620";
		return $result;
		/**/

		$endpoint_base			= API_ENDPOINT;

		$oauth_request			= OAuthRequest::from_request(null, null, null);

		$oauth_token			= $oauth_request->get_parameter('oauth_token');
		$oauth_token_secret		= $oauth_request->get_parameter('oauth_token_secret');
		$oauth_signature		= $oauth_request->get_parameter('oauth_signature');
		$opensocial_viewer_id	= $oauth_request->get_parameter('opensocial_viewer_id');
		$consumer_key			= $oauth_request->get_parameter('oauth_consumer_key');
		$consumer_secret		= OAUTH_CONSUMER_SECRET;

		if(empty($oauth_token) && !empty($_SESSION['auth']['oauth_token'])){
			$oauth_token			= $_SESSION['auth']['oauth_token'];
		}

		if(empty($oauth_token_secret) && !empty($_SESSION['auth']['oauth_token_secret'])){
			$oauth_token_secret		= $_SESSION['auth']['oauth_token_secret'];
		}

		if(empty($oauth_signature) && !empty($_SESSION['auth']['oauth_signature'])){
			$oauth_signature		= $_SESSION['auth']['oauth_signature'];
		}

		if(empty($opensocial_viewer_id) && !empty($_SESSION['auth']['opensocial_viewer_id'])){
			$opensocial_viewer_id	= $_SESSION['auth']['opensocial_viewer_id'];
		}

		if(empty($consumer_key)){
			$consumer_key			= OAUTH_CONSUMER_KEY;
		}

		# CREATE END POINT URL  [Use PeopleApi]
		$endpoint_url			= $endpoint_base . 'people/' . $opensocial_viewer_id . '/@self';

		# SIGN REQUEST -> OAuthライブラリ
		$signature_method		= new OAuthSignatureMethod_HMAC_SHA1();
		$oauth_consumer			= new OAuthConsumer($consumer_key, $consumer_secret);

		$oauth_request			= OAuthRequest::from_consumer_and_token(
		    $oauth_consumer,
		    null, // OAuthToken
		    'GET',
		    $endpoint_url,
		    null  // QueryStringParameter
		);

		$oauth_request->sign_request(
		    $signature_method,
		    $oauth_consumer,
		    null // OAuthToken
		);

		# BUILD API REQUEST
		$ch = curl_init();
		curl_setopt_array(
		    $ch,
		    array(
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_URL => $endpoint_url,
		        CURLOPT_HTTPHEADER => array(
		            'Content-Type: application/json',
		            $oauth_request->to_header()
		        ),
		    )
		);

		# SEND REQUEST
		$request_result = curl_exec($ch);

		# GET API RESULT
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		# CLOSE
		curl_close($ch);

		# OUTPUT RESULT
		switch($response_code)
		{
			/***********************
			**
			**	正常
			**
			***********************/
		    case 200:  // 正常終了

				# true -> array型 $result['entry'][0][id]
				# false -> object型 $result->entry[0]->id
		        $result = json_decode($request_result,true);
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム障害
			**
			***********************/
		    case 500:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム停止
			**
			***********************/
		    case 503:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	パラメータ異常
			**
			***********************/
		    case 400:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

			/***********************
			**
			**	認証失敗
			**
			***********************/
		    case 401:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

			/***********************
			**
			**	許可されないメソッド
			**
			***********************/
		    case 405:
		    default:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

		}

	}



	/************************************************
	**
	**	getUserDataFromNijiyomeByUserId
	**	---------------------------------------------
	**	プラットフォームユーザーデータ取得
	**
	************************************************/

	public function getUserDataFromNijiyomeByUserId($user_id,$fields=NULL){
		/**/
		## にじよめAPI使えないとき用 //20190220 add by A.cos
		$result['error'] = "";
		$result['entry']['0']['id'] = "5516";
		# NICKNAME
		$result['entry']['0']['nickname'] = "鹿";
		$result['entry']['0']['displayName'] = "鹿";
		# 性別
		$result['entry']['0']['gender']="male";
		# 年齢
		$result['entry']['0']['age'] = "30";
		# 生年月日
		$result['entry']['0']['birthday'] = "19800101";
		# ユーザータイプ
		$result['entry']['0']['userType'] = "1";
		# ユーザーグレード
		$result['entry']['0']['userGrade'] = "2";
		# プロフ画像
		$result['entry']['0']['thumbnailUrl'] = "test";
		return $result;
		/**/

		$endpoint_base			= API_ENDPOINT;

		$oauth_request			= OAuthRequest::from_request(null, null, null);

		$consumer_secret		= OAUTH_CONSUMER_SECRET;

		$consumer_key			= OAUTH_CONSUMER_KEY;


		# CREATE END POINT URL  [Use PeopleApi]
		$endpoint_url			= $endpoint_base . 'people/' . $user_id . '/@self';

		# GET FIELDS
		if(!empty($fields)){
			$endpoint_url		.= "?".$fields;
		}

		# SIGN REQUEST -> OAuthライブラリ
		$signature_method		= new OAuthSignatureMethod_HMAC_SHA1();
		$oauth_consumer			= new OAuthConsumer($consumer_key, $consumer_secret);

		$oauth_request			= OAuthRequest::from_consumer_and_token(
		    $oauth_consumer,
		    null, // OAuthToken
		    'GET',
		    $endpoint_url,
		    null  // QueryStringParameter
		);

		$oauth_request->sign_request(
		    $signature_method,
		    $oauth_consumer,
		    null // OAuthToken
		);

		# BUILD API REQUEST
		$ch = curl_init();
		curl_setopt_array(
		    $ch,
		    array(
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_URL => $endpoint_url,
		        CURLOPT_HTTPHEADER => array(
		            'Content-Type: application/json',
		            $oauth_request->to_header()
		        ),
		    )
		);

		# SEND REQUEST
		$request_result = curl_exec($ch);

		# GET API RESULT
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		# CLOSE
		curl_close($ch);

		# OUTPUT RESULT
		switch($response_code)
		{
			/***********************
			**
			**	正常
			**
			***********************/
		    case 200:  // 正常終了

				# true -> array型 $result['entry'][0][id]
				# false -> object型 $result->entry[0]->id
		        $result = json_decode($request_result,true);
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム障害
			**
			***********************/
		    case 500:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム停止
			**
			***********************/
		    case 503:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	パラメータ異常
			**
			***********************/
		    case 400:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

			/***********************
			**
			**	認証失敗
			**
			***********************/
		    case 401:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

			/***********************
			**
			**	許可されないメソッド
			**
			***********************/
		    case 405:
		    default:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

		}

	}



	/************************************************
	**
	**	createPaymentObjectFromNijiyomeByUserId
	**	---------------------------------------------
	**	課金アイテム生成
	**
	************************************************/

	public function createPaymentObjectFromNijiyomeByUserId($user_id,$item_data){

		if(empty($user_id) || empty($item_data)){
			return FALSE;
		}

		$endpoint_base					= API_ENDPOINT;

		$oauth_request					= OAuthRequest::from_request(NULL, NULL, NULL);

		$oauth_token					= $oauth_request->get_parameter('oauth_token');
		$oauth_token_secret				= $oauth_request->get_parameter('oauth_token_secret');
		$oauth_signature				= $oauth_request->get_parameter('oauth_signature');
		$opensocial_viewer_id			= $oauth_request->get_parameter('opensocial_viewer_id');
		$consumer_key					= $oauth_request->get_parameter('oauth_consumer_key');
		$consumer_secret				= OAUTH_CONSUMER_SECRET;

		if(empty($oauth_token) && !empty($_SESSION['auth']['oauth_token'])){
			$oauth_token				= $_SESSION['auth']['oauth_token'];
		}

		if(empty($oauth_token_secret) && !empty($_SESSION['auth']['oauth_token_secret'])){
			$oauth_token_secret			= $_SESSION['auth']['oauth_token_secret'];
		}

		if(empty($oauth_signature) && !empty($_SESSION['auth']['oauth_signature'])){
			$oauth_signature			= $_SESSION['auth']['oauth_signature'];
		}

		if(empty($opensocial_viewer_id) && !empty($_SESSION['auth']['opensocial_viewer_id'])){
			$opensocial_viewer_id		= $_SESSION['auth']['opensocial_viewer_id'];
		}

		if(empty($consumer_key)){
			$consumer_key				= OAUTH_CONSUMER_KEY;
		}

		$oauth_consumer 				= new OAuthConsumer($consumer_key, $consumer_secret, NULL);

		# Token from Gadget Server
		$token_from_gadget				= new OAuthToken($oauth_token, $oauth_token_secret);

		# CREATE END POINT URL [Use PaymentApi]
		$endpoint_url					= $endpoint_base . 'payment/@me/@self/@app?xoauth_requestor_id='.$user_id;

		# METHOD
		$method							= "POST";

		# PARAMS
		$params							= NULL;

		# Parse Parameters
		parse_str(parse_url($endpoint_url, PHP_URL_QUERY), $params);

		# AUTH REQUEST
		$oauth_request					= OAuthRequest::from_consumer_and_token(
		    $oauth_consumer,
		    $token_from_gadget,
		    $method,
		    $endpoint_url,
		    $params
		);

		# SIGN REQUEST -> OAuthライブラリ
		$signature_method				= new OAuthSignatureMethod_HMAC_SHA1();

		$oauth_request->sign_request(
		    $signature_method,
		    $oauth_consumer,
		    $token_from_gadget // OAuthToken
		);

		# AUTH HEADER
		$auth_header					= $oauth_request->to_header();

		$request_fields					= array();

		# DATA
		$request_fields					= array(
			'callbackUrl'				=> $item_data['callbackUrl'],
			'finishPageUrl'				=> $item_data['finishPageUrl'],
			'paymentItems'				=> array(
				array(
					'itemId'			=> $item_data['itemId'],
					'itemName'			=> $item_data['itemName'],
					'unitPrice'			=> $item_data['unitPrice'],
					'quantity'			=> $item_data['quantity'],
					'imageUrl'			=> $item_data['imageUrl'],
					'description'		=> $item_data['description'],
				)
			)
		);

		$post_data						= json_encode($request_fields);

		# BUILD API REQUEST
		$ch								= curl_init($endpoint_url);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',$auth_header));

		# SEND REQUEST
		$request_result					= curl_exec($ch);

		# GET API RESULT
		$response_code 					= curl_getinfo($ch, CURLINFO_HTTP_CODE);

		# CLOSE
		curl_close($ch);

		# OUTPUT RESULT
		switch($response_code)
		{
			/***********************
			**
			**	正常
			**
			***********************/
		    case 200:  // 正常終了

		        $result = json_decode($request_result,true);
				return $result;
		        break;

			/***********************
			**
			**	正常
			**
			***********************/
		    case 201:  // 正常終了

		        $result = json_decode($request_result,true);
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム障害
			**
			***********************/
		    case 500:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム停止
			**
			***********************/
		    case 503:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	パラメータ異常
			**
			***********************/
		    case 400:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />リクエスト不正。";
				return $result;
		        break;

			/***********************
			**
			**	認証失敗
			**
			***********************/
		    case 401:

				$result['error']	= 3;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

			/***********************
			**
			**	許可されないメソッド
			**
			***********************/
		    case 405:
		    default:

				$result['error']	= 3;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

		}

	}



	/************************************************
	**
	**	checkPaymentObjectFromNijiyomeByUserId
	**	---------------------------------------------
	**	課金アイテムチェック
	**
	************************************************/

	public function checkPaymentObjectFromNijiyomeByUserId($user_id,$payment_id){

		if(empty($user_id) || empty($payment_id)){
			return FALSE;
		}

		$endpoint_base					= API_ENDPOINT;

		$oauth_request					= OAuthRequest::from_request(null, null, null);

		$consumer_secret				= OAUTH_CONSUMER_SECRET;

		$consumer_key					= OAUTH_CONSUMER_KEY;

		# CREATE END POINT URL [Use PaymentApi]
		$endpoint_url					= $endpoint_base . 'payment/'.$user_id.'/@self/@app/'.$payment_id;

		# SIGN REQUEST -> OAuthライブラリ
		$signature_method				= new OAuthSignatureMethod_HMAC_SHA1();
		$oauth_consumer					= new OAuthConsumer($consumer_key, $consumer_secret);

		$oauth_request					= OAuthRequest::from_consumer_and_token(
		    $oauth_consumer,
		    null, // OAuthToken
		    'GET',
		    $endpoint_url,
		    null  // QueryStringParameter
		);

		$oauth_request->sign_request(
		    $signature_method,
		    $oauth_consumer,
		    null // OAuthToken
		);

		# BUILD API REQUEST
		$ch = curl_init();
		curl_setopt_array(
		    $ch,
		    array(
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_URL => $endpoint_url,
		        CURLOPT_HTTPHEADER => array(
		            'Content-Type: application/json',
		            $oauth_request->to_header()
		        ),
		    )
		);

		# SEND REQUEST
		$request_result					= curl_exec($ch);

		# GET API RESULT
		$response_code 					= curl_getinfo($ch, CURLINFO_HTTP_CODE);

		# CLOSE
		curl_close($ch);

		# OUTPUT RESULT
		switch($response_code)
		{
			/***********************
			**
			**	正常
			**
			***********************/
		    case 200:  // 正常終了

		        $result = json_decode($request_result,true);
				return $result;
		        break;

			/***********************
			**
			**	正常
			**
			***********************/
		    case 201:  // 正常終了

		        $result = json_decode($request_result,true);
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム障害
			**
			***********************/
		    case 500:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	プラットフォーム停止
			**
			***********************/
		    case 503:

		        throw new \Exception('予期せぬエラー');
				$result['error']	= 1;
				$result['message']	= "Status : ".$response_code."<br />予期せぬエラーが発生致しました。";
				return $result;
		        break;

			/***********************
			**
			**	パラメータ異常
			**
			***********************/
		    case 400:

				$result['error']	= 2;
				$result['message']	= "Status : ".$response_code."<br />リクエスト不正。";
				return $result;
		        break;

			/***********************
			**
			**	認証失敗
			**
			***********************/
		    case 401:

				$result['error']	= 3;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

			/***********************
			**
			**	許可されないメソッド
			**
			***********************/
		    case 405:
		    default:

				$result['error']	= 3;
				$result['message']	= "Status : ".$response_code."<br />認証失敗致しました。";
				return $result;
		        break;

		}

	}


}

?>