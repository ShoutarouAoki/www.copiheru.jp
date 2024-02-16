<?php
################################ FILE MANAGEMENT ################################
##
##	default.config.php
##	=============================================================================
##
##	■PAGE / 
##	ROOT OPTION CONFIG
##	DEFAULT ROOT SETTING
##
##	=============================================================================
##
##	■MEANS / 
##	ROOT内サイトデフォルト【変数】設定
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
**	SITE SETTING
**	-------------------------------------------------------
**	DEFINE NAME
**	-------------------------------------------------------
**	SETTING SITE
**
***********************************************************/

# SITE CODE
define("SITE_CD",							"1");

# SITE NAME
define("SITE_NAME",							"こぴへる");

# USER NAME
define("USER_NAME",							"カラット");

# COPYRIGHTS
define("COPYRIGHTS",						"© カラット");



/***********************************************************
**
**	CONTENTS SETTING
**	-------------------------------------------------------
**	DEFINE NAME
**	-------------------------------------------------------
**	表記系の定義
**
***********************************************************/

# TICKET NAME
define("TICKET_NAME",						"tok");
define("TICKET_UNIT_NAME",					"個");
define("TICKET_NAME_USER",					"ゴールドtok");
define("TICKET_NAME_FREE",					"シルバーtok");

# DEGREE NAME
define("DEGREE_NAME",						"ランク");

# COIN NAME
define("COIN_NAME",							"にじコイン");

# GACHA ITEM ID
define("GACHA_ITEM_ID",						"gii0001");

# GACHA ITEM NAME
define("GACHA_ITEM_NAME",					"嬢指名ガチャ");

# GACHA ITEM DESCRIPTION
define("GACHA_ITEM_DESCRIPTION",			"購入することでエロシーンやアイテムがGET出来るtokガチャを回せます！");



/***********************************************************
**
**	SYSTEM SETTING
**	-------------------------------------------------------
**	DEFINE NAME
**	-------------------------------------------------------
**	機能系の定義
**
***********************************************************/

# 送信時の確認ポップアップ
define("MAIL_SEND_CONFIRM",					"ON");

# 開封時の確認ポップアップ
define("MAIL_READ_CONFIRM",					"ON");

# 確認ポップアップ ２回目以降出すかのチェック
define("MAIL_CONFIRM_CHECK",				"OFF");

# ポイントでガチャ引けるか
define("GACHA_USE_POINT",					"OFF");



/***********************************************************
**
**	NUMERIC VALUE SETTING
**	-------------------------------------------------------
**	DEFINE NAME
**	-------------------------------------------------------
**	数値系の定義
**
***********************************************************/

# 一回の送信で消費するポイント
define("DEFAULT_SEND_POINT",				1);

# 一回の開封で消費するポイント
define("DEFAULT_READ_POINT",				1);

# 未読画像閲覧で消費するポイント
define("DEFAULT_IMAGE_POINT",				0);

# ショップで購入できるポイント単価
define("DEFAULT_SHOP_POINT",				1);

# ガチャで消費するポイント
define("DEFAULT_GACHA_POINT",				5);

# ガチャで消費するPF通貨
define("DEFAULT_GACHA_COIN",				300);

# 1通送信で何パーセント好感度を上げるか
define("DEFAULT_FAVORITE_POINT",			10);

# 1通送信でキャラに付くランキングポイント
define("DEFAULT_RANKING_POINT",				1);

# メール返信画面での最大送信文字数
define("MAIL_MESSAGE_MAX_LENGTH",			90);


# ニックネーム最大文字数
define("NICKNAME_MAX_LENGTH",				10);

# プロフィールメッセージの最大文字数
define("PROFILE_MESSAGE_MAX_LENGTH",		100);

# 返信画面やりとり表示件数
define("MAIL_LIST_UNIT",					10);

# プレゼントボックス表示件数
define("PRESENTBOX_LIST_UNIT",				20);

# アルバム表示件数
define("ALBUM_LIST_UNIT",					20);

# お知らせ表示件数
define("NEWS_LIST_UNIT",					10);

# 無料ポイント所持上限
define("FREE_POINT_LIMIT",					9999);



/***********************************************************
**
**	AD CODE
**	-------------------------------------------------------
**	DEFINE NAME
**	-------------------------------------------------------
**	文字列系の定義
**
***********************************************************/

# アドコード デフォルト
define("DEFAULT_ADCODE",					"free");

