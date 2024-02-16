<?php
/****************************
**
**	このページでのスクリプト
**
*****************************/
?>
<script src="/common/js/tutorial.script.js"></script>
<script src="/common/js/tutorial.function.js"></script>

<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link rel="stylesheet" href="/common/css/pc/tutorial.css" />
<link rel="stylesheet" href="/common/css/pc/index.css" />


<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

<?php
/******************************
**
**	ERROR
**
*******************************/
if(!empty($error)){
?>

	<div class="title">エラー</div>

	<div class="container-box">
		<div id="warning">
			<?php print($errormessage); ?>
		</div>
		<div class="image-center"><a href="/tutorial/"><img src="/images/pc/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>
	</div>

<?php
}else{
?>

	<script src="/common/js/shop.script.js" type="text/javascript" ></script>
	<link href="/plugins/animate/animate.css" rel="stylesheet" />
	<div id="tutorial-result">
		<section id="tutorial-result-area">
			<div id="tutorial-result-box">
				<div class="tutorial-result-title"><img src="/images/pc/title/title-game-start.png" /></div>
				<div id="tutorial-result-comment-frame">
					<div id="tutorial-result-comment">プロフィール作成しました！</div>
				</div>
				<div id="tutorial-result-image">
					<img src="/images/character/tutorial-finish.png" />
				</div>
				<div class="image-center"><a href="/main/"><img src="/images/pc/button/button-start.png" style="width: 250px; height: 60px;" /></a></div>
			</div>
		</section>
	</div>
	<script src="/plugins/anijs/dist/anijs.js"></script>
<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>