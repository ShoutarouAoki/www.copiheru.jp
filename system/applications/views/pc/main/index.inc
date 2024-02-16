<?php /* SlidesJS用CSS追加 */ ?>
<link rel="stylesheet" href="/common/css/pc/main.css">

<script src="/common/js/character.function.js" type="text/javascript" ></script>

<?php /* SlidesJS追加 */ ?>
<script src="/common/js/jquery/jquery.slides.js"></script>
<script type="text/javascript" >
<!--
$(function(){
  $("#slides").slidesjs({
    width: 680,
    height: 416,
    start: 1, //最初に表示するスライドの番号
    navigation: {
      active: false,
      effect: "slide"
    },
    pagination: {
      active: true,
      effect: "fade"
    },
    play: {
      active: false, //trueで再生、停止ボタンを表示、falseで非表示
      effect: "slide", //自動再生のときのエフェクトを"slide", "fade"から選択
      interval: 7000, //再生間隔をミリ秒で指定
      auto: true, //trueで自動再生を有効に
      swap: true, //falseで再生、停止ボタンを両方常に表示、trueで切り替え
      pauseOnHover: true, //trueでマウスホバー時に一時停止
      restartDelay: 2500 //最初に戻るときに間隔を遅らせる場合、ミリ秒で指定
    },
    effect: {
      slide: {
        speed: 300
      },
      fade: {
        speed: 300,
        crossfade: true
      }
    },
    //コールバックの設定
    callback: {
      loaded: function(number) {
      },
      start: function(number) {
      },
      complete: function(number) {
      }
    }
 
  });
 
});
-->
</script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<?php /* MAIN IMAGE */ ?>
	<div id="main-image">
		<?php if(!empty($header_data[0]['img_name'])){ ?>
			<!-- <img src="<?php print(HTTP_WEB_IMAGE); ?>/<?php print($header_data[0]['img_name']); ?>" />-->
			<? if(count($header_data)==1){ ?>
				<img src="<?php print(HTTP_WEB_IMAGE); ?>/<?php print($header_data[0]['img_name']); ?>" >
			<? }else{ ?>
			<div id="slides">
				<?php for($i=0;$i<count($header_data);$i++){ ?>
				<img src="<?php print(HTTP_WEB_IMAGE); ?>/<?php print($header_data[$i]['img_name']); ?>" alt="<?php print($i); ?>">
				<?php } ?>
				<div id="frame-slide-control" class="slidesjs-navigation">
					<div id="frame-indicator-area" >&nbsp;</div>
				</div><!-- /frame-slide-control -->
			</div>
			<? } ?>
		<?php }else{ ?>
			<img src="/images/pc/main.png" />
		<?php } ?>
		<div id="main-button"><a href="/character/"><img src="/images/smart/button/button-main.png" /></a></div>
	</div>
	<?php /* MAIN IMAGE */ ?>


	<?php
	/****************************************
	**
	**	キャンペーン
	**	===================================
	**	TOPには出さないようにしたよ
	**
	*****************************************/
	/*
	if(!empty($campaign_contents['id'])){
	?>
		<section id="campaign">
			<div class="title"><?php print($campaign_contents['title_normal']); ?></div>
			<?php if(!empty($campaign_body)){ ?>
				<div id="campaign-body">
					<?php print($campaign_body); ?>
				</div>
			<?php } ?>
		</section>
	<?php } ?>
	<?php */ ?>

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


	<?php if(isset($mail_list['id'])){ ?>
		<div class="title">新着メール<span>未読 : <?php print($no_read_count); ?>件</span></div>
		<div class="container-box">
			<?php
			$count	= count($mail_list['id']);
			if($count > 0){
				for($i=0;$i<$count;$i++){
					# 鍵付きキャラだったら
					if(!empty($mail_list['secret_key'][$i])){
						$key_image			= "<span class=\"secret-key\"><img src=\"/images/icon/icon-key.png\" id=\"image-".$mail_list['character_id'][$i]."\" /></span>";
						//$key_class			= " class=\"list-secret\"";
						$key_class			= NULL;
					}else{
						$key_image			= NULL;
						$key_class			= NULL;
					}
			?>
					<ul class="list">
						<li<?php print($key_class); ?>>
						<?php
						# 鍵付きキャラ
						if(!empty($mail_list['secret_key'][$i])){
						?>
						<a href="javascript:void(0)" onclick="secretKeyCheck(<?php print($mail_list['character_id'][$i]); ?>,'<?php print($mail_list['name'][$i]); ?>',<?php print($mail_list['secret_key'][$i]); ?>,'<?php print($mail_list['key_name'][$i]); ?>','<?php print($mail_list['key_image'][$i]); ?>');" id="key-<?php print($mail_list['character_id'][$i]); ?>">
						<?php
						# ノーマルキャラ
						}else{
						?>
							<a href="/mail/detail/<?php print($mail_list['character_id'][$i]); ?>/">
						<?php } ?>
							<div class="image">
								<?php if(!empty($mail_list['image'][$i])){ ?>
									<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($mail_list['image'][$i]); ?>" />
								<?php }else{ ?>
									<img src="/images/pc/noimage.png" />
								<?php } ?>
							</div>
							<div class="list-contents">
								<div class="content">
									<div class="name"><?php print($mail_list['name'][$i]); ?></div>
									<div class="talk">
										<?php print($mail_list['title'][$i]); ?>
									</div>
								</div>
							</div>
							<div class="clear"></div>
							<div class="under">
								<span class="option">新着♪</span>
								<?php if($mail_list['recv_flg'][$i] == 1){ ?>
									<span class="unread">未読</span>
								<?php }else{ ?>
									<span class="read">既読</span>
								<?php } ?>
								<?php if(!empty($mail_list['media'][$i])){ ?>
									<span class="photocheck"><?php print($mail_list['media'][$i]); ?></span>
								<?php } ?>
								<span class="under-date"><?php print($mail_list['send_date'][$i]); ?></span>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
							<?php print($key_image); ?>
						</a>
						</li>
					</ul>
				<?php } ?>
			<?php } ?>
		</div>

		<div class="image-center"><a href="/character/"><img src="/images/pc/button/button-talklist.png" style="width: 250px; height: 60px;" /></a></div>
	<?php } ?>

	<?php
	/****************************************
	**
	**	ログインボーナス受け取り
	**
	*****************************************/
	if(!empty($present_check) && !empty($present_dialog)){
	?>
		<script type="text/javascript" >
			loginBonusDialog("プレゼント","<?php print($present_dialog); ?>");
		</script>
	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>