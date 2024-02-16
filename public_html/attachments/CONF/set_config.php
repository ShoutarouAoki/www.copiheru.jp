<?
#######################################################################################
##	
##	FILE ADMIN用 SETUP CONFIG
##	-----------------------------------------------------------------------------------
##	set_config.inc
##	
##	
#######################################################################################



/**********************************************************
**
**	各種定数
**	-------------------------------------------------------
**	DEFINE
**
***********************************************************/
/* 
//こぴへるに以降

define('D_SITE_NAME','きゃばへる');

define('WEB_ROOT','/var/www/htdocs/kyabaheru.net/public_html/attachments/');
define('CLASS_ROOT','/var/www/htdocs/kyabaheru.net/public_html/attachments/class/');
define('CONF_ROOT','/var/www/htdocs/kyabaheru.net/public_html/attachments/CONF/');
define('ADMIN_HTTP','https://kyabaherumanage.higatest.com/');
define('FILE_HTTP','https://kyabaheru.higatest.com/attachments/');
define('IMAGE_HTTP','https://kyabaheru.higatest.com/attachments/img/');
define('MOVIE_HTTP','https://kyabaheru.higatest.com/attachments/movie/');

define('MASTER_HTTP','https://kyabaherumanage.higatest.com/');

define('TEST_FILE_HTTP',	'https://kyabaheru.higatest.com/attachments/');
define('TEST_IMAGE_HTTP',	'https://kyabaheru.higatest.com/attachments/img/');
define('TEST_MOVIE_HTTP',	'https://kyabaheru.higatest.com/attachments/movie/');

//
*/

//こぴへる版
define('D_SITE_NAME','こぴへる');

define('WEB_ROOT','/var/www/htdocs/www.copiheru.jp/public_html/attachments/');
define('CLASS_ROOT','/var/www/htdocs/www.copiheru.jp/public_html/attachments/class/');
define('CONF_ROOT','/var/www/htdocs/www.copiheru.jp/public_html/attachments/CONF/');
define('ADMIN_HTTP','https://copiheru-manage.higatest.com/');
define('FILE_HTTP','https://copiheru.higatest.com/attachments/');
define('IMAGE_HTTP','https://copiheru.higatest.com/attachments/img/');
define('MOVIE_HTTP','https://copiheru.higatest.com/attachments/movie/');

define('MASTER_HTTP','https://copiheru-manage.higatest.com/');

define('TEST_FILE_HTTP',	'https://copiheru.higatest.com/attachments/');
define('TEST_IMAGE_HTTP',	'https://copiheru.higatest.com/attachments/img/');
define('TEST_MOVIE_HTTP',	'https://copiheru.higatest.com/attachments/movie/');

/**********************************************************
**
**	IP関連
**	-------------------------------------------------------
**	
**
***********************************************************/


# SYSTEM GLOBAL IP
$system_ip_array		= array(
	'221.184.239.9',
	'219.111.12.197',
	'122.219.108.156',
	'153.142.13.6',
	'153.156.232.172',
);


# SYSTEM GLOBAL IP
$check_system_ip	= NULL;
$global_ip_count	= count($system_ip_array);
for($system_count=0;$system_count<$global_ip_count;$system_count++){
	if($system_ip_array[$system_count] == $_SERVER['REMOTE_ADDR']){
		$check_system_ip	= $system_ip_array[$system_count];
		break;
	}
}

if(!empty($check_system_ip)){
	define('SYSTEM_IP',		$check_system_ip);
}else{
	define('SYSTEM_IP',		'221.184.239.9');
}



/**********************************************************
**
**	文字コード指定
**	-------------------------------------------------------
**	
**
***********************************************************/

mb_internal_encoding("utf-8");
mb_http_input("auto");
mb_http_output("SJIS");


/**********************************************************
**
**	画像のMAX SIZE / MAX WIDTH
**	-------------------------------------------------------
**	
**
***********************************************************/

define('MAX_IMAGE_SIZE','3000000');
define('MAX_IMAGE_WIDTH','680');
define('MAX_ICON_SIZE','500000');
define('MAX_ICON_WIDTH','150');


/**********************************************************
**
**	管理画面認証パラメータ
**	-------------------------------------------------------
**	$sec_data
**	$form_sec_data
**
***********************************************************/

$sec_data       = "op_id=".$_REQUEST["op_id"]."&sec=".$_REQUEST["sec"]."&site_cd=".$_REQUEST["site_cd"]."&staff_id=".$_REQUEST['staff_id'];
$form_sec_data  = "<input type=\"hidden\" name=\"op_id\" value=\"".$_REQUEST["op_id"]."\" />\n";
$form_sec_data .= "<input type=\"hidden\" name=\"sec\" value=\"".$_REQUEST["sec"]."\" />\n";
$form_sec_data .= "<input type=\"hidden\" name=\"site_cd\" value=\"".$_REQUEST["site_cd"]."\" />\n";
$form_sec_data .= "<input type=\"hidden\" name=\"staff_id\" value=\"".$_REQUEST["staff_id"]."\" />\n";


/**********************************************************
**
**	画像設定
**	-------------------------------------------------------
**	INDEX  -> CATEGORY -> IMAGE SIZE -> IMAGE WIDTH -> NAME -> FILE
**	-------------------------------------------------------
**	INDEX		-> 配列INDEX
**	CATEGORY	-> filesetsに入るcategory値
**	IMAGE SIZE	-> 画像容量最大値
**	IMAGE WIDTH	-> 画像横最大値最大値
**	NAME		-> ネーム
**	FILE		-> 画像格納ディレクトリ名
**	:::::::::::::::::::::::::::::::::::::::::::::::::::::::
**
**	ここは固定
**	リトライやキャンペーンで使用する画像設定情報は上記に記載
**
***********************************************************/

