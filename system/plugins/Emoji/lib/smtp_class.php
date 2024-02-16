<?php
###############################################################################
# SMTPメール送信処理クラス
# Potora/inaken(C) 2008.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# 2007.03.18 v.0.00.00 新規作成
###############################################################################
$mail_ver = 'v.0.00.00';

# SMTPｵﾌﾞｼﾞｪｸﾄ生成 ////////////////////////////////////////////////////////////
$smtp_obj = new smtp();

# SMTP接続設定 ////////////////////////////////////////////////////////////////
# 設定ﾊﾟﾀｰﾝ 1/2 どちらで設定しても良い
# SMTP接続設定-1 //////////////////////////////////////////////////////////////
#$smtp_obj->this_server = ''
#$smtp_obj->smtp_server = ''
#$smtp_obj->smtp_port   = 25;
#$smtp_obj->pop3_server = '';
#$smtp_obj->pop3_port   = 110;
#$smtp_obj->mail_user   = '';
#$smtp_obj->mail_pass   = '';
#$smtp_obj->auth        = True;
#$smtp_obj->auth_type   = 'POP';

# SMTP接続設定-2 //////////////////////////////////////////////////////////////
#$smtp_obj->connect_setting('','',25,'',110,'','',True,'POP');

###############################################################################
# SMTP接続ﾒｰﾙ送信ｸﾗｽ ##########################################################
###############################################################################
class smtp {

  var $smtp_connect_flag = False;
  var $smtp_res          = '';
  var $pop3_connect_flag = False;
  var $pop3_res          = '';

  var $this_server = '';
  var $smtp_server = '';
  var $smtp_port   = 25;
  var $pop3_server = '';
  var $pop3_port   = 110;
  var $auth        = False;
  var $auth_tyle   = 'POP';
  var $pop3_connect_retry_num = 3;

  var $mail_user = '';
  var $mail_pass = '';

  var $crlf         = "\r\n";
  var $in_chr_code  = 'SJIS';
  var $out_chr_code = 'JIS';

  var $TOLIST  = array();
  var $CCLIST  = array();
  var $BCCLIST = array();
  var $from_name        = '';
  var $from_address     = '';
  var $reply_to_name    = '';
  var $reply_to_address = '';
  var $return_path      = '';
  var $add_header       = '';
  var $subject          = '';
  var $body             = '';

  # 文字ｺｰﾄﾞｴﾝｺｰﾄﾞﾘｽﾄ設定
  var $ENCODINGLIST = array(
    'Shift_JIS'   => 'SJIS-win,SJIS,JIS,EUC-JP,UTF-8',
    'SJIS'        => 'SJIS-win,SJIS,JIS,EUC-JP,UTF-8',
    'SJIS-win'    => 'SJIS-win,SJIS,JIS,EUC-JP,UTF-8',
    'EUC-JP'      => 'EUC-JP,SJIS-win,SJIS,JIS,UTF-8',
    'EUC'         => 'EUC-JP,SJIS-win,SJIS,JIS,UTF-8',
    'eucJP-win'   => 'EUC-JP,SJIS-win,SJIS,JIS,UTF-8',
    'UTF-8'       => 'UTF-8,SJIS-win,SJIS,JIS,EUC-JP',
    'JIS'         => 'SJIS-win,SJIS,JIS,EUC-JP,UTF-8',
    'ISO-2022-JP' => 'SJIS-win,SJIS,JIS,EUC-JP,UTF-8',
  );

  # ｺﾝｽﾄﾗｸﾀ ///////////////////////////////////////////////////////////////////
  function smtp() {
  }

