<?php
//------------------------------------------------------------------------------------
//  ユーザ側から年齢認証用画像アップロードした場合に処理を行うプログラム
//                                                                      10/07/15 nagata
//------------------------------------------------------------------------------------


#################### 設定ファイル読み出し ####################
require_once('/var/www/htdocs/attaches/setting.php');
//require_once('/usr/local/apache2/htdocs/attaches/mimeDecode.php');
//require_once('/usr/local/php/lib/php/Mail/mimeDecode.php');


#################### アップロード設定ファイル読み出し ####################
/** CONF FILE **/
require_once(dirname(__FILE__)."/../CONF/config.php");

/** CLASS FILE **/
require_once(dirname(__FILE__).'/../class/images.php');


#################### 初期化・DB接続 ####################
$Setting = new DataBase;
$Setting->Initialize();
$Setting->DbConnect();



//web側 リダイレクト用
$home_param = "/".$_REQUEST['ad_code']."/".$_REQUEST['sex']."/".$_REQUEST['base64_mo_mail']."/type~s";
$home_param_ok = "/".$_REQUEST['ad_code']."/".$_REQUEST['sex']."/".$_REQUEST['base64_mo_mail']."/type~y";


    if(!empty($_FILES['file']) && $_SERVER["REQUEST_METHOD"] == "POST"){



        $file_type = $_FILES['file']['type'];

        if($file_type != "image/jpeg" && $file_type != "image/pjpeg"){
            print("CHOOSE jpg FILE".$file_type);
            print("<br /><br /><a href = \"".SMART_AGE_DIR.$home_param."\">BACK</a>");
            exit();
        } else {

            $extend = 'jpg';
        }

        $save_dir = HOUSE_AGE.'/';                            // 保存ディレクトリ
        $file_name = date('YmdHis');                          // ファイル名
        $file = $file_name.$_REQUEST['user_id'].'.'.$extend;  // 拡張子
        $sql_file_name = $file;

        //list($width_orig, $height_orig) = getimagesize($_FILES['file']['tmp_name']);
        //if($width_orig > "240") {$size = "240";}


        $up_load = move_uploaded_file($_FILES['file']['tmp_name'], $save_dir.$file);

        if(!$up_load){
            die("COULD NOT UPLOAD FILE");
        }

        $save_file = $save_dir.$file;
        

        if( file_exists( $save_file ) ){
            chmod($save_file,0766);
        }else{
            die("COULD NOT CREATE FILE");
        }
        $SrcStat = stat($save_file);
        $SrcFileSize = ceil($SrcStat[7]/1024);


        //Attachesインサート処理
        $answer = $Setting->GetUserData($_REQUEST['user_id']);
        $category = '3'; //年齢認証画像
        $sql_file_name = $file;

        $Setting->InsertData($_REQUEST['user_id'], $answer, $sql_file_name, $category);

    }else{
        exit;
    }

$Setting->DbClose();

header("Location: http://test-evol.com/promotions/attestation/$home_param_ok");


?>