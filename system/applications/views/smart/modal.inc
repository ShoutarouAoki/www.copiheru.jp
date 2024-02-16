<?php
/****************************
**
**	モーダル・ダイアログ用CSS
**
*****************************/
?>
<link href="/common/css/smart/modal.css" rel="stylesheet" type="text/css" />


<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>
<script src="/common/js/modal.script.js" type="text/javascript" ></script>
<script src="/common/js/modal.function.js" type="text/javascript" ></script>


<?php
/*************************************
**
**	CONTENTS MODAL / iOS
**	---------------------------------
**	メニュー用モーダルiOS用
**
**************************************/
if($os_number == 1){
?>

	<link href="/plugins/ModalWindowEffects/css/component.css" rel="stylesheet" type="text/css" />

<?php
/*************************************
**
**	CONTENTS MODAL / Android
**	---------------------------------
**	メニュー用モーダルAndroid用
**
**************************************/
}elseif($os_number == 2){
?>

	<link href="/plugins/colorbox-master/css/colorbox.css" rel="stylesheet" />
	<script src="/plugins/colorbox-master/js/jquery.colorbox.js" type="text/javascript"></script>

<?php } ?>

<?php
/*************************************
**
**	デバッグ用
**
**************************************/
# システム部からスマフォデザインをPCで見た場合
if($default_device == "pc"){
	$colorbox_width		= "460";
# 通常
}else{
	$colorbox_width		= "90%";
}
?>

<?php
/*************************************
**
**	jQuery / Ajax
**
**************************************/
?>
<script type="text/javascript" >

	<?php /* 変数定義 */ ?>
	colorbox_width	= "<?php print($colorbox_width); ?>";

	<?php /* 必要ないけど念のためjsファイルに変数渡して上書き定義 */ ?>
	constructDefine(colorbox_width);


</script>



<?php
/*************************************
**
**	CONTENTS MODAL / iOS
**	==================================
**	所持チケット / 所持アイテム iOS
**
**************************************/
if($os_number == 1){
?>

	<?php
	if(!empty($modal_position)){
	?>
		<style>
			.md-modal {
				position: absolute;
				top: <?php print($modal_position); ?>px;
			}
		</style>
	<?php } ?>

	<div class="md-modal md-effect-8" id="modal-0001">
		<div class="md-content">
			<div class="modal-box">
				<div class="modal-bottom"><a class="md-close"><img src="/images/smart/button/button-close.png" /></a></div>
				<div class="clear"></div>
				<div class="modal-title"><img src="/images/smart/title/title-ticket.png" /></div>
				<div class="modal-contents">
					<div class="modal-frame-user-point"><div class="modal-frame-point"><?php print(TICKET_NAME_USER); ?> × <span id="user-point"><?php print($members_data['user_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
					<div class="modal-frame-free-point"><div class="modal-frame-point"><?php print(TICKET_NAME_FREE); ?> × <span id="free-point"><?php print($members_data['f_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
					<?php if(!empty($modal_shop_buy)){ ?>
						<div class="modal-button"><a href="/shop/list/point/"><img src="/images/smart/button/button-buypoint.png" /></a></div>
					<?php } ?>
					<div class="clear"></div>
				</div>
				<div class="modal-under"></div>
			</div>
		</div>
	</div>

	<div class="md-modal md-effect-8" id="modal-0002">
		<div class="md-content">
			<div class="modal-box">
				<div class="modal-bottom"><a class="md-close"><img src="/images/smart/button/button-close.png" /></a></div>
				<div class="clear"></div>
				<div class="modal-title"><img src="/images/smart/title/title-itembox.png" /></div>
				<?php
				$count			= 0;
				if(isset($item_list['id'])){
					$count		= count($item_list['id']);
				}
				if($count > 0){
				?>
					<div class="modal-list">
						<ul>
						<?php
						for($i=0;$i<$count;$i++){
							$item_id			= $item_list['id'][$i];
							$item_count			= $item_list['unit'][$i];
						?>
							<li class="item-list" id="item-id-<?php print($item_id); ?>">
								<?php if(!empty($item_list['image'][$i])){ ?>
								<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list['image'][$i]); ?>" />
								<?php } ?>
								<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list['name'][$i]); ?></span>
								<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list['description'][$i]); ?></span>
								<?php if(!empty($item_count)){ ?>
									<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
								<?php } ?>
							</li>
						<?php } ?>
						</ul>
					</div>
				<?php }else{ ?>
					<div id="no-contents">所持アイテムはありません</div><br /><br />
				<?php } ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>


	<script src="/plugins/ModalWindowEffects/js/classie.js"></script>
	<script src="/plugins/ModalWindowEffects/js/modalEffects.js"></script>


<?php
/*************************************
**
**	CONTENTS MODAL / ANDROID
**	==================================
**	所持チケット / 所持アイテム ANDROID
**
**************************************/
}elseif($os_number == 2){
?>

	<div id="ticket-frame">
		<div id="ticket-area" class="modal-box">
			<div class="modal-title"><img src="/images/smart/title/title-ticket.png" /></div>
			<div class="modal-contents">
				<div class="modal-frame-user-point"><div class="modal-frame-point"><?php print(TICKET_NAME_USER); ?> × <span id="user-point"><?php print($members_data['user_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
				<div class="modal-frame-free-point"><div class="modal-frame-point"><?php print(TICKET_NAME_FREE); ?> × <span id="free-point"><?php print($members_data['f_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
				<?php if(!empty($modal_shop_buy)){ ?>
					<div class="modal-button"><a href="/shop/list/point/"><img src="/images/smart/button/button-buypoint.png" /></a></div>
				<?php } ?>
				<div class="clear"></div>
			</div>
			<div class="modal-under"></div>
		</div>
	</div>

	<div id="item-frame">
		<div id="item-area" class="modal-box">
			<div class="modal-title"><img src="/images/smart/title/title-itembox.png" /></div>
			<?php
			$count			= 0;
			if(isset($item_list['id'])){
				$count		= count($item_list['id']);
			}
			if($count > 0){
			?>
				<div class="modal-list">
					<ul>
					<?php
					for($i=0;$i<$count;$i++){
						$item_id			= $item_list['id'][$i];
						$item_count			= $item_list['unit'][$i];
					?>
						<li class="item-list" id="item-id-<?php print($item_id); ?>">
							<?php if(!empty($item_list['image'][$i])){ ?>
							<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list['image'][$i]); ?>" />
							<?php } ?>
							<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list['name'][$i]); ?></span>
							<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list['description'][$i]); ?></span>
							<?php if(!empty($item_count)){ ?>
								<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
							<?php } ?>
						</li>
					<?php } ?>
					</ul>
				</div>
			<?php }else{ ?>
				<div id="no-contents">所持アイテムはありません</div><br /><br />
			<?php } ?>
			<div class="clear"></div>
		</div>
	</div>

<?php } ?>
