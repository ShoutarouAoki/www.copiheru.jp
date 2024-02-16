<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>
<script src="/common/js/a.ctrl.function.js" type="text/javascript" ></script>
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
	foreach($banner_list as $value){
	?>
	<div class="banner">
		<img src="<?php print(HTTP_BANNER_IMAGE);?>/<?=$value?>" border="0" />
	</div>
	<?php } ?>

	<div class="title-nomargin">
		新着メール
		<span>
			未読 : <?php print(empty($no_read_count)?"0":$no_read_count); ?>件
		</span>
	</div>
	<?php
	if(isset($character_list)){
		$count	= count($character_list);
		if($count > 0){
	?>
		<div id="character-list">
			<ul>
				<?php
				for($i=$start;$i<$stop;$i++){
				?>
					<li>
						<?php
						# 鍵付きキャラ
						if(!empty($character_list[$i]['secret_key'])){
						?>
							<a href="javascript:void(0)" id="key-<?=$character_list[$i]['parent_id']?>" 
								onclick="secretKeyCheck(
									<?=$character_list[$i]['parent_id']?>,
									'<?=$character_list[$i]['nickname']?>',
									<?=$character_list[$i]['secret_key']?>,
									'<?=$character_list[$i]['key_name']?>',
									'<?=$character_list[$i]['key_image']?>'
								);"
							>
						<?php
						# ノーマルキャラ
						}else{
						?>
							<a href="/mail/detail/<?php print($character_list[$i]['parent_id']); ?>/">
						<?php } ?>
						<?php if(!empty($character_list[$i]['image'])){ ?>
							<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($character_list[$i]['image']); ?>" 
								id="secret-<?php print($character_list[$i]['parent_id']); ?>"
								<?php if(!empty($character_list[$i]['secret_key'])){ ?>
								class="key-back";
								<?php } ?>
							/>

							<?php if(!empty($character_list[$i]['secret_key'])){ ?>
								<span class="secret-key">
									<img src="/images/icon/icon-secret.png" id="image-<?=$character_list[$i]['parent_id']?>"/>
								</span>

							<?php } ?>
						<?php }else{ ?>
							<img src="/images/smart/noimage.png" 
								<?php if(!$character_list[$i]['secret_key']){ ?>
									class="key-back"
								<? } ?>
							/>

							<span class="secret-key">
								<img src="/images/icon/icon-secret.png" id="image-<?=$character_list[$i]['parent_id']?>"/>
							</span>
						<?php } ?>
						<?php if(!empty($character_list[$i]['no_read'])){ ?>
							<span class="mail-unread">
								<?=$character_list[$i]['no_read']?>
							</span>
						<?php } ?>
						<?php if(!empty($character_list[$i]['eventmail'])){ //イベントメールがある ?>
							<div class="button-eventmail">
								<?php
								# 鍵付きキャラ
								if(!empty($character_list[$i]['secret_key'])){
								?>
									<a href="javascript:void(0)" id="key-<?=$character_list[$i]['parent_id']?>" 
										onclick="secretKeyCheck(<?=$character_list[$i]['parent_id']?>,
											'<?=$character_list[$i]['nickname']?>',
											<?=$character_list[$i]['secret_key']?>,
											'<?=$character_list[$i]['key_name']?>',
											'<?=$character_list[$i]['key_image']?>'
										);"
									>
								<?php
								# ノーマルキャラ
								}else{
								?>
								<a href="/eventmail/detail/<?php print($character_list[$i]['parent_id']); ?>/<?php print($character_list[$i]['eventid']); ?>/">
								<?php } ?>
									<img src="/images/smart/button/button-eventmail.png" />
									<?php if(!empty($character_list[$i]['no_read_eventmail'])){ ?>
										<span class="mail-unread_event"><?php print($character_list[$i]['no_read_eventmail']); ?></span>
									<?php } ?>
								</a>								
							</div>
						<?php } ?>
						<div class="button-profile">
							<a href="/character/profile/<?php print($set); ?>/<?php print($character_list[$i]['parent_id']); ?>/">
								<img src="/images/smart/button/button-profile.png" />
							</a>
						</div>
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


