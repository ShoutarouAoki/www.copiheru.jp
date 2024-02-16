<?php
###############################################################################
# 携帯絵文字変換ﾗｲﾌﾞﾗﾘ 2008
# Potora/inaken(C) 2003-2009.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# 2008.10.07 v.8.00.00 全面改訂
# 2008.10.20 v.8.01.00 SMTPﾒｰﾙ送信機能追加(通常版のみ)
# 2008.10.23 v.8.01.01 SMTPﾒｰﾙ送信時CC,BCC指定不具合修正(通常版のみ)
# 2008.11.02 v.8.01.02 絵文字ﾒｰﾙ送信不具合修正(通常版のみ)
# 2008.11.28 v.8.01.03 DB仕様Heapﾃｰﾌﾞﾙｻｲｽﾞ取得方法修正
# 2008.11.28 v.8.01.04 ｸﾞﾛｰﾊﾞﾙ変数扱い変更
# 2009.02.28 v.8.01.05 画像表示位置ｽﾞﾚ修正
# 2009.03.17 v.8.01.06 replace_emoji_form引数指定不具合修正
# 2009.03.23 v.8.01.07 EUC-JPｺｰﾄﾞ絵文字ｴﾝｺｰﾄﾞ不具合修正
# 2009.04.11 v.8.01.08 ﾘｸｴｽﾄ前処理不具合,入力文字ｺｰﾄﾞ指定不具合修正
# 2009.04.11 v.8.01.09 DoCoMo絵文字Unicode指定変換不具合修正
# 2009.05.01 v.8.01.10 SoftBank絵文字ｴﾝｺｰﾄﾞ不具合修正
###############################################################################
# これまでの来歴
###############################################################################
# 2003.05.01 v.1.00.00 新規
# 2003.05.07 v.1.00.01 携帯絵文字表示不具合修正
# 2003.07.18 v.1.00.02 未対応文字適用不具合修正、PC画像枠消去
# 2003.07.24 v.1.00.03 au携帯HTML対応化
# 2003.09.01 v.1.01.00 au携帯HTML自動対応化
# 2003.09.05 v.1.01.01 URLｴﾝｺｰﾄﾞ見直し
# 2003.10.02 v.1.01.02 ﾊﾞｸﾞ修正
# 2003.11.11 v.1.01.03 AU認識修正
# 2004.02.06 v.2.00.00 ﾊｯｼｭ展開見直し、EUCｺｰﾄﾞ対応化
# 2004.09.17 v.3.00.00 PHP版作成
# 2005.01.17 v.3.01.00 PHP版au機種絵文字表示不具合修正
# 2005.01.22 v.3.02.00 処理見直し、一括変換機能、絵文字削除機能追加
# 2005.01.23 v.4.00.00 新ﾊﾞｰｼﾞｮﾝﾃﾞｰﾀﾍﾞｰｽ対応
# 2005.01.28 v.4.00.01 DoCoMo,au絵文字変換順序見直し
# 2005.02.04 v.4.00.02 ｴﾝｺｰﾄﾞ,ﾃﾞｺｰﾄﾞ変換不具合見直し
# 2005.02.07 v.4.00.03 ｸﾞﾛｰﾊﾞﾙ変数処理方法変更
# 2005.02.07 v.4.00.04 au拡張絵文字一時ﾌｨﾙﾀｰ処理追加
# 2005.02.13 v.4.00.05 au端末認識ﾊﾞｸﾞ修正
# 2005.02.13 v.4.01.00 固定絵文字ﾃﾞｰﾀ生成機能追加
# 2005.03.04 v.4.02.00 Vodafone新ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ対応
# 2005.03.24 v.5.00.00 ﾃﾞｰﾀﾍﾞｰｽ ver.6 対応
# 2005.04.20 v.5.00.01 絵文字ｴﾝｺｰﾄﾞ不具合修正
# 2005.04.22 v.5.00.02 携帯ﾃﾞｰﾀ取得時の不足ﾃﾞｰﾀに対する処理方法変更
# 2005.05.24 v.5.01.00 DoCoMo絵文字ｶﾗｰ化、DoCoMo拡張絵文字処理適正化、au絵文字ﾌｫｰﾑ表示対応化
# 2005.06.13 v.5.01.01 au固定絵文字表示不具合修正
# 2005.07.28 v.5.01.02 DoCoMo絵文字Unicode記述対応不具合修正
# 2005.08.18 v.5.01.03 DoCoMo絵文字Unicode記述処理不具合修正
# 2005.09.23 v.6.00.00 ｸﾗｽﾗｲﾌﾞﾗﾘ化
# 2005.11.08 v.6.00.03 文字ｺｰﾄﾞ扱い不具合修正
# 2006.02.02 v.6.01.00 ﾒｰﾙ対応
# 2006.02.14 v.6.01.01 旧ｴﾝｺｰﾄﾞﾃﾞｰﾀ対応追加
# 2006.02.14 v.6.01.02 指定ｷｬﾘｱ強制変換機能追加
# 2006.02.15 v.6.01.03 携帯無限ﾙｰﾌﾟ不具合修正
# 2006.02.20 v.6.01.04 自動初期化時の機種判別結果の変数渡し追加、au携帯処理不具合修正
# 2006.02.21 v.6.01.05 DoCoMo絵文字ｺｰﾄﾞUnicode扱いﾊﾞｸﾞ修正
# 2006.03.01 v.6.01.06 固定絵文字格納変数ｸﾞﾛｰﾊﾞﾙ変数化
# 2006.03.13 v.6.01.07 Vodafone 3G UTF-8ｺｰﾄﾞ絵文字対応
# 2006.03.18 v.6.01.08 携帯詳細情報取得関数追加
# 2006.04.05 v.6.01.09 ﾃﾞｺｰﾄﾞ状態絵文字削除処理追加
# 2006.05.09 v.6.01.10 絵文字ﾒｰﾙ送信関数不具合修正
# 2006.05.09 v.6.01.11 DoCoMo固体識別番号取得不具合修正
# 2006.05.12 v.6.01.12 PCﾌｫｰﾑ表示不具合暫定対策
# 2006.05.14 v.6.01.13 DoCoMo宛絵文字ﾒｰﾙ変換不具合修正
# 2006.05.15 v.6.01.14 ﾒｰﾙ絵文字変換不具合、ﾒｰﾙ送信関数不具合修正
# 2006.05.18 v.6.01.15 auﾒｰﾙｴﾝｺｰﾄﾞ→ﾃﾞｺｰﾄﾞ不具合修正
# 2006.05.18 v.6.01.16 携帯絵文字変換不具合修正
# 2006.05.29 v.6.01.17 初期化(DoCoMoﾒｰﾙ用ﾃﾞｰﾀﾊｯｼｭ展開)不具合修正
# 2006.06.10 v.6.01.18 ﾒｰﾙ処理不具合(au絵文字ｺｰﾄﾞ誤判定)修正
# 2006.06.12 v.6.01.19 絵文字HTMLﾒｰﾙ送信機能追加
# 2006.06.14 v.6.01.20 Vodafone 3G UTF-8ｺｰﾄﾞ絵文字変換不具合修正
# 2006.06.18 v.6.01.21 絵文字ﾒｰﾙ送信ｺｰﾄﾞ不具合修正
# 2006.08.14 v.6.01.22 Willcomﾌﾗｸﾞ追加、PC HTMLﾒｰﾙ処理不具合修正
# 2006.08.18 v.6.01.23 DoCoMo個体識別番号取得不具合修正
# 2006.08.19 v.6.01.24 絵文字ﾌｫｰﾑ表示不具合修正
# 2006.10.05 v.6.02.00 SoftBank対応,Get_PhoneData関数修正,Get_Hardware関数追加
# 2006.10.18 v.6.02.01 絵文字数ｶｳﾝﾄ不具合修正
# 2006.10.19 v.6.02.02 絵文字ﾒｰﾙ送信関数不具合修正
# 2006.10.22 v.6.02.03 絵文字ﾒｰﾙBASE64ｴﾝｺｰﾄﾞ対応
# 2006.11.13 v.6.02.04 au宛ﾒｰﾙ送信絵文字ｺｰﾄﾞ不具合修正
# 2006.11.22 v.6.02.05 絵文字ﾒｰﾙ送信関数不具合修正
# 2006.11.24 v.6.02.06 絵文字削除関数、下駄変換関数不具合修正
# 2006.11.28 v.6.02.07 ﾊﾞｰｼﾞｮﾝ処理、初期化不具合修正
# 2006.12.26 v.6.02.08 DoCoMo拡張文字ｶﾗｰ処理不具合修正
# 2006.12.27 v.6.02.09 携帯情報取得不具合修正
# 2007.01.09 v.6.02.10 DoCoMo拡張文字ﾌｫｰﾑ表示置換え処理不具合修正
# 2007.01.15 v.6.02.11 DoCoMo拡張文字ﾌｫｰﾑ表示置換え処理不具合修正2
# 2007.01.16 v.6.02.12 DoCoMo拡張文字処理、絵文字削除不具合修正
# 2007.01.16 v.6.02.13 絵文字削除不具合修正2
# 2007.02.09 v.6.02.15 ﾒｰﾙ送信関数ﾌｧｲﾙ添付機能追加
# 2007.02.11 v.6.02.16 ﾒｰﾙ送信関数不具合修正
# 2007.06.24 v.6.02.17 個体識別番号取得関数引渡し変数追加
# 2007.08.01 v.7.00.00 全面改訂
# 2007.08.08 v.7.00.01 ｸﾗｽ変数宣言不具合修正
# 2007.08.08 v.7.00.02 au表示不具合対策
# 2007.08.09 v.7.00.03 DBﾌｧｲﾙ読込み不具合処理,au入力不具合修正
# 2007.08.10 v.7.00.04 絵文字ｴﾝｺｰﾄﾞ時ｺｰﾄﾞ変換不具合修正
# 2007.08.11 v.7.01.00 emj_strimwidth,emj_change関数追加
# 2007.08.16 v.7.01.01 DoCoMo扱い絵文字ｺｰﾄﾞShift_JISﾃｷｽﾄ→Unicodeﾃｷｽﾄ変更
# 2007.08.17 v.7.01.02 絵文字変換(ﾒｰﾙ用)不具合修正
# 2007.08.24 v.7.01.03 出力文字ｺｰﾄﾞ処理不具合修正
# 2007.08.25 v.7.02.00 UTF-8ｺｰﾄﾞ対応不具合修正
# 2007.08.27 v.7.02.01 SoftBank UTF-8ｺｰﾄﾞ対応不具合,auﾒｰﾙｺｰﾄﾞ設定不具合修正
# 2007.08.28 v.7.02.02 絵文字ｴﾝｺｰﾄﾞ不具合修正
# 2007.08.28 v.7.02.03 ﾗｲﾌﾞﾗﾘ初期化不具合修正
# 2007.08.28 v.7.02.04 SoftBank絵文字ｴﾝｺｰﾄﾞﾊﾞｸﾞ修正
# 2007.08.28 v.7.02.05 絵文字ｴﾝｺｰﾄﾞ不具合修正(UTF-8ｺｰﾄﾞ対応による不具合対策)
# 2007.09.05 v.7.02.06 ﾃﾞｰﾀﾍﾞｰｽｵﾌﾞｼﾞｪｸﾄ指定不具合修正
# 2007.09.05 v.7.02.07 SoftBank UTF-8ｺｰﾄﾞ処理不具合修正
# 2007.10.03 v.7.02.08 文字ｺｰﾄﾞ認識順位最適化,ﾌｧｲﾙDBﾊﾞｰｼﾞｮﾝ認識化
# 2007.10.04 v.7.02.09 文字ｺｰﾄﾞ認識不具合修正,UTF-8ｴﾝｺｰﾄﾞ不具合修正
# 2007.10.05 v.7.02.10 au絵文字ｴﾝｺｰﾄﾞ不具合修正
# 2007.10.09 v.7.02.11 出力ｺｰﾄﾞ変換不具合修正
# 2007.10.10 v.7.02.12 絵文字ｴﾝｺｰﾄﾞTYPE-2ｴﾝｺｰﾄﾞ不具合修正
# 2007.10.11 v.7.02.13 au絵文字ｴﾝｺｰﾄﾞ不具合修正
# 2007.10.17 v.7.03.00 絵文字ﾒｰﾙ送信関数追加
# 2007.10.23 v.7.03.01 数字絵文字化機能追加
# 2007.11.05 v.7.03.02 SoftBankUTF-8 TYPE2ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝ不具合修正,ﾌｫｰﾑ表示用HTMLｴﾝﾃｨﾃｨ処理不具合修正
# 2007.12.04 v.7.03.03 emoji_send_mail3関数不具合修正
# 2007.12.05 v.7.03.04 emoji_send_mail3関数ｷｬﾘｱ指定不具合修正
# 2007.12.11 v.7.03.05 DoCoMo絵文字ﾃｷｽﾄｺｰﾄﾞｴﾝｺｰﾄﾞ処理追加
# 2007.12.14 v.7.04.00 ﾃﾞｺﾒ対応,SoftBank3G入力処理不具合修正
# 2007.12.16 v.7.04.01 ﾃﾞｺﾒ送信emoji_decome2関数因数設定修正
# 2007.12.16 v.7.04.02 ﾃﾞｺﾒｸﾗｽ組込み不具合修正
# 2007.12.18 v.7.04.03 絵文字ｴﾝｺｰﾄﾞ無限ﾙｰﾌﾟ不具合修正
# 2007.12.20 v.7.04.04 au絵文字ｴﾝｺｰﾄﾞ無限ﾙｰﾌﾟ不具合修正
# 2007.12.23 v.7.04.05 MIME取得不具合修正不具合修正
# 2007.12.25 v.7.04.06 MIME取得不具合修正不具合修正
# 2008.03.29 v.7.04.07 変換引渡し値不具合見直し
# 2008.03.30 v.7.04.08 個体識別番号取得関数の任意ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ指定不具合修正,DoCoMo DB不具合修正
# 2008.04.01 v.7.05.00 ﾌｫｰﾑ入力自動絵文字ｴﾝｺｰﾄﾞ機能追加
# 2008.04.15 v.7.05.01 emoji_send_mail3関数ﾌｧｲﾙ添付送信不具合見直し
# 2008.04.17 v.7.05.02 emoji_send_mail3関数本文生成不具合修正
# 2008.04.18 v.7.05.03 emoji_send_mail3関数絵文字ﾒｰﾙ送信不具合修正
# 2008.06.11 v.7.06.00 ﾃﾞﾊﾞｯｸﾓｰﾄﾞ追加,新ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝ追加,初期化ﾒｿｯﾄﾞ追加
# 2008.07.01 v.7.06.01 初期化時端末情報取得不具合,ﾃﾞﾘﾐﾀ不具合修正
# 2008.07.07 v.7.06.02 初期化時機種情報取得不足(kisyu_type)追加
# 2008.07.17 v.7.06.03 SoftBank携帯絵文字ﾒｰﾙ送信対応
# 2008.07.18 v.7.06.04 SoftBank携帯絵文字ﾒｰﾙ送信対応不具合,ﾃﾞﾘﾐﾀ設定不具合修正
# 2008.07.19 v.7.06.05 絵文字ﾃﾞｺｰﾄﾞ正規表現不具合修正
# 2008.07.25 v.7.06.06 ﾃﾞｺﾒ送信機能 件名･本文半角ｶﾀｶﾅ全角変換ｷｬﾝｾﾙ機能追加
# 2008.07.25 v.7.06.07 SoftBankｼｭﾐﾚｰﾀ対応
# 2008.08.05 v.7.06.08 iﾓｰﾄﾞID取得機能追加
# 2008.08.29 v.7.06.09 iﾓｰﾄﾞID取得機能不具合修正
###############################################################################
$setting_file	= EMOJI_SETTING_FILE;
$emoji_obj		= new emoji();
###############################################################################
# 絵文字処理基本ｸﾗｽ ###########################################################
###############################################################################
class emoji 
{
  # ﾊﾞｰｼﾞｮﾝ設定
  var $ver = 'v.8.01.10';
  #############################################################################
  # ﾒｲﾝｽｸﾘﾌﾟﾄからﾗｲﾌﾞﾗﾘ設定ﾌｧｲﾙへの位置を指定します
  var $settingc_file = EMOJI_SETTING_FILE;
  #############################################################################
  var $setting_file  = EMOJI_SETTING_FILE;
  # ﾃﾞｰﾀﾍﾞｰｽﾌｧｲﾙ設定
  var $emj_path_b;       # 絵文字対応ﾃﾞｰﾀﾍﾞｰｽ
  var $emj_path_d;       # DoCoMo絵文字ﾃﾞｰﾀﾍﾞｰｽ
  var $emj_path_v;       # SoftBank絵文字ﾃﾞｰﾀﾍﾞｰｽ
  var $emj_path_a;       # au絵文字ﾃﾞｰﾀﾍﾞｰｽ
  var $emj_path_am;      # auﾒｰﾙ用絵文字ﾃﾞｰﾀﾍﾞｰｽ
  var $mob_path;         # 携帯情報ﾃﾞｰﾀﾍﾞｰｽ

  var $emj_path;         # 絵文字ﾃﾞｰﾀﾍﾞｰｽ位置設定
  var $emjimg_path;      # 絵文字画像位置設定
  var $emoji_non;        # 未対応絵文字対応
  var $emoji_chr;        # 未対応絵文字潰し文字
  var $fitimg_path;      # 画像変換ｽｸﾘﾌﾟﾄ指定
  var $chr_code;         # ｽｸﾘﾌﾟﾄ扱い文字ｺｰﾄﾞ指定(Shift_JIS,EUC-JP)
  var $emojiset;         # 固定絵文字ﾊﾟﾀｰﾝ指定
  var $init_flag = '';   # ﾗｲﾌﾞﾗﾘ初期化設定
  var $color_flag;       # DoCoMo絵文字ｶﾗｰ化設定
  var $enc_type;         # ｴﾝｺｰﾄﾞﾀｲﾌﾟ設定
  var $old_enc_flag;     # 旧ｴﾝｺｰﾄﾞﾀｲﾌﾟ処理設定
  var $geta_str;         # 下駄文字設定
#  var $htmlarea_flag;    # HTMLArea使用設定

  var $img_onry_flag;    # 画像表示のみﾌﾗｸﾞ
  var $dec_to_code_flag; # DoCoMo,auﾃﾞｺｰﾄﾞ後復元ｺｰﾄﾞ処理ﾌﾗｸﾞ

  var $hard;             # ｷｬﾘｱ判別
  var $hard_k;           # 区分
  var $ez_flag;          # au機種ﾌﾗｸﾞ
  var $cac;              # ｷｬｯｼｭ容量
  var $mheight;          # ﾃﾞｨｽﾌﾟﾚｲ高さ
  var $mwidth;           # ﾃﾞｨｽﾌﾟﾚｲ幅
  var $mcolor;           # 解像度
  var $will_flag;        # Willcomﾌﾗｸﾞ
  var $chg_code_sjis;    # Shift-JISｺｰﾄﾞの扱いｺｰﾄﾞﾀｲﾌﾟ
  var $chg_code_euc;     # EUC-JPｺｰﾄﾞの扱いｺｰﾄﾞﾀｲﾌﾟ

  var $debug_flag = '';  # ﾃﾞﾊﾞｯｸﾓｰﾄﾞﾌﾗｸﾞ

  # 携帯機種情報保存配列初期化
  var $HARD_DATA  = array();  # 端末情報ﾃﾞｰﾀ
  var $PHONE_DATA = array();  # 携帯情報ﾃﾞｰﾀ

  # DoCoMo用配列初期化
  var $DOCOMO_NO_TO_NAME       = array();
  var $DOCOMO_NO_TO_FILE       = array();
  var $DOCOMO_NO_TO_IMG        = array();
  var $DOCOMO_NO_TO_IMG_MAIL   = array();
  var $DOCOMO_SJIS10_TO_NO     = array();
  var $DOCOMO_UTF8_TO_NO       = array();
  var $DOCOMO_UNI_TO_SIS10     = array();
  var $DOCOMO_NO_TO_BIN        = array();
  var $DOCOMO_NO_TO_BIN_UTF8   = array();
  var $DOCOMO_NO_TO_TXT        = array();
  var $DOCOMO_NO_TO_UTXT       = array();
  var $DOCOMO_NO_TO_BIN_COLOR  = array();
  var $DOCOMO_NO_TO_TXT_COLOR  = array();
  var $DOCOMO_NO_TO_UTXT_COLOR = array();

  # SoftBank用配列初期化
  var $SOFT_NO_TO_NAME       = array();
  var $SOFT_NO_TO_FILE       = array();
  var $SOFT_NO_TO_IMG        = array();
  var $SOFT_NO_TO_IMG_MAIL   = array();
  var $SOFT_NO_TO_WEBCODE    = array();
  var $SOFT_WEBCODE_TO_NO    = array();
  var $SOFT3G_DEC_TO_WEBCODE = array();
  var $SOFT3G_DEC_TO_NO      = array();
  var $SOFT3G_NO_TO_UTF8     = array();

  # au用配列初期化
  var $AU_NO_TO_NAME     = array();
  var $AU_NO_TO_FILE     = array();
  var $AU_NO_TO_IMG      = array();
  var $AU_NO_TO_IMG_MAIL = array();
  var $AU_NO_TO_SJIS10   = array();
  var $AU_SJIS10_TO_NO   = array();
  var $AU_UTF8_TO_NO     = array();
  var $AU_NO_TO_MAILCODE = array();
  var $AU_NO_TO_BIN      = array();
  var $AU_NO_TO_BIN_UTF8 = array();
  var $AU_NO_TO_BIN_MAIL = array();
  var $AU_NO_TO_TXT      = array();
  var $AU_NO_TO_TXT_WIN  = array();

  # 変換対応配列初期化
  var $DOCOMO_TO_SOFT = array();
  var $DOCOMO_TO_AU   = array();
  var $SOFT_TO_DOCOMO = array();
  var $SOFT_TO_AU     = array();
  var $AU_TO_DOCOMO   = array();
  var $AU_TO_SOFT     = array();

  # ｴﾝｺｰﾄﾞ/ﾃﾞｺｰﾄﾞ用配列初期化
  var $ENC_TYPE1 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ1
  var $ENC_TYPE2 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ2
  var $ENC_TYPE3 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ3
  var $ENC_TYPE4 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ4
  var $ENC_TYPE5 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ5
  var $ENC_TYPE6 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ6
  var $ENC_TYPE7 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ7
  var $ENC_TYPE8 = array();   # ｴﾝｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ8

  var $DEC_TYPE1 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ1
  var $DEC_TYPE2 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ2
  var $DEC_TYPE3 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ3
  var $DEC_TYPE4 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ4
  var $DEC_TYPE5 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ5
  var $DEC_TYPE6 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ6
  var $DEC_TYPE7 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ7
  var $DEC_TYPE8 = array();   # ﾃﾞｺｰﾄﾞﾊﾟﾀｰﾝﾃﾞｰﾀ - ﾀｲﾌﾟ8

  # 固定絵文字用配列初期化
  var $FIX_EMJ = array();

  # DB文字変換ﾊﾟﾀｰﾝ設定
  var $save_ptn = '';
  var $read_ptn = '';

  # ﾃﾞｰﾀﾍﾞｰｽｵﾌﾞｼﾞｪｸﾄ
  var $emj_db_obj;

  # 文字ｺｰﾄﾞｴﾝｺｰﾄﾞﾘｽﾄ初期化
  # var $ENCODINGLIST = array();

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

