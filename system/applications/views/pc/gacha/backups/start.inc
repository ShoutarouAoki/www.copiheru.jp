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
		<div class="image-center"><a href="/gacha/"><img src="/images/smart/button/button-return-top.png" style="width: 250px; height: 60px;" /></a></div>
	</div>


<?php
/********************************
**
**	ガチャスタート
**
*********************************/
}else{
?>

	<script src="/plugins/jqfloat/jqfloat.js" type="text/javascript"></script>
	<script src="/plugins/jQueryRotate/jQueryRotate.js" type="text/javascript"></script>

	<script>
	$(document).ready(function() {

	   $("#gacha-start-button img").jqFloat({
		    width: 10,
		    height: 50,
		    speed: 1000
	    });

		$("#gacha-start-button").click(function() {
			rotation();
			$("body").fadeOut("slow");
			setTimeout(function(){
				window.location.href = "/gacha/lottery/<?php print($data['type']); ?>/<?php print($data['pay']); ?>/<?php print($data['campaign_id']); ?>/";
			}, 1800); 
		});

		var rotation = function (){　/* 早い回転を繰り返す */
		  $("#start").rotate({
		    angle:0,
		    animateTo:1440,
		    callback: rotation
		  });
		}

	});

	</script>

	<div id="gacha-start">
		<div id="gacha-start-button"><img src="/images/smart/button/button-gachastart.png" id="start" /></div>s
	</div>

<?php } ?>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>