

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">お客様アクセス情報</div>

	<form action ="/<?php print($directory); ?>/third/" method="post">
	<input type="hidden" name="user_id" value="<?php print($_POST['user_id']); ?>" />
	<input type="hidden" name="user_device" value="<?php print($user_device); ?>" />
	<input type="hidden" name="user_session" value="<?php print($user_session); ?>" />
	<input type="hidden" name="user_ip" value="<?php print($user_ip); ?>" />
	<input type="hidden" name="user_browser" value="<?php print($user_browser['browser']); ?>" />
		<input type="hidden" name="file_name" value="<?php print($_POST['file_name']); ?>" />
	    <div id="form">
			<?php if(!empty($error)){ ?>
				<div id="warning">
					<?php print($error); ?><br />
					上記のエラーが見つかりました。<br />
				</div>
			<?php }else{ ?>
				<div id="check-comment">最後に、お客様情報を確認致しますので次へ、ボタンを押して下さい。</div>
			<?php } ?>

			<ul>
				<li>
					<p>お客様にじよめID</p>
					<label><?php print($_POST['user_id']); ?></label>
				</li>
				<li>
					<p>アクセスデバイス</p>
					<label>前ページ : <?php print($_POST['user_device']); ?><br />現ページ : <?php print($user_device); ?></label>
					<?php if(!empty($device_check)){ ?>
						<div style="color: #FF0000;">こちら、アクセスしたデバイスに不整合があります。デバイス偽装等行っていないかご確認下さい。</div>
					<?php } ?>
				</li>
				<li>
					<p>セッション</p>
					<label>前ページ : <?php print($_POST['user_session']); ?><br />現ページ : <?php print($user_session); ?></label>
					<?php if(!empty($session_check)){ ?>
						<div style="color: #FF0000;">こちら、ブラウザにセッションが保持されておりません。ご利用のブラウザの設定にてクッキーの利用ができるようご変更下さい。</div>
					<?php } ?>
				</li>
				<li>
					<p>アクセスIP</p>
					<label>前ページ : <?php print($_POST['user_ip']); ?><br />現ページ : <?php print($user_ip); ?></label>
					<?php if(!empty($ip_check)){ ?>
						<div style="color: #FF0000;">こちら、アクセス毎のIPに不整合があります。ご利用のネットワーク環境をご確認下さい。</div>
					<?php } ?>
				</li>
				<li>
					<p>ブラウザ情報</p>
					<label>前ページ : <?php print($_POST['user_browser']); ?><br />現ページ : <?php print($user_browser['browser']); ?></label>
					<?php if(!empty($brower_check)){ ?>
						<div style="color: #FF0000;">こちら、アクセス毎のブラウザの情報に不整合があります。ブラウザ偽装を行っていないかご確認下さい。</div>
					<?php } ?>
				</li>
				<?php if(empty($error)){ ?>
					<li><input type="submit" id="button-next" value="&nbsp;" /></li>
				<?php } ?>
			</ul>
		</div>
	</form>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

