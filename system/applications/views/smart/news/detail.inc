<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php
	if(empty($error)){
	?>

		<?php
		/****************************************
		**
		**	お知らせ詳細
		**
		*****************************************/
		if(!empty($news_data['id'])){

		?>
			<div class="title"><?php print($news_data['title']); ?></div>
			<section id="news">
				<?php if(!empty($content)){ ?>
					<div id="news-body">
						<?php print($content); ?>
					</div>
					<div id="news-date">
						DATE : <?php print($display_date); ?>
					</div>
				<?php } ?>
			</section>

		<?php }else{ ?>

			<div class="title">お知らせ</div>

			<div class="container-box">
				<div id="warning">
					<?php print($errormessage); ?>
				</div>
			</div>

		<?php } ?>

	<?php }else{ ?>

		<div class="title">お知らせ</div>

		<div class="container-box">
			<div id="warning">
				<?php print($errormessage); ?>
			</div>
		</div>

	<?php } ?>

	<div class="image-center"><a href="/news/index/<?php print($set); ?>/"><img src="/images/smart/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>