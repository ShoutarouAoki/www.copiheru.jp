<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php
	/************************************************
	**
	**	DETAIL : 詳細
	**
	************************************************/
	if(!empty($display_detail)){
	?>

		<div class="title"><?php print($guide_data['title']); ?></div>
		<div id="guide-area">
		<?php print($content); ?>
		</div>

		<div class="image-center"><a href="/information/guide/"><img src="/images/pc/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>

	<?php
	/************************************************
	**
	**	LIST : 一覧
	**
	************************************************/
	}else{
	?>
		<div class="title-nomargin">遊び方ガイド</div>
		<section id="list-menu">
			<?php
			if(isset($guide_list['id'])){
				$count	= count($guide_list['id']);
				if($count > 0){
			?>
				<ul>
				<?php
					for($i=0;$i<$count;$i++){
				?>
						<li><a href="/information/guide/<?php print($guide_list['id'][$i]); ?>/"><?php print($guide_list['title'][$i]); ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>

			<?php }else{ ?>
				<div id="exection">現在データがありません</div>
			<?php } ?>
			</ul>
		</section>
	<?php } ?>

	<div class="title-nomargin">MENU</div>
	<section id="list-menu">
		<ul>
			<li><a href="/news/">お知らせ</a></li>
			<li><a href="/information/guide/">遊び方ガイド</a></li>
			<li><a href="/tutorial/">チュートリアル</a></li>
			<li><a href="/information/help/">ヘルプ</a></li>
		</ul>
	</section>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>
