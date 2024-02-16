<?php
################################ FILE MANAGEMENT ################################
##
##	debug.config.php
##	=============================================================================
##
##	■PAGE / 
##	KARAT DEBUG CONFIG SETTING
##
##	=============================================================================
##
##	■MEANS / 
##	KARAT DEBUG定数
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2016/05/07
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
#################################################################################


/***********************************************************
**
**	DEBUG
**
***********************************************************/

# DEBUG MODE
define("DEBUG_MODE",							"ON");

# DEBUG VIEW
define("DEBUG_VIEW",							"smart");

# DEBUG SQL  : ALL / NORMAL / NONE
define("DEBUG_SQL",								"ALL");




/***********************************************************
**	
**	デバッグ用function
**	-------------------------------------------------------
**	出力
**	
***********************************************************/

function pr($string=NULL){

	if(defined("SYSTEM_CHECK") && !empty($string)){

		print("<pre>");
		print_r($string);
		print("</pre>");
		print("<hr style=\"border: 1px solid #CCCCCC;\" />");

	}

}



/***********************************************************
**	
**	デバッグ用function
**	-------------------------------------------------------
**	メール
**	
***********************************************************/

function ml($subject=NULL,$message=NULL,$array_data=NULL){

	if(empty($subject)){
		$subject			= "DEBUG MAIL";
	}

	# データ
	if(!empty($array_data)){
		foreach($array_data as $key => $value){
			$message		.= "\n\n【".$key."】\n".$value;
		}
	}

	if(empty($message)){
		$message			= "DEBUG MAIL";
	}

	mail("eikoshi@k-arat.co.jp",$subject,$message,"From:info@kyabaheru.net");
	
}


?>