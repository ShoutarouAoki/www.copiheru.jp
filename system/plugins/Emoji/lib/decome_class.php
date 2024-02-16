<?php

###############################################################################
# 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙ処理ｸﾗｽ
# Potora/inaken(C) 2003-2007.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# ※本ﾗｲﾌﾞﾗﾘは【絵文字変換ﾗｲﾌﾞﾗﾘ2007/2008】のｻﾌﾞﾗｲﾌﾞﾗﾘ(子ｸﾗｽではない)になります。
# 　単独での使用はできません。
###############################################################################
$decome_ver = 'deco_v.1.00.00'; # 2007.12.14 新規作成
$decome_ver = 'deco_v.1.00.01'; # 2007.12.16 ｲﾝﾗｲﾝ画像取得不足修正
$decome_ver = 'deco_v.1.00.02'; # 2007.12.19 本文ｻｲｽﾞﾁｪｯｸ不具合修正
$decome_ver = 'deco_v.1.00.03'; # 2007.12.23 簡易ﾁｪｯｸ機能追加、ｴﾗｰ処理修正
$decome_ver = 'deco_v.1.00.04'; # 2008.07.17 SoftBank宛て絵文字ﾒｰﾙ対応
$decome_ver = 'deco_v.1.00.05'; # 2008.07.28 件名･本文半角ｶﾀｶﾅ全角変換ｷｬﾝｾﾙ機能追加
$decome_ver = 'deco_v.2.00.00'; # 2008.10.20 SMTP接続ﾒｰﾙ送信機能追加
$decome_ver = 'deco_v.2.00.01'; # 2008.10.20 SMTP送信時CC,BCC指定不具合修正
$decome_ver = 'deco_v.2.00.02'; # 2008.11.02 絵文字ﾒｰﾙ送信不具合修正
$decome_ver = 'deco_v.2.00.03'; # 2008.11.28 ｸﾞﾛｰﾊﾞﾙ変数扱い変更
$decome_ver = 'deco_v.2.00.04'; # 2009.02.24 ｵﾌﾞｼﾞｪｸﾄ指定不具合修正
$decome_ver = 'deco_v.2.01.00'; # 2009.03.09 SoftBank宛て本文生成不具合修正
$decome_ver = 'deco_v.2.01.01'; # 2009.03.10 SoftBank宛てHTML本文生成不具合修正
$decome_ver = 'deco_v.2.01.02'; # 2009.03.11 au,SoftBankﾃﾞｺﾒ処理不具合修正
$decome_ver = 'deco_v.2.01.03'; # 2009.03.12 ﾊﾞｰｼﾞｮﾝ相違によるｵﾌﾞｼﾞｪｸﾄ呼び出し不具合修正
$decome_ver = 'deco_v.2.01.04'; # 2009.03.23 絵文字画像ｾｯﾄ時ｺﾒﾝﾄ削除追加
$decome_ver = 'deco_v.2.01.05'; # 2009.03.25 SB携帯宛て絵文字ﾒｰﾙ生成時HTML生成不具合修正
$decome_ver = 'deco_v.2.01.06'; # 2009.04.13 ﾃｷｽﾄﾒｰﾙHTML化時URL･ﾒｰﾙｱﾄﾞﾚｽﾘﾝｸ化
$decome_ver = 'deco_v.2.01.07'; # 2009.04.15 SoftBankHTMLﾒｰﾙﾍｯﾀﾞｰ修正
$decome_ver = 'deco_v.2.01.08'; # 2009.05.13 PC用ﾍｯﾀﾞｰ修正
###############################################################################

#define('DECOME_DEBUG_MODE',False);
#define('DECOME_DEBUG_MODE',True);

class decome {

  # ﾃﾞｺﾒﾓｰﾄﾞ有無効化
  # 　True :有効-因数指定に従います
  # 　False:無効-因数指定を無視し無効化します
  var $decome_flag = True;
  # SoftBank宛て送信時処理指定
  # 　0:ｲﾝﾗｲﾝ画像送信しない(HTMLﾓｰﾄﾞ)
  # 　1:ｲﾝﾗｲﾝ画像で送信(ﾃﾞｺﾒﾓｰﾄﾞ)
  var $softbank_inline = 0;

  # PC用制限設定
  var $inline_max_num_pc       = 0;    # ｲﾝﾗｲﾝ画像数制限
  var $inline_max_size_pc      = 0;    # ｲﾝﾗｲﾝ画像ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $inline_all_max_size_pc  = 0;    # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙﾌｧｲﾙｻｲｽﾞ制限(Byte)
  var $upfile_max_num_pc       = 0;    # 添付ﾌｧｲﾙ数制限
  var $upfile_max_size_pc      = 0;    # 添付ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $upfile_all_max_size_pc  = 0;    # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $allfile_max_num_pc      = 0;    # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙ数制限
  var $allfile_max_size_pc     = 0;    # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_max_size_pc        = 0;    # 本文(ﾃｷｽﾄ+HTML)ﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_all_max_size_pc    = 0;    # ﾄｰﾀﾙｻｲｽﾞ制限(Byte)

  # DoCoMo用制限設定
  var $inline_max_num_docomo       = 10;         # ｲﾝﾗｲﾝ画像数制限
  var $inline_max_size_docomo      = 10000;      # ｲﾝﾗｲﾝ画像ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $inline_all_max_size_docomo  = 0;          # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙﾌｧｲﾙｻｲｽﾞ制限(Byte)
  var $upfile_max_num_docomo       = 0;          # 添付ﾌｧｲﾙ数制限
  var $upfile_max_size_docomo      = 0;          # 添付ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $upfile_all_max_size_docomo  = 10240;      # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $allfile_max_num_docomo      = 0;          # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙ数制限
  var $allfile_max_size_docomo     = 0;          # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_max_size_docomo        = 10240;      # 本文(ﾃｷｽﾄ+HTML)ﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_all_max_size_docomo    = 1002400;    # ﾄｰﾀﾙｻｲｽﾞ制限(Byte)

  # au用制限設定
  var $inline_max_num_au       = 10;        # ｲﾝﾗｲﾝ画像数制限
  var $inline_max_size_au      = 0;         # ｲﾝﾗｲﾝ画像ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $inline_all_max_size_au  = 0;         # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙﾌｧｲﾙｻｲｽﾞ制限(Byte)
  var $upfile_max_num_au       = 0;         # 添付ﾌｧｲﾙ数制限
  var $upfile_max_size_au      = 0;         # 添付ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $upfile_all_max_size_au  = 102400;    # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $allfile_max_num_au      = 0;         # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙ数制限
  var $allfile_max_size_au     = 0;         # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_max_size_au        = 10000;     # 本文(ﾃｷｽﾄ+HTML)ﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_all_max_size_au    = 150000;    # ﾄｰﾀﾙｻｲｽﾞ制限(Byte)

  # SoftBank用制限設定
  var $inline_max_num_softbank       = 0;         # ｲﾝﾗｲﾝ画像数制限
  var $inline_max_size_softbank      = 0;         # ｲﾝﾗｲﾝ画像ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $inline_all_max_size_softbank  = 0;         # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙﾌｧｲﾙｻｲｽﾞ制限(Byte)
  var $upfile_max_num_softbank       = 0;         # 添付ﾌｧｲﾙ数制限
  var $upfile_max_size_softbank      = 0;         # 添付ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $upfile_all_max_size_softbank  = 0;         # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $allfile_max_num_softbank      = 0;         # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙ数制限
  var $allfile_max_size_softbank     = 307200;    # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_max_size_softbank        = 0;         # 本文(ﾃｷｽﾄ+HTML)ﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_all_max_size_softbank    = 0;         # ﾄｰﾀﾙｻｲｽﾞ制限(Byte)

  # ｴﾗｰ設定
  var $error_flag        = False;
  var $error_code        = 0;
  var $error_coment      = '';
  var $file_error_flag   = False;
  var $file_error_code   = 0;
  var $file_error_coment = '';

