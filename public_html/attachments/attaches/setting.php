<?php
//------------------------------------------------------------------------------------
//  ユーザ側からメールに動画像を添付し送信した場合に処理を行うプログラムの設定ファイル
//                                                                      10/06/22 inoue
//------------------------------------------------------------------------------------

# 接続IP
define('CONNECT_IP', '192.168.0.110');
#define('CONNECT_IP', '192.168.0.14');

# ユーザ名
define('USER_NAME', 'boke');

# パスワード
define('PASSWORD', 'TaoPa1Pa1');

# 使用DB
define('USE_DATABASE', 'win');

# 画像格納ディレクトリ（fullpath）
define('HOUSE_IMG', '/var/www/htdocs/img/attaches');

# 年齢認証用ディレクトリ（fullpath）
define('HOUSE_AGE', '/var/www/htdocs/img/age');

# smartphone用ディレクトリ
define('SMART_AGE_DIR','http://test-evol.com/promotions/smartauth/');

# attachesディレクトリ
define('ATTACHES_DIR','/var/www/htdocs/attaches');

# 動画格納ディレクトリ（fullpath、認証前に置きます
define('HOUSE_MOV', '/var/www/htdocs/movie/not_certify');

# 最高サイズ
define('MAX_SIZE', '5000000000');

# テスト用ディレクトリ
define('HOUSE_TEST', '/var/www/htdocs/attaches/test_dir');

# DB使用関連class
class DataBase {
    var $UseTable;

    function Initialize() {
    # 使用テーブル
    //-----------------------------------------------------------------
    // 書き方（family-aベースです。その都度必要があれば追記して下さい
    // $UseTable = array(
    //     '0' => array(
    //         '0'=>'動画像をINSERTするテーブル',
    //         '1'=>'ユーザのID',
    //         '2'=>'サイトコード',
    //         '3'=>'変換された画像ファイル名',
    //         '4'=>'画像名',
    //         '5'=>'動画像判別フラグ',
    //         '6'=>'公開する判定',
    //         '7'=>'UPLOAD日時',
    //         '8'=>'削除判定'
    //      ),
    //     '1' => array(
    //         '0' => 'ユーザの情報テーブル',
    //         '1' => 'ユーザのID(membersのid)',
    //         '2' => 'サイトコード'
    //     )
    // );
    //-----------------------------------------------------------------
        $this->UseTable = array(
            '0' => array(
                '0' => 'attaches',
                '1' => 'user_id',
                '2' => 'site_cd',
                '3' => 'attached',
                '4' => 'name',
                '5' => 'category',
                '6' => 'use_flg',
                '7' => 'reg_date',
                '8' => 'status'
            ),
            '1' => array(
                '0' => 'members',
                '1' => 'id',
                '2' => 'site_cd',
                '3' => 'ad_code',
                '4' => 'sex',
                '5' => 'mo_mail',
            )
        );
    }

    # 接続
	function DbConnect() {
       $CONNECT = mysql_connect(CONNECT_IP, USER_NAME, PASSWORD) or die(mail("takai@d-ef.co.jp","a","dadada"));
       mysql_select_db(USE_DATABASE, $CONNECT) or die(mail("takai@d-ef.co.jp","a","Mis"));
	}

    # 切断
    function DbClose() {
        mysql_close(mysql_connect(CONNECT_IP, USER_NAME, PASSWORD));
    }

    # ユーザのサイトコード取得
    function GetUserData($UserId) {
        $UserSiteCodeSql  = 'SELECT '.$this->UseTable['1']['2'].' FROM '.$this->UseTable['1']['0'].' WHERE '.$this->UseTable['1']['1'].' = '.$UserId;
        $UserSiteCodeRtn  = mysql_query($UserSiteCodeSql);
        $UserSiteCodeData = mysql_fetch_object($UserSiteCodeRtn);

        $SiteCode = $this->UseTable['1']['2'];
        $UserSiteCode = $UserSiteCodeData->$SiteCode;
        return $UserSiteCode;
    }

    # smartphone members取得
    function GetSmartData($UserId) {
        $UserDataSql  = 'SELECT '.$this->UseTable['1']['3'].','.$this->UseTable['1']['4'].','.$this->UseTable['1']['5'].' FROM '.$this->UseTable['1']['0'].' WHERE '.$this->UseTable['1']['1'].' = '.$UserId;
        $UserDataRtn  = mysql_query($UserDataSql);
        $UserData = mysql_fetch_object($UserDataRtn);

        $ad_code = $this->UseTable['1']['3'];
        $sex     = $this->UseTable['1']['4'];
        $mo_mail = $this->UseTable['1']['5'];

        $MemberData[] = $UserData->$ad_code;
        $MemberData[] = $UserData->$sex;
        $MemberData[] = $UserData->$mo_mail;
        return $MemberData;
    }


    # 動画像データ保存
    function InsertData($UserId, $UserSiteCode, $Attached, $category) {
        $current_timestamp = date('YmdHis');

        $InsDataSql  = "INSERT ".$this->UseTable['0']['0']." SET ";
        $InsDataSql .= $this->UseTable['0']['1']." = ".$UserId.", ";
        $InsDataSql .= $this->UseTable['0']['2']." = ".$UserSiteCode.", ";
        $InsDataSql .= $this->UseTable['0']['3']." = '".$Attached."', ";
        $InsDataSql .= $this->UseTable['0']['5']." = ".$category.", ";
        $InsDataSql .= $this->UseTable['0']['7']." = ".$current_timestamp."";
        $InsDataRtn  = mysql_query($InsDataSql);
    }

    # 年齢認証データ保存
    function InsertAgeData($UserId, $UserSiteCode, $Attached, $category) {
        $current_timestamp = date('YmdHis');

        $InsDataSql  = "INSERT ".$this->UseTable['0']['0']." SET ";
        $InsDataSql .= $this->UseTable['0']['1']." = ".$UserId.", ";
        $InsDataSql .= $this->UseTable['0']['2']." = ".$UserSiteCode.", ";
        $InsDataSql .= $this->UseTable['0']['3']." = '".$Attached."', ";
        $InsDataSql .= $this->UseTable['0']['5']." = ".$category.", ";
        $InsDataSql .= $this->UseTable['0']['7']." = ".$current_timestamp."";
        $InsDataRtn  = mysql_query($InsDataSql);
    }
}

?>