$image_setting_type = array(
	array('0', '0', '0', '0', '', '', '0'),
	array('1', '101', '0', '1', 'エンターページ背景(スマフォ)',		'web', '0', '2'),
	array('2', '101', '0', '2', 'チュートリアル(PC/スマフォ)',		'web', '0', '2'),
	array('3', '101', '0', '3', 'メインページ画像(スマフォ)',		'web', '0', '2'),
);


# ITEM
# INDEX -> CATEGORY -> FIXED -> FILETYPE -> NAME -> IMAGE DIR -> SEX -> TEXTAREA ROWS -> WIDTH 上書き
$item_file_type = array(
	array('0', '0', '0', '0', '', '', '0'),
	array('1', '70','0', '3', 'アイテム', 		'item', '0', '2'),
	array('2', '71','0', '3', 'チケット', 		'item', '0', '2'),
	array('3', '72','0', '3', 'ショップ販売用', 'item', '0', '2'),
	array('4', '73','0', '3', 'ガチャ排出用',	'item', '0', '2'),
	array('5', '74','0', '3', 'ログイン配布用',	'item', '0', '2'),
	array('6', '75','0', '3', '通常配布用',		'item', '0', '2','900','200'),
);

# バナーカテゴリ
# INDEX -> CATEGORY -> FIXED -> FILE TYPE -> NAME -> IMAGE DIR -> SEX -> TEXTAREA ROWS
$banner_file_type = array(
	array('0', '0', '0', '0', '', '', '0'),
	array('1', '100','0', '4', 'TOPページバナー',				'banner', '0', '2'),
	array('2', '100','0', '5', 'ガチャページTOPバナー',			'banner', '0', '2'),
	array('3', '102','0', '6', 'ガチャページボタンバナー', 		'banner', '0', '2'),
	array('4', '100','0', '7', 'ショップページTOPバナー',		'banner', '0', '2'),
	array('5', '102','0', '8', 'ショップページボタンバナー', 	'banner', '0', '2'),
	array('6', '100','0', '9', 'ランキング一覧バナー',			'banner', '0', '2'),
	array('7', '100','0', '10', '特典コードページバナー',			'banner', '0', '2'),
	array('8', '100','0', '11', 'セレクトページバナー',			'banner', '0', '2'),
);


/**********************************************************
**
**	各種設定ファイルの定義付け
**	-------------------------------------------------------
**	INDEX -> CATEGORY -> FIXED -> IMAGE -> NAME
**	-------------------------------------------------------
**	INDEX		-> 配列INDEX
**	CATEGORY	-> filesetsに入るcategory値
**	FIXED		-> [1]だったらファイルを追加できるタイプ
**	IMAGE		-> [0]じゃなかったらimagesに画像を登録できるタイプ(1=mail,2=html)
**	NAME		-> ネーム
**	FILE		-> 画像格納ディレクトリ名
**
***********************************************************/

# お知らせメール用ファイルカテゴリ / FILE TYPE = 1
$mail_file_type = array(
	array('0', '0', '0', '0', '', ''),
	array('1', '1', '0', '0', 'キャンセル', ''),
	array('2', '2', '0', '0', 'アドレス変更', ''),
	array('3', '3', '0', '0', '入金関係', ''),
	array('4', '4', '1', '1', 'INFO同報', 'mail'),
	array('5', '5', '1', '0', 'RETRY', ''),
	array('6', '6', '1', '0', 'CSV RETRY', ''),
	array('7', '7', '1', '1', 'HTML RETRY', 'mail'),
	array('8', '8', '1', '0', 'MAX', ''),
	array('9', '9', '1', '0', 'INFO MAX', ''),
	array('10','10','1', '0', '督促', ''),
	#array('11','11','1', '0', '新規自動初日', ''),
	#array('12','12','1', '0', '新規自動2日～5日', ''),
	array('13','13','1', '0', 'INFO自動初日', ''),
	array('14','14','1', '0', 'INFO自動２日目以降', ''),
	array('15','99','1', '0', 'キャンペーン', ''),
	array('16','15','1', '0', 'POINT配布', ''),
);

# HTML文言用ファイルカテゴリ / FILE TYPE = 2
# INDEX -> CATEGORY -> FIXED -> IMAGE -> NAME -> IMAGE DIR -> SEX -> TEXTAREA ROWS
$html_file_type = array(
	array('0', '0', '0', '0', '', '', '0'),
	array('1', '1', '0', '0', 'マーキー', '', '1', '2'),
	array('2', '1', '0', '0', 'マーキー', '', '2', '2'),
	array('3', '2', '0', '0', '利用規約', '', '1', '15'),
	array('4', '2', '0', '0', '利用規約', '', '2', '15'),
	array('5', '3', '0', '0', '特定商取引法に基づく表記', '', '1', '15'),
	array('6', '3', '0', '0', '特定商取引法に基づく表記', '', '2', '15'),
	array('7', '4', '0', '0', 'よくある質問', '', '1', '15'),
	array('8', '4', '0', '0', 'よくある質問', '', '2', '15'),
	array('9', '5', '0', '0', 'サイトポリシー', '', '1', '15'),
	array('10','5', '0', '0', 'サイトポリシー', '', '2', '15'),
	array('11','99','1', '2', 'キャンペーン', 'campaign', '0', '30'),
	array('12','98','1', '2', 'イベント', 'event', '0', '30'),
	array('13','10','1', '2', 'お知らせ', 	'web', '0', '30'),
	array('14','11','1', '2', '遊び方ガイド', 'web', '0', '30'),
);




?>