  # ﾃﾞﾘﾐﾀ設定
  var $DELIMITER = array(
    1 => array('left'=>'{'    ,'a'=>'emj_' ,'b'=>'_' ,'right'=>'}'),
    2 => array('left'=>'{'    ,'a'=>''     ,'b'=>''  ,'right'=>'}'),
    3 => array('left'=>'{#'   ,'a'=>'emj_' ,'b'=>'_' ,'right'=>'#}'),
    4 => array('left'=>'{#'   ,'a'=>''     ,'b'=>''  ,'right'=>'#}'),
    5 => array('left'=>'###'  ,'a'=>'emj_' ,'b'=>'_' ,'right'=>'###'),
    6 => array('left'=>'###'  ,'a'=>''     ,'b'=>''  ,'right'=>'###'),
    7 => array('left'=>'<!--' ,'a'=>'emj_' ,'b'=>'_' ,'right'=>'-->'),
    8 => array('left'=>'<!--' ,'a'=>''     ,'b'=>''  ,'right'=>'-->'),
  );

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
  var $inline_max_size_docomo      = 100000;      # ｲﾝﾗｲﾝ画像ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $inline_all_max_size_docomo  = 0;          # ｲﾝﾗｲﾝ画像ﾄｰﾀﾙﾌｧｲﾙｻｲｽﾞ制限(Byte)
  var $upfile_max_num_docomo       = 0;          # 添付ﾌｧｲﾙ数制限
  var $upfile_max_size_docomo      = 0;          # 添付ﾌｧｲﾙｻｲｽﾞ制限(1ﾌｧｲﾙ最大ｻｲｽﾞ)(Byte)
  var $upfile_all_max_size_docomo  = 102400;      # 添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $allfile_max_num_docomo      = 0;          # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙ数制限
  var $allfile_max_size_docomo     = 0;          # ｲﾝﾗｲﾝ画像、添付ﾌｧｲﾙﾄｰﾀﾙｻｲｽﾞ制限(Byte)
  var $body_max_size_docomo        = 102400;      # 本文(ﾃｷｽﾄ+HTML)ﾄｰﾀﾙｻｲｽﾞ制限(Byte)
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
  var $body_max_size_au        = 100000;     # 本文(ﾃｷｽﾄ+HTML)ﾄｰﾀﾙｻｲｽﾞ制限(Byte)
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

