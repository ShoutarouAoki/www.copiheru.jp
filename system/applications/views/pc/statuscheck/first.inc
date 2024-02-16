

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">お客様アクセス情報</div>

	<?php if(!empty($error)){ ?>

		<div class="container-box">
			<div id="warning">
				<?php print($error); ?>
			</div>
			<div class="image-center"><a href="/<?php print($directory); ?>/" target="_top"><img src="/images/pc/button/button-return-top.png" style="width: 250px; height: 60px;" /></a></div>
		</div>

	<?php }else{ ?>

		<form action ="/<?php print($directory); ?>/second/" method="post">
		<input type="hidden" name="user_id" value="<?php print($_POST['user_id']); ?>" />
		<input type="hidden" name="user_device" value="<?php print($user_device); ?>" />
		<input type="hidden" name="user_session" value="<?php print($user_session); ?>" />
		<input type="hidden" name="user_ip" value="<?php print($user_ip); ?>" />
		<input type="hidden" name="user_browser" value="<?php print($user_browser['browser']); ?>" />
		<input type="hidden" name="file_name" value="<?php print($file_name); ?>" />
		    <div id="form">
				<div id="check-comment">下記情報を確認し、再度『次へ』を押して下さい</div>
				<ul>
					<li>
						<p>お客様にじよめID</p>
						<label><?php print($_POST['user_id']); ?></label>
					</li>
					<li>
						<p>アクセスデバイス</p>
						<label><?php print($user_device); ?></label>
					</li>
					<li>
						<p>現在のセッション</p>
						<label><?php print($user_session); ?></label>
					</li>
					<li>
						<p>アクセスIP</p>
						<label><?php print($user_ip); ?></label>
					</li>
					<li>
						<p>ブラウザ情報</p>
						<label><?php print($user_browser['browser']); ?></label>
					</li>
					<li><input type="submit" id="button-next" value="&nbsp;" /></li>
				</ul>
			</div>
		</form>

	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

