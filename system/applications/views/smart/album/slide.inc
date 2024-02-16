<link href="/plugins/slider-pro-master/dist/css/slider-pro.css" rel="stylesheet" type="text/css" media="screen" />
<script src="/plugins/slider-pro-master/dist/js/jquery.sliderPro.min.js"></script>


<script type="text/javascript">
$( document ).ready(function( $ ) {

	<?php /* SMART */ ?>
	<?php if($default_device == "smart"){ ?>

		var sliderWidth		= screen.width;
		var sliderHieght	= (sliderWidth / 2) * 3;

		$('#thumb-h').sliderPro({
			width: sliderWidth,
			height: sliderHieght,
			buttons: false,
			shuffle: false,
			loop: true,
			autoplay: false,
			thumbnailWidth: 40,
			thumbnailHeight: 60,
			slideDistance:0,
			breakpoints: {
				480: {
					thumbnailWidth: 40,
					thumbnailHeight: 60
				}
			}
		});

	<?php /* それ以外 */ ?>
	<?php }else{ ?>

		$('#thumb-h').sliderPro({
			height:720,
			buttons: false,
			shuffle: false,
			loop: true,
			autoplay: false,
			thumbnailWidth: 40,
			thumbnailHeight: 60,
			slideDistance:0,
			breakpoints: {
				480: {
					thumbnailWidth: 40,
					thumbnailHeight: 60
				}
			}
		});

	<?php } ?>

});

</script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title"><span class="left"><a href="/<?php print($directory); ?>/">リスト</a></span>マイアルバム<span class="right"><a href="/<?php print($directory); ?>/slide/">スライド</a></span><div class="clear"></div></div>

	<?php
	if(isset($album_list['id'])){
	?>
		<div id="thumb-h" class="slider-pro" style="margin-top: -5px;">
		
		<?php
		$count	= count($album_list['id']);
		if($count > 0){
		?>
			<div class="sp-slides">
			<?php
			for($i=0;$i<$count;$i++){
			?>
				<div class="sp-slide">
					<img class="sp-image" src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($album_list['image'][$i]); ?>" />
					<?php if(!empty($album_list['name'][$i])){ ?>
					<p class="sp-layer sp-black sp-padding" data-position="bottomCenter" data-vertical="1%" data-width="96%" data-show-delay="400" data-hide-delay="500" data-show-transition="up" data-hide-transition="down"><?php print($album_list['name'][$i]); ?></p>
					<?php } ?>
				</div>
			<?php } ?>
			</div>
			<div class="sp-thumbnails">
			<?php
			for($i=0;$i<$count;$i++){
			?>
				<img class="sp-thumbnail" src="<?php print(HTTP_ITEM_IMAGE); ?>/thumb/<?php print($album_list['image'][$i]); ?>"/>
			<?php } ?>
			</div>
		<?php } ?>
		</div>

	<?php }else{ ?>

		<div id="exection">マイアルバムにデータはありません</div>

	<?php } ?>

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
