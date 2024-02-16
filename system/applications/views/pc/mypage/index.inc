<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">マイページ</div>

	<section id="mypage">
		<div id="user-image"><img src="<?php print($image_url); ?>" /></div>
		<div id="user-data">
			<div id="user-data-contents">
				<div class="profile-line">
					<span>【ニックネーム】</span><br />
					<?php print($members_data['nickname']); ?><br />
				</div>
				<div class="profile-line">
					<span>【誕生日】</span><br />
					<?php print($birthday); ?><br />
				</div>
				<div class="profile-line">
					<span>【自己紹介】</span><br />
					<?php print($userinfos_data['message']); ?><br />
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</section>

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