<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php
	if(!empty($error)){
	?>

		<div class="title">エラー</div>

		<div class="container-box">
			<div id="warning">
				<?php print($errormessage); ?>
			</div>
			<div class="image-center"><a href="/character/list/<?php print($set); ?>/"><img src="/images/pc/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>
		</div>

	<?php }elseif(!empty($standby)){ ?>

		<div class="title">準備中</div>

		<div class="container-box">
			<div id="warning">
				<?php print($errormessage); ?>
			</div>
			<div class="image-center"><a href="/character/list/<?php print($set); ?>/"><img src="/images/pc/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>
		</div>

	<?php }else{ ?>

		<div id="profile-area">
			<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($image); ?>" />
			<div id="return-button" class="image-center"><a href="/character/list/<?php print($set); ?>/"><img src="/images/pc/button/button-return-heart.png" /></a></div>
		</div>

		<?php
		//if(strlen($images_otherinfo[0]["attached"])){ 
			foreach($images_otherinfo as $val){
		?>
		<div id="profile-other-area">
		<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($val["attached"]); ?>" />
		</div>
		<?php 
			}
		//} 
		?>
	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>