# アドコード 事前予約
define("RESERVED_ADCODE",					"reserved");



/***********************************************************
**
**	LOGIN BONUS
**	-------------------------------------------------------
**	DEFINE NAME
**	-------------------------------------------------------
**	ログインボーナス受け取りの日時設定
**	午前3時を日付変更とする為ここで定義
**
***********************************************************/

# CALCULATION
$bonus_change_time							= 3;
$bonus_datetime								= date("YmdHis",strtotime("-".$bonus_change_time." hour"));
$bonus_date									= date("Ymd",strtotime($bonus_datetime));
$bonus_time									= date("His",strtotime($bonus_datetime));

# BONUS CHANGE TIME
define("BONUS_CHANGE_TIME",					$bonus_change_time);

# BONUS DATE TIME
define("BONUS_DATE_TIME",					$bonus_datetime);

# BONUS DATE
define("BONUS_DATE",						$bonus_date);

# BONUS TIME
define("BONUS_TIME",						$bonus_time);

# ログインボーナス除外時間帯スタート
define("BONUS_OUT_TIME_START",				date("Ymd")."000000");

# ログインボーナス除外時間帯エンド
define("BONUS_OUT_TIME_END",				date("Ymd")."030000");



/***********************************************
**
**	LOGIN PAGE CHECK
**	--------------------------------------------
**	常時ログインが必要ないページを記入
**	--------------------------------------------
**	index
**	enter
**	connection
**	error
**
************************************************/

# LOGIN
$login_through_array	= array();
$login_through_array	= array(
	'index'				=> '1',
	'enter'				=> '1',
	'guide'				=> '1',
	'connection'		=> '1',
	'settlement'		=> '1',
	'error'				=> '1',
	'statuscheck'		=> '1',
	'debug'				=> '1',
);



/***********************************************
**
**	WEB PAGE CHECK
**	--------------------------------------------
**	controllerごとに番号振り
**	--------------------------------------------
**	pageviews の directoryに格納
**
************************************************/

$web_category_array		= array();
$web_category_array		= array(
	'main'				=> '1',
	'character'			=> '2',
	'mail'				=> '3',
	'shop'				=> '4',
	'gacha'				=> '5',
	'album'				=> '6',
	'ranking'			=> '7',
	'presentbox'		=> '8',
	'mypage'			=> '9',
	'news'				=> '10',
	'information'		=> '11',
	'tutorial'			=> '12',
	'campaign'			=> '13',
	'event'				=> '14',
	'exchange'			=> '15',
	'index'				=> '100',
	'enter'				=> '101',
	'item'				=> '102',
	'connection'		=> '103',
	'settlement'		=> '104',
	'guide'				=> '105',
	'error'				=> '200',
	'statuscheck'		=> '200',
	'debug'				=> '999',
	'system'			=> '999',
);

# pageviewsでカウント取らないcontroller
$web_count_out			= array();
$web_count_out			= array(
	'index'				=> 1,
	'enter'				=> 1,
	'item'				=> 1,
	'connection'		=> 1,
	'settlement'		=> 1,
	'guide'				=> 1,
	'error'				=> 1,
	'statuscheck'		=> 1,
	'debug'				=> 1,
	'system'			=> 1,
);

# controller内の$data['page']でカウント取るページ
$access_page_array		= array();
$access_page_array		= array(
	'index'				=> 1,	// 全ページINDEX
	'profile'			=> 2,	// characterController
	'detail'			=> 3,	// mailController
	'list'				=> 4,	// shopController
	'buy'				=> 5,	// shopController
	'start'				=> 6,	// gachaController
	'guide'				=> 7,	// informationController
	'help'				=> 8,	// informationController
	'character'			=> 9,	// rankingController
	'user'				=> 10,	// rankingController
	'send'				=> 11,	// mailController
	'read'				=> 12,	// mailController
);



/***********************************************
**
**	消費ポイント番号配列
**
************************************************/

