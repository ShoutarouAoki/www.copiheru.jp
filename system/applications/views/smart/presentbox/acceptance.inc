
<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/presentbox.script.js" type="text/javascript" ></script>

<div class="title">プレゼントBOX <span><?php print($presentbox_rows); ?>件</span></div>
<?php if(isset($presentbox_list['id'])){ ?>
	<div class="acceptance-all" id="all-<?php print($set); ?>"><img src="/images/smart/button/button-acceptance.png" /></div>
	<div class="container-box hide">
		<?php
		$count	= count($presentbox_list['id']);
		if($count > 0){
			for($i=0;$i<$count;$i++){
		?>
				<ul class="list-line" id="<?php print($presentbox_list['id'][$i]); ?>-<?php print($set); ?>">
					<li>
					<? /*<a href="/presentbox/acceptance/<?php print($set); ?>/<?php print($presentbox_list['id'][$i]); ?>/" class="min">*/ ?>
						<div class="thumbnail">
							<?php if(!empty($presentbox_list['image'][$i])){ ?>
								<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($presentbox_list['image'][$i]); ?>" />
							<?php }else{ ?>
								<img src="/images/smart/noimage.png" />
							<?php } ?>
						</div>
						<div class="list-contents-long">
							<div class="content">
								<div class="shop-name" id="present-name-<?php print($presentbox_list['id'][$i]); ?>"><?php print($presentbox_list['name'][$i]); ?> × <?php print($presentbox_list['unit'][$i]); ?></div>
								<div class="limit">受け取り期限 :  <?php print($presentbox_list['limit_date'][$i]); ?></div>
							</div>
						</div>
						<div class="clear"></div>
					<? /*</a> */ ?>
					</li>
				</ul>
			<?php } ?>
		<?php } ?>
	</div>

<?php }else{ ?>

	<div id="exection">全て受け取り済みです</div>

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


<?php
/*************************************
**
**	ERROR
**
**************************************/
?>
<?php if(!empty($error)){ ?>
<script type="text/javascript" >
	showErrorDialog("受け取りエラー","<?php print($errormessage); ?>");
</script>
<?php } ?>

<?php
/*************************************
**
**	ERROR
**
**************************************/
?>
<?php if(!empty($result)){ ?>
<script type="text/javascript" >
	showDialog("受け取り完了","受け取りました！");
</script>
<?php } ?>
