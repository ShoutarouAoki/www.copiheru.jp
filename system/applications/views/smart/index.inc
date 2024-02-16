
<?php if(!empty($ie_check)){ ?>
	<div id="ie-check"><a href="/guide/browser/ie/">IEをお使いの方は正常にゲームをお楽しみできない場合が御座いますのでこちらをお読み下さい。</a></div>
<?php } ?>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php
	/****************************
	**
	**	このページのjavascript処理ファイル
	**
	*****************************/
	?>

	<script src="/common/js/index.script.js" type="text/javascript" ></script>
	<script src="/plugins/jqfloat/jqfloat.js" type="text/javascript"></script>

	<div id="index-area">
		<?php if(!empty($image_data['img_name'])){ ?>
			<img src="<?php print(HTTP_WEB_IMAGE); ?>/<?php print($image_data['img_name']); ?>" />
		<?php }else{ ?>
			<img src="/images/smart/enter.png" />
		<?php } ?>
		<div id="index-enter-button"><img src="/images/smart/button/button-enter.png" id="enter" /></div>
	</div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

<?php
/***********************
**
**	cookie書き込み
**
************************/
if($default_device == "smart"){
?>
<script type="text/javascript">
	//SetCookie("http://www.kyabaheru.net/");
	//SetCookie("https://kyabaheru.higatest.com/");
	SetCookie("https://copiheru.higatest.com/");
</script>
<?php } ?>