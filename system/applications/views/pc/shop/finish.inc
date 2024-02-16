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

	<div class="title">ショップエラー</div>

	<div class="container-box">
		<div id="warning">
			<?php print($errormessage); ?>
		</div>
		<div class="image-center"><a href="/gacha/"><img src="/images/pc/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>
	</div>

<?php
}else{
?>

	<script src="/common/js/shop.script.js" type="text/javascript" ></script>
	<link href="/plugins/animate/animate.css" rel="stylesheet" />
	<div id="shop-result">
		<section id="shop-result-area">
			<div id="shop-result-box">
				<div class="shop-result-title"><img src="/images/pc/title/title-shop.png" /></div>
				<div id="shop-result-item">
					<ul>
						<li>
							<p>
								<?php if(!empty($shops_data['image'])){ ?>
									<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($shops_data['image']); ?>" />
								<?php } ?>
							</p>
						</li>
					</ul>
				</div>
				<div id="shop-item-name"><span><?php print($shops_data['name']); ?></span></div>
				<div class="image-center"><a href="/main/"><img src="/images/pc/button/button-closing.png" style="width: 250px; height: 60px;" /></a></div>
			</div>
		</section>
	</div>
	<script src="/plugins/anijs/dist/anijs.js"></script>

<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>