<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">ヘルプ</div>
	<section id="help-list">

		<?php
		if(isset($help_list['id'])){
			$count	= count($help_list['id']);
			if($count > 0){
		?>
			<ul>
			<?php
				for($i=0;$i<$count;$i++){
			?>
				<li>
					<div class="help-title"><?php print($help_list['number'][$i]); ?>.<?php print($help_list['title'][$i]); ?></div>
					<div class="help-answer">
						<?php print($help_list['content'][$i]); ?>
					</div>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>

		<?php }else{ ?>
			<div id="exection">現在データがありません</div>
		<?php } ?>
		</ul>

	</section>

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