$point_no_array			= array();
$point_no_array			= array(
	array('0', 'DEFAULT','0'),
	array('1', 'メール開封',			'21','1'),
	array('2', 'メール送信',			'22','1'),
	array('3', '画像閲覧',				'32'),
	array('4', '動画閲覧',				'33'),
	array('5', '新規登録',				'5'),
	array('6', 'ログインボーナス付与',	'6'),
	array('7', 'インセンティブ付与',	'7'),
	array('8', 'メール当選付与',		'8'),
	array('9', 'ガチャ当選付与',		'9'),
	array('10','ポイント購入(コイン)',	'12'),
	array('11','アイテム購入(コイン)',	'13'),
	array('12','ガチャ消費(ポイント)',	'41'),
	array('13','ガチャ消費(コイン)',	'42'),
	array('14','付与好感度ポイント',	'10'),
	array('15','送信ランキングポイント(有料)','11'),
	array('16','送信ランキングポイント(無料)','14'),
	array('17','開封ランキングポイント(有料)','15'),
	array('18','開封ランキングポイント(無料)','16'),
	array('19','ガチャ購入(コイン)',	'43'),
	array('20','鍵交換付与ポイント','17'),
	array('21','付与好感度ポイント(閲覧)','19'),
	//20181015 add by A.cos
	array('22','シーン交換付与ポイント','20'),
	//20180320 add by A.cos
	array('23','限定ガチャ消費(ポイント)',	'45'),
	array('24','限定ガチャ消費(コイン)',	'46'),
	array('25','Sアップガチャ消費(ポイント)',	'47'),
	array('26','Sアップガチャ消費(コイン)',	'48'),
);

$point_name_array			= array();
$point_name_array			= array(
	'read'					=> 1,
	'send'					=> 2,
	'image'					=> 3,
	'movie'					=> 4,
	'regist'				=> 5,
	'login_recv'			=> 6,
	'present_recv'			=> 7,
	'mail_recv'				=> 8,
	'gacha_recv'			=> 9,
	'shop_point'			=> 10,
	'shop_item'				=> 11,
	'gacha_point'			=> 12,
	'gacha_coin'			=> 13,
	'favorite'				=> 14,
	'ranking_send_point'	=> 15,
	'ranking_send_free'		=> 16,
	'ranking_read_point'	=> 17,
	'ranking_read_free'		=> 18,
	'buy_gacha_coin'		=> 19,
	'exchange_key'		=> 20,
	'favorite_read'			=> 21,
	//20181015 add by A.cos
	'exchange_image'		=> 22,
	//20180320 add by A.cos
	'limitted_gacha_point'			=> 23,
	'limitted_gacha_coin'			=> 24,
	'stepup_gacha_point'			=> 25,
	'stepup_gacha_coin'			=> 26,
);



/***********************************************
**
**	プレゼントカテゴリ
**
************************************************/

# PRESENT CATEGORY
$present_category_array	= array();
$present_category_array	= array(
	'login'				=> 1,
	'normal'			=> 2,
	'levelup'			=> 11,
	'continuity'		=> 21,
	'daily'				=> 22,
	'support_rewards_ticket' => 31,
	'support_rewards_item' => 32,
	'support_rewards_picture' => 33,
	'mail_present_ticket' => 41,
	'mail_present_item' => 42,
	'mail_present_picture' => 43,
	'benefits_code_ticket' => 51,
	'benefits_code_item' => 52,
	'benefits_code_picture' => 53,
);



/***********************************************
**
**	ショップカテゴリ
**
************************************************/

# SHOP CATEGORY
$shop_category_array	= array();
$shop_category_array	= array(
	'point'				=> '1',
	'item'				=> '2',
);

# SHOP NAME
$shop_name_array		= array();
$shop_name_array		= array(
	'point'				=> TICKET_NAME,
	'item'				=> 'アイテム',
);



/***********************************************
**
**	ガチャタイプ -> ループ回数
**
************************************************/

# GACHA
$gacha_loop_array		= array();
$gacha_loop_array		= array(
	'free'				=> '1',
	'single'			=> '1',
	'multi'				=> '10',
	'multi_loop'		=> '11',
	//20180313 add by A.cos
	'limitted1'				=> '1',//単発と10連しかなかったときの仕様にあわせてデフォは1としておく
	'limitted2'				=> '1',//2つ以上の限定ガチャを行う場合の予備、プログラムはまだない
	'limitted3'				=> '1',//2つ以上の限定ガチャを行う場合の予備、プログラムはまだない
	'stepup1'				=> '1',//単発と10連しかなかったときの仕様にあわせてデフォは1としておく
	'stepup2'				=> '1',//2つ以上のステップアップガチャを行う場合の予備、プログラムはまだない
	'stepup3'				=> '1'//2つ以上のステップアップガチャを行う場合の予備、プログラムはまだない
);

