<?php
################################ FILE MANAGEMENT ################################
##
##	option.config.php
##	=============================================================================
##
##	■PAGE / 
##	ROOT OPTION CONFIG
##	DEFAULT ROOT OPTION SETTING
##
##	=============================================================================
##
##	■MEANS / 
##	ROOT内サイトオプション【変数】設定
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
**	JAPAN AREA SETTING
**	-------------------------------------------------------
**	$area_array	INDEX(TYPE) -> NAME
**	-------------------------------------------------------
**	日本地域設定
**
***********************************************************/

# AREA 初期化
$area_array = NULL;
$area_array = array(
	array('0', '選択して下さい'),
	array('1', '北海道'),
	array('2', '東北'),
	array('3', '関東'),
	array('4', '甲信越'),
	array('5', '東海'),
	array('6', '関西'),
	array('7', '中国'),
	array('8', '四国'),
	array('9', '九州・沖縄'),
	array('10','海外'),
);



/**********************************************************
**
**	JAPAN PREF SETTING
**	-------------------------------------------------------
**	$pref_array	INDEX(TYPE) -> NAME
**	-------------------------------------------------------
**	日本都道府県設定
**
***********************************************************/

# PREF 初期化
$pref_array = NULL;
$pref_array = array(
	array('0', '都道府県なし',''),
	array('1', '北海道',  '1'),
	array('2', '青森県',  '2'),
	array('3', '岩手県',  '2'),
	array('4', '宮城県',  '2'),
	array('5', '秋田県',  '2'),
	array('6', '山形県',  '2'),
	array('7', '福島県',  '2'),
	array('8', '茨城県',  '3'),
	array('9', '栃木県',  '3'),
	array('10','群馬県',  '3'),
	array('11','埼玉県',  '3'),
	array('12','千葉県',  '3'),
	array('13','東京都',  '3'),
	array('14','神奈川県','3'),
	array('15','新潟県',  '4'),
	array('16','富山県',  '4'),
	array('17','石川県',  '4'),
	array('18','福井県',  '4'),
	array('19','山梨県',  '4'),
	array('20','長野県',  '4'),
	array('21','岐阜県',  '5'),
	array('22','静岡県',  '5'),
	array('23','愛知県',  '5'),
	array('24','三重県',  '5'),
	array('25','滋賀県',  '6'),
	array('26','京都府',  '6'),
	array('27','大阪府',  '6'),
	array('28','兵庫県',  '6'),
	array('29','奈良県',  '6'),
	array('30','和歌山県','6'),
	array('31','鳥取県',  '7'),
	array('32','島根県',  '7'),
	array('33','岡山県',  '7'),
	array('34','広島県',  '7'),
	array('35','山口県',  '7'),
	array('36','徳島県',  '8'),
	array('37','香川県',  '8'),
	array('38','愛媛県',  '8'),
	array('39','高知県',  '8'),
	array('40','福岡県',  '9'),
	array('41','佐賀県',  '9'),
	array('42','長崎県',  '9'),
	array('43','熊本県',  '9'),
	array('44','大分県',  '9'),
	array('45','宮崎県',  '9'),
	array('46','鹿児島県','9'),
	array('47','沖縄県',  '9'),
	array('48','海外',    '10'),
);



/***********************************************************
**	
**	SEX
**	-------------------------------------------------------
**
**	-------------------------------------------------------
**	性別
**
***********************************************************/

# PF側の性別を数字に変換
$gender_array		= array();
$gender_array		= array(
	'male'			=> '1',
	'female'		=> '2',
);

# SEX
$sex_array			 = array();
$sex_array			 = array(
	array('0','DEFAULT'),
	array('1','男性'),
	array('2','女性'),
	array('3','不明'),
);


/***********************************************************
**	
**	MOBILE DOMAIN
**	-------------------------------------------------------
**	array -> docomo / softbank / au /	INDEX(NAME)
**	-------------------------------------------------------
**	携帯キャリア設定
**
***********************************************************/

# MOBILE DOMAIN
$mobile_domain_array = array();
$mobile_domain_array = array(
	array('選択して下さい'),
	array('docomo.ne.jp'),
	array('ezweb.ne.jp'),
	array('softbank.ne.jp'),
	array('d.vodafone.ne.jp'),
	array('h.vodafone.ne.jp'),
	array('t.vodafone.ne.jp'),
	array('r.vodafone.ne.jp'),
	array('c.vodafone.ne.jp'),
	array('k.vodafone.ne.jp'),
	array('n.vodafone.ne.jp'),
	array('s.vodafone.ne.jp'),
	array('q.vodafone.ne.jp'),
);



