<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/gacha.script.js" type="text/javascript" ></script>
<script src="/common/js/gacha.function.js" type="text/javascript" ></script>


<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title-nomargin">ガチャ</div>

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
				<?php if(!empty($banner_list['link'][$i])){ ?>
					<a href="<?php print($banner_list['link'][$i]); ?>"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($banner_list['image'][$i]); ?>" border="0" /></a>
				<?php }else{ ?>
					<img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($banner_list['image'][$i]); ?>" border="0" />
				<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>


	<div class="gacha-list">
		<ul>
			<?php if(!empty($site_data) && $site_data['free_gacha'] == 1){ ?>
				<?php if(!empty($free_gacha)){ ?>
					<li class="gachar-start" id="free"><img src="/images/pc/banner/banner-gacha-free.png" /></li>
				<?php }else{ ?>
					<li><img src="/images/pc/banner/banner-gacha-free-none.png" /></li>
				<?php } ?>
			<?php } ?>

			<?php
			if(isset($button_list['id'])){
				$count		= count($button_list['id']);
				if($count > 0){
					for($i=0;$i<$count;$i++){
						if(!empty($button_list['point'][$i])){
							$class			= "gachar-start";
						}else{
							$class			= "error-point";
						}
						if($button_list['link'][$i] == "limitted1" || $button_list['link'][$i] == "limitted2" || $button_list['link'][$i] == "limitted3"
							|| $button_list['link'][$i] == "stepup1" || $button_list['link'][$i] == "stepup2" || $button_list['link'][$i] == "stepup3"){//限定ステップアップ
						?>
							
							<?php if(!empty($button_list['content'][$i]) && $button_list['content'][$i] == "point"){ ?>
								<li class="<?php print($class); ?>" id="<?php print($button_list['link'][$i]); ?>" name="<?php print($button_list['target_id'][$i]); ?>-<?php print($button_list['point'][$i]); ?>"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($button_list['image'][$i]); ?>" border="0" /></li>
							<?php }else{ ?>
								<li>
								<? if($button_list['max'][$i]>0 && $button_list['rest'][$i]>0){ ?>
									<a href="/gacha/buy/<?php print($button_list['link'][$i]); ?>/<?php print($button_list['content'][$i]); ?>/<?php print($button_list['target_id'][$i]); ?>/">
								<?php } ?>
									<img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($button_list['image'][$i]); ?>" border="0" /><br/>
								<? if($button_list['max'][$i]>0 && $button_list['rest'][$i]>0){ ?>
									</a>
								<?php } ?>
								<?php 
									if($button_list['link'][$i] == "limitted1" || $button_list['link'][$i] == "limitted2" || $button_list['link'][$i] == "limitted3"){
										if($button_list['max'][$i]>0 && $button_list['rest'][$i]>0){
											echo "<span class=\"gacha-stepup-message\">".$button_list['times'][$i]."個中確定".$button_list['abs'][$i]."個、あと".$button_list['rest'][$i]."回!!</span>";
										}else{
											echo "<span class=\"gacha-stepup-message\">ガチャ回数が限度数まで達しました。</span>";
										}
									}else if($button_list['link'][$i] == "stepup1" || $button_list['link'][$i] == "stepup2" || $button_list['link'][$i] == "stepup3"){
										if($button_list['max'][$i]>0 && $button_list['rest'][$i]>0){
											echo "<span class=\"gacha-stepup-message\">".($button_list['max'][$i] - $button_list['rest'][$i]+1)."回目".(($button_list['abs'][$i]>0)?"、確定アリ!!":"!!")."</span>";
										}
									}
								?>
								</li>
							<?php } ?>
						<?php }else{?>
							<?php if(!empty($button_list['content'][$i]) && $button_list['content'][$i] == "point"){ ?>
								<li class="<?php print($class); ?>" id="<?php print($button_list['link'][$i]); ?>" name="<?php print($button_list['target_id'][$i]); ?>-<?php print($button_list['point'][$i]); ?>"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($button_list['image'][$i]); ?>" border="0" /></li>
							<?php }else{ ?>
								<li><a href="/gacha/buy/<?php print($button_list['link'][$i]); ?>/<?php print($button_list['content'][$i]); ?>/<?php print($button_list['target_id'][$i]); ?>/"><img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($button_list['image'][$i]); ?>" border="0" /></a></li>
							<?php } ?>
						<?php 
						}
			?>
			<?php
					}
				}
			}
			?>

			<?php if(GACHA_USE_POINT == "ON"){ ?>
				<?php if(empty($check_point_single)){ ?>
					<li class="gachar-start" id="single"><img src="/images/pc/banner/banner-gacha-point-single.png" /></li>
				<? }else{ ?>
					<li class="error-point"><img src="/images/pc/banner/banner-gacha-point-single.png" /></li>
				<?php } ?>
				<?php if(empty($check_point_multi)){ ?>
					<li class="gachar-start" id="multi"><img src="/images/pc/banner/banner-gacha-point-multi.png" /></li>
				<? }else{ ?>
					<li class="error-point"><img src="/images/pc/banner/banner-gacha-point-multi.png" /></li>
				<?php } ?>
			<?php } ?>

			<li><a href="/gacha/buy/single/coin/"><img src="/images/pc/banner/banner-gacha-coin-single.png" /></a></li>
			<? /* <li><a href="/gacha/buy/multi/coin/"><img src="/images/pc/banner/banner-gacha-coin-multi.png" /></a></li> */ ?>

		</ul>
	</div>

<?php
/*
<a href="/gacha/start/free/"></a>
<a href="/gacha/start/single/point/"></a>
<a href="/gacha/start/multi/point/"></a>
*/
?>
	<script type="text/javascript" >
		point			= <?php print($members_data['total_point']); ?>;
		single			= <?php print($point_single); ?>;
		multi			= <?php print($point_multi); ?>;
		ticket			= "<?php print(TICKET_NAME); ?>";
		constructDefine(point,single,multi,ticket);
	</script>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>