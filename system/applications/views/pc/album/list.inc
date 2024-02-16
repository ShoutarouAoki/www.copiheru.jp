<link href="/plugins/colorbox-master/css/colorbox.css" rel="stylesheet" />
<script src="/plugins/colorbox-master/js/jquery.colorbox.js" type="text/javascript"></script>




<script type="text/javascript">
$(function(){

	<?php if($default_device == "pc"){ ?>
		var colorboxWidth	= 480;
		$(".colorbox").colorbox({rel:'colorbox',width: colorboxWidth});
	<?php }else{ ?>
		<?php if($os_number == 1){ ?>
			var colorboxWidth	= screen.width;
			var colorboxHeight	= screen.height;
			$(".colorbox").colorbox({rel:'colorbox',width: colorboxWidth,height:colorboxHeight});
		<?php }else{ ?>
			$(".colorbox").colorbox({rel:'colorbox',width: "90%"});
		<?php } ?>
	<?php } ?>

});
</script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title"><span class="left"><a href="/<?php print($directory); ?>/">リスト</a></span>開放シーン<span class="right"><a href="/<?php print($directory); ?>/slide/">スライド</a></span><div class="clear"></div></div>

	<div id="album-list">

		<?php
		if(isset($album_list['id'])){
			$count	= count($album_list['id']);
			if($count > 0){
				$album_count	= 0;
				for($i=0;$i<$count;$i++){
				?>
				<?php if($album_count == 0){ ?>
				<div class="album-list">
					<ul>
				<?php } ?>

					<li><p><a href="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($album_list['image'][$i]); ?>" class="colorbox" title="<?php print($album_list['name'][$i]); ?>"><img src="<?php print(HTTP_ITEM_IMAGE); ?>/thumb/<?php print($album_list['image'][$i]); ?>" /><span><?php print($album_list['name'][$i]); ?></span></a></p></li>
				<?php
				$album_count++;
				if($album_count >= 4){
				?>
					<div class="clear"></div>
					</ul>
				</div>
				<?php
					$album_count	= 0;
				}
				?>
				<?php } ?>

				<?php if($album_count > 0){ ?>
					<div class="clear"></div>
					</ul>
				</div>
				<?php } ?>
				<div class="clear"></div>

			<?php } ?>

		<?php }else{ ?>

			<div id="exection">開放シーンにデータはありません</div>

		<?php } ?>

	</div>

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
