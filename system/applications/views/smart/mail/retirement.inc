<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link href="/common/css/smart/mail.css" rel="stylesheet" type="text/css" />

<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/mail.script.js" type="text/javascript" ></script>
<script src="/common/js/mail.function.js" type="text/javascript" ></script>

<?php
/****************************
**
**	背景画像切り替えプラグイン
**
*****************************/
?>

<script src="/plugins/ParaPara/src/parapara.js" type="text/javascript"></script>


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
	post_send_id	= <?php print($post_send_id); ?>;
	post_parent_id	= <?php print($post_parent_id); ?>;
	first_mail		= <?php print($first_children_id); ?>;
	next_before_id	= <?php print($next_before_id); ?>;
	item_using		= <?php print($item_using); ?>;
	colorbox_width	= "<?php print($colorbox_width); ?>";
	point_send		= <?php print($point_data['send']); ?>;
	point_read		= <?php print($point_data['read']); ?>;
	point_image		= <?php print($point_data['image']); ?>;
	status			= <?php print($mail_status['free']); ?>;
	sleep			= <?php print($character_data['word']); ?>;
	ticket_name		= "<?php print(TICKET_NAME); ?>";
	sender			= "<?php print($directory); ?>";
	more_path		= "<?php print($more_path); ?>";

	<?php /* 必要ないけど念のためjsファイルに変数渡して上書き定義 */ ?>
	constructDefine(post_send_id,post_parent_id,first_mail,next_before_id,item_using,colorbox_width,point_send,point_read,point_image,status,sleep,ticket_name,sender,more_path);

	<?php /* キャラおやすみチェック間隔 10分(600秒) */ ?>
	//var sleep_timer	= 600000;
	//setInterval("checkSleep()",sleep_timer);

