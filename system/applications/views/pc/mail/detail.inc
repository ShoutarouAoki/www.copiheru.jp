<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link href="/common/css/<?php print($default_device); ?>/mail.css" rel="stylesheet" type="text/css" />
<link href="/common/css/<?php print($default_device); ?>/modal.css" rel="stylesheet" type="text/css" />

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


<link href="/plugins/colorbox-master/css/colorbox.css" rel="stylesheet" />
<script src="/plugins/colorbox-master/js/jquery.colorbox.js" type="text/javascript"></script>



<?php
/*************************************
**
**	デバッグ用
**
**************************************/
$colorbox_width		= "460";
?>


<?php
/*************************************
**
**	jQuery / Ajax
**
**************************************/
?>
<script type="text/javascript">

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
	var sleep_timer	= 600000;
	setInterval("checkSleep()",sleep_timer);

	<?php /* 新着メールチェック間隔 20秒 */ ?>
	<?php if($character_data['open_flg'] <= 2){ ?>
	var check_timer	= 20000;
	setInterval("checkNewMessage()",check_timer);
	<?php } ?>

</script>


<?php
/*************************************
**
**	追加CSS
**
**************************************/
?>
<style>
#message::-webkit-input-placeholder::after {
	display: block;
	content: "最大<?php print(MAIL_MESSAGE_MAX_LENGTH); ?>文字";
}
</style>