  function startup (&$controller) {
    $this->controller = $controller;
  }
  # ｺﾝｽﾄﾗｸﾀ ///////////////////////////////////////////////////////////////////
  # [引渡し値]
  # 　$setting_file : 設定ﾌｧｲﾙ指定
  # 　$auto_flag    : 自動実行指定(1:ｷｬﾝｾﾙ)
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function emoji ($setting_file='',$auto_flag='',$empty='') {

    if (!isset($emj_lite_flag)) { $emj_lite_flag = False; }

    # 設定ﾌｧｲﾙ設定
    if ($setting_file != '') {
      $this->setting_file = $setting_file;
    } else {
      if ($this->setting_file == '') { $this->setting_file = $settingc_file; }
    }
    # 設定ﾌｧｲﾙ読込み
    if (file_exists($this->setting_file)) {
      $SETTING_DATA = array();
      $SETTING_DATA = file($this->setting_file);
      foreach ($SETTING_DATA as $sdt) {
        if ($sdt == '') { break; }
        list($namedt,$setdt) = explode("\t",$sdt);
        $this->$namedt = $setdt;
        if (!defined('EMOJI_'.$namedt)) { define('EMOJI_'.$namedt,$setdt); }
      }
      if ($this->geta_str == '') { $this->geta_str = '〓'; }
    } else {
      # 設定ﾌｧｲﾙが見つからない場合
      print 'Emoji Change Library Setting Data File Error.';
      exit();
    }

    if (!defined('EMOJI_delimiter_flag')) { define('EMOJI_delimiter_flag','1'); }
    if (!defined('EMOJI_emj_lite_flag'))  { define('EMOJI_emj_lite_flag',True); }

    # ﾗｲﾄ版認識

    if (file_exists(dirname(__FILE__).'/mobile_class_8_sub.php') and 
      file_exists(dirname(__FILE__).'/decome_class.php') and 
      file_exists(dirname(__FILE__).'/mobile_class_8_mail.php')) {
      if (EMOJI_emj_lite_flag == True) {
        if ($emj_lite_flag == True) {
          define('EMJ_LITE_FLAG',True);
        } else {
          define('EMJ_LITE_FLAG',False);
        }
      } else {
        if ($emj_lite_flag == True) {
          define('EMJ_LITE_FLAG',True);
        } else {
          define('EMJ_LITE_FLAG',False);
        }
      }
    } else {
      if (!defined('EMJ_LITE_FLAG')) { define('EMJ_LITE_FLAG',True); }
      #define('EMJ_LITE_FLAG',True);
    }

    # ｴﾝｺｰﾄﾞﾀｲﾌﾟ再設定
    if (EMJ_LITE_FLAG == True) { $this->enc_type = 1; }

    # 文字ｺｰﾄﾞ変換設定
    if ($this->db_flag == '1') {
      # ﾃﾞｰﾀﾍﾞｰｽ仕様
      if ($this->db_code == 'SJIS') {
      } elseif ($this->db_code == 'EUC-JP') {
        $this->save_ptn = 'StoE';
        $this->read_ptn = 'EtoS';
      } elseif ($this->db_code == 'UTF-8') {
        $this->save_ptn = 'StoU';
        $this->read_ptn = 'UtoS';
      }
      define('EMOJI_save_ptn',$this->save_ptn);
      define('EMOJI_read_ptn',$this->read_ptn);
    }

    # ﾃﾞｰﾀﾍﾞｰｽﾌｧｲﾙ設定
    $this->emj_path_b  = $this->emj_path.'/emoji.cgi';         # 絵文字対応ﾃﾞｰﾀﾍﾞｰｽ
    $this->emj_path_d  = $this->emj_path.'/docomo.cgi';        # DoCoMo絵文字ﾃﾞｰﾀﾍﾞｰｽ
    $this->emj_path_v  = $this->emj_path.'/vodafone.cgi';      # SoftBank絵文字ﾃﾞｰﾀﾍﾞｰｽ
    $this->emj_path_a  = $this->emj_path.'/au.cgi';            # au絵文字ﾃﾞｰﾀﾍﾞｰｽ
    $this->emj_path_am = $this->emj_path.'/au_mail.cgi';       # auﾒｰﾙ用絵文字ﾃﾞｰﾀﾍﾞｰｽ
    $this->mob_path    = $this->emj_path.'/mobile.cgi';        # 携帯情報ﾃﾞｰﾀﾍﾞｰｽ
    if (!defined('EMOJI_mob_path')) { define('EMOJI_mob_path',$this->mob_path); }

    # 初期化自動実行
    if ($this->db_flag != '1') {
      # ﾌｧｲﾙ仕様の場合のみ
      if (($this->init_flag == '') or ($this->init_flag == '0')) {
        if (empty($empty)) {
          # ﾗｲﾌﾞﾗﾘ自動初期化
          $this->_auto_init();
        } else {
          # ﾗｲﾌﾞﾗﾘ自動初期化
          $this->_empty_auto_init($empty);
        }
      }
    }
    # 文字ｺｰﾄﾞｴﾝｺｰﾄﾞﾘｽﾄ設定
    if (!isset($this->encode_list_sjis)) { $this->encode_list_sjis = 'SJIS-win,SJIS,JIS,EUC-JP,UTF-8'; }
    if (!isset($this->encode_list_euc))  { $this->encode_list_euc  = 'EUC-JP,SJIS-win,SJIS,JIS,UTF-8'; }
    if (!isset($this->encode_list_utf8)) { $this->encode_list_utf8 = 'UTF-8,SJIS-win,SJIS,JIS,EUC-JP'; }
    if (!isset($this->encode_list_jis))  { $this->encode_list_jis  = 'SJIS-win,SJIS,JIS,EUC-JP,UTF-8'; }
    $this->ENCODINGLIST = array(
      'Shift_JIS'   => $this->encode_list_sjis,
      'SJIS'        => $this->encode_list_sjis,
      'SJIS-win'    => $this->encode_list_sjis,
      'EUC-JP'      => $this->encode_list_euc,
      'EUC'         => $this->encode_list_euc,
      'eucJP-win'   => $this->encode_list_euc,
      'UTF-8'       => $this->encode_list_utf8,
      'JIS'         => $this->encode_list_jis,
      'ISO-2022-JP' => $this->encode_list_jis,
    );


    # 値設定
    if (!defined('EMOJI_smtp_flag')) { define('EMOJI_smtp_flag','0'); }

    if (EMOJI_smtp_flag == 1) {
        # SMTP接続先設定
      $this->this_server = '';
      $this->smtp_server = '';
      $this->smtp_port   = 25;
      $this->pop3_server = '';
      $this->pop3_port   = 110;
      $this->mail_user   = '';
      $this->mail_pass   = '';
      $this->auth        = True;
      $this->auth_type   = 'POP';
      if (defined('EMOJI_this_server')) { $this->this_server = EMOJI_this_server; }
      if (defined('EMOJI_smtp_server')) { $this->smtp_server = EMOJI_smtp_server; }
      if (defined('EMOJI_smtp_port'))   { $this->smtp_port   = EMOJI_smtp_port; }
      if (defined('EMOJI_pop3_server')) { $this->pop3_server = EMOJI_pop3_server; }
      if (defined('EMOJI_pop3_port'))   { $this->pop3_port   = EMOJI_pop3_port; }
      if (defined('EMOJI_mail_user'))   { $this->mail_user   = EMOJI_mail_user; }
      if (defined('EMOJI_mail_pass'))   { $this->mail_pass   = EMOJI_mail_pass; }
      if (defined('EMOJI_auth'))        { $this->auth        = EMOJI_auth; }
      if (defined('EMOJI_auth_tyle'))   { $this->auth_tyle   = EMOJI_auth_tyle; }
    }

	if (isset($this->emj_auto_in_flag)) {
	  if ($this->emj_auto_in_flag >= 1) {
	    $base_code = '';
	    if ($this->emj_auto_in_flag == 2) {
	      if ($this->HARD_DATA['hard'] == $this->softbank_name) { $base_code = 'UTF-8'; }
	    }
	    $cc = $this->chr_code;
	    if ($this->chr_code == 'Shift_JIS') { $cc = $this->chg_code_sjis; }
	    if ($this->chr_code == 'EUC-JP')    { $cc = $this->chg_code_euc; }
	    $conv = '';
	    if (isset($this->emj_auto_in_hensu_r)) { $conv .= $this->emj_auto_in_hensu_r; }
	    if (isset($this->emj_auto_in_hensu_g)) { $conv .= $this->emj_auto_in_hensu_g; }
	    if (isset($this->emj_auto_in_hensu_p)) { $conv .= $this->emj_auto_in_hensu_p; }
	    if ($conv != '') {
	      if (isset($this->emj_auto_in_kana)) {
	        $this->reqest_data_conv($conv,$this->emj_auto_in_kana,$cc,$base_code);
	      } else {
	        $this->reqest_data_conv($conv,'',$cc,$base_code);
	      }
	    }
	  }
	}
  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘ初期化自動実行 ////////////////////////////////////////////
  # ﾗｲﾌﾞﾗﾘ初期化時に自動実行する関数を指定します。
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function _empty_auto_init($empty) {
    # 機種判別、情報取得
    $HARDDATA = $this->Get_Hardware($empty);   # 機種判別,auﾌﾗｸﾞ,ｷｬｯｼｭ,高さ,幅,色数
    # ﾗｲﾌﾞﾗﾘ初期化
    $this->read_emojidata();             # 絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘ初期化自動実行 ////////////////////////////////////////////
  # ﾗｲﾌﾞﾗﾘ初期化時に自動実行する関数を指定します。
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function _auto_init() {
    # 機種判別、情報取得
    $HARDDATA = $this->Get_Hardware();   # 機種判別,auﾌﾗｸﾞ,ｷｬｯｼｭ,高さ,幅,色数
    # ﾗｲﾌﾞﾗﾘ初期化
    $this->read_emojidata();             # 絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘ初期化(手動実行用)-ver. ////////////////////////////////////////
  # ﾗｲﾌﾞﾗﾘ初期化を任意に指定します。
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function Emoji_init() {
    # 機種判別、情報取得
    $HARDDATA = $this->Get_Hardware();   # 機種判別,auﾌﾗｸﾞ,ｷｬｯｼｭ,高さ,幅,色数
    # ﾗｲﾌﾞﾗﾘ初期化
    $this->read_emojidata();             # 絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ取得 ///////////////////////////////////////////////
  # ｷｬﾘｱ判別と機種情報を取得します。(新処理->推奨)
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　$ver : ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ
  #////////////////////////////////////////////////////////////////////////////
  function Get_Emj_Version() {
    $ver = $this->ver;
    if (EMJ_LITE_FLAG == True) { $ver .= 'L'; }
    return $ver;
  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘﾓｰﾄﾞ取得 ///////////////////////////////////////////////
  # 絵文字変換ﾗｲﾌﾞﾗﾘの動作ﾓｰﾄﾞを取得します。
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　Lite:ﾗｲﾄ版,Normal:通常版
  #////////////////////////////////////////////////////////////////////////////
  function Get_Emj_Mode() {
    if (EMJ_LITE_FLAG == True) { return 'Lite'; }
    return 'Normal';
  }

  # 機種判別・携帯情報取得 ////////////////////////////////////////////////////
  # ｷｬﾘｱ判別と機種情報を取得します。(新処理->推奨)
  # [引渡し値]
  # 　$huag            : ﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ指定(指定無しの場合ｱｸｾｽ端末のﾕｰｻﾞｰｴｰｼﾞｪﾝﾄ)
  # 　$career_get_flag : ｷｬﾘｱ識別方法指定(標準3ｷｬﾘｱ識別の場合"3"(ﾃﾞﾌｫﾙﾄ),Willcomも識別の場合"4")
  # [返り値]
  # 　$RETURNDATA['hard']           : ｷｬﾘｱ判別結果(PC,DoCoMo,au,SoftBank or Vodafone,Willcom)
  # 　$RETURNDATA['will_flag']      : Willcom携帯の場合"1"
  # 　$RETURNDATA['tg_flag']        : DoCoMo 3G -> "FOMA",au 3G -> "WIN",SoctBank 3G -> "3G"
  # 　$RETURNDATA['cache_size']     : 携帯ｷｬｯｼｭｻｲｽﾞ(KB)(PCの場合無し)
  # 　$RETURNDATA['display_height'] : 携帯ﾃﾞｨｽﾌﾟﾚｲ高さ(pt)
  # 　$RETURNDATA['display_width']  : 携帯ﾃﾞｨｽﾌﾟﾚｲ幅(pt)
  # 　$RETURNDATA['display_color']  : 携帯ﾃﾞｨｽﾌﾟﾚｲ表示色数
  #////////////////////////////////////////////////////////////////////////////
  function Get_Hardware($huag='',$career_get_flag='3') {

    if ($huag == '') { $huag = $_SERVER['HTTP_USER_AGENT']; }
    $hard       = 'PC';
    $tg_flag    = '';
    $will_flag  = 0;
    $user_agent = explode('/', $huag);
    if (preg_match('/KDDI/',$user_agent[0])) {
      # au
      $hard    = 'au';
      $tg_flag = 'WIN';
    } elseif ($user_agent[0] == 'DoCoMo') {
      # DoCoMo
      $hard    = 'DoCoMo';
      if ($user_agent[1] == '2.0') { $tg_flag = 'FOMA'; }
    } elseif ($user_agent[0] == 'L-mode') {
      # Lﾓｰﾄﾞ
      $hard    = 'DoCoMo';
    } elseif ($user_agent[0] == 'ASTEL') {
      # ASTEL
      $hard    = 'DoCoMo';
    } elseif ($user_agent[0] == 'UP.Browser') {
      # au(旧機種)
      $hard    = 'au';
    } elseif (($user_agent[0] == 'DDIPOCKET') or ($user_agent[0] == 'PDXGW')) {
      # PDXGW(Willcom)
      if ($career_get_flag == '4') {
        $hard  = 'DoCoMo';
      } else {
        $hard  = 'Willcom';
      }
      $will_flag = 1;
    } elseif (preg_match("/(J-PHONE)|(Vodafone)|(MOT)|(Vemulator)/",$user_agent[0]) or ($user_agent[0] == 'SoftBank')) {
      # Vodafone,SoftBank
      $hard    = $this->softbank_name;
      if (preg_match('/(Vodafone)|(MOT)|(Vemulator)/',$user_agent[0]) or ($user_agent[0] == 'SoftBank')) { $tg_flag = '3G'; }
    } else {
      if ($this->debug_flag == '1') {
        if (preg_match("/Vemulator/",$user_agent[0])) {
          # SoftBankｴﾐｭﾚｰﾀー
          $hard    = $this->softbank_name;
          $tg_flag = '3G';
        }
      } else {
        $hard    = 'PC';
      }
    }

    # 機種情報取得
    $cache_size_s     = '';
    $display_height_s = '';
    $display_width_s  = '';
    $display_color_s  = '';
    $PHONEDATA = array();
    if (EMJ_LITE_FLAG == False) {
      $PHONEDATA = $this->Get_PhoneData();
    }

    # 携帯個体識別番号取得
    $career  = '';
    $model   = '';
    $devid   = '';
    $ser     = '';
    $icc     = '';
    $imodeid = '';
    $SER_RETDATA = array();
    if (EMJ_LITE_FLAG == False) {
      $SER_RETDATA = $this->get_ser_no($huag);
    }

    # 返り値設定
    $RETURNDATA = array();
    $RETURNDATA['hard']           = '';
    $RETURNDATA['will_flag']      = '';
    $RETURNDATA['tg_flag']        = '';
    $RETURNDATA['cache_size']     = '';
    $RETURNDATA['display_height'] = '';
    $RETURNDATA['display_width']  = '';
    $RETURNDATA['display_color']  = '';
    $RETURNDATA['kisyu_type']     = '';
    $RETURNDATA['model']          = '';
    $RETURNDATA['devid']          = '';
    $RETURNDATA['ser']            = '';
    $RETURNDATA['icc']            = '';
    $RETURNDATA['imodeid']        = '';
    if (isset($hard))                        { $RETURNDATA['hard']           = $hard; }
    if (isset($will_flag))                   { $RETURNDATA['will_flag']      = $will_flag; }
    if (isset($tg_flag))                     { $RETURNDATA['tg_flag']        = $tg_flag; }
    if (isset($PHONEDATA['cache_size']))     { $RETURNDATA['cache_size']     = $PHONEDATA['cache_size']; }
    if (isset($PHONEDATA['display_height'])) { $RETURNDATA['display_height'] = $PHONEDATA['display_height']; }
    if (isset($PHONEDATA['display_width']))  { $RETURNDATA['display_width']  = $PHONEDATA['display_width']; }
    if (isset($PHONEDATA['display_color']))  { $RETURNDATA['display_color']  = $PHONEDATA['display_color']; }
    if (isset($PHONEDATA['kisyu_type']))     { $RETURNDATA['kisyu_type']     = $PHONEDATA['kisyu_type']; }
    if (isset($SER_RETDATA['model']))        { $RETURNDATA['model']          = $SER_RETDATA['model']; }
    if (isset($SER_RETDATA['devid']))        { $RETURNDATA['devid']          = $SER_RETDATA['devid']; }
    if (isset($SER_RETDATA['ser']))          { $RETURNDATA['ser']            = $SER_RETDATA['ser']; }
    if (isset($SER_RETDATA['icc']))          { $RETURNDATA['icc']            = $SER_RETDATA['icc']; }
    if (isset($SER_RETDATA['imodeid']))      { $RETURNDATA['imodeid']        = $SER_RETDATA['imodeid']; }

    # ﾗｲﾌﾞﾗﾘ値設定
    $this->HARD_DATA  = array();
    $this->PHONE_DATA = array();
    if (is_array($RETURNDATA))        { $this->HARD_DATA  = $RETURNDATA; }
    if (is_array($PHONEDATA))         { $this->PHONE_DATA = $PHONEDATA; }

    $this->PHONE_DATA['model']   = '';
    $this->PHONE_DATA['devid']   = '';
    $this->PHONE_DATA['ser']     = '';
    $this->PHONE_DATA['icc']     = '';
    $this->PHONE_DATA['imodeid'] = '';
    if (isset($SER_RETDATA['model']))   { $this->PHONE_DATA['model']   = $SER_RETDATA['model']; }
    if (isset($SER_RETDATA['devid']))   { $this->PHONE_DATA['devid']   = $SER_RETDATA['devid']; }
    if (isset($SER_RETDATA['ser']))     { $this->PHONE_DATA['ser']     = $SER_RETDATA['ser']; }
    if (isset($SER_RETDATA['icc']))     { $this->PHONE_DATA['icc']     = $SER_RETDATA['icc']; }
    if (isset($SER_RETDATA['imodeid'])) { $this->PHONE_DATA['imodeid'] = $SER_RETDATA['imodeid']; }

    return $RETURNDATA;
  }


  # ﾗｲﾌﾞﾗﾘ初期化 //////////////////////////////////////////////////////////////
  # ﾗｲﾌﾞﾗﾘを初期化します。
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function read_emojidata() {

    # 基本ﾃﾞｰﾀﾍﾞｰｽ読込み
    $EMJDATA_BASE   = array();
    $EMJDATA_DOCOMO = array();
    $EMJDATA_SOFT   = array();
    $EMJDATA_AU     = array();

    if ($this->db_flag == '1') {
      # ﾃﾞｰﾀﾍﾞｰｽ使用
      # DB接続
      $emj_db_obj->db_connect();
      # 絵文字変換対応ﾃﾞｰﾀﾍﾞｰｽ読込み
      $sql = "SELECT * FROM emj_emoji ORDER BY Base_emj_id";
      $sth = $emj_db_obj->sql_set_data(0,$sql,'','',$this->save_ptn);
      while ($GETDATA = $emj_db_obj->sql_get_data(0,$sth,'','','loop','ass','1',$this->read_ptn)) {
        $EMJDATA_BASE[] = $GETDATA['Base_emj_id']."\t".$GETDATA['script_code']."\t".$GETDATA['DoCoMo_no']."\t".$GETDATA['SoftBank_no']."\t".$GETDATA['au_no']."\t".$GETDATA['yusen_no']."\t";
      }
      # DoCoMo絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
      $sql = "SELECT * FROM emj_DoCoMo ORDER BY DoCoMo_emj_id";
      $sth = $emj_db_obj->sql_set_data(0,$sql,'','',$this->save_ptn);
      while ($GETDATA = $emj_db_obj->sql_get_data(0,$sth,'','','loop','ass','1',$this->read_ptn)) {
        $EMJDATA_DOCOMO[] = $GETDATA['DoCoMo_emj_id']."\t".$GETDATA['emj_name']."\t".$GETDATA['emj_file']."\t".$GETDATA['sjis16']."\t".$GETDATA['sjis10']."\t".$GETDATA['web_code']."\t".$GETDATA['unicode']."\t".$GETDATA['color']."\t\r\n";
      }

      # au絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
      $sql = "SELECT * FROM emj_au ORDER BY au_emj_id";
      $sth = $emj_db_obj->sql_set_data(0,$sql,'','',$this->save_ptn);
      while ($GETDATA = $emj_db_obj->sql_get_data(0,$sth,'','','loop','ass','1',$this->read_ptn)) {
        $EMJDATA_AU[] = $GETDATA['au_emj_id']."\t".$GETDATA['emj_name']."\t".$GETDATA['emj_file']."\t".$GETDATA['sjis16']."\t".$GETDATA['sjis10']."\t".$GETDATA['web_code']."\t".$GETDATA['unicode']."\t".$GETDATA['mail_code']."\t".$GETDATA['mail_code']."\t\r\n";
      }

      # SoftBank絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
      $sql = "SELECT * FROM emj_SoftBank ORDER BY SoftBank_emj_id";
      $sth = $emj_db_obj->sql_set_data(0,$sql,'','',$this->save_ptn);
      while ($GETDATA = $emj_db_obj->sql_get_data(0,$sth,'','','loop','ass','1',$this->read_ptn)) {
        $EMJDATA_SOFT[] = $GETDATA['SoftBank_emj_id']."\t".$GETDATA['emj_name']."\t".$GETDATA['emj_file']."\t".$GETDATA['sjis16']."\t".$GETDATA['mail_code']."\t".$GETDATA['web_code']."\t".$GETDATA['unicode']."\t".$GETDATA['utf_8']."\t\r\n";
      }
    } else {
      # ﾌｧｲﾙﾃﾞｰﾀﾍﾞｰｽ使用
      # 絵文字変換対応ﾃﾞｰﾀﾍﾞｰｽ読込み
      if (file_exists($this->emj_path_b)) {
        if (!$EMJDATA_BASE = @file($this->emj_path_b)) {
          print 'Emoji DataBase File Read Error.';
          exit();
        }
      } else {
        print 'Emoji DataBase File Read Error.';
        exit();
      }
      # DoCoMo絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
      if (file_exists($this->emj_path_d)) {
        if (!$EMJDATA_DOCOMO = @file($this->emj_path_d)) {
          print 'DoCoMo Emoji DataBase File Read Error.';
          exit();
        }
        # 絵文字ﾃﾞｰﾀﾍﾞｰｽﾊﾞｰｼﾞｮﾝﾁｪｯｸ
        $FDT = explode("\t",$EMJDATA_DOCOMO[0]);
        if (count($FDT) < 10) {
          print 'DoCoMo Emoji DataBase File is Old Format Error.';
          exit();
        }
      } else {
        print 'DoCoMo Emoji DataBase File Read Error.';
        exit();
      }
      # SoftBank絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
      if (file_exists($this->emj_path_v)) {
        if (!$EMJDATA_SOFT = file($this->emj_path_v)) {
          print 'SoftBank Emoji DataBase File Read Error.';
          exit();
        }
      } else {
        print 'SoftBank Emoji DataBase File Read Error.';
        exit();
      }
      # au絵文字ﾃﾞｰﾀﾍﾞｰｽ読込み
      if (file_exists($this->emj_path_a)) {
        if (!$EMJDATA_AU = file($this->emj_path_a)) {
          print 'au Emoji DataBase File Read Error.';
          exit();
        }
        # 絵文字ﾃﾞｰﾀﾍﾞｰｽﾊﾞｰｼﾞｮﾝﾁｪｯｸ
        $FDT = explode("\t",$EMJDATA_AU[0]);
        if (count($FDT) < 10) {
          print 'au Emoji DataBase File is Old Format Error.';
          exit();
        }
      } else {
        print 'au Emoji DataBase File Read Error.';
        exit();
      }
      # ﾗﾍﾞﾙ削除
      array_shift($EMJDATA_DOCOMO);
      array_shift($EMJDATA_SOFT);
      array_shift($EMJDATA_AU);
      # 絵文字変換対応ﾃﾞｰﾀﾍﾞｰｽﾊﾞｰｼﾞｮﾝ取得
      $e_ver = $EMJDATA_BASE[0];
      if ($e_ver != '') { array_splice($EMJDATA_BASE,0,2); }
    }

    # ﾃﾞﾘﾐﾀ設定取得設定(Line版はﾃﾞﾌｫﾙﾄの {emj_*_####} ﾊﾟﾀｰﾝのみ)
    if (EMJ_LITE_FLAG == True) {
      $set_deli = 1;
      $loop_num = 1;
    } else {
      if (EMOJI_delimiter_flag == '1') {
        $set_deli = EMOJI_enc_type;
        $loop_num = 1;
      } else {
        $set_deli = 1;
        $loop_num = 8;
      }
    }

    # DoCoMo用絵文字ﾃﾞｰﾀ配列展開
    foreach ($EMJDATA_DOCOMO as $edt) {
      if ($edt != '') {
        list($eno,$ename,$efile,$esjis16,$esjis10,$eweb,$euni,$color,$eutf8) = explode("\t",$edt);
        if (isset($eutf8) and preg_match('/^[0-9a-fA-F]{6}$/',$eutf8)) { $utf8c = substr($eutf8,2); }
        # 絵文字名設定
        $this->DOCOMO_NO_TO_NAME[$eno] = $ename;
        # 絵文字画像ﾌｧｲﾙ設定
        $this->DOCOMO_NO_TO_FILE[$eno] = $efile;
        # 絵文字画像表示設定
        $img_opt = '';
        if ($this->img_title_flag == '1') { $img_opt .= ' title="'.$ename.'"'; }
        if ($this->img_alt_flag   == '1') { $img_opt .= ' alt="'.$ename.'"'; }
        if ($this->fitimg_path) {
          # Fitimg使用する場合
#          $this->DOCOMO_NO_TO_IMG[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0" align="center"'.$img_opt.'>';
          $this->DOCOMO_NO_TO_IMG[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0"'.$img_opt.'>';
          if (EMJ_LITE_FLAG == False) {
#            $this->DOCOMO_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0" align="center">';
            $this->DOCOMO_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0">';
          }
        } else {
          # Fitimg使用しない場合
#          $this->DOCOMO_NO_TO_IMG[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0" align="center"'.$img_opt.'>';
          $this->DOCOMO_NO_TO_IMG[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0"'.$img_opt.'>';
          if (EMJ_LITE_FLAG == False) {
#            $this->DOCOMO_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0" align="center">';
            $this->DOCOMO_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0">';
          }
        }
        $this->DOCOMO_SJIS10_TO_NO[$esjis10] = $eno;
        $this->DOCOMO_UTF8_TO_NO[0] = '';
        if (isset($utf8c)) {
          $this->DOCOMO_UTF8_TO_NO[hexdec($utf8c)] = $eno;
        }
        $this->DOCOMO_UNI_TO_SIS10[$euni] = $esjis10;
        # ﾊﾞｲﾅﾘｺｰﾄﾞ設定
        $this->DOCOMO_NO_TO_BIN[$eno] = pack("H4",$esjis16);
        if (isset($eutf8) and preg_match('/^[0-9a-fA-F]{6}$/',$eutf8)) {
          $this->DOCOMO_NO_TO_BIN_UTF8[$eno] = pack("H6",$eutf8);
        }
        # ﾃｷｽﾄｺｰﾄﾞ設定
        if ($eno < 1000) {
          # SJIS(基本絵文字)
          $this->DOCOMO_NO_TO_TXT[$eno] = "&#{$esjis10};";
        } else {
          # Unicode(拡張絵文字)
          $this->DOCOMO_NO_TO_TXT[$eno] = "&#x{$euni};";
        }
        $this->DOCOMO_NO_TO_UTXT[$eno] = "&#x{$euni};";
        # ｶﾗｰ設定
        if (($this->color_flag == 1) and preg_match('/#[0-9a-fA-F]{6}/',$color)) {
          # ｶﾗｰ指定あり
          $this->DOCOMO_NO_TO_BIN_COLOR[$eno]  = '<font color="'.$color.'">'.$this->DOCOMO_NO_TO_BIN[$eno].'</font>';
          $this->DOCOMO_NO_TO_TXT_COLOR[$eno]  = '<font color="'.$color.'">'.$this->DOCOMO_NO_TO_TXT[$eno].'</font>';
          $this->DOCOMO_NO_TO_UTXT_COLOR[$eno] = '<font color="'.$color.'">'.$this->DOCOMO_NO_TO_UTXT[$eno].'</font>';
        } else {
          # ｶﾗｰ指定なし
          $this->DOCOMO_NO_TO_BIN_COLOR[$eno]  = $this->DOCOMO_NO_TO_BIN[$eno];
          $this->DOCOMO_NO_TO_TXT_COLOR[$eno]  = $this->DOCOMO_NO_TO_TXT[$eno];
          $this->DOCOMO_NO_TO_UTXT_COLOR[$eno] = $this->DOCOMO_NO_TO_UTXT[$eno];
        }
        # ｴﾝｺｰﾄﾞ展開
        for ($i = $set_deli; $i <= $loop_num; $i++) {
          # ﾃｷｽﾄｺｰﾄﾞ展開(SJISｷｰ…10進)
          $this->{'ENC_TYPE'.$i}[$esjis10] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'d'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
          # ﾃｷｽﾄｺｰﾄﾞ展開(Unicodeｷｰ…16進)
          $this->{'ENC_TYPE'.$i}[$euni] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'d'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
          # ﾃｷｽﾄｺｰﾄﾞ展開(Unicodeｷｰ…10進)
          $this->{'ENC_TYPE'.$i}[hexdec('0x'.$euni)] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'d'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
          # ﾊﾞｲﾅﾘ展開
          $this->{'ENC_TYPE'.$i}[$this->DOCOMO_NO_TO_BIN[$eno]] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'d'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
        }
        if (isset($this->DOCOMO_NO_TO_BIN_UTF8[$eno]) and ($this->DOCOMO_NO_TO_BIN_UTF8[$eno] != '')) {
          for ($i = $set_deli; $i <= $loop_num; $i++) {
            $this->{'ENC_TYPE'.$i}[$this->DOCOMO_NO_TO_BIN_UTF8[$eno]] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'d'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
          }
        }
      }
    }

    # SoftBank用絵文字ﾃﾞｰﾀ配列展開
    foreach ($EMJDATA_SOFT as $edt) {
      if ($edt != '') {
        list($eno,$ename,$efile,$esjis16,$emailcd,$eweb,$euni,$eutf8) = explode("\t",$edt);
        # 絵文字名設定
        $this->SOFT_NO_TO_NAME[$eno] = $ename;
        # 絵文字画像ﾌｧｲﾙ設定
        $this->SOFT_NO_TO_FILE[$eno] = $efile;
        # 絵文字画像表示設定
        $img_opt = '';
        if ($this->img_title_flag == '1') { $img_opt .= ' title="'.$ename.'"'; }
        if ($this->img_alt_flag   == '1') { $img_opt .= ' alt="'.$ename.'"'; }
        if ($this->fitimg_path) {
          # Fitimg使用する場合
#          $this->SOFT_NO_TO_IMG[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0" align="center"'.$img_opt.'>';
          $this->SOFT_NO_TO_IMG[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0"'.$img_opt.'>';
          if (EMJ_LITE_FLAG == False) {
#            $this->SOFT_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0" align="center">';
            $this->SOFT_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0">';
          }
        } else {
          # Fitimg使用しない場合
#          $this->SOFT_NO_TO_IMG[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0" align="center"'.$img_opt.'>';
          $this->SOFT_NO_TO_IMG[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0"'.$img_opt.'>';
          if (EMJ_LITE_FLAG == False) {
#            $this->SOFT_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0" align="center">';
            $this->SOFT_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0">';
          }
        }
        $this->SOFT_NO_TO_WEBCODE[$eno]  = $eweb;
        $this->SOFT_WEBCODE_TO_NO[$eweb] = $eno;
        $decdt = hexdec(substr($eutf8,2));
        $this->SOFT3G_DEC_TO_WEBCODE[$decdt] = $eweb;
        $this->SOFT3G_DEC_TO_NO[$decdt] = $eno;
        if (isset($eutf8) and preg_match('/^[0-9a-fA-F]{6}$/',$eutf8)) {
          $this->SOFT3G_NO_TO_UTF8[$eno] = pack("H6",$eutf8);
        }
        # ｴﾝｺｰﾄﾞ用展開
        for ($i = $set_deli; $i <= $loop_num; $i++) {
          $this->{'ENC_TYPE'.$i}[$eweb] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'v'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
        }
      }
    }

    # au用絵文字ﾃﾞｰﾀ配列展開
    foreach ($EMJDATA_AU as $edt) {
      if ($edt != '') {
        list($eno,$ename,$efile,$esjis16,$esjis10,$eweb,$euni,$esjis16m,$eutf8) = explode("\t",$edt);
        if (isset($eutf8) and preg_match('/^[0-9a-fA-F]{6}$/',$eutf8)) { $utf8c = substr($eutf8,2); }
        # 絵文字名設定
        $this->AU_NO_TO_NAME[$eno] = $ename;
        # 絵文字画像ﾌｧｲﾙ設定
        $this->AU_NO_TO_FILE[$eno] = $efile;
        # 絵文字画像表示設定
        $img_opt = '';
        if ($this->img_title_flag == '1') { $img_opt .= ' title="'.$ename.'"'; }
        if ($this->img_alt_flag   == '1') { $img_opt .= ' alt="'.$ename.'"'; }
        if ($this->fitimg_path) {
          # Fitimg使用する場合
#          $this->AU_NO_TO_IMG[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0" align="center"'.$img_opt.'>';
          $this->AU_NO_TO_IMG[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0"'.$img_opt.'>';
          if (EMJ_LITE_FLAG == False) {
#            $this->AU_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0" align="center">';
            $this->AU_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->fitimg_path.'/fitimg.php?file='.$this->emjimg_path.'/'.$efile.'&w='.$this->fitimg_size.'" border="0">';
          }
        } else {
          # Fitimg使用しない場合
#          $this->AU_NO_TO_IMG[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0" align="center"'.$img_opt.'>';
          $this->AU_NO_TO_IMG[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0"'.$img_opt.'>';
          if (EMJ_LITE_FLAG == False) {
#            $this->AU_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0" align="center">';
            $this->AU_NO_TO_IMG_MAIL[$eno] = '<img src="'.$this->emjimg_path.'/'.$efile.'" border="0">';
          }
        }
        $this->AU_NO_TO_SJIS10[$eno]     = $esjis10;
        $this->AU_SJIS10_TO_NO[$esjis10] = $eno;
        if (EMJ_LITE_FLAG == False) {
          $this->AU_NO_TO_MAILCODE[$eno] = hexdec($esjis16m);
          $this->AU_SJIS10_TO_NO[$this->AU_NO_TO_MAILCODE[$eno]] = $eno;
        }
        if (isset($utf8c) and ($utf8c != '')) { $this->AU_UTF8_TO_NO[hexdec($utf8c)] = $eno; }
        # ﾊﾞｲﾅﾘｺｰﾄﾞ設定
        $this->AU_NO_TO_BIN[$eno] = pack("H4",$esjis16);
        if (isset($eutf8) and preg_match('/^[0-9a-fA-F]{6}$/',$eutf8)) {
          $this->AU_NO_TO_BIN_UTF8[$eno] = pack("H6",$eutf8);
        }
        if (EMJ_LITE_FLAG == False) {
          $this->AU_NO_TO_BIN_MAIL[$eno] = pack("H4",$esjis16m);
        }
        # ﾃｷｽﾄｺｰﾄﾞ設定
        $enos = preg_replace('|^0*|','',$eno);
        $this->AU_NO_TO_TXT_WIN[$eno] = '<img localsrc="'.$enos.'">';
        $this->AU_NO_TO_TXT[$eno]     = '<IMG ICON="'.$enos.'">';
        # ｴﾝｺｰﾄﾞ展開
        for ($i = $set_deli; $i <= $loop_num; $i++) {
          # ｴﾝｺｰﾄﾞ展開
          $this->{'ENC_TYPE'.$i}[$esjis10] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'a'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
          $this->{'ENC_TYPE'.$i}[$this->AU_NO_TO_BIN[$eno]] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'a'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
          # ﾒｰﾙｺｰﾄﾞｴﾝｺｰﾄﾞ用展開
          if (EMJ_LITE_FLAG == False) { 
            $this->{'DEC_TYPE'.$i}[$this->AU_NO_TO_MAILCODE[$eno]] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'am'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
            $this->{'DEC_TYPE'.$i}[$this->AU_NO_TO_BIN_MAIL[$eno]] = $this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'am'.$this->DELIMITER[$i]['b'].$eno.$this->DELIMITER[$i]['right'];
          }
        }

      }
    }

    # 変換対応ﾃﾞｰﾀ準備
    foreach ($EMJDATA_BASE as $edt) {
      if ($edt != '') {
        list($enob,$enameb,$d_nob,$v_nob,$a_nob,$junib) = explode("\t",$edt);
        $this->DOCOMO_TO_SOFT[$d_nob] = $v_nob;
        $this->DOCOMO_TO_AU[$d_nob]   = $a_nob;
        $this->SOFT_TO_DOCOMO[$v_nob] = $d_nob;
        $this->SOFT_TO_AU[$v_nob]     = $a_nob;
        $this->AU_TO_DOCOMO[$a_nob]   = $d_nob;
        $this->AU_TO_SOFT[$a_nob]     = $v_nob;
      }
    }

    # 固定絵文字設定(ｱｸｾｽｷｬﾘｱに応じて設定)
    foreach ($EMJDATA_BASE as $edt) {
      if ($edt != '') {
        list($enob,$enameb,$d_nob,$v_nob,$a_nob,$junib) = explode("\t",$edt);
        if (preg_match('/^pc$/i',$this->HARD_DATA['hard'])) {
          # PC表示時
          $check_flag = False;
          if (($this->emojiset == "DoCoMo") and ($d_nob != '')) {
            # DoCoMo絵文字画像に変換(対応絵文字設定がある場合)
            $this->FIX_EMJ[$enob] = $this->DOCOMO_NO_TO_IMG[$d_nob];
            $check_flag = True;
          } elseif (($this->emojiset == "au") and ($a_nob != '')) {
            # au絵文字画像に変換(対応絵文字設定がある場合)
            $this->FIX_EMJ[$enob] = $this->AU_NO_TO_IMG[$a_nob];
            $check_flag = True;
          } elseif (($this->emojiset == "SoftBank") and ($v_nob != '')) {
            # SoftBank絵文字画像に変換(対応絵文字設定がある場合)
            $this->FIX_EMJ[$enob] = $this->SOFT_NO_TO_IMG[$v_nob];
            $check_flag = True;
          }
          # 対応絵文字設定が無い場合
          if ($check_flag == False) { $this->FIX_EMJ[$enob] = $this->emoji_chr; }
        } elseif (preg_match('/^docomo$/i',$this->HARD_DATA['hard'])) {
          # DoCoMo携帯表示時
          if ($d_nob != '') {
            # 対応絵文字設定がある場合
            if ($this->color_flag == 1) {
              # ｶﾗｰ指定有りの場合
              if ($this->docomo_fix_code == 'Unicode') {
                $this->FIX_EMJ[$enob] = $this->DOCOMO_NO_TO_UTXT_COLOR[$d_nob];
              } else {
                $this->FIX_EMJ[$enob] = $this->DOCOMO_NO_TO_TXT_COLOR[$d_nob];
              }
            } else {
              # ｶﾗｰ指定無しの場合
              if ($this->docomo_fix_code == 'Unicode') {
                $this->FIX_EMJ[$enob] = $this->DOCOMO_NO_TO_UTXT[$d_nob];
              } else {
                $this->FIX_EMJ[$enob] = $this->DOCOMO_NO_TO_TXT[$d_nob];
              }
            }
          } else {
            # 対応絵文字設定が無い場合
            $this->FIX_EMJ[$enob] = $this->emoji_chr;
          }
        } elseif (preg_match('/^'.$this->softbank_name.'$/i',$this->HARD_DATA['hard'])) {
          # Vodafone,Softbank携帯表示時
          if ($v_nob != '') {
            # 対応絵文字設定がある場合
            $this->FIX_EMJ[$enob] = $this->SOFT_NO_TO_WEBCODE[$v_nob];
          } else {
            # 対応絵文字設定が無い場合
            $this->FIX_EMJ[$enob] = $this->emoji_chr;
          }
        } elseif (preg_match('/^au$/i',$this->HARD_DATA['hard'])) {
          # au携帯表示時
          if ($a_nob != '') {
            # 対応絵文字設定がある場合
            if ($this->HARD_DATA['tg_flag'] == 'WIN') {
              $this->FIX_EMJ[$enob] = $this->AU_NO_TO_TXT_WIN[$a_nob];
            } else {
              $this->FIX_EMJ[$enob] = $this->AU_NO_TO_TXT[$a_nob];
            }
          } else {
            # 対応絵文字設定が無い場合
            $this->FIX_EMJ[$enob] = $this->emoji_chr;
          }
        }
      }
    }

  }

  # ﾘｸｴｽﾄﾃﾞｰﾀ前処理(ｴｽｹｰﾌﾟｺｰﾄﾞ削除,文字変換,絵文字ｴﾝｺｰﾄﾞ) /////////////////
  # ﾘｸｴｽﾄﾃﾞｰﾀの前処理をします。
  # [引渡し値]
  # 　$mode     : ﾘｸｴｽﾄ処理区分を指定
  # 　　　　　　　'P'or'p':$_POSTのみ
  # 　　　　　　　'G'or'g':$_GETのみ
  # 　　　　　　　'R'or'r':$_REQUESTのみ
  # 　　　　　　　'p','g','r'の組合せにより複数指定可能
  # 　$kana     : 文字列変換指定
  # 　　　　　　　指定なし→変換なし
  # 　　　　　　　全角数字→半角数字 'n'
  # 　　　　　　　全角英字→半角英字 'r'
  # 　　　　　　　全角英数字→半角英数字 'a'
  # 　　　　　　　全角ｶﾀｶﾅ→半角ｶﾀｶﾅ 'kv'
  # 　　　　　　　全角英数字ｶﾀｶﾅ→半角英数字ｶﾀｶﾅ 'kva'
  # 　　　　　　　半角数字→全角数字 'N' 
  # 　　　　　　　半角英字→全角英字 'R'
  # 　　　　　　　半角英数字→全角英数字 'A'
  # 　　　　　　　半角ｶﾀｶﾅ→全角ｶﾀｶﾅ 'KV'
  # 　　　　　　　半角英数字ｶﾀｶﾅ→全角英数字ｶﾀｶﾅ 'KVA'
  # 　$out_code : 出力文字ｺｰﾄﾞ指定(ﾃﾞﾌｫﾙﾄ 'Shift_JIS')
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function reqest_data_conv($mode='r',$kana='',$out_code='SJIS',$input_code='') {
    if ((mb_preferred_mime_name($out_code) == mb_preferred_mime_name('SJIS'))  and ($out_code != $this->chg_code_sjis)) { $out_code = $this->chg_code_sjis; }
    if ((mb_preferred_mime_name($out_code) == mb_preferred_mime_name('EUC'))   and ($out_code != $this->chg_code_euc))  { $out_code = $this->chg_code_euc; }
    if (preg_match('/r/i',$mode)) {
      $_REQUEST = $this->_reqest_data_conv($_REQUEST,$kana,$out_code,$input_code);
    }
    if (preg_match('/g/i',$mode)) {
      $_GET = $this->_reqest_data_conv($_GET,$kana,$out_code,$input_code);
    }
    if (preg_match('/p/i',$mode)) {
      $_POST = $this->_reqest_data_conv($_POST,$kana,$out_code,$input_code);
    }
  }

  # 配列内ﾃﾞｰﾀ一括処理(ｴｽｹｰﾌﾟｺｰﾄﾞ削除,文字変換,絵文字ｴﾝｺｰﾄﾞ) //////////////////
  # 配列(ﾊｯｼｭ)内ﾃﾞｰﾀの一括処理をします。
  # [引渡し値]
  # 　$ARRAY_DATA  : 処理対象の配列(ﾊｯｼｭ)指定
  # 　$kana        : 文字列変換指定
  # 　　　　　　　　指定なし→変換なし
  # 　　　　　　　　全角数字→半角数字 'n'
  # 　　　　　　　　全角英字→半角英字 'r'
  # 　　　　　　　　全角英数字→半角英数字 'a'
  # 　　　　　　　　全角ｶﾀｶﾅ→半角ｶﾀｶﾅ 'kv'
  # 　　　　　　　　全角英数字ｶﾀｶﾅ→半角英数字ｶﾀｶﾅ 'kva'
  # 　　　　　　　　半角数字→全角数字 'N' 
  # 　　　　　　　　半角英字→全角英字 'R'
  # 　　　　　　　　半角英数字→全角英数字 'A'
  # 　　　　　　　　半角ｶﾀｶﾅ→全角ｶﾀｶﾅ 'KV'
  # 　　　　　　　　半角英数字ｶﾀｶﾅ→全角英数字ｶﾀｶﾅ 'KVA'
  # 　$escape_flag : ｴｽｹｰﾌﾟｺｰﾄﾞ削除指定(指定なしor 0:php.ini設定に従う、1:削除する、2:処理しない)
  # 　$out_code    : 出力文字ｺｰﾄﾞ指定(ﾃﾞﾌｫﾙﾄ 'Shift_JIS')
  # 　$input_code  : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　なし
  #////////////////////////////////////////////////////////////////////////////
  function array_data_conv($ARRAY_DATA,$kana='',$escape_flag=0,$out_code='SJIS',$input_code='') {
    if ((mb_preferred_mime_name($out_code) == mb_preferred_mime_name('SJIS')) and ($out_code != $this->chg_code_sjis)) { $out_code = $this->chg_code_sjis; }
    if ((mb_preferred_mime_name($out_code) == mb_preferred_mime_name('EUC'))  and ($out_code != $this->chg_code_euc))  { $out_code = $this->chg_code_euc; }
    $ARRAY_DATA = $this->_reqest_data_conv($ARRAY_DATA,$kana,$out_code,$input_code,$escape_flag);
    return $ARRAY_DATA;
  }

  # 配列内ﾃﾞｰﾀ前処理(内部処理) ////////////////////////////////////////////////
  function _reqest_data_conv($ARRAYDATA,$kana,$out_code,$input_code,$escape_flag=0) {
    $ARRAYOUT = array();
    if ($escape_flag == 0) {
      $quote_flag = ini_get('magic_quotes_gpc');
    } elseif ($escape_flag == 1) {
      $quote_flag = '1';
    } elseif ($escape_flag == 2) {
      $quote_flag = '0';
    }
    $RQT = array();
    $RQT = array_keys($ARRAYDATA);
    foreach ($RQT as $rdt) {
      if (is_array($ARRAYDATA[$rdt])) {
        # 値が配列の場合
        $ARRAYOUT[$rdt] = $this->_reqest_data_conv($ARRAYDATA[$rdt],$kana,$out_code,$input_code);
      } else {
        if ($ARRAYDATA[$rdt] == '') {
          # 値無しの場合
          $ARRAYOUT[$rdt] = $ARRAYDATA[$rdt];
        } else {
          # ｴｽｹｰﾌﾟ処理
          if ($quote_flag == '1') { $ARRAYOUT[$rdt] = stripslashes($ARRAYDATA[$rdt]); }
          # ﾃﾞｰﾀｴﾝｺｰﾃﾞｨﾝｸﾞ取得
          if ($input_code == '') {
            if (($this->HARD_DATA['hard'] == $this->softbank_name) and ($this->HARD_DATA['tg_flag'] == '3G')) {
              $this_code = mb_detect_encoding($ARRAYDATA[$rdt],$this->ENCODINGLIST['UTF-8']);
            } else {
              $this_code = mb_detect_encoding($ARRAYDATA[$rdt],$this->ENCODINGLIST[$this->chr_code]);
            }
          }
          # 絵文字ｴﾝｺｰﾄﾞ
          if ($input_code == '') {
            $ARRAYOUT[$rdt] = $this->emj_encode($ARRAYDATA[$rdt],$out_code,'',$this_code);
          } else {
            $ARRAYOUT[$rdt] = $this->emj_encode($ARRAYDATA[$rdt],$out_code,'',$input_code);
          }
          # 文字変換
          if ($kana != '') { $ARRAYOUT[$rdt] = mb_convert_kana($ARRAYDATA[$rdt],$kana,$out_code); }
        }
      }
    }
    return $ARRAYOUT;
  }

  # 絵文字変換(Web用) /////////////////////////////////////////////////////////
  # 絵文字ｺｰﾄﾞをｱｸｾｽｷｬﾘｱに応じてWeb表示用に絵文字変換して出力します。
  # [引渡し値]
  # 　$textstr  : 変換対象文字列
  # 　$out_code : 変換後出力ｺｰﾄﾞ指定
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr  : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function replace_emoji($textstr,$out_code='',$input_code='') {
    if (isset($textstr)) {
      # 絵文字ｴﾝｺｰﾄﾞ
      $textstr = $this->emj_encode($textstr,'',1,$input_code);
      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
      }
      # 絵文字ﾃﾞｺｰﾄﾞ
      $TEXTSTR = $this->emj_decode($textstr,'',$out_code,'');
      $textstr = $TEXTSTR['web'];
    }
    return $textstr;
  }

  # 絵文字変換(Web用ｷｬﾘｱ指定) /////////////////////////////////////////////////
  # 絵文字ｺｰﾄﾞを指定ｷｬﾘｱに応じてWeb表示用に絵文字変換して出力します。
  # [引渡し値]
  # 　$textstr  : 変換対象文字列
  # 　$career   : 変換対象ｷｬﾘｱ指定(指定無い場合ｱｸｾｽｷｬﾘｱ,'DoCoMo','au','SoftBank'or'Vodafone')
  # 　$out_code : 変換後出力ｺｰﾄﾞ指定
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr  : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function replace_emoji_career($textstr,$career='DoCoMo',$out_code='',$input_code='') {
    if (isset($textstr)) {
      # 絵文字ｴﾝｺｰﾄﾞ
      $textstr = $this->emj_encode($textstr,'',1,$input_code);
      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
      }
      # 絵文字ﾃﾞｺｰﾄﾞ
      $TEXTSTR = $this->emj_decode($textstr,$career,$out_code,'');
      $textstr = $TEXTSTR['web'];
    }
    return $textstr;
  }

  # 絵文字変換(ﾒｰﾙ送信用) /////////////////////////////////////////////////////
  # 絵文字ｺｰﾄﾞを指定ｷｬﾘｱに応じてﾒｰﾙ送信用に絵文字変換して出力します。
  # [引渡し値]
  # 　$textstr     : 変換対象文字列
  # 　$career      : 変換対象ｷｬﾘｱ指定(指定無い場合ｱｸｾｽｷｬﾘｱ,'DoCoMo','au','SoftBank'or'Vodafone')
  # 　$out_code    : 変換後出力ｺｰﾄﾞ指定
  # 　$input_code  : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # 　$career_gene : 携帯世代('3G':第3世代(ﾃﾞﾌｫﾙﾄ)、'2G':第2世代-SoftBank携帯のみ)
  # [返り値]
  # 　$textstr  : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function replace_emoji_mail($textstr,$career='PC',$input_code='',$career_gene='3G') {
    if (isset($textstr)) {
      # 絵文字ｴﾝｺｰﾄﾞ
      $textstr = $this->emj_encode($textstr,'',1,$input_code);
      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
      }
      # 絵文字ﾃﾞｺｰﾄﾞ
      $TEXTSTR = $this->emj_decode($textstr,$career,'JIS','');
      if ((($career == 'Vodafone') or ($career == 'SoftBank')) and ($career_gene == '2G')) {
        $textstr = $TEXTSTR['mail_plain'];
      } else {
        $textstr = $TEXTSTR['mail'];
      }
    }
    return $textstr;
  }

  # 絵文字変換(ﾌｫｰﾑ表示用) ////////////////////////////////////////////////////
  # 絵文字ｺｰﾄﾞをｱｸｾｽｷｬﾘｱに応じてﾌｫｰﾑ表示用に絵文字変換して出力します。
  # [引渡し値]
  # 　$textstr  : 変換対象文字列
  # 　$career   : 変換対象ｷｬﾘｱ指定(指定無い場合ｱｸｾｽｷｬﾘｱ,'DoCoMo','au','SoftBank'or'Vodafone')
  # 　$out_code : 変換後出力ｺｰﾄﾞ指定
  # 　$input_code : 入力文字ｺｰﾄﾞ指定(指定なし:設定による、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr  : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function replace_emoji_form($textstr,$career='',$out_code='',$input_code='') {
    if (isset($textstr)) {
      # 絵文字ｴﾝｺｰﾄﾞ
      $textstr = $this->emj_encode($textstr,'',1,$input_code);
      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
      }
      # 絵文字ﾃﾞｺｰﾄﾞ
      $TEXTSTR = $this->emj_decode($textstr,$career,$out_code,'');
      $textstr = $TEXTSTR['form'];
    }
    return $textstr;
  }

  # 絵文字ｺｰﾄﾞｴﾝｺｰﾄﾞ //////////////////////////////////////////////////////////
  # 文字列中の絵文字をｴﾝｺｰﾄﾞします。
  # [引渡し値]
  # 　$textstr     : 変換対象文字列
  # 　$out_code    : 変換後出力ｺｰﾄﾞ指定
  # 　$encode_pass : 文字ｺｰﾄﾞ変換無効化('1')
  # 　$input_code  : 入力文字ｺｰﾄﾞ指定(指定なし:全ｺｰﾄﾞﾁｪｯｸ、UTF-8ｺｰﾄﾞ:UTF-8、その他ｺｰﾄﾞ:SJIS)
  # [返り値]
  # 　$textstr     : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function emj_encode($textstr,$out_code='',$encode_pass='',$input_code='') {
    if (isset($textstr)) {
      # 入力文字ｺｰﾄﾞ設定
      if ($input_code == '') {
#        if ($this->chr_code == 'UTF-8') { $input_code = 'UTF-8'; }
        $input_code = $this->chr_code;
      }
#      if ($input_code != 'UTF-8') { $input_code = 'SJIS'; }

      # SoftBank絵文字UTF-8ｴﾝｺｰﾄﾞ
      if (($input_code == '') or (mb_preferred_mime_name($input_code) == mb_preferred_mime_name('UTF-8'))) {
        $textstr = $this->_replace_v_emoji_utf8($textstr);
      }
      # SoftBank絵文字ｴﾝｺｰﾄﾞ
      if (($input_code == '') or (mb_preferred_mime_name($input_code) == mb_preferred_mime_name('Shift_JIS'))) {
        $textstr = $this->_replace_v_emoji($textstr);
      }

      # DoCoMo絵文字UTF-8ｴﾝｺｰﾄﾞ
      if (($input_code == '') or (mb_preferred_mime_name($input_code) == mb_preferred_mime_name('UTF-8'))) {
        $textstr = $this->_replace_d_emoji_utf8($textstr);
      }
      # DoCoMo絵文字ｴﾝｺｰﾄﾞ
      if (($input_code == '') or (mb_preferred_mime_name($input_code) == mb_preferred_mime_name('Shift_JIS'))) {
        $textstr = $this->_replace_d_emoji($textstr);
      }
      # DoCoMo絵文字ﾃｷｽﾄｴﾝｺｰﾄﾞ
      $textstr = $this->_replace_d_emoji_text($textstr);

      # au絵文字UTF-8ｴﾝｺｰﾄﾞ
      if (($input_code == '') or (mb_preferred_mime_name($input_code) == mb_preferred_mime_name('UTF-8'))) {
        $textstr = $this->_replace_a_emoji_utf8($textstr);
      }
      # au絵文字ｴﾝｺｰﾄﾞ
      if (($input_code == '') or (mb_preferred_mime_name($input_code) == mb_preferred_mime_name('Shift_JIS'))) {
        $textstr = $this->_replace_a_emoji($textstr);
      }

      # ﾃｷｽﾄｺｰﾄﾞ変換
      if ($encode_pass == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
        if ($de) {
          $text_code = mb_preferred_mime_name($de);
          # 出力ｺｰﾄﾞ設定
          if ($out_code == '') { $oc = $this->chr_code; } else { $oc = $out_code; }
          if ($text_code != mb_preferred_mime_name($oc)) {
            # 文字列ｺｰﾄﾞが指定出力ｺｰﾄﾞと異なる場合
            if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($this->chg_code_sjis)) {
              # SJIS指定の場合
              $textstr = @mb_convert_encoding($textstr,$oc,$this->chg_code_sjis);
            } else {
              # SJIS以外の場合
              $textstr = @mb_convert_encoding($textstr,$oc,$text_code);
            }
          }
        }
      }
    } else {
      $textstr = '';
    }
    return $textstr;
  }

  # 絵文字ｺｰﾄﾞﾃﾞｺｰﾄﾞ //////////////////////////////////////////////////////////
  # 文字列中の絵文字をﾃﾞｺｰﾄﾞします。
  # [引渡し値]
  # 　$textstr  : 変換対象文字列
  # 　$career   : 変換対象ｷｬﾘｱ指定(指定無い場合ｱｸｾｽｷｬﾘｱ,'DoCoMo','au','SoftBank'or'Vodafone')
  # 　$out_code : 変換後出力ｺｰﾄﾞ指定
  # 　$img_mode : 画像変換強制指定(1:強制画像変換)
  # [返り値]
  # 　$DECODE_DATA['web']        : 変換後文字列(Web用)
  # 　$DECODE_DATA['form']       : 変換後文字列(Form用)
  # 　$DECODE_DATA['mail']       : 変換後文字列(Mail用)
  # 　$DECODE_DATA['mail_plain'] : 変換後文字列(Mail用-Softbankﾃｷｽﾄ用)
  # 　$DECODE_DATA['text']       : 変換後文字列(ﾃｷｽﾄｺｰﾄﾞ)
  # 　$DECODE_DATA['bin']        : 変換後文字列(ﾊﾞｲﾅﾘｺｰﾄﾞ)
  #////////////////////////////////////////////////////////////////////////////
  function emj_decode($textstr,$career='',$out_code='',$img_mode='') {
    if ($out_code == '') { $oc = $this->chr_code; } else { $oc = $out_code; }
    $DECODE_DATA = array();
    $DECODE_DATA['web']        = $textstr;
    $DECODE_DATA['form']       = $textstr;
    $DECODE_DATA['mail']       = $textstr;
    $DECODE_DATA['mail_plain'] = $textstr;
    $DECODE_DATA['text']       = $textstr;
    $DECODE_DATA['bin']        = $textstr;
    if (isset($textstr)) {
      # 変換先ｷｬﾘｱ設定
      if ($career == '') {
        # 変換先ｷｬﾘｱ指定無し(ｱｸｾｽｷｬﾘｱ変換)
        $set_career = $this->HARD_DATA['hard'];
      } else {
        # 変換先ｷｬﾘｱ指定有り(指定ｷｬﾘｱ変換)
        $set_career = $career;
      }

      # 絵文字ｴﾝｺｰﾄﾞ変換
      $textstr_img = $textstr;
      if (($this->img_onry_flag != '1') and ($img_mode != '1')) {
        # PC又は強制画像変換指定以外について絵文字対応変換
        $textstr = $this->_emj_enc_change($textstr,$set_career);
      }

      # ﾃｷｽﾄｺｰﾄﾞ変換
      $text_code = mb_detect_encoding($textstr,$this->ENCODINGLIST[$oc]);
      if ($text_code != '') {
        if ($out_code == '') {
          # 出力ｺｰﾄﾞ指定なしの場合(ﾃﾞﾌｫﾙﾄ設定ｺｰﾄﾞ出力)
          if (mb_preferred_mime_name($this->chr_code) != mb_preferred_mime_name($text_code)) {
            $textstr = @mb_convert_encoding($textstr,$this->chr_code,$text_code);
          }
        } else {
          # 出力ｺｰﾄﾞ指定有りの場合
          if (mb_preferred_mime_name($out_code) != mb_preferred_mime_name($text_code)) {
            $textstr = @mb_convert_encoding($textstr,$out_code,$text_code);
          }
        }
      }
      # ﾃｷｽﾄ準備
      $DECODE_DATA['web']        = $textstr;
      $DECODE_DATA['form']       = $textstr;
      $DECODE_DATA['mail']       = $textstr;
      $DECODE_DATA['mail_plain'] = $textstr;
      $DECODE_DATA['text']       = $textstr;
      $DECODE_DATA['bin']        = $textstr;
      # ﾌｫｰﾑ表示時HTMLｴﾝﾃｨﾃｨ実行
      $DECODE_DATA['form'] = $this->form_htmlentities($DECODE_DATA['form']);
      # ﾙｰﾌﾟ用ﾃｷｽﾄ設定
      $loop_string = $textstr;
      if (preg_match('/^pc$/i',$set_career)) {
        # PC変換時
        while (preg_match('/(\{|\{#|###|<!\-\-)(emj_d_|d|emj_a_|a|emj_am_|am|emj_v_|v)(\d{4})(\}|#\}|###|\-\->)/',$loop_string,$PM)) {
          # Web表示用ﾃﾞｺｰﾄﾞ(画像変換)
          # ﾒｰﾙ用ﾃﾞｺｰﾄﾞ(本関数での処理なし)
          if (($PM[2] == 'emj_d_') or ($PM[2] == 'd')) {
            # DoCoMoｴﾝｺｰﾄﾞ
            $set_data = $this->DOCOMO_NO_TO_IMG[$PM[3]];
          } elseif (($PM[2] == 'emj_a_') or ($PM[2] == 'a') or ($PM[2] == 'emj_am_') or ($PM[2] == 'am')) {
            # auｴﾝｺｰﾄﾞ
            $set_data = $this->AU_NO_TO_IMG[$PM[3]];
          } elseif (($PM[2] == 'emj_v_') or ($PM[2] == 'v')) {
            # SoftBankｴﾝｺｰﾄﾞ
            $set_data = $this->SOFT_NO_TO_IMG[$PM[3]];
          }
          $DECODE_DATA['web']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_data,$DECODE_DATA['web']);
          $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_data,$DECODE_DATA['web']);
          # ﾌｫｰﾑ表示用ﾃﾞｺｰﾄﾞ(絵文字ｴﾝｺｰﾄﾞのまま出力)
          $DECODE_DATA['form'] = $this->form_htmlentities($textstr);
          # ﾃｷｽﾄﾃﾞｺｰﾄﾞ
          if (($PM[2] == 'emj_d_') or ($PM[2] == 'd')) {
            # DoCoMoｴﾝｺｰﾄﾞ
            $set_data = $this->DOCOMO_NO_TO_UTXT[$PM[3]];
          } elseif (($PM[2] == 'emj_a_') or ($PM[2] == 'a') or ($PM[2] == 'emj_am_') or ($PM[2] == 'am')) {
            # auｴﾝｺｰﾄﾞ
            $set_data = $this->AU_NO_TO_TXT_WIN[$PM[3]];
          } elseif (($PM[2] == 'emj_v_') or ($PM[2] == 'v')) {
            # SoftBankｴﾝｺｰﾄﾞ
            $set_data = $this->SOFT_NO_TO_WEBCODE[$PM[3]];
          }
          $DECODE_DATA['text'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_data,$DECODE_DATA['text']);
          # ﾊﾞｲﾅﾘﾃﾞｺｰﾄﾞ
          if (($PM[2] == 'emj_d_') or ($PM[2] == 'd')) {
            # DoCoMoｴﾝｺｰﾄﾞ
            $set_data = $this->DOCOMO_NO_TO_BIN[$PM[3]];
          } elseif (($PM[2] == 'emj_a_') or ($PM[2] == 'a') or ($PM[2] == 'emj_am_') or ($PM[2] == 'am')) {
            # auｴﾝｺｰﾄﾞ
            $set_data = $this->AU_NO_TO_BIN[$PM[3]];
          } elseif (($PM[2] == 'emj_v_') or ($PM[2] == 'v')) {
            # SoftBankｴﾝｺｰﾄﾞ
            $set_data = $this->SOFT_NO_TO_WEBCODE[$PM[3]];
          }
          $DECODE_DATA['bin'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_data,$DECODE_DATA['bin']);
          # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
          $loop_string = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_string);
        }
      } elseif (preg_match('/^docomo$/i',$set_career)) {
        # DoCoMoｷｬﾘｱに対しての絵文字ﾃﾞｺｰﾄﾞ
        while (preg_match('/(\{|\{#|###|<!\-\-)(emj_d_|d)(\d{4})(\}|#\}|###|\-\->)/',$loop_string,$PM)) {
          # Web表示用
          if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
            # 強制画像変換指定
            $DECODE_DATA['web']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_IMG[$PM[3]],$DECODE_DATA['web']);
          } else {
            # 絵文字ｺｰﾄﾞ変換
            $DECODE_DATA['web']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_UTXT_COLOR[$PM[3]],$DECODE_DATA['web']);
          }
          # ﾌｫｰﾑ表示用ﾃﾞｺｰﾄﾞ
          $DECODE_DATA['form'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_UTXT[$PM[3]],$DECODE_DATA['form']);
          # ﾒｰﾙ用ﾃﾞｺｰﾄﾞ
          if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
            $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_IMG_MAIL[$PM[3]],$DECODE_DATA['mail']);
          } else {
            # 絵文字ｺｰﾄﾞ変換
            $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_BIN[$PM[3]],$DECODE_DATA['mail']);
          }
          $DECODE_DATA['mail'] = preg_replace('|\stitle=\".+?\"|i','',$DECODE_DATA['mail']);
          $DECODE_DATA['mail'] = preg_replace('|\salt=\".+?\"|i'  ,'',$DECODE_DATA['mail']);
          # ﾃｷｽﾄﾃﾞｺｰﾄﾞ
          $DECODE_DATA['text'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_UTXT[$PM[3]],$DECODE_DATA['text']);
          # ﾊﾞｲﾅﾘﾃﾞｺｰﾄﾞ
          if ($oc == 'UTF-8') {
            $DECODE_DATA['bin']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_BIN_UTF8[$PM[3]],$DECODE_DATA['bin']);
          } else {
            $DECODE_DATA['bin']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->DOCOMO_NO_TO_BIN[$PM[3]],$DECODE_DATA['bin']);
          }
          # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
          $loop_string = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_string);
        }
        if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
          # 強制画像変換指定
          while (preg_match('/(\{|\{#|###|<!\-\-)(emj_a_|a|emj_am_|am|emj_v_|v)(\d{4})(\}|#\}|###|\-\->)/',$loop_string,$PM)) {
            if (($PM[2] == 'emj_a_') or ($PM[2] == 'a') or ($PM[2] == 'emj_am_') or ($PM[2] == 'am')) {
              $set_text      = $this->AU_NO_TO_IMG[$PM[3]];
              $set_text_mail = $this->AU_NO_TO_IMG_MAIL[$PM[3]];
            } elseif (($PM[2] == 'emj_v_') or ($PM[2] == 'v')) {
              $set_text      = $this->SOFT_NO_TO_IMG[$PM[3]];
              $set_text_mail = $this->SOFT_NO_TO_IMG_MAIL[$PM[3]];
            }
            $DECODE_DATA['web']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_text     ,$DECODE_DATA['web']);
            $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_text_mail,$DECODE_DATA['mail']);
            # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
            $loop_string = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_string);
          }
        } else {
          # 未対応文字が存在する場合
          while (preg_match('/\?(emj_a_|a|emj_am_|am|emj_v_|v)(\d{4})\?/',$loop_string,$PM)) {
            if ($this->emoji_non == 0) {
              # 文字列で潰して表示
              $set_text = $this->emoji_chr;
            } elseif ($this->emoji_non == 1) {
              # 説明文で表示
              if (($PM[1] == 'emj_a_') or ($PM[1] == 'a') or ($PM[1] == 'emj_am_') or ($PM[1] == 'am')) {
                $set_text = $this->AU_NO_TO_NAME[$PM[2]];
              } elseif (($PM[1] == 'emj_v_') or ($PM[1] == 'v')) {
                $set_text = $this->SOFT_NO_TO_NAME[$PM[2]];
              }
            } elseif ($this->emoji_non == 2) {
              # 画像で表示
              if (($PM[1] == 'emj_a_') or ($PM[1] == 'a') or ($PM[1] == 'emj_am_') or ($PM[1] == 'am')) {
                $set_text = $this->AU_NO_TO_IMG[$PM[2]];
              } elseif (($PM[1] == 'emj_v_') or ($PM[1] == 'v')) {
                $set_text = $this->SOFT_NO_TO_IMG[$PM[2]];
              }
            }
            # Web表示用
            $DECODE_DATA['web']  = preg_replace('|\?'.$PM[1].$PM[2].'\?|',$set_text,$DECODE_DATA['web']);
            # ﾌｫｰﾑ表示用ﾃﾞｺｰﾄﾞ
            $DECODE_DATA['form'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['form']);
            # ﾒｰﾙ用ﾃﾞｺｰﾄﾞ
            $DECODE_DATA['mail'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|',$this->emoji_chr,$DECODE_DATA['mail']);
            # ﾃｷｽﾄﾃﾞｺｰﾄﾞ
            $DECODE_DATA['text'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['text']);
            # ﾊﾞｲﾅﾘﾃﾞｺｰﾄﾞ
            $DECODE_DATA['bin']  = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['bin']);
            # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
            $loop_string = preg_replace('|\?'.$PM[1].$PM[2].'\?|','',$loop_string);
          }
        }
      } elseif (preg_match('/^au$/i',$set_career)) {
        # auｷｬﾘｱに対しての絵文字ﾃﾞｺｰﾄﾞ
        while (preg_match('/(\{|\{#|###|<!\-\-)(emj_a_|a|emj_am_|am)(\d{4})(\}|#\}|###|\-\->)/',$loop_string,$PM)) {
          # Web表示用
          if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
            # 強制画像変換指定
            $DECODE_DATA['web']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_IMG[$PM[3]],$DECODE_DATA['web']);
          } else {
            # 絵文字ｺｰﾄﾞ変換
            if ($career == '') {
              # ｱｸｾｽｷｬﾘｱ変換の場合
              if ($this->HARD_DATA['tg_flag'] == 'WIN') {
                $set_data = $this->AU_NO_TO_TXT_WIN[$PM[3]];
              } else {
                $set_data = $this->AU_NO_TO_TXT[$PM[3]];
              }
            } else {
              # 変換ｷｬﾘｱ指定の場合(WIN用に変換)
              $set_data = $this->AU_NO_TO_TXT_WIN[$PM[3]];
            }
            $DECODE_DATA['web']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_data,$DECODE_DATA['web']);
          }
          # ﾌｫｰﾑ表示用ﾃﾞｺｰﾄﾞ
          if ($oc == 'UTF-8') {
            $DECODE_DATA['form'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_BIN_UTF8[$PM[3]],$DECODE_DATA['form']);
          } else {
            $DECODE_DATA['form'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_BIN[$PM[3]],$DECODE_DATA['form']);
          }
          $DECODE_DATA['form'] = $this->form_htmlentities($DECODE_DATA['form']);
          # ﾒｰﾙ用ﾃﾞｺｰﾄﾞ
          if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
            # 強制画像変換指定
            $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_IMG_MAIL[$PM[3]],$DECODE_DATA['mail']);
          } else {
            # 絵文字ｺｰﾄﾞ変換
			#　ここ急におかしくなった。。
            $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_BIN[$PM[3]],$DECODE_DATA['mail']);
            #$DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_BIN_MAIL[$PM[3]],$DECODE_DATA['mail']);
          }
          $DECODE_DATA['mail'] = preg_replace('|\stitle=\".+?\"|i','',$DECODE_DATA['mail']);
          $DECODE_DATA['mail'] = preg_replace('|\salt=\".+?\"|i'  ,'',$DECODE_DATA['mail']);
          # ﾃｷｽﾄﾃﾞｺｰﾄﾞ
          if ($this->HARD_DATA['tg_flag'] == 'WIN') {
            $DECODE_DATA['text'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_TXT_WIN[$PM[3]],$DECODE_DATA['text']);
          } else {
            $DECODE_DATA['text'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_TXT[$PM[3]],$DECODE_DATA['text']);
          }
          # ﾊﾞｲﾅﾘﾃﾞｺｰﾄﾞ
          if ($oc == 'UTF-8') {
            $DECODE_DATA['bin']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_BIN_UTF8[$PM[3]],$DECODE_DATA['bin']);
          } else {
            $DECODE_DATA['bin']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->AU_NO_TO_BIN[$PM[3]],$DECODE_DATA['bin']);
          }
          # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
          $loop_string = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_string);
        }
        if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
          # 強制画像変換指定
          while (preg_match('/(\{|\{#|###|<!\-\-)(emj_d_|d|emj_v_|v)(\d{4})(\}|#\}|###|\-\->)/',$loop_string,$PM)) {
            if (($PM[2] == 'emj_d_') or ($PM[2] == 'd')) {
              $set_text      = $this->DOCOMO_NO_TO_IMG[$PM[3]];
              $set_text_mail = $this->DOCOMO_NO_TO_IMG_MAIL[$PM[3]];
            } elseif (($PM[2] == 'emj_v_') or ($PM[2] == 'v')) {
              $set_text      = $this->SOFT_NO_TO_IMG[$PM[3]];
              $set_text_mail = $this->SOFT_NO_TO_IMG_MAIL[$PM[3]];
            }
            $DECODE_DATA['web']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_text     ,$DECODE_DATA['web']);
            $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_text_mail,$DECODE_DATA['mail']);
            # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
            $loop_string = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_string);
          }
        } else {
          # 未対応文字が存在する場合
          while (preg_match('/\?(emj_d_|d|emj_v_|v)(\d{4})\?/',$loop_string,$PM)) {
            if ($this->emoji_non == 0) {
              # 文字列で潰して表示
              $set_text = $this->emoji_chr;
            } elseif ($this->emoji_non == 1) {
              # 説明文で表示
              if (($PM[1] == 'emj_d_') or ($PM[1] == 'd')) {
                $set_text = $this->DOCOMO_NO_TO_NAME[$PM[2]];
              } elseif (($PM[1] == 'emj_v_') or ($PM[1] == 'v')) {
                $set_text = $this->SOFT_NO_TO_NAME[$PM[2]];
              }
            } elseif ($this->emoji_non == 2) {
              # 画像で表示
              if (($PM[1] == 'emj_d_') or ($PM[1] == 'd')) {
                $set_text = $this->DOCOMO_NO_TO_IMG[$PM[2]];
              } elseif (($PM[1] == 'emj_v_') or ($PM[1] == 'v')) {
                $set_text = $this->SOFT_NO_TO_IMG[$PM[2]];
              }
            }
            # Web表示用
            $DECODE_DATA['web']  = preg_replace('|\?'.$PM[1].$PM[2].'\?|',$set_text,$DECODE_DATA['web']);
            # ﾌｫｰﾑ表示用ﾃﾞｺｰﾄﾞ
            $DECODE_DATA['form'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['form']);
            # ﾒｰﾙ用ﾃﾞｺｰﾄﾞ
            $DECODE_DATA['mail'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|',$this->emoji_chr,$DECODE_DATA['mail']);
            # ﾃｷｽﾄﾃﾞｺｰﾄﾞ
            $DECODE_DATA['text'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['text']);
            # ﾊﾞｲﾅﾘﾃﾞｺｰﾄﾞ
            $DECODE_DATA['bin']  = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['bin']);
            # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
            $loop_string = preg_replace('|\?'.$PM[1].$PM[2].'\?|','',$loop_string);
          }
        }
      } elseif (preg_match('/^'.$this->softbank_name.'$/i',$set_career)) {
        # SoftBankｷｬﾘｱに対しての絵文字ﾃﾞｺｰﾄﾞ
        while (preg_match('/(\{|\{#|###|<!\-\-)(emj_v_|v)(\d{4})(\}|#\}|###|\-\->)/',$loop_string,$PM)) {
          # Web表示用
          if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
            # 強制画像変換指定
            $DECODE_DATA['web'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT_NO_TO_IMG[$PM[3]],$DECODE_DATA['web']);
          } else {
            # 絵文字ｺｰﾄﾞ変換
            $DECODE_DATA['web'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT_NO_TO_WEBCODE[$PM[3]],$DECODE_DATA['web']);
          }
          # ﾌｫｰﾑ表示用ﾃﾞｺｰﾄﾞ
          $DECODE_DATA['form'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT_NO_TO_WEBCODE[$PM[3]],$DECODE_DATA['form']);
          # ﾒｰﾙ用ﾃﾞｺｰﾄﾞ
          if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
            # 強制画像変換指定
            $DECODE_DATA['mail'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT_NO_TO_IMG_MAIL[$PM[3]],$DECODE_DATA['mail']);
          } else {
            # 絵文字ｺｰﾄﾞ変換
            $DECODE_DATA['mail']       = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT3G_NO_TO_UTF8[$PM[3]],$DECODE_DATA['mail']);
            $DECODE_DATA['mail_plain'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT_NO_TO_WEBCODE[$PM[3]],$DECODE_DATA['mail']);
          }
          $DECODE_DATA['mail'] = preg_replace('|\stitle=\".+?\"|i','',$DECODE_DATA['mail']);
          $DECODE_DATA['mail'] = preg_replace('|\salt=\".+?\"|i'  ,'',$DECODE_DATA['mail']);
          # ﾃｷｽﾄﾃﾞｺｰﾄﾞ
          $DECODE_DATA['text'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT_NO_TO_WEBCODE[$PM[3]],$DECODE_DATA['text']);
          # ﾊﾞｲﾅﾘﾃﾞｺｰﾄﾞ
          $DECODE_DATA['bin']  = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$this->SOFT_NO_TO_WEBCODE[$PM[3]],$DECODE_DATA['bin']);
          # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
          $loop_string = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_string);
        }
        if (($this->img_onry_flag == '1') or ($img_mode == '1')) {
          # 強制画像変換指定
          while (preg_match('/(\{|\{#|###|<!\-\-)(emj_d_|d|emj_a_|a|emj_am_|am)(\d{4})(\}|#\}|###|\-\->)/',$loop_string,$PM)) {
            if (($PM[2] == 'emj_d_') or ($PM[2] == 'd')) {
              $set_text      = $this->DOCOMO_NO_TO_IMG[$PM[3]];
              $set_text_mail = $this->DOCOMO_NO_TO_IMG_MAIL[$PM[3]];
            } elseif (($PM[2] == 'emj_a_') or ($PM[2] == 'a') or ($PM[2] == 'emj_am_') or ($PM[2] == 'am')) {
              $set_text      = $this->AU_NO_TO_IMG[$PM[3]];
              $set_text_mail = $this->AU_NO_TO_IMG_MAIL[$PM[3]];
            }
            $DECODE_DATA['web']        = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_text     ,$DECODE_DATA['web']);
            $DECODE_DATA['mail']       = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_text_mail,$DECODE_DATA['mail']);
            $DECODE_DATA['mail_plain'] = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$set_text_mail,$DECODE_DATA['mail_plain']);
            # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
            $loop_string = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_string);
          }
        } else {
          # 未対応文字が存在する場合
          while (preg_match('/\?(emj_d_|d|emj_a_|a|emj_am_|am)(\d{4})\?/',$loop_string,$PM)) {
            if ($this->emoji_non == 0) {
              # 文字列で潰して表示
              $set_text = $this->emoji_chr;
            } elseif ($this->emoji_non == 1) {
              # 説明文で表示
              if (($PM[1] == 'emj_d_') or ($PM[1] == 'd')) {
                $set_text = $this->DOCOMO_NO_TO_NAME[$PM[2]];
              } elseif (($PM[1] == 'emj_a_') or ($PM[1] == 'a') or ($PM[1] == 'emj_am_') or ($PM[1] == 'am')) {
                $set_text = $this->AU_NO_TO_NAME[$PM[2]];
              }
            } elseif ($this->emoji_non == 2) {
              # 画像で表示
              if (($PM[1] == 'emj_d_') or ($PM[1] == 'd')) {
                $set_text = $this->DOCOMO_NO_TO_IMG[$PM[2]];
              } elseif (($PM[1] == 'emj_a_') or ($PM[1] == 'a') or ($PM[1] == 'emj_am_') or ($PM[1] == 'am')) {
                $set_text = $this->AU_NO_TO_IMG[$PM[2]];
              }
            }
            # Web表示用
            $DECODE_DATA['web']  = preg_replace('|\?'.$PM[1].$PM[2].'\?|',$set_text,$DECODE_DATA['web']);
            # ﾌｫｰﾑ表示用ﾃﾞｺｰﾄﾞ
            $DECODE_DATA['form'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['form']);
            # ﾒｰﾙ用ﾃﾞｺｰﾄﾞ
            $DECODE_DATA['mail']       = preg_replace('|\?'.$PM[1].$PM[2].'\?|',$this->emoji_chr,$DECODE_DATA['mail']);
            $DECODE_DATA['mail_plain'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|',$this->emoji_chr,$DECODE_DATA['mail_plain']);
            # ﾃｷｽﾄﾃﾞｺｰﾄﾞ
            $DECODE_DATA['text'] = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['text']);
            # ﾊﾞｲﾅﾘﾃﾞｺｰﾄﾞ
            $DECODE_DATA['bin']  = preg_replace('|\?'.$PM[1].$PM[2].'\?|','{'.$PM[1].$PM[2].'}',$DECODE_DATA['bin']);
            # ﾙｰﾌﾟ用ﾃｷｽﾄ処理
            $loop_string = preg_replace('|\?'.$PM[1].$PM[2].'\?|','',$loop_string);
          }
        }
      }
    } else {
      $DECODE_DATA['web']        = '';
      $DECODE_DATA['form']       = '';
      $DECODE_DATA['mail']       = '';
      $DECODE_DATA['mail_plain'] = '';
      $DECODE_DATA['text']       = '';
      $DECODE_DATA['bin']        = '';
    }
    return $DECODE_DATA;
  }

  # 絵文字ｴﾝｺｰﾄﾞ絵文字変換 ////////////////////////////////////////////////////
  # 絵文字ｴﾝｺｰﾄﾞされた文字列をｱｸｾｽｷｬﾘｱ、或いは指定のｷｬﾘｱの絵文字に変換します。
  # [引渡し値]
  # 　$textstr  : 変換対象文字列
  # 　$career   : ｷｬﾘｱ指定(指定無い場合ｱｸｾｽｷｬﾘｱ,'DoCoMo','au','SoftBank'or'Vodafone')
  # [返り値]
  # 　$textstr  : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function _emj_enc_change($textstr,$career='') {
    if (isset($textstr)) {
      # 変換先ｷｬﾘｱ設定
      if ($career == '') {
        # 変換先ｷｬﾘｱ指定無し(ｱｸｾｽｷｬﾘｱ変換)
        $career = $this->HARD_DATA['hard'];
      } else {
        # 変換先ｷｬﾘｱ指定有り(指定ｷｬﾘｱ変換)
      }
      if (!preg_match('/^pc$/i',$career)) {
        # PC以外変換
        $loop_text = $textstr;
        # ｴﾝｺｰﾄﾞﾀｲﾌﾟ(ﾃﾞﾘﾐﾀ)指定
        $left_delimiter  = $this->DELIMITER[$this->enc_type]['left'];
        $right_delimiter = $this->DELIMITER[$this->enc_type]['right'];
        $etype_top       = $this->DELIMITER[$this->enc_type]['a'];
        $etype_sec       = $this->DELIMITER[$this->enc_type]['b'];

        while (preg_match('/([\{|\{#|###|<!\-\-])(emj_d_|d|emj_a_|a|emj_am_|am|emj_v_|v)(\d{4})([\}|#\}|###|\-\->])/',$loop_text,$PM)) {
          $check_flag = False;
          if (($PM[2] == 'emj_d_') or ($PM[2] == 'd')) {
            # DoCoMo絵文字変換
            if (preg_match('/^docomo$/i',$career)) {
              # DoCoMo変換
              $check_flag = True;
            } elseif (preg_match('/^au$/i',$career)) {
              # au変換
              if (isset($this->DOCOMO_TO_AU[$PM[3]])) {
                if (preg_match('/^[0-9]{4}$/',$this->DOCOMO_TO_AU[$PM[3]])) {
                  $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$PM[1].$etype_top.'a'.$etype_sec.$this->DOCOMO_TO_AU[$PM[3]].$PM[4],$textstr);
                  $check_flag = True;
                }
              }
            } elseif (preg_match('/'.$this->softbank_name.'/i',$career)) {
              # SoftBank変換
              if (isset($this->DOCOMO_TO_SOFT[$PM[3]])) {
                if (preg_match('/^[0-9]{4}$/',$this->DOCOMO_TO_SOFT[$PM[3]])) {
                  $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$PM[1].$etype_top.'v'.$etype_sec.$this->DOCOMO_TO_SOFT[$PM[3]].$PM[4],$textstr);
                  $check_flag = True;
                }
              }
            }
            if ($check_flag == False) {
              # 対応絵文字が無い場合
              $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','?'.$PM[2].$PM[3].'?',$textstr);
            }
          } elseif (($PM[2] == 'emj_a_') or ($PM[2] == 'a') or ($PM[2] == 'emj_am_') or ($PM[2] == 'am')) {
            # au絵文字変換
            if (preg_match('/^docomo$/i',$career)) {
              # DoCoMo変換
              if (isset($this->AU_TO_DOCOMO[$PM[3]])) {
                if (preg_match('/^[0-9]{4}$/',$this->AU_TO_DOCOMO[$PM[3]])) {
                  $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$PM[1].$etype_top.'d'.$etype_sec.$this->AU_TO_DOCOMO[$PM[3]].$PM[4],$textstr);
                  $check_flag = True;
                }
              }
            } elseif (preg_match('/^au$/i',$career)) {
              # au変換
              $check_flag = True;
            } elseif (preg_match('/'.$this->softbank_name.'/i',$career)) {
              # SoftBank変換
              if (isset($this->AU_TO_SOFT[$PM[3]])) {
                if (preg_match('/^[0-9]{4}$/',$this->AU_TO_SOFT[$PM[3]])) {
                  $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$PM[1].$etype_top.'v'.$etype_sec.$this->AU_TO_SOFT[$PM[3]].$PM[4],$textstr);
                  $check_flag = True;
                }
              }
            }
            if ($check_flag == False) {
              # 対応絵文字が無い場合
              $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','?'.$PM[2].$PM[3].'?',$textstr);
            }
          } elseif (($PM[2] == 'emj_v_') or ($PM[2] == 'v')) {
            # SoftBank絵文字変換
            if (preg_match('/^docomo$/i',$career)) {
              # DoCoMoｴﾝｺｰﾄﾞ変換
              if (isset($this->SOFT_TO_DOCOMO[$PM[3]])) {
                if (preg_match('/^[0-9]{4}$/',$this->SOFT_TO_DOCOMO[$PM[3]])) {
                  $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$PM[1].$etype_top.'d'.$etype_sec.$this->SOFT_TO_DOCOMO[$PM[3]].$PM[4],$textstr);
                  $check_flag = True;
                }
              }
            } elseif (preg_match('/^au$/i',$career)) {
              # au変換
              if (isset($this->SOFT_TO_AU[$PM[3]])) {
                if (preg_match('/^[0-9]{4}$/',$this->SOFT_TO_AU[$PM[3]])) {
                  $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|',$PM[1].$etype_top.'a'.$etype_sec.$this->SOFT_TO_AU[$PM[3]].$PM[4],$textstr);
                  $check_flag = True;
                }
              }
            } elseif (preg_match('/'.$this->softbank_name.'/i',$career)) {
              # SoftBank変換
              $check_flag = True;
            }
            if ($check_flag == False) {
              # 対応絵文字が無い場合
              $textstr = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','?'.$PM[2].$PM[3].'?',$textstr);
            }
          }
          $loop_text = preg_replace('|'.$PM[1].$PM[2].$PM[3].$PM[4].'|','',$loop_text);
        }
      }

    } else {
      $textstr = '';
    }
    return $textstr;
  }

  # SoftBank 3G UTF-8ｺｰﾄﾞ対応 /////////////////////////////////////////////////
  # 絵文字ｴﾝｺｰﾄﾞされた文字列をｱｸｾｽｷｬﾘｱ、或いは指定のｷｬﾘｱの絵文字に変換します。
  # [引渡し値]
  # 　$textstr     : 変換対象文字列
  # 　$change_mode : 強制処理指定(1:強制変換処理)
  # [返り値]
  # 　$textstr     : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function v3_utf8_sjis($textstr,$change_mode='') {
    if (($this->HARD_DATA['hard'] == $this->softbank_name) and ($this->HARD_DATA['tg_flag'] == '3G') and (($change_mode == '1') or (mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]) == 'UTF-8'))) {
      # SoftBank絵文字ｴﾝｺｰﾄﾞ
      $textstr = $this->_replace_v_emoji_utf8($textstr);
      # 文字ｺｰﾄﾞ変換
      $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,'UTF-8');
      # Vofadone絵文字ﾃﾞｺｰﾄﾞ
      $TEXTSTR = $this->emj_decode($textstr);
      $textstr = $TEXTSTR['web'];
    }
    return $textstr;
  }

  # SoftBank 3G UTF-8ｺｰﾄﾞ変換(内部処理用) /////////////////////////////////////
  # SoftBank絵文字(UTF-8ｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞします。
  # [引渡し値]
  # 　$textstr : 変換対象文字列
  # [返り値]
  # 　$textstr : 変換後文字列
  #////////////////////////////////////////////////////////////////////////////
  function _replace_v_emoji_utf8($textstr) {
    # SoftBank絵文字ｴﾝｺｰﾄﾞ
    if ($this->enc_type == '') { $this->enc_type = '1'; }
    $ptn   = '';
    $NEWDT = array();
    $OLDDT = array();
    $OLDDT = explode("\r\n", $textstr);
    foreach ($OLDDT as $str) {
      if (preg_match('/\xEE[\x80\x81\x84\x85\x88\x89\x8C\x8D\x90\x91\x94][\x80-\xBF]/',$str)) {
        while (preg_match('/\xEE([\x80\x81\x84\x85\x88\x89\x8C\x8D\x90\x91\x94][\x80-\xBF])/',$str,$PM)) {
          $DEC = unpack('n1int', $PM[1]);
          if (isset($this->SOFT3G_DEC_TO_NO[$DEC['int']])) {
            $str = preg_replace('|\xEE'.$PM[1].'|',$this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].$this->SOFT3G_DEC_TO_NO[$DEC['int']].$this->DELIMITER[$this->enc_type]['right'], $str);
          } else {
            $str = preg_replace('|\xEE'.$PM[1].'|',$this->emoji_chr, $str);
          }
        }
      }
      $NEWDT[] = $str;
    }
    $news = join("\r\n", $NEWDT);
    return $news;
  }

  # DoCoMo絵文字ﾊﾞｲﾅﾘｺｰﾄﾞ変換(内部処理用) /////////////////////////////////////
  # DoCoMo絵文字(SJISﾊﾞｲﾅﾘｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞ又はSJISﾃｷｽﾄ変換します。
  # [引渡し値]
  # 　$str  : 変換対象文字列
  # 　$mode : 処理指定(指定なし:ｺｰﾄﾞ変換,1:削除,2:ｶｳﾝﾄ,3:下駄変換)
  # [返り値]
  # 　$news : 処理後文字列(ｶｳﾝﾄﾓｰﾄﾞの場合はｶｳﾝﾄ数)
  #////////////////////////////////////////////////////////////////////////////
  function _replace_d_emoji($str, $mode = '') {
    if ($this->enc_type == '') { $this->enc_type = '1'; }
    $no    = 0;
    $news  = '';
    $OLDDT = array();
    $NEWDT = array();
    $OLDDT = explode("\r\n", $str);
    foreach ($OLDDT as $str) {
      $old = $str;
      $new = '';
      if (preg_match('/[\xF8\xF9]/', $old)) {
        while (1) {
          $RES = array();
          if (preg_match('/^[\xF8\xF9][\x40-\xFC]/', $old , $RES)) {
            $old = preg_replace('/^[\xF8\xF9][\x40-\xFC]/', '', $old);
            if ($mode == '') {
              # 絵文字置換え
              $bin = unpack('n1int', $RES[0]);
              if (($this->enc_type >= 1) and ($this->enc_type <= '8')) {
                if (isset($this->DOCOMO_SJIS10_TO_NO[$bin["int"]])) {
                  $new .= $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].$this->DOCOMO_SJIS10_TO_NO[$bin["int"]].$this->DELIMITER[$this->enc_type]['right'];
                } else {
                  $new .= $this->emoji_chr;
                }
              } else {
                $new .= '&#'.$bin["int"].';';
              }
            } elseif ($mode == 1) {
              # 絵文字削除
            } elseif ($mode == 2) {
              # 絵文字ｶｳﾝﾄ
              $no++;
            } elseif ($mode == 3) {
              # 絵文字下駄変換
              $new .= $this->geta_str;
            }
          } elseif (preg_match('/^[\x81-\x9F\xE0-\xF7\xFA-\xFC][\x40-\x7E\x80-\xFC]/', $old, $RES)) {
            $old = preg_replace('/^[\x81-\x9F\xE0-\xF7\xFA-\xFC][\x40-\x7E\x80-\xFC]/', '', $old);
            $new .= $RES[0];
          } elseif (preg_match('/^./', $old, $RES)) {
            $old = preg_replace('/^./', '', $old);
            $new .= $RES[0];
          } else {
            break;
          }
        }
      } else {
        $new = $old;
      }
      $NEWDT[] = $new;
    }
    if ($mode == 2) {
      $news = $no;
    } else {
      $news = join("\r\n", $NEWDT);
    }
    return $news;
  }

  # DoCoMo絵文字UTF-8ﾊﾞｲﾅﾘｺｰﾄﾞ変換(内部処理用) ///////////////////////////////
  # DoCoMo絵文字(UTF-8ﾊﾞｲﾅﾘｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞ又はSJISﾃｷｽﾄ変換します。
  # [引渡し値]
  # 　$str  : 変換対象文字列
  # 　$mode : 処理指定(指定なし:ｺｰﾄﾞ変換,1:削除,2:ｶｳﾝﾄ,3:下駄変換)
  # [返り値]
  # 　$news : 処理後文字列(ｶｳﾝﾄﾓｰﾄﾞの場合はｶｳﾝﾄ数)
  #////////////////////////////////////////////////////////////////////////////
  function _replace_d_emoji_utf8($str, $mode = '') {
    if ($this->enc_type == '') { $this->enc_type = '1'; }
    $no    = 0;
    $news  = '';
    $OLDDT = array();
    $NEWDT = array();
    $OLDDT = explode("\r\n", $str);
    foreach ($OLDDT as $str) {
      $old = $str;
      $new = '';
      if (preg_match('/\xEE([\x98-\x9D][\x80-\xBF])/', $old)) {
        while (1) {
          $RES = array();
          if (preg_match('/^\xEE([\x98-\x9D][\x80-\xBF])/', $old , $RES)) {
            $old = preg_replace('/^\xEE[\x98-\x9D][\x80-\xBF]/', '', $old);
            if ($mode == '') {
              # 絵文字置換え
              $bin = unpack('n1int', $RES[1]);
              if (($this->enc_type >= 1) and ($this->enc_type <= '8')) {
                if (isset($this->DOCOMO_UTF8_TO_NO[$bin["int"]])) {
                  $new .= $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].$this->DOCOMO_UTF8_TO_NO[$bin["int"]].$this->DELIMITER[$this->enc_type]['right'];
                } else {
                  $new .= $this->emoji_chr;
                }
              } else {
                $new .= '&#'.$bin["int"].';';
              }
            } elseif ($mode == 1) {
              # 絵文字削除
            } elseif ($mode == 2) {
              # 絵文字ｶｳﾝﾄ
              $no++;
            } elseif ($mode == 3) {
              # 絵文字下駄変換
              $new .= $this->geta_str;
            }
          } elseif (preg_match('/^./', $old, $RES)) {
            $old = preg_replace('/^./', '', $old);
            $new .= $RES[0];
          } else {
            break;
          }
        }
      } else {
        $new = $old;
      }
      $NEWDT[] = $new;
    }
    if ($mode == 2) {
      $news = $no;
    } else {
      $news = join("\r\n", $NEWDT);
    }
    return $news;
  }

  # DoCoMo絵文字ﾃｷｽﾄｺｰﾄﾞ変換(内部処理用) ///////////////////////////////
  # DoCoMo絵文字(ﾃｷｽﾄｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞします。
  # [引渡し値]
  # 　$str  : 変換対象文字列
  # 　$mode : 処理指定(指定なし:ｺｰﾄﾞ変換,1:削除,2:ｶｳﾝﾄ,3:下駄変換)
  # [返り値]
  # 　$news : 処理後文字列(ｶｳﾝﾄﾓｰﾄﾞの場合はｶｳﾝﾄ数)
  #////////////////////////////////////////////////////////////////////////////
  function _replace_d_emoji_text($str, $mode = '') {

    if ($this->enc_type == '') { $this->enc_type = '1'; }

    $no    = 0;
    $news  = '';
    $OLDDT = array();
    $NEWDT = array();
    $OLDDT = explode("\r\n", $str);
    foreach ($OLDDT as $str) {
      $old = $str;
      $new = '';
      if (preg_match('/&#(x*)([0-9a-fA-F]+?);/', $old)) {
        while (1) {
          $RES = array();
          if (preg_match('/^&#(x*)([0-9a-fA-F]+?);/',$old ,$RES)) {
            $eflag = False;
            if ($mode == '') {
              # 絵文字置換え
              if (($this->enc_type >= 1) and ($this->enc_type <= '8')) {
                if (isset($this->{'ENC_TYPE'.$this->enc_type}[$RES[2]])) {
                  $new  .= $this->{'ENC_TYPE'.$this->enc_type}[$RES[2]];
                  $eflag = True;
                } else {
                  $new .= $this->emoji_chr;
                }
              } else {
                $new .= '&#'.$RES[1].$RES[2].';';
              }
            } elseif ($mode == 1) {
              # 絵文字削除
            } elseif ($mode == 2) {
              # 絵文字ｶｳﾝﾄ
              $no++;
            } elseif ($mode == 3) {
              # 絵文字下駄変換
              $new .= $this->geta_str;
            }
            $old = preg_replace('/^&#'.$RES[1].$RES[2].';/','',$old);
          } elseif (preg_match('/^./',$old,$RES)) {
            $old = preg_replace('/^./','',$old);
            $new .= $RES[0];
          } else {
            break;
          }
        }
      } else {
        $new = $old;
      }
      $NEWDT[] = $new;
    }
    if ($mode == 2) {
      $news = $no;
    } else {
      $news = join("\r\n", $NEWDT);
    }
    return $news;
  }

  # au絵文字ﾊﾞｲﾅﾘｺｰﾄﾞ変換(内部処理用) /////////////////////////////////////////
  # au絵文字(SJISﾊﾞｲﾅﾘｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞ又はSJISﾃｷｽﾄ変換します。
  # [引渡し値]
  # 　$str  : 変換対象文字列
  # 　$mode : 処理指定(指定なし:ｺｰﾄﾞ変換,1:削除,2:ｶｳﾝﾄ,3:下駄変換)
  # [返り値]
  # 　$news : 処理後文字列(ｶｳﾝﾄﾓｰﾄﾞの場合はｶｳﾝﾄ数)
  #////////////////////////////////////////////////////////////////////////////
  function _replace_a_emoji($str, $mode = '') {

    if ($this->enc_type == '') { $this->enc_type = '1'; }

    $no    = 0;
    $news  = '';
    $OLDDT = array();
    $NEWDT = array();
    $OLDDT = explode("\r\n", $str);
    foreach ($OLDDT as $str) {
      $old = $str;
      $new = '';
      if (preg_match('/[\xEB\xEC\xED\xEE\xF3\xF4\xF6\xF7]/', $old)) {
        while (1) {
          $RES = array();
          if (preg_match('/^[\xEB\xEC\xED\xEE\xF3\xF4\xF6\xF7][\x40-\xFC]/', $old , $RES)) {
            $old = preg_replace('/^[\xEB\xEC\xED\xEE\xF3\xF4\xF6\xF7][\x40-\xFC]/', '', $old);
            if ($mode == '') {
              # 絵文字置換え
              $bin = unpack('n1int', $RES[0]);
              if (($this->enc_type >= 1) and ($this->enc_type <= '8')) {
                if (isset($this->AU_SJIS10_TO_NO[$bin["int"]])) {
                  $new .= $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].$this->AU_SJIS10_TO_NO[$bin["int"]].$this->DELIMITER[$this->enc_type]['right'];
                } else {
                  $new .= $this->emoji_chr;
                }
              } else {
                $new .= '&#'.$bin["int"].';';
              }
            } elseif ($mode == 1) {
              # 絵文字削除
            } elseif ($mode == 2) {
              # 絵文字ｶｳﾝﾄ
              $no++;
            } elseif ($mode == 3) {
              # 絵文字下駄変換
              $new .= $this->geta_str;
            }

          } elseif (preg_match('/^[\x81-\x9F\xE0-\xF7\xFA-\xFC][\x40-\x7E\x80-\xFC]/', $old, $RES)) {
            $old = preg_replace('/^[\x81-\x9F\xE0-\xF7\xFA-\xFC][\x40-\x7E\x80-\xFC]/', '', $old);
            $new .= $RES[0];
          } elseif (preg_match('/^./', $old, $RES)) {
            $old = preg_replace('/^./', '', $old);
            $new .= $RES[0];
          } else {
            break;
          }
        }
      } else {
        $new = $old;
      }
      $NEWDT[] = $new;
    }
    if ($mode == 2) {
      $news = $no;
    } else {
      $news = join("\r\n", $NEWDT);
    }
    return $news;
  }

  # au絵文字UTF-8ﾊﾞｲﾅﾘｺｰﾄﾞ変換(内部処理用) ////////////////////////////////////
  # au絵文字(UTF-8ﾊﾞｲﾅﾘｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞ又はSJISﾃｷｽﾄ変換します。
  # [引渡し値]
  # 　$str  : 変換対象文字列
  # 　$mode : 処理指定(指定なし:ｺｰﾄﾞ変換,1:削除,2:ｶｳﾝﾄ,3:下駄変換)
  # [返り値]
  # 　$news : 処理後文字列(ｶｳﾝﾄﾓｰﾄﾞの場合はｶｳﾝﾄ数)
  #////////////////////////////////////////////////////////////////////////////
  function _replace_a_emoji_utf8($str, $mode = '') {

    if ($this->enc_type == '') { $this->enc_type = '1'; }

    $no    = 0;
    $news  = '';
    $OLDDT = array();
    $NEWDT = array();
    $OLDDT = explode("\r\n", $str);
    foreach ($OLDDT as $str) {
      $old = $str;
      $new = '';
      if (preg_match('/\xEE[\xB1-\xB3\xB5\xB6\xBD-\xBF][\x80-\xBF]/',$old) or preg_match('/\xEF[\x81\x82\x83][\x80-\xBF]/',$old)) {
        while (1) {
          $RES = array();
          if (preg_match('/^\xEE([\xB1-\xB3\xB5\xB6\xBD-\xBF][\x80-\xBF])/',$old,$RES) or preg_match('/^\xEF([\x81\x82\x83][\x80-\xBF])/',$old,$RES)) {
            $old = preg_replace('/^'.$RES[0].'/','',$old);
            if ($mode == '') {
              # 絵文字置換え
              $bin = unpack('n1int', $RES[1]);
              if (($this->enc_type >= 1) and ($this->enc_type <= '8')) {
                if (isset($this->AU_UTF8_TO_NO[$bin["int"]])) {
                  $new .= $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].$this->AU_UTF8_TO_NO[$bin["int"]].$this->DELIMITER[$this->enc_type]['right'];
                } else {
                  $new .= $this->emoji_chr;
                }
              } else {
                $new .= '&#'.$bin["int"].';';
              }
            } elseif ($mode == 1) {
              # 絵文字削除
            } elseif ($mode == 2) {
              # 絵文字ｶｳﾝﾄ
              $no++;
            } elseif ($mode == 3) {
              # 絵文字下駄変換
              $new .= $this->geta_str;
            }
          } elseif (preg_match('/^./', $old, $RES)) {
            $old = preg_replace('/^./', '', $old);
            $new .= $RES[0];
          } else {
            break;
          }
        }
      } else {
        $new = $old;
      }
      $NEWDT[] = $new;
    }
    if ($mode == 2) {
      $news = $no;
    } else {
      $news = join("\r\n", $NEWDT);
    }
    return $news;
  }

  # au絵文字ﾊﾞｲﾅﾘｺｰﾄﾞ変換(内部処理用ｻﾌﾞ処理) //////////////////////////////////
  # au絵文字(SJISﾊﾞｲﾅﾘｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞ又はSJISﾃｷｽﾄ変換します。
  # [引渡し値]
  # 　$str  : 変換対象文字列
  # 　$mode : 処理指定(指定なし:ｺｰﾄﾞ変換,1:削除,2:ｶｳﾝﾄ,3:下駄変換)
  # [返り値]
  # 　$news : 処理後文字列(ｶｳﾝﾄﾓｰﾄﾞの場合はｶｳﾝﾄ数)
  #////////////////////////////////////////////////////////////////////////////
  function _replace_a_emoji_sub($str, $mode = '') {
    $no = 0;
    $news = '';
    $OLDDT = array();
    $NEWDT = array();
    $OLDDT = explode("\r\n", $str);
    foreach ($OLDDT as $str) {
      $old = $str;
      $new = '';
      if (preg_match('/[\xEB\xEC\xED\xEE\xF3\xF4\xF6\xF7]/', $old)) {
        while (1) {
          $RES = array();
          if (preg_match('/^[\xEB\xEC\xED\xEE\xF3\xF4\xF6\xF7][\x40-\xFC]/', $old , $RES)) {
            $old = preg_replace('/^[\xEB\xEC\xED\xEE\xF3\xF4\xF6\xF7][\x40-\xFC]/', '', $old);

            if ($mode == '') {
              # 絵文字置換え
              $bin = unpack('n1int', $RES[0]);
              $new .= '&#'.$bin["int"].'_sub;';
            } elseif ($mode == 1) {
              # 絵文字削除
            } elseif ($mode == 2) {
              # 絵文字ｶｳﾝﾄ
              $no++;
            } elseif ($mode == 3) {
              # 絵文字下駄変換
              $new .= $this->geta_str;
            }

          } elseif (preg_match('/^[\x81-\x9F\xE0-\xF7\xFA-\xFC][\x40-\x7E\x80-\xFC]/', $old, $RES)) {
            $old = preg_replace('/^[\x81-\x9F\xE0-\xF7\xFA-\xFC][\x40-\x7E\x80-\xFC]/', '', $old);
            $new .= $RES[0];
          } elseif (preg_match('/^./', $old, $RES)) {
            $old = preg_replace('/^./', '', $old);
            $new .= $RES[0];
          } else {
            break;
          }
        }
      } else {
        $new = $old;
      }
      $NEWDT[] = $new;
    }
    if ($mode == 2) {
      $news = $no;
    } else {
      $news = join("\r\n", $NEWDT);
    }
    return $news;
  }

  # SoftBank絵文字ﾊﾞｲﾅﾘｺｰﾄﾞ変換(内部処理用) ///////////////////////////////////
  # SoftBank絵文字(SJISWebｺｰﾄﾞ)を絵文字ｴﾝｺｰﾄﾞ変換します。
  # [引渡し値]
  # 　$str  : 変換対象文字列
  # 　$mode : 処理指定(指定なし:ｺｰﾄﾞ変換,1:削除,2:ｶｳﾝﾄ,3:下駄変換)
  # [返り値]
  # 　$news : 処理後文字列(ｶｳﾝﾄﾓｰﾄﾞの場合はｶｳﾝﾄ数)
  #////////////////////////////////////////////////////////////////////////////
  function _replace_v_emoji($str, $mode = '') {
    if ($this->enc_type == '') { $this->enc_type = '1'; }
    $str .= chr(0x0F);
    # 絵文字第一ﾊﾞｲﾄ展開
    while (preg_match('/(\x1B\$[GEFOPQ])([\x21-\x7A])([\x21-\x7A]+)(\x0F)/', $str)) {
      $str = preg_replace('/(\x1B\$[GEFOPQ])([\x21-\x7A])([\x21-\x7A]+)(\x0F)/', '\\1\\2\\4\\1\\3\\4', $str);
    }
    # 絵文字置換え
    while (preg_match('/(\x1B\$[GEFOPQ][\x21-\x7A]\x0F)/', $str, $PM)) {
      $pms = quotemeta($PM[1]);
      if ($mode == '') {
        # 絵文字置換え
        $str = preg_replace('|'.$pms.'|', $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].$this->SOFT_WEBCODE_TO_NO[$PM[1]].$this->DELIMITER[$this->enc_type]['right'], $str);
      } elseif ($mode == 1) {
        # 絵文字削除
        $str = preg_replace('|'.$pms.'|', '', $str);
      } elseif ($mode == 2) {
        # 絵文字ｶｳﾝﾄ
        $no++;
      } elseif ($mode == 3) {
        # 絵文字下駄変換
        $str = preg_replace('|'.$pms.'|', $this->geta_str, $str);
      }
    }
    # SI消去
    $str = preg_replace('/\x0F$/', '', $str);
    if ($mode == 2) { $str = $no; }
    return $str;
  }

  # ﾌｫｰﾑ処理用 ////////////////////////////////////////////////////////////////
  # ﾌｫｰﾑで表示する際のｴﾝﾃｨﾃｨ処理を行います。
  # [引渡し値]
  # 　$html : ｴﾝﾃｨﾃｨ対象文字列
  # [返り値]
  # 　$html : ｴﾝﾃｨﾃｨ処理後文字列
  #////////////////////////////////////////////////////////////////////////////
  function form_htmlentities($html) {
    $html = preg_replace('/</','&lt;',$html);
    $html = preg_replace('/>/','&gt;',$html);
    $html = preg_replace('/"/','&#34;',$html);
    $html = preg_replace("/'/",'&#39;',$html);
    return $html;
  }



















  # mobile_class_8_mail 移植 //////////////////////////////////////////////////
  # 　$this->へ変更
  #////////////////////////////////////////////////////////////////////////////

  # ﾒｰﾙｱﾄﾞﾚｽｷｬﾘｱ解析 //////////////////////////////////////////////////////////
  # ﾒｰﾙｱﾄﾞﾚｽよりｷｬﾘｱ情報を取得します
  # [引渡し値]
  # 　$mail_address : ﾒｰﾙｱﾄﾞﾚｽ
  # [返り値]
  # 　$career : ｷｬﾘｱ判別結果(DoCoMo,au,SoftBank or Vodafone)
  #////////////////////////////////////////////////////////////////////////////
  function get_mail_career($mail_address) {

    $career = '';
    if (preg_match('/^(.+?)\@(.*)docomo(.+)$/',$mail_address)) {
      # DoCoMo携帯
      $career = 'DoCoMo';
    } elseif (preg_match('/^(.+?)\@(.*)vodafone(.+)$/',$mail_address) or preg_match('/^(.+?)\@softbank(.+)$/',$mail_address)) {
      # SoftBank(Vodafone)携帯
      $career = $this->softbank_name;
    } elseif (preg_match('/^(.+?)\@(.*)disney(.+)$/',$mail_address) or preg_match('/^(.+?)\@i.softbank(.+)$/',$mail_address)) {
      # SoftBank(Vodafone)携帯
      $career = $this->softbank_name;
    } elseif (preg_match('/^(.+?)\@(.*)ezweb(.+)$/',$mail_address)) {
      # au携帯
      $career = 'au';
    } else {
      # その他
      $career = 'PC';
    }
    return $career;
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


/*---------------sbtest-----------------------------------------------*/

/*
pr($mail_code);
pr($html_flag);

$to_career = $this->get_mail_career($to_add);

$BODY = $this->emj_decode($body,$to_career,$mail_code,$html_flag);

$bbbbb = mb_detect_encoding($body);

pr($bbbbb);


$subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";

$body = $BODY['mail'];

#$body = base64_encode($body);

# ヘッダ
$he  = "From: ".$from_add."\r\n";
$he .= "Reply-To: ".$repry_to."\r\n";
$he .= "MIME-Version: 1.0\r\n";
$he .= "Content-Type: text/plain;charset=UTF-8\r\n";
$he .= "Content-Transfer-Encoding: 7bit";
pr($he);
pr($to_add);

$success = @mail($to_add,$subject,$body,$he,'-f'.$return_path);
*/
/*--------------------------------------------------------------*/
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
      if ($this->cont_trs_enc) {
        $content_transfer_encoding = $this->cont_trs_enc;
      } else {
        $content_transfer_encoding = '7bit';
      }
    }
    # 送信先ｷｬﾘｱ取得d
    $to_career = $this->get_mail_career($to_add);

    # 送信先(To句)生成
    $set_to = '';
    if ($to_name != '') {
      # 送信者名の指定がある場合
      $str_code = mb_detect_encoding($to_name,$this->ENCODINGLIST[$this->chr_code]);
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
      $str_code = mb_detect_encoding($from_name,$this->ENCODINGLIST[$this->chr_code]);
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
      $str_code = mb_detect_encoding($repry_name,$this->ENCODINGLIST[$this->chr_code]);
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
      $subject = $this->emj_encode($subject,'','',$input_code);
      $body    = $this->emj_encode($body,'','',$input_code);
    }
    # 文字ｺｰﾄﾞ取得
    $subject_code = mb_detect_encoding($subject,$this->ENCODINGLIST[$this->chr_code]);
    $body_code    = mb_detect_encoding($body,$this->ENCODINGLIST[$this->chr_code]);
    # 文字ｺｰﾄﾞ変換
    if ($subject_code != $mail_code) { $subject = @mb_convert_encoding($subject,$mail_code,$subject_code); }
    if ($body_code    != $mail_code) { $body    = @mb_convert_encoding($body,$mail_code,$subject_code); }
    # ｶﾀｶﾅ変換
    $subject = mb_convert_kana($subject,'KV',$mail_code);
    $body    = mb_convert_kana($body,'KV',$mail_code);

    # 件名処理
    # 絵文字変換(ﾃﾞｺｰﾄﾞ)
    $SUBJECT = $this->emj_decode($subject,$to_career,$mail_code);
    $subject = $SUBJECT['mail'];
    # 件名処理
    if (preg_match('/^'.$this->softbank_name.'$/i',$to_career)) {
        if ($subject == '') { $subject = "無題"; }
        $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
    } else {

        if ($subject == '') { $subject = @mb_convert_encoding('無題','JIS','SJIS'); }
        $subject = base64_encode($subject);
        $subject = '=?ISO-2022-JP?B?'.$subject.'?=';
    }
    # 本文処理
    # 本文絵文字認識
    $EMJ_COUNT = $this->emj_check($body,'1');
    if ($EMJ_COUNT['total'] > 0) { $body_emj_flag = True; } else { $body_emj_flag = False; }
    # ﾒｰﾙ送信用絵文字変換(ﾃﾞｺｰﾄﾞ)
    $BODY = $this->emj_decode($body,$to_career,$mail_code,$html_flag);
    $body = $BODY['mail'];
    if ((preg_match('/^pc$/i',$to_career) or preg_match('/^'.$this->softbank_name.'$/i',$to_career)) and (($body_emj_flag == True) or ($html_flag == '1'))) {
      # HTMLﾀｸﾞ有無ﾁｪｯｸ
      $tag_flag = False;
      if ($body != strip_tags($body)) { $tag_flag = True; }
      # 本文HTML化処理
      $body = preg_replace('/\r/','',$body);
      if ($tag_flag == False) { $body = preg_replace('/\r\n/',"<br>\r\n",$body); }
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
      $header .= "Content-Type: multipart/mixed;\r\n";
      $header .= "\tboundary=\"".$boundary."\"\r\n";
      # 本文生成
      $msg .= "This is a multi-part message in MIME format.\r\n\r\n";
      $msg .= "--".$boundary."\r\n";
    }
    $ht = '';
    # ここsoftbank消した kizu
    if ((preg_match('/^pc$/i',$to_career)) and (($body_emj_flag == True) or ($html_flag == '1'))) {
      # HTMLﾒｰﾙの場合
      $ht .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    } else {
      # ﾃｷｽﾄﾒｰﾙの場合 これ追加 kizu
      if (preg_match('/^'.$this->softbank_name.'$/i',$to_career)) {
        $ht .= "Content-Type: text/plain;charset=UTF-8\r\n";
      } else {
        $ht .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
      }
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
      $msg .= "\r\n\r\n--".$boundary."\r\n";
      $msg .= "Content-Type: ".$upfile_type.";\r\n";
      $msg .= "\tname=\"".$upfile_name."\"\r\n";
      $msg .= "Content-Transfer-Encoding: base64\r\n";
      $msg .= "Content-Disposition: attachment;\r\n";
      $msg .= "\tfilename=\"".$upfile_name."\"\r\n\r\n";
      $msg .= $f_encoded."\r\n";
      $msg .= "--".$boundary."--";
      $body = $msg;
    } else {
      # 添付ﾌｧｲﾙ無い場合
      $add_mail_header .= $ht;
    }

    #if ((EMOJI_smtp_flag == 1) and is_object($this)) {
    if ((EMOJI_smtp_flag == 1)) {
      # SMTP送信
      # 送信内容設定
      $this->TOLIST[$to_name] = $to_add;
      $this->from_name        = $from_name;
      $this->from_address     = $from_add;
      $this->reply_to_name    = $repry_name;
      $this->reply_to_address = $repry_to;
      $this->return_path      = $return_path;
      #$this->add_header       = '';
      $this->add_header       = $add_mail_header;
      $this->subject          = $subject;
      $this->body             = $body;

      # ﾒｰﾙ送信
      $success = $this->smtp_mail();

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
      if ($this->cont_trs_enc) {
        $content_transfer_encoding = $this->cont_trs_enc;
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
          $str_code   = mb_detect_encoding($namedt,$this->ENCODINGLIST[$this->chr_code]);
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
          $str_code   = mb_detect_encoding($namedt,$this->ENCODINGLIST[$this->chr_code]);
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
          $str_code    = mb_detect_encoding($namedt,$this->ENCODINGLIST[$this->chr_code]);
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
      $str_code = mb_detect_encoding($from_name,$this->ENCODINGLIST[$this->chr_code]);
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
      $str_code = mb_detect_encoding($repry_name,$this->ENCODINGLIST[$this->chr_code]);
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
      $subject    = $this->emj_encode($subject   ,'','',$input_code);
      $body_plain = $this->emj_encode($body_plain,'','',$input_code);
      $body_html  = $this->emj_encode($body_html ,'','',$input_code);
    }

    # 文字ｺｰﾄﾞ取得
    $subject_code    = mb_detect_encoding($subject   ,$this->ENCODINGLIST[$this->chr_code]);
    $body_plain_code = mb_detect_encoding($body_plain,$this->ENCODINGLIST[$this->chr_code]);
    $body_html_code  = mb_detect_encoding($body_html ,$this->ENCODINGLIST[$this->chr_code]);

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
      $subject = $this->delete_emoji_code($subject);
    } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
      # SoftBank宛ての場合(絵文字削除)
      $subject = $this->delete_emoji_code($subject);
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $SUBJECT = $this->emj_decode($subject,$to_career,$mail_code);
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
      $ECOUNT = $this->emj_check($body_plain,'',$input_code);
      if ($ECOUNT['total'] > 0) {
        # HTML化
        if ($body_html == '') {
          $body_html = $body_plain;
          $mail_type = 'multipart';
        }
        # 絵文字有り絵文字削除
        $body_plain = $this->delete_emoji_code($body_plain);
      }
    } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
      # SoftBank向け
      # 絵文字有無ﾁｪｯｸ
      $ECOUNT = $this->emj_check($body_plain,'',$input_code);
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
            $body_html = preg_replace('/\r\n/',"<br>\r\n",$body_html);
            $body_html = "<html><head><meta http-equiv=\"content-type\" content=\"text/html;charset=UTF-8\"></head><body>\r\n{$body_html}\r\n</body></html>";
          }
          $BODYPLAIN    = $this->emj_decode($body_plain,$to_career,$mail_code);
          $body_plain   = $BODYPLAIN['mail_plain'];
          $to_html_flag = True;
          $enc_code     = 'UTF-8';
          $mail_type    = 'multipart';
        }
      }
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $BODYPLAIN  = $this->emj_decode($body_plain,$to_career,$mail_code);
      $body_plain = $BODYPLAIN['mail'];
    }
    $body_plain = preg_replace('/\r/','',$body_plain);
    if (($body_plain != '') and !preg_match('/\r\n$/',$body_plain)) { $body_plain .= "\r\n"; }

    # 本文処理(HTML)
    # ﾒｰﾙ送信用絵文字変換(ﾃﾞｺｰﾄﾞ)
    if (($to_career == '') or ($to_career == 'PC')) {
      # PC及び全ｷｬﾘｱ向けの場合(画像変換)
      $BODYHTML  = $this->emj_decode($body_html,$to_career,$mail_code,1);
      $body_html = $BODYHTML['mail'];
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      if ($to_html_flag == True) {
        # SoftBank宛てHTMLﾒｰﾙ用絵文字ﾃﾞｺｰﾄﾞ
        $BODYPLAIN  = $this->emj_decode($body_html,$to_career,'UTF-8');
        $body_html  = $BODYPLAIN['mail'];
      } else {
        # SoftBank宛てHTMLﾒｰﾙ以外用絵文字ﾃﾞｺｰﾄﾞ
        $BODYHTML  = $this->emj_decode($body_html,$to_career,$mail_code);
        $body_html = $BODYHTML['mail'];
      }
    }

    # 本文HTML化処理
    $body_html = preg_replace('/\r/','',$body_html);
    if (($body_html != '') and !preg_match('/\r\n$/',$body_html)) { $body_html .= "\r\n"; }

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
    $add_mail_header      .= "From: ".$set_form."\r\n";
    $add_mail_header      .= "Reply-To: ".$set_repry_to."\r\n";
    if ($set_cc  != '') { $add_mail_header .= "Cc: ".$set_cc."\r\n"; }
    if ($set_bcc != '') { $add_mail_header .= "Bcc: ".$set_bcc."\r\n"; }
    $add_mail_header .= "MIME-Version: 1.0\r\n";
    $add_mail_header_smtp .= "MIME-Version: 1.0\r\n";

    # ﾍｯﾀﾞｰ処理
    if (preg_match('/^multipart/',$mail_type)) {
      # ﾏﾙﾁﾊﾟｰﾄﾒｰﾙ(ﾃｷｽﾄ+HTML,ﾃｷｽﾄ+HTML+添付ﾌｧｲﾙ,ﾃｷｽﾄorHTML+添付ﾌｧｲﾙ)
      # ﾊﾞｳﾝﾀﾞﾘｰ文字(ﾊﾟｰﾄの境界)
      $boundary = md5(uniqid(rand()));
      # ﾍｯﾀﾞｰ設定
      if ($mail_type == 'multipart') {
        # HTMLﾒｰﾙ
        $add_mail_header      .= "Content-Type: multipart/alternative; boundary=\"".$boundary."\"\r\n";
        $add_mail_header_smtp .= "Content-Type: multipart/alternative; boundary=\"".$boundary."\"\r\n";
      } else {
        # 添付ﾌｧｲﾙ
        $add_mail_header      .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
        $add_mail_header_smtp .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
      }
      $add_mail_header      .= "This is a multi-part message in MIME format.";
      $add_mail_header_smtp .= "This is a multi-part message in MIME format.";
    } elseif ($mail_type == 'plain') {
      $add_mail_header      .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
      $add_mail_header      .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
      $add_mail_header_smtp .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
      $add_mail_header_smtp .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
    } elseif ($mail_type == 'html') {
      $add_mail_header .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $add_mail_header .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
      $add_mail_header_smtp .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $add_mail_header_smtp .= "Content-Transfer-Encoding: ".$content_transfer_encoding;
    }

    # 本文処理
    $msg = '';
    if (preg_match('/^multipart/',$mail_type)) {
      if (($body_plain != '') and ($body_html != '') and preg_match('/file/',$mail_type)) {
        # ﾏﾙﾁﾊﾟｰﾄﾒｰﾙ(ﾃｷｽﾄ+HTML+添付ﾌｧｲﾙ)
        $boundary_2 = md5(uniqid(rand()));
        # ﾊﾟｰﾄ区切りｽﾀｰﾄ
        $msg .= "--".$boundary."\r\n";
        $msg .= "Content-Type: multipart/alternative; boundary=\"".$boundary_2."\"\r\n";
        $msg .= "\r\n";
        # ﾃｷｽﾄ本文
        $msg .= "--".$boundary_2."\r\n";
        $msg .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
        $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\r\n";
        $msg .= "\r\n";
        $msg .= $body_plain;
        $msg .= "\r\n";
        # HTML本文
        $msg .= "--".$boundary_2."\r\n";
        $msg .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\r\n";
        $msg .= "\r\n";
        $msg .= $body_html;
        $msg .= "\r\n";
        # ﾊﾟｰﾄ区切り終了
        $msg .= "--".$boundary_2."--\r\n";
        # 添付ﾌｧｲﾙ
        foreach ($UPFILELIST as $UDT) {
          $msg .= "--".$boundary."\r\n";
          $msg .= "Content-Type: ".$UDT['mime'].";\r\n";
          $msg .= "\tname=\"".$UDT['basename']."\"\r\n";
          $msg .= "Content-Transfer-Encoding: base64\r\n";
          $msg .= "Content-Disposition: attachment;\r\n";
          $msg .= "\tfilename=\"".$UDT['basename']."\"\r\n\r\n";
          $msg .= $UDT['filedata']."\r\n";
          $msg .= "\r\n";
        }

      } else {
        # ﾏﾙﾁﾊﾟｰﾄﾒｰﾙ(ﾃｷｽﾄ+HTML,ﾃｷｽﾄorHTML+添付ﾌｧｲﾙ)
        if ($body_plain != '') {
          # ﾃｷｽﾄ設定
          $msg .= "--".$boundary."\r\n";
          $msg .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
          $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\r\n";
          $msg .= "\r\n";
          $msg .= $body_plain;
          $msg .= "\r\n";
        }
        if ($body_html != '') {
          # HTML設定
          $msg .= "--".$boundary."\r\n";
          $msg .= "Content-Type: text/html; charset=\"{$enc_code}\"\r\n";
          $msg .= "Content-Transfer-Encoding: ".$content_transfer_encoding."\r\n";
          $msg .= "\r\n";
          $msg .= $body_html;
          $msg .= "\r\n";
        }
        if ($upfile_flag == 1) {
          # 添付ﾌｧｲﾙ有る場合
          foreach ($UPFILELIST as $UDT) {
            $msg .= "--".$boundary."\r\n";
            $msg .= "Content-Type: ".$UDT['mime'].";\r\n";
            $msg .= "\tname=\"".$UDT['basename']."\"\r\n";
            $msg .= "Content-Transfer-Encoding: base64\r\n";
            $msg .= "Content-Disposition: attachment;\r\n";
            $msg .= "\tfilename=\"".$UDT['basename']."\"\r\n\r\n";
            $msg .= $UDT['filedata']."\r\n";
            $msg .= "\r\n";
          }
        }
      }
      $msg .= "--".$boundary."--\r\n";
    } elseif ($mail_type == 'plain') {
      # ﾃｷｽﾄﾒｰﾙ
      $msg .= $body_plain;
    } elseif ($mail_type == 'html') {
      # HTMLﾒｰﾙ
      $msg .= $body_html;
    }
    # ﾒｰﾙ送信
    #if ((EMOJI_smtp_flag == 1) and is_object($this)) {
    if ((EMOJI_smtp_flag == 1)) {
      # SMTP送信
      # 送信内容設定
      $this->TOLIST           = $TODATA;
      $this->CCDATA           = $CCDATA;
      $this->BCCDATA          = $BCCDATA;
      $this->from_name        = $from_name;
      $this->from_address     = $from_add;
      $this->reply_to_name    = $repry_name;
      $this->reply_to_address = $repry_to;
      $this->return_path      = $return_path;
      $this->add_header       = $add_mail_header_smtp;
      $this->subject          = $subject;
      $this->body             = $msg;
      # ﾒｰﾙ送信
      $success = $this->smtp_mail();
    } else {
      # PHP mail関数送信
      $success = @mail($set_to,$subject,$msg,$add_mail_header,'-f'.$return_path);
    }
    if ($success) { return True; } else { return False; }
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

    if (is_object($this)) {
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
      return $this->emoji_decome($MAIL_DATA,$SETTING_DATA,$UPFILE,$katakana_chg_cancel);
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

    $mime = 'application/octet-stream';
    $PATHDATA  = pathinfo($filename);
    if (isset($PATHDATA['extension'])) {
      if ($PATHDATA['extension'] != '') {
        $extension = $PATHDATA['extension'];
#        if (isset($this->FILETYPE[$PATHDATA['extension']])) { $mime = $this->FILETYPE[$PATHDATA['extension']]; }
        if (isset($this->FILETYPE[$PATHDATA['extension']])) { $mime = $this->FILETYPE[$PATHDATA['extension']]; }
      }
    }
    return $mime;
  }


























  # mobile_class_8_sub 移植 //////////////////////////////////////////////////
  # 　$this->へ変更
  #////////////////////////////////////////////////////////////////////////////


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

    # 絵文字下駄変換
    $textstr = $this->emoji_str_replace($textstr,$this->geta_str,$docomo_flag,$voda_flag,$au_flag,$out_code,$enc_cancel,$input_code);
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

    if (isset($textstr)) {
      if ($out_code == '') { $oc = $this->chr_code; } else { $oc = $out_code; }
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $this->emj_encode($textstr,'',1,$input_code); }
      # 変換対象文字列ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
      }
      # 置換え文字列ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($replace_str,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($replace_str,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $replace_str_code = mb_preferred_mime_name($de);
        if ($replace_str_code != mb_preferred_mime_name($this->chg_code_sjis)) { $replace_str = @mb_convert_encoding($replace_str,$this->chg_code_sjis,$text_code); }
      }
      # DoCoMo絵文字置換え
      if ($docomo_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'d'.$this->DELIMITER[$i]['b'].'(\d+?)'.$this->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # au絵文字置換え
      if ($au_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'a'.$this->DELIMITER[$i]['b'].'(\d+?)'.$this->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
          $textstr = preg_replace('/'.$this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'am'.$this->DELIMITER[$i]['b'].'(\d+?)'.$this->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # SoftBank絵文字置換え
      if ($voda_flag == '0') {
        for ($i = 1; $i <= 8; $i++) {
          $textstr = preg_replace('/'.$this->DELIMITER[$i]['left'].$this->DELIMITER[$i]['a'].'v'.$this->DELIMITER[$i]['b'].'(\d+?)'.$this->DELIMITER[$i]['right'].'/',$replace_str,$textstr);
        }
      }
      # ﾃｷｽﾄｺｰﾄﾞ変換
      $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # 出力ｺｰﾄﾞ設定
        if ($text_code != mb_preferred_mime_name($oc)) {
          # 文字列ｺｰﾄﾞが指定出力ｺｰﾄﾞと異なる場合
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($this->chg_code_sjis)) {
            # SJIS指定の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$this->chg_code_sjis);
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

    $COUNTDATA = array();
    $COUNTDATA['mb_strlen']   = 0;
    $COUNTDATA['mb_strwidth'] = 0;
    $COUNTDATA['total']       = 0;
    $COUNTDATA['DoCoMo']      = 0;
    $COUNTDATA['au']          = 0;
    $COUNTDATA['SoftBank']    = 0;
    if (isset($textstr)) {
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $this->emj_encode($textstr,'',$enc_cancel,$input_code); }

      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
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

    if (isset($textstr)) {
      if ($out_code == '') { $oc = $this->chr_code; } else { $oc = $out_code; }
      $offset = 0;
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $this->emj_encode($textstr,'',1,$input_code); }
      # ﾃｷｽﾄShift_JIS変換
      if ($input_code == '') {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
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
      $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # 出力ｺｰﾄﾞ設定
        if ($text_code != mb_preferred_mime_name($oc)) {
          # 文字列ｺｰﾄﾞが指定出力ｺｰﾄﾞと異なる場合
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($this->chg_code_sjis)) {
            # SJIS指定の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$this->chg_code_sjis);
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

    if (isset($textstr) and isset($original_emj) and isset($change_emj)) {
      if ($out_code == '') { $oc = $this->chr_code; } else { $oc = $out_code; }
      # 絵文字ｴﾝｺｰﾄﾞ
      if ($enc_cancel != '1') { $textstr = $this->emj_encode($textstr,'',1,$input_code); }
      $original_emj = $this->emj_encode($original_emj,'',1,$input_code);
      $change_emj   = $this->emj_encode($change_emj,'',1,$input_code);
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
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$this->chr_code]);
      } else {
        $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$input_code]);
      }
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        if ($text_code != mb_preferred_mime_name($this->chg_code_sjis)) { $textstr = @mb_convert_encoding($textstr,$this->chg_code_sjis,$text_code); }
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
      $de = mb_detect_encoding($textstr,$this->ENCODINGLIST[$oc]);
      if ($de) {
        $text_code = mb_preferred_mime_name($de);
        # 出力ｺｰﾄﾞ設定
        if ($text_code != mb_preferred_mime_name($oc)) {
          # 文字列ｺｰﾄﾞが指定出力ｺｰﾄﾞと異なる場合
          if (mb_preferred_mime_name($oc) != mb_preferred_mime_name($this->chg_code_sjis)) {
            # SJIS指定の場合
            $textstr = @mb_convert_encoding($textstr,$oc,$this->chg_code_sjis);
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
    $html = preg_replace('/\r\n/','\\r\n',$html);
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

    $emoji_num = '';
    if (!isset($num_text)) { return $emoji_num; }
    if ($num_text == '')   { return $emoji_num; }
    if (strlen($num_text) == 1) {
      if ($this->hard == 'PC') {
        # PC絵文字画像(DoCoMo絵文字使用)
        if ($num_text == '0') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0134'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0125'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0126'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0127'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0128'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0129'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0130'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0131'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0132'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0133'.$this->DELIMITER[$this->enc_type]['right']; }
      } elseif ($this->hard == 'DoCoMo') {
        # DoCoMo絵文字
        if ($num_text == '0') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0134'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0125'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0126'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0127'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0128'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0129'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0130'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0131'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0132'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0133'.$this->DELIMITER[$this->enc_type]['right']; }
      } elseif ($this->hard == 'au') {
        # au絵文字
        if ($num_text == '0') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0325'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0180'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0181'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0182'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0183'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0184'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0185'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0186'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0187'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0188'.$this->DELIMITER[$this->enc_type]['right']; }
      } elseif ($this->hard == $this->softbank_name) {
        # SoftBank絵文字
        if ($num_text == '0') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0227'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '1') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0218'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '2') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0219'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '3') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0220'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '4') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0221'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '5') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0222'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '6') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0223'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '7') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0224'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '8') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0225'.$this->DELIMITER[$this->enc_type]['right']; }
        if ($num_text == '9') { $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0226'.$this->DELIMITER[$this->enc_type]['right']; }
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
      if ($this->hard == 'PC') {
        # PC絵文字画像(DoCoMo絵文字使用)
        $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0134'.$this->DELIMITER[$this->enc_type]['right'];
      } elseif ($this->hard == 'DoCoMo') {
        # DoCoMo絵文字
        $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'d'.$this->DELIMITER[$this->enc_type]['b'].'0134'.$this->DELIMITER[$this->enc_type]['right'];
      } elseif ($this->hard == 'au') {
        # au絵文字
        $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'a'.$this->DELIMITER[$this->enc_type]['b'].'0189'.$this->DELIMITER[$this->enc_type]['right'];
      } elseif ($this->hard == $this->softbank_name) {
        # SoftBank絵文字
        $emoji_num = $this->DELIMITER[$this->enc_type]['left'].$this->DELIMITER[$this->enc_type]['a'].'v'.$this->DELIMITER[$this->enc_type]['b'].'0227'.$this->DELIMITER[$this->enc_type]['right'];
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
        $KTDATA[] = $GETDATA['career']."\t".$GETDATA['kubun']."\t".$GETDATA['maker']."\t".$GETDATA['model']."\t".$GETDATA['yusen']."\t".$GETDATA['user_agent_patt']."\t".$GETDATA['sikibetu']."\t".$GETDATA['check_point']."\t".$GETDATA['check_string']."\t".$GETDATA['img_mime']."\t".$GETDATA['img_ext']."\t".$GETDATA['mov_mime']."\t".$GETDATA['mov_ext']."\t".$GETDATA['mov_size']."\t".$GETDATA['mov_download_max_size']."\t".$GETDATA['mov_stream_max_size']."\t".$GETDATA['display_width']."\t".$GETDATA['display_height']."\t".$GETDATA['display_color']."\t".$GETDATA['cache_size']."\t".$GETDATA['fitmov_patt_name1']."\t".$GETDATA['fitmov_patt_name2']."\t".$GETDATA['biko0']."\t".$GETDATA['biko1']."\t".$GETDATA['biko2']."\t".$editdate."\t\t\r\n";
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
















  # decome_class 移植 //////////////////////////////////////////////////
  # 　$this->へ変更
  #////////////////////////////////////////////////////////////////////////////

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
	/*
    if (DECOME_DEBUG_MODE == True) {
      header('Content-Type: text/plain; charset=ISO-2022-JP');
      print "To     =>".$MAIL['set_to']."\r\n";
      print "Return =>".$MAIL['return_path']."\r\n";
      print "Subject=>".$MAIL['subject']."\r\n";
      print "header =>".$MAIL['add_mail_header']."\r\n";
      print "Body=>\r\n";
      print $MAIL['mail_body'];
      exit();
    }
	*/

    # ﾒｰﾙ送信
    if ($MAIL['error'] == False) {
      #if ((EMOJI_smtp_flag == 1) and is_object($this)) {
      if ((EMOJI_smtp_flag == 1) ) {
        # SMTP送信
        # 送信内容設定
        $this->TOLIST           = $MAIL_DATA['TODATA'];
        $this->CCLILST          = $MAIL_DATA['CCDATA'];
        $this->BCCLIST          = $MAIL_DATA['BCCDATA'];
        $this->from_name        = $MAIL_DATA['from_name'];
        $this->from_address     = $MAIL_DATA['from_add'];
        $this->reply_to_name    = $MAIL_DATA['repry_name'];
        $this->reply_to_address = $MAIL_DATA['repry_to'];
        $this->return_path      = $MAIL['return_path'];
        $this->add_header       = $MAIL['add_mail_header'];
        $this->subject          = $MAIL['subject'];
        $this->body             = $MAIL['mail_body'];
        # ﾒｰﾙ送信
        $success = $this->smtp_mail();
      } else {
        # docomoのdecome crlfはダメなので強制的にlfに変換
        if ($SETTING_DATA['to_career'] === "DoCoMo") {
            $MAIL['mail_body'] = preg_replace('/\r\n/',"\n",$MAIL['mail_body']);
            $MAIL['add_mail_header'] = preg_replace('/\r\n/',"\n",$MAIL['add_mail_header']);
        }
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
        return 1;
      }
    } else {
      $this->error_flag   = True;
#      $this->error_coment = 'Mail Data Make Error.';
      return 2;
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
#    if (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $this->softbank_name)) { $SETTING_DATA['decome_mode'] = ''; }

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
      if (isset($this->cont_trs_enc)) {
        if ($this->cont_trs_enc == '') {
          $SETTING_DATA['content_transfer_encoding'] = $this->cont_trs_enc;
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
          $str_code   = mb_detect_encoding($namedt,$this->ENCODINGLIST[$this->chr_code]);
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
          $str_code   = mb_detect_encoding($namedt,$this->ENCODINGLIST[$this->chr_code]);
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
          $str_code    = mb_detect_encoding($namedt,$this->ENCODINGLIST[$this->chr_code]);
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
      $str_code = mb_detect_encoding($MAIL_DATA['from_name'],$this->ENCODINGLIST[$this->chr_code]);
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
      $str_code = mb_detect_encoding($MAIL_DATA['repry_name'],$this->ENCODINGLIST[$this->chr_code]);
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
      $MAIL_DATA['subject']    = $this->emj_encode($MAIL_DATA['subject']   ,'','',$SETTING_DATA['input_code']);
      $MAIL_DATA['body_plain'] = $this->emj_encode($MAIL_DATA['body_plain'],'','',$SETTING_DATA['input_code']);
      $MAIL_DATA['body_html']  = $this->emj_encode($MAIL_DATA['body_html'] ,'','',$SETTING_DATA['input_code']);
    }

    # 文字ｺｰﾄﾞ取得
    $subject_code    = mb_detect_encoding($MAIL_DATA['subject']   ,$this->ENCODINGLIST[$this->chr_code]);
    $body_plain_code = mb_detect_encoding($MAIL_DATA['body_plain'],$this->ENCODINGLIST[$this->chr_code]);
    $body_html_code  = mb_detect_encoding($MAIL_DATA['body_html'] ,$this->ENCODINGLIST[$this->chr_code]);
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
      $MAIL_DATA['subject'] = $this->delete_emoji_code($MAIL_DATA['subject']);
    } elseif (($SETTING_DATA['to_career'] == 'Vodafone') or ($SETTING_DATA['to_career'] == 'SoftBank')) {
      # SoftBank3G向けの場合(絵文字削除)
      $MAIL_DATA['subject'] = $this->delete_emoji_code($MAIL_DATA['subject']);
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $SUBJECT = $this->emj_decode($MAIL_DATA['subject'],$SETTING_DATA['to_career'],$SETTING_DATA['mail_code']);
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
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $this->softbank_name)) {
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
    $add_mail_header .= "From: ".$set_form."\r\n";
    $add_mail_header .= "Reply-To: ".$set_repry_to."\r\n";
    if ($set_cc  != '') { $add_mail_header .= "Cc: ".$set_cc."\r\n"; }
    if ($set_bcc != '') { $add_mail_header .= "Bcc: ".$set_bcc."\r\n"; }
    $add_mail_header .= "MIME-Version: 1.0\r\n";

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
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $this->softbank_name)) {
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
    if (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $this->softbank_name)) { $SETTING_DATA['decome_mode'] = ''; }

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
      if (isset($this->cont_trs_enc)) {
        if ($this->cont_trs_enc == '') {
          $SETTING_DATA['content_transfer_encoding'] = $this->cont_trs_enc;
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
    $subject_code    = mb_detect_encoding($MAIL_DATA['subject']   ,$this->ENCODINGLIST[$this->chr_code]);
    $body_plain_code = mb_detect_encoding($MAIL_DATA['body_plain'],$this->ENCODINGLIST[$this->chr_code]);
    $body_html_code  = mb_detect_encoding($MAIL_DATA['body_html'] ,$this->ENCODINGLIST[$this->chr_code]);
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
      $MAIL_DATA['subject'] = $this->delete_emoji_code($MAIL_DATA['subject']);
    } elseif (($SETTING_DATA['to_career'] == 'Vodafone') or ($SETTING_DATA['to_career'] == 'SoftBank')) {
      # SoftBank宛ての場合(絵文字削除)
      $MAIL_DATA['subject'] = $this->delete_emoji_code($MAIL_DATA['subject']);
    } else {
      # 各ｷｬﾘｱ向け(絵文字ﾃﾞｺｰﾄﾞ)
      $SUBJECT = $this->emj_decode($MAIL_DATA['subject'],$SETTING_DATA['to_career'],$SETTING_DATA['mail_code']);
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
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $this->softbank_name)) {
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
    } elseif (($SETTING_DATA['to_career'] == 'SoftBank') or ($SETTING_DATA['to_career'] == $this->softbank_name)) {
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
        $PLCOUNT = $this->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # 絵文字が含まれている場合(ﾃｷｽﾄ→HTML本文)
          $body_html  = $body_plain;
          # URL･ﾒｰﾙｱﾄﾞﾚｽﾘﾝｸ化
          $body_html = $this->link_make($body_html);
          # 絵文字削除
          $body_plain = $this->delete_emoji_code($body_plain);
#          if ($upfile_flag == True) { $RETURN['ptn_no'] = 9; } else { $RETURN['ptn_no'] = 8; }
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } else { $RETURN['ptn_no'] = 7; }
          $plain_flag = '1';
        } else {
          # 絵文字が含まれていない場合
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 2; } else { $RETURN['ptn_no'] = 1; }
        }
      } elseif (($body_plain != '') and ($body_html != '')) {
        # ﾃｷｽﾄ + HTML
        $PLCOUNT = $this->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # ﾃｷｽﾄ本文に絵文字が含まれている場合(絵文字ｶｯﾄ)
          $body_plain = $this->delete_emoji_code($body_plain);
        }
        if ($decome_mode == '1') {
          # ﾃﾞｺﾒﾓｰﾄﾞ
          $HTCOUNT = $this->emj_check($body_html,'',$input_code);
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
        $body_plain = preg_replace('|<br\s*/*>|i',"\r\n",$body_plain);
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
        $body_plain = preg_replace('|<br\s*/*>|i',"\r\n",$body_plain);
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

    } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
      # SoftBank宛て
      if (($body_plain == '') and ($body_html != '')) {
        # HTMLのみ
        # ﾃｷｽﾄ本文設定
        $body_plain = strip_tags($body_html,'<br>');
        $body_plain = preg_replace('|<br\s*/*>|i',"\r\n",$body_plain);
        # HTML内画像数ﾁｪｯｸ
        if ($decome_mode == '1') { $img_num = preg_match('/<img\s/i',$body_html); } else { $img_num = 0; }
#        $PLCOUNT = $this->emj_check($body_plain,'',$input_code);
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
        $PLCOUNT = $this->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # 絵文字が含まれている場合(ﾃｷｽﾄ→HTML本文)
          $body_html = $body_plain;
          # URL･ﾒｰﾙｱﾄﾞﾚｽﾘﾝｸ化
          $body_html = $this->link_make($body_html);
          # ﾃｷｽﾄﾊﾟｰﾄ絵文字削除
          $body_plain = $this->delete_emoji_code($body_plain);
#          if ($upfile_flag == True) { $RETURN['ptn_no'] = 9; } else { $RETURN['ptn_no'] = 8; }
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 3; } else { $RETURN['ptn_no'] = 4; }
          $plain_flag = '1';
        } else {
          # 絵文字が含まれていない場合
          if ($upfile_flag == True) { $RETURN['ptn_no'] = 2; } else { $RETURN['ptn_no'] = 1; }
        }
      } elseif (($body_plain != '') and ($body_html != '')) {
        # ﾃｷｽﾄ + HTML
        $PLCOUNT = $this->emj_check($body_plain,'',$input_code);
        if ($PLCOUNT['total'] > 0) {
          # ﾃｷｽﾄ本文に絵文字が含まれている場合(絵文字ｶｯﾄ)
          $body_plain = $this->delete_emoji_code($body_plain);
        }
        if ($decome_mode == '1') {
          # ﾃﾞｺﾒﾓｰﾄﾞ
          $HTCOUNT = $this->emj_check($body_html,'',$input_code);
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
    if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
      $BODYPLAIN  = $this->emj_decode($body_plain,$to_career,$mail_code);
      $body_plain = $BODYPLAIN['mail_plain'];
      $body_html  = mb_convert_encoding($body_html,'SJIS','JIS');
      $BODYHTML   = $this->emj_decode($body_html,$to_career,'UTF-8');
    } else {
      $BODYPLAIN  = $this->emj_decode($body_plain,$to_career,$mail_code);
      $body_plain = $BODYPLAIN['mail'];
      $BODYHTML   = $this->emj_decode($body_html,$to_career,$mail_code);
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

    $this->file_error_flag   = False;
    $this->file_error_code   = 0;
    $this->file_error_coment = '';

    $INLINE_IMGLIST = array();
    $body_html      = preg_replace('/\r/','',$body_html);
    $body_html_sub  = $body_html;
    $no             = 0;

    # <IMG>ﾀｸﾞ内画像取得
    while (preg_match('|(<img\s+src\s*=[\s\"\']*)(.+?)([\"\'\s>])|i',$body_html_sub,$MATCH)) {

		# TAKAI
      # ﾌｧｲﾙ読込み
      if ($filedata = @file($MATCH[2])) {

        $fdata = join('',$filedata);
        # CID設定
        $cid = 'img_cid_'.str_pad($no,3,'0',STR_PAD_LEFT).'@'.date('ymd.His',time());
        # ﾃﾞｰﾀ取得
        $PATHDATA = pathinfo($MATCH[2]);
        $INLINE_IMGLIST[$cid]['name'] = $PATHDATA['basename'];
        #if ((isset($this) and is_object($this)) and method_exists($this,'get_mime_type')) {
        if (method_exists($this,'get_mime_type')) {
          # ver.8用
          $INLINE_IMGLIST[$cid]['mime'] = $this->get_mime_type($MATCH[2]);
        } else {
          # ver.7用
          $INLINE_IMGLIST[$cid]['mime'] = $this->get_mime_type($MATCH[2]);
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
        #if ((isset($this) and is_object($this)) and method_exists($this,'get_mime_type')) {
        if (method_exists($this,'get_mime_type')) {
          # ver.8用
          $INLINE_IMGLIST[$cid]['mime'] = $this->get_mime_type($MATCH[2]);
        } else {
          # ver.7用
          $INLINE_IMGLIST[$cid]['mime'] = $this->get_mime_type($MATCH[2]);
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
              #if ((isset($this) and is_object($this)) and method_exists($this,'get_mime_type')) {
              if (method_exists($this,'get_mime_type')) {
                # ver.8用
                $UPFILELIST[$no]['mime'] = $this->get_mime_type($pathdt);
              } else {
                # ver.7用
                $UPFILELIST[$no]['mime'] = $this->get_mime_type($pathdt);
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
    if (($body_plain != '') and !preg_match('/\r\n$/',$body_plain)) { $body_plain .= "\r\n"; }

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

    # 元ﾃﾞｰﾀ準備
    $body_html = preg_replace('/\r/','',$body_html);

    # 元ﾃﾞｰﾀがﾃｷｽﾄの場合
    if ($body_plain_flag == '1') {
      $body_html = preg_replace('/\r\n/','<br />',$body_html);
      $body_html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-2022-jp\"></head><body>".$body_html."</body></html>";
    }

    # HTMLﾍｯﾀﾞｰﾁｪｯｸ
    if (!preg_match('/<html>.+<\/html>/',$body_html)) {
      if (preg_match('/<body.+<\/body>/',$body_html)) {
        if (preg_match('/<head>.+<\/head>/',$body_html)) {
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
    if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
      # SoftBankの場合強制UTF-8変換
      if (preg_match('/<meta\s[^>]+content\s*=\s*\"[^>]+\scharset=([^>]+)\"[^>]*>/i',$body_html,$MATCH)) {
        # 文字ｺｰﾄﾞ指定<META>ﾀｸﾞが含まれている場合
        $body_html = preg_replace('/(<meta\s[^>]+content\s*=\s*\"[^>]+\scharset=)([^>]+)(\"[^>]*>)/i','\\1UTF-8\\3',$body_html);
      } else {
        # 文字ｺｰﾄﾞ指定<META>ﾀｸﾞが含まれていない場合
        $body_html = str_replace('<head>','<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">',$body_html);
      }
      $mcode = 'UTF-8';
      $body_html_code = mb_detect_encoding($body_html,$this->ENCODINGLIST[$this->chr_code]);
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
      $TDATA  = explode("\r\n",$body_html);
      $TDATAS = array();
      foreach ($TDATA as $tdt) {
        $TDATAS[] = preg_replace('/^\s*/','',$tdt);
      }
      $body_html = join("\r\n",$TDATAS);
      # 改行削除
      $body_html = preg_replace('/[\r\n]/','',$body_html);
    }

    # ﾃﾞｰﾀ末改行処理
    if (($body_html != '') and preg_match('/\r\n$/',$body_html)) { $body_html = preg_replace('/\r\n$/','',$body_html); }

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

    $mail_header_ptn = '';
    $mail_body       = '';
    # 本文ｴﾝｺｰﾄﾞ
    if ($content_transfer_encoding == 'base64') {
      # Base64ｴﾝｺｰﾄﾞ
      $body_plain = chunk_split(base64_encode($body_plain));
      $body_html  = chunk_split(base64_encode($body_html));
    } elseif ($content_transfer_encoding == 'quoted_printable') {
      # Quoted_Printableｴﾝｺｰﾄﾞ
      $body_plain = $this->quoted_printable_encodee($body_plain);
      $body_html  = $this->quoted_printable_encodee($body_html);
    } else {
      # 指定無し(7bit)
      if ($to_career == 'PC') {
        # PC宛ての場合
      } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        # SoftBank携帯宛ての場合
#        $content_transfer_encoding = 'quoted_printable';
#        $body_plain = $this->quoted_printable_encodee($body_plain);
        $content_transfer_encoding = '7bit';
        $body_html = $this->quoted_printable_encodee($body_html);
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
      } elseif (($to_career == 'SoftBank') or  ($to_career == $this->softbank_name)) {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_5_sb($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career);
      } else {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_5($body_plain,$body_html,$INLINEFILE,$content_transfer_encoding,$to_career);
      }
    } elseif ($mail_ptn == '6') {
      # ﾃｷｽﾄ + HTML + ｲﾝﾗｲﾝ画像 + ﾌｧｲﾙ添付(携帯用)
      if ($to_career == 'au') {
        list($mail_header_ptn,$mail_body) = $this->_mail_ptn_6_au($body_plain,$body_html,$INLINEFILE,$UPFILE,$content_transfer_encoding,$to_career);
      } elseif (($to_career == 'SoftBank') or  ($to_career == $this->softbank_name)) {
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
    $mail_header_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
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
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # 添付ﾌｧｲﾙﾊﾟｰﾄ設定
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\r\n";
    if ($to_career == 'PC') {
      # PC宛て
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    } else {
      # 携帯宛て
      if ($content_transfer_encoding == '7bit') {
        if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
          $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
          $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
        } else {
          $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
          $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
        }
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_2}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_2}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\r\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
#    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
    $mail_header_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\r\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
#    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_2}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\r\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
#    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_2}--\r\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\r\n";
    $mail_ptn .= "--{$boundary_2}--\r\n";

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

    # ﾊﾞｳﾝﾀﾞﾘｰ設定
    $boundary_1 = md5(uniqid(rand()));
    $boundary_2 = md5(uniqid(rand()));
    $boundary_3 = md5(uniqid(rand()));

    # ﾒｰﾙ追加ﾍｯﾀﾞｰ設定
    $mail_header_ptn  = '';
#    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
    $mail_header_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
#    $mail_ptn .= "--{$boundary_1}\r\n";
#    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
#    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_3}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    if ($content_transfer_encoding == '7bit') {
      if (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
        $mail_ptn .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: quoted-printable\r\n";
      } else {
        $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
        $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
      }
    } else {
      $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
      $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    }
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_2}--\r\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
#    $mail_ptn .= "--{$boundary_1}--\r\n";
    $mail_ptn .= "--{$boundary_2}--\r\n";

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
    $mail_header_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--".$boundary."\r\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\r\n";

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
    $mail_header_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_1}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\r\n";

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
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_2}\"\r\n";
    $mail_ptn .= "\r\n";
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ2設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_3}\"\r\n";
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_3}\r\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_3);
    # ﾊﾟｰﾄ3終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\r\n";

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
    $mail_header_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_header_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    $mail_ptn .= $body_html."\r\n";

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
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\r\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\r\n";

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
    $mail_header_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary}\r\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary);
    # ﾊﾟｰﾄ終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary}--\r\n";

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
    $mail_header_ptn .= "Content-Type: multipart/mixed; boundary=\"{$boundary_1}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # ｲﾝﾗｲﾝ画像ﾊﾟｰﾄ設定
    $mail_ptn .= $this->_inlinefile($INLINEFILE,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_3}--\r\n";
    # 添付ﾌｧｲﾙ追加
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_1);
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\r\n";

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
    $mail_header_ptn .= "Content-Type: multipart/alternative; boundary=\"{$boundary_1}\"\r\n";
