<header>

	<?php
	/****************************************
	**
	**	INDEX / ONLY SUB HEADER
	**
	****************************************/
	if(!empty($sub_header)){
	?>

		<div id="top"><img src="/images/smart/header.png" /></div>

	<?php
	/****************************************
	**
	**	OTHER
	**
	****************************************/
	}else{
	?>

		<?php
		# MENU 非表示フラグ
		if(empty($header_hide)){
		?>

			<link rel="stylesheet" type="text/css" href="/common/css/smart/component.css" />
			<script src="/plugins/SlidePushMenus/js/classie.js"></script>

			<?php
			/*************************************
			**
			**	LIST 2
			**
			**************************************/
			?>
			<div class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right" id="cbp-spmenu-s1">
				<h3>
				メニュー
				<div class="navClose"><a id="closeRight"><img src="/images/smart/button/button-close.png" /></a></div>
				</h3>
				<div class="slide-single-short">
				<a href="/mypage/">マイページ</a>
				<a href="/album/">マイアルバム</a>
				<a href="/rewards/">貢献度報酬</a>
				<a href="/key/">指名権交換所</a>
				<a href="/exchange/">アルバム交換所</a>
				<a href="/benefits/">特典コード</a>
				<a href="/news/">お知らせ</a>
				<a href="/information/guide/">遊び方ガイド</a>
				<a href="/tutorial/">チュートリアル</a>
				<a href="/information/help/">HELP</a>
				</div>
			</div>


			<?php
			/*************************************
			**
			**	NAVIGATION
			**
			**************************************/
			?>
			<div class="navigation-line"></div>
			<div id="header-navigation">
				<ul>
					<li><a href="/main/"><img src="/images/smart/menu/menu-home.png" /></a></li>
					<li><a href="/character/"><img src="/images/smart/menu/menu-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
					<li><a href="/gacha/"><img src="/images/smart/menu/menu-gacha.png" /></a></li>
					<li><a href="/shop/"><img src="/images/smart/menu/menu-shop.png" /></a></li>
					<li><a href="/presentbox/"><img src="/images/smart/menu/menu-present.png" /><?php if(!empty($newarrival_present_count)){ ?><p class="check-present" id="present-unit"><?php print($newarrival_present_count); ?></p><?php } ?></a></li>
					<li><a id="showRight"><img src="/images/smart/menu/menu-navi.png" /></a></li>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="navigation-line"></div>
			<script>
				var menuRight = document.getElementById( 'cbp-spmenu-s1' ),
					showRight = document.getElementById( 'showRight' ),
					body = document.body;
				showRight.onclick = function() {
					classie.toggle( this, 'active' );
					classie.toggle( menuRight, 'cbp-spmenu-open' );
					disableOther( 'showRight' );
				};
				closeRight.onclick = function() {
					classie.toggle( this, 'active' );
					classie.toggle( menuRight, 'cbp-spmenu-open' );
					disableOther( 'showRight' );
				};
				function disableOther( button ) {
					if( button !== 'showRight' ) {
						classie.toggle( showRight, 'disabled' );
					}
				}
			</script>

		<?php } ?>

	<?php } ?>

</header>

