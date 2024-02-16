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


	<div class="title">新着メール<span>未読 : <?php print($no_read_count); ?>件</span></div>

	<?php
	if(isset($character_list['id'])){
		$count	= count($character_list['id']);
		if($count > 0){
		$character_count	= 0;
			for($i=0;$i<$count;$i++){

			# 鍵付きキャラだったら
			if(!empty($character_list['secret_key'][$i])){
				$key_image			= "<span><img src=\"/images/icon/icon-key.png\" id=\"image-".$character_list['id'][$i]."\" /></span>";
			}else{
				$key_image			= NULL;
			}

	?>
			<?php if($character_count == 0){ ?>
				<div class="character-list">
					<ul>
			<?php } ?>
					<li>
						<div class="character-list-box">

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
								<a href="javascript:void(0)" onclick="secretKeyCheck(<?php print($character_list['id'][$i]); ?>,'<?php print($character_list['name'][$i]); ?>',<?php print($character_list['secret_key'][$i]); ?>,'<?php print($character_list['key_name'][$i]); ?>','<?php print($character_list['key_image'][$i]); ?>');" id="key-<?php print($character_list['id'][$i]); ?>">
								<?php
								# ノーマルキャラ
								}else{
								?>
								<a href="/mail/detail/<?php print($character_list['id'][$i]); ?>/">
								<?php } ?>
									<?php if(!empty($character_list['image'][$i])){ ?>
										<div class="character-list-image"><img src="<?php print(HTTP_ATTACHES); ?>/<?php print($character_list['image'][$i]); ?>" /><?php print($key_image); ?></div>
									<?php }else{ ?>
										<div class="character-list-image"><img src="/images/smart/noimage.png" /><?php print($key_image); ?></div>
									<?php } ?>
									<span class="character-list-name"><?php print($character_list['name'][$i]); ?></span>
									<?php if(isset($character_list['no_read'][$i])){ ?>
										<div class="character-list-box">未読件数 : <?php print($character_list['no_read'][$i]); ?>件</div>
									<?php } ?>
								</a>
								<div class="character-list-box"><a href="/character/profile/<?php print($character_list['id'][$i]); ?>/">プロフィールを見るボタン</a></div>
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
										<div class="character-list-image"><div class="no-mail" id="screen-<?php print($character_list['id'][$i]); ?>"><img src="<?php print(HTTP_ATTACHES); ?>/<?php print($character_list['image'][$i]); ?>" /></div><?php print($key_image); ?></div>
									<?php }else{ ?>
										<div class="character-list-image"><div class="no-mail" id="screen-<?php print($character_list['id'][$i]); ?>"><img src="/images/smart/noimage.png" /></div><?php print($key_image); ?></div>
									<?php } ?>
									<span class="character-list-name"><?php print($character_list['name'][$i]); ?></span>
								</a>
								<div class="character-list-box"><a href="/character/profile/<?php print($character_list['id'][$i]); ?>/">プロフィールを見るボタン</a></div>
							<?php } ?>

						</div>
					</li>
			<?php
					$character_count++;
					if($character_count >= 3){
			?>
					<div class="clear"></div>
					</ul>
				</div>
			<?php
						$character_count = 0;
					}
				}
			?>
			<?php if($character_count > 0){ ?>
				<div class="clear"></div>
				</ul>
			</div>
			<?php } ?>

		<?php } ?>
	<?php } ?>

	<div class="clear"></div>

	<?php if(empty($retirement)){ ?>
		<a href="/character/index/retirement/">卒業キャラ</a>
	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>