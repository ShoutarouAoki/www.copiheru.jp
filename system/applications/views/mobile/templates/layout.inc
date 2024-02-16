<?php
/************************************************
**
**	INCLUED HTML HEADER
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
include_once(dirname(__FILE__)."/html_header.inc");
?>


<?php
/************************************************
**
**	WRAPPER
**	---------------------------------------------
**	TABLE WIDTH 96%
**
************************************************/
?>
<div style="<?php print($font_size.$preview_setting); ?>">


<div style="text-align: center;">
<img src="/images/mobile/header.gif" border="0" />
</div>



<?php
/************************************************
**
**	INCLUED CONTENTS COLUMN
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
?>
<div id="contents">
<?php include_once($view_directory); ?>
</div>

<div style="text-align: right;">
<a href="#">▲上へ戻る</a>
</div>
<?php if($directory != "index"){ ?>
<?php print($hr); ?>
<div style="text-align: right;">
<img src="/images/emoji/docomo/38.gif" /><a href="/">ホーム</a>
</div>
<? } ?>
<?php print($spacer); ?>


<?php
/************************************************
**
**	INCLUED FOOTER
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
?>

<div style="<?php print($footer_background); ?><?php print($footer_color); ?><?php print($font_small); ?> text-align:center;">
<?php include_once(dirname(__FILE__)."/footer.inc"); ?>
</div>

</div>
