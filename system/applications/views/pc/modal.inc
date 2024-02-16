<?php
if($directory != "mail"){
?>

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

	<link href="/plugins/colorbox-master/css/colorbox.css" rel="stylesheet" />
	<script src="/plugins/colorbox-master/js/jquery.colorbox.js" type="text/javascript"></script>

<?php } ?>


<div id="ticket-frame">
	<div id="user-ticket" class="modal-box">
		<div class="modal-title"><img src="/images/smart/title/title-ticket.png" /></div>
		<div class="modal-contents">
			<div class="modal-frame-user-point"><div class="modal-frame-point"><?php print(TICKET_NAME_USER); ?> × <span id="user-point" class="user-point"><?php print($members_data['user_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
			<div class="modal-frame-free-point"><div class="modal-frame-point"><?php print(TICKET_NAME_FREE); ?> × <span id="free-point" class="free-point"><?php print($members_data['f_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
			<div class="modal-button"><a href="/shop/list/point/"><img src="/images/smart/button/button-buypoint.png" /></a></div>
			<div class="clear"></div>
		</div>
		<div class="modal-under"></div>
	</div>
</div>

<div id="item-frame">
	<div id="user-item" class="modal-box">
		<div class="modal-title"><img src="/images/smart/title/title-itembox.png" /></div>
		<?php
		$count			= 0;
		if(isset($user_item_list['id'])){
			$count		= count($user_item_list['id']);
		}
		if($count > 0){
		?>
			<div class="modal-list">
				<ul>
				<?php
				for($i=0;$i<$count;$i++){
					$user_item_id			= $user_item_list['id'][$i];
					$user_item_count			= $user_item_list['unit'][$i];
				?>
					<li class="item-list" id="item-id-<?php print($user_item_id); ?>">
						<?php if(!empty($user_item_list['image'][$i])){ ?>
						<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($user_item_list['image'][$i]); ?>" />
						<?php } ?>
						<span class="item-name"	id="item-name-<?php print($user_item_id); ?>"><?php print($user_item_list['name'][$i]); ?></span>
						<span class="item-description" id="item-description-<?php print($user_item_id); ?>"><?php print($user_item_list['description'][$i]); ?></span>
						<?php if(!empty($user_item_count)){ ?>
							<p class="check-count" id="item-num-<?php print($user_item_id); ?>"><?php print($user_item_count); ?></p>
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

