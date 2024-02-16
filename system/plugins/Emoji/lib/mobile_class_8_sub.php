<?php

###############################################################################
# 携帯絵文字変換ﾗｲﾌﾞﾗﾘ 2008(拡張ｸﾗｽﾗｲﾌﾞﾗﾘ)
# Potora/inaken(C) 2008.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# 2008.10.07 v.1.00.00 新規
# 2008.11.28 v.1.00.01 ｸﾞﾛｰﾊﾞﾙ変数扱い変更
###############################################################################

###############################################################################
# 絵文字処理拡張ｸﾗｽ ###########################################################
###############################################################################
class emoji_sub {
  # ﾊﾞｰｼﾞｮﾝ設定
  var $ver = 'sub_v.1.00.00';

  # ｺﾝｽﾄﾗｸﾀ ///////////////////////////////////////////////////////////////////
  function emoji_sub() {
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

  # 機種名・固体識別番号取得 //////////////////////////////////////////////////
  # 携帯の機種名と個体識別番号を取得します。
  # [引渡し値]
  # 　$user_agent : ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ指定(指定無しの場合ｱｸｾｽ端末のﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ)
  # [返り値]
  # 　$RETURNDATA['career']  : ｷｬﾘｱ(DoCoMo,au,SoftBank or Vodafone)
  # 　$RETURNDATA['model']   : 機種名
  # 　$RETURNDATA['devid']   : ﾃﾞﾊﾞｲｽID
  # 　$RETURNDATA['ser']     : 個体識別番号(ｻﾌﾞｽｸﾗｲﾊﾞID)
  # 　$RETURNDATA['icc']     : FOMAｶｰﾄﾞ個体識別子
  # 　$RETURNDATA['imodeid'] : iﾓｰﾄﾞID
  #////////////////////////////////////////////////////////////////////////////
  function get_ser_no($user_agent='') {
    $career  = '';
    $model   = '';
    $devid   = '';
    $ser     = '';
    $icc     = '';
    $imodeid = '';
    # ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ設定
    if ($user_agent == '') {
      $user_agent = explode('/',$_SERVER['HTTP_USER_AGENT']);
    } else {
      $user_agent = explode('/',$user_agent);
    }
    # 機種名、個体識別番号取得
    if ($user_agent[0] == 'DoCoMo') {
      # DoCoMo
      if (preg_match('/^1\..$/', $user_agent[1])) {
        # ﾌﾞﾗｳｻﾞﾊﾞｰｼﾞｮﾝ 1.0
        $model = $user_agent[2];
        $devid = '';
        if (preg_match('/^ser(.+)/',$user_agent[4],$MATCH)) { $ser   = $MATCH[1]; }
        $icc   = '';
      } elseif (preg_match('/^2\..\s/', $user_agent[1],$MATCH)) {
        # ﾌﾞﾗｳｻﾞﾊﾞｰｼﾞｮﾝ 2.0(FOMA)
        if (preg_match('/^2\..\s(.+?)\(/', $user_agent[1],$MATCH)) { $model = $MATCH[1]; }
        if (preg_match('/ser(.+?)[\s;]/' , $user_agent[1],$MATCH)) { $ser   = $MATCH[1]; }
        if (preg_match('/icc(.+?)\)/'    , $user_agent[1],$MATCH)) { $icc   = $MATCH[1]; }
      }
      if (isset($_SERVER['HTTP_X_DCMGUID'])) { $imodeid = $_SERVER['HTTP_X_DCMGUID']; }
      $career  = 'DoCoMo';
    } elseif (preg_match('/KDDI/',$user_agent[0]) or ($user_agent[0] == 'UP.Browser')) {
      # au(旧機種)
      $model = '';
      if ($user_agent[0] == 'UP.Browser') {
        $devid = preg_replace('/(.+?)-(.+)/','\\2',$user_agent[1]);
      } elseif (preg_match('/KDDI/',$user_agent[1])) {
        $devid = preg_replace('/^KDDI-(.+?)\sUP(.+)/','\\1',$user_agent[0]);
      }
      $ser     = preg_replace('/^(.+?)_t.+/','\\1',$_SERVER['HTTP_X_UP_SUBNO']);
      $icc     = '';
      $imodeid = '';
      $career  = 'au';
    } elseif (preg_match('/(J-PHONE)|(Vodafone)|(MOT)|(SoftBank)|(Vemulator)/',$user_agent[0])) {
      # Vodafone,SoftBank
      if (isset($_SERVER['HTTP_X_JPHONE_MSNAME'])) {
        $model = preg_replace('/^(.+?)[\s_]*/','\\1',$_SERVER['HTTP_X_JPHONE_MSNAME']);
      }
      if ($model == '') {
        if (preg_match('/SoftBank/',$user_agent[0])) {
          $model = $user_agent[2];
        } else {
          $model = preg_replace('/^(.+?)\s*/','\\1',$user_agent[2]);
        }
      }
      if (preg_match('/J-PHONE/',$user_agent[0])) {
        # 'J-PHONE'ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ
        if (preg_match('/^SN(.+?)\s.+$/',$user_agent[3],$MATCH)) { $ser = $MATCH[1]; }
      } elseif (preg_match('/Vodafone/',$user_agent[0]) or preg_match('/SoftBank/',$user_agent[0]) or preg_match('/Vemulator/',$user_agent[0])) {
        # 'Vodafone','SoftBank'ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ
        if (preg_match('/^SN(.+?)\s.+$/',$user_agent[4],$MATCH)) { $ser = $MATCH[1]; }
      } elseif (preg_match('/MOT/',$user_agent[0])) {
        $ser = '';
      }
      $devid = '';
      $icc   = '';
      $imodeid = '';
      $career  = EMOJI_softbank_name;
    } else {
      $career  = 'PC';
      $model   = $user_agent[0].' '.$user_agent[1];
      $devid   = '';
      $ser     = '';
      $imodeid = '';
      $icc     = '';
    }
    # 返り値設定
    $RETURNDATA = array();
    $RETURNDATA['career']  = $career;
    $RETURNDATA['model']   = $model;
    $RETURNDATA['devid']   = $devid;
    $RETURNDATA['ser']     = $ser;
    $RETURNDATA['icc']     = $icc;
    $RETURNDATA['imodeid'] = $imodeid;
    return $RETURNDATA;
  }

  # 絵文字ｺｰﾄﾞ削除 ////////////////////////////////////////////////////////////
  # 文字列から絵文字を削除します。
  # [引渡し値]
  # 　$textstr     : 変換対象文字列
  # 　$docomo_flag : DoCoMo絵文字削除(0:削除する,1:削除しない)
  # 　$voda_flag   : SoftBank絵文字削除(0:削除する,1:削除しない)
  # 　$au_flag     : au絵文字削除(0:削除する,1:削除しない)
  # 　$out_code    : 変換後出力ｺｰﾄﾞ指定
  # 　$enc_cancel  : 内部ｴﾝｺｰﾄﾞ処理ｷｬﾝｾﾙ指定(1:ｷｬﾝｾﾙ)
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr     : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function delete_emoji_code($textstr,$docomo_flag='0',$voda_flag='0',$au_flag='0',$out_code='',$enc_cancel='',$input_code='') {
    # 絵文字削除
    $textstr = $this->emoji_str_replace($textstr,'',$docomo_flag,$voda_flag,$au_flag,$out_code,$enc_cancel,$input_code);
    return $textstr;
  }

  # 絵文字ｺｰﾄﾞ下駄変換 ////////////////////////////////////////////////////////
  # 文字列中の絵文字を下駄変換します。
  # [引渡し値]
  # 　$textstr     : 変換対象文字列
  # 　$docomo_flag : DoCoMo絵文字下駄変換(0:変換する,1:変換しない)
  # 　$voda_flag   : SoftBank絵文字下駄変換(0:変換する,1:変換しない)
  # 　$au_flag     : au絵文字下駄変換(0:変換する,1:変換しない)
  # 　$out_code    : 変換後出力ｺｰﾄﾞ指定
  # 　$enc_cancel  : 内部ｴﾝｺｰﾄﾞ処理ｷｬﾝｾﾙ指定(1:ｷｬﾝｾﾙ)
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr     : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function emoji2geta($textstr,$docomo_flag='0',$voda_flag='0',$au_flag='0',$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    # 絵文字下駄変換
    $textstr = $this->emoji_str_replace($textstr,$emoji_obj->geta_str,$docomo_flag,$voda_flag,$au_flag,$out_code,$enc_cancel,$input_code);
    return $textstr;
  }

  # 絵文字ｺｰﾄﾞ指定ﾃｷｽﾄ変換 ////////////////////////////////////////////////////
  # 文字列中の絵文字を指定の文字列に変換します。
  # [引渡し値]
  # 　$textstr     : 変換対象文字列
  # 　$replace_str : 変換対象文字列
  # 　$docomo_flag : DoCoMo絵文字下駄変換(0:変換する,1:変換しない)
  # 　$voda_flag   : SoftBank絵文字下駄変換(0:変換する,1:変換しない)
  # 　$au_flag     : au絵文字下駄変換(0:変換する,1:変換しない)
  # 　$out_code    : 変換後出力ｺｰﾄﾞ指定
  # 　$enc_cancel  : 内部ｴﾝｺｰﾄﾞ処理ｷｬﾝｾﾙ指定(1:ｷｬﾝｾﾙ)
  # 　$input_code  : 入力文字ｺｰﾄﾞ指定(指定なし:SJIS、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr     : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function emoji_str_replace($textstr,$replace_str,$docomo_flag='0',$voda_flag='0',$au_flag='0',$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    if (isset($textstr)) {
      if ($out_code == '') { $oc = $emoji_obj->chr_code; } else { $oc = $out_code; }
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',1,$input_code); }
      # 変換対象文字列ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # 置換え文字列ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($replace_str,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($replace_str,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $replace_str_code = mb_preferred_mime_name($de);
        if ($replace_str_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $replace_str = @mb_convert_encoding($replace_str,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # DoCoMo絵文字置換え
      if ($docomo_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'d'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # au絵文字置換え
      if ($au_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'a'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'am'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # SoftBank絵文字置換え
      if ($voda_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'v'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # ﾃｷｽﾄｺｰﾄﾞ変換
      $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # 出力ｺｰﾄﾞ設定
        if ($text_code != mb_preferred_mime_name($oc)) {
          # 文字列ｺｰﾄﾞが指定出力ｺｰﾄﾞと異なる場合
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) {
            # SJIS指定の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$emoji_obj->chg_code_sjis);
          } else {
            # SJIS以外の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$text_code);
          }
        }
      }
    } else {
      $textstr = '';
    }
    return $textstr;
  }

  # 文字数ｶｳﾝﾄ ////////////////////////////////////////////////////////////////
  # 文字列の絵文字を加味した文字数をｶｳﾝﾄします。
  # ﾊﾞｲﾅﾘｶｳﾝﾄは絵文字を2ﾊﾞｲﾄとしてｶｳﾝﾄします。
  # [引渡し値]
  # 　$textstr    : ﾁｪｯｸ対象文字列
  # 　$enc_cancel : 内部ｴﾝｺｰﾄﾞ処理ｷｬﾝｾﾙ指定(1:ｷｬﾝｾﾙ)
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$COUNTDATA['mb_strlen']   : 全文字数(ﾏﾙﾁﾊﾞｲﾄも1文字としてｶｳﾝﾄ)
  # 　$COUNTDATA['mb_strwidth'] : 全ﾊﾞｲﾄ数(半角:1,全角:2,絵文字:2)
  # 　$COUNTDATA['total']       : 全絵文字数
  # 　$COUNTDATA['DoCoMo']      : DoCoMo絵文字数
  # 　$COUNTDATA['au']          : au絵文字数
  # 　$COUNTDATA['SoftBank']    : SoftBank絵文字数
  #////////////////////////////////////////////////////////////////////////////
  function emj_check($textstr,$enc_cancel='',$input_code='') {
    global $emoji_obj;

    $COUNTDATA = array();
    $COUNTDATA['mb_strlen']   = 0;
    $COUNTDATA['mb_strwidth'] = 0;
    $COUNTDATA['total']       = 0;
    $COUNTDATA['DoCoMo']      = 0;
    $COUNTDATA['au']          = 0;
    $COUNTDATA['SoftBank']    = 0;
    if (isset($textstr)) {
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',$enc_cancel,$input_code); }

      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # 文字ｶｳﾝﾄ準備
      $textstr_str = $textstr;
      while (preg_match('/\{(emj_._|d|a|am|v)[0-9]{4}\}/',$textstr_str)) {
        $textstr_str = preg_replace('/\{(emj_._|d|a|am|v)[0-9]{4}\}/',"\x82\xA0",$textstr_str);
      }
      # 全文字数ｶｳﾝﾄ
      $COUNTDATA['mb_strlen']   = mb_strlen($textstr_str,'SJIS');
      # 全ﾊﾞｲﾄ数ｶｳﾝﾄ
      $COUNTDATA['mb_strwidth'] = mb_strwidth($textstr_str,'SJIS');
      # DoCoMo絵文字ｶｳﾝﾄ
      while (preg_match('/\{(emj_d_|d)[0-9]{4}\}/', $textstr)) {
        $textstr = preg_replace('/\{(emj_d_|d)[0-9]{4}\}/','',$textstr,1);
        $COUNTDATA['DoCoMo']++;
        $COUNTDATA['total']++;
      }
      # au絵文字ｶｳﾝﾄ
      while (preg_match('/\{(emj_a_|a|emj_am_|am)[0-9]{4}\}/', $textstr)) {
        $textstr = preg_replace('/\{(emj_a_|a|emj_am_|am)[0-9]{4}\}/','',$textstr,1);
        $COUNTDATA['au']++;
        $COUNTDATA['total']++;
      }
      # SoftBank絵文字ｶｳﾝﾄ
      while (preg_match('/\{(v|emj_v_)[0-9]{4}\}/', $textstr)) {
        $textstr = preg_replace('/\{(emj_v_|v)[0-9]{4}\}/','',$textstr,1);
        $COUNTDATA['SoftBank']++;
        $COUNTDATA['total']++;
      }
    }
    return $COUNTDATA;
  }

  # 文字切り詰め //////////////////////////////////////////////////////////////
  # 絵文字を含む文字列を指定した幅に切り詰めます。
  # ※絵文字は2ﾊﾞｲﾄとして処理されます。
  # [引渡し値]
  # 　$textstr    : 処理対象文字列
  # 　$offset     : 開始位置
  # 　$width      : 文字列の幅
  # 　$end_str    : 切り詰めた場合の目印の文字列
  # 　$out_code   : 出力文字ｺｰﾄﾞ
  # 　$enc_cancel : 内部ｴﾝｺｰﾄﾞ処理ｷｬﾝｾﾙ指定(1:ｷｬﾝｾﾙ)
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr    : 指定された幅の文字列
  #////////////////////////////////////////////////////////////////////////////
  function emj_strimwidth($textstr,$offset,$width,$end_str,$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    if (isset($textstr)) {
      if ($out_code == '') { $oc = $emoji_obj->chr_code; } else { $oc = $out_code; }
      $offset = 0;
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',1,$input_code); }
      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # 文字処理準備
      $textstr_str = $textstr;
      $LISTUPDT = array();
      while (preg_match('/\{(emj_._|d|a|am|v)([0-9]{4})\}/',$textstr_str,$MATCH)) {
        $LISTUPDT[]  = '{'.$MATCH[1].$MATCH[2].'}';
        $textstr_str = preg_replace('/\{(emj_._|d|a|am|v)([0-9]{4})\}/',"\xEA\x9C",$textstr_str,1);
      }
      # 文字列切り詰め
      $textstr_str = mb_strimwidth($textstr_str,$offset,$width,$end_str);
      # 絵文字戻し
      $loop_no = 0;
      while (preg_match('/\xEA\x9C/',$textstr_str,$MATCH)) {
        $textstr_str = preg_replace('/\xEA\x9C/',$LISTUPDT[$loop_no],$textstr_str,1);
        $loop_no++;
      }
      $textstr = $textstr_str;
      # ﾃｷｽﾄｺｰﾄﾞ変換
      $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # 出力ｺｰﾄﾞ設定
        if ($text_code != mb_preferred_mime_name($oc)) {
          # 文字列ｺｰﾄﾞが指定出力ｺｰﾄﾞと異なる場合
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) {
            # SJIS指定の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$emoji_obj->chg_code_sjis);
          } else {
            # SJIS以外の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$text_code);
          }
        }
      }
    }
    return $textstr;
  }

