本ﾌｧｲﾙは基本表示用のﾃﾝﾌﾟﾚｰﾄﾌｧｲﾙです。文字ｺｰﾄﾞは"Shift-JIS"で記述してください。

■PC用の著作権表示用ﾃﾝﾌﾟﾚｰﾄです。
[copyright_pc_tmpl]------------------------------------------------------------
<div align="right" style="font-size:8pt; color:#aaaaaa">###copyright_pc###</div><br>
-------------------------------------------------------------------------------

■携帯用の著作権表示用ﾃﾝﾌﾟﾚｰﾄです。
[copyright_mobile_tmpl]--------------------------------------------------------
<div align="right" color="#aaaaaa">###copyright_mobile###</div><br>
-------------------------------------------------------------------------------

■PC用のｴﾗｰの簡易表示用ﾃﾝﾌﾟﾚｰﾄです。
[simple_error_vew_pc_tmpl]-----------------------------------------------------
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=###script_code###">
    <title>Error</title>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
    <center><font color="#ff0000"><b>Error</b></font></center>
    <hr size="1">
    <br>
    <center>
      ###err_mess###<br><br>
      ブラウザの"戻る"で戻ってください。<br><br>
    </center>
    <hr size="1">
    ###copyright_pc###
  </body>
</html>
-------------------------------------------------------------------------------

■携帯用のｴﾗｰの簡易表示用ﾃﾝﾌﾟﾚｰﾄです。
[simple_error_vew_mobile_tmpl]-------------------------------------------------
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=###script_code###">
<title>Error</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body><center>
<font color="#ff0000"><b>Error</b></font><br>
<hr size="1">
###err_mess###<br><br>
"戻る"で戻ってください。<br><br><hr>
###copyright_mobile###
</body></html>
-------------------------------------------------------------------------------

■PC用のｴﾗｰの表示用ﾃﾝﾌﾟﾚｰﾄです。(引渡し値の制御有り)
[error_vew_sorce_pc_tmpl]------------------------------------------------------
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=###script_code###">
    <title>Error</title>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
    <center>
      <font style="font-size:14px; font-weight:bold; color:#ff0000">Error</font><br>
    </center>
    <hr size="1">
    <br>
    ###err_mess###<br><br>
    <hr size="1">
    <form name="form00" action="###back_url###" method="POST">
      ###hidden_list###
      <center><input type="submit" value="戻る"></center>
    </form>
    <hr size="1">
    ###copyright_pc###
  </body>
</html>
-------------------------------------------------------------------------------

■携帯用のｴﾗｰの表示用ﾃﾝﾌﾟﾚｰﾄです。(引渡し値の制御有り)
[error_vew_sorce_mobile_tmpl]--------------------------------------------------
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=###script_code###">
<title>Error</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body><center><font color="#ff0000\"><b>Error</b></font></center>
<hr size="1">
###err_mess###<br><br>
<hr size="1">
<form name="form00" action="###back_url###" method="POST">
###hidden_list###
<center><input type="submit" value="戻る"></center>
</form>
<hr size="1">
###copyright_mobile###
</body></html>
-------------------------------------------------------------------------------

■PC用のﾍﾟｰｼﾞ表示用ﾍｯﾀﾞｰﾃﾝﾌﾟﾚｰﾄです。
[header_pc_tmpl]---------------------------------------------------------------
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=###script_code###">
    <title>###titlebar_title###</title>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
    <center>
      <font style="font-size:14px; font-weight:bold">###pagetop_title###</font>
    </center>
    <hr size="1">
-------------------------------------------------------------------------------

■携帯用のﾍﾟｰｼﾞ表示用ﾍｯﾀﾞｰﾃﾝﾌﾟﾚｰﾄです。
[header_mobaile_tmpl]----------------------------------------------------------
<html><head>
<meta http-equiv="Content-Type" content="text/html;charset=###script_code###">
<title>###titlebar_title###</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<center><b>###pagetop_title###</b></center><hr size="1">
-------------------------------------------------------------------------------

■PC用のﾍﾟｰｼﾞ表示用ﾌｯﾀｰﾃﾝﾌﾟﾚｰﾄです。
[footer_pc_tmpl]---------------------------------------------------------------
    <hr size="1">
    <center>
      <input type="button" value="戻る" onClick="location.href='###back_url###'">
    </center>
    <hr size="1">
    ###copyright_pc###
  </body>
</html>
-------------------------------------------------------------------------------

■携帯用のﾍﾟｰｼﾞ表示用ﾌｯﾀｰﾃﾝﾌﾟﾚｰﾄです。
[footer_mobile_tmpl]-----------------------------------------------------------
<hr size="1">
<center>[<a href="###back_url###">戻る</a>]</center>
<hr size="1">
###copyright_mobile###
</body></html>
-------------------------------------------------------------------------------

