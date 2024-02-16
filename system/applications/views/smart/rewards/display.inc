<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link href="/common/css/smart/ranking.css" rel="stylesheet" type="text/css" />
<?php
/*
echo "<pre>";
print_r($rewards_list);
echo "</pre>";
*/
?>
<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title"><?php print($event_title); ?></div>

	<?php
	if(empty($error)){
	?>
	<div id="reward-list">
	<ul>
		<li><img src="<?php print($character_image); ?>" border="0" /></li>
		<?php if($reward_character>0){ ?>
			<?php foreach($rewards_list as $key=>$val){ ?>
			<li>
				<div>
				<img src="<?php print($val["image"]); ?>" />
				<?php print($val["name"]."&nbsp".$val["unit"]."個&nbsp;"); ?><br />
				<?php if($val["getted"]>0){ 
					print("<font color=\"#F00\">".$val["point"]."PT突破、獲得済み</font>");
				}else{
					print("貢献ポイント".$val["point"]."PT突破が条件");
				} ?>
				</div>
				
			</li>
			<?php } ?>
		<?php } ?>
	<?php
	}else{
	?>
		<div id="exection"><?php print($errormessage); ?></div>
	<?php } ?>

	<div class="image-center"><a href="/<?php print($directory); ?>/"><img src="/images/smart/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>



<?php
/****************************
**
**	モーダル・ダイアログ用CSS
**
*****************************/
?>
<link href="/common/css/smart/modal.css" rel="stylesheet" type="text/css" />


<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>
<script src="/common/js/modal.script.js" type="text/javascript" ></script>
<script src="/common/js/modal.function.js" type="text/javascript" ></script>


<?php
/*************************************
**
**	CONTENTS MODAL / iOS
**	---------------------------------
**	メニュー用モーダルiOS用
**
**************************************/
if($os_number == 1){
?>

	<link href="/plugins/ModalWindowEffects/css/component.css" rel="stylesheet" type="text/css" />

<?php
/*************************************
**
**	CONTENTS MODAL / Android
**	---------------------------------
**	メニュー用モーダルAndroid用
**
**************************************/
}elseif($os_number == 2){
?>

	<link href="/plugins/colorbox-master/css/colorbox.css" rel="stylesheet" />
	<script src="/plugins/colorbox-master/js/jquery.colorbox.js" type="text/javascript"></script>

<?php } ?>

<?php
/*************************************
**
**	デバッグ用
**
**************************************/
# システム部からスマフォデザインをPCで見た場合
if(defined("SYSTEM_CHECK") && $default_device == "pc"){
	$colorbox_width		= "460";
# 通常
}else{
	$colorbox_width		= "90%";
}
?>

<?php
/*************************************
**
**	jQuery / Ajax
**
**************************************/
?>
<script type="text/javascript" >

	<?php /* 変数定義 */ ?>
	colorbox_width	= "<?php print($colorbox_width); ?>";

	<?php /* 必要ないけど念のためjsファイルに変数渡して上書き定義 */ ?>
	constructDefine(colorbox_width);


</script>



<?php
/*************************************
**
**	CONTENTS MODAL / iOS
**	==================================
**	所持チケット / 所持アイテム iOS
**
**************************************/
if($os_number == 1){
?>
	<div class="md-modal md-effect-8" id="modal-0001">
		<div class="md-content">
			<div class="modal-box">
				<div class="modal-bottom"><a class="md-close"><img src="/images/smart/button/button-close.png" /></a></div>
				<div class="clear"></div>
				<div class="modal-title"><img src="/images/smart/title/title-myrank.png" /></div>
				<div class="modal-contents">
					<?php
					if(!empty($myrank)){
					?>
						<div class="margin15"></div>
						<div class="myrank"><span class="myrank-name">あなたの順位</span>&nbsp;&nbsp;<?php print($myrank) ?>位</div>
						<div class="myrank"><span class="myrank-name">あなたの貢献Pt(％)</span>&nbsp;&nbsp;<?php print($mypercent); ?>％</div>
					<?php
					}else{
					?>
						<div id="exection">まだ貢献Ptがありません。</div>
					<?php } ?>
					<div class="clear"></div>
				</div>
				<div class="modal-under"></div>
			</div>
		</div>
	</div>

	<script src="/plugins/ModalWindowEffects/js/classie.js"></script>
	<script src="/plugins/ModalWindowEffects/js/modalEffects.js"></script>


<?php
/*************************************
**
**	CONTENTS MODAL / ANDROID
**	==================================
**	所持チケット / 所持アイテム ANDROID
**
**************************************/
}elseif($os_number == 2){
?>

	<div id="myrank-frame">
		<div id="myrank-area" class="modal-box">
			<div class="modal-title"><img src="/images/smart/title/title-myrank.png" /></div>
			<div class="modal-contents">
				<?php
				if(!empty($myrank)){
				?>
					<div class="margin15"></div>
					<div class="myrank"><span class="myrank-name">あなたの順位</span>&nbsp;&nbsp;<?php print($myrank) ?>位</div>
					<div class="myrank"><span class="myrank-name">あなたの貢献Pt(％)</span>&nbsp;&nbsp;<?php print($mypercent); ?>％</div>
				<?php
				}else{
				?>
					<div id="exection">まだ貢献Ptがありません。</div>
				<?php } ?>
				<div class="clear"></div>
			</div>
			<div class="modal-under"></div>
		</div>
	</div>

<?php } ?>
