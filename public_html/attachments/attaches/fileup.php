<?php

//------------------------------------------------------------------------------------
//  ユーザ側からメールに動画像を添付し送信した場合に処理を行うプログラム
//                                                                      10/06/22 inoue
//------------------------------------------------------------------------------------

#################### 設定ファイル読み出し ####################
require_once('/var/www/htdocs/attaches/setting.php');
//require_once('/usr/local/php/lib/php/Mail/mimeDecode.php');
require_once('/usr/share/pear/Mail/mimeDecode.php');


#################### 初期化・DB接続 ####################

#$Setting->Initialize();
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

//送信者のメールアドレスを抽出 
$mail = $structure->headers['from'];
$mail = addslashes($mail);
$mail = str_replace('"','',$mail);

//署名付きの場合の処理を追加 
preg_match("/<.*>/",$mail,$str);
if($str[0]!=""){
    $str=substr($str[0],1,strlen($str[0])-2);
    $mail = $str;
}

//宛先のメールアドレスを抽出 
$to = $structure->headers['to'];
$to = addslashes($to);
$to = str_replace('"','',$to);

//宛先署名付きの場合の処理を追加 
preg_match("/<.*>/",$to,$str);
if($str[0]!=""){
    $str=substr($str[0],1,strlen($str[0])-2);
    $send_ad = $str;
} else {
    //送られたアドレス取得
    $send_ad = $structure->headers['to'];
}


//↑からユーザのID取得
$user_ary = explode('@', $send_ad);
$user_id  = explode('-', $user_ary['0']);

$image_type = $user_id['2']; // 1なら動画像、２なら年齢認証画像
$answer = $Setting->GetUserData($user_id['0']);




#################### 動画像保存・INSERT ####################
$i = 0;
$mail_body = strtolower($structure->ctype_primary);
if ($mail_body == 'multipart') {

    foreach($structure->parts as $part){

        $mail_type = strtolower($part->ctype_primary);

        if ($mail_type == 'text' && $i == 0) {
            $i++;
            continue;
        }

        if ($mail_type == 'image' || $mail_type == 'video') {
            $type = strtolower($part->ctype_secondary);

            // ファイル名
            $file_name = date('YmdHis');

            if ($type == 'jpg' || $type == 'jpeg' || $type == 'gif') {
                if ($type == 'jpeg') {
                    $type = 'jpg';
                }
                if ($image_type == '1'){
                    $category = '1';                         // 画像
                    $save_dir = HOUSE_IMG;                   // 保存ディレクトリ
                } else {
                    $category = '3';                         // 画像
                    $save_dir = HOUSE_AGE;                   // 保存ディレクトリ
                }
                $file = $file_name.$user_id['0'].'.'.$type;  // 拡張子
                $sql_file_name = $file;                      // SQLにINSERTする用
            } elseif ($type == '3gp' || $type == '3g2' || $type == '3gpp' || $type == '3gpp2') {
                if ($image_type == '2'){
                    break;
                }
                $category = '2';
                $save_dir = HOUSE_MOV;
                $file = $file_name.$user_id['0'].'.'.$type;
                $sql_file_name = $file_name.$user_id['0'];
            } else {
                // 動画像なのだが上記拡張子以外の場合
                break;
            }
            $open_dir = $save_dir.'/'.$file;

            // 添付内容をファイルに保存
            $fp = fopen($open_dir, "w" );
            $length = strlen( $part->body );
            fwrite( $fp, $part->body, $length );
            fclose( $fp );
            // 権限与えます
            chmod($open_dir, 0777);
            $file_size = filesize($open_dir);

            if ($file_size > MAX_SIZE) {

                break;
            }
            
            // 指定サイズ外の場合の対処
            if ($category == '1') {

                /*
                $ImageSize = getimagesize($open_dir);
                if ($ImageSize['0'] > '320') { // 横幅
                    $width = '320';
                    $resize = 'on';
                } else {
                    $width = $ImageSize['0'];
                }

                if ($ImageSize['1'] > '240') { // 縦幅
                    $height = '240';
                    $resize = 'on';
                } else {
                    $height = $ImageSize['1'];
                }

                // リサイズ開始
                if ($resize == 'on') {
                    $image = new Imagick($open_dir);
                    $image->resizeImage($width, $height,imagick::FILTER_POINT,1);
                    $image->writeImage($open_dir);
                    $image->destroy();
                }
                */

				$ImageSize = getimagesize($open_dir);
				$width		= $ImageSize['0'];
				$height		= $ImageSize['1'];
				$max_width	= 320;

				# RESIZE MAIN
				if($width > $max_width){
					$new_height = round($height * $max_width / $width);
					$new_width	= $max_width;
				}

				if($new_width){

                    # high quality 下で画質が荒くなりすぎた場合はこっち
				    #exec("/usr/local/bin/mogrify -resize " . $new_width . "x" . $new_height . " -quality 100 -sharpen 0.1 " . $open_dir );
				    #exec(escapeshellcmd("/usr/local/bin/mogrify -resize ".$new_width."x -unsharp 2x1.4+0.5+0 -colors 65 -quality 100 -verbose " .$open_dir));

                    #exec("/usr/bin/mogrify -resize ".$new_width."x".$new_height."! $open_dir");
                    exec("/usr/local/bin/mogrify -resize ".$new_width."x".$new_height."! $open_dir");
	                #@$image = new Imagick($open_dir);
	                #$image->resizeImage($new_width, $new_height,imagick::FILTER_POINT,1);
	                #@$image->resizeImage($new_width, $new_height,imagick::FILTER_UNDEFINED,1);
	                #@$image->writeImage($open_dir);
	                #@$image->destroy();
                    chmod($open_dir, 0777);
				}

				/*
				$tsw	= "mogrify -resize " . $new_width . "x" . $new_height . " -quality 100 -sharpen 0.1 " . $open_dir;
				mail("takai@d-ef.co.jp","TEST",$tsw,"From:info@family-a.jp");
				*/
				# CREATE MAIN
				#exec( "mogrify -resize " . $new_width . "x" . $new_height . " -quality 100 -sharpen 0.1 " . $open_dir );

            }
         	$Setting->InsertData($user_id['0'], $answer, $sql_file_name, $category);
        }
        $i++;
        if ($i != 0) { break; } // 複数添付はさせません
    }
// 動画像がなかった場合
} else {

   # break;

}
$Setting->DbClose();

//mail("seraphic.blue@docomo.ne.jp",$mail_type,$type);

?>