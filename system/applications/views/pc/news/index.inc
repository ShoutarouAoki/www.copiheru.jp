<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>


	<div class="title-nomargin">お知らせ</div>
	<section id="news-list">
		<?php
		if(isset($news_list['id'])){

			$count	= count($news_list['id']);
			if($count > 0){
		?>
			<ul>
			<?php
				for($i=0;$i<$count;$i++){
			?>
					<li><a href="/news/detail/<?php print($set) ?>/<?php print($news_list['id'][$i]); ?>/"><div class="news-date"><?php print($news_list['date'][$i]); ?></div><?php print($news_list['title'][$i]); ?></a></li>
				<?php } ?>
			</ul>
			<?php } ?>

		<?php }else{ ?>
			<div id="exection">現在お知らせ情報はありません</div>
		<?php } ?>
	</section>

	<?php
	/*************************************
	**
	**	NEXT PREV
	**
	**************************************/
	?>
	<?php if(!empty($next_previous)){ ?>
	<div id="next_previous">
		<?php print($next_previous); ?>
		<div class="clear"></div>
	</div>
	<?php } ?>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>