<?php
/********************************
**
**	メール添付特別プレゼントエリア
**
*********************************/
?>
<div id="dialog-mail-presents"></div>

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
			<a href="javascript:void(0)" class="front-show"><img src="/images/pc/button/button-show.png" class="button-show" /></a>
			<div class="clear"></div>
		</section>


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
		if($character_data['open_flg'] <= 2){
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
		<? } ?>


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

			<?php if($character_data['open_flg'] <= 2){ ?>
				<a href="javascript:void(0)" id="item-button" data-tor-smoothScroll="noSmooth"><div id="item-using-check"><?php if(!empty($item_using)){ ?><span class="item-using-check">使用中</span><?php } ?></div><img src="/images/pc/button/button-item.png" /></a>
			<?php } ?>

			<?php /* <a href="javascript:void(0)" id="menu-button" data-tor-smoothScroll="noSmooth"><img src="/images/pc/button/button-menu.png" /></a> */ ?>

			<?php if(empty($mail_status['free'])){ ?>
				<a href="javascript:void(0)" id="ticket-button" data-tor-smoothScroll="noSmooth"><div id="menu-ticket"><img src="/images/pc/button/button-ticket.png" /><span class="check-point"><?php print($members_data['total_point']); ?></span></div></a>
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
		**	CONTENTS MODAL / PC
		**	==================================
		**	アイテムメニュー PC
		**
		**************************************/
		?>

		<?php if($character_data['open_flg'] <= 2){ ?>
			<div id="item-box">
				<div id="item-use" class="modal-box">
					<div class="modal-title"><img src="/images/pc/title/title-itembox.png" /></div>
					<?php
					$count			= 0;
					$count_none		= 0;
					if(isset($item_list['id'])){
						$count		= count($item_list['id']);
					}
					if(isset($item_list_nouse['id'])){
						$count_none	= count($item_list_nouse['id']);
					}
					if($count > 0 || $count_none > 0){
					?>
						<div class="modal-list">
							<ul>
							<?php
							/*************************************
							**
							**	使えるアイテム
							**
							**************************************/
							for($i=0;$i<$count;$i++){
								$itembox_id			= $item_list['id'][$i];
								$item_id			= $item_list['item_id'][$i];
								$item_count			= $item_list['unit'][$i];
								if($item_count > 0){
									# 使用中
									if(isset($itemuse_list[$item_id]['id'])){
										$class		= "item-list-using";
										$name		= " name=\"using\"";
									# 使用可能
									}else{
										$class		= "item-list";
										$name		= NULL;
									}
								}else{
									# 使用中
									if(isset($itemuse_list[$item_id]['id'])){
										$class		= "item-list-using item-last";
										$name		= " name=\"using\"";
									# 使用不可
									}else{
										$class		= "item-list-none";
										$name		= NULL;
									}
								}
							?>
								<li class="<?php print($class); ?>" id="item-id-<?php print($itembox_id); ?>"<?php print($name); ?>>
									<?php if(!empty($item_list['image'][$i])){ ?>
									<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list['image'][$i]); ?>" />
									<?php } ?>
									<span class="item-name"	id="item-name-<?php print($itembox_id); ?>"><?php print($item_list['name'][$i]); ?></span>
									<span class="item-description" id="item-description-<?php print($itembox_id); ?>"><?php print($item_list['description'][$i]); ?></span>
									<?php if(!empty($item_count)){ ?>
										<p class="check-count" id="item-num-<?php print($itembox_id); ?>"><?php print($item_count); ?></p>
									<?php } ?>
									<div class="item-using-area" id="item-use-<?php print($itembox_id); ?>">
										<?php if(isset($itemuse_list[$item_id]['id'])){ ?>
										<p class="item-using">現在使用中</p>
										<?php } ?>
									</div>
								</li>
							<?php } ?>

							<?php
							/*************************************
							**
							**	使えないアイテム
							**
							**************************************/
							for($i=0;$i<$count_none;$i++){
								$itembox_id		= $item_list_nouse['id'][$i];
								$item_id		= $item_list_nouse['item_id'][$i];
								$item_count		= $item_list_nouse['unit'][$i];
							?>
								<li class="item-list-nouse" id="item-id-<?php print($itembox_id); ?>">
									<?php if(!empty($item_list_nouse['image'][$i])){ ?>
									<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list_nouse['image'][$i]); ?>" />
									<?php } ?>
									<span class="item-name"	id="item-name-<?php print($itembox_id); ?>"><?php print($item_list_nouse['name'][$i]); ?></span>
									<span class="item-description" id="item-description-<?php print($itembox_id); ?>"><?php print($item_list_nouse['description'][$i]); ?></span>
									<?php if(!empty($item_count)){ ?>
										<p class="check-count" id="item-num-<?php print($itembox_id); ?>"><?php print($item_count); ?></p>
									<?php } ?>
									<p class="item-nouse">ここでは使えません</p>
								</li>
							<?php } ?>
							</ul>
						</div>
					<?php }else{ ?>
						<div id="no-contents">所持アイテムはありません</div><br /><br />
					<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
		<?php } ?>

		<?php /*
		<div id="menu-box">
			<div id="menu-list" class="modal-box">
				<div class="modal-title"><img src="/images/pc/title/title-menu.png" /></div>
				<div class="modal-list">
					<ul>
						<li><a href="/main/"><img src="/images/pc/button/button-home.png" /></a></li>
						<li><a href="/character/"><img src="/images/pc/button/button-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
						<li><a href="/gacha/"><img src="/images/pc/button/button-gacha.png" /></a></li>
						<li><a href="/shop/"><img src="/images/pc/button/button-shop.png" /></a></li>
					</ul>
					<ul>
						<li id="present-menu"><a href="/presentbox/"><img src="/images/pc/button/button-present.png" /></a><?php if(!empty($newarrival_present_count)){ ?><p class="check-present"><?php print($newarrival_present_count); ?></p><?php } ?></li>
						<li><a href="/ranking/"><img src="/images/pc/button/button-ranking.png" /></a></li>
						<li><a href="/album/"><img src="/images/pc/button/button-album.png" /></a></li>
						<li><a href="/information/help/"><img src="/images/pc/button/button-help.png" /></a></li>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		*/ ?>

		<div id="ticket-box">
			<div id="ticket-area" class="modal-box">
				<div class="modal-title"><img src="/images/pc/title/title-ticket.png" /></div>
				<div class="modal-contents">
					<div class="modal-frame-user-point"><div class="modal-frame-point"><?php print(TICKET_NAME_USER); ?> × <span id="user-point"><?php print($members_data['user_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
					<div class="modal-frame-free-point"><div class="modal-frame-point"><?php print(TICKET_NAME_FREE); ?> × <span id="free-point"><?php print($members_data['f_point']); ?></span><?php print(TICKET_UNIT_NAME); ?></div></div>
					<div class="modal-button"><a href="/shop/list/point/"><img src="/images/pc/button/button-buypoint.png" /></a></div>
					<div class="clear"></div>
				</div>
				<div class="modal-under"></div>
			</div>
		</div>



		<?php if($character_data['open_flg'] <= 2){ ?>

			<?php /* アイテム利用確認ダイアログ */ ?>
			<div id="dialog-item-confirm"></div>


			<?php /* アイテム利用後ダイアログ */ ?>
			<div id="dialog-item-use"></div>

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
				<a href="javascript:void(0)" class="mail-frame-zoomin-pc"><img src="/images/pc/button/button-zoomin.png" /></a>
				<? /*<a href="javascript:void(0)" class="front-hide"><img src="/images/pc/button/button-hide.png" /></a> */ ?>
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
									<div class="mail-inner">
										<?php print($mail_list['title'][$i]); ?>
										<?php if(!empty($mail_list['present'][$i])){ ?>
											<span class="mail-point">【プレゼントあり】</span>
										<?php } ?>
										<span class="display-read">【続きを読む】</span>
									</div>
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
							<a id="more-button" class="first-more-button"><img src="/images/pc/button/button-more.png" /></a>
						</div>
					<?php } ?>

					<div class="clear"></div>

				<?php
				}else{
				?>

					<?php if($character_data['open_flg'] <= 2){ ?>
						<div id="exection">早速メールしてみよう！</div>
					<?php }else{ ?>
						<div id="exection">やりとりはありません</div>
					<?php } ?>
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

			<?php
			/********************************
			**
			**	送信フォーム
			**
			*********************************/
			if($character_data['open_flg'] <= 2){
			?>
				<div id="mail-form">
					<div id="message-area">
						<form method="post">
							<input type="hidden" name="message-max-length" id="message-max-length" value="<?php print(MAIL_MESSAGE_MAX_LENGTH); ?>" />
							<textarea name="message" id="message" maxlength="<?php print(MAIL_MESSAGE_MAX_LENGTH); ?>" placeholder="最大<?php print(MAIL_MESSAGE_MAX_LENGTH); ?>文字"></textarea>
							<div class="clear margin8"></div>
							<span class="length-area">残り文字数 <span id="text-length"><?php print(MAIL_MESSAGE_MAX_LENGTH); ?></span> 文字</span>
							<?php
							# 送信チェック チケット消費確認ダイアログ
							if(MAIL_SEND_CONFIRM == "ON"){
							?>
								<?php
								# membersのconfirmationで判別
								if($members_data['confirmation'] == 0 || $members_data['confirmation'] == 2){
								?>
									<input type="button" id="button-send" name="send-confirm-on" value="&nbsp;" />
								<?php }else{ ?>
									<input type="button" id="button-send" name="send-confirm-off" value="&nbsp;" />
								<?php } ?>
							<?php }else{ ?>
								<input type="button" id="button-send" name="send-confirm-off" value="&nbsp;" />
							<?php } ?>
							<div class="clear"></div>
						</form>
					</div>
				</div>
			<?php } ?>

			<div id="dialog-ticket">
				<? /* <?php print(TICKET_NAME); ?>を1枚消費しますが宜しいですか？<br /> */ ?>
				<?php
				# チケット消費確認ダイアログ 継続チェックする
				if(MAIL_CONFIRM_CHECK == "ON"){
				?>
					<span class="confirm-check"><label><input type="checkbox" name="confirm" value="1" />今後表示しない</label></span>
				<?php
				# 継続チェックしない(常に表示する)
				}else{
				?>
					<input type="hidden" name="confirm" value="0" />
				<?php } ?>
				<div class="clear"></div>
			</div>

		</section>

	</div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

