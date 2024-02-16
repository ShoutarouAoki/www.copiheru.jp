<?php
//------------------------------------------------------------------------------------
//  年齢認証用に画像を添付して送ってきた場合の処理
//                                                                      10/06/22 inoue
//------------------------------------------------------------------------------------

#################### 設定ファイル読み出し ####################
require_once('/var/www/htdocs/attaches/setting.php');
//require_once('/usr/local/apache2/htdocs/attaches/mimeDecode.php');
require_once('/usr/local/php/lib/php/Mail/mimeDecode.php');





#################### 初期化・DB接続 ####################
$Setting = new DataBase;
$Setting->Initialize();
$Setting->DbConnect();





#################### メール解析 ####################
$params['include_bodies'] = true;
$params['decode_bodies']  = true;
$params['decode_headers'] = true;
$params['input']          = file_get_contents("php://stdin");
$params['crlf']           = "\r\n";
$structure = Mail_mimeDecode::decode($params);

//送られたアドレス取得
$send_ad = $structure->headers['to'];
//↑からユーザのID取得
$user_ary = explode('@', $send_ad);
$user_id  = explode('-', $user_ary['0']);
$answer = $Setting->GetUserData($user_id['0']);





#################### 画像保存 ####################
$mail_body = strtolower($structure->ctype_primary);
if ($mail_body == 'multipart') {

    foreach($structure->parts as $part){
        $mail_type = strtolower($part->ctype_primary);

        if ($mail_type == 'image' || $mail_type == 'video') {
            $type = strtolower($part->ctype_secondary);

            // ファイル名
            $file_name = date('YmdHis');

            if ($type == 'jpg' || $type == 'jpeg' || $type == 'gif') {
                if ($type == 'jpeg') {
                    $type = 'jpg';
                }
                $category = '3';                             // 年齢認証画像はここ
                $save_dir = HOUSE_AGE;                       // 保存ディレクトリ
                $file = $file_name.$user_id['0'].'.'.$type;  // 拡張子
                $sql_file_name = $file;                      // SQLにINSERTする用
            } else {
                exit;
            }
            $open_dir = $save_dir.'/'.$file;
            #mail("seraphic.blue@docomo.ne.jp","aa",$open_dir);
            
            // 添付内容をファイルに保存
            $fp = fopen($open_dir, "w" );
            $length = strlen( $part->body );
            fwrite( $fp, $part->body, $length );
            fclose( $fp );
            // 権限与えます
            chmod($open_dir, 0766);
            $file_size = filesize($open_dir);
            if ($file_size > MAX_SIZE) {
                exit;
            }
            $Setting->InsertAgeData($user_id['0'], $answer, $sql_file_name, $category);
        }
    }
// 画像がなかった場合
} else {
    exit;
}
//mail("seraphic.blue@docomo.ne.jp",$mail_type,$type);
$Setting->DbClose();
?>