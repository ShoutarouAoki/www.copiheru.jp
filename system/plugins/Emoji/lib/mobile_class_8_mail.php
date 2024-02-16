<?php

###############################################################################
# 携帯絵文字変換ﾗｲﾌﾞﾗﾘ 2008(ﾒｰﾙ処理ｸﾗｽﾗｲﾌﾞﾗﾘ)
# Potora/inaken(C) 2008.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# 2008.10.07 v.1.00.00 新規
# 2008.10.20 v.2.00.00 SMTP接続ﾒｰﾙ送信機能追加
# 2008.11.28 v.2.00.01 ｸﾞﾛｰﾊﾞﾙ変数扱い変更
# 2009.03.11 v.2.00.02 MIME取得関数不具合修正
###############################################################################

###############################################################################
# 絵文字ﾒｰﾙ処理ｸﾗｽ ############################################################
###############################################################################
class emoji_mail {
  # ﾊﾞｰｼﾞｮﾝ設定
  var $ver = 'mail_v.1.00.00';

  var $html_mail_flag;   # PC宛HTMLﾒｰﾙ送信設定
  var $cont_trs_enc;     # ﾒｰﾙ送信ｴﾝｺｰﾄﾞ設定

  # ﾌｧｲﾙMIME指定
  var $FILETYPE = array(
    'txt'  => 'text/plain',
    'htm'  => 'text/html',
    'html' => 'text/html',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'png'  => 'image/png',
    'bmp'  => 'image/x-bmp',
    'ai'   => 'application/postscript',
    'psd'  => 'image/x-photoshop',
    'eps'  => 'application/postscript',
    'pdf'  => 'application/pdf',
    'swf'  => 'application/x-shockwave-flash',
    'lzh'  => 'application/x-lha-compressed',
    'zip'  => 'application/x-zip-compressed',
    'sit'  => 'application/x-stuffit',
  );

