<?php
/******************************
**
**	ERROR
**
*******************************/
if(!empty($error)){
?>

	<div class="title">ガチャエラー</div>

	<div class="container-box">
		<div id="warning">
			<?php print($errormessage); ?>
		</div>
		<div class="image-center"><a href="/gacha/"><img src="/images/pc/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>
	</div>

<?php

}else{
?>

	<link href="/plugins/animate/animate.css" rel="stylesheet" />
	<section id="gacha-result-area">

		<div id="gacha-result-box">
			<div class="gacha-result-title"><img src="/images/pc/title/title-item.png" /></div>

			<?php
			/********************************
			**
			**	マルチ / SINGLE
			**
			*********************************/
			if($data['type'] == "multi"
				|| $data['type'] == "limitted1" || $data['type'] == "limitted2" || $data['type'] == "limitted3"
				|| $data['type'] == "stepup1" || $data['type'] == "stepup2" || $data['type'] == "stepup3"){
			//if($data['type'] == "multi"){
				$section_id			= "gacha-result-item-multi";
			}else{
				$section_id			= "gacha-result-item-single";
			}
			?>

			<div id="<?php print($section_id); ?>">

				<?php
				$count				= count($gacha_list['id']);
				if($count > 0){
				?>
					<ul>
					<?php
					for($i=0;$i<$count;$i++){
						if($data['type'] != "multi"){
							$multiplication	= "×";
						}elseif($gacha_list['unit'][$i] >= 10){
							$multiplication	= NULL;
						}else{
							$multiplication	= "×";
						}
					?>
						<li>
							<p>
								<?php if(!empty($gacha_list['display'][$i])){ ?>
									<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($gacha_list['display'][$i]); ?>" />
								<?php }elseif(!empty($gacha_list['image'][$i])){ ?>
									<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($gacha_list['image'][$i]); ?>" />
								<?php } ?>
								<span><?php print($multiplication); ?><?php print($gacha_list['unit'][$i]); ?></span>
							</p>
						</li>
					<?php } ?>

						<div class="clear"></div>
					</ul>
				<?php }else{ ?>
					<div id="warning">ガチャ結果セットがありません</div>
				<?php } ?>


			</div>

			<div class="image-center"><a href="/gacha/"><img src="/images/pc/button/button-closing.png" style="width: 250px; height: 60px;" /></a></div>
		</div>

	</section>
	<script src="/plugins/anijs/dist/anijs.js"></script>

<?php } ?>