  # ｺﾝｽﾄﾗｸﾀ ///////////////////////////////////////////////////////////////////
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function decome() {

  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ取得 ///////////////////////////////////////////////
  # ｷｬﾘｱ判別と機種情報を取得します。(新処理->推奨)
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　$this->ver : ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ
  #////////////////////////////////////////////////////////////////////////////
  function Get_Emj_Version() {
    global $decome_ver;
    return $decome_ver;
  }

  # 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙ送信(mail関数送信) ///////////////////////////////////////
  # ﾃﾞｺﾚｰｼｮﾝﾒｰﾙ(絵文字)を送信します。
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
  # 　$katakana_chg_cancel                       : 件名･本文半角ｶﾀｶﾅ全角変換ｷｬﾝｾﾙ(指定なし:強制変換,1:変換ｷｬﾝｾﾙ)
  # [返り値]
  # 　True : 送信成功、False : 送信失敗
  #////////////////////////////////////////////////////////////////////////////
  function emoji_decome($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel='') {
    global $smtp_obj;

    # ｴﾗｰ初期化
    $this->error_flag   = False;
    $this->error_code   = 0;
    $this->error_coment = '';

    # 初期設定
    if (!isset($MAIL_DATA['from_add'])) {
      $this->error_flag   = True;
      $this->error_code   = 100;
      $this->error_coment = 'No Form Address.';
      return False;
    }
    if (!isset($MAIL_DATA['from_name']))                    { $MAIL_DATA['from_name']                    = ''; }
    if (!isset($MAIL_DATA['repry_name']))                   { $MAIL_DATA['repry_name']                   = ''; }
    if (!isset($MAIL_DATA['repry_to']))                     { $MAIL_DATA['repry_to']                     = ''; }
    if (!isset($MAIL_DATA['return_path']))                  { $MAIL_DATA['return_path']                  = ''; }
    if (!isset($MAIL_DATA['subject']))                      { $MAIL_DATA['subject']                      = ''; }
    if (!isset($MAIL_DATA['body_plain']))                   { $MAIL_DATA['body_plain']                   = ''; }
    if (!isset($MAIL_DATA['body_html']))                    { $MAIL_DATA['body_html']                    = ''; }
    if (!isset($SETTING_DATA['decome_mode']))               { $SETTING_DATA['decome_mode']               = ''; }
    if (!isset($SETTING_DATA['to_career']))                 { $SETTING_DATA['to_career']                 = 'PC'; }
    if (!isset($SETTING_DATA['content_transfer_encoding'])) { $SETTING_DATA['content_transfer_encoding'] = ''; }
    if (!isset($SETTING_DATA['mail_code']))                 { $SETTING_DATA['mail_code']                 = 'JIS'; }
    if (!isset($SETTING_DATA['encode_pass']))               { $SETTING_DATA['encode_pass']               = ''; }
    if (!isset($SETTING_DATA['input_code']))                { $SETTING_DATA['input_code']                = ''; }

    # ﾒｰﾙﾃﾞｰﾀ生成
    $MAIL = $this->make_mail_data($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel);

    # Debug Mode
    if (DECOME_DEBUG_MODE == True) {
      header('Content-Type: text/plain; charset=ISO-2022-JP');
      print "To     =>".$MAIL['set_to']."\n";
      print "Return =>".$MAIL['return_path']."\n";
      print "Subject=>".$MAIL['subject']."\n";
      print "header =>".$MAIL['add_mail_header']."\n";
      print "Body=>\n";
      print $MAIL['mail_body'];
      exit();
    }

    # ﾒｰﾙ送信
    if ($MAIL['error'] == False) {
      if ((EMOJI_smtp_flag == 1) and is_object($smtp_obj)) {
        # SMTP送信
        # 送信内容設定
        $smtp_obj->TOLIST           = $MAIL_DATA['TODATA'];
        $smtp_obj->CCLILST          = $MAIL_DATA['CCDATA'];
        $smtp_obj->BCCLIST          = $MAIL_DATA['BCCDATA'];
        $smtp_obj->from_name        = $MAIL_DATA['from_name'];
        $smtp_obj->from_address     = $MAIL_DATA['from_add'];
        $smtp_obj->reply_to_name    = $MAIL_DATA['repry_name'];
        $smtp_obj->reply_to_address = $MAIL_DATA['repry_to'];
        $smtp_obj->return_path      = $MAIL['return_path'];
        $smtp_obj->add_header       = $MAIL['add_mail_header'];
        $smtp_obj->subject          = $MAIL['subject'];
        $smtp_obj->body             = $MAIL['mail_body'];
        # ﾒｰﾙ送信
        $success = $smtp_obj->smtp_mail();
      } else {
        # PHP mail関数送信
        $success = @mail($MAIL['set_to'],$MAIL['subject'],$MAIL['mail_body'],$MAIL['add_mail_header'],'-f'.$MAIL['return_path']);
      }
#      if (@mail($MAIL['set_to'],$MAIL['subject'],$MAIL['mail_body'],$MAIL['add_mail_header'],'-f'.$MAIL['return_path'])) {
      if ($success == True) {
        return True;
      } else {
        $this->error_flag   = True;
        $this->error_code   = 101;
        $this->error_coment = 'Mail Send Error.';
        return False;
      }
    } else {
      $this->error_flag   = True;
#      $this->error_coment = 'Mail Data Make Error.';
      return False;
    }

  }

  # 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙﾃﾞｰﾀ生成 /////////////////////////////////////////////////
  # ﾃﾞｺﾚｰｼｮﾝﾒｰﾙ(絵文字)の送信ﾃﾞｰﾀを生成します。
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
  # 　$katakana_chg_cancel                       : 件名･本文半角ｶﾀｶﾅ全角変換ｷｬﾝｾﾙ(指定なし:強制変換,1:変換ｷｬﾝｾﾙ)
  # [返り値]
  # 　$MAIL                      : ﾒｰﾙ生成ﾃﾞｰﾀ
  # 　　$MAIL['error']           : ｴﾗｰﾌﾗｸﾞ(True:ｴﾗｰ有り、False:ｴﾗｰ無し)
  # 　　$MAIL['set_to']          : 送信先ﾃﾞｰﾀ(To)
  # 　　$MAIL['return_path']     : 不達ﾒｰﾙｱﾄﾞﾚｽ
  # 　　$MAIL['subject']         : 件名
  # 　　$MAIL['add_mail_header'] : ﾒｰﾙ追加ﾍｯﾀﾞｰ
  # 　　$MAIL['mail_body']       : ﾒｰﾙ本文
  #////////////////////////////////////////////////////////////////////////////
  function make_mail_data($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel='') {
    global $emoji_obj;

    # ｴﾗｰ初期化
    $this->error_flag   = False;
    $this->error_code   = 0;
    $this->error_coment = '';

    # 初期設定
    $MAIL = array();
    if (!isset($MAIL_DATA['from_add'])) {
      $this->error_flag   = True;
      $this->error_code   = 200;
      $this->error_coment = 'No From Address.';
      $MAIL['error']      = True;
      return $MAIL;
    }
    if (!isset($MAIL_DATA['from_name']))                    { $MAIL_DATA['from_name']                    = ''; }
    if (!isset($MAIL_DATA['repry_name']))                   { $MAIL_DATA['repry_name']                   = ''; }
    if (!isset($MAIL_DATA['repry_to']))                     { $MAIL_DATA['repry_to']                     = ''; }
    if (!isset($MAIL_DATA['return_path']))                  { $MAIL_DATA['return_path']                  = ''; }
    if (!isset($MAIL_DATA['subject']))                      { $MAIL_DATA['subject']                      = ''; }
    if (!isset($MAIL_DATA['body_plain']))                   { $MAIL_DATA['body_plain']                   = ''; }
    if (!isset($MAIL_DATA['body_html']))                    { $MAIL_DATA['body_html']                    = ''; }
    if (!isset($SETTING_DATA['decome_mode']))               { $SETTING_DATA['decome_mode']               = ''; }
    if (!isset($SETTING_DATA['to_career']))                 { $SETTING_DATA['to_career']                 = 'PC'; }
    if (!isset($SETTING_DATA['content_transfer_encoding'])) { $SETTING_DATA['content_transfer_encoding'] = ''; }
    if (!isset($SETTING_DATA['mail_code']))                 { $SETTING_DATA['mail_code']                 = 'JIS'; }
    if (!isset($SETTING_DATA['encode_pass']))               { $SETTING_DATA['encode_pass']               = ''; }
    if (!isset($SETTING_DATA['input_code']))                { $SETTING_DATA['input_code']                = ''; }

    # ﾓｰﾄﾞ設定
    if ($this->decome_flag == False) { $SETTING_DATA['decome_mode'] = ''; }
#    # SoftBank用ﾓｰﾄﾞ設定
#    if (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $emoji_obj->softbank_name)) { $SETTING_DATA['decome_mode'] = ''; }

    # 送信先ﾁｪｯｸ
    $to_flag  = False;
    $cc_flag  = False;
    $bcc_flag = False;
    $flag     = False;
    if (isset($MAIL_DATA['TODATA'])) {
      if (is_array($MAIL_DATA['TODATA'])) {
        if (isset($MAIL_DATA['TODATA'])) { $flag = True; $to_flag = True; }
      }
    }
    # 送信先ﾁｪｯｸ
    if (isset($MAIL_DATA['CCDATA'])) {
      if (is_array($MAIL_DATA['CCDATA'])) {
        if (isset($MAIL_DATA['CCDATA'])) { $flag = True; $cc_flag = True; }
      }
    }
    # 同報送信ﾁｪｯｸ
    if (isset($MAIL_DATA['BCCDATA'])) {
      if (is_array($MAIL_DATA['BCCDATA'])) {
        if (isset($MAIL_DATA['BCCDATA'])) { $flag = True; $bcc_flag = True; }
      }
    }
    if ($flag == False) {
      $this->error_flag   = True;
      $this->error_flag   = 201;
      $this->error_coment = 'To or CC or BCC Address Set Error.';
      return False;
    }

    # 返信先名ﾁｪｯｸ
    if ($MAIL_DATA['repry_name'] == '')  { $MAIL_DATA['repry_name']  = $MAIL_DATA['from_name']; }
    # 返信先名ﾁｪｯｸ
    if ($MAIL_DATA['repry_to'] == '')    { $MAIL_DATA['repry_to']    = $MAIL_DATA['from_add']; }
    # 不達ﾒｰﾙ戻り先ﾁｪｯｸ
    if ($MAIL_DATA['return_path'] == '') { $MAIL_DATA['return_path'] = $MAIL_DATA['from_add']; }

    # 本文ﾁｪｯｸ
    if (($MAIL_DATA['body_plain'] == '') and ($MAIL_DATA['body_html'] == '')) {
      $this->error_flag   = True;
      $this->error_flag   = 202;
      $this->error_coment = 'No Body Data.';
      return False;
    }

    # 添付ﾌｧｲﾙﾁｪｯｸ
    $upfile_flag = False;
    if (isset($UPFILE)) {
      if (is_array($UPFILE)) {
        foreach ($UPFILE as $pathdt => $namedt) {
          if (isset($pathdt)) {
            if (file_exists($pathdt)) { $upfile_flag = True; break; }
          }
        }
      }
    }

    # 送信ｴﾝｺｰﾄﾞ設定
    if ($SETTING_DATA['content_transfer_encoding'] == '') {
      if (isset($emoji_obj->cont_trs_enc)) {
        if ($emoji_obj->cont_trs_enc == '') {
          $SETTING_DATA['content_transfer_encoding'] = $emoji_obj->cont_trs_enc;
        } else {
          $SETTING_DATA['content_transfer_encoding'] = '7bit';
        }
      } else {
        $SETTING_DATA['content_transfer_encoding'] = '7bit';
      }
    }

    # 送信先(To句)生成
    $sp     = '';
    $set_to = '';
    if ($to_flag == True) {
      foreach ($MAIL_DATA['TODATA'] as $adddt => $namedt) {
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
      foreach ($MAIL_DATA['CCDATA'] as $adddt => $namedt) {
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
      foreach ($MAIL_DATA['BCCDATA'] as $adddt => $namedt) {
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
    if ($MAIL_DATA['from_name'] != '') {
      $str_code = mb_detect_encoding($MAIL_DATA['from_name'],$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if ($str_code == 'JIS') {
        $set_form = $MAIL_DATA['from_name'];
      } else {
        $set_form = @mb_convert_encoding($MAIL_DATA['from_name'],'JIS',$str_code);
      }
      $set_form  = mb_convert_kana($set_form,'KV','JIS');
      $set_form  = mb_encode_mimeheader($set_form,'JIS');
      $set_form .= ' <'.$MAIL_DATA['from_add'].'>';
    } else {
      $set_form = $MAIL_DATA['from_add'];
    }

    # 返信先(Repry_to句)生成
    $set_repry_to = '';
    if ($MAIL_DATA['repry_name'] != '') {
      $str_code = mb_detect_encoding($MAIL_DATA['repry_name'],$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if ($str_code == 'JIS') {
        $set_repry_to  = $MAIL_DATA['repry_name'];
      } else {
        $set_repry_to  = @mb_convert_encoding($MAIL_DATA['repry_name'],'JIS',$str_code);
      }
      $set_repry_to  = mb_convert_kana($set_repry_to,'KV','JIS');
      $set_repry_to  = mb_encode_mimeheader($set_repry_to,'JIS');
      $set_repry_to .= " <".$MAIL_DATA['repry_to'].">";
    } else {
      $set_repry_to = $MAIL_DATA['repry_to'];
    }

    # ﾒｰﾙ送信用絵文字変換(ｴﾝｺｰﾄﾞ)
    if ($SETTING_DATA['encode_pass'] != '1') {
      $MAIL_DATA['subject']    = $emoji_obj->emj_encode($MAIL_DATA['subject']   ,'','',$SETTING_DATA['input_code']);
      $MAIL_DATA['body_plain'] = $emoji_obj->emj_encode($MAIL_DATA['body_plain'],'','',$SETTING_DATA['input_code']);
      $MAIL_DATA['body_html']  = $emoji_obj->emj_encode($MAIL_DATA['body_html'] ,'','',$SETTING_DATA['input_code']);
    }

    # 文字ｺｰﾄﾞ取得
    $subject_code    = mb_detect_encoding($MAIL_DATA['subject']   ,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    $body_plain_code = mb_detect_encoding($MAIL_DATA['body_plain'],$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    $body_html_code  = mb_detect_encoding($MAIL_DATA['body_html'] ,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    if ($subject_code    != '') { $subject_code    = mb_preferred_mime_name($subject_code); }
    if ($body_plain_code != '') { $body_plain_code = mb_preferred_mime_name($body_plain_code); }
    if ($body_html_code  != '') { $body_html_code  = mb_preferred_mime_name($body_html_code); }

    # 文字ｺｰﾄﾞ変換
    if ($subject_code    != mb_preferred_mime_name($SETTING_DATA['mail_code'])) { $MAIL_DATA['subject']    = @mb_convert_encoding($MAIL_DATA['subject']   ,$SETTING_DATA['mail_code'],$subject_code); }
    if ($body_plain_code != mb_preferred_mime_name($SETTING_DATA['mail_code'])) { $MAIL_DATA['body_plain'] = @mb_convert_encoding($MAIL_DATA['body_plain'],$SETTING_DATA['mail_code'],$body_plain_code); }
    if ($body_html_code  != mb_preferred_mime_name($SETTING_DATA['mail_code'])) { $MAIL_DATA['body_html']  = @mb_convert_encoding($MAIL_DATA['body_html'] ,$SETTING_DATA['mail_code'],$body_html_code); }

    # ｶﾀｶﾅ変換
    if ($katakana_chg_cancel == '') {
      $MAIL_DATA['subject']    = mb_convert_kana($MAIL_DATA['subject']   ,'KV',$SETTING_DATA['mail_code']);
      $MAIL_DATA['body_plain'] = mb_convert_kana($MAIL_DATA['body_plain'],'KV',$SETTING_DATA['mail_code']);
      $MAIL_DATA['body_html']  = mb_convert_kana($MAIL_DATA['body_html'] ,'KV',$SETTING_DATA['mail_code']);
    }

    # 件名処理
    if ($MAIL_DATA['subject'] == '') { $MAIL_DATA['subject'] = @mb_convert_encoding('無題','JIS','SJIS'); }
    # 絵文字変換(ﾃﾞｺｰﾄﾞ)
    if (($SETTING_DATA['to_career'] == '') or ($SETTING_DATA['to_career'] == 'PC')) {
      # PC及び全ｷｬﾘｱ向けの場合(絵文字削除)
      $MAIL_DATA['subject'] = $emoji_obj->delete_emoji_code($MAIL_DATA['subject']);
    } elseif (($SETTING_DATA['to_career'] == 'Vodafone') or ($SETTING_DATA['to_career'] == 'SoftBank')) {
      # SoftBank3G向けの場合(絵文字削除)
      $MAIL_DATA['subject'] = $emoji_obj->delete_emoji_code($MAIL_DATA['subject']);
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $SUBJECT = $emoji_obj->emj_decode($MAIL_DATA['subject'],$SETTING_DATA['to_career'],$SETTING_DATA['mail_code']);
      $MAIL_DATA['subject'] = $SUBJECT['mail'];
    }
    $MAIL_DATA['subject'] = base64_encode($MAIL_DATA['subject']);
    $MAIL_DATA['subject'] = '=?ISO-2022-JP?B?'.$MAIL_DATA['subject'].'?=';

    # ﾒｰﾙﾓｰﾄﾞ取得
    $MAILMODE = $this->_get_mail_mode($MAIL_DATA['body_plain'],$MAIL_DATA['body_html'],$SETTING_DATA['to_career'],$SETTING_DATA['decome_mode'],$upfile_flag,$SETTING_DATA['content_transfer_encoding'],$SETTING_DATA['mail_code'],$SETTING_DATA['input_code']);
    $MAIL_DATA['body_plain'] = $MAILMODE['body_plain'];
    $MAIL_DATA['body_html']  = $MAILMODE['body_html'];

    # 本文容量ﾁｪｯｸ(ﾃｷｽﾄ本文+HTML)
    $mail_body_size = strlen($MAIL_DATA['body_plain']) + strlen($MAIL_DATA['body_html']);
    if ($SETTING_DATA['to_career'] == 'PC') {
      # PC用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_pc > 0) and ($this->body_all_max_size_pc < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 210;
        $this->error_coment = 'PC Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'DoCoMo') {
      # DoCoMo用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_docomo > 0) and ($this->body_all_max_size_docomo < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 211;
        $this->error_coment = 'DoCoMo Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'au') {
      # au用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_au > 0) and ($this->body_all_max_size_au < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 212;
        $this->error_coment = 'au Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $emoji_obj->softbank_name)) {
      # SoftBank用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_softbank > 0) and ($this->body_all_max_size_softbank < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 213;
        $this->error_coment = 'SoftBank Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    }

    # ｲﾝﾗｲﾝ画像取得
    $INLINEFILE = array();
    if ($SETTING_DATA['decome_mode'] == '1') {
      list($MAIL_DATA['body_html'],$INLINEFILE) = $this->_get_inline_img($MAIL_DATA['body_html'],$SETTING_DATA['to_career']);
      # ｲﾝﾗｲﾝ画像ﾁｪｯｸ
      if (!$this->_inline_check($INLINEFILE,$SETTING_DATA['to_career'])) {
        $this->error_flag   = True;
        $this->error_code   = 220;
        $this->error_coment = 'Inline Image Check Error.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    }

    # 添付ﾌｧｲﾙ取得
    list($upfile_flag,$UPFILELIST) = $this->_get_upfile($UPFILE);
    # 添付ﾌｧｲﾙﾁｪｯｸ
    if (!$this->_upfile_check($UPFILELIST,$SETTING_DATA['to_career'])) {
      $this->error_flag   = True;
      $this->error_code   = 221;
      $this->error_coment = 'Add File Check Error.';
      $MAIL['error']      = True;
      return $MAIL;
    }

    # ｲﾝﾗｲﾝ、添付ﾌｧｲﾙﾄｰﾀﾙﾁｪｯｸ
    if (!$this->_all_file_check($INLINEFILE,$UPFILELIST,$SETTING_DATA['to_career'])) {
      $this->error_flag   = True;
      $this->error_code   = 222;
      $this->error_coment = 'Inline Image And Add File Check Error.';
      $MAIL['error']      = True;
      return $MAIL;
    }

    # 共通ﾍｯﾀﾞｰ処理
    $add_mail_header  = '';
    $add_mail_header .= "From: ".$set_form."\n";
    $add_mail_header .= "Reply-To: ".$set_repry_to."\n";
    if ($set_cc  != '') { $add_mail_header .= "Cc: ".$set_cc."\n"; }
    if ($set_bcc != '') { $add_mail_header .= "Bcc: ".$set_bcc."\n"; }
    $add_mail_header .= "MIME-Version: 1.0\n";

    # 本文生成
    list($mail_header_ptn,$mail_body) = $this->_make_mail_body($MAILMODE['ptn_no'],$MAIL_DATA['body_plain'],$MAIL_DATA['body_html'],$SETTING_DATA['to_career'],$INLINEFILE,$UPFILELIST,$SETTING_DATA['decome_mode'],$upfile_flag,$SETTING_DATA['content_transfer_encoding'],$SETTING_DATA['mail_code'],$SETTING_DATA['input_code']);
    $add_mail_header .= $mail_header_ptn;

    # 本文容量ﾁｪｯｸ
    if ($SETTING_DATA['to_career'] == 'PC') {
      # PC用本文容量ﾁｪｯｸ
      if (($this->body_max_size_pc > 0) and ($this->body_max_size_pc < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 230;
        $this->error_coment = 'PC All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'DoCoMo') {
      # DoCoMo用本文容量ﾁｪｯｸ
      if (($this->body_max_size_docomo > 0) and ($this->body_max_size_docomo < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 231;
        $this->error_coment = 'DoCoMo All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'au') {
      # au用本文容量ﾁｪｯｸ
      if (($this->body_max_size_au > 0) and ($this->body_max_size_au < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 232;
        $this->error_coment = 'au All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $emoji_obj->softbank_name)) {
      # SoftBank用本文容量ﾁｪｯｸ
      if (($this->body_max_size_softbank > 0) and ($this->body_max_size_softbank < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 233;
        $this->error_coment = 'SoftBank All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    }

    # 返り値設定
    $MAIL['error']           = False;
    $MAIL['set_to']          = $set_to;
    $MAIL['return_path']     = $MAIL_DATA['return_path'];
    $MAIL['subject']         = $MAIL_DATA['subject'];
    $MAIL['mail_body']       = $mail_body;
    $MAIL['add_mail_header'] = $add_mail_header;

    return $MAIL;

  }

  # 絵文字ﾃﾞｺﾚｰｼｮﾝﾒｰﾙﾃﾞｰﾀ簡易ﾁｪｯｸ /////////////////////////////////////////////
  # ﾃﾞｺﾚｰｼｮﾝﾒｰﾙ(絵文字)の送信ﾃﾞｰﾀを簡易ﾁｪｯｸします。
  # [引渡し値]
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
  # 　$katakana_chg_cancel                       : 件名･本文半角ｶﾀｶﾅ全角変換ｷｬﾝｾﾙ(指定なし:強制変換,1:変換ｷｬﾝｾﾙ)
  # [返り値]
  # 　$MAIL                      : ﾒｰﾙ生成ﾃﾞｰﾀ
  # 　　$MAIL['error']           : ｴﾗｰﾌﾗｸﾞ(True:ｴﾗｰ有り、False:ｴﾗｰ無し)
  # 　　$MAIL['subject']         : 件名
  # 　　$MAIL['add_mail_header'] : ﾒｰﾙ追加ﾍｯﾀﾞｰ
  # 　　$MAIL['mail_body']       : ﾒｰﾙ本文
  #////////////////////////////////////////////////////////////////////////////
  function check_mail_data($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel='') {
    global $emoji_obj;

    # ｴﾗｰ初期化
    $this->error_flag   = False;
    $this->error_code   = 0;
    $this->error_coment = '';

    # 初期設定
    if (!isset($MAIL_DATA['subject']))                      { $MAIL_DATA['subject']                      = ''; }
    if (!isset($MAIL_DATA['body_plain']))                   { $MAIL_DATA['body_plain']                   = ''; }
    if (!isset($MAIL_DATA['body_html']))                    { $MAIL_DATA['body_html']                    = ''; }
    if (!isset($SETTING_DATA['decome_mode']))               { $SETTING_DATA['decome_mode']               = ''; }
    if (!isset($SETTING_DATA['to_career']))                 { $SETTING_DATA['to_career']                 = 'PC'; }
    if (!isset($SETTING_DATA['content_transfer_encoding'])) { $SETTING_DATA['content_transfer_encoding'] = ''; }
    if (!isset($SETTING_DATA['mail_code']))                 { $SETTING_DATA['mail_code']                 = 'JIS'; }
    if (!isset($SETTING_DATA['encode_pass']))               { $SETTING_DATA['encode_pass']               = ''; }
    if (!isset($SETTING_DATA['input_code']))                { $SETTING_DATA['input_code']                = ''; }

    # ﾓｰﾄﾞ設定
    if ($this->decome_flag == False) { $SETTING_DATA['decome_mode'] = ''; }
    # SoftBank用ﾓｰﾄﾞ設定
    if (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $emoji_obj->softbank_name)) { $SETTING_DATA['decome_mode'] = ''; }

    # 本文ﾁｪｯｸ
    if (($MAIL_DATA['body_plain'] == '') and ($MAIL_DATA['body_html'] == '')) {
      $this->error_flag   = True;
      $this->error_code   = 202;
      $this->error_coment = 'No Body Data.';
      return False;
    }

    # 添付ﾌｧｲﾙﾁｪｯｸ
    $upfile_flag = False;
    if (isset($UPFILE)) {
      if (is_array($UPFILE)) {
        foreach ($UPFILE as $pathdt => $namedt) {
          if (isset($pathdt)) {
            if (file_exists($pathdt)) { $upfile_flag = True; break; }
          }
        }
      }
    }

    # 送信ｴﾝｺｰﾄﾞ設定
    if ($SETTING_DATA['content_transfer_encoding'] == '') {
      if (isset($emoji_obj->cont_trs_enc)) {
        if ($emoji_obj->cont_trs_enc == '') {
          $SETTING_DATA['content_transfer_encoding'] = $emoji_obj->cont_trs_enc;
        } else {
          $SETTING_DATA['content_transfer_encoding'] = '7bit';
        }
      } else {
        $SETTING_DATA['content_transfer_encoding'] = '7bit';
      }
    }

    # ﾒｰﾙ送信用絵文字変換(ｴﾝｺｰﾄﾞ)
    if ($SETTING_DATA['encode_pass'] != '1') {
      $MAIL_DATA['subject']    = $Gemoji_obj->emj_encode($MAIL_DATA['subject']   ,'','',$SETTING_DATA['input_code']);
      $MAIL_DATA['body_plain'] = $Gemoji_obj->emj_encode($MAIL_DATA['body_plain'],'','',$SETTING_DATA['input_code']);
      $MAIL_DATA['body_html']  = $Gemoji_obj->emj_encode($MAIL_DATA['body_html'] ,'','',$SETTING_DATA['input_code']);
    }

    # 文字ｺｰﾄﾞ取得
    $subject_code    = mb_detect_encoding($MAIL_DATA['subject']   ,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    $body_plain_code = mb_detect_encoding($MAIL_DATA['body_plain'],$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    $body_html_code  = mb_detect_encoding($MAIL_DATA['body_html'] ,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
    if ($subject_code    != '') { $subject_code    = mb_preferred_mime_name($subject_code); }
    if ($body_plain_code != '') { $body_plain_code = mb_preferred_mime_name($body_plain_code); }
    if ($body_html_code  != '') { $body_html_code  = mb_preferred_mime_name($body_html_code); }

    # 文字ｺｰﾄﾞ変換
    if ($subject_code    != mb_preferred_mime_name($SETTING_DATA['mail_code'])) { $MAIL_DATA['subject']    = @mb_convert_encoding($MAIL_DATA['subject']   ,$SETTING_DATA['mail_code'],$subject_code); }
    if ($body_plain_code != mb_preferred_mime_name($SETTING_DATA['mail_code'])) { $MAIL_DATA['body_plain'] = @mb_convert_encoding($MAIL_DATA['body_plain'],$SETTING_DATA['mail_code'],$body_plain_code); }
    if ($body_html_code  != mb_preferred_mime_name($SETTING_DATA['mail_code'])) { $MAIL_DATA['body_html']  = @mb_convert_encoding($MAIL_DATA['body_html'] ,$SETTING_DATA['mail_code'],$body_html_code); }

    # ｶﾀｶﾅ変換
    if ($katakana_chg_cancel == '') {
      $MAIL_DATA['subject']    = mb_convert_kana($MAIL_DATA['subject']   ,'KV',$SETTING_DATA['mail_code']);
      $MAIL_DATA['body_plain'] = mb_convert_kana($MAIL_DATA['body_plain'],'KV',$SETTING_DATA['mail_code']);
      $MAIL_DATA['body_html']  = mb_convert_kana($MAIL_DATA['body_html'] ,'KV',$SETTING_DATA['mail_code']);
    }

    # 件名処理
    if ($MAIL_DATA['subject'] == '') { $MAIL_DATA['subject'] = @mb_convert_encoding('無題','JIS','SJIS'); }
    # 絵文字変換(ﾃﾞｺｰﾄﾞ)
    if (($SETTING_DATA['to_career'] == '') or ($SETTING_DATA['to_career'] == 'PC')) {
      # PC及び全ｷｬﾘｱ向けの場合(絵文字削除)
      $MAIL_DATA['subject'] = $emoji_obj->delete_emoji_code($MAIL_DATA['subject']);
    } elseif (($SETTING_DATA['to_career'] == 'Vodafone') or ($SETTING_DATA['to_career'] == 'SoftBank')) {
      # SoftBank宛ての場合(絵文字削除)
      $MAIL_DATA['subject'] = $emoji_obj->delete_emoji_code($MAIL_DATA['subject']);
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $SUBJECT = $emoji_obj->emj_decode($MAIL_DATA['subject'],$SETTING_DATA['to_career'],$SETTING_DATA['mail_code']);
      $MAIL_DATA['subject'] = $SUBJECT['mail'];
    }
    $MAIL_DATA['subject'] = base64_encode($MAIL_DATA['subject']);
    $MAIL_DATA['subject'] = '=?ISO-2022-JP?B?'.$MAIL_DATA['subject'].'?=';

    # ﾒｰﾙﾓｰﾄﾞ取得
    $MAILMODE = $this->_get_mail_mode($MAIL_DATA['body_plain'],$MAIL_DATA['body_html'],$SETTING_DATA['to_career'],$SETTING_DATA['decome_mode'],$upfile_flag,$SETTING_DATA['content_transfer_encoding'],$SETTING_DATA['mail_code'],$SETTING_DATA['input_code']);
    $MAIL_DATA['body_plain'] = $MAILMODE['body_plain'];
    $MAIL_DATA['body_html']  = $MAILMODE['body_html'];

    # 本文容量ﾁｪｯｸ(ﾃｷｽﾄ本文+HTML)
    $mail_body_size = strlen($MAIL_DATA['body_plain']) + strlen($MAIL_DATA['body_html']);
    if ($SETTING_DATA['to_career'] == 'PC') {
      # PC用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_pc > 0) and ($this->body_all_max_size_pc < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 210;
        $this->error_coment = 'PC Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'DoCoMo') {
      # DoCoMo用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_docomo > 0) and ($this->body_all_max_size_docomo < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 211;
        $this->error_coment = 'DoCoMo Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'au') {
      # au用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_au > 0) and ($this->body_all_max_size_au < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 212;
        $this->error_coment = 'au Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $emoji_obj->softbank_name)) {
      # SoftBank用本文容量ﾁｪｯｸ
      if (($this->body_all_max_size_softbank > 0) and ($this->body_all_max_size_softbank < $mail_body_size)) {
        $this->error_flag   = True;
        $this->error_code   = 213;
        $this->error_coment = 'SoftBank Body(Text and HTML) Size Orver.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    }
    # ｲﾝﾗｲﾝ画像取得
    $INLINEFILE = array();
    if ($SETTING_DATA['decome_mode'] == '1') {
      list($MAIL_DATA['body_html'],$INLINEFILE) = $this->_get_inline_img($MAIL_DATA['body_html'],$SETTING_DATA['to_career']);
      if ($this->file_error_flag == True) {
        $this->error_flag   = True;
        $this->error_code   = $this->file_error_code;
        $this->error_coment = $this->file_error_coment;
        $MAIL['error']      = True;
        return $MAIL;
      } else {
        # ｲﾝﾗｲﾝ画像ﾁｪｯｸ
        if (!$this->_inline_check($INLINEFILE,$SETTING_DATA['to_career'])) {
          $this->error_flag   = True;
          $this->error_code   = 220;
          $this->error_coment = 'Inline Image Check Error.';
          $MAIL['error']      = True;
          return $MAIL;
        }
      }
    }

    # 添付ﾌｧｲﾙ取得
    list($upfile_flag,$UPFILELIST) = $this->_get_upfile($UPFILE);
    if ($this->file_error_flag == True) {
      $this->error_flag   = True;
      $this->error_code   = $this->file_error_code;
      $this->error_coment = $this->file_error_coment;
      $MAIL['error']      = True;
      return $MAIL;
    } else {
      # 添付ﾌｧｲﾙﾁｪｯｸ
      if (!$this->_upfile_check($UPFILELIST,$SETTING_DATA['to_career'])) {
        $this->error_flag   = True;
        $this->error_code   = 221;
        $this->error_coment = 'Add File Check Error.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    }

    # ｲﾝﾗｲﾝ、添付ﾌｧｲﾙﾄｰﾀﾙﾁｪｯｸ
    if (!$this->_all_file_check($INLINEFILE,$UPFILELIST,$SETTING_DATA['to_career'])) {
      $this->error_flag   = True;
      $this->error_code   = 222;
      $this->error_coment = 'Inline Image And Add File Check Error.';
      $MAIL['error']      = True;
      return $MAIL;
    }

    # 共通ﾍｯﾀﾞｰ処理
    $add_mail_header  = '';

    # 本文生成
    list($mail_header_ptn,$mail_body) = $this->_make_mail_body($MAILMODE['ptn_no'],$MAIL_DATA['body_plain'],$MAIL_DATA['body_html'],$SETTING_DATA['to_career'],$INLINEFILE,$UPFILELIST,$SETTING_DATA['decome_mode'],$upfile_flag,$SETTING_DATA['content_transfer_encoding'],$SETTING_DATA['mail_code'],$SETTING_DATA['input_code']);
    $add_mail_header .= $mail_header_ptn;

    # 本文容量ﾁｪｯｸ
    if ($SETTING_DATA['to_career'] == 'PC') {
      # PC用本文容量ﾁｪｯｸ
      if (($this->body_max_size_pc > 0) and ($this->body_max_size_pc < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 230;
        $this->error_coment = 'PC All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'DoCoMo') {
      # DoCoMo用本文容量ﾁｪｯｸ
      if (($this->body_max_size_docomo > 0) and ($this->body_max_size_docomo < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 231;
        $this->error_coment = 'DoCoMo All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif ($SETTING_DATA['to_career'] == 'au') {
      # au用本文容量ﾁｪｯｸ
      if (($this->body_max_size_au > 0) and ($this->body_max_size_au < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 232;
        $this->error_coment = 'au All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $emoji_obj->softbank_name)) {
      # SoftBank用本文容量ﾁｪｯｸ
      if (($this->body_max_size_softbank > 0) and ($this->body_max_size_softbank < strlen($mail_body))) {
        $this->error_flag   = True;
        $this->error_code   = 233;
        $this->error_coment = 'SoftBank All Body Size Order.';
        $MAIL['error']      = True;
        return $MAIL;
      }
    }

    # 返り値設定
    $MAIL['error']           = False;
    $MAIL['subject']         = $MAIL_DATA['subject'];
    $MAIL['mail_body']       = $mail_body;
    $MAIL['add_mail_header'] = $add_mail_header;

    return $MAIL;

  }

  # ﾒｰﾙﾓｰﾄﾞ設定 //////////////////////////////////////////////////////////////
  # 内容によるﾒｰﾙﾓｰﾄﾞを取得します。
  # [引渡し値]
  # 　$body_plain  : ﾃｷｽﾄ本文
  # 　$body_html   : HTML本文
  # 　$to_career   : 送信先ｷｬﾘｱ
  # 　$decome_mode : ﾃﾞｺﾒﾓｰﾄﾞ指定
  # 　$upfile_flag : 添付ﾌｧｲﾙﾌﾗｸﾞ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # 　$mail_code   : ﾒｰﾙ本文文字ｺｰﾄﾞ指定(指定なし又は'JIS':JIS)
  # 　$input_code  : 入力ｺｰﾄﾞ
  # [返り値]
  # 　$ptn_no : 整形後値
  #////////////////////////////////////////////////////////////////////////////
  function _get_mail_mode($body_plain,$body_html,$to_career,$decome_mode,$upfile_flag,$content_transfer_encoding,$mail_code,$input_code) {
    global $emoji_obj;

    $RETURN           = array();
    $RETURN['ptn_no'] = '';
    $plain_flag       = '';

    # ﾒｰﾙﾀｲﾌﾟ設定
    if (($to_career == '') or ($to_career == 'PC')) {
      # PC宛て
      if (($body_plain == '') and ($body_html != '')) {
        # HTMLのみ
        if ($decome_mode == '1') { $img_num = preg_match('/<img\s/i',$body_html); } else { $img_num = 0; }
        if ($img_num == 0) {
          # ｲﾝﾗｲﾝ画像無し
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 11; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 10; }
        } elseif ($img_num > 0) {
          # ｲﾝﾗｲﾝ画像有り
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 13; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 12; }
        }
      } elseif (($body_plain != '') and ($body_html == '')) {
        # ﾃｷｽﾄのみ
        # 絵文字有無ﾁｪｯｸ
        $PLCOUNT = $emoji_obj->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # 絵文字が含まれている場合(ﾃｷｽﾄ→HTML本文)
          $body_html  = $body_plain;
          # URL･ﾒｰﾙｱﾄﾞﾚｽﾘﾝｸ化
          $body_html = link_make($body_html);
          # 絵文字削除
          $body_plain = $emoji_obj->delete_emoji_code($body_plain);
#          if ($upfile_flag == True) { $RETURN['ptn_no'] = 9; } else { $RETURN['ptn_no'] = 8; }
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } else { $RETURN['ptn_no'] = 7; }
          $plain_flag = '1';
        } else {
          # 絵文字が含まれていない場合
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 2; } else { $RETURN['ptn_no'] = 1; }
        }
      } elseif (($body_plain != '') and ($body_html != '')) {
        # ﾃｷｽﾄ + HTML
        $PLCOUNT = $emoji_obj->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # ﾃｷｽﾄ本文に絵文字が含まれている場合(絵文字ｶｯﾄ)
          $body_plain = $emoji_obj->delete_emoji_code($body_plain);
        }
        if ($decome_mode == '1') {
          # ﾃﾞｺﾒﾓｰﾄﾞ
          $HTCOUNT = $emoji_obj->emj_check($body_html,'',$input_code);
          $img_num = preg_match('/<img\s/i',$body_html);
        } else {
          # 通常ﾓｰﾄﾞ
          $HTCOUNT['total'] = 0;
          $img_num          = 0;
        }
        if (($HTCOUNT['total'] == 0) and ($img_num == 0)) {
          # 画像が含まれない場合
#          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 7; }
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 14; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 7; }
        } elseif (($HTCOUNT['total'] > 0) or ($img_num > 0)) {
          # 画像が含まれる場合
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 9; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 8; }
        }
      }

    } elseif ($to_career == 'DoCoMo') {
      # DoCoMo宛て
      if (($body_plain == '') and ($body_html != '')) {
        # HTMLのみ
        # ﾃｷｽﾄ本文設定
        $body_plain = strip_tags($body_html,'<br>');
        $body_plain = preg_replace('|<br\s*/*>|i',"\n",$body_plain);
        if ($decome_mode == '1') { $img_num = preg_match('/<img\s/i',$body_html); } else { $img_num = 0; }
        if ($img_num == 0) {
          # ｲﾝﾗｲﾝ画像無し
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 4; }
        } elseif ($img_num > 0) {
          # ｲﾝﾗｲﾝ画像有り
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 6; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 5; }
        }
      } elseif (($body_plain != '') and ($body_html == '')) {
        # ﾃｷｽﾄのみ
        if ($upfile_flag == True) { $RETURN['ptn_no'] = 2; } else { $RETURN['ptn_no'] = 1; }
      } elseif (($body_plain != '') and ($body_html != '')) {
        # ﾃｷｽﾄ + HTML
        if ($decome_mode == '1') { $img_num = preg_match('/<img\s/i',$body_html); } else { $img_num = 0; }
        if ($img_num == 0) {
          # ｲﾝﾗｲﾝ画像無し
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 4; }
        } elseif ($img_num > 0) {
          # ｲﾝﾗｲﾝ画像有り
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 6; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 5; }
        }
      }

    } elseif ($to_career == 'au') {
      # au宛て
      if (($body_plain == '') and ($body_html != '')) {
        # HTMLのみ
        # ﾃｷｽﾄ本文設定
        $body_plain = strip_tags($body_html,'<br>');
        $body_plain = preg_replace('|<br\s*/*>|i',"\n",$body_plain);
        if ($decome_mode == '1') { $img_num = preg_match('/<img\s/i',$body_html); } else { $img_num = 0; }
        if ($img_num == 0) {
          # ｲﾝﾗｲﾝ画像無し
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 4; }
        } elseif ($img_num > 0) {
          # ｲﾝﾗｲﾝ画像有り
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 6; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 5; }
        }
      } elseif (($body_plain != '') and ($body_html == '')) {
        # ﾃｷｽﾄのみ
        if ($upfile_flag == True) { $RETURN['ptn_no'] = 2; } else { $RETURN['ptn_no'] = 1; }
      } elseif (($body_plain != '') and ($body_html != '')) {
        # ﾃｷｽﾄ + HTML
        if ($decome_mode == '1') { $img_num = preg_match('/<img\s/i',$body_html); } else { $img_num = 0; }
        if ($img_num == 0) {
          # ｲﾝﾗｲﾝ画像無し
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 4; }
        } elseif ($img_num > 0) {
          # ｲﾝﾗｲﾝ画像有り
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 6; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 5; }
        }
      }

    } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      # SoftBank宛て
      if (($body_plain == '') and ($body_html != '')) {
        # HTMLのみ
        # ﾃｷｽﾄ本文設定
        $body_plain = strip_tags($body_html,'<br>');
        $body_plain = preg_replace('|<br\s*/*>|i',"\n",$body_plain);
        # HTML内画像数ﾁｪｯｸ
        if ($decome_mode == '1') { $img_num = preg_match('/<img\s/i',$body_html); } else { $img_num = 0; }
#        $PLCOUNT = $emoji_obj->emj_check($body_plain,'',$input_code);
#        if (($PLCOUNT['total'] == 0) and ($img_num == 0)) {
        if ($img_num == 0) {
          # ｲﾝﾗｲﾝ画像無し
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 4; }
        } else {
          # ｲﾝﾗｲﾝ画像有り
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 6; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 5; }
        }
      } elseif (($body_plain != '') and ($body_html == '')) {
        # ﾃｷｽﾄのみ
        # 絵文字有無ﾁｪｯｸ
        $PLCOUNT = $emoji_obj->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # 絵文字が含まれている場合(ﾃｷｽﾄ→HTML本文)
          $body_html = $body_plain;
          # URL･ﾒｰﾙｱﾄﾞﾚｽﾘﾝｸ化
          $body_html = link_make($body_html);
          # ﾃｷｽﾄﾊﾟｰﾄ絵文字削除
          $body_plain = $emoji_obj->delete_emoji_code($body_plain);
#          if ($upfile_flag == True) { $RETURN['ptn_no'] = 9; } else { $RETURN['ptn_no'] = 8; }
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } else { $RETURN['ptn_no'] = 4; }
          $plain_flag = '1';
        } else {
          # 絵文字が含まれていない場合
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 2; } else { $RETURN['ptn_no'] = 1; }
        }
      } elseif (($body_plain != '') and ($body_html != '')) {
        # ﾃｷｽﾄ + HTML
        $PLCOUNT = $emoji_obj->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # ﾃｷｽﾄ本文に絵文字が含まれている場合(絵文字ｶｯﾄ)
          $body_plain = $emoji_obj->delete_emoji_code($body_plain);
        }
        if ($decome_mode == '1') {
          # ﾃﾞｺﾒﾓｰﾄﾞ
          $HTCOUNT = $emoji_obj->emj_check($body_html,'',$input_code);
          $img_num = preg_match('/<img\s/i',$body_html);
        } else {
          # 通常ﾓｰﾄﾞ
          $HTCOUNT['total'] = 0;
          $img_num          = 0;
        }
#        if (($HTCOUNT['total'] == 0) and ($img_num == 0)) {
        if ($img_num == 0) {
          # 画像が含まれない場合
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 4; }
#        } elseif (($HTCOUNT['total'] > 0) or ($img_num > 0)) {
        } elseif ($img_num > 0) {
          # 画像が含まれる場合
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 6; } elseif ($upfile_flag == False) { $RETURN['ptn_no'] = 5; }
        }
      }

    }

    # 本文処理
    $body_plain = $this->_body_plain_make($body_plain,$to_career);
    $body_html  = $this->_body_html_make($body_html,$to_career,$mail_code,$plain_flag);

    # 絵文字ﾃﾞｺｰﾄﾞ
    if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      $BODYPLAIN  = $emoji_obj->emj_decode($body_plain,$to_career,$mail_code);
      $body_plain = $BODYPLAIN['mail_plain'];
      $body_html  = mb_convert_encoding($body_html,'SJIS','JIS');
      $BODYHTML   = $emoji_obj->emj_decode($body_html,$to_career,'UTF-8');
    } else {
      $BODYPLAIN  = $emoji_obj->emj_decode($body_plain,$to_career,$mail_code);
      $body_plain = $BODYPLAIN['mail'];
      $BODYHTML   = $emoji_obj->emj_decode($body_html,$to_career,$mail_code);
    }
    $body_html = $BODYHTML['mail'];

    # 絵文字画像ｺﾒﾝﾄ削除
    $body_html = preg_replace('/(<img\ssrc=\"[^>]+\"[^>]*)\stitle=\"[^>]+\"\salt=\"[^>]+\">/i','\\1>',$body_html);

    $RETURN['body_plain'] = $body_plain;
    $RETURN['body_html']  = $body_html;

    return $RETURN;
  }

  # ｲﾝﾗｲﾝ画像取得 //////////////////////////////////////////////////////////////
  # HTML本文内の画像を取得します。
  # [引渡し値]
  # 　$body_html : HTML本文
  # 　$to_career : 送信先ｷｬﾘｱ
  # [返り値]
  # 　$INLINE_IMGLIST : 取得ｲﾝﾗｲﾝ画像ﾘｽﾄ
  #////////////////////////////////////////////////////////////////////////////
  function _get_inline_img($body_html,$to_career) {
    global $emoji_obj,$emoji_mail_obj;

    $this->file_error_flag   = False;
    $this->file_error_code   = 0;
    $this->file_error_coment = '';

    $INLINE_IMGLIST = array();
    $body_html      = preg_replace('/\r/','',$body_html);
    $body_html_sub  = $body_html;
    $no             = 0;

    # <IMG>ﾀｸﾞ内画像取得
    while (preg_match('|(<img\s+src\s*=[\s\"\']*)(.+?)([\"\'\s>])|i',$body_html_sub,$MATCH)) {
      # ﾌｧｲﾙ読込み
      if ($filedata = @file($MATCH[2])) {
        $fdata = join('',$filedata);
        # CID設定
        $cid = 'img_cid_'.str_pad($no,3,'0',STR_PAD_LEFT).'@'.date('ymd.His',time());
        # ﾃﾞｰﾀ取得
        $PATHDATA = pathinfo($MATCH[2]);
        $INLINE_IMGLIST[$cid]['name'] = $PATHDATA['basename'];
        if ((isset($emoji_mail_obj) and is_object($emoji_mail_obj)) and method_exists($emoji_mail_obj,'get_mime_type')) {
          # ver.8用
          $INLINE_IMGLIST[$cid]['mime'] = $emoji_mail_obj->get_mime_type($MATCH[2]);
        } else {
          # ver.7用
          $INLINE_IMGLIST[$cid]['mime'] = $emoji_obj->get_mime_type($MATCH[2]);
        }
        $INLINE_IMGLIST[$cid]['size'] = strlen(base64_encode($fdata));
        $INLINE_IMGLIST[$cid]['data'] = chunk_split(base64_encode($fdata));
        # 本文調整
        $body_html_sub = preg_replace('|'.$MATCH[1].$MATCH[2].$MATCH[3].'|i','',$body_html_sub);
        $body_html     = preg_replace('|'.$MATCH[2].'|i','cid:'.$cid,$body_html);
      } else {
        $body_html_sub = preg_replace('|'.$MATCH[1].$MATCH[2].$MATCH[3].'|i','',$body_html_sub);
        $body_html     = preg_replace('|'.$MATCH[2].'|i','',$body_html);
        $this->file_error_flag   = True;
        $this->file_error_code   = 300;
        $this->file_error_coment = 'Imline Image No Link Error.';
      }
      $no++;
    }

    # <BODY>ﾀｸﾞ内画像取得
    if (preg_match('|(<body\s+background\s*=[\s\"\']*)(.+?)([\"\'\s>])|i',$body_html_sub,$MATCH)) {
      # ﾌｧｲﾙ読込み
      if ($filedata = @file($MATCH[2])) {
        $fdata = join('',$filedata);
        # CID設定
        $cid = 'img_cid_'.str_pad($no,3,'0',STR_PAD_LEFT).'@'.date('ymd.His',time());
        # ﾃﾞｰﾀ取得
        $PATHDATA = pathinfo($MATCH[2]);
        $INLINE_IMGLIST[$cid]['name'] = $PATHDATA['basename'];
        if ((isset($emoji_mail_obj) and is_object($emoji_mail_obj)) and method_exists($emoji_mail_obj,'get_mime_type')) {
          # ver.8用
          $INLINE_IMGLIST[$cid]['mime'] = $emoji_mail_obj->get_mime_type($MATCH[2]);
        } else {
          # ver.7用
          $INLINE_IMGLIST[$cid]['mime'] = $emoji_obj->get_mime_type($MATCH[2]);
        }
        $INLINE_IMGLIST[$cid]['size'] = strlen(base64_encode($fdata));
        $INLINE_IMGLIST[$cid]['data'] = chunk_split(base64_encode($fdata));
        # 本文調整
        $body_html = preg_replace('|'.$MATCH[2].'|i','cid:'.$cid,$body_html);
      } else {
        $body_html = preg_replace('|'.$MATCH[2].'|i','',$body_html);
        $this->file_error_flag   = True;
        $this->file_error_code   = 300;
        $this->file_error_coment = 'Inline Image No Link Error.';
      }
    }

    return array($body_html,$INLINE_IMGLIST);
  }

  # 添付ﾌｧｲﾙ取得 //////////////////////////////////////////////////////////////
  # 添付ﾌｧｲﾙを取得します。
  # [引渡し値]
  # 　$UPFILE : ｱｯﾌﾟﾛｰﾄﾞﾌｧｲﾙﾘｽﾄ
  # [返り値]
  # 　$UPFILELIST : 取得ﾌｧｲﾙﾘｽﾄ
  #////////////////////////////////////////////////////////////////////////////
  function _get_upfile($UPFILE) {
    global $emoji_obj,$emoji_mail_obj;

    $this->file_error_flag   = False;
    $this->file_error_code   = 0;
    $this->file_error_coment = '';

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
              if ((isset($emoji_mail_obj) and is_object($emoji_mail_obj)) and method_exists($emoji_mail_obj,'get_mime_type')) {
                # ver.8用
                $UPFILELIST[$no]['mime'] = $emoji_mail_obj->get_mime_type($pathdt);
              } else {
                # ver.7用
                $UPFILELIST[$no]['mime'] = $emoji_obj->get_mime_type($pathdt);
              }
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
              if ($fp = @fopen($pathdt,"r")) {
                $fdata = fread($fp,filesize($pathdt));
                fclose($fp);
              } else {
                $this->file_error_flag   = True;
                $this->file_error_code   = 301;
                $this->file_error_coment = 'Add File No Link Error.';
              }
              # ｴﾝｺｰﾄﾞして分割
              $UPFILELIST[$no]['size']     = strlen(base64_encode($fdata));
              $UPFILELIST[$no]['filedata'] = chunk_split(base64_encode($fdata));
              $upfile_flag = True;
              $no++;
            }
          }
        }
      }
    }

    return array($upfile_flag,$UPFILELIST);
  }

  # ﾃｷｽﾄﾃﾞｰﾀ整形 //////////////////////////////////////////////////////////////
  # ﾃｷｽﾄ本文を整形します。
  # [引渡し値]
  # 　$body_plain : 整形前値
  # 　$to_career  : 送信先ｷｬﾘｱ
  # [返り値]
  # 　$body_plain : 整形後値
  #////////////////////////////////////////////////////////////////////////////
  function _body_plain_make($body_plain,$to_career) {

    # ﾃﾞｰﾀ末改行処理
    if (($body_plain != '') and !preg_match('/\n$/',$body_plain)) { $body_plain .= "\n"; }

    return $body_plain;
  }

  # HTMLﾃﾞｰﾀ整形 //////////////////////////////////////////////////////////////
  # HTML本文を整形します。
  # [引渡し値]
  # 　$body_html       : 整形前値
  # 　$to_career       : 送信先ｷｬﾘｱ
  # 　$mail_code       : ﾒｰﾙ文字ｺｰﾄﾞ
  # 　$body_plain_flag : 元のﾃﾞｰﾀがﾃｷｽﾄ本文の場合'1'
  # [返り値]
  # 　$body_html : 整形後値
  #////////////////////////////////////////////////////////////////////////////
  function _body_html_make($body_html,$to_career,$mail_code,$body_plain_flag='') {
    global $emoji_obj;

    # 元ﾃﾞｰﾀ準備
    $body_html = preg_replace('/\r/','',$body_html);

    # 元ﾃﾞｰﾀがﾃｷｽﾄの場合
    if ($body_plain_flag == '1') {
      $body_html = preg_replace('/\n/','<br />',$body_html);
      $body_html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-2022-jp\"></head><body>".$body_html."</body></html>";
    }

    # HTMLﾍｯﾀﾞｰﾁｪｯｸ
    if (!eregi('<html>.+</html>',$body_html)) {
      if (eregi('<body.+</body>',$body_html)) {
        if (eregi('<head>.+</head>',$body_html)) {
          $body_html = "<html>".$body_html."</html>";
        } else {
          $body_html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-2022-jp\"></head>".$body_html."</html>";
        }
      } else {
        $body_html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-2022-jp\"></head><body>".$body_html."</body></html>";
      }
    }

    # HTML文字ｺｰﾄﾞ設定
    $mcode = '';
    if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      # SoftBankの場合強制UTF-8変換
      if (preg_match('/<meta\s[^>]+content\s*=\s*\"[^>]+\scharset=([^>]+)\"[^>]*>/i',$body_html,$MATCH)) {
        # 文字ｺｰﾄﾞ指定<META>ﾀｸﾞが含まれている場合
        $body_html = preg_replace('/(<meta\s[^>]+content\s*=\s*\"[^>]+\scharset=)([^>]+)(\"[^>]*>)/i','\\1UTF-8\\3',$body_html);
      } else {
        # 文字ｺｰﾄﾞ指定<META>ﾀｸﾞが含まれていない場合
        $body_html = str_replace('<head>','<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">',$body_html);
      }
      $mcode = 'UTF-8';
      $body_html_code = mb_detect_encoding($body_html,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      if (mb_preferred_mime_name($body_html_code) != mb_preferred_mime_name('UTF-8')) {
        $body_html = mb_convert_encoding($body_html,'UTF-8');
      }
    } else {
      # SoftBank以外
      if (preg_match('/^jis$/i',$mail_code)) {
        $mcode = 'ISO-2022-JP';
      } elseif (preg_match('/sjis/i',$mail_code) or preg_match('/shift_jis/i',$mail_code)) {
        $mcode = 'Shift_JIS';
      } elseif (preg_match('/euc/i',$mail_code)) {
        $mcode = 'EUC-JP';
      } elseif (preg_match('/utf/i',$mail_code)) {
        $mcode = 'UTF-8';
      }
    }
    $body_html = preg_replace('|(<meta\s.*\scharset=)(.+?)([\'\"].*?>)|i','\\1'.$mcode.'\\3',$body_html);

    if ($to_career != 'PC') {
      # PC以外
      # 行頭空白削除
      $TDATA  = explode("\n",$body_html);
      $TDATAS = array();
      foreach ($TDATA as $tdt) {
        $TDATAS[] = preg_replace('/^\s*/','',$tdt);
      }
      $body_html = join("\n",$TDATAS);
      # 改行削除
      $body_html = preg_replace('/[\r\n]/','',$body_html);
    }

    # ﾃﾞｰﾀ末改行処理
    if (($body_html != '') and preg_match('/\n$/',$body_html)) { $body_html = preg_replace('/\n$/','',$body_html); }

    return $body_html;
  }

  # ﾒｰﾙ本文生成 //////////////////////////////////////////////////////////////
  # ﾒｰﾙの本文を生成します。
  # [引渡し値]
  # 　$mail_ptn    : ﾒｰﾙ形式ﾊﾟﾀｰﾝ
  # 　$body_plain  : ﾃｷｽﾄ本文
  # 　$body_html   : HTML本文
  # 　$INLINEFILE  : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$UPFILE      : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$to_career   : 送信先ｷｬﾘｱ
  # 　$decome_mode : ﾃﾞｺﾒﾓｰﾄﾞ指定
  # 　$upfile_flag : 添付ﾌｧｲﾙﾌﾗｸﾞ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # 　$mail_code   : ﾒｰﾙ本文文字ｺｰﾄﾞ指定(指定なし又は'JIS':JIS)
  # 　$input_code  : 入力ｺｰﾄﾞ
  # [返り値]
  # 　$mail_body   : ﾒｰﾙ本文
  #////////////////////////////////////////////////////////////////////////////
  function _make_mail_body($mail_ptn,$body_plain,$body_html,$to_career,$INLINEFILE,$UPFILE,$decome_mode,$upfile_flag,$content_transfer_encoding,$mail_code,$input_code) {
    global $emoji_obj;

    $mail_header_ptn = '';
    $mail_body       = '';
    # 本文ｴﾝｺｰﾄﾞ
    if ($content_transfer_encoding == 'base64') {
      # Base64ｴﾝｺｰﾄﾞ
      $body_plain = chunk_split(base64_encode($body_plain));
      $body_html  = chunk_split(base64_encode($body_html));
    } elseif ($content_transfer_encoding == 'quoted_printable') {
      # Quoted_Printableｴﾝｺｰﾄﾞ
      $body_plain = quoted_printable_encodee($body_plain);
      $body_html  = quoted_printable_encodee($body_html);
    } else {
      # 指定無し(7bit)
      if ($to_career == 'PC') {
        # PC宛ての場合
      } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        # SoftBank携帯宛ての場合
#        $content_transfer_encoding = 'quoted_printable';
#        $body_plain = quoted_printable_encodee($body_plain);
        $content_transfer_encoding = '7bit';
        $body_html = quoted_printable_encodee($body_html);
      } else {
        # SoftBank携帯宛て以外の場合
      }
    }

    if ($mail_ptn == '1') {
      # ﾃｷｽﾄ(共通)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_1($body_plain,$content_transfer_encoding);
    } elseif ($mail_ptn == '2') {
      # ﾃｷｽﾄ + ﾌｧｲﾙ添付(共通)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_2($body_plain,$UPFILE,$content_transfer_encoding);
    } elseif ($mail_ptn == '3') {
      # ﾃｷｽﾄ + HTML + ﾌｧｲﾙ添付(共通)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_3($body_plain,$body_html,$UPFILE,$content_transfer_encoding,$to_career);
    } elseif ($mail_ptn == '4') {
      # ﾃｷｽﾄ + HTML(携帯用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_4($body_plain,$body_html,$content_transfer_encoding,$to_career);
    } elseif ($mail_ptn == '5') {
      # ﾃｷｽﾄ + HTML + ｲﾝﾗｲﾝ画像(携帯用)
      if ($to_career == 'au') {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_5_au($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career);
      } elseif (($to_career == 'SoftBank') or  ($to_career == $emoji_obj->softbank_name)) {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_5_sb($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career);
      } else {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_5($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career);
      }
    } elseif ($mail_ptn == '6') {
      # ﾃｷｽﾄ + HTML + ｲﾝﾗｲﾝ画像 + ﾌｧｲﾙ添付(携帯用)
      if ($to_career == 'au') {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_6_au($body_plain,$body_html,$INLINEFILE,$UPFILE,$content_transfer_encoding,$to_career);
      } elseif (($to_career == 'SoftBank') or  ($to_career == $emoji_obj->softbank_name)) {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_6_sb($body_plain,$body_html,$INLINEFILE,$UPFILE,$content_transfer_encoding,$to_career);
      } else {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_6($body_plain,$body_html,$INLINEFILE,$UPFILE,$content_transfer_encoding,$to_career);
      }
    } elseif ($mail_ptn == '7') {
      # ﾃｷｽﾄ + HTML(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_7($body_plain,$body_html,$content_transfer_encoding);
    } elseif ($mail_ptn == '8') {
      # ﾃｷｽﾄ + HTML + ｲﾝﾗｲﾝ画像(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_8($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding);
    } elseif ($mail_ptn == '9') {
      # ﾃｷｽﾄ + HTML + ｲﾝﾗｲﾝ画像 + ﾌｧｲﾙ添付(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_9($body_plain,$body_html,$INLINEFILE,$UPFILE,$content_transfer_encoding);
    } elseif ($mail_ptn == '10') {
      # HTML(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_10($body_html,$content_transfer_encoding);
    } elseif ($mail_ptn == '11') {
      # HTML + ﾌｧｲﾙ添付(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_11($body_html,$UPFILE,$content_transfer_encoding);
    } elseif ($mail_ptn == '12') {
      # HTML + ｲﾝﾗｲﾝ画像(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_12($body_html,$INLINEFILE,$content_transfer_encoding);
    } elseif ($mail_ptn == '13') {
      # HTML + ｲﾝﾗｲﾝ画像 + ﾌｧｲﾙ添付(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_13($body_html,$INLINEFILE,$UPFILE,$content_transfer_encoding);
    } elseif ($mail_ptn == '14') {
      # ﾃｷｽﾄ + HTML + ﾌｧｲﾙ添付(PC用)
      list($mail_header_ptn,$mail_body) = $this->_mail_ptn_14($body_plain,$body_html,$UPFILE,$content_transfer_encoding);
    }
    return array($mail_header_ptn,$mail_body);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ1 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ1(PC,携帯共通) - ﾃｷｽﾄ本文のみ
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_1($body_plain,$content_transfer_encoding) {

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_header_ptn .= "Content-Transfer-Encoding: ".$content_transfer_encoding;

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    $mail_ptn .= $body_plain;

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ2 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ2(PC,携帯共通) - ﾃｷｽﾄ本文 + 添付ﾌｧｲﾙ
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$UPFILELIST                : ｱｯﾌﾟﾛｰﾄﾞﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_2($body_plain,$UPFILELIST,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # 添付ﾌｧｲﾙﾊﾟｰﾄ設定
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ3 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ3(PC,携帯共通) - ﾃｷｽﾄ本文 + HTML本文 + 添付ﾌｧｲﾙ
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$UPFILELIST                : ｱｯﾌﾟﾛｰﾄﾞﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # 　$to_career                 : 送信先ｷｬﾘｱ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_3($body_plain,$body_html,$UPFILELIST,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\n";
    if ($to_career == 'PC') {
      # PC宛て
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    } else {
      # 携帯宛て
      if ($content_transfer_encoding == '7bit') {
        if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
          $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
          $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
        } else {
          $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
          $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
        }
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ4 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ4(携帯用) - ﾃｷｽﾄ本文 + HTML本文
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_4($body_plain,$body_html,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_2}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ5 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ5(携帯用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_5($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ5(au携帯専用) /////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ5(au携帯専用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_5_au($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_2}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
#    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ5(SoftBank携帯専用) ///////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ5(Softbank携帯専用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_5_sb($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
    $mail_header_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
#    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ6 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ6(携帯用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像 + 添付
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$UPFILELIST                : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_6($body_plain,$body_html,$INLINEFILE,$UPFILELIST,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ6(au携帯専用) /////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ6(au携帯専用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像 + 添付
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$UPFILELIST                : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_6_au($body_plain,$body_html,$INLINEFILE,$UPFILELIST,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_2}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
#    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_2}--\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\n";
    $mail_ptn .= "--{$boundary_2}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ6(Softbank携帯専用) ///////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ6(Softbank携帯専用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像 + 添付
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$UPFILELIST                : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_6_sb($body_plain,$body_html,$INLINEFILE,$UPFILELIST,$content_transfer_encoding,$to_career) {
    global $emoji_obj;

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
    $mail_header_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
#    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    }
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_2}--\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\n";
    $mail_ptn .= "--{$boundary_2}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ7 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ7(PC用) - ﾃｷｽﾄ本文 + HTML本文
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_7($body_plain,$body_html,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--".$boundary."\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ8 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ8(PC用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_8($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_1}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ9 /////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ9(PC用) - ﾃｷｽﾄ本文 + HTML本文 + ｲﾝﾗｲﾝ画像 + 添付
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$UPFILELIST                : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_9($body_plain,$body_html,$INLINEFILE,$UPFILELIST,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_2}\"\n";
    $mail_ptn .= "\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_3}\"\n";
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_3);
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ10 ////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ10(PC) - HTML本文のみ
  # [引渡し値]
  # 　$body_html                 : HTML本文
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_10($body_html,$content_transfer_encoding) {

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_header_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    $mail_ptn .= $body_html."\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ11 ////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ11(PC用) - HTML本文 + 添付
  # [引渡し値]
  # 　$body_html                 : HTML本文
  # 　$UPFILELIST                : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_11($body_html,$UPFILELIST,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ12 ////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ12(PC用) - HTML本文 + ｲﾝﾗｲﾝ画像
  # [引渡し値]
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_12($body_html,$INLINEFILE,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ13 ////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ13(PC用) - HTML本文 + ｲﾝﾗｲﾝ画像 + 添付
  # [引渡し値]
  # 　$body_html                 : HTML本文
  # 　$INLINEFILE                : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$UPFILELIST                : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_13($body_html,$INLINEFILE,$UPFILELIST,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ﾒｰﾙﾊﾟﾀｰﾝ14 ////////////////////////////////////////////////////////////////
  # ﾒｰﾙ形式ﾊﾟﾀｰﾝ14(PC用) - ﾃｷｽﾄ本文 + HTML本文 + 添付ﾌｧｲﾙ
  # [引渡し値]
  # 　$body_plain                : ﾃｷｽﾄ本文
  # 　$body_html                 : HTML本文
  # 　$UPFILELIST                : ｱｯﾌﾟﾛｰﾄﾞﾌｧｲﾙﾘｽﾄ
  # 　$content_transfer_encoding : ｴﾝｺｰﾄﾞｺｰﾄﾞ
  # 　$to_career                 : 送信先ｷｬﾘｱ
  # [返り値]
  # 　$mail_header_ptn : 追加ﾍｯﾀﾞｰ
  # 　$mail_ptn        : 本文
  #////////////////////////////////////////////////////////////////////////////
  function _mail_ptn_14($body_plain,$body_html,$UPFILELIST,$content_transfer_encoding) {

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_1}\"\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\n";
    $mail_ptn .= "\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\n";
    $mail_ptn .= "\n";
    $mail_ptn .= $body_html."\n";
    $mail_ptn .= "\n";
    # 添付ﾌｧｲﾙﾊﾟｰﾄ設定
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\n";

    return array($mail_header_ptn,$mail_ptn);
  }

  # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ処理 /////////////////////////////////////////////////////////
  # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ処理
  # [引渡し値]
  # 　$INLINEFILE : ｲﾝﾗｲﾝ画像ﾌｧｲﾙﾘｽﾄ
  # 　$boundary   : ﾊﾞｳﾝﾀﾞﾘｰNo
  # [返り値]
  # 　$inlinefile_part : ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ
  #////////////////////////////////////////////////////////////////////////////
  function _inlinefile($INLINEFILE,$boundary) {
    $inlinefile_part = '';
    foreach ($INLINEFILE as $kdt => $IDT) {
      $inlinefile_part .= "--{$boundary}\n";
      $inlinefile_part .= "Content-Type: {$IDT['mime']};\n";
      $inlinefile_part .= "\tname=\"{$IDT['name']}\"\n";
      $inlinefile_part .= "Content-Transfer-Encoding: base64\n";
      $inlinefile_part .= "Content-ID: <{$kdt}>\n";
      $inlinefile_part .= "\n";
#      $inlinefile_part .= $IDT['data']."\n";
      $inlinefile_part .= $IDT['data'];
      $inlinefile_part .= "\n";
    }
    return $inlinefile_part;
  }

  # ﾌｧｲﾙ添付ﾊﾟｰﾄ処理 //////////////////////////////////////////////////////////////
  # ﾌｧｲﾙ添付ﾊﾟｰﾄ処理
  # [引渡し値]
  # 　$UPFILELIST : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$boundary   : ﾊﾞｳﾝﾀﾞﾘｰNo
  # [返り値]
  # 　$addfile_part : ﾌｧｲﾙ添付ﾊﾟｰﾄ
  #////////////////////////////////////////////////////////////////////////////
  function _addfile($UPFILELIST,$boundary) {
    $addfile_part = '';
    foreach ($UPFILELIST as $kdt => $UDT) {
      $addfile_part .= "--{$boundary}\n";
      $addfile_part .= "Content-Type: {$UDT['mime']};\n";
      $addfile_part .= "\tname=\"{$UDT['basename']}\"\n";
      $addfile_part .= "Content-Transfer-Encoding: base64\n";
      $addfile_part .= "Content-Disposition: attachment;\n";
      $addfile_part .= "\tfilename=\"{$UDT['basename']}\"\n\n";
#      $addfile_part .= $UDT['filedata']."\n";
      $addfile_part .= $UDT['filedata'];
      $addfile_part .= "\n";
    }
    return $addfile_part;
  }

  # ｲﾝﾗｲﾝ画像ﾁｪｯｸ処理 /////////////////////////////////////////////////////////
  # ｲﾝﾗｲﾝ画像のﾁｪｯｸを行います。
  # [引渡し値]
  # 　$INLINEFILE : ｲﾝﾗｲﾝ画像ﾘｽﾄ
  # 　$to_career  : 送信先ｷｬﾘｱ
  # [返り値]
  # 　$check_flag : ﾌｧｲﾙ添付ﾊﾟｰﾄ
  #////////////////////////////////////////////////////////////////////////////
  function _inline_check($INLINEFILE,$to_career) {
    global $emoji_obj;

    $check_flag = True;

    # ｲﾝﾗｲﾝ画像ｻｲｽﾞﾁｪｯｸ
    $total_size = 0;
    foreach ($INLINEFILE as $kdt => $IDT) {
      $total_size += $IDT['size'];
      if ($to_career == 'PC') {
        # PC用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
        if (($this->inline_max_size_pc > 0) and ($this->inline_max_size_pc < $IDT['size'])) { $check_flag = False; }
      } elseif ($to_career == 'DoCoMo') {
        # DoCoMo用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
        if (($this->inline_max_size_docomo > 0) and ($this->inline_max_size_docomo < $IDT['size'])) { $check_flag = False; }
      } elseif ($to_career == 'au') {
        # au用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
        if (($this->inline_max_size_au > 0) and ($this->inline_max_size_au < $IDT['size'])) { $check_flag = False; }
      } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        # SoftBank用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
        if (($this->inline_max_size_softbank > 0) and ($this->inline_max_size_softbank < $IDT['size'])) { $check_flag = False; }
      }
    }
    if ($to_career == 'PC') {
      # PC用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
      # ｲﾝﾗｲﾝ画像数ﾁｪｯｸ
      if (($this->inline_max_num_pc > 0) and ($this->inline_max_num_pc < count($INLINEFILE))) { $check_flag = False; }
      # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->inline_all_max_size_pc > 0) and ($this->inline_all_max_size_pc < $total_size)) { $check_flag = False; }
    } elseif ($to_career == 'DoCoMo') {
      # DoCoMo用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
      # ｲﾝﾗｲﾝ画像数ﾁｪｯｸ
      if (($this->inline_max_num_docomo > 0) and ($this->inline_max_num_docomo < count($INLINEFILE))) { $check_flag = False; }
      # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->inline_all_max_size_docomo > 0) and ($this->inline_all_max_size_docomo < $total_size)) { $check_flag = False; }
    } elseif ($to_career == 'au') {
      # au用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
      # ｲﾝﾗｲﾝ画像数ﾁｪｯｸ
      if (($this->inline_max_num_au > 0) and ($this->inline_max_num_au < count($INLINEFILE))) { $check_flag = False; }
      # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->inline_all_max_size_au > 0) and ($this->inline_all_max_size_au < $total_size)) { $check_flag = False; }
    } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      # SoftBank用ｲﾝﾗｲﾝ画像ﾁｪｯｸ
      # ｲﾝﾗｲﾝ画像数ﾁｪｯｸ
      if (($this->inline_max_num_softbank > 0) and ($this->inline_max_num_softbank < count($INLINEFILE))) { $check_flag = False; }
      # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->inline_all_max_size_softbank > 0) and ($this->inline_all_max_size_softbank < $total_size)) { $check_flag = False; }
    }
    return $check_flag;
  }

  # 添付ﾌｧｲﾙﾁｪｯｸ処理 /////////////////////////////////////////////////////////
  # 添付ﾌｧｲﾙのﾁｪｯｸを行います。
  # [引渡し値]
  # 　$UPFILELIST : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$to_career  : 送信先ｷｬﾘｱ
  # [返り値]
  # 　$check_flag : ﾌｧｲﾙ添付ﾊﾟｰﾄ
  #////////////////////////////////////////////////////////////////////////////
  function _upfile_check($UPFILELIST,$to_career) {
    global $emoji_obj;

    $check_flag = True;

    # 添付ﾌｧｲﾙｻｲｽﾞﾁｪｯｸ
    $total_size = 0;
    foreach ($UPFILELIST as $kdt => $UDT) {
      $total_size += $UDT['size'];
      if ($to_career == 'PC') {
        # PC用添付ﾌｧｲﾙﾁｪｯｸ
        if (($this->upfile_max_size_pc > 0) and ($this->upfile_max_size_pc < $UDT['size'])) { $check_flag = False; }
      } elseif ($to_career == 'DoCoMo') {
        # DoCoMo用添付ﾌｧｲﾙﾁｪｯｸ
        if (($this->upfile_max_size_docomo > 0) and ($this->upfile_max_size_docomo < $UDT['size'])) { $check_flag = False; }
      } elseif ($to_career == 'au') {
        # au用添付ﾌｧｲﾙﾁｪｯｸ
        if (($this->upfile_max_size_au > 0) and ($this->upfile_max_size_au < $UDT['size'])) { $check_flag = False; }
      } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
        # SoftBank用添付ﾌｧｲﾙﾁｪｯｸ
        if (($this->upfile_max_size_softbank > 0) and ($this->upfile_max_size_softbank < $UDT['size'])) { $check_flag = False; }
      }
    }
    if ($to_career == 'PC') {
      # PC用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->upfile_max_num_pc > 0) and ($this->upfile_max_num_pc < count($INLINEFILE))) { $check_flag = False; }
      # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->upfile_all_max_size_pc > 0) and ($this->upfile_all_max_size_pc < $total_size)) { $check_flag = False; }
    } elseif ($to_career == 'DoCoMo') {
      # DoCoMo用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->upfile_max_num_docomo > 0) and ($this->upfile_max_num_docomo < count($INLINEFILE))) { $check_flag = False; }
      # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->upfile_all_max_size_docomo > 0) and ($this->upfile_all_max_size_docomo < $total_size)) { $check_flag = False; }
    } elseif ($to_career == 'au') {
      # au用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->upfile_max_num_au > 0) and ($this->upfile_max_num_au < count($INLINEFILE))) { $check_flag = False; }
      # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->upfile_all_max_size_au > 0) and ($this->upfile_all_max_size_au < $total_size)) { $check_flag = False; }
    } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      # SoftBank用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->upfile_max_num_softbank > 0) and ($this->upfile_max_num_softbank < count($INLINEFILE))) { $check_flag = False; }
      # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞﾁｪｯｸ
      if (($this->upfile_all_max_size_softbank > 0) and ($this->upfile_all_max_size_softbank < $total_size)) { $check_flag = False; }
    }
    return $check_flag;
  }

  # 添付ﾌｧｲﾙﾁｪｯｸ処理 /////////////////////////////////////////////////////////
  # 添付ﾌｧｲﾙのﾁｪｯｸを行います。
  # [引渡し値]
  # 　$INLINEFILE : ｲﾝﾗｲﾝ画像ﾘｽﾄ
  # 　$UPFILELIST : 添付ﾌｧｲﾙﾘｽﾄ
  # 　$to_career  : 送信先ｷｬﾘｱ
  # [返り値]
  # 　$check_flag : ﾌｧｲﾙ添付ﾊﾟｰﾄ
  #////////////////////////////////////////////////////////////////////////////
  function _all_file_check($INLINEFILE,$UPFILELIST,$to_career) {
    global $emoji_obj;

    $check_flag = True;

    $total_file_num = count($INLINEFILE) + count($UPFILELIST);
    $total_size = 0;
    foreach ($INLINEFILE as $kdt => $IDT) { $total_size += $IDT['size']; }
    foreach ($UPFILELIST as $kdt => $UDT) { $total_size += $UDT['size']; }
    if ($to_career == 'PC') {
      # PC用添付ﾌｧｲﾙﾁｪｯｸ
      # ﾄｰﾀﾙﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->allfile_max_num_pc > 0) and ($this->allfile_max_num_pc < $total_file_num)) { $check_flag = False; }
      # ﾄｰﾀﾙﾌｧｲﾙｻｲｽﾞﾁｪｯｸ
      if (($this->allfile_max_size_pc > 0) and ($this->allfile_max_size_pc < $total_size)) { $check_flag = False; }
    } elseif ($to_career == 'DoCoMo') {
      # DoCoMo用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->allfile_max_num_docomo > 0) and ($this->allfile_max_num_docomo < $total_file_num)) { $check_flag = False; }
      # 添付ﾌｧｲﾙｻｲｽﾞﾁｪｯｸ
      if (($this->allfile_max_size_docomo > 0) and ($this->allfile_max_size_docomo < $total_size)) { $check_flag = False; }
    } elseif ($to_career == 'au') {
      # au用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->allfile_max_num_au > 0) and ($this->allfile_max_num_au < $total_file_num)) { $check_flag = False; }
      # 添付ﾌｧｲﾙｻｲｽﾞﾁｪｯｸ
      if (($this->allfile_max_size_au > 0) and ($this->allfile_max_size_au < $total_size)) { $check_flag = False; }
    } elseif (($to_career == 'SoftBank') or ($to_career == $emoji_obj->softbank_name)) {
      # SoftBank用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->allfile_max_num_softbank > 0) and ($this->allfile_max_num_softbank < $total_file_num)) { $check_flag = False; }
      # 添付ﾌｧｲﾙｻｲｽﾞﾁｪｯｸ
      if (($this->allfile_max_size_softbank > 0) and ($this->allfile_max_size_softbank < $total_size)) { $check_flag = False; }
    }
    return $check_flag;
  }

}

# Quoted_Printable ｴﾝｺｰﾄﾞ /////////////////////////////////////////////////////
function quoted_printable_encodee($sText,$bEmulate_imap_8bit=true) {
  // split text into lines
  $aLines=explode(chr(13).chr(10),$sText);

  for ($i=0;$i<count($aLines);$i++) {
    $sLine =& $aLines[$i];
    if (strlen($sLine)===0) continue; // do nothing, if empty

    $sRegExp = '/[^\x09\x20\x21-\x3C\x3E-\x7E]/e';

    // imap_8bit encodes x09 everywhere, not only at lineends,
    // for EBCDIC safeness encode !"#$@[\]^`{|}~,
    // for complete safeness encode every character :)
    if ($bEmulate_imap_8bit)
      $sRegExp = '/[^\x20\x21-\x3C\x3E-\x7E]/e';

    $sReplmt = 'sprintf( "=%02X", ord ( "$0" ) ) ;';
    $sLine = preg_replace( $sRegExp, $sReplmt, $sLine ); 

    // encode x09,x20 at lineends
    {
      $iLength = strlen($sLine);
      $iLastChar = ord($sLine{$iLength-1});

      //              !!!!!!!!   
      // imap_8_bit does not encode x20 at the very end of a text,
      // here is, where I don't agree with imap_8_bit,
      // please correct me, if I'm wrong,
      // or comment next line for RFC2045 conformance, if you like
      if (!($bEmulate_imap_8bit && ($i==count($aLines)-1)))
         
      if (($iLastChar==0x09)||($iLastChar==0x20)) {
        $sLine{$iLength-1}='=';
        $sLine .= ($iLastChar==0x09)?'09':'20';
      }
    }    // imap_8bit encodes x20 before chr(13), too
    // although IMHO not requested by RFC2045, why not do it safer :)
    // and why not encode any x20 around chr(10) or chr(13)
    if ($bEmulate_imap_8bit) {
      $sLine=str_replace(' =0D','=20=0D',$sLine);
      //$sLine=str_replace(' =0A','=20=0A',$sLine);
      //$sLine=str_replace('=0D ','=0D=20',$sLine);
      //$sLine=str_replace('=0A ','=0A=20',$sLine);
    }

    // finally split into softlines no longer than 76 chars,
    // for even more safeness one could encode x09,x20
    // at the very first character of the line
    // and after soft linebreaks, as well,
    // but this wouldn't be caught by such an easy RegExp                  
    preg_match_all( '/.{1,73}([^=]{0,2})?/', $sLine, $aMatch );
    $sLine = implode( '=' . chr(13).chr(10), $aMatch[0] ); // add soft crlf's
  }

  // join lines into text
  return implode(chr(13).chr(10),$aLines);
}

# Quoted_Printable ｴﾝｺｰﾄﾞ2 ////////////////////////////////////////////////////
function quoted_printable($string) {
  $crlf   = "\n" ;
  $string = preg_replace('!(\r\n|\r|\n)!', $crlf, $string) . $crlf ;
  $f[]    = '/([\000-\010\013\014\016-\037\075\177-\377])/e' ;
  $r[]    = "'=' . sprintf('%02X', ord('\\1'))" ;
  $f[]    = '/([\011\040])' . $crlf . '/e' ;
  $r[]    = "'=' . sprintf('%02X', ord('\\1')) . '" . $crlf . "'" ;
  $string = preg_replace($f, $r, $string) ;
  return trim(wordwrap($string, 70, ' =' . $crlf)) ;
}

# URL･ﾒｰﾙ置換えﾘﾝｸ置換え ///////////////////////////////////////////////////////////////////
function link_make($string) {
  $string_sub = $string;
  # URLﾘﾝｸ置換え
  $pattern     = '/(https?(:\/\/[-_.!~*\'()a-z0-9;\/?:\@&=+\$,%#]+))/i';
  $replacement = '<a href="\1">\1</a>';
  $string      = preg_replace($pattern,$replacement,$string);
  # ﾒｰﾙｱﾄﾞﾚｽ置換え
  $pattern     = '/([a-z0-9_\-.]+@([a-z0-9_\-]+\.)+[a-z]+)/i';
  $replacement ='<a href="mailto:\1">\1</a>';
  $string      = preg_replace($pattern,$replacement,$string);
  return $string;
}

?>