</script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>


	<div id="mail-detail">

		<?php
		/********************************
		**
		**	画像
		**
		*********************************/
		?>
		<section id="character-image">
			<?php if($attaches_count > 0){ ?>
				<?php
				$count		= count($display_image['id']);
				for($i=0;$i<$count;$i++){
				?>
					<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($display_image['image'][$i]); ?>" />
				<?php } ?>
			<?php } else{ ?>
				<img src="<?php print(HTTP_ATTACHES); ?>/noimage.png" />
			<?php } ?>
		</section>
		<section id="animation-display">
			<?php if($attaches_count > 0){ ?>
				<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($display_image['image'][0]); ?>" />
			<?php } else{ ?>
				<img src="<?php print(HTTP_ATTACHES); ?>/noimage.png" />
			<?php } ?>
		</section>


		<?php
		/********************************
		**
		**	コントロールボタン(非表示対応)
		**
		*********************************/
		?>
		<section id="controll-back">
			<a href="javascript:void(0)" class="front-show"><img src="/images/smart/button/button-show.png" class="button-show" /></a>
			<div class="clear"></div>
		</div>


		<?php
		/********************************
		**
		**	好感度エリア
		**
		*********************************/
		?>
		<section id="favorite-area" class="front out">
			<div id="favorite-crown">
				<div id="favorite-level">Lv.<?php print($favorite_level); ?></div>
				<div id="degree-name"><?php print($degree_name); ?></div>
			</div>
			<div id="favorite-gauge"><div id="favorite-gauge-screen" style="height: <?php print($favorite_gauge); ?>%;"></div></div>
			<div id="favorite-percent"><?php print($favorite_percent); ?>％</div>
		</section>

		<?php /* 好感度レベルアップダイアログ */ ?>
		<div id="dialog-favorite"></div>


		<?php
		/********************************
		**
		**	文言エリア
		**
		*********************************/
		?>
		<section id="word-area" class="front out">

			<?php
			$count			= 0;
			$count_none		= 0;
			if(isset($itemuse_list['id'])){
				$count		= count($itemuse_list['id']);
			}
			if($count > 0){
				for($i=0;$i<$count;$i++){
			?>
				<div id="item-word-<?php print($itemuse_list['id'][$i]); ?>"><?php print($itemuse_list['word'][$i]); ?></div>
			<?php
				}
			}
			?>

		</section>


		<?php
		/********************************
		**
		**	メニュー
		**	=============================
		**	モーダルウィンドウで開く
		**	一番下にモーダルメニュー部分書いてある
		**
		*********************************/
		?>
		<section id="menu-area" class="front out">
			<?php if($os_number == 1){ ?>
				<a class="md-trigger item-menu" data-modal="modal-0002" id="0002"><img src="/images/smart/button/button-menu.png" /></a>
			<?php }elseif($os_number == 2){?>
				<a href="javascript:void(0)" id="menu-button" data-tor-smoothScroll="noSmooth"><img src="/images/smart/button/button-menu.png" /></a>
			<?php } ?>
			<?php if(empty($mail_status['free'])){ ?>
				<?php if($os_number == 1){ ?>
					<a class="md-trigger item-menu" data-modal="modal-0003" id="0003"><div id="menu-ticket"><img src="/images/smart/button/button-ticket.png" /><span class="check-point"><?php print($members_data['total_point']); ?></span></div></a>
				<?php }elseif($os_number == 2){?>
					<a href="javascript:void(0)" id="ticket-button" data-tor-smoothScroll="noSmooth"><div id="menu-ticket"><img src="/images/smart/button/button-ticket.png" /><span class="check-point"><?php print($members_data['total_point']); ?></span></div></a>
				<?php } ?>
				<?php
				# 全無料
				if(!empty($mail_status['all'])){
				?>
				<div class="mail-status-check" name="all"><img src="/images/icon/icon-free-all.png" /></div>
				<?php
				# 送信無料
				}elseif(!empty($mail_status['send'])){
				?>
				<div class="mail-status-check" name="send"><img src="/images/icon/icon-free-send.png" /></div>
				<?php
				# 開封無料
				}elseif(!empty($mail_status['free'])){
				?>
				<div class="mail-status-check" name="read"><img src="/images/icon/icon-free-read.png" /></div>
				<?php } ?>
			<?php } ?>
		</section>


		<?php
		/*************************************
		**
		**	CONTENTS MODAL / iOS
		**	==================================
		**	アイテムメニュー iOS
		**
		**************************************/
		if($os_number == 1){
		?>

			<div class="md-modal md-effect-8" id="modal-0002">
				<div class="md-content">
					<div class="modal-box">
						<div class="modal-bottom"><a class="md-close"><img src="/images/smart/button/button-close.png" /></a></div>
						<div class="clear"></div>
						<div class="modal-title"><img src="/images/smart/title/title-menu.png" /></div>
						<div class="modal-list">
							<ul>
								<li><a href="/main/"><img src="/images/smart/button/button-home.png" /></a></li>
								<li><a href="/character/"><img src="/images/smart/button/button-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
								<li><a href="/gacha/"><img src="/images/smart/button/button-gacha.png" /></a></li>
								<li><a href="/shop/"><img src="/images/smart/button/button-shop.png" /></a></li>
							</ul>
							<ul>
								<li><a href="/presentbox/"><img src="/images/smart/button/button-present.png" /></a><?php if(!empty($newarrival_present_count)){ ?><p class="check-present"><?php print($newarrival_present_count); ?></p><?php } ?></li>
								<li><a href="/mypage/"><img src="/images/smart/button/button-mypage.png" /></li>
								<li><a href="/album/"><img src="/images/smart/button/button-album.png" /></li>
								<li><a href="/information/help/"><img src="/images/smart/button/button-help.png" /></li>
							</ul>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>


			<div class="md-modal md-effect-8" id="modal-0003">
				<div class="md-content">
					<div class="modal-box">
						<div class="modal-bottom"><a class="md-close"><img src="/images/smart/button/button-close.png" /></a></div>
						<div class="clear"></div>
						<div class="modal-title"><img src="/images/smart/title/title-ticket.png" /></div>
						<div class="modal-contents">
							<div class="modal-frame user-point"><?php print(TICKET_NAME_USER); ?> × <span id="user-point"><?php print($members_data['user_point']); ?></span>枚</div>
							<div class="modal-frame free-point"><?php print(TICKET_NAME_FREE); ?> × <span id="free-point"><?php print($members_data['f_point']); ?></span>枚</div>
							<div class="modal-button"><a href="/shop/ticket/"><img src="/images/smart/button/button-buypoint.png" /></a></div>
							<div class="clear"></div>
						</div>
						<div class="modal-under"></div>
					</div>
				</div>
			</div>

			<?php /* <div class="md-overlay"></div> */ ?>

			<script src="/plugins/ModalWindowEffects/js/classie.js"></script>
			<script src="/plugins/ModalWindowEffects/js/modalEffects.js"></script>


		<?php
		/*************************************
		**
		**	CONTENTS MODAL / ANDROID
		**	==================================
		**	アイテムメニュー ANDROID
		**
		**************************************/
		}elseif($os_number == 2){
		?>

			<div id="menu-box">
				<div id="menu-list" class="modal-box">
					<div class="modal-title"><img src="/images/smart/title/title-menu.png" /></div>
					<div class="modal-list">
						<ul>
							<li><a href="/main/"><img src="/images/smart/button/button-home.png" /></a></li>
							<li><a href="/character/"><img src="/images/smart/button/button-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
							<li><a href="/gacha/"><img src="/images/smart/button/button-gacha.png" /></a></li>
							<li><a href="/shop/"><img src="/images/smart/button/button-shop.png" /></a></li>
						</ul>
						<ul>
							<li><a href="/presentbox/"><img src="/images/smart/button/button-present.png" /></a><?php if(!empty($newarrival_present_count)){ ?><p class="check-present"><?php print($newarrival_present_count); ?></p><?php } ?></li>
							<li><a href="/mypage/"><img src="/images/smart/button/button-mypage.png" /></li>
							<li><a href="/album/"><img src="/images/smart/button/button-album.png" /></li>
							<li><a href="/information/help/"><img src="/images/smart/button/button-help.png" /></li>
						</ul>
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<div id="ticket-box">
				<div id="ticket-area" class="modal-box">
					<div class="modal-title"><img src="/images/smart/title/title-ticket.png" /></div>
					<div class="modal-contents">
						<div class="modal-frame user-point"><?php print(TICKET_NAME_USER); ?> × <span id="user-point"><?php print($members_data['user_point']); ?></span>枚</div>
						<div class="modal-frame free-point"><?php print(TICKET_NAME_FREE); ?> × <span id="free-point"><?php print($members_data['f_point']); ?></span>枚</div>
						<div class="modal-button"><a href="/shop/ticket/"><img src="/images/smart/button/button-buypoint.png" /></a></div>
						<div class="clear"></div>
					</div>
					<div class="modal-under"></div>
				</div>
			</div>

		<?php } ?>


		<?php
		/********************************
		**
		**	コントロールボタン
		**
		*********************************/
		?>
		<section id="controll-area" class="front out">
			<div class="controll-button">
				<a href="javascript:void(0)" class="mail-frame-zoomin"><img src="/images/smart/button/button-zoomin.png" /></a>
				<a href="javascript:void(0)" class="front-hide"><img src="/images/smart/button/button-hide.png" /></a>
				<div class="clear"></div>
			</div>
		</section>


		<?php
		/********************************
		**
		**	メールエリア
		**
		*********************************/
		?>
		<section name="mail-area" id="mail-area" class="front">

			<?php
			/********************************
			**
			**	メールログ
			**
			*********************************/
			?>
			<div id="mail-frame">

				<div id="add-area"><img src="/images/loading.gif" class="loading" /></div>

				<?php
				if(!empty($mail_list['id'])){
				?>

					<?php
					$count	= count($mail_list['id']);
					for($i=0;$i<$count;$i++){
					?>

						<?php
						# キャラのメール
						if($mail_list['send_type'][$i] == 1){

							# 未読チェック チケット消費確認ダイアログ
							if(MAIL_READ_CONFIRM == "ON"){
								if($members_data['confirmation'] == 0 || $members_data['confirmation'] == 2){
									$name		= " name=\"read-confirm-on\"";
								}else{
									$name		= " name=\"read-confirm-off\"";
								}
							}else{
								$name			= " name=\"read-confirm-off\"";
							}

							# 画像あり
							if(!empty($mail_list['media'][$i] && $mail_list['media_flg'][$i] == 1)){
								$class			= " mail-image";
							}else{
								$class			= NULL;
							}

						?>
							<div class="chara-mail" id="<?php print($mail_list['id'][$i]); ?>">
								<?php
								# 未読
								if($mail_list['recv_flg'][$i] == 1){
								?>
								<div id="read-space-<?php print($mail_list['id'][$i]); ?>" class="read-space"></div>
								<div class="mail-title read-check<?php print($class); ?>" id="mails-id-<?php print($mail_list['id'][$i]); ?>"<?php print($name); ?>>
									<span class="unread">未読</span><br />
									<div class="mail-inner"><?php print($mail_list['title'][$i]); ?><span class="display-read">【続きを読む】</span></div>
									<?php if(!empty($mail_list['media'][$i] && $mail_list['media_flg'][$i] == 1)){ ?>
									<div class="image-unread">【画像あり】</div>
									<?php } ?>
								</div>
								<?php
								# 既読
								}else{
								?>
								<div class="mail-contents">
									<span class="read">既読</span><br />
									<div class="mail-inner"><?php print($mail_list['title'][$i]); ?><?php print nl2br($mail_list['message'][$i]); ?></div>
									<?php if(!empty($mail_list['media'][$i] && $mail_list['media_flg'][$i] == 1)){ ?>
									<div class="image-read" id="mails-image-<?php print($mail_list['id'][$i]); ?>">【画像を見る】</div>
									<?php } ?>
								</div>
								<?php } ?>
							</div>
						<?php
						# ユーザーのメール
						}else{
						?>
							<div class="user-mail">
							<?php print nl2br($mail_list['message'][$i]); ?>
							</div>
						<?php } ?>

					<?php } ?>

					<?php
					# もっと読むボタン
					if(!empty($more_button)){
					?>
						<div class="clear"></div>
						<div id="more-area"></div>
						<div id="more-read"  class="first-more-button">
							<a id="more-button" class="first-more-button"><img src="/images/smart/button/button-more.png" /></a>
						</div>
					<?php } ?>

					<div class="clear"></div>

				<?php
				}else{
				?>

					<div id="exection">早速メールしてみよう！</div>
					<div class="clear"></div>


				<?php } ?>


				<?php
				/********************************
				**
				**	ここ重要
				**	============================
				**	キャラからのメールが一通も無かった場合
				**	自動メールチェックを強制的に走らせる
				**
				**
				*********************************/
				if(!empty($last_mail_id)){
				?>
				<div style="display: none;">
					<div class="chara-mail" id="<?php print($last_mail_id); ?>" style="display: none;"></div>
				</div>
				<?php } ?>

			</div>

		</section>

	</div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

