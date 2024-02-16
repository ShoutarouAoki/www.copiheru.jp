<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">プロフィール編集</div>

	<?php
	if(!empty($error)){
	?>
		<div class="container-box">
			<div id="warning">
				<?php print($errormessage); ?>
			</div>
		</div>
	<?php } ?>

	<?php
	if(!empty($exection)){
	?>
		<div class="container-box">
			<div id="exection">
			更新しました
			</div>
		</div>
	<?php } ?>

	<form action="./" method="post">
	<input type="hidden" name="edit" value="1" />
    <section id="form">
		<ul>
			<li>
				<p>ニックネーム<span>(<?php print(NICKNAME_MAX_LENGTH); ?>文字まで)</span></p>
				<label><input type="text" name="nickname" id="nickname" maxlength="<?php print(NICKNAME_MAX_LENGTH); ?>" value="<?php print($nickname); ?>" /></label>
			</li>

			<li>
				<p>誕生日&nbsp;<span class="style-red">※誕生日は変更できません</span></p>
				<?php print($birthday); ?>
			</li>

			<li>
				<p>自己紹介<span>(<?php print(PROFILE_MESSAGE_MAX_LENGTH); ?>文字まで)</span></p>
				<textarea name="message" id="message" maxlength="<?php print(PROFILE_MESSAGE_MAX_LENGTH); ?>"><?php print($message); ?></textarea>
				<div class="form-comment">残り文字数 <span id="text-length"><?php print(PROFILE_MESSAGE_MAX_LENGTH); ?></span> 文字</div>
			</li>
		</ul>
		<div class="submit-area"><input type="submit" id="edit-button" value="&nbsp;" onClick="return confirm('この内容でよろしいですか？');" /></div>
	</section>
	</form>

	<section id="list-menu">
		<ul>
			<li><a href="/mypage/edit/">プロフィール編集</a></li>
			<li><a href="javascript:void(0)" class="ticket-menu" data-tor-smoothScroll="noSmooth">所持<?php print(TICKET_NAME); ?></a></li>
			<li><a href="javascript:void(0)" class="item-menu" data-tor-smoothScroll="noSmooth">所持アイテム一覧</a></li>
			<li><a href="/album/">開放シーン</a></li>
		</ul>
	</section>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

<?php
/*************************************
**
**	ポイント / アイテム 共通モーダルファイル
**
**************************************/
# 購入ボタン出す
$modal_shop_buy		= 1;
include_once(dirname(__FILE__)."/../modal.inc");
?>

