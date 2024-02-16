<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<? print(SITE_CHARSET); ?>" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<title><?php print(HTML_TITLE); ?></title>
<? if(defined("ADMIN_CHECK")){ ?>
<link href="/common/css/debug.css" rel="stylesheet" type="text/css" />
<? } ?>
<style type="text/css">
<!--
<?php if($access_check == "pc"){ ?>
body{
	font-family: "ＭＳ Ｐゴシック", Arial, "ヒラギノ角ゴ Pro W3";
}
div{
	white-space: -moz-pre-wrap; /* Mozilla */
	word-wrap: break-word;      /* IE 5.5+ */
	word-break: break-all;
	margin: 0;
	padding: 0;
}
<?php } ?>
a:link    { color:<?php print($link_color); ?>;}
a:visited { color:<?php print($link_visited); ?>;}
a:focus   { color:<?php print($link_hover); ?>;}
-->
</style>
</head>

<body style="<?php print($body_background); ?><?php print($text_color); ?> margin: 0px;">