#    $mail_header_ptn .= "This is a multi-part message in MIME format.";

    # ﾒｰﾙ本文設定
    $mail_ptn  = '';
    # ﾃｷｽﾄ本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_plain;
    $mail_ptn .= "\r\n";
    # ﾏﾙﾁﾊﾟｰﾄﾍｯﾀﾞｰ1設定
    $mail_ptn .= "--{$boundary_1}\r\n";
    $mail_ptn .= "Content-Type: multipart/related; boundary=\"{$boundary_2}\"\r\n";
    $mail_ptn .= "\r\n";
    # HTML本文ﾊﾟｰﾄ設定
    $mail_ptn .= "--{$boundary_2}\r\n";
    $mail_ptn .= "Content-Type: text/html; charset=\"ISO-2022-JP\"\r\n";
    $mail_ptn .= "Content-Transfer-Encoding: {$content_transfer_encoding}\r\n";
    $mail_ptn .= "\r\n";
    $mail_ptn .= $body_html."\r\n";
    $mail_ptn .= "\r\n";
    # 添付ﾌｧｲﾙﾊﾟｰﾄ設定
    $mail_ptn .= $this->_addfile($UPFILELIST,$boundary_2);
    # ﾊﾟｰﾄ2終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_2}--\r\n";
    # ﾊﾟｰﾄ1終了ﾊﾞｳﾝﾀﾞﾘｰ
    $mail_ptn .= "--{$boundary_1}--\r\n";

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
      $inlinefile_part .= "--{$boundary}\r\n";
      $inlinefile_part .= "Content-Type: {$IDT['mime']};\r\n";
      $inlinefile_part .= "\tname=\"{$IDT['name']}\"\r\n";
      $inlinefile_part .= "Content-Transfer-Encoding: base64\r\n";
      $inlinefile_part .= "Content-ID: <{$kdt}>\r\n";
      $inlinefile_part .= "\r\n";
