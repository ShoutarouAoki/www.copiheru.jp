<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">ショップエラー</div>

	<div class="container-box">
		<div id="warning">
			<?php print($errormessage); ?>
		</div>
		<div class="image-center"><a href="/shop/list/<?php print($data['type']); ?>/<?php print($data['campaign_id']); ?>/"><img src="/images/smart/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>
	</div>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>