  # 絵文字変換 ////////////////////////////////////////////////////////////////
  # 指定の絵文字を別の指定した絵文字に置き換えます。
  # [引渡し値]
  # 　$textstr      : 処理対象文字列
  # 　$original_emj : 元絵文字
  # 　$change_emj   : 変換絵文字
  # 　$out_code     : 出力文字ｺｰﾄﾞ
  # 　$enc_cancel   : 内部ｴﾝｺｰﾄﾞ処理ｷｬﾝｾﾙ指定(1:ｷｬﾝｾﾙ)
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr             : 指定された幅の文字列
  #////////////////////////////////////////////////////////////////////////////
  function emj_change($textstr,$original_emj,$change_emj,$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    if (isset($textstr) and isset($original_emj) and isset($change_emj)) {
      if ($out_code == '') { $oc = $emoji_obj->chr_code; } else { $oc = $out_code; }
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',1,$input_code); }
      $original_emj = $emoji_obj->emj_encode($original_emj,'',1,$input_code);
      $change_emj   = $emoji_obj->emj_encode($change_emj,'',1,$input_code);
      # 絵文字ﾁｪｯｸ
      if (!preg_match('/\{(emj_d_|emj_a_|emj_am_|emj_v_|d|a|am|v)(\d{4})\}/',$original_emj)) { return $textstr; }
      if (!preg_match('/\{(emj_d_|emj_a_|emj_am_|emj_v_|d|a|am|v)(\d{4})\}/',$change_emj))   { return $textstr; }
      # 元絵文字指定分解
      $ORIGINAL = array();
      $original_emj_sub = $original_emj;
      while (preg_match('/\{(emj_d_|emj_a_|emj_am_|emj_v_|d|a|am|v)(\d{4})\}/',$original_emj_sub,$MATCH)) {
        $ORIGINAL[] = '{'.$MATCH[1].$MATCH[2].'}';
        $original_emj_sub = preg_replace('/\{'.$MATCH[1].$MATCH[2].'\}/','',$original_emj_sub,1);
      }
      if (count($ORIGINAL) < 1) { return $textstr; }
      # 変換絵文字指定分解
      $CHANGE = array();
      $change_emj_sub = $change_emj;
      while (preg_match('/\{(emj_d_|emj_a_|emj_am_|emj_v_|d|a|am|v)(\d{4})\}/',$change_emj_sub,$MATCH)) {
        $CHANGE[] = '{'.$MATCH[1].$MATCH[2].'}';
        $change_emj_sub = preg_replace('/\{'.$MATCH[1].$MATCH[2].'\}/','',$change_emj_sub,1);
      }
      if (count($CHANGE) < 1) {
        return $textstr;
      } elseif (count($CHANGE) == 1) {
        $mode = 0;
      } elseif (count($CHANGE) > 1) {
        if ((count($ORIGINAL) != count($CHANGE))) { return $textstr; }
        $mode = 1;
      }
      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # 絵文字変換
      $lpno = 0;
      foreach ($ORIGINAL as $odt) {
        if ($mode == 0) {
          $textstr = preg_replace('/'.$odt.'/',$CHANGE[0],$textstr);
        } elseif ($mode == 1) {
          $textstr = preg_replace('/'.$odt.'/',$CHANGE[$lpno],$textstr);
        }
        $lpno++;
      }
      # ﾃｷｽﾄｺｰﾄﾞ変換
      $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # 出力ｺｰﾄﾞ設定
        if ($text_code != mb_preferred_mime_name($oc)) {
          # 文字列ｺｰﾄﾞが指定出力ｺｰﾄﾞと異なる場合
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) {
            # SJIS指定の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$emoji_obj->chg_code_sjis);
          } else {
            # SJIS以外の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$text_code);
          }
        }
      }
    }
    return $textstr;
  }

  # 絵文字入力ﾌｫｰﾑ用ｴｽｹｰﾌﾟ処理 ////////////////////////////////////////////////
  # 絵文字入力ﾌｫｰﾑで表示するための前処理を行います。
  # [引渡し値]
  # 　$html : ｴｽｹｰﾌﾟ対象文字列
  # [返り値]
  # 　$html : ｴｽｹｰﾌﾟ処理後文字列
  #////////////////////////////////////////////////////////////////////////////
  function emj_form_escape($html) {
    $html = preg_replace("/'/","\\'",$html);
    $html = preg_replace('/\r/','\\r',$html);
    $html = preg_replace('/\n/','\\n',$html);
    $html = preg_replace('/<br>/','',$html);
    return $html;
  }

  # 数字→絵文字数字変換(ｴﾝｺｰﾄﾞ文字) //////////////////////////////////////////
  # 数字から絵文字数字へ変換します。
  # [引渡し値]
  # 　$num_text    : 数値
  # 　$change_mode : 10以上の数値の場合の変換ﾊﾟﾀｰﾝ指定
  # [返り値]
  # 　$emoji_num : 変換結果
  #////////////////////////////////////////////////////////////////////////////
  function num2emojinum($num_text,$change_mode='') {
    global $emoji_obj;

    $emoji_num = '';
    if (!isset($num_text)) { return $emoji_num; }
    if ($num_text == '')   { return $emoji_num; }
    if (strlen($num_text) == 1) {
      if ($emoji_obj->hard == 'PC') {
        # PC絵文字画像(DoCoMo絵文字使用)
        if ($num_text == '0') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0134'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0125'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0126'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0127'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0128'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0129'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0130'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0131'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0132'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0133'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
      } elseif ($emoji_obj->hard == 'DoCoMo') {
        # DoCoMo絵文字
        if ($num_text == '0') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0134'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0125'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0126'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0127'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0128'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0129'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0130'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0131'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0132'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0133'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
      } elseif ($emoji_obj->hard == 'au') {
        # au絵文字
        if ($num_text == '0') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0325'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0180'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0181'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0182'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0183'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0184'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0185'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0186'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0187'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0188'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
      } elseif ($emoji_obj->hard == $emoji_obj->softbank_name) {
        # SoftBank絵文字
        if ($num_text == '0') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0227'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0218'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0219'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0220'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0221'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0222'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0223'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0224'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0225'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0226'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right']; }
      } else {
        # その他
        if ($num_text == '0') { $emoji_num = '0'; }
        if ($num_text == '1') { $emoji_num = '1'; }
        if ($num_text == '2') { $emoji_num = '2'; }
        if ($num_text == '3') { $emoji_num = '3'; }
        if ($num_text == '4') { $emoji_num = '4'; }
        if ($num_text == '5') { $emoji_num = '5'; }
        if ($num_text == '6') { $emoji_num = '6'; }
        if ($num_text == '7') { $emoji_num = '7'; }
        if ($num_text == '8') { $emoji_num = '8'; }
        if ($num_text == '9') { $emoji_num = '9'; }
      }
    } elseif ($num_text == '10') {
      if ($emoji_obj->hard == 'PC') {
        # PC絵文字画像(DoCoMo絵文字使用)
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0134'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } elseif ($emoji_obj->hard == 'DoCoMo') {
        # DoCoMo絵文字
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0134'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } elseif ($emoji_obj->hard == 'au') {
        # au絵文字
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0189'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } elseif ($emoji_obj->hard == $emoji_obj->softbank_name) {
        # SoftBank絵文字
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0227'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } else {
        # その他
        $emoji_num = '0';
      }
    } elseif (strlen($num_text) > 1) {
      if ($change_mode == '1') {
        $emoji_num = $num_text.':';
      } elseif ($change_mode == '2') {
        $emoji_num = '['.$num_text.']';
      } elseif ($change_mode == '3') {
        $emoji_num = 'No.'.$num_text;
      } else {
        $emoji_num = '□';
      }
    }
    return $emoji_num;
  }

  # 携帯情報取得 //////////////////////////////////////////////////////////////
  # 本関数は携帯の詳細情報を取得するための関数です。
  # [引渡し値]
  # 　$user_agent : ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ指定(指定無しの場合ｱｸｾｽ端末のﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ)
  # [返り値]
  # 　$RETURNDATA['hard']           : ｷｬﾘｱ(PC,DoCoMo,au,Vodafone)
  # 　$RETURNDATA['career']         : ｷｬﾘｱ(PC,PSP,DoCoMo,au,Vodafone)
  # 　$RETURNDATA['kubun']          : 区分(DoCoMo:FOMA/mova,au:win,SoftBank:3G)
  # 　$RETURNDATA['meka_name']      : ﾒｰｶｰ名
  # 　$RETURNDATA['kisyu_type']     : 機種名
  # 　$RETURNDATA['image_mime']     : 画像MIME
  # 　$RETURNDATA['image_kaku']     : ﾃﾞﾌｫﾙﾄ画像拡張子
  # 　$RETURNDATA['movie_mime']     : 動画MIME
  # 　$RETURNDATA['movie_kaku']     : ﾃﾞﾌｫﾙﾄ動画拡張子
  # 　$RETURNDATA['movie_size']     : ﾃﾞﾌｫﾙﾄ動画ｻｲｽﾞ
  # 　$RETURNDATA['down_size']      : ﾀﾞｳﾝﾛｰﾄﾞ動画最大ｻｲｽﾞ(KB)
  # 　$RETURNDATA['str_size']       : ｽﾄﾘｰﾐﾝｸﾞ動画最大ｻｲｽﾞ(KB)
  # 　$RETURNDATA['display_width']  : ﾃﾞｨｽﾌﾟﾚｲ幅(pt)
  # 　$RETURNDATA['display_height'] : ﾃﾞｨｽﾌﾟﾚｲ高さ(pt)
  # 　$RETURNDATA['display_color']  : ﾃﾞｨｽﾌﾟﾚｲ表示色数
  # 　$RETURNDATA['cache_size']     : ｷｬｯｼｭｻｲｽﾞ
  # 　$RETURNDATA['export_type']    : 動画処理用ﾀｲﾌﾟ指定1
  # 　$RETURNDATA['export_type2']   : 動画処理用ﾀｲﾌﾟ指定2
  #////////////////////////////////////////////////////////////////////////////
  function Get_PhoneData($user_agent='') {
    global $emoji_db_obj;

    # 返り値初期化
    $RETURNDATA = array();
    $RETURNDATA['hard']           = '';
    $RETURNDATA['career']         = '';
    $RETURNDATA['kubun']          = '';
    $RETURNDATA['meka_name']      = '';
    $RETURNDATA['kisyu_type']     = '';
    $RETURNDATA['image_mime']     = '';
    $RETURNDATA['image_kaku']     = '';
    $RETURNDATA['movie_mime']     = '';
    $RETURNDATA['movie_kaku']     = '';
    $RETURNDATA['movie_size']     = '';
    $RETURNDATA['down_size']      = '';
    $RETURNDATA['str_size']       = '';
    $RETURNDATA['display_width']  = '';
    $RETURNDATA['display_height'] = '';
    $RETURNDATA['display_color']  = '';
    $RETURNDATA['cache_size']     = '';
    $RETURNDATA['export_type']    = '';
    $RETURNDATA['export_type2']   = '';
    $KTDATA = array();

    # ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ処理
    if ($user_agent == '') { $user_agent = $_SERVER['HTTP_USER_AGENT']; }

    # 判定ﾃﾞｰﾀ生成
    $USRAGENT = array();
    $USRAGENT = explode('/',$user_agent);
    $maxnum   = count($USRAGENT) - 1;

    if (EMOJI_db_flag == '1') {
      # ﾃﾞｰﾀﾍﾞｰｽ使用
      # DB接続
      $emj_db_obj->db_connect();
      # 携帯情報ﾃﾞｰﾀﾍﾞｰｽ読込み
      $sql = "SELECT * FROM Phone_Spec";
      $sth = $emj_db_obj->sql_set_data(0,$sql,'','',EMOJI_save_ptn);
      while ($GETDATA = $emj_db_obj->sql_get_data(0,$sth,'','','loop','ass','1',EMOJI_read_ptn)) {
        $editdate = substr($GETDATA['editdate'],0,4).'/'.substr($GETDATA['editdate'],4,2).'/'.substr($GETDATA['editdate'],6,2).' '.substr($GETDATA['editdate'],8,2).':'.substr($GETDATA['editdate'],10,2);
        $KTDATA[] = $GETDATA['career']."\t".$GETDATA['kubun']."\t".$GETDATA['maker']."\t".$GETDATA['model']."\t".$GETDATA['yusen']."\t".$GETDATA['user_agent_patt']."\t".$GETDATA['sikibetu']."\t".$GETDATA['check_point']."\t".$GETDATA['check_string']."\t".$GETDATA['img_mime']."\t".$GETDATA['img_ext']."\t".$GETDATA['mov_mime']."\t".$GETDATA['mov_ext']."\t".$GETDATA['mov_size']."\t".$GETDATA['mov_download_max_size']."\t".$GETDATA['mov_stream_max_size']."\t".$GETDATA['display_width']."\t".$GETDATA['display_height']."\t".$GETDATA['display_color']."\t".$GETDATA['cache_size']."\t".$GETDATA['fitmov_patt_name1']."\t".$GETDATA['fitmov_patt_name2']."\t".$GETDATA['biko0']."\t".$GETDATA['biko1']."\t".$GETDATA['biko2']."\t".$editdate."\t\t\n";
      }
      # DB切断
      $emj_db_obj->db_disconnect();
    } else {
      # ﾌｧｲﾙﾃﾞｰﾀﾍﾞｰｽ使用
      # 携帯ﾃﾞｰﾀﾍﾞｰｽ読込み
      if ((EMOJI_mob_path == '') or !@file_exists(EMOJI_mob_path)) {
        $RETURNDATA['hard'] = 'PC';
        return $RETURNDATA;
      }
      $KTDATA = file(EMOJI_mob_path);
    }

    # ﾃﾞｰﾀ判定
    for ($ix = 1; $ix <= 2; $ix++) {
      $cfl = 0;
      foreach ($KTDATA as $kdt) {
        list($career,$kubun,$meka_name,$kisyu_type,$yusendo,$ue_pat,$hoho,$ichi,$patn,$image_mime,$image_kaku,$movie_mime,$movie_kaku,$movie_size,$down_size,$str_size,$display_width,$display_height,$display_color,$cache_size,$export_type,$export_type2,$biko0,$biko1,$biko2,$editdate) = explode("\t",$kdt);
        $patn = preg_replace('|/|','\/',$patn);
        $patn = preg_replace('|\(|','\(',$patn);
        if ($yusendo == $ix) {
          if ($hoho == 0) {
            # 部分一致
            if ($ichi == 0) {
              # 全文字列判定
              if (preg_match('/'.$patn.'/',$user_agent)) { $cfl = 1; break; }
            } else {
              # 部分文字列判定
              if ($maxnum >= $ichi - 1) {
                if (preg_match('/'.$patn.'/',$USRAGENT[$ichi - 1])) { $cfl = 1; break; }
              }
            }
          } elseif ($hoho == 1) {
            # 完全一致
            if ($ichi == 0) {
              # 全文字列判定
              if ($user_agent == $patn) { $cfl = 1; break; }
            } else {
              # 部分文字列判定
              if ($maxnum >= $ichi - 1) {
                if ($USRAGENT[$ichi - 1] == $patn) { $cfl = 1; break; }
              }
            }
          }
        }
      }
      if ($cfl == 1) { break; }
    }

    if ($cfl == 0) {
      if (preg_match('/PSP/',$user_agent)) {
        $hard         = 'PSP';
      } else {
        $hard         = 'PC';
      }
      $career         = 'PC';
      $kubun          = '';
      $meka_name      = '';
      $kisyu_type     = '';
      $image_mime     = '';
      $image_kaku     = '';
      $movie_mime     = '';
      $movie_kaku     = '';
      $movie_size     = '';
      $down_size      = '';
      $str_size       = '';
      $display_width  = '';
      $display_height = '';
      $display_color  = '';
      $cache_size     = '';
      $export_type    = '';
      $export_type2   = '';
    } else {
      $hard           = $career;
    }

    # 返り値設定
    $RETURNDATA = array();
    $RETURNDATA['hard']           = $hard;
    $RETURNDATA['career']         = $career;
    $RETURNDATA['kubun']          = $kubun;
    $RETURNDATA['meka_name']      = $meka_name;
    $RETURNDATA['kisyu_type']     = $kisyu_type;
    $RETURNDATA['image_mime']     = $image_mime;
    $RETURNDATA['image_kaku']     = $image_kaku;
    $RETURNDATA['movie_mime']     = $movie_mime;
    $RETURNDATA['movie_kaku']     = $movie_kaku;
    $RETURNDATA['movie_size']     = $movie_size;
    $RETURNDATA['down_size']      = $down_size;
    $RETURNDATA['str_size']       = $str_size;
    $RETURNDATA['display_width']  = $display_width;
    $RETURNDATA['display_height'] = $display_height;
    $RETURNDATA['display_color']  = $display_color;
    $RETURNDATA['cache_size']     = $cache_size;
    $RETURNDATA['export_type']    = $export_type;
    $RETURNDATA['export_type2']   = $export_type2;

    return $RETURNDATA;
  }

}

?>