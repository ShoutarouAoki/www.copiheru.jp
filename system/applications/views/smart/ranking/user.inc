<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link href="/common/css/smart/ranking.css" rel="stylesheet" type="text/css" />

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title"><?php print($event_title); ?></div>

	<?php
	if(empty($error)){
	?>

		<?php if(isset($ranking_list['user_id'])){ ?>

			<div id="ranking-menu">
			<?php if($os_number == 1){ ?>
				<a class="md-trigger dialog-button" data-modal="modal-0001" id="0001"><img src="/images/smart/button/button-myranking.png" /></a>
			<?php }elseif($os_number == 2){?>
				<a href="javascript:void(0)" id="myrank-button" data-tor-smoothScroll="noSmooth"><img src="/images/smart/button/button-myranking.png" /></a>
			<?php } ?>
				<div class="clear"></div>
			</div>

			<div class="ranking-line"></div>
			<div id="ranking-area" class="container-box">
				<ul class="ranking-list">
				<?php
				$count	= count($ranking_list['user_id']);
				if($count > 0){
					for($i=0;$i<$count;$i++){

						if($ranking_list['ranking'][$i] == 1){

							$list_class			= NULL;
							$contents_class		= "ranking-contents-user";
							$name_class			= "ranking-name1";
							$result_class		= "ranking-result-user";
							$option_class		= "ranking-option1";
							$user_class			= "ranking-user";

						}elseif($ranking_list['ranking'][$i] == 2){

							$list_class			= NULL;
							$contents_class		= "ranking-contents-user";
							$name_class			= "ranking-name2";
							$result_class		= "ranking-result-user";
							$option_class		= "ranking-option2";
							$user_class			= "ranking-user";

						}elseif($ranking_list['ranking'][$i] == 3){

							$list_class			= NULL;
							$contents_class		= "ranking-contents-user";
							$name_class			= "ranking-name3";
							$result_class		= "ranking-result-user";
							$option_class		= "ranking-option3";
							$user_class			= "ranking-user";

						}else{

							$list_class			= "list-background";
							$contents_class		= "ranking-contents-user";
							$name_class			= "user-name";
							$result_class		= "ranking-result-user-normal";
							$option_class		= "option";
							$user_class			= "ranking-user-normal";

						}


				?>
						<li class="<?php print($list_class); ?>">
							<div class="<?php print($contents_class); ?>">
								<div class="content">
									<?php if($ranking_list['ranking'][$i] <= 3){ ?>
										<div class="name-line"><span class="<?php print($name_class); ?>"><?php print($ranking_list['name'][$i]); ?></span></div>
										<div class="<?php print($result_class); ?>">
											<span>総合順位</span>&nbsp;&nbsp;<?php print($ranking_list['ranking'][$i]) ?>位&nbsp;&nbsp;&nbsp;
											<span>応援Pt(％)</span>&nbsp;&nbsp;<?php print($ranking_list['percent'][$i]); ?>％
										</div>
									<?php }else{ ?>
										<span class="<?php print($name_class); ?>"><?php print($ranking_list['name'][$i]); ?></span>
										<span class="<?php print($result_class); ?>">
											<span>総合順位</span>&nbsp;&nbsp;<?php print($ranking_list['ranking'][$i]) ?>位&nbsp;&nbsp;&nbsp;
											<span>応援Pt(％)</span>&nbsp;&nbsp;<?php print($ranking_list['percent'][$i]); ?>％
										</span>
									<?php } ?>
								</div>
							</div>
							<div class="clear"></div>
						</li>
					<?php } ?>
				<?php } ?>
				</ul>
			</div>

		<?php } ?>

	<?php
	}else{
	?>
		<div id="exection"><?php print($errormessage); ?></div>
	<?php } ?>

	<div class="image-center"><a href="/<?php print($directory); ?>/character/<?php print($data['event_id']); ?>/"><img src="/images/smart/button/button-return.png" style="width: 250px; height: 60px;" /></a></div>

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
						<div class="myrank"><span class="myrank-name">あなたの応援Pt(％)</span>&nbsp;&nbsp;<?php print($mypercent); ?>％</div>
					<?php
					}else{
					?>
						<div id="exection">まだ応援Ptがありません。</div>
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
					<div class="myrank"><span class="myrank-name">あなたの応援Pt(％)</span>&nbsp;&nbsp;<?php print($mypercent); ?>％</div>
				<?php
				}else{
				?>
					<div id="exection">まだ応援Ptがありません。</div>
				<?php } ?>
				<div class="clear"></div>
			</div>
			<div class="modal-under"></div>
		</div>
	</div>

<?php } ?>
