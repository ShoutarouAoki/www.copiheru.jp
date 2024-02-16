<?php
/********************************************************************************
**	
**	SessionClass.php
**	=============================================================================
**
**	■PAGE / 
**	SESSION MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	SESSION CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	SESSION 引継ぎ
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
class SessionClass{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	private	$session_exist;

	# CONSTRUCT
	function __construct(){

		# SESSION START
		session_start();

		// 初期接続
		if(!isset($_SESSION['expires'])){
			// 接続時間の保存
			$_SESSION['expires'] = time();
		}

		if ($_SESSION['expires'] < time() - 7) {
			session_regenerate_id(true);
			$_SESSION['expires'] = time();
		
		}

    }

	# DESTRUCT
	function __destruct(){
		
    }



	/************************************************
	**
	**	getSessionExist
	**	---------------------------------------------
	**	GET SESSION
	**
	************************************************/

	public function getSessionExist($device_type=NULL,$device){

		# START
		$session_exist				= NULL;

		# DOCOMO SESSION ON
		if($device_type == "DoCoMo" && empty($device)){

			# SESSION EXIST
			$session_exist			= 1;

		# DOCOMO SESSION OUT
		}elseif($device_type == "DoCoMo" && isset($device)){

			# SESSION EXIST
			$session_exist			= NULL;

		# DEFAULT SESSION
		}elseif(empty($_SESSION['session_id'])){

			# SESSION ID
			$session_id				= session_id();

			# SESSION REWRITE
			$_SESSION['session_id']	= $session_id;

			# SESSION EXIST
			$session_exist			= 1;

		}

		return $session_exist;

	}



	/************************************************
	**
	**	resetAuthSessionFromNijiyome
	**	---------------------------------------------
	**	Auth情報 セッション破棄
	**
	************************************************/

	public function resetAuthSession(){

		if(!empty($_SESSION['auth'])){
			$_SESSION['auth']	= array();
			$_SESSION['set']	= NULL;
		}

	}



	/************************************************
	**
	**	setAuthSessionFromNijiyome
	**	---------------------------------------------
	**	Auth情報 セッション格納
	**
	************************************************/

	public function setAuthSession($data){

		if(!empty($data) && empty($_SESSION['auth'])){
			$_SESSION['auth']	= $data;
			$_SESSION['set']	= 1;
		}

	}



	/************************************************
	**
	**	getAuthSessionFrom
	**	---------------------------------------------
	**	Auth情報 セッション取得
	**
	************************************************/

	public function getAuthSession(){

		if(!empty($_SESSION['auth'])){
			$result	= $_SESSION['auth'];
			return $result;
		}else{
			return FALSE;
		}

	}



	/************************************************
	**
	**	checkAuthSession
	**	---------------------------------------------
	**	CHECK AUTH SESSIONI
	**
	************************************************/

	public function checkAuthSession($device_type=NULL,$member_id=NULL,$user_id=NULL,$user_pass=NULL){

		# NULL
		if(empty($_SESSION['member_id']) || empty($_SESSION['user_id']) || empty($_SESSION['user_pass'])){
			return FALSE;
		}

		# RESULT
		$result		= NULL;
		$result		= array();

		# DOCOMO SESSION
		if($device_type == "DoCoMo"){


			if(!empty($member_id)){

				/***********************************************************
				**	
				**	ACCOUNT ID / ACCOUNT PASS : -> SEND -> RETURN
				**	-------------------------------------------------------
				**	ID / PASS : 引き回し
				**	
				***********************************************************/

				# ACCOUNT ID / ACCOUNT PASS / CHECK CERTIFY
				$result['member_id']		= $member_id;
				$result['user_id']			= $user_id;
				$result['user_pass']		= $user_pass;
				$result['session_time']		= date("YmdHis");


				/************************************************
				**
				**	OUTPUT ADD REWRITE VAR
				**	---------------------------------------
				**	DOCOMO のみ 変数受け渡し
				**
				************************************************/

				output_add_rewrite_var("ai",$member_id);
				output_add_rewrite_var("ui",$user_id);
				output_add_rewrite_var("up",$user_pass);

			}


		# DEFAULT SESSION
		}else{


			/***********************************************************
			**	
			**	USER ID / USER PASS : -> SEND -> RETURN
			**	-------------------------------------------------------
			**	ID / PASS : 引き回し
			**	
			***********************************************************/

			# USER ID / USER PASS
			$result['member_id']			= $_SESSION['member_id'];
			$result['user_id']	 			= $_SESSION['user_id'];
			$result['user_pass']			= $_SESSION['user_pass'];


			/***********************************************************
			**	
			**	SESSION LOG OUT TIME
			**	-------------------------------------------------------
			**	
			**	
			***********************************************************/

			$check_time						= NULL;

			if(!empty($_SESSION['session_time'])){

				# BEFORE PAGE SETTION TIME
				$check_time					= $_SESSION['session_time'];

				# UNSET SESSION TIME
				unset($_SESSION['session_time']);

			}

			# ACCSESS SESSION TIME
			$_SESSION['session_time']		= date("YmdHis");


		}

		return $result;


	}



	/************************************************
	**
	**	setToken
	**	---------------------------------------------
	**	SET Token
	**
	************************************************/

	public function setToken(){

    	$token					= bin2hex(openssl_random_pseudo_bytes(16));
    	$_SESSION['token'] 		= $token;
		output_add_rewrite_var("token",$token);

	}



	/************************************************
	**
	**	checkToken
	**	---------------------------------------------
	**	SET Token
	**
	************************************************/

	public function checkToken($token){

		if(empty($token)){
			return FALSE;
		}

		#$_SESSION['token']	= "takai";

		if(empty($_SESSION['token'])){
			return FALSE;
		}elseif($_SESSION['token'] == $token){
			return TRUE;
		}else{
			return FALSE;
		}

    	return FALSE;

	}



	/************************************************
	**
	**	sessionDestroy
	**	---------------------------------------
	**	DESTROY -> UNSET : 総ての値を破棄
	**
	************************************************/

	public function sessionDestroy($token){

		# COOKIE DELETE -> ERRORの場合はLOGIN COOKIEを破棄
		if(isset($_SESSION['auto_login'])){
			setcookie("login_id",	"",	time() - 60,	"/login/");
			setcookie("login_pass",	"",	time() - 60,	"/login/");
		}

		# SESSION FREE
		$_SESSION = array();

		# SESSION DESTROY
		session_destroy();


	}


}

?>