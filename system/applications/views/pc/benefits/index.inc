<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/benefits.script.js" type="text/javascript" ></script>
<script src="/common/js/benefits.function.js" type="text/javascript" ></script>

<style>

</style>

<?php
/********************************
**
**	特典プレゼントorエラー表示エリア
**
*********************************/
?>
<div id="dialog-benefits"></div>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title-nomargin">特典コードでプレゼントをゲット！！</div>

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
					<img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($banner_list['image'][$i]); ?>" border="0" />
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>



	<div class="benefits-list">
		<ul>
			<li>
				<form method="post">
				<input type="hidden" name="site_cd" id="site_cd" value="<?php print($members_data['site_cd']);?>" />
				<input type="hidden" name="user_id" id="user_id" value="<?php print($members_data['id']);?>" />
				<section id="form-benefits">
					<ul>
						<li>
							<p>特典コードを入力してください</p>
							<label><input type="text" name="bcode" id="bcode" maxlength="12" value="" /></label>
						</li>
					</ul>
					<div id="submit-area-benefits"><input type="button" id="button-benefits" value="&nbsp;"/></div>
				</section>
				</form>
			</li>
		</ul>
	</div>
	
	
<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>