<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php
	if(empty($error)){
	?>

		<?php
		/****************************************
		**
		**	キャンペーン詳細
		**
		*****************************************/
		if(!empty($campaign_contents['id'])){

		?>
			<section id="campaign">
				<div class="title"><?php print($campaign_contents['title_normal']); ?></div>
				<?php if(!empty($campaign_body)){ ?>
					<div id="campaign-body">
						<?php print($campaign_body); ?>
					</div>
				<?php } ?>
			</section>

		<?php }else{ ?>

			<div class="title">キャンペーン</div>

			<div class="container-box">
				<div id="warning">
					<?php print($errormessage); ?>
				</div>
			</div>

		<?php } ?>

	<?php }else{ ?>

		<div class="title">キャンペーン</div>

		<div class="container-box">
			<div id="warning">
				<?php print($errormessage); ?>
			</div>
		</div>

	<?php } ?>

	<div class="image-center"><a href="/main/"><img src="/images/pc/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>