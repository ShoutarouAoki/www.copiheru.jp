<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php
	if(empty($error)){
	?>

		<?php
		/****************************************
		**
		**	イベント詳細
		**
		*****************************************/
		if(!empty($event_contents['id'])){

		?>
			<section id="campaign">
				<div class="title"><?php print($event_contents['title_normal']); ?></div>
				<?php if(!empty($event_body)){ ?>
					<div id="campaign-body">
						<?php print($event_body); ?>
					</div>
				<?php } ?>
				<?php if($event_data['type'] == 1){ ?>
					<div id="event-ranking-button"><a href="/ranking/character/<?php print($event_data['id']); ?>/">このイベントのランキングを見る</a></div>
				<?php } ?>
			</section>

		<?php }else{ ?>

			<div class="title">イベント情報</div>

			<div class="container-box">
				<div id="warning">
					<?php print($errormessage); ?>
				</div>
			</div>

		<?php } ?>

	<?php }else{ ?>

		<div class="title">イベント情報</div>

		<div class="container-box">
			<div id="warning">
				<?php print($errormessage); ?>
			</div>
		</div>

	<?php } ?>

	<div class="image-center"><a href="/main/"><img src="/images/smart/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>