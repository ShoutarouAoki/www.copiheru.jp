<?php
###############################################################################
# SMTP���[�����M�����N���X
# Potora/inaken(C) 2008.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# 2007.03.18 v.0.00.00 �V�K�쐬
###############################################################################
$mail_ver = 'v.0.00.00';

# SMTP��޼ު�Đ��� ////////////////////////////////////////////////////////////
$smtp_obj = new smtp();

# SMTP�ڑ��ݒ� ////////////////////////////////////////////////////////////////
# �ݒ������ 1/2 �ǂ���Őݒ肵�Ă��ǂ�
# SMTP�ڑ��ݒ�-1 //////////////////////////////////////////////////////////////
#$smtp_obj->this_server = ''
#$smtp_obj->smtp_server = ''
#$smtp_obj->smtp_port   = 25;
#$smtp_obj->pop3_server = '';
#$smtp_obj->pop3_port   = 110;
#$smtp_obj->mail_user   = '';
#$smtp_obj->mail_pass   = '';
#$smtp_obj->auth        = True;
#$smtp_obj->auth_type   = 'POP';

# SMTP�ڑ��ݒ�-2 //////////////////////////////////////////////////////////////
#$smtp_obj->connect_setting('','',25,'',110,'','',True,'POP');

###############################################################################
# SMTP�ڑ�Ұّ��M�׽ ##########################################################
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

  # �������޴ݺ���ؽĐݒ�
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

  # �ݽ�׸� ///////////////////////////////////////////////////////////////////
  function smtp() {
  }

  # Ұٻ��ް�ڑ��ݒ� //////////////////////////////////////////////////////////
  # Ұٻ��ް(POP3)�̐ݒ�����܂��B
  # ���n���l:
  #   $this_server : �ڑ������ް�ݒ�
  #   $smtp_server : SMTP���ް�ݒ�
  #   $smtp_port   : SMTP���ް�߰Đݒ�
  #   $pop3_server : POP3���ް�ݒ�(Auth�F�ؗL��̏ꍇ)
  #   $pop3_port   : POP3���ް�߰Đݒ�(Auth�F�ؗL��̏ꍇ)
  #   $mail_user   : հ�ްID
  #   $mail_pass   : �߽ܰ��
  #   $auth        : Auth�F��
  #   $auth_type   : Auth����
  # �Ԃ�l:
  #   $flag        : �ݒ茋��(True:�����AFalse:���s)
  #////////////////////////////////////////////////////////////////////////////
  function connect_setting($this_server,$smtp_server,$smtp_port,$pop3_server,$pop3_port,$mail_user,$mail_pass,$auth,$auth_type) {
    $flag = True;

    # �ڑ��ؒf
    if ($this->smtp_connect_flag == True) { $this->smtp_disconnect(); }
    if ($this->pop3_connect_flag == True) { $this->pop3_disconnect(); }

    # �ڑ��ݒ�
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

  # SMTP�ڑ� //////////////////////////////////////////////////////////////////
  # �Ԃ�l:
  #   $flag : ���M����(True:�����AFalse:���s)
  #////////////////////////////////////////////////////////////////////////////
  function smtp_connect() {
    $flag = False;
    if ($this->smtp_connect_flag == False) {
      if ($this->smtp_server and $this->smtp_port) {
        if ($this->smtp_res = fsockopen($this->smtp_server,$this->smtp_port)) {
          fputs($this->smtp_res,"HELO {$this->this_server}\r\n");
#          $result = fgets($this->smtp_res,128);
          # Auth�F��(LOGIN)
          if (($this->auth == True) and ($this->auth_type == 'LOGIN')) {
            # �F�؊m�F
            fputs($this->smtp_res,"AUTH LOGIN\r\n");
            $result = fgets($this->smtp_res,128);
            if(!ereg("^334",$result)){
              $this->smtp_disconnect();
              return False;
            }
            # հ�ްID�F��
            fputs($this->smtp_res,base64_encode($this->mail_user)."\r\n");
            $result = fgets($this->smtp_res,128);
            if(!ereg("^334",$result)){
              $this->smtp_disconnect();
              return False;
            }
            # �߽ܰ�ޔF��
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

  # SMTP�ؒf //////////////////////////////////////////////////////////////////
  # �Ԃ�l:
  #   �Ȃ�
  #////////////////////////////////////////////////////////////////////////////
  function smtp_disconnect() {
    @fclose($this->smtp_res);
    $this->smtp_connect_flag = False;
  }

  # POP3�ڑ� //////////////////////////////////////////////////////////////
  # �Ԃ�l:
  #   $flag : ����(True:�����AFalse:���s)
  #////////////////////////////////////////////////////////////////////////////
  function pop3_connect() {
    $flag = False;
    # �ڑ��������
    if ($this->pop3_server and $this->pop3_port and $this->mail_user and $this->mail_pass) {
      # POP3���ސڑ�
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

  # POP3�ڑ��ؒf //////////////////////////////////////////////////////////////
  # �Ԃ�l:
  #   �Ȃ�
  #////////////////////////////////////////////////////////////////////////////
  function pop3_disconnect() {
    @imap_close($this->pop3_res);
    $this->connect_flag = False;
  }

  # SMTP�ڑ�Ұّ��M ///////////////////////////////////////////////////////////
  # �Ԃ�l:
  #   $flag : ���M����(True:�����AFalse:���s)
  #////////////////////////////////////////////////////////////////////////////
  function smtp_mail() {

    # Auth�F��(POP befor SMTP)
    if (($this->auth == True) and ($this->auth_type == 'POP')) {
      if ($this->pop3_connect()) {
        $this->pop3_disconnect();
      } else {
        return False;
      }
    }

    # �װҰٕԐM��ݒ�
    if ($this->return_path == '') {
      $this->return_path = $this->from_address;
    }

    # ���M�Ґݒ�
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
    # �ԐM��ݒ�
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

#    # ��������
#    $subject_code = mb_detect_encoding($this->subject,$this->ENCODINGLIST[$this->in_chr_code]);
#    if ($subject_code != '') { $subject_code = mb_preferred_mime_name($subject_code); }
#    if ($subject_code != mb_preferred_mime_name($this->out_chr_code)) {
#      $this->subject = @mb_convert_encoding($this->subject,$this->out_chr_code,$subject_code);
#    }
#    $this->subject = mb_convert_kana($this->subject,'KV',$this->out_chr_code);
##    if ($this->subject == '') { $this->subject = @mb_convert_encoding('����',$this->out_chr_code,'SJIS'); }
#    $this->subject = base64_encode($this->subject);
#    $this->subject = '=?ISO-2022-JP?B?'.$this->subject.'?=';

    # ���M��ݒ�
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
    # CC���M��ݒ�
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

    # SMTP���ް�ڑ�
    if ($this->smtp_connect()) {
      # TO���M
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
      # CC���M
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
      # BCC���M
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
      # ���M�I������ޑ��M
      fputs($this->smtp_res,'QUIT'.$this->crlf);
#      $result = fgets($this->smtp_res,128);
      # SMTP�ڑ��ؒf
      $this->smtp_disconnect();
    } else {
      return False;
    }
    return True;
  }

}

?>