//20180314 add by A.cos
/***********************************************
**
**	ステップアップガチャタイプ
**
************************************************/

# STEPUP GACHA
$stepup_gacha_number		= array();
$stepup_gacha_number		= array(
	'limitted1'				=> '101',
	'limitted2'				=> '102',//2つ以上の限定ガチャを行う場合の予備、プログラムはまだない
	'limitted3'				=> '103',//2つ以上の限定ガチャを行う場合の予備、プログラムはまだない
	'stepup1'				=> '201',
	'stepup2'				=> '202',//2つ以上のステップアップガチャを行う場合の予備、プログラムはまだない
	'stepup3'				=> '203'//2つ以上のステップアップガチャを行う場合の予備、プログラムはまだない
);


/***********************************************
**
**	WEB画像 FILE TYPE
**
************************************************/

# FILE TYPE CATEGORY
$web_filetype_array		= array();
$web_filetype_array		= array(
	'index'				=> 1,
	'tutorial'			=> 2,
	'main'				=> 3
);



/***********************************************************
**	
**	$ CATEGORY
**	-------------------------------------------------------
**	各テーブルのカテゴリ
**	-------------------------------------------------------
**	基本設定
**	
***********************************************************/

# チケットカテゴリ ( shops ) / タイプ ( bonuses / presentbox )
$number_ticket				= 1;

# アイテムカテゴリ ( shops ) / タイプ ( bonuses / presentbox )
$number_item				= 2;

# 配布画像 タイプ ( bonuses / presentbox )
$number_image				= 3;

# イベントカテゴリ (images)
$event_category				= 98;

# キャンペーンカテゴリ (images)
$campaign_category			= 99;

# attaches キャラ画像サムネイルカテゴリ
$thumbnail_image_category	= 11;

# attaches キャラ画像メインカテゴリ
$main_image_category		= 12;

# attaches キャラ画像おやすみカテゴリ
$sleep_image_category		= 13;

# attaches キャラ画像リストカテゴリ
$list_image_category		= 14;

# attaches キャラ画像プロフィールカテゴリ
$profile_image_category		= 15;

# attaches その他補足情報画像（レベルアップ報酬など）カテゴリ
$profile_other_image_category	= 16;

# BANNER IMAGE CATEGORY
$banner_image_category		= 100;

# WEB IMAGE CATEGORY
$web_image_category			= 101;

# BUTTON IMAGE CATEGORY
$button_image_category		= 102;



/***********************************************************
**	
**	$ THUMBNAIL
**	-------------------------------------------------------
**	サムネイル画像幅
**	-------------------------------------------------------
**	基本設定
**	
***********************************************************/

# サムネイルWIDTH
$thumbnail_width			= 75;


/**************************************************
**
**　Memcached関連
**	----------------------------------------------
**
**************************************************/
define("MEMCACHED_SERVER", "localhost");
define("MEMCACHED_RANKING_PAST_KEY", "ranking_past_");
define("MEMCACHED_RANKING_FRAME_KEY", "ranking_frame");
define("MEMCACHED_CHARACTER_SCHEDULES", "character_schedule");
define("MEMCACHED_EXPIRATION", 600);


/**************************************************
**
**　返信メールに使う文字修飾タグの文字色(管理画面側のpublic_html\CONF\set_config.phpからコピペ)
**	----------------------------------------------
**
**************************************************/
$font_color_array = array(
	"WHITE" => "#EEEEEE",
	"BLACK" => "#222222",
	"RED" => "#E0002A",
	"BLUE" => "#0099FF",
	"GREEN" => "#289F29",
	"ORANGE" => "#FD680D",
	"PURPLE" => "#913093",
	"PINK" => "#FF6699",
	"GRAY" => "#838383",
	"DARKGRAY" => "#545257",
	"NABY" => "#002A5A",
	"GOLD" => "#A37E00",
	"KHAKI" => "#93845B",
	"AQUA" => "#0096A9",
	"INDIGO" => "#0A0A2D",
	"WINE" => "#6B0032"
);


/**************************************************
**
**　マニー投げ銭関連
**	----------------------------------------------
**
**************************************************/
define("MANII_PAY_POINT", 1000);
define("MANII_CONSUMPTION", 1);

/**************************************************
**
**　ガチャポイント関連
**	----------------------------------------------
**
**************************************************/
define("GACHA_SERVICE_POINT", 10);

?>