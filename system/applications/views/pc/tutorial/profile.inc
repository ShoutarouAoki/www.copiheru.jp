<?php
/****************************
**
**	fullPage.js plugin
**
*****************************/
?>
<link rel="stylesheet" href="/plugins/fullPage-master/jquery.fullPage.css" />
<script src="/plugins/fullPage-master/vendors/jquery.easings.min.js"></script>
<script src="/plugins/fullPage-master/jquery.fullPage.min.js"></script>


<?php
/****************************
**
**	このページでのスクリプト
**
*****************************/
?>
<script src="/common/js/tutorial.script.js"></script>
<script src="/common/js/tutorial.function.js"></script>

<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link rel="stylesheet" href="/common/css/pc/tutorial.css" />
<link rel="stylesheet" href="/common/css/pc/index.css" />

<script type="text/javascript">
	nickname_max_length		= <?php print(NICKNAME_MAX_LENGTH); ?>;
	message_max_length		= <?php print(PROFILE_MESSAGE_MAX_LENGTH); ?>;
	constructDefine(nickname_max_length,message_max_length);
</script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="profile-title"><img src="/images/pc/title/title-profile.png" /></div>
    <div id="form">
		<ul>
			<li>
				<p>ニックネーム<span>(<?php print(NICKNAME_MAX_LENGTH); ?>文字まで)</span><span class="style-red">＊</span></p>
				<label><input type="text" name="nickname" id="nickname" maxlength="<?php print(NICKNAME_MAX_LENGTH); ?>" /></label>
			</li>

			<li>
				<p>誕生日<span class="style-red">＊</span></p>
				<div class="month-day-select">
					<label>
						<select name="month" id="month">
							<?php print($month_option); ?>
						</select>
					</label>
				</div>
				<div class="month-day-select">
					<label>
						<select name="day" id="day">
							<?php print($day_option); ?>
						</select>
					</label>
				</div>
				<div class="clear"></div>
				<span class="style-red">※あとから変更できませんのでご注意下さい</span>
			</li>

			<li>
				<p>自己紹介<span>(<?php print(PROFILE_MESSAGE_MAX_LENGTH); ?>文字まで)</span></p>
				<textarea name="message" id="message" maxlength="<?php print(PROFILE_MESSAGE_MAX_LENGTH); ?>"></textarea>
				<div class="form-comment">残り文字数 <span id="text-length"><?php print(PROFILE_MESSAGE_MAX_LENGTH); ?></span> 文字</div>
			</li>

			<li><div id="send" class="image-center"><a href="javascript:void(0)"><img src="/images/pc/button/button-profile-edit.png" style="width: 250px;" /></a></div></li>
		</ul>
	</div>





<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