  # ﾒｰﾙｻｰﾊﾞｰ接続設定 //////////////////////////////////////////////////////////
  # ﾒｰﾙｻｰﾊﾞｰ(POP3)の設定をします。
  # 引渡し値:
  #   $this_server : 接続元ｻｰﾊﾞｰ設定
  #   $smtp_server : SMTPｻｰﾊﾞｰ設定
  #   $smtp_port   : SMTPｻｰﾊﾞｰﾎﾟｰﾄ設定
  #   $pop3_server : POP3ｻｰﾊﾞｰ設定(Auth認証有りの場合)
  #   $pop3_port   : POP3ｻｰﾊﾞｰﾎﾟｰﾄ設定(Auth認証有りの場合)
  #   $mail_user   : ﾕｰｻﾞｰID
  #   $mail_pass   : ﾊﾟｽﾜｰﾄﾞ
  #   $auth        : Auth認証
  #   $auth_type   : Authﾀｲﾌﾟ
  # 返り値:
  #   $flag        : 設定結果(True:成功、False:失敗)
  #////////////////////////////////////////////////////////////////////////////
  function connect_setting($this_server,$smtp_server,$smtp_port,$pop3_server,$pop3_port,$mail_user,$mail_pass,$auth,$auth_type) {
    $flag = True;

    # 接続切断
    if ($this->smtp_connect_flag == True) { $this->smtp_disconnect(); }
    if ($this->pop3_connect_flag == True) { $this->pop3_disconnect(); }

    # 接続設定
    if ($this_server) { $this->this_server = $this_server; }
    if ($smtp_server) { $this->smtp_server = $smtp_server; }
    if ($smtp_port)   { $this->smtp_port   = $smtp_port; }
    if ($pop3_server) { $this->pop3_server = $pop3_server; }
    if ($pop3_port)   { $this->pop3_port   = $pop3_port; }
    if ($mail_user)   { $this->mail_user   = $mail_user; }
    if ($mail_pass)   { $this->mail_pass   = $mail_pass; }
    if ($auth)        { $this->auth        = $auth; }
    if ($auth_type)   { $this->auth_type   = $auth_type; }

    return $flag;
  }

  # SMTP接続 //////////////////////////////////////////////////////////////////
  # 返り値:
  #   $flag : 送信結果(True:成功、False:失敗)
  #////////////////////////////////////////////////////////////////////////////
  function smtp_connect() {
    $flag = False;
    if ($this->smtp_connect_flag == False) {
      if ($this->smtp_server and $this->smtp_port) {
        if ($this->smtp_res = fsockopen($this->smtp_server,$this->smtp_port)) {
          fputs($this->smtp_res,"HELO {$this->this_server}\r\n");
#          $result = fgets($this->smtp_res,128);
          # Auth認証(LOGIN)
          if (($this->auth == True) and ($this->auth_type == 'LOGIN')) {
            # 認証確認
            fputs($this->smtp_res,"AUTH LOGIN\r\n");
            $result = fgets($this->smtp_res,128);
            if(!ereg("^334",$result)){
              $this->smtp_disconnect();
              return False;
            }
            # ﾕｰｻﾞｰID認証
            fputs($this->smtp_res,base64_encode($this->mail_user)."\r\n");
            $result = fgets($this->smtp_res,128);
            if(!ereg("^334",$result)){
              $this->smtp_disconnect();
              return False;
            }
            # ﾊﾟｽﾜｰﾄﾞ認証
            fputs($this->smtp_res,base64_encode($this->mail_pass)."\r\n");
            $result = fgets($this->smtp_res,128);
            if(!ereg("^334",$result)){
              $this->smtp_disconnect();
              return False;
            }
          }

          $flag = True;
          $this->smtp_connect_flag = True;
        }
      }
    }
    return $flag;
  }

  # SMTP切断 //////////////////////////////////////////////////////////////////
  # 返り値:
  #   なし
  #////////////////////////////////////////////////////////////////////////////
  function smtp_disconnect() {
    @fclose($this->smtp_res);
    $this->smtp_connect_flag = False;
  }

  # POP3接続 //////////////////////////////////////////////////////////////
  # 返り値:
  #   $flag : 結果(True:成功、False:失敗)
  #////////////////////////////////////////////////////////////////////////////
  function pop3_connect() {
    $flag = False;
    # 接続情報ﾁｪｯｸ
    if ($this->pop3_server and $this->pop3_port and $this->mail_user and $this->mail_pass) {
      # POP3ｻｰﾊﾞ接続
      for ($no = 1; $no <= $this->pop3_connect_retry_num; $no++) {
        if ($this->pop3_res = imap_open("{".$this->pop3_server.":".$this->pop3_port."/pop3/notls}INBOX",$this->mail_user,$this->mail_pass)) {
          $flag = True;
          $this->pop3_connect_flag = True;
          break;
        }
      }
    }
    return $flag;
  }

  # POP3接続切断 //////////////////////////////////////////////////////////////
  # 返り値:
  #   なし
  #////////////////////////////////////////////////////////////////////////////
  function pop3_disconnect() {
    @imap_close($this->pop3_res);
    $this->connect_flag = False;
  }

