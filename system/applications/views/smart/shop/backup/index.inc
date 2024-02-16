<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">ショップ</div>

	<div class="shop-link">
		<?php if($os_number == 1){ ?>
			<div><a class="md-trigger dialog-button" data-modal="modal-0001" id="0001"><img src="/images/smart/button/button-ticketbox.png" /></a></div>
			<div><a class="md-trigger dialog-button" data-modal="modal-0002" id="0002"><img src="/images/smart/button/button-itembox.png" /></a></div>
		<?php }elseif($os_number == 2){?>
			<div><a href="javascript:void(0)" id="ticket-button" data-tor-smoothScroll="noSmooth"><img src="/images/smart/button/button-ticketbox.png" /></a></div>
			<div><a href="javascript:void(0)" id="item-button" data-tor-smoothScroll="noSmooth"><img src="/images/smart/button/button-itembox.png" /></a></div>
		<?php } ?>
		<p class="clear"></p>
	</div>
	<div class="clear"></div>

	<?php
	/****************************************
	**
	**	バナー
	**
	*****************************************/
	if(isset($banner_list['id'])){
		$count	= count($banner_list['id']);
		if($count > 0){
			for($i=0;$i<$count;$i++){
		?>
				<div class="banner">
				<?php if(!empty($banner_list['link'][$i])){ ?>
					<a href="<?php print($banner_list['link'][$i]); ?>"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($banner_list['image'][$i]); ?>" border="0" /></a>
				<?php }else{ ?>
					<img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($banner_list['image'][$i]); ?>" border="0" />
				<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>

	<div class="shop-banner">
		<ul>
			<?php
			if(isset($button_list['id'])){
				$count		= count($button_list['id']);
				if($count > 0){
					for($i=0;$i<$count;$i++){
			?>
						<li name="<?php print($button_list['target_id'][$i]); ?>"><a href="/<?php print($directory); ?>/list/<?php print($button_list['link'][$i]); ?>/<?php print($button_list['target_id'][$i]); ?>/"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($button_list['image'][$i]); ?>" border="0" /></li>
			<?php
					}
				}
			}
			?>
			<?php if(empty($point_button_hidden)){ ?>
				<li><a href="/shop/list/point/"><img src="/images/smart/banner/banner-shop-point.png" /></a></li>
			<?php } ?>
			<?php if(empty($item_button_hidden)){ ?>
				<li><a href="/shop/list/item/"><img src="/images/smart/banner/banner-shop-item.png" /></a></li>
			<?php } ?>
		</ul>
	</div>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>


<?php
/*************************************
**
**	ポイント / アイテム 共通モーダルファイル
**
**************************************/
include_once(dirname(__FILE__)."/../modal.inc");
?>