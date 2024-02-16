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

	function htmlHeader($page,$site_cd=NULL){

		ob_start();
		print("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
		print("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");
		print("<head>\n");
		print("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n");
		print("<meta http-equiv=\"Content-Language\" content=\"ja\">\n");
		print("<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n");
		print("<meta http-equiv=\"Content-Script-Type\" content=\"text/javascrip\" />\n");
		if($page == "main"){
		print("<link href=\"./CONF/css/import.css\" rel=\"stylesheet\" type=\"text/css\">\n");
		if(!empty($site_cd)){
		print("<link href=\"./CONF/css/".$site_cd."/style.css\" rel=\"stylesheet\" type=\"text/css\">\n");
		}
		}else{
		print("<link href=\"../CONF/css/import.css\" rel=\"stylesheet\" type=\"text/css\">\n");
		if(!empty($site_cd)){
		print("<link href=\"../CONF/css/".$site_cd."/style.css\" rel=\"stylesheet\" type=\"text/css\">\n");
		}
		#print("<script type=\"text/javascript\" src=\"../CONF/js/jquery.js\"></script>\n");
		#print("<script type=\"text/javascript\" src=\"../CONF/js/script.js\"></script>\n");
		}
		print("<title>".D_SITE_NAME."</title>\n");
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


}


?>
