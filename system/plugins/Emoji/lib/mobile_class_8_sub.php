<?php

###############################################################################
# �g�ъG�����ϊ�ײ���� 2008(�g���׽ײ����)
# Potora/inaken(C) 2008.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# 2008.10.07 v.1.00.00 �V�K
# 2008.11.28 v.1.00.01 ��۰��ٕϐ������ύX
###############################################################################

###############################################################################
# �G���������g���׽ ###########################################################
###############################################################################
class emoji_sub {
  # �ް�ޮݐݒ�
  var $ver = 'sub_v.1.00.00';

  # �ݽ�׸� ///////////////////////////////////////////////////////////////////
  function emoji_sub() {
  }

  # �G�����ϊ�ײ�����ް�ޮݎ擾 ///////////////////////////////////////////////
  # ��ر���ʂƋ@������擾���܂��B(�V����->����)
  # [���n���l]
  # �@�Ȃ�
  # [�Ԃ�l]
  # �@$this->ver : ײ�����ް�ޮ�
  #////////////////////////////////////////////////////////////////////////////
  function Get_Emj_Version() {
    return $this->ver;
  }

  # �@�햼�E�ő̎��ʔԍ��擾 //////////////////////////////////////////////////
  # �g�т̋@�햼�ƌ̎��ʔԍ����擾���܂��B
  # [���n���l]
  # �@$user_agent : հ�ް���ު�Ďw��(�w�薳���̏ꍇ�����[����հ�ް���ު��)
  # [�Ԃ�l]
  # �@$RETURNDATA['career']  : ��ر(DoCoMo,au,SoftBank or Vodafone)
  # �@$RETURNDATA['model']   : �@�햼
  # �@$RETURNDATA['devid']   : ���޲�ID
  # �@$RETURNDATA['ser']     : �̎��ʔԍ�(��޽�ײ��ID)
  # �@$RETURNDATA['icc']     : FOMA���ތ̎��ʎq
  # �@$RETURNDATA['imodeid'] : iӰ��ID
  #////////////////////////////////////////////////////////////////////////////
  function get_ser_no($user_agent='') {
    $career  = '';
    $model   = '';
    $devid   = '';
    $ser     = '';
    $icc     = '';
    $imodeid = '';
    # հ�ް���ު�Đݒ�
    if ($user_agent == '') {
      $user_agent = explode('/',$_SERVER['HTTP_USER_AGENT']);
    } else {
      $user_agent = explode('/',$user_agent);
    }
    # �@�햼�A�̎��ʔԍ��擾
    if ($user_agent[0] == 'DoCoMo') {
      # DoCoMo
      if (preg_match('/^1\..$/', $user_agent[1])) {
        # ��׳���ް�ޮ� 1.0
        $model = $user_agent[2];
        $devid = '';
        if (preg_match('/^ser(.+)/',$user_agent[4],$MATCH)) { $ser   = $MATCH[1]; }
        $icc   = '';
      } elseif (preg_match('/^2\..\s/', $user_agent[1],$MATCH)) {
        # ��׳���ް�ޮ� 2.0(FOMA)
        if (preg_match('/^2\..\s(.+?)\(/', $user_agent[1],$MATCH)) { $model = $MATCH[1]; }
        if (preg_match('/ser(.+?)[\s;]/' , $user_agent[1],$MATCH)) { $ser   = $MATCH[1]; }
        if (preg_match('/icc(.+?)\)/'    , $user_agent[1],$MATCH)) { $icc   = $MATCH[1]; }
      }
      if (isset($_SERVER['HTTP_X_DCMGUID'])) { $imodeid = $_SERVER['HTTP_X_DCMGUID']; }
      $career  = 'DoCoMo';
    } elseif (preg_match('/KDDI/',$user_agent[0]) or ($user_agent[0] == 'UP.Browser')) {
      # au(���@��)
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
        # 'J-PHONE'հ�ް���ު��
        if (preg_match('/^SN(.+?)\s.+$/',$user_agent[3],$MATCH)) { $ser = $MATCH[1]; }
      } elseif (preg_match('/Vodafone/',$user_agent[0]) or preg_match('/SoftBank/',$user_agent[0]) or preg_match('/Vemulator/',$user_agent[0])) {
        # 'Vodafone','SoftBank'հ�ް���ު��
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
    # �Ԃ�l�ݒ�
    $RETURNDATA = array();
    $RETURNDATA['career']  = $career;
    $RETURNDATA['model']   = $model;
    $RETURNDATA['devid']   = $devid;
    $RETURNDATA['ser']     = $ser;
    $RETURNDATA['icc']     = $icc;
    $RETURNDATA['imodeid'] = $imodeid;
    return $RETURNDATA;
  }

  # �G�������ލ폜 ////////////////////////////////////////////////////////////
  # �����񂩂�G�������폜���܂��B
  # [���n���l]
  # �@$textstr     : �ϊ��Ώە�����
  # �@$docomo_flag : DoCoMo�G�����폜(0:�폜����,1:�폜���Ȃ�)
  # �@$voda_flag   : SoftBank�G�����폜(0:�폜����,1:�폜���Ȃ�)
  # �@$au_flag     : au�G�����폜(0:�폜����,1:�폜���Ȃ�)
  # �@$out_code    : �ϊ���o�ͺ��ގw��
  # �@$enc_cancel  : �����ݺ��ޏ�����ݾَw��(1:��ݾ�)
  # �@$input_code : ���͕������ގw��(�w��Ȃ�:�ݒ�ɂ��AUTF-8����:UTF-8�A���̑�����:SJIS)
  # [�Ԃ�l]
  # �@$textstr     : �ϊ��㕶����
  #////////////////////////////////////////////////////////////////////////////
  function delete_emoji_code($textstr,$docomo_flag='0',$voda_flag='0',$au_flag='0',$out_code='',$enc_cancel='',$input_code='') {
    # �G�����폜
    $textstr = $this->emoji_str_replace($textstr,'',$docomo_flag,$voda_flag,$au_flag,$out_code,$enc_cancel,$input_code);
    return $textstr;
  }

  # �G�������މ��ʕϊ� ////////////////////////////////////////////////////////
  # �����񒆂̊G���������ʕϊ����܂��B
  # [���n���l]
  # �@$textstr     : �ϊ��Ώە�����
  # �@$docomo_flag : DoCoMo�G�������ʕϊ�(0:�ϊ�����,1:�ϊ����Ȃ�)
  # �@$voda_flag   : SoftBank�G�������ʕϊ�(0:�ϊ�����,1:�ϊ����Ȃ�)
  # �@$au_flag     : au�G�������ʕϊ�(0:�ϊ�����,1:�ϊ����Ȃ�)
  # �@$out_code    : �ϊ���o�ͺ��ގw��
  # �@$enc_cancel  : �����ݺ��ޏ�����ݾَw��(1:��ݾ�)
  # �@$input_code : ���͕������ގw��(�w��Ȃ�:�ݒ�ɂ��AUTF-8����:UTF-8�A���̑�����:SJIS)
  # [�Ԃ�l]
  # �@$textstr     : �ϊ��㕶����
  #////////////////////////////////////////////////////////////////////////////
  function emoji2geta($textstr,$docomo_flag='0',$voda_flag='0',$au_flag='0',$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    # �G�������ʕϊ�
    $textstr = $this->emoji_str_replace($textstr,$emoji_obj->geta_str,$docomo_flag,$voda_flag,$au_flag,$out_code,$enc_cancel,$input_code);
    return $textstr;
  }

  # �G�������ގw��÷�ĕϊ� ////////////////////////////////////////////////////
  # �����񒆂̊G�������w��̕�����ɕϊ����܂��B
  # [���n���l]
  # �@$textstr     : �ϊ��Ώە�����
  # �@$replace_str : �ϊ��Ώە�����
  # �@$docomo_flag : DoCoMo�G�������ʕϊ�(0:�ϊ�����,1:�ϊ����Ȃ�)
  # �@$voda_flag   : SoftBank�G�������ʕϊ�(0:�ϊ�����,1:�ϊ����Ȃ�)
  # �@$au_flag     : au�G�������ʕϊ�(0:�ϊ�����,1:�ϊ����Ȃ�)
  # �@$out_code    : �ϊ���o�ͺ��ގw��
  # �@$enc_cancel  : �����ݺ��ޏ�����ݾَw��(1:��ݾ�)
  # �@$input_code  : ���͕������ގw��(�w��Ȃ�:SJIS�AUTF-8����:UTF-8�A���̑�����:SJIS)
  # [�Ԃ�l]
  # �@$textstr     : �ϊ��㕶����
  #////////////////////////////////////////////////////////////////////////////
  function emoji_str_replace($textstr,$replace_str,$docomo_flag='0',$voda_flag='0',$au_flag='0',$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    if (isset($textstr)) {
      if ($out_code == '') { $oc = $emoji_obj->chr_code; } else { $oc = $out_code; }
      # �G�����ݺ���
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',1,$input_code); }
      # �ϊ��Ώە�����÷��Shift_JIS�ϊ�
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # �u����������÷��Shift_JIS�ϊ�
      if ($input_code == '') {
        $de = mb_detect_encoding($replace_str,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($replace_str,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $replace_str_code = mb_preferred_mime_name($de);
        if ($replace_str_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $replace_str = @mb_convert_encoding($replace_str,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # DoCoMo�G�����u����
      if ($docomo_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'d'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # au�G�����u����
      if ($au_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'a'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'am'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # SoftBank�G�����u����
      if ($voda_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$emoji_obj->DELIMITER[$i]['left'].$emoji_obj->DELIMITER[$i]['a'].'v'.$emoji_obj->DELIMITER[$i]['b'].'(\d+?)'.$emoji_obj->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # ÷�ĺ��ޕϊ�
      $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # �o�ͺ��ސݒ�
        if ($text_code != mb_preferred_mime_name($oc)) {
          # �������ނ��w��o�ͺ��ނƈقȂ�ꍇ
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) {
            # SJIS�w��̏ꍇ
            $textstr = @mb_convert_encoding($textstr,$oc,$emoji_obj->chg_code_sjis);
          } else {
            # SJIS�ȊO�̏ꍇ
            $textstr = @mb_convert_encoding($textstr,$oc,$text_code);
          }
        }
      }
    } else {
      $textstr = '';
    }
    return $textstr;
  }

  # ���������� ////////////////////////////////////////////////////////////////
  # ������̊G�����������������������Ă��܂��B
  # �޲�ض��Ă͊G������2�޲ĂƂ��Ķ��Ă��܂��B
  # [���n���l]
  # �@$textstr    : �����Ώە�����
  # �@$enc_cancel : �����ݺ��ޏ�����ݾَw��(1:��ݾ�)
  # �@$input_code : ���͕������ގw��(�w��Ȃ�:�ݒ�ɂ��AUTF-8����:UTF-8�A���̑�����:SJIS)
  # [�Ԃ�l]
  # �@$COUNTDATA['mb_strlen']   : �S������(����޲Ă�1�����Ƃ��Ķ���)
  # �@$COUNTDATA['mb_strwidth'] : �S�޲Đ�(���p:1,�S�p:2,�G����:2)
  # �@$COUNTDATA['total']       : �S�G������
  # �@$COUNTDATA['DoCoMo']      : DoCoMo�G������
  # �@$COUNTDATA['au']          : au�G������
  # �@$COUNTDATA['SoftBank']    : SoftBank�G������
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
      # �G�����ݺ���
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',$enc_cancel,$input_code); }

      # ÷��Shift_JIS�ϊ�
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # �������ď���
      $textstr_str = $textstr;
      while (preg_match('/\{(emj_._|d|a|am|v)[0-9]{4}\}/',$textstr_str)) {
        $textstr_str = preg_replace('/\{(emj_._|d|a|am|v)[0-9]{4}\}/',"\x82\xA0",$textstr_str);
      }
      # �S����������
      $COUNTDATA['mb_strlen']   = mb_strlen($textstr_str,'SJIS');
      # �S�޲Đ�����
      $COUNTDATA['mb_strwidth'] = mb_strwidth($textstr_str,'SJIS');
      # DoCoMo�G��������
      while (preg_match('/\{(emj_d_|d)[0-9]{4}\}/', $textstr)) {
        $textstr = preg_replace('/\{(emj_d_|d)[0-9]{4}\}/','',$textstr,1);
        $COUNTDATA['DoCoMo']++;
        $COUNTDATA['total']++;
      }
      # au�G��������
      while (preg_match('/\{(emj_a_|a|emj_am_|am)[0-9]{4}\}/', $textstr)) {
        $textstr = preg_replace('/\{(emj_a_|a|emj_am_|am)[0-9]{4}\}/','',$textstr,1);
        $COUNTDATA['au']++;
        $COUNTDATA['total']++;
      }
      # SoftBank�G��������
      while (preg_match('/\{(v|emj_v_)[0-9]{4}\}/', $textstr)) {
        $textstr = preg_replace('/\{(emj_v_|v)[0-9]{4}\}/','',$textstr,1);
        $COUNTDATA['SoftBank']++;
        $COUNTDATA['total']++;
      }
    }
    return $COUNTDATA;
  }

  # �����؂�l�� //////////////////////////////////////////////////////////////
  # �G�������܂ޕ�������w�肵�����ɐ؂�l�߂܂��B
  # ���G������2�޲ĂƂ��ď�������܂��B
  # [���n���l]
  # �@$textstr    : �����Ώە�����
  # �@$offset     : �J�n�ʒu
  # �@$width      : ������̕�
  # �@$end_str    : �؂�l�߂��ꍇ�̖ڈ�̕�����
  # �@$out_code   : �o�͕�������
  # �@$enc_cancel : �����ݺ��ޏ�����ݾَw��(1:��ݾ�)
  # �@$input_code : ���͕������ގw��(�w��Ȃ�:�ݒ�ɂ��AUTF-8����:UTF-8�A���̑�����:SJIS)
  # [�Ԃ�l]
  # �@$textstr    : �w�肳�ꂽ���̕�����
  #////////////////////////////////////////////////////////////////////////////
  function emj_strimwidth($textstr,$offset,$width,$end_str,$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    if (isset($textstr)) {
      if ($out_code == '') { $oc = $emoji_obj->chr_code; } else { $oc = $out_code; }
      $offset = 0;
      # �G�����ݺ���
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',1,$input_code); }
      # ÷��Shift_JIS�ϊ�
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # ������������
      $textstr_str = $textstr;
      $LISTUPDT = array();
      while (preg_match('/\{(emj_._|d|a|am|v)([0-9]{4})\}/',$textstr_str,$MATCH)) {
        $LISTUPDT[]  = '{'.$MATCH[1].$MATCH[2].'}';
        $textstr_str = preg_replace('/\{(emj_._|d|a|am|v)([0-9]{4})\}/',"\xEA\x9C",$textstr_str,1);
      }
      # ������؂�l��
      $textstr_str = mb_strimwidth($textstr_str,$offset,$width,$end_str);
      # �G�����߂�
      $loop_no = 0;
      while (preg_match('/\xEA\x9C/',$textstr_str,$MATCH)) {
        $textstr_str = preg_replace('/\xEA\x9C/',$LISTUPDT[$loop_no],$textstr_str,1);
        $loop_no++;
      }
      $textstr = $textstr_str;
      # ÷�ĺ��ޕϊ�
      $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # �o�ͺ��ސݒ�
        if ($text_code != mb_preferred_mime_name($oc)) {
          # �������ނ��w��o�ͺ��ނƈقȂ�ꍇ
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) {
            # SJIS�w��̏ꍇ
            $textstr = @mb_convert_encoding($textstr,$oc,$emoji_obj->chg_code_sjis);
          } else {
            # SJIS�ȊO�̏ꍇ
            $textstr = @mb_convert_encoding($textstr,$oc,$text_code);
          }
        }
      }
    }
    return $textstr;
  }

  # �G�����ϊ� ////////////////////////////////////////////////////////////////
  # �w��̊G������ʂ̎w�肵���G�����ɒu�������܂��B
  # [���n���l]
  # �@$textstr      : �����Ώە�����
  # �@$original_emj : ���G����
  # �@$change_emj   : �ϊ��G����
  # �@$out_code     : �o�͕�������
  # �@$enc_cancel   : �����ݺ��ޏ�����ݾَw��(1:��ݾ�)
  # �@$input_code : ���͕������ގw��(�w��Ȃ�:�ݒ�ɂ��AUTF-8����:UTF-8�A���̑�����:SJIS)
  # [�Ԃ�l]
  # �@$textstr             : �w�肳�ꂽ���̕�����
  #////////////////////////////////////////////////////////////////////////////
  function emj_change($textstr,$original_emj,$change_emj,$out_code='',$enc_cancel='',$input_code='') {
    global $emoji_obj;

    if (isset($textstr) and isset($original_emj) and isset($change_emj)) {
      if ($out_code == '') { $oc = $emoji_obj->chr_code; } else { $oc = $out_code; }
      # �G�����ݺ���
      if ($enc_cancel != '1') { $textstr = $emoji_obj->emj_encode($textstr,'',1,$input_code); }
      $original_emj = $emoji_obj->emj_encode($original_emj,'',1,$input_code);
      $change_emj   = $emoji_obj->emj_encode($change_emj,'',1,$input_code);
      # �G��������
      if (!preg_match('/\{(emj_d_|emj_a_|emj_am_|emj_v_|d|a|am|v)(\d{4})\}/',$original_emj)) { return $textstr; }
      if (!preg_match('/\{(emj_d_|emj_a_|emj_am_|emj_v_|d|a|am|v)(\d{4})\}/',$change_emj))   { return $textstr; }
      # ���G�����w�蕪��
      $ORIGINAL = array();
      $original_emj_sub = $original_emj;
      while (preg_match('/\{(emj_d_|emj_a_|emj_am_|emj_v_|d|a|am|v)(\d{4})\}/',$original_emj_sub,$MATCH)) {
        $ORIGINAL[] = '{'.$MATCH[1].$MATCH[2].'}';
        $original_emj_sub = preg_replace('/\{'.$MATCH[1].$MATCH[2].'\}/','',$original_emj_sub,1);
      }
      if (count($ORIGINAL) < 1) { return $textstr; }
      # �ϊ��G�����w�蕪��
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
      # ÷��Shift_JIS�ϊ�
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$emoji_obj->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$emoji_obj->chg_code_sjis,$text_code); }
      }
      # �G�����ϊ�
      $lpno = 0;
      foreach ($ORIGINAL as $odt) {
        if ($mode == 0) {
          $textstr = preg_replace('/'.$odt.'/',$CHANGE[0],$textstr);
        } elseif ($mode == 1) {
          $textstr = preg_replace('/'.$odt.'/',$CHANGE[$lpno],$textstr);
        }
        $lpno++;
      }
      # ÷�ĺ��ޕϊ�
      $de = mb_detect_encoding($textstr,$emoji_obj->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # �o�ͺ��ސݒ�
        if ($text_code != mb_preferred_mime_name($oc)) {
          # �������ނ��w��o�ͺ��ނƈقȂ�ꍇ
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($emoji_obj->chg_code_sjis)) {
            # SJIS�w��̏ꍇ
            $textstr = @mb_convert_encoding($textstr,$oc,$emoji_obj->chg_code_sjis);
          } else {
            # SJIS�ȊO�̏ꍇ
            $textstr = @mb_convert_encoding($textstr,$oc,$text_code);
          }
        }
      }
    }
    return $textstr;
  }

  # �G��������̫�їp�����ߏ��� ////////////////////////////////////////////////
  # �G��������̫�тŕ\�����邽�߂̑O�������s���܂��B
  # [���n���l]
  # �@$html : �����ߑΏە�����
  # [�Ԃ�l]
  # �@$html : �����ߏ����㕶����
  #////////////////////////////////////////////////////////////////////////////
  function emj_form_escape($html) {
    $html = preg_replace("/'/","\\'",$html);
    $html = preg_replace('/\r/','\\r',$html);
    $html = preg_replace('/\n/','\\n',$html);
    $html = preg_replace('/<br>/','',$html);
    return $html;
  }

  # �������G���������ϊ�(�ݺ��ޕ���) //////////////////////////////////////////
  # ��������G���������֕ϊ����܂��B
  # [���n���l]
  # �@$num_text    : ���l
  # �@$change_mode : 10�ȏ�̐��l�̏ꍇ�̕ϊ�����ݎw��
  # [�Ԃ�l]
  # �@$emoji_num : �ϊ�����
  #////////////////////////////////////////////////////////////////////////////
  function num2emojinum($num_text,$change_mode='') {
    global $emoji_obj;

    $emoji_num = '';
    if (!isset($num_text)) { return $emoji_num; }
    if ($num_text == '')   { return $emoji_num; }
    if (strlen($num_text) == 1) {
      if ($emoji_obj->hard == 'PC') {
        # PC�G�����摜(DoCoMo�G�����g�p)
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
        # DoCoMo�G����
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
        # au�G����
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
        # SoftBank�G����
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
        # ���̑�
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
        # PC�G�����摜(DoCoMo�G�����g�p)
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0134'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } elseif ($emoji_obj->hard == 'DoCoMo') {
        # DoCoMo�G����
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'d'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0134'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } elseif ($emoji_obj->hard == 'au') {
        # au�G����
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'a'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0189'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } elseif ($emoji_obj->hard == $emoji_obj->softbank_name) {
        # SoftBank�G����
        $emoji_num = $emoji_obj->DELIMITER[$emoji_obj->enc_type]['left'].$emoji_obj->DELIMITER[$emoji_obj->enc_type]['a'].'v'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['b'].'0227'.$emoji_obj->DELIMITER[$emoji_obj->enc_type]['right'];
      } else {
        # ���̑�
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
        $emoji_num = '��';
      }
    }
    return $emoji_num;
  }

  # �g�я��擾 //////////////////////////////////////////////////////////////
  # �{�֐��͌g�т̏ڍ׏����擾���邽�߂̊֐��ł��B
  # [���n���l]
  # �@$user_agent : հ�ް���ު�Ďw��(�w�薳���̏ꍇ�����[����հ�ް���ު��)
  # [�Ԃ�l]
  # �@$RETURNDATA['hard']           : ��ر(PC,DoCoMo,au,Vodafone)
  # �@$RETURNDATA['career']         : ��ر(PC,PSP,DoCoMo,au,Vodafone)
  # �@$RETURNDATA['kubun']          : �敪(DoCoMo:FOMA/mova,au:win,SoftBank:3G)
  # �@$RETURNDATA['meka_name']      : Ұ����
  # �@$RETURNDATA['kisyu_type']     : �@�햼
  # �@$RETURNDATA['image_mime']     : �摜MIME
  # �@$RETURNDATA['image_kaku']     : ��̫�ĉ摜�g���q
  # �@$RETURNDATA['movie_mime']     : ����MIME
  # �@$RETURNDATA['movie_kaku']     : ��̫�ē���g���q
  # �@$RETURNDATA['movie_size']     : ��̫�ē��滲��
  # �@$RETURNDATA['down_size']      : �޳�۰�ޓ���ő廲��(KB)
  # �@$RETURNDATA['str_size']       : ��ذ�ݸޓ���ő廲��(KB)
  # �@$RETURNDATA['display_width']  : �ި���ڲ��(pt)
  # �@$RETURNDATA['display_height'] : �ި���ڲ����(pt)
  # �@$RETURNDATA['display_color']  : �ި���ڲ�\���F��
  # �@$RETURNDATA['cache_size']     : ���������
  # �@$RETURNDATA['export_type']    : ���揈���p���ߎw��1
  # �@$RETURNDATA['export_type2']   : ���揈���p���ߎw��2
  #////////////////////////////////////////////////////////////////////////////
  function Get_PhoneData($user_agent='') {
    global $emoji_db_obj;

    # �Ԃ�l������
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

    # հ�ް���ު�ď���
    if ($user_agent == '') { $user_agent = $_SERVER['HTTP_USER_AGENT']; }

    # �����ް�����
    $USRAGENT = array();
    $USRAGENT = explode('/',$user_agent);
    $maxnum   = count($USRAGENT) - 1;

    if (EMOJI_db_flag == '1') {
      # �ް��ް��g�p
      # DB�ڑ�
      $emj_db_obj->db_connect();
      # �g�я���ް��ް��Ǎ���
      $sql = "SELECT * FROM Phone_Spec";
      $sth = $emj_db_obj->sql_set_data(0,$sql,'','',EMOJI_save_ptn);
      while ($GETDATA = $emj_db_obj->sql_get_data(0,$sth,'','','loop','ass','1',EMOJI_read_ptn)) {
        $editdate = substr($GETDATA['editdate'],0,4).'/'.substr($GETDATA['editdate'],4,2).'/'.substr($GETDATA['editdate'],6,2).' '.substr($GETDATA['editdate'],8,2).':'.substr($GETDATA['editdate'],10,2);
        $KTDATA[] = $GETDATA['career']."\t".$GETDATA['kubun']."\t".$GETDATA['maker']."\t".$GETDATA['model']."\t".$GETDATA['yusen']."\t".$GETDATA['user_agent_patt']."\t".$GETDATA['sikibetu']."\t".$GETDATA['check_point']."\t".$GETDATA['check_string']."\t".$GETDATA['img_mime']."\t".$GETDATA['img_ext']."\t".$GETDATA['mov_mime']."\t".$GETDATA['mov_ext']."\t".$GETDATA['mov_size']."\t".$GETDATA['mov_download_max_size']."\t".$GETDATA['mov_stream_max_size']."\t".$GETDATA['display_width']."\t".$GETDATA['display_height']."\t".$GETDATA['display_color']."\t".$GETDATA['cache_size']."\t".$GETDATA['fitmov_patt_name1']."\t".$GETDATA['fitmov_patt_name2']."\t".$GETDATA['biko0']."\t".$GETDATA['biko1']."\t".$GETDATA['biko2']."\t".$editdate."\t\t\n";
      }
      # DB�ؒf
      $emj_db_obj->db_disconnect();
    } else {
      # ̧���ް��ް��g�p
      # �g���ް��ް��Ǎ���
      if ((EMOJI_mob_path == '') or !@file_exists(EMOJI_mob_path)) {
        $RETURNDATA['hard'] = 'PC';
        return $RETURNDATA;
      }
      $KTDATA = file(EMOJI_mob_path);
    }

    # �ް�����
    for ($ix = 1; $ix <= 2; $ix++) {
      $cfl = 0;
      foreach ($KTDATA as $kdt) {
        list($career,$kubun,$meka_name,$kisyu_type,$yusendo,$ue_pat,$hoho,$ichi,$patn,$image_mime,$image_kaku,$movie_mime,$movie_kaku,$movie_size,$down_size,$str_size,$display_width,$display_height,$display_color,$cache_size,$export_type,$export_type2,$biko0,$biko1,$biko2,$editdate) = explode("\t",$kdt);
        $patn = preg_replace('|/|','\/',$patn);
        $patn = preg_replace('|\(|','\(',$patn);
        if ($yusendo == $ix) {
          if ($hoho == 0) {
            # ������v
            if ($ichi == 0) {
              # �S�����񔻒�
              if (preg_match('/'.$patn.'/',$user_agent)) { $cfl = 1; break; }
            } else {
              # ���������񔻒�
              if ($maxnum >= $ichi - 1) {
                if (preg_match('/'.$patn.'/',$USRAGENT[$ichi - 1])) { $cfl = 1; break; }
              }
            }
          } elseif ($hoho == 1) {
            # ���S��v
            if ($ichi == 0) {
              # �S�����񔻒�
              if ($user_agent == $patn) { $cfl = 1; break; }
            } else {
              # ���������񔻒�
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

    # �Ԃ�l�ݒ�
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