  # ｺﾝｽﾄﾗｸﾀ ///////////////////////////////////////////////////////////////////
  function emoji_mail() {
    global $decome_obj,$smtp_obj;

    # 値設定
    if (!defined('EMOJI_smtp_flag')) { define('EMOJI_smtp_flag','0'); }
    # ﾃﾞｺﾒﾗｲﾌﾞﾗﾘ対応
    $decome_obj = '';
    if (file_exists(dirname(__FILE__).'/decome_class.php')) {
      include_once(dirname(__FILE__).'/decome_class.php');
      $decome_obj = new decome();
    }
    # SMTPﾗｲﾌﾞﾗﾘ読込み
    $smtp_obj = '';
    if (EMOJI_smtp_flag == 1) {
      if (file_exists(dirname(__FILE__).'/smtp_class.php')) {
        include_once(dirname(__FILE__).'/smtp_class.php');
        $smtp_obj = new smtp();
        # SMTP接続先設定
        $smtp_obj->this_server = '';
        $smtp_obj->smtp_server = '';
        $smtp_obj->smtp_port   = 25;
        $smtp_obj->pop3_server = '';
        $smtp_obj->pop3_port   = 110;
        $smtp_obj->mail_user   = '';
        $smtp_obj->mail_pass   = '';
        $smtp_obj->auth        = True;
        $smtp_obj->auth_type   = 'POP';
        if (defined('EMOJI_this_server')) { $smtp_obj->this_server = EMOJI_this_server; }
        if (defined('EMOJI_smtp_server')) { $smtp_obj->smtp_server = EMOJI_smtp_server; }
        if (defined('EMOJI_smtp_port'))   { $smtp_obj->smtp_port   = EMOJI_smtp_port; }
        if (defined('EMOJI_pop3_server')) { $smtp_obj->pop3_server = EMOJI_pop3_server; }
        if (defined('EMOJI_pop3_port'))   { $smtp_obj->pop3_port   = EMOJI_pop3_port; }
        if (defined('EMOJI_mail_user'))   { $smtp_obj->mail_user   = EMOJI_mail_user; }
        if (defined('EMOJI_mail_pass'))   { $smtp_obj->mail_pass   = EMOJI_mail_pass; }
        if (defined('EMOJI_auth'))        { $smtp_obj->auth        = EMOJI_auth; }
        if (defined('EMOJI_auth_tyle'))   { $smtp_obj->auth_tyle   = EMOJI_auth_tyle; }
      }
    }
  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ取得 ///////////////////////////////////////////////
  # ｷｬﾘｱ判別と機種情報を取得します。(新処理->推奨)
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　$this->ver : ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ
  #////////////////////////////////////////////////////////////////////////////
  function Get_Emj_Version() {
    return $this->ver;
  }

  # ﾒｰﾙｱﾄﾞﾚｽｷｬﾘｱ解析 //////////////////////////////////////////////////////////
  # ﾒｰﾙｱﾄﾞﾚｽよりｷｬﾘｱ情報を取得します
  # [引渡し値]
  # 　$mail_address : ﾒｰﾙｱﾄﾞﾚｽ
  # [返り値]
  # 　$career : ｷｬﾘｱ判別結果(DoCoMo,au,SoftBank or Vodafone)
  #////////////////////////////////////////////////////////////////////////////
  function get_mail_career($mail_address) {
    global $emoji_obj;

    $career = '';
    if (preg_match('/^(.+?)\@(.*)docomo(.+)$/',$mail_address)) {
      # DoCoMo携帯
      $career = 'DoCoMo';
    } elseif (preg_match('/^(.+?)\@(.*)vodafone(.+)$/',$mail_address) or preg_match('/^(.+?)\@softbank(.+)$/',$mail_address)) {
      # SoftBank(Vodafone)携帯
      $career = $emoji_obj->softbank_name;
    } elseif (preg_match('/^(.+?)\@(.*)disney(.+)$/',$mail_address) or preg_match('/^(.+?)\@i.softbank(.+)$/',$mail_address)) {
      # SoftBank(Vodafone)携帯
      $career = $emoji_obj->softbank_name;
    } elseif (preg_match('/^(.+?)\@(.*)ezweb(.+)$/',$mail_address)) {
      # au携帯
      $career = 'au';
    } else {
      # その他
      $career = 'PC';
    }
    return $career;
  }

  # ﾒｰﾙ送信(mail関数送信) /////////////////////////////////////////////////////
  # ﾒｰﾙ送信関数 emoji_send_mail のｴｲﾘｱｽです。(旧ﾊﾞｰｼﾞｮﾝとの互換性保持のため)
  # [引渡し値]
  # 　$to_name                   : 送信先名
  # 　$to_add                    : 送信先ﾒｰﾙｱﾄﾞﾚｽ
  # 　$from_name                 : 送信元名
  # 　$from_add                  : 送信元ﾒｰﾙｱﾄﾞﾚｽ
  # 　$repry_name                : 返信先名
  # 　$repry_to                  : 返信先ﾒｰﾙｱﾄﾞﾚｽ
  # 　$return_path               : 不達ﾒｰﾙ送信先ｱﾄﾞﾚｽ
  # 　$subject                   : 件名
  # 　$body                      : 本文
  # 　$to_career                 : 送信先ｷｬﾘｱ
  # 　$html_flag                 : HTMLﾒｰﾙﾌﾗｸﾞ
  # 　$content_transfer_encoding : ﾒｰﾙｴﾝｺｰﾃﾞｨﾝｸﾞ指定
  # 　$upfile                    : 添付ﾌｧｲﾙ保存ﾊﾟｽ
  # 　$file_name                 : 添付ﾌｧｲﾙ名
  # [返り値]
  # 　True : 送信成功、False : 送信失敗
  #////////////////////////////////////////////////////////////////////////////
  function emoji_send_mail2($to_name,$to_add,$from_name,$from_add,$repry_name,$repry_to,$return_path,$subject,$body,$to_career='DoCoMo',$html_flag='0',$content_transfer_encoding='',$upfile='',$file_name='') {
    # ﾒｰﾙ送信関数呼出
    $flag = $this->emoji_send_mail($to_name,$to_add,$from_name,$from_add,$subject,$body,$repry_name,$repry_to,$return_path,$html_flag,$content_transfer_encoding,'JIS',$upfile,$file_name);
    return $flag;
  }

  # 絵文字ﾒｰﾙ送信(mail関数送信) ///////////////////////////////////////////////
  # 絵文字ﾒｰﾙを送信します。
  # [引渡し値]
  # 　$to_name                   : 送信先名
  # 　$to_add                    : 送信先ﾒｰﾙｱﾄﾞﾚｽ
  # 　$from_name                 : 送信元名
  # 　$from_add                  : 送信元ﾒｰﾙｱﾄﾞﾚｽ
  # 　$subject                   : 件名
  # 　$body                      : 本文
  # 　$repry_name                : 返信先名(指定無い場合は送信元名)
  # 　$repry_to                  : 返信先ﾒｰﾙｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$return_path               : 不達ﾒｰﾙ送信先ｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$html_flag                 : HTMLﾒｰﾙﾌﾗｸﾞ(指定なし又は'0':ﾃｷｽﾄﾒｰﾙ、'1':HTMLﾒｰﾙ、'2':HTMLﾒｰﾙ(ｲﾝﾅｰ画像-ﾃﾞｺﾒﾀｲﾌﾟ))
  # 　$content_transfer_encoding : ﾒｰﾙｴﾝｺｰﾃﾞｨﾝｸﾞ指定(指定なし又は'7bit':ﾃﾞﾌｫﾙﾄ又は7bit、'base64':base64)
  # 　$mail_code                 : ﾒｰﾙ本文文字ｺｰﾄﾞ指定(指定なし又は'JIS':JIS)
  # 　$upfile                    : 添付ﾌｧｲﾙ保存ﾊﾟｽ
  # 　$file_name                 : 添付ﾌｧｲﾙ名
  # 　$encode_pass               : ｴﾝｺｰﾄﾞ処理無効化('1')
  # 　$input_code                : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　True : 送信成功、False : 送信失敗
  #////////////////////////////////////////////////////////////////////////////
  function emoji_send_mail($to_name,$to_add,$from_name,$from_add,$subject,$body,$repry_name='',$repry_to='',$return_path='',$html_flag='0',$content_transfer_encoding='',$mail_code='JIS',$upfile='',$file_name='',$encode_pass='',$input_code='') {
    global $emoji_obj,$emoji_sub_obj,$smtp_obj;

    # 送信先、送信元ﾁｪｯｸ
    if (($to_add == '') or ($from_add == '')) { return False; }
    # 返信先名ﾁｪｯｸ
    if ($repry_name == '')  { $repry_name  = $from_name; }
    # 返信先名ﾁｪｯｸ
    if ($repry_to == '')    { $repry_to    = $from_add; }
    # 不達ﾒｰﾙ戻り先ﾁｪｯｸ
    if ($return_path == '') { $return_path = $from_add; }
    # 送信ｴﾝｺｰﾄﾞ設定
    if ($content_transfer_encoding == '') {
      if ($emoji_obj->cont_trs_enc) {
        $content_transfer_encoding = $emoji_obj->cont_trs_enc;
      } else {
        $content_transfer_encoding = '7bit';
      }
    }
    # 送信先ｷｬﾘｱ取得
    $to_career = $this->get_mail_career($to_add);

    # 送信先(To句)生成
    $set_to = '';
    if ($to_name != '') {
      # 送信者名の指定がある場合
      $str_code = mb_detect_encoding($to_name,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if ($str_code == 'JIS') {
        $set_to  = $to_name;
      } else {
        $set_to  = @mb_convert_encoding($to_name,'JIS',$str_code);
      }
      $set_to  = mb_convert_kana($set_to,'KV','JIS');
      $set_to  = mb_encode_mimeheader($set_to,'JIS');
      $set_to .= ' <'.$to_add.'>';
    } else {
      # 送信者名の指定が無い場合
      $set_to = $to_add;
    }
    # 送信元(From句)生成
    $set_form = '';
    if ($from_name != '') {
      $str_code = mb_detect_encoding($from_name,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if ($str_code == 'JIS') {
        $set_form = $from_name;
      } else {
        $set_form = @mb_convert_encoding($from_name,'JIS',$str_code);
      }
      $set_form  = mb_convert_kana($set_form,'KV','JIS');
      $set_form  = mb_encode_mimeheader($set_form,'JIS');
      $set_form .= ' <'.$from_add.'>';
    } else {
      $set_form = $from_add;
    }
    # 返信先(Repry_to句)生成
    $set_repry_to = '';
    if ($repry_name != '') {
      $str_code = mb_detect_encoding($repry_name,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if ($str_code == 'JIS') {
        $set_repry_to  = $repry_name;
      } else {
        $set_repry_to  = @mb_convert_encoding($repry_name,'JIS',$str_code);
      }
      $set_repry_to  = mb_convert_kana($set_repry_to,'KV','JIS');
      $set_repry_to  = mb_encode_mimeheader($set_repry_to,'JIS');
      $set_repry_to .= " <".$repry_to.">";
    } else {
      $set_repry_to = $repry_to;
    }
    # ﾒｰﾙ送信用絵文字変換(ｴﾝｺｰﾄﾞ)
    if ($encode_pass != '1') {
      $subject = $emoji_obj->emj_encode($subject,'','',$input_code);
      $body    = $emoji_obj->emj_encode($body,'','',$input_code);
    }
    # 文字ｺｰﾄﾞ取得
    $subject_code = mb_detect_encoding($subject,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    $body_code    = mb_detect_encoding($body,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    # 文字ｺｰﾄﾞ変換
    if ($subject_code != $mail_code) { $subject = @mb_convert_encoding($subject,$mail_code,$subject_code); }
    if ($body_code    != $mail_code) { $body    = @mb_convert_encoding($body,$mail_code,$subject_code); }
    # ｶﾀｶﾅ変換
    $subject = mb_convert_kana($subject,'KV',$mail_code);
    $body    = mb_convert_kana($body,'KV',$mail_code);

    # 件名処理
    # 絵文字変換(ﾃﾞｺｰﾄﾞ)
    $SUBJECT = $emoji_obj->emj_decode($subject,$to_career,$mail_code);
    $subject = $SUBJECT['mail'];
    # 件名処理
    if ($subject == '') { $subject = @mb_convert_encoding('無題','JIS','SJIS'); }
    $subject = base64_encode($subject);
    $subject = '=?ISO-2022-JP?B?'.$subject.'?=';

    # 本文処理
    # 本文絵文字認識
    $EMJ_COUNT = $emoji_sub_obj->emj_check($body,'1');
    if ($EMJ_COUNT['total'] > 0) { $body_emj_flag = True; } else { $body_emj_flag = False; }
    # ﾒｰﾙ送信用絵文字変換(ﾃﾞｺｰﾄﾞ)
    $BODY = $emoji_obj->emj_decode($body,$to_career,$mail_code,$html_flag);
    $body = $BODY['mail'];
    if ((preg_match('/^pc$/i',$to_career) or preg_match('/^'.$emoji_obj->softbank_name.'$/i',$to_career)) and (($body_emj_flag == True) or ($html_flag == '1'))) {
      # HTMLﾀｸﾞ有無ﾁｪｯｸ
      $tag_flag = False;
      if ($body != strip_tags($body)) { $tag_flag = True; }
      # 本文HTML化処理
      $body = preg_replace('/\r/','',$body);
      if ($tag_flag == False) { $body = preg_replace('/\n/',"<br>\n",$body); }
    }
    # Base64ﾃﾞｺｰﾄﾞ
    if ($content_transfer_encoding == 'base64') { $body = base64_encode($body); }

    # ﾍｯﾀﾞｰ、本文処理
    $msg  = '';
    $add_mail_header  = '';
    $add_mail_header .= "From: ".$set_form."\r\n";
    $add_mail_header .= "Reply-To: ".$set_repry_to."\r\n";
    $add_mail_header .= "MIME-Version: 1.0\r\n";

    # 添付ﾌｧｲﾙﾁｪｯｸ
    $upfile_type = '';
    $tail        = '';
    $upfile_flag = 0;
    if (file_exists($upfile)) {
      if ($fp = @fopen($upfile,"r")) {
        @fclose($fp);
        if (preg_match('/.gif$/i',$upfile)) {
          $upfile_type = 'image/gif';
          $tail        = '.gif';
        } elseif (preg_match('/.jpe*g$/i',$upfile)) {
          $upfile_type = 'image/jpeg';
          $tail        = '.jpg';
        } elseif (preg_match('/.png$/i',$upfile)) {
          $upfile_type = 'image/png';
          $tail        = '.png';
        }
        $FDT = split('/',$upfile);
        $upfile_name = $FDT[count($FDT) - 1];
        $upfile_flag = 1;
      }
    }

    if ($upfile_flag == 1) {
      # 添付ﾌｧｲﾙ有る場合
      # ﾊﾞｳﾝﾀﾞﾘｰ文字(ﾊﾟｰﾄの境界)
      $boundary = md5(uniqid(rand()));
      # ﾍｯﾀﾞｰ設定
      $header .= "Content-Type: multipart/mixed;\n";
      $header .= "\tboundary=\"".$boundary."\"\n";
      # 本文生成
      $msg .= "This is a multi-part message in MIME format.\n\n";
      $msg .= "--".$boundary."\n";
    }
    $ht = '';
    if ((preg_match('/^pc$/i',$to_career) or preg_match('/^'.$emoji_obj->softbank_name.'$/i',$to_career)) and (($body_emj_flag == True) or ($html_flag == '1'))) {
      # HTMLﾒｰﾙの場合
      $ht .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    } else {
      # ﾃｷｽﾄﾒｰﾙの場合
      $ht .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    }
    $ht .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
    if ($upfile_flag == 1) {
      # 添付ﾌｧｲﾙ有る場合
      $msg .= $ht;
      $msg .= $body;
      # ﾌｧｲﾙ読込み
      $fp = fopen($upfile,"r");
      $fdata = fread($fp, filesize($upfile));
      fclose($fp);
      # ﾌｧｲﾙ名設定
      if ($file_name) { $upfile_name = $file_name.$tail; }
      # ｴﾝｺｰﾄﾞして分割
      $f_encoded = chunk_split(base64_encode($fdata));
      $msg .= "\n\n--".$boundary."\n";
      $msg .= "Content-Type: ".$upfile_type.";\n";
      $msg .= "\tname=\"".$upfile_name."\"\n";
      $msg .= "Content-Transfer-Encoding: base64\n";
      $msg .= "Content-Disposition: attachment;\n";
      $msg .= "\tfilename=\"".$upfile_name."\"\n\n";
      $msg .= $f_encoded."\n";
      $msg .= "--".$boundary."--";
      $body = $msg;
    } else {
      # 添付ﾌｧｲﾙ無い場合
      $add_mail_header .= $ht;
    }

    if ((EMOJI_smtp_flag == 1) and is_object($smtp_obj)) {
      # SMTP送信
      # 送信内容設定
      $smtp_obj->TOLIST[$to_name] = $to_add;
      $smtp_obj->from_name        = $from_name;
      $smtp_obj->from_address     = $from_add;
      $smtp_obj->reply_to_name    = $repry_name;
      $smtp_obj->reply_to_address = $repry_to;
      $smtp_obj->return_path      = $return_path;
      $smtp_obj->add_header       = '';
      $smtp_obj->subject          = $subject;
      $smtp_obj->body             = $body;
      # ﾒｰﾙ送信
      $success = $smtp_obj->smtp_mail();
    } else {
      # PHP mail関数送信
      $success = @mail($set_to,$subject,$body,$add_mail_header,'-f'.$return_path);
    }
    if ($success) { return True; } else { return False; }

  }

  # 絵文字ﾒｰﾙ送信3(mail関数送信) //////////////////////////////////////////////
  # 絵文字ﾒｰﾙを送信します。
  # [引渡し値]
  # 　$TODATA[*****]             : ｷｰ名:送信先ﾒｰﾙｱﾄﾞﾚｽ、要素(値):送信先名
  # 　$CCDATA[*****]             : ｷｰ名:送信先(ｶｰﾎﾞﾝｺﾋﾟｰ)ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ、要素(値):送信先(ｶｰﾎﾞﾝｺﾋﾟｰ)名
  # 　$BCCDATA[*****]            : ｷｰ名:同報先ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ、要素(値):同報先名
  # 　$from_name                 : 送信元名
  # 　$from_add                  : 送信元ﾒｰﾙｱﾄﾞﾚｽ
  # 　$subject                   : 件名
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$repry_name                : 返信先名(指定無い場合は送信元名)
  # 　$repry_to                  : 返信先ﾒｰﾙｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$return_path               : 不達ﾒｰﾙ送信先ｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$to_career                 : 送信先ｷｬﾘｱ(指定なし:PC及び全ｷｬﾘｱ、'DoCoMo':DoCOMo、'au':au、'SoftBank':SoftBank)
  # 　$content_transfer_encoding : ﾒｰﾙｴﾝｺｰﾃﾞｨﾝｸﾞ指定(指定なし又は'7bit':ﾃﾞﾌｫﾙﾄ又は7bit、'base64':base64)
  # 　$mail_code                 : ﾒｰﾙ本文文字ｺｰﾄﾞ指定(指定なし又は'JIS':JIS)
  # 　$UPFILE[*****]             : ｷｰ名:添付ﾌｧｲﾙﾊﾟｽ、要素(値):添付ﾌｧｲﾙ名
  # 　$encode_pass               : ｴﾝｺｰﾄﾞ処理無効化('1')
  # 　$input_code                : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　True : 送信成功、False : 送信失敗
  #////////////////////////////////////////////////////////////////////////////
  function emoji_send_mail3($TODATA,$CCDATA,$BCCDATA,$from_name,$from_add,$subject,$body_plain,$body_html,$repry_name='',$repry_to='',$return_path='',$to_career='',$content_transfer_encoding='',$mail_code='JIS',$UPFILE='',$encode_pass='',$input_code='') {
    global $emoji_obj,$emoji_sub_obj,$smtp_obj;

    # 送信先ﾁｪｯｸ
    $to_flag  = False;
    $cc_flag  = False;
    $bcc_flag = False;
    $flag     = False;
    if (isset($TODATA)) {
      if (is_array($TODATA)) {
        if (isset($TODATA)) { $flag = True; $to_flag = True; }
      }
    }
    # 送信先ﾁｪｯｸ
    if (isset($CCDATA)) {
      if (is_array($CCDATA)) {
        if (isset($CCDATA)) { $flag = True; $cc_flag = True; }
      }
    }
    # 同報送信ﾁｪｯｸ
    if (isset($BCCDATA)) {
      if (is_array($BCCDATA)) {
        if (isset($BCCDATA)) { $flag = True; $bcc_flag = True; }
      }
    }
    if ($flag == False) { return False; }
    # 送信元ﾁｪｯｸ
    if (!isset($from_add))  { return False; }
    if (!isset($from_name)) { $from_name = ''; }
    # 返信先名ﾁｪｯｸ
    if ($repry_name == '')  { $repry_name  = $from_name; }
    # 返信先名ﾁｪｯｸ
    if ($repry_to == '')    { $repry_to    = $from_add; }
    # 不達ﾒｰﾙ戻り先ﾁｪｯｸ
    if ($return_path == '') { $return_path = $from_add; }

    # 送信ｴﾝｺｰﾄﾞ設定
    if ($content_transfer_encoding == '') {
      if ($emoji_obj->cont_trs_enc) {
        $content_transfer_encoding = $emoji_obj->cont_trs_enc;
      } else {
        $content_transfer_encoding = '7bit';
      }
    }

    # ﾒｰﾙﾀｲﾌﾟ設定
    $mail_type = '';
    if (isset($body_plain)) {
      if ($body_plain != '') {
        if (isset($body_html)) {
          if ($body_html != '') { $mail_type = 'multipart'; } else { $mail_type = 'plain'; }
        } else {
          $mail_type = 'plain';
        }
      } else {
        if (isset($body_html)) {
          if ($body_html != '') { $mail_type = 'html'; } else { return False; }
        } else {
          return False;
        }
      }
    } else {
      if (isset($body_html)) {
        if ($body_html != '') { $mail_type = 'html'; } else { return False; }
      } else {
        return False;
      }
    }

    # 送信先(To句)生成
    $sp     = '';
    $set_to = '';
    if ($to_flag == True) {
      foreach ($TODATA as $adddt => $namedt) {
        if ($namedt != '') {
          # 送信先名の指定がある場合
          $set_to_sub = '';
          $str_code   = mb_detect_encoding($namedt,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
          if ($str_code == 'JIS') {
            $set_to_sub = $namedt;
          } else {
            $set_to_sub = @mb_convert_encoding($namedt,'JIS',$str_code);
          }
          $set_to_sub  = mb_convert_kana($set_to_sub,'KV','JIS');
          $set_to_sub  = mb_encode_mimeheader($set_to_sub,'JIS');
          $set_to     .= $sp.$set_to_sub.' <'.$adddt.'>';
        } else {
          # 送信先名の指定が無い場合
          $set_to .= $sp.$adddt;
        }
        $sp = ',';
      }
    }

    # 送信先(CC句)生成
    $sp     = '';
    $set_cc = '';
    if ($cc_flag == True) {
      foreach ($CCDATA as $adddt => $namedt) {
        if ($namedt != '') {
          # 送信先名の指定がある場合
          $set_cc_sub = '';
          $str_code   = mb_detect_encoding($namedt,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
          if ($str_code == 'JIS') {
            $set_cc_sub = $namedt;
          } else {
            $set_cc_sub = @mb_convert_encoding($namedt,'JIS',$str_code);
          }
          $set_cc_sub  = mb_convert_kana($set_cc_sub,'KV','JIS');
          $set_cc_sub  = mb_encode_mimeheader($set_cc_sub,'JIS');
          $set_cc     .= $sp.$set_cc_sub.' <'.$adddt.'>';
        } else {
          # 送信名の指定が無い場合
          $set_cc .= $sp.$adddt;
        }
        $sp = ',';
      }
    }

    # 同報(Bcc句)生成
    $sp      = '';
    $set_bcc = '';
    if ($bcc_flag == True) {
      foreach ($BCCDATA as $adddt => $namedt) {
        if ($namedt != '') {
          # 同報先名の指定がある場合
          $set_bcc_sub = '';
          $str_code    = mb_detect_encoding($namedt,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
          if ($str_code == 'JIS') {
            $set_bcc_sub = $namedt;
          } else {
            $set_bcc_sub = @mb_convert_encoding($namedt,'JIS',$str_code);
          }
          $set_bcc_sub  = mb_convert_kana($set_bcc_sub,'KV','JIS');
          $set_bcc_sub  = mb_encode_mimeheader($set_bcc_sub,'JIS');
          $set_bcc     .= $sp.$set_bcc_sub.' <'.$adddt.'>';
        } else {
          # 同報名の指定が無い場合
          $set_bcc .= $sp.$adddt;
        }
        $sp = ',';
      }
    }

    # 送信元(From句)生成
    $set_form = '';
    if ($from_name != '') {
      $str_code = mb_detect_encoding($from_name,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if ($str_code == 'JIS') {
        $set_form = $from_name;
      } else {
        $set_form = @mb_convert_encoding($from_name,'JIS',$str_code);
      }
      $set_form  = mb_convert_kana($set_form,'KV','JIS');
      $set_form  = mb_encode_mimeheader($set_form,'JIS');
      $set_form .= ' <'.$from_add.'>';
    } else {
      $set_form = $from_add;
    }

    # 返信先(Repry_to句)生成
    $set_repry_to = '';
    if ($repry_name != '') {
      $str_code = mb_detect_encoding($repry_name,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if ($str_code == 'JIS') {
        $set_repry_to  = $repry_name;
      } else {
        $set_repry_to  = @mb_convert_encoding($repry_name,'JIS',$str_code);
      }
      $set_repry_to  = mb_convert_kana($set_repry_to,'KV','JIS');
      $set_repry_to  = mb_encode_mimeheader($set_repry_to,'JIS');
      $set_repry_to .= " <".$repry_to.">";
    } else {
      $set_repry_to = $repry_to;
    }

    # 本文設定
    if (!isset($body_plain)) { $body_plain = ''; }
    if (!isset($body_html))  { $body_html  = ''; }

    # ﾒｰﾙ送信用絵文字変換(ｴﾝｺｰﾄﾞ)
    if ($encode_pass != '1') {
      $subject    = $emoji_obj->emj_encode($subject   ,'','',$input_code);
      $body_plain = $emoji_obj->emj_encode($body_plain,'','',$input_code);
      $body_html  = $emoji_obj->emj_encode($body_html ,'','',$input_code);
    }

    # 文字ｺｰﾄﾞ取得
    $subject_code    = mb_detect_encoding($subject   ,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    $body_plain_code = mb_detect_encoding($body_plain,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    $body_html_code  = mb_detect_encoding($body_html ,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);

    # 文字ｺｰﾄﾞ変換
    if ($subject_code    != $mail_code) { $subject    = @mb_convert_encoding($subject   ,$mail_code,$subject_code); }
    if ($body_plain_code != $mail_code) { $body_plain = @mb_convert_encoding($body_plain,$mail_code,$subject_code); }
    if ($body_html_code  != $mail_code) { $body_html  = @mb_convert_encoding($body_html ,$mail_code,$subject_code); }

    # ｶﾀｶﾅ変換
    $subject    = mb_convert_kana($subject   ,'KV',$mail_code);
    $body_plain = mb_convert_kana($body_plain,'KV',$mail_code);
    $body_html  = mb_convert_kana($body_html ,'KV',$mail_code);

    # 件名処理
    # 絵文字変換(ﾃﾞｺｰﾄﾞ)
    if (($to_career == '') or ($to_career == 'PC')) {
      # PC及び全ｷｬﾘｱ向けの場合(絵文字削除)
      $subject = $emoji_sub_obj->delete_emoji_code($subject);
    } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      # SoftBank宛ての場合(絵文字削除)
      $subject = $emoji_sub_obj->delete_emoji_code($subject);
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $SUBJECT = $emoji_sub_obj->emj_decode($subject,$to_career,$mail_code);
      $subject = $SUBJECT['mail'];
    }
    # 件名処理
    if ($subject == '') { $subject = @mb_convert_encoding('無題','JIS','SJIS'); }
    $subject = base64_encode($subject);
    $subject = '=?ISO-2022-JP?B?'.$subject.'?=';

    # SoftBankｷｬﾘｱ設定

    # 本文処理(ﾃｷｽﾄ)
    $to_html_flag = False;
    $enc_code = 'ISO-2022-JP';
    # ﾒｰﾙ送信用絵文字変換(ﾃﾞｺｰﾄﾞ)
    if (($to_career == '') or ($to_career == 'PC')) {
      # PC及び全ｷｬﾘｱ向けの場合(絵文字削除→HTML化)
      # 絵文字有無ﾁｪｯｸ
      $ECOUNT = $emoji_sub_obj->emj_check($body_plain,'',$input_code);
      if ($ECOUNT['total'] > 0) {
        # HTML化
        if ($body_html == '') {
          $body_html = $body_plain;
          $mail_type = 'multipart';
        }
        # 絵文字有り絵文字削除
        $body_plain = $emoji_sub_obj->delete_emoji_code($body_plain);
      }
    } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      # SoftBank向け
      # 絵文字有無ﾁｪｯｸ
      $ECOUNT = $emoji_sub_obj->emj_check($body_plain,'',$input_code);
      if ($ECOUNT['total'] > 0) {
        # HTML化
        if ($body_html == '') {
          $body_html = $body_plain;
          # 文字ｺｰﾄﾞ変換
          $dt = mb_detect_encoding($body_html,'auto');
          if ($dt != '') {
            if (mb_preferred_mime_name($dt) != mb_preferred_mime_name('UTF-8')) {
              $body_html = mb_convert_encoding($body_html,'UTF-8',$dt);
            }
            $body_html = preg_replace('/\r/','',$body_html);
            $body_html = preg_replace('/\n/',"<br>\n",$body_html);
            $body_html = "<html><head><meta http-equiv=\"content-type\" content=\"text/html;charset=UTF-8\"></head><body>\n{$body_html}\n</body></html>";
          }
          $BODYPLAIN    = $emoji_obj->emj_decode($body_plain,$to_career,$mail_code);
          $body_plain   = $BODYPLAIN['mail_plain'];
          $to_html_flag = True;
          $enc_code     = 'UTF-8';
          $mail_type    = 'multipart';
        }
      }
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $BODYPLAIN  = $emoji_obj->emj_decode($body_plain,$to_career,$mail_code);
      $body_plain = $BODYPLAIN['mail'];
    }
    $body_plain = preg_replace('/\r/','',$body_plain);
    if (($body_plain != '') and !preg_match('/\n$/',$body_plain)) { $body_plain .= "\n"; }

    # 本文処理(HTML)
    # ﾒｰﾙ送信用絵文字変換(ﾃﾞｺｰﾄﾞ)
    if (($to_career == '') or ($to_career == 'PC')) {
      # PC及び全ｷｬﾘｱ向けの場合(画像変換)
      $BODYHTML  = $emoji_obj->emj_decode($body_html,$to_career,$mail_code,1);
      $body_html = $BODYHTML['mail'];
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      if ($to_html_flag == True) {
        # SoftBank宛てHTMLﾒｰﾙ用絵文字ﾃﾞｺｰﾄﾞ
        $BODYPLAIN  = $emoji_obj->emj_decode($body_html,$to_career,'UTF-8');
        $body_html  = $BODYPLAIN['mail'];
      } else {
        # SoftBank宛てHTMLﾒｰﾙ以外用絵文字ﾃﾞｺｰﾄﾞ
        $BODYHTML  = $emoji_obj->emj_decode($body_html,$to_career,$mail_code);
        $body_html = $BODYHTML['mail'];
      }
    }

    # 本文HTML化処理
    $body_html = preg_replace('/\r/','',$body_html);
    if (($body_html != '') and !preg_match('/\n$/',$body_html)) { $body_html .= "\n"; }

    # Base64ﾃﾞｺｰﾄﾞ
    if ($content_transfer_encoding == 'base64') {
      $body_plain = base64_encode($body_plain);
      $body_html  = base64_encode($body_html);
    }

    # 添付ﾌｧｲﾙﾁｪｯｸ
    $upfile_flag = False;
    $UPFILELIST  = array();
    if (isset($UPFILE)) {
      if (is_array($UPFILE)) {
        $no = 0;
        foreach ($UPFILE as $pathdt => $namedt) {
          if (isset($pathdt)) {
            if (file_exists($pathdt)) {
              # 添付ﾌｧｲﾙ情報設定
              $PATHDATA = pathinfo($pathdt);
              $UPFILELIST[$no]['path']      = $PATHDATA['dirname'];
              $UPFILELIST[$no]['extension'] = $PATHDATA['extension'];
              $UPFILELIST[$no]['mime']      = $this->get_mime_type($pathdt);
              # ﾌｧｲﾙ名設定
              $UPFILELIST[$no]['basename']  = $PATHDATA['basename'];
              if (isset($namedt)) {
                if ($namedt == '') {
                  $UPFILELIST[$no]['basename'] = $PATHDATA['basename'];
                } else {
                  $UPFILELIST[$no]['basename'] = $namedt;
                }
              } else {
                $UPFILELIST[$no]['basename'] = $PATHDATA['basename'];
              }
              # ﾌｧｲﾙ読込み
              $fp    = fopen($pathdt,"r");
              $fdata = fread($fp,filesize($pathdt));
              fclose($fp);
              # ｴﾝｺｰﾄﾞして分割
              $UPFILELIST[$no]['filedata'] = chunk_split(base64_encode($fdata));
              $upfile_flag = True;
              $mail_type   = 'multipart/file';
              $no++;
            }
          }
        }
      }
    }

    # 共通ﾍｯﾀﾞｰ処理
    $add_mail_header       = '';
    $add_mail_header_smtp  = '';
    $add_mail_header      .= "From: ".$set_form."\n";
    $add_mail_header      .= "Reply-To: ".$set_repry_to."\n";
    if ($set_cc  != '') { $add_mail_header .= "Cc: ".$set_cc."\n"; }
    if ($set_bcc != '') { $add_mail_header .= "Bcc: ".$set_bcc."\n"; }
    $add_mail_header .= "MIME-Version: 1.0\n";
    $add_mail_header_smtp .= "MIME-Version: 1.0\n";

    # ﾍｯﾀﾞｰ処理
    if (preg_match('/^multipart/',$mail_type)) {
      # ﾏﾙﾁﾊﾟｰﾄﾒｰﾙ(ﾃｷｽﾄ+HTML,ﾃｷｽﾄ+HTML+添付ﾌｧｲﾙ,ﾃｷｽﾄorHTML+添付ﾌｧｲﾙ)
      # ﾊﾞｳﾝﾀﾞﾘｰ文字(ﾊﾟｰﾄの境界)
      $boundary = md5(uniqid(rand()));
      # ﾍｯﾀﾞｰ設定
      if ($mail_type == 'multipart') {
        # HTMLﾒｰﾙ
        $add_mail_header      .= "Content-Type: multipart/alternative; boundary=\"".$boundary."\"\n";
        $add_mail_header_smtp .= "Content-Type: multipart/alternative; boundary=\"".$boundary."\"\n";
      } else {
        # 添付ﾌｧｲﾙ
        $add_mail_header      .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\n";
        $add_mail_header_smtp .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\n";
      }
      $add_mail_header      .= "This is a multi-part message in MIME format.";
      $add_mail_header_smtp .= "This is a multi-part message in MIME format.";
    } elseif ($mail_type == 'plain') {
      $add_mail_header      .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
      $add_mail_header      .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
      $add_mail_header_smtp .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
      $add_mail_header_smtp .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
    } elseif ($mail_type == 'html') {
      $add_mail_header .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $add_mail_header .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
      $add_mail_header_smtp .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $add_mail_header_smtp .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
    }

    # 本文処理
    $msg = '';
    if (preg_match('/^multipart/',$mail_type)) {
      if (($body_plain != '') and ($body_html != '') and preg_match('/file/',$mail_type)) {
        # ﾏﾙﾁﾊﾟｰﾄﾒｰﾙ(ﾃｷｽﾄ+HTML+添付ﾌｧｲﾙ)
        $boundary_2 = md5(uniqid(rand()));
        # ﾊﾟｰﾄ区切りｽﾀｰﾄ
        $msg .= "--".$boundary."\n";
        $msg .= "Content-Type: multipart/alternative; boundary=\"".$boundary_2."\"\n";
        $msg .= "\n";
        # ﾃｷｽﾄ本文
        $msg .= "--".$boundary_2."\n";
        $msg .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
        $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\n";
        $msg .= "\n";
        $msg .= $body_plain;
        $msg .= "\n";
        # HTML本文
        $msg .= "--".$boundary_2."\n";
        $msg .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\n";
        $msg .= "\n";
        $msg .= $body_html;
        $msg .= "\n";
        # ﾊﾟｰﾄ区切り終了
        $msg .= "--".$boundary_2."--\n";
        # 添付ﾌｧｲﾙ
        foreach ($UPFILELIST as $UDT) {
          $msg .= "--".$boundary."\n";
          $msg .= "Content-Type: ".$UDT['mime'].";\n";
          $msg .= "\tname=\"".$UDT['basename']."\"\n";
          $msg .= "Content-Transfer-Encoding: base64\n";
          $msg .= "Content-Disposition: attachment;\n";
          $msg .= "\tfilename=\"".$UDT['basename']."\"\n\n";
          $msg .= $UDT['filedata']."\n";
          $msg .= "\n";
        }

      } else {
        # ﾏﾙﾁﾊﾟｰﾄﾒｰﾙ(ﾃｷｽﾄ+HTML,ﾃｷｽﾄorHTML+添付ﾌｧｲﾙ)
        if ($body_plain != '') {
          # ﾃｷｽﾄ設定
          $msg .= "--".$boundary."\n";
          $msg .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
          $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\n";
          $msg .= "\n";
          $msg .= $body_plain;
          $msg .= "\n";
        }
        if ($body_html != '') {
          # HTML設定
          $msg .= "--".$boundary."\n";
          $msg .= "Content-Type: text/html; charset=\"{$enc_code}\"\n";
          $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\n";
          $msg .= "\n";
          $msg .= $body_html;
          $msg .= "\n";
        }
        if ($upfile_flag == 1) {
          # 添付ﾌｧｲﾙ有る場合
          foreach ($UPFILELIST as $UDT) {
            $msg .= "--".$boundary."\n";
            $msg .= "Content-Type: ".$UDT['mime'].";\n";
            $msg .= "\tname=\"".$UDT['basename']."\"\n";
            $msg .= "Content-Transfer-Encoding: base64\n";
            $msg .= "Content-Disposition: attachment;\n";
            $msg .= "\tfilename=\"".$UDT['basename']."\"\n\n";
            $msg .= $UDT['filedata']."\n";
            $msg .= "\n";
          }
        }
      }
      $msg .= "--".$boundary."--\n";
    } elseif ($mail_type == 'plain') {
      # ﾃｷｽﾄﾒｰﾙ
      $msg .= $body_plain;
    } elseif ($mail_type == 'html') {
      # HTMLﾒｰﾙ
      $msg .= $body_html;
    }
    # ﾒｰﾙ送信
    if ((EMOJI_smtp_flag == 1) and is_object($smtp_obj)) {
      # SMTP送信
      # 送信内容設定
      $smtp_obj->TOLIST           = $TODATA;
      $smtp_obj->CCDATA           = $CCDATA;
      $smtp_obj->BCCDATA          = $BCCDATA;
      $smtp_obj->from_name        = $from_name;
      $smtp_obj->from_address     = $from_add;
      $smtp_obj->reply_to_name    = $repry_name;
      $smtp_obj->reply_to_address = $repry_to;
      $smtp_obj->return_path      = $return_path;
      $smtp_obj->add_header       = $add_mail_header_smtp;
      $smtp_obj->subject          = $subject;
      $smtp_obj->body             = $msg;
      # ﾒｰﾙ送信
      $success = $smtp_obj->smtp_mail();
    } else {
      # PHP mail関数送信
      $success = @mail($set_to,$subject,$msg,$add_mail_header,'-f'.$return_path);
    }
    if ($success) { return True; } else { return False; }
  }

  # 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙ送信 /////////////////////////////////////////////////////
  # 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙを送信します。
  # [引渡し値]
  # 　$MAIL_DATA['TODATA']                       : 送信先ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ
  # 　　$MAIL_DATA['TODATA'][*****]              : ｷｰ名:送信先ﾒｰﾙｱﾄﾞﾚｽ、要素(値):送信先名
  # 　$MAIL_DATA['CCDATA']                       : 送信先ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ(ｶｰﾎﾞﾝｺﾋﾟｰ)
  # 　　$MAIL_DATA['CCDATA'][*****]              : ｷｰ名:送信先(ｶｰﾎﾞﾝｺﾋﾟｰ)ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ、要素(値):送信先(ｶｰﾎﾞﾝｺﾋﾟｰ)名
  # 　$MAIL_DATA['BCCDATA']                      : 同報先ﾒｰﾙｱﾄﾞﾚｽ
  # 　　$MAIL_DATA['BCCDATA'][*****]             : ｷｰ名:同報先ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ、要素(値):同報先名
  # 　$MAIL_DATA['from_name']                    : 送信元名
  # 　$MAIL_DATA['from_add']                     : 送信元ﾒｰﾙｱﾄﾞﾚｽ
  # 　$MAIL_DATA['repry_name']                   : 返信先名(指定無い場合は送信元名)
  # 　$MAIL_DATA['repry_to']                     : 返信先ﾒｰﾙｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$MAIL_DATA['return_path']                  : 不達ﾒｰﾙ送信先ｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$MAIL_DATA['subject']                      : 件名
  # 　$MAIL_DATA['body_plain']                   : ﾃｷｽﾄ本文
  # 　$MAIL_DATA['body_html']                    : HTML本文
  # 　$SETTING_DATA['decome_mode']               : ﾃﾞｺﾒ指定(指定なし:一般送信、'1':ﾃﾞｺﾒ送信)
  # 　$SETTING_DATA['to_career']                 : 送信先ｷｬﾘｱ(指定なし:PC及び全ｷｬﾘｱ、'DoCoMo':DoCoMo、'au':au、'SoftBank':SoftBank(絵文字変換ﾗｲﾌﾞﾗﾘで設定した名前))
  # 　$SETTING_DATA['content_transfer_encoding'] : ﾒｰﾙｴﾝｺｰﾃﾞｨﾝｸﾞ指定(指定なし又は'7bit':ﾃﾞﾌｫﾙﾄ又は7bit、'base64':base64)
  # 　$SETTING_DATA['mail_code']                 : ﾒｰﾙ本文文字ｺｰﾄﾞ指定(指定なし又は'JIS':JIS)
  # 　$SETTING_DATA['encode_pass']               : ｴﾝｺｰﾄﾞ処理無効化('1')
  # 　$SETTING_DATA['input_code']                : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # 　$UPFILE[*****]                             : ｷｰ名:添付ﾌｧｲﾙﾊﾟｽ、要素(値):添付ﾌｧｲﾙ名
  # 　$katakana_chg_cancel       : 件名･本文半角ｶﾀｶﾅ全角変換ｷｬﾝｾﾙ(指定なし:強制変換,1:変換ｷｬﾝｾﾙ)
  # [返り値]
  # 　True : 送信成功、False : 送信失敗
  #////////////////////////////////////////////////////////////////////////////
  function emoji_decome($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel='') {
    global $decome_obj;

    if (is_object($decome_obj)) {
      # ｵﾌﾞｼﾞｪｸﾄが作成されている場合
      return $decome_obj->emoji_decome($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel);
    } else {
      # ｵﾌﾞｼﾞｪｸﾄが作成されていない場合
      return False;
    }
  }

  # 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙ送信2(emoji_send_mail3関数と互換性) //////////////////////
  # 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙを送信します。
  # [引渡し値]
  # 　$TODATA[*****]             : ｷｰ名:送信先ﾒｰﾙｱﾄﾞﾚｽ、要素(値):送信先名
  # 　$CCDATA[*****]             : ｷｰ名:送信先(ｶｰﾎﾞﾝｺﾋﾟｰ)ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ、要素(値):送信先(ｶｰﾎﾞﾝｺﾋﾟｰ)名
  # 　$BCCDATA[*****]            : ｷｰ名:同報先ﾒｰﾙｱﾄﾞﾚｽﾘｽﾄ、要素(値):同報先名
  # 　$from_name                 : 送信元名
  # 　$from_add                  : 送信元ﾒｰﾙｱﾄﾞﾚｽ
  # 　$subject                   : 件名
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$repry_name                : 返信先名(指定無い場合は送信元名)
  # 　$repry_to                  : 返信先ﾒｰﾙｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$return_path               : 不達ﾒｰﾙ送信先ｱﾄﾞﾚｽ(指定無い場合は送信元ﾒｰﾙｱﾄﾞﾚｽ)
  # 　$to_career                 : 送信先ｷｬﾘｱ(指定なし:PC及び全ｷｬﾘｱ、'DoCoMo':DoCOMo、'au':au、'SoftBank':SoftBank)
  # 　$content_transfer_encoding : ﾒｰﾙｴﾝｺｰﾃﾞｨﾝｸﾞ指定(指定なし又は'7bit':ﾃﾞﾌｫﾙﾄ又は7bit、'base64':base64)
  # 　$mail_code                 : ﾒｰﾙ本文文字ｺｰﾄﾞ指定(指定なし又は'JIS':JIS)
  # 　$UPFILE[*****]             : ｷｰ名:添付ﾌｧｲﾙﾊﾟｽ、要素(値):添付ﾌｧｲﾙ名
  # 　$encode_pass               : ｴﾝｺｰﾄﾞ処理無効化('1')
  # 　$input_code                : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # 　$decome_mode               : ﾃﾞｺﾒ指定(指定なし:一般送信(emoji_send_mail3関数と同等の処理となります)、'1':ﾃﾞｺﾒ送信)
  # 　$katakana_chg_cancel       : 件名･本文半角ｶﾀｶﾅ全角変換ｷｬﾝｾﾙ(指定なし:強制変換,1:変換ｷｬﾝｾﾙ)
  # [返り値]
  # 　True : 送信成功、False : 送信失敗
  #////////////////////////////////////////////////////////////////////////////
  function emoji_decome2($TODATA,$CCDATA,$BCCDATA,$from_name,$from_add,$subject,$body_plain,$body_html,$repry_name='',$repry_to='',$return_path='',$to_career='',$content_transfer_encoding='',$mail_code='JIS',$UPFILE='',$encode_pass='',$input_code='',$decome_mode='1',$katakana_chg_cancel='') {
    global $decome_obj;

    if (is_object($decome_obj)) {
      # ｵﾌﾞｼﾞｪｸﾄが作成されている場合
      # 値ｾｯﾄ
      $MAIL_DATA    = array();
      $SETTING_DATA = array();
      $MAIL_DATA['TODATA']                       = $TODATA;
      $MAIL_DATA['CCDATA']                       = $CCDATA;
      $MAIL_DATA['BCCDATA']                      = $BCCDATA;
      $MAIL_DATA['from_name']                    = $from_name;
      $MAIL_DATA['from_add']                     = $from_add;
      $MAIL_DATA['repry_name']                   = $repry_name;
      $MAIL_DATA['repry_to']                     = $repry_to;
      $MAIL_DATA['return_path']                  = $return_path;
      $MAIL_DATA['subject']                      = $subject;
      $MAIL_DATA['body_plain']                   = $body_plain;
      $MAIL_DATA['body_html']                    = $body_html;
      $SETTING_DATA['to_career']                 = $to_career;
      $SETTING_DATA['content_transfer_encoding'] = $content_transfer_encoding;
      $SETTING_DATA['mail_code']                 = $mail_code;
      $SETTING_DATA['encode_pass']               = $encode_pass;
      $SETTING_DATA['input_code']                = $input_code;
      $SETTING_DATA['decome_mode']               = $decome_mode;
      return $decome_obj->emoji_decome($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel);
    } else {
      # ｵﾌﾞｼﾞｪｸﾄが作成されていない場合
      return False;
    }
  }

  # ﾌｧｲﾙMIME取得処理 ////////////////////////////////////////////////
  # ﾌｧｲﾙの拡張子からﾌｧｲﾙMIMEを取得します。
  # [引渡し値]
  # 　$filename : ﾌｧｲﾙ名
  # [返り値]
  # 　$mime : ﾌｧｲﾙMIME
  #////////////////////////////////////////////////////////////////////////////
  function get_mime_type($filename) {
    global $emoji_obj;

    $mime = 'application/octet-stream';
    $PATHDATA  = pathinfo($filename);
    if (isset($PATHDATA['extension'])) {
      if ($PATHDATA['extension'] != '') {
        $extension = $PATHDATA['extension'];
#        if (isset($emoji_obj->FILETYPE[$PATHDATA['extension']])) { $mime = $emoji_obj->FILETYPE[$PATHDATA['extension']]; }
        if (isset($this->FILETYPE[$PATHDATA['extension']])) { $mime = $this->FILETYPE[$PATHDATA['extension']]; }
      }
    }
    return $mime;
  }

}

?>