#      $inlinefile_part .= $IDT['data']."\r\n";
      $inlinefile_part .= $IDT['data'];
      $inlinefile_part .= "\r\n";
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
      $addfile_part .= "--{$boundary}\r\n";
      $addfile_part .= "Content-Type: {$UDT['mime']};\r\n";
      $addfile_part .= "\tname=\"{$UDT['basename']}\"\r\n";
      $addfile_part .= "Content-Transfer-Encoding: base64\r\n";
      $addfile_part .= "Content-Disposition: attachment;\r\n";
      $addfile_part .= "\tfilename=\"{$UDT['basename']}\"\r\n\r\n";
#      $addfile_part .= $UDT['filedata']."\r\n";
      $addfile_part .= $UDT['filedata'];
      $addfile_part .= "\r\n";
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
      } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
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
    } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
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
      } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
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
    } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
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
    } elseif (($to_career == 'SoftBank') or ($to_career == $this->softbank_name)) {
      # SoftBank用添付ﾌｧｲﾙﾁｪｯｸ
      # 添付ﾌｧｲﾙ数ﾁｪｯｸ
      if (($this->allfile_max_num_softbank > 0) and ($this->allfile_max_num_softbank < $total_file_num)) { $check_flag = False; }
      # 添付ﾌｧｲﾙｻｲｽﾞﾁｪｯｸ
      if (($this->allfile_max_size_softbank > 0) and ($this->allfile_max_size_softbank < $total_size)) { $check_flag = False; }
    }
    return $check_flag;
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
    $crlf   = "\r\n" ;
    $string = preg_replace('!(\r\n|\r|\r\n)!', $crlf, $string) . $crlf ;
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


















  # smtp_class 移植 //////////////////////////////////////////////////
  # 　$this->へ変更
  #////////////////////////////////////////////////////////////////////////////


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
          $result = fgets($this->smtp_res,128);

          # Auth認証(LOGIN)

          if (($this->auth == True) and ($this->auth_type == 'LOGIN')) {

            # 認証確認
            fputs($this->smtp_res,"AUTH LOGIN\r\n");
            $result = fgets($this->smtp_res,128);
            if(!preg_match("/^334/",$result)){
              $this->smtp_disconnect();
              return False;
            }
            # ﾕｰｻﾞｰID認証
            fputs($this->smtp_res,base64_encode($this->mail_user)."\r\n");
            $result = fgets($this->smtp_res,128);

            if(!preg_match("/^334/",$result)){
              $this->smtp_disconnect();
              return False;
            }
            # ﾊﾟｽﾜｰﾄﾞ認証
            fputs($this->smtp_res,base64_encode($this->mail_pass)."\r\n");
            $result = fgets($this->smtp_res,128);

            if(!preg_match("/^334/",$result)){
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
    fclose($this->smtp_res);
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
		mail("takai@d-ef.co.jp","False1","MAIL\n\n");
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

#$taddress = "=?ISO-2022-JP?B?ZGVmX243MDZpMkBkb2NvbW8ubmUuanA=?=";

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
	  $to_count	= 0;
      foreach ($this->TOLIST as $to_address => $to_name) {

		# $this->TOLISTが複数あったら強制終了
		$to_count++;
		if($to_count > 1){
			mail("system@d-ef.co.jp","SMTP MAIL ERROR","mobile_class_8.php\nLINE:7143\TOLIST : OVER");
			exit();
		}

/*
pr($this->add_header);
pr($to_address);
pr($this->return_path);
pr($this->subject);
pr($faddress);
pr($rpaddress);
pr($this->body);
*/

        fputs($this->smtp_res,'MAIL FROM:'.$this->return_path.$this->crlf);
        fputs($this->smtp_res,'RCPT TO:'.$to_address.$this->crlf);
        fputs($this->smtp_res,'DATA'.$this->crlf);
        fputs($this->smtp_res,'Subject: '.$this->subject.$this->crlf);
        # 2010/07/04
        #fputs($this->smtp_res,'From: '.$faddress.$this->crlf);
        if ($rpaddress != '') {
          #fputs($this->smtp_res,'Reply-To: '.$rpaddress.$this->crlf);
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
        $result = fgets($this->smtp_res,128);
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
      $result = fgets($this->smtp_res,128);
      # SMTP接続切断
      $this->smtp_disconnect();
      #fclose($this->smtp_res); //ソケット閉じる
/*
      $this->TOLIST = "";
      $this->from_address = "";
      $this->reply_to_address = "";
*/
        $this->TOLIST[$to_name] = "";
        $this->TOLIST = "";
        $this->CCDATA           = "";
        $this->BCCDATA          = "";
        $this->from_name        = "";
        $this->from_address     = "";
        $this->reply_to_name    = "";
        $this->reply_to_address = "";
        $this->return_path      = "";
        $this->add_header       = "";
        $this->subject          = "";
        $this->body             = "";
    } else {
        $this->TOLIST[$to_name] = "";
        $this->TOLIST = "";
        $this->CCDATA           = "";
        $this->BCCDATA          = "";
        $this->from_name        = "";
        $this->from_address     = "";
        $this->reply_to_name    = "";
        $this->reply_to_address = "";
        $this->return_path      = "";
        $this->add_header       = "";
        $this->subject          = "";
        $this->body             = "";

		mail("system@d-ef.co.jp","SMTP MAIL ERROR","mobile_class_8.php\nLINE:7254\nrelay ip : NO CONNECT");
		exit();

    #mail("yamamoto@d-ef.co.jp","return False",$this->TOLIST."\n\n".$this->TOLIST[$to_name]);
      return False;
    }
   # mail("yamamoto@d-ef.co.jp","return ture",$this->TOLIST."\n\n".$this->TOLIST[$to_name]);
    return True;
  }


}
