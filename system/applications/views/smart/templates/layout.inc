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
**	LOADING / SCREEN OVERLAY
**
************************************************/
?>

<?php /* LOADING */ ?>
<div id="loading"><img src="/images/loading.gif" /></div>
<?php /* LOADING */ ?>

<?php /* MAIL PAGE SCREEN */ ?>
<div id="screen-overlay">
	<div class="controll-button">
		<a href="javascript:void(0)" class="mail-frame-zoomout"><img src="/images/smart/button/button-zoomout.png" class="button-zoomout" /></a>
		<div class="clear"></div>
	</div>
</div>
<?php /* MAIL PAGE SCREEN */ ?>

<?php /* MODAL OVERLAY SCREEN */ ?>
<div id="modal-overlay"></div>
<?php /* MODAL OVERLAY SCREEN */ ?>

<?php /* DEFAULT DIALOG */ ?>
<div id="dialog-box"></div>
<?php /* DEFAULT DIALOG */ ?>

<?php /* ERROR DIALOG */ ?>
<div id="dialog-error"></div>
<?php /* ERROR DIALOG */ ?>

<div id="top"></div>

<?php
/************************************************
**
**	WRAPPER
**
************************************************/
?>
<?php if($default_device == "pc"){  ?>
<div id="preview-frame">
<?php } ?>


<div id="wrapper">

<?php /* START FADE SCREEN */ ?>
<div id="screen"></div>
<?php /* START FADE SCREEN */ ?>


<?php
/************************************************
**
**	HEADER
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
?>
<?php include_once(dirname(__FILE__)."/header.inc"); ?>


<?php
/************************************************
**
**	INCLUED CONTENTS COLUMN
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
include_once($view_directory);
?>


<?php
/************************************************
**
**	INCLUED FOOTER
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
?>
<?php include_once(dirname(__FILE__)."/footer.inc"); ?>


</div>
<?php /*** WRAPPER END ***/ ?>


<?php if($default_device == "pc"){  ?>
</div>
<?php } ?>

<script>
gadgets.window.adjustHeight();
</script>
