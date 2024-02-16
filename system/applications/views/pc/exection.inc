<?php if($directory == "exection"){ ?>
	<div class="page-title-margin"><?php print($exection_title); ?></div>
	<div class="contents_box">
	<div id="exection">
	<?php print($exection_message) ?>
	</div>
	</div>
	<?php if(!empty($exection_close)){ ?>
	<div class="large-button"><a href="finish://">閉じる</a></div>
	<?php }else{ ?>
	<div class="large-button"><a href="<?php print($return_path); ?>">戻る</a></div>
	<?php } ?>
	<div class="clearline"></div>
<?php }else{ ?>
	<div class="margin15"></div>
	<div id="exection" name="exection">
	<?php print($exection_message) ?>
	</div>
	<div class="top-line"></div>
<?php } ?>
