

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">開放シーン交換所</div>
	<div class="exchange-rule">
		重複した開放シーンを<?php print(TICKET_NAME); ?>に交換することができます。<br />
		開放シーン１つにつき<span style="color: #FF0000;"><?php print($exchange_point); ?><?php print(TICKET_NAME); ?></span>になります。
	</div>

	<div id="exchange-list">
		<?php
		if(isset($album_list['id'])){
		?>
			<form action="/<?php print($directory); ?>/exection/" method="post" id="editForm" name="editForm">
			<input type="hidden" name="return" value="<?php print($directory); ?>" />
			<input type="hidden" name="purpose" value="2" />
			<?php
			$count	= count($album_list['id']);
			if($count > 0){
				$album_count	= 0;
				for($i=0;$i<$count;$i++){
				?>
				<?php if($album_count == 0){ ?>
				<div class="exchange-list">
					<ul>
				<?php } ?>

				<?php
					$no_touch			= NULL;
					if(!empty($album_list['none'][$i])){
						$no_touch		= " class=\"no-touch\"";
					}
				?>

					<li>
						<p>
							<label for="image-<?php print($album_list['id'][$i]); ?>"><img src="<?php print(HTTP_ITEM_IMAGE); ?>/thumb/<?php print($album_list['image'][$i]); ?>"<?php print($no_touch); ?> /><span><?php print($album_list['name'][$i]); ?></span></label>
							<?php if(empty($no_touch)){ ?>
								<span class="exchange-checkbox"><input type="checkbox" name="exchange[]" id="image-<?php print($album_list['id'][$i]); ?>" value="<?php print($album_list['id'][$i]); ?>" /></span>
							<?php }else{ ?>
								<span class="no-exchange"><br />この画像は交換できません</span>
							<?php } ?>
						</p>
					</li>
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

				<div class="submit-area"><submit class="submit-button" onclick="submitData('<?php print($directory); ?>','exection','交換してもよろしいですか？');">交換する</submit></div>


<? /*
<?php if(defined("SYSTEM_CHECK")){ ?>
<input type="submit" value="SYSTEM POST" />
<input type="hidden" name="debug" value="1" />
<?php } ?>
*/ ?>


			<?php } ?>
			</form>


		<?php }else{ ?>

			<div id="exection">開放シーンにデータはありません</div>

		<?php } ?>

	</div>

	<?php if(!empty($exection_message)){ ?>
		<script type="text/javascript" >
			showSuccessDialog('交換しました！','<?php print($exection_message); ?>');
		</script>
	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

