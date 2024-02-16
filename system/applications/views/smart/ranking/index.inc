<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>
<script src="/common/js/a.ctrl.function.js" type="text/javascript" ></script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php
	if(isset($event_list['id']) || isset($event_past['id'])){
	?>

		<div class="title">イベントランキング一覧</div>

		<div class="title-link">
			<div><a href="/<?php print($directory); ?>/"><img src="/images/smart/button/button-ranking-event.png" /></a></div>
			<div><a href="/<?php print($directory); ?>/character/0/"><img src="/images/smart/button/button-ranking-total.png" /></a></div>
			<p class="clear"></p>
		</div>
		<div class="clear"></div>

		<?php
		/****************************************
		**
		**	バナー
		**
		*****************************************/
		if(isset($event_list['id'])){

			$count	= count($event_list['id']);
			if($count > 0){
				for($i=0;$i<$count;$i++){
			?>
					<div class="banner">
						<a href="/ranking/character/<?php print($event_list['id'][$i]); ?>/"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($event_list['image'][$i]); ?>" border="0" /></a>
					</div>
				<?php } ?>
			<?php } ?>

		<?php }else{ ?>
			<div id="exection">現在開催中のランキングイベントはありません</div>
		<?php } ?>


		<?php
		/****************************************
		**
		**	過去イベント
		**
		*****************************************/
		if(isset($event_past['id'])){
			$count	= count($event_past['id']);
			if($count > 0){
		?>
				<div class="title">過去ランキングイベント一覧</div>
				<?php
				for($i=0;$i<$count;$i++){
				?>
					<div class="banner">
						<a href="/ranking/character/<?php print($event_past['id'][$i]); ?>/"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($event_past['image'][$i]); ?>" border="0" /></a>
					</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>


	<?php
	}else{
	?>

		<div class="title">ランキングイベント</div>
		<div id="exection">ランキングイベント情報はありません</div>

	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>
