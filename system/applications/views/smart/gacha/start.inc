<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

<?php
/********************************
**
**	エラー
**
*********************************/
if(!empty($error)){
?>

	<div class="title">ガチャエラー</div>

	<div class="container-box">
		<div id="warning">
			<?php print($errormessage); ?>
		</div>
		<?php if($error == 11){ ?>
			<div class="image-center"><a href="/<?php print($directory); ?>/<?php print($data['page']); ?>/<?php print($data['type']); ?>/<?php print($data['pay']); ?>/<?php print($data['campaign_id']); ?>/<?php print($params); ?>"><img src="/images/smart/button/button-reload.png" style="width: 250px; height: 60px;" /></a></div><br /><br />
		<?php } ?>

		<div class="image-center"><a href="/gacha/"><img src="/images/smart/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>
	</div>


<?php
/********************************
**
**	ガチャスタート
**
*********************************/
}else{
?>

	<?php
	/****************************
	**
	**	このページのjavascript処理ファイル
	**
	*****************************/
	?>

	<script src="/common/js/gacha.script.js" type="text/javascript" ></script>
	<script src="/common/js/gacha.function.js" type="text/javascript" ></script>
	<script src="/plugins/jqfloat/jqfloat.js" type="text/javascript"></script>
	<script src="/plugins/jQueryRotate/jQueryRotate.js" type="text/javascript"></script>

	<script>
	$(document).ready(function() {

		$("#gacha-start-button").click(function() {
			var	type		= "<?php print($data['type']); ?>";
			var	pay			= "<?php print($data['pay']); ?>";
			var	cid			= <?php print($data['campaign_id']); ?>;
			var pid			= <?php print($pays_id); ?>;
			gachaLottery(type,pay,cid,pid);
		});

	});

	</script>

	<div id="gacha-area">
		<div id="gacha-start-button"><img src="/images/smart/button/button-gachastart.png" id="start" /></div>
		<div id="gacha-result"></div>
	</div>


<?php } ?>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>