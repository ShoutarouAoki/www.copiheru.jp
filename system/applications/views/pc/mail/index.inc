<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>



	<div class="title">新着メール<span>未読 : <?php print($no_read_count); ?>件</span></div>

	
		<?php
		$count	= count($chara_list['id']);
		if($count > 0){
			for($i=0;$i<$count;$i++){
		?>
			<?php if($chara_count = 0){ ?>
				<div class="chara-list">
			<?php } ?>
				<ul>
					<li>
					<a href="/mail/talk/<?php print($chara_list['id'][$i]); ?>/">
						<div class="image">
							<?php if(!empty($chara_list['image'][$i])){ ?>
								<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($chara_list['image'][$i]); ?>" />
							<?php }else{ ?>
								<img src="/images/pc/noimage.png">
							<?php } ?>
						</div>
						<div class="name"><?php print($chara_list['name'][$i]); ?></div>
					</li>
				</ul>
			<?php } ?>
		<?php } ?>
	</div>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>