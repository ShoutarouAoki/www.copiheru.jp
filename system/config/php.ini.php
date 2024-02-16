<?php
################################ FILE MANAGEMENT ################################
##
##	php.ini.php
##	=============================================================================
##
##	■PAGE / 
##	PHP INI REWRITE
##	PHP INI SETTING
##
##	=============================================================================
##
##	■MEANS / 
##	php.ini 上書き変更
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
#################################################################################


/***********************************************************
**	
**	ERROR INI SETTING
**	-------------------------------------------------------
**	PHP ERROR 表示設定
**	SYSTEMとUSERの切り分け
**	
***********************************************************/

# SYSTEM ERROR
if(defined("SYSTEM_CHECK")){

	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	#error_reporting(E_ERROR | E_WARNING | E_PARSE);

	ini_set('display_errors',		1);

	ini_set('log_errors',		"On");

# USER ERROR
}else{

	ini_set('display_errors',		0);

}



/***********************************************************
**	
**	SESSION INI SETTING
**	-------------------------------------------------------
**	session破棄時間設定 -> LOGOUT
**	現在停止中
**	
***********************************************************/

# SESSION
ini_set('session.cookie_lifetime',	0);
ini_set('session.use_cookies',		1);
ini_set('session.use_only_cookies',	1);
ini_set('session.hash_function',	1);
ini_set('session.use_trans_sid',	0);
ini_set('url_rewriter.tags',		'form=');



/***********************************************************
**	
**	MAGIC QUOT -> 自動エスケープ処理
**	-------------------------------------------------------
**	$magic_quotes : 初期化
**	サーバーのmagic_quotes_gpcがONの場合は1
**	
***********************************************************/

$magic_quotes	= get_magic_quotes_gpc();





?>