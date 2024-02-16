<?php
/********************************************************************************
**	
**	MailClass.php
**	=============================================================================
**
**	■PAGE / 
**	MAIL MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	NAIL CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	MAIL送信クラス
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
class MailClass{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	private	$db;
	private	$main_class;

	# CONSTRUCT
	function __construct($database=NULL,$main=NULL){
		$this->db			= $database;
		$this->main_class	= $main;
    }

	# DESTRUCT
	function __destruct(){
		
    }



	/**************************************************
	**
	**	sendMail
	**	----------------------------------------------
	**	通常メール送信
	**
	**************************************************/

	public function sendMail($mail_address,$subject,$message,$send_address=NULL,$mail_footer=NULL,$type=NULL){

		if(empty($mail_address)){
			return FALSE;
		}

		# DB / MAIN CLASS
		$db			 = NULL;
		$db			 = $this->db;
		$main_class	 = NULL;
		$main_class	 = $this->main_class;

		/*************************************
		**
		**	MULTI BYTE SETTING
		**
		**************************************/

		mb_language("Ja");
		mb_internal_encoding("SJIS");


		/*************************************
		**
		**	MAIL ADDRESS
		**
		**************************************/

		# NO SEND ADDRESS
		if(empty($send_address)){
			return FALSE;
		}


		/*************************************
		**
		**	ERROR
		**
		**************************************/

		# SEND OFFICIAL
		if(empty($type)){
			$error		= "-f".$send_address;
		# SEND USER
		}else{
			$error		= "-f".MAIL_ERROR;
		}


		/*************************************
		**
		**	FILE GET
		**
		**************************************/

		# TITLE / CONTENT
		$title		 = stripslashes($subject);
		$content	 = stripslashes($message);

		if(!empty($mail_footer)){
			$content	.= "\n\n".$mail_footer;
		}


		/*************************************
		**
		**	RELAY
		**
		**************************************/

		if(RELAY_MAIL == "ON"){


			# CURL RELAY
			if($result = $this->curlRelayMail($mail_address,$title,$content,$send_address,$error)){
				return TRUE;
			}else{
				return FALSE;
			}


		/*************************************
		**
		**	NORMAL
		**
		**************************************/

		}else{


			/*************************************
			**
			**	ENCODING
			**
			**************************************/

			$title		= mb_convert_encoding($title,'SJIS','UTF-8');
			$content	= mb_convert_encoding($content,'SJIS','UTF-8');


			/*************************************
			**
			**	SEND MAIL
			**
			**************************************/

			# MB SEND MAIL
			if(mb_send_mail($mail_address,$title,$content,"From:".$send_address,$error)){
				return TRUE;
			}else{
				return FALSE;
			}


		}


	}



	/************************************************
	**
	**	curlRelayMail
	**	---------------------------------------------
	**	RELAY MAIL SEND BY CURL
	**
	************************************************/

	public function curlRelayMail($mail_address,$title,$content,$send_address,$error){

		if(empty($mail_address)){
			return FALSE;
		}

		if(empty($title)){
			return FALSE;
		}

		if(empty($content)){
			return FALSE;
		}

		if(empty($send_address)){
			return FALSE;
		}

		if(empty($error)){
			return FALSE;
		}

		# RELAY DOMAIN
		global	$global_relay_array;

		# RELAY SELECT
		$count			= count($global_relay_array);
		$check			= $count - 1;
		$random			= mt_rand(0,$check);
		$relay_domain	= $global_relay_array[$random][1];
		$relay_url		= RELAY_DOMAIN.RELAY_FILE;

		# ENCORDING
		$mail_address	 = rawurlencode($mail_address);
		$title			 = rawurlencode($title);
		$content		 = rawurlencode($content);
		$send_address	 = rawurlencode($send_address);
		$error			 = rawurlencode($error);

		# PARAMETER
		$parameter		 = "mail_address=".$mail_address;
		$parameter		.= "&title=".$title;
		$parameter		.= "&content=".$content;
		$parameter		.= "&send_address=".$send_address;
		$parameter		.= "&error=".$error;

		# CURL SEND
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $relay_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $parameter );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
		$res = curl_exec( $ch ); 
		curl_close( $ch );

		return TRUE;

	}



}

?>