/***********************************************************
**	
**	MAIL ADDRESS CARRIER
**	-------------------------------------------------------
**	INDEX -> STR
**	-------------------------------------------------------
**	キャリア設定
**	
***********************************************************/

# CARRIER
$carrier_array = array();
$carrier_array = array(
	array('0','DEFAULT'),
	array('1','docomo'),
	array('2','ezweb'),
	array('3','softbank'),
	array('9','PC'),
);

# CARRIER
$mail_address_array = array();
$mail_address_array = array(
	array('0','DEFAULT'),
	array('1','docomo.ne.jp','1'),
	array('2','ezweb.ne.jp','2'),
	array('3','softbank.ne.jp','3'),
	array('4','softbank.jp','3'),
	array('5','vodafone.ne.jp','3'),
);

# DEVICE
$device_array = array();
$device_array = array(
	array('0', '関係なく'),
	array('1', 'PC'),
	array('2', 'SMARTPHONE(APP)'),
	array('3', 'MOBILE'),
);

# DEVICE NUMBER
$device_number_array = array();
$device_number_array = array(
	array('0', 'total',		'TOTAL'),
	array('1', 'pc',		'PC'),
	array('2', 'smart',		'SMARTPHONE(APP)'),
	array('3', 'mobile',	'MOBILE'),
);

# OS
$os_array = array();
$os_array = array(
	array('0', '関係なく'),
	array('1', 'iOS'),
	array('2', 'Android'),
);

# これは管理システム側の定義 accessesテーブルにはこっちで入れる
$system_device_array = array(
	array('0','設定なし'),
	array('1','docomo'),
	array('2','ezweb'),
	array('3','softbank'),
	array('4','PC'),
	array('5','iPhone'),
	array('6','PHS'),
	array('7','Android'),
	array('8','イーモバイル'),
	array('9','etc'),
);



/***********************************************************
**	
**	EXECTION STR SETTING
**	-------------------------------------------------------
**	INDEX -> STR
**	-------------------------------------------------------
**	処理後コメント
**	
***********************************************************/

# EXECTION
$exection_array = array();
$exection_array = array(
	array('0','DEFAULT'),
	array('1','追加致しました。'),
	array('2','更新致しました。'),
	array('3','削除致しました。'),
	array('4','処理致しました。'),
	array('5','処理致しました。'),
	array('6','送信致しました。'),
);



/***********************************************************
**	
**	CALENDAR
**	-------------------------------------------------------
**	DAYS ARRAY
**	-------------------------------------------------------
**	JAPANISE / ENGLISH
**	
***********************************************************/

# DAYS ARRAY
$days_array = array();
$days_array = array(
	array('0','Sun','日','Sun'),
	array('1','Mon','月','Mon'),
	array('2','Tue','火','Tue'),
	array('3','Wed','水','Wed'),
	array('4','Thu','木','Thu'),
	array('5','Fri','金','Fri'),
	array('6','Sat','土','Sat'),
);



/***********************************************************
**	
**	FONT
**	-------------------------------------------------------
**	FONT SIZE ARRAY
**	-------------------------------------------------------
**	MOBILE
**	
***********************************************************/

# MOBILE
$font_size_mobile_array = array();
$font_size_mobile_array = array(
	array('0', '0',		'DEFAULT'),
	array('1', '大',	'large'	,	'large',	'large'),
	array('2', '中',	'medium',	'medium',	'medium'),
	array('3', '小',	'xx-small',	'small',	'xx-small'),
);



/***********************************************************
**	
**	$ SETTING
**	-------------------------------------------------------
**	TIME 変数設定
**	-------------------------------------------------------
**	日本時間設定
**	
***********************************************************/

# DATE / TIME
$date						= date("Y-m-d");
$date_time					= date("Y-m-d H:i:s");
$last_week					= date("Y-m-d",strtotime("-1 week"));
$last_time					= date("Y-m-d H:i:s",strtotime("-1 hour"));
$year_date1					= date("Y-m-d H:i:s",strtotime("+1 year"));
$next_monthdate				= date("Y-m-d H:i:s",strtotime("+1 month"));



?>