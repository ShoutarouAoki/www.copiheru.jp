<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/key.function.js" type="text/javascript" ></script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>
<div id="dialog-key-exchange"></div>
	<div class="title">指名権交換所</div>
	<div class="exchange-rule">
		指名権を<?php print(TICKET_NAME); ?>に交換することができます。<br />
		<span style="color: #FF0000;">指名権１つにつき交換される<?php print(TICKET_NAME); ?>の量はキャラクター毎に異なります。</span><br />
		ご了承ください。
	</div>

	<?php
	if(empty($error)){
	?>
		<div id="key-exchange-list">
		<?php
		if(isset($item_list['id'])){
			$count	= count($item_list['id']);
			if($count > 0){
				$item_count	= 0;
				for($i=0;$i<$count;$i++){
		?>
					<?php if($item_count == 0){ ?>
					<div class="key-exchange-list">
						<ul>
					<?php } ?>
					
					<?php /* 指名権情報表示 */ ?>
					<li>
					<p>
						<?php
							$exchange_param = "'".$item_list['id'][$i]."', '".$item_list['name'][$i]."', '".$item_list['exchange'][$i]."', '".$item_list['unit'][$i]."', '".TICKET_NAME."'";
						?>
						<?php if($item_list['use'][$i]){ ?>
							<a href="javascript:void(0)" onclick="generateExchangeDialog(<?php print($exchange_param); ?>);" >
						<?php }else{ ?>
							<a href="javascript:void(0)" >
						<?php } ?>
						<img src="<?php print(HTTP_ITEM_IMAGE); ?>/thumb/<?php print($item_list['image'][$i]); ?>" />
						<?php if(!$item_list['use'][$i]){ ?>
							<span>未使用、交換できません</span>
						<?php }else{ ?>
							<span><?php print($item_list['name'][$i]); ?><br /><?php print("単価:".$item_list['exchange'][$i]); ?>,<?php print("所持数:".$item_list['unit'][$i]); ?></span>
						<?php } ?>
						</a>
						
					</p>
					
					</li>
					
					<?php
					$item_count++;
					if($item_count >= 4){
					?>
						<div class="clear"></div>
						</ul>
					</div>
					<?php
						$item_count	= 0;
					}
					?>
				<?php } ?>
					
				<?php if($item_count > 0){ ?>
					<div class="clear"></div>
					</ul>
				</div>
				<?php } ?>
				<div class="clear"></div>
			<?php } ?>

			<?php }else{ ?>
				<div id="exection">指名権をお持ちではありません。</div>
			<?php } ?>

		<?php
		}else{
		?>
			<div id="exection"><?php print($errormessage); ?></div>
		<?php } ?>
		</ul>
		</div>
	

	<div class="image-center"><a href="/<?php print($directory); ?>/"><img src="/images/smart/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>


	<?php if(!empty($exection_message)){ ?>
		<script type="text/javascript" >
			showSuccessDialog('交換しました！','<?php print($exection_message); ?>');
		</script>
	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

