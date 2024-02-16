<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/character.function.js" type="text/javascript" ></script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

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
				<div class="banner"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($banner_list['image'][$i]); ?>" border="0" /></div>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	
	<div class="title-nomargin">新着メール<span>未読 : <?php print($no_read_count); ?>件</span></div>
	<?php
	if(isset($character_list['id'])){
		$count	= count($character_list['id']);
		if($count > 0){
	?>
		<div id="character-list">
			<ul>
			<?php
			for($i=$start;$i<$stop;$i++){
				# 鍵付きキャラだったら
				if(!empty($character_list['secret_key'][$i])){
					$key_image			= "<span class=\"secret-key\"><img src=\"/images/icon/icon-secret.png\" id=\"image-".$character_list['parent_id'][$i]."\" /></span>";
					$key_class			= " class=\"key-back\"";
				}else{
					$key_image			= NULL;
					$key_class			= NULL;
				}
			?>
					<li>
						<?php
						/********************************
						**
						**	やり取りあり
						**
						*********************************/
						if($character_list['mail'][$i] == 2 || $character_list['mail'][$i] == 1){
						?>
							<?php
							# 鍵付きキャラ
							if(!empty($character_list['secret_key'][$i])){
							?>
							<a href="javascript:void(0)" onclick="secretKeyCheck(<?php print($character_list['parent_id'][$i]); ?>,'<?php print($character_list['name'][$i]); ?>',<?php print($character_list['secret_key'][$i]); ?>,'<?php print($character_list['key_name'][$i]); ?>','<?php print($character_list['key_image'][$i]); ?>');" id="key-<?php print($character_list['parent_id'][$i]); ?>">
							<?php
							# ノーマルキャラ
							}else{
							?>
							<a href="/mail/detail/<?php print($character_list['id'][$i]); ?>/">
							<?php } ?>
								<?php if(!empty($character_list['image'][$i])){ ?>
									<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($character_list['image'][$i]); ?>" id="secret-<?php print($character_list['id'][$i]); ?>"<?php print($key_class); ?> /><?php print($key_image); ?>
								<?php }else{ ?>
									<img src="/images/pc/noimage.png" /><?php print($key_image); ?>
								<?php } ?>
								<?php if(!empty($character_list['no_read'][$i])){ ?>
									<span class="mail-unread"><?php print($character_list['no_read'][$i]); ?></span>
								<?php } ?>
								<div class="button-profile"><a href="/character/profile/<?php print($set); ?>/<?php print($character_list['parent_id'][$i]); ?>/"><img src="/images/pc/button/button-profile.png" /></a></div>
							</a>
						<?php
						/********************************
						**
						**	やり取りなし
						**
						*********************************/
						}else{
						?>

							<?php
							# 鍵付きキャラ
							if(!empty($character_list['secret_key'][$i])){
							?>
							<a href="javascript:void(0)" onclick="secretKeyCheck(<?php print($character_list['id'][$i]); ?>,'<?php print($character_list['name'][$i]); ?>',<?php print($character_list['secret_key'][$i]); ?>,'<?php print($character_list['key_name'][$i]); ?>','<?php print($character_list['key_image'][$i]); ?>');" id="key-<?php print($character_list['id'][$i]); ?>">
							<?php
							# ノーマルキャラ
							}else{
							?>
							<a href="/mail/detail/<?php print($character_list['id'][$i]); ?>/">
							<?php } ?>
								<?php if(!empty($character_list['image'][$i])){ ?>
									<div class="no-mail" id="screen-<?php print($character_list['id'][$i]); ?>"><img src="<?php print(HTTP_ATTACHES); ?>/<?php print($character_list['image'][$i]); ?>" id="secret-<?php print($character_list['id'][$i]); ?>"<?php print($key_class); ?> /></div><?php print($key_image); ?>
								<?php }else{ ?>
									<div class="no-mail" id="screen-<?php print($character_list['id'][$i]); ?>"><img src="/images/pc/noimage.png"<?php print($key_class); ?> /></div><?php print($key_image); ?>
								<?php } ?>
								<div class="button-profile"><a href="/character/profile/<?php print($set); ?>/<?php print($character_list['parent_id'][$i]); ?>/"><img src="/images/pc/button/button-profile.png" /></a></div>
							</a>
						<?php } ?>

					</li>
					<?php } ?>

				</ul>
			</div>
		<?php } ?>
	<?php } ?>

	<div class="clear"></div>

	<?php if(empty($retirement)){ ?>
		<?php /*<a href="/character/index/retirement/">卒業キャラ</a>*/ ?>
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


