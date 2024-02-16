<?php
################################ FILE MANAGEMENT ################################
##
##	auth.config.php
##	=============================================================================
##
##	■PAGE / 
##	ROOT AUTH CONFIG
##	AUTH SETTING
##
##	=============================================================================
##
##	■MEANS / 
##	プラットフォームAuth用
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


/**********************************************************
**
**	にじよめ AUTH情報
**
***********************************************************/

# 本番
/*
define("APP_ID",					"499");
define("OAUTH_CONSUMER_KEY",		"53f653bc3ecfd71894a8f3e86da4c3");
define("OAUTH_CONSUMER_SECRET",		"fde0bfe34d");
define("API_ENDPOINT",				"http://api.nijiyome.jp/api/rest/");
define("APP_URL",					"http://www.nijiyome.com/app/start/499");
*/
# 開発環境
define("APP_ID",					"683");
define("OAUTH_CONSUMER_KEY",		"5280e02e0c487619f80b4c6c90ebc0");
define("OAUTH_CONSUMER_SECRET",	"4b0b4acc83");
define("API_ENDPOINT",			"https://spapi.nijiyome.jp/spapi/rest/");
// define("APP_URL",					"https://sb.nijiyome.jp/apps/start/683");
//define("APP_URL",					"http://kyabaheru.higatest.com");
define("APP_URL",					"http://copiheru.higatest.com");



#http[s]://spapi.nijiyome.jp/spapi/rest/people -> ここに使いたいAPIを

?>