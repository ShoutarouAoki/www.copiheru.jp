<?php
################################ FILE MANAGEMENT ################################
##
##	config.php
##	=============================================================================
##
##	■PAGE / 
##	SITE ROOT CONFIG
##	DEFAULT CONFIG SETTING
##
##	=============================================================================
##
##	■MEANS / 
##	サイトROOT内基本設定
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
############################ FILE READING START TIME ############################

# SYSTEM START TIME
$system_start_time	= microtime();
$system_start_secd	= date("i");

#################################################################################



/***********************************************************
**	
**	CONF FILE REQUIRE
**	-------------------------------------------------------
**	site.ini.php			: サイト基本定義
**	ip.config.php			: IP管理 / CHECK
**	php.ini.php				: PHP設定上書き
**	initialization.php		: 変数初期化
**	default.config.php		: サイト構造設定(基本)
**	option.config.php		: サイト構造設定(追加)
**	database.config.php		: データベース情報設定
**	mail.config.php			: メール設定
**	error.config.php		: エラー設定
**	emoji.config.php		: 絵文字用設定
**	auth.config.php			: OAuth用設定
**	debug.config.php		: デバッグ用
**
***********************************************************/

# SITE INI SETTING (BASIC)
require_once(dirname(__FILE__)."/site.ini.php");

# IP CHECK
require_once(dirname(__FILE__)."/ip.config.php");

# PHP INI SETTING (BASIC)
require_once(dirname(__FILE__)."/php.ini.php");

# INITIALIZATION (BASIC)
require_once(dirname(__FILE__)."/initialization.php");

# SITE DEFAULT SETTING (BASIC)
require_once(dirname(__FILE__)."/default.config.php");

# SITE OPTION SETTING (BASIC)
require_once(dirname(__FILE__)."/option.config.php");

# DATABASE SETTING
require_once(dirname(__FILE__)."/database.config.php");

# MAIL SETTING
require_once(dirname(__FILE__)."/mail.config.php");

# ERROR SETTING
require_once(dirname(__FILE__)."/error.config.php");

# EMOJI SETTING
require_once(dirname(__FILE__)."/emoji.config.php");

# AUTH SETTING
require_once(dirname(__FILE__)."/auth.config.php");

# DEBUG SETTING
require_once(dirname(__FILE__)."/debug.config.php");


?>