  # SMTP接続ﾒｰﾙ送信 ///////////////////////////////////////////////////////////
  # 返り値:
  #   $flag : 送信結果(True:成功、False:失敗)
  #////////////////////////////////////////////////////////////////////////////
  function smtp_mail() {

    # Auth認証(POP befor SMTP)
    if (($this->auth == True) and ($this->auth_type == 'POP')) {
      if ($this->pop3_connect()) {
        $this->pop3_disconnect();
      } else {
        return False;
      }
    }

    # ｴﾗｰﾒｰﾙ返信先設定
    if ($this->return_path == '') {
      $this->return_path = $this->from_address;
    }

    # 送信者設定
    if ($this->from_name != '') {
      $str_code = mb_detect_encoding($this->from_name,$this->ENCODINGLIST[$this->in_chr_code]);
      if ($str_code != '') { $str_code = mb_preferred_mime_name($str_code); }
      if ($str_code != mb_preferred_mime_name($this->out_chr_code)) {
        $this->from_name = @mb_convert_encoding($this->from_name,$this->out_chr_code,$str_code);
      }
      $this->from_name = mb_convert_kana($this->from_name,'KV',$this->out_chr_code);
      $this->from_name = mb_encode_mimeheader($this->from_name,$this->out_chr_code);
      $faddress = "{$this->from_name} <{$this->from_address}>";
    } else {
      $faddress = $this->from_address;
    }
    # 返信先設定
    $rpaddress = '';
    if ($this->reply_to_address != '') {
      if ($this->reply_to_name != '') {
        $str_code = mb_detect_encoding($this->reply_to_name,$this->ENCODINGLIST[$this->in_chr_code]);
        if ($str_code != '') { $str_code = mb_preferred_mime_name($str_code); }
        if ($str_code != mb_preferred_mime_name($this->out_chr_code)) {
          $this->reply_to_name = @mb_convert_encoding($this->reply_to_name,$this->out_chr_code,$str_code);
        }
        $this->reply_to_name = mb_convert_kana($this->reply_to_name,'KV',$this->out_chr_code);
        $this->reply_to_name = mb_encode_mimeheader($this->reply_to_name,$this->out_chr_code);
        $rpaddress = "{$this->reply_to_name} <{$this->reply_to_address}>";
      } else {
        $rpaddress = $this->reply_to_address;
      }
    }

#    # 件名処理
#    $subject_code = mb_detect_encoding($this->subject,$this->ENCODINGLIST[$this->in_chr_code]);
#    if ($subject_code != '') { $subject_code = mb_preferred_mime_name($subject_code); }
#    if ($subject_code != mb_preferred_mime_name($this->out_chr_code)) {
#      $this->subject = @mb_convert_encoding($this->subject,$this->out_chr_code,$subject_code);
#    }
#    $this->subject = mb_convert_kana($this->subject,'KV',$this->out_chr_code);
##    if ($this->subject == '') { $this->subject = @mb_convert_encoding('無題',$this->out_chr_code,'SJIS'); }
#    $this->subject = base64_encode($this->subject);
#    $this->subject = '=?ISO-2022-JP?B?'.$this->subject.'?=';

    # 送信先設定
    $taddress = '';
    $sp       = '';
    foreach ($this->TOLIST as $to_address => $to_name) {
      if ($to_name != '') {
        $str_code = mb_detect_encoding($to_name,$this->ENCODINGLIST[$this->in_chr_code]);
        if ($str_code != '') { $str_code = mb_preferred_mime_name($str_code); }
        if ($str_code != mb_preferred_mime_name($this->out_chr_code)) {
          $to_name = @mb_convert_encoding($to_name,$this->out_chr_code,$str_code);
        }
        $to_name = mb_convert_kana($to_name,'KV',$this->out_chr_code);
        $to_name = mb_encode_mimeheader($to_name,$this->out_chr_code);
        $taddress .= $sp."{$to_name} <{$to_address}>";
      } else {
        $taddress .= $sp.$to_address;
      }
      $sp = ',';
    }
    # CC送信先設定
    $caddress = '';
    $sp       = '';
    foreach ($this->CCLIST as $cc_address => $cc_name) {
      if ($cc_name != '') {
        $str_code = mb_detect_encoding($cc_name,$this->ENCODINGLIST[$this->in_chr_code]);
        if ($str_code != '') { $str_code = mb_preferred_mime_name($str_code); }
        if ($str_code != mb_preferred_mime_name($this->out_chr_code)) {
          $cc_name = @mb_convert_encoding($cc_name,$this->out_chr_code,$str_code);
        }
        $cc_name = mb_convert_kana($cc_name,'KV',$this->out_chr_code);
        $cc_name = mb_encode_mimeheader($cc_name,$this->out_chr_code);
        $caddress .= $sp."{$cc_name} <{$cc_address}>";
      } else {
        $caddress .= $sp.$cc_address;
      }
      $sp = ',';
    }

    # SMTPｻｰﾊﾞｰ接続
    if ($this->smtp_connect()) {
      # TO送信
      foreach ($this->TOLIST as $to_address => $to_name) {
        fputs($this->smtp_res,'MAIL FROM:'.$this->return_path.$this->crlf);
        fputs($this->smtp_res,'RCPT TO:'.$to_address.$this->crlf);
        fputs($this->smtp_res,'DATA'.$this->crlf);
        fputs($this->smtp_res,'Subject: '.$this->subject.$this->crlf);
        fputs($this->smtp_res,'From: '.$faddress.$this->crlf);
        if ($rpaddress != '') {
          fputs($this->smtp_res,'Reply-To: '.$rpaddress.$this->crlf);
        }
        fputs($this->smtp_res,'To: '.$taddress.$this->crlf);
        if ($caddress != '') {
          fputs($this->smtp_res,'Cc: '.$caddress.$this->crlf);
        }
        if ($this->add_header != '') {
          fputs($this->smtp_res,$this->add_header.$this->crlf);
        }
        fputs($this->smtp_res,$this->crlf);
        fputs($this->smtp_res,$this->body.$this->crlf);
        fputs($this->smtp_res,$this->crlf.'.'.$this->crlf);
      }
      # CC送信
      foreach ($this->CCLIST as $cc_address => $cc_name) {
        fputs($this->smtp_res,'MAIL FROM:'.$this->return_path.$this->crlf);
        fputs($this->smtp_res,'RCPT TO:'.$cc_address.$this->crlf);
        fputs($this->smtp_res,'DATA'.$this->crlf);
        fputs($this->smtp_res,'Subject: '.$this->subject.$this->crlf);
        fputs($this->smtp_res,'From: '.$faddress.$this->crlf);
        if ($rpaddress != '') {
          fputs($this->smtp_res,'Reply-To: '.$rpaddress.$this->crlf);
        }
        fputs($this->smtp_res,'To: '.$taddress.$this->crlf);
        if ($caddress != '') {
          fputs($this->smtp_res,'Cc: '.$caddress.$this->crlf);
        }
        if ($this->add_header != '') {
          fputs($this->smtp_res,$this->add_header.$this->crlf);
        }
        fputs($this->smtp_res,$this->crlf);
        fputs($this->smtp_res,$this->body.$this->crlf);
        fputs($this->smtp_res,$this->crlf.'.'.$this->crlf);
      }
      # BCC送信
      foreach ($this->BCCLIST as $bcc_address => $bcc_name) {
        fputs($this->smtp_res,'MAIL FROM:'.$this->return_path.$this->crlf);
        fputs($this->smtp_res,'RCPT TO:'.$bcc_address.$this->crlf);
        fputs($this->smtp_res,'DATA'.$this->crlf);
        fputs($this->smtp_res,'Subject: '.$this->subject.$this->crlf);
        fputs($this->smtp_res,'From: '.$faddress.$this->crlf);
        if ($rpaddress != '') {
          fputs($this->smtp_res,'Reply-To: '.$rpaddress.$this->crlf);
        }
        if ($this->add_header != '') {
          fputs($this->smtp_res,$this->add_header.$this->crlf);
        }
        fputs($this->smtp_res,$this->crlf);
        fputs($this->smtp_res,$this->body.$this->crlf);
        fputs($this->smtp_res,$this->crlf.'.'.$this->crlf);
      }
      # 送信終了ｺﾏﾝﾄﾞ送信
      fputs($this->smtp_res,'QUIT'.$this->crlf);
#      $result = fgets($this->smtp_res,128);
      # SMTP接続切断
      $this->smtp_disconnect();
    } else {
      return False;
    }
    return True;
  }

}

?>