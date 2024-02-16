<footer>

	<?php
	/*************************************
	**
	**	INDEX / ONLY SUB FOOTER
	**
	**************************************/
	if(!empty($sub_footer)){
	?>

		<div id="footer-end"><img src="/images/smart/footer.png" /></div>

	<?php
	/****************************************
	**
	**	OTHER
	**
	****************************************/
	}else{
	?>

		<?php
		/*************************************
		**
		**	NAVIGATION
		**
		**************************************/
		# MENU 非表示フラグ
		if(empty($footer_hide)){
		?>

			<?php /*<div id="return"><a href="#top"><img src="/images/smart/return.png" /></a></div>*/ ?>
			<div id="return-top"><a href="#top"><img src="/images/smart/return-top.png" /></a></div>

			<div class="navigation-line"></div>
			<div id="footer-navigation">
				<ul>
					<li><a href="/main/"><img src="/images/smart/menu/menu-home.png" /></a></li>
					<li><a href="/character/"><img src="/images/smart/menu/menu-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
					<li><a href="/gacha/"><img src="/images/smart/menu/menu-gacha.png" /></a></li>
					<li><a href="/shop/"><img src="/images/smart/menu/menu-shop.png" /></a></li>
					<li><a href="/presentbox/"><img src="/images/smart/menu/menu-present.png" /><?php if(!empty($newarrival_present_count)){ ?><p class="check-present"><?php print($newarrival_present_count); ?></p><?php } ?></a></li>
					<li><a id="showRight2"><img src="/images/smart/menu/menu-navi.png" /></a></li>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="navigation-line"></div>
			<script>
				var menuRight2 = document.getElementById( 'cbp-spmenu-s1' ),
					showRight2 = document.getElementById( 'showRight2' ),
					body = document.body;
				showRight2.onclick = function() {
					classie.toggle( this, 'active' );
					classie.toggle( menuRight2, 'cbp-spmenu-open' );
					var Top = "top";
					nijiyome.ui({method: 'scroll', x: 0, y: Top.offsetTop});
					disableOther( 'showRight2' );
				};
				closeRight.onclick = function() {
					classie.toggle( this, 'active' );
					classie.toggle( menuRight2, 'cbp-spmenu-open' );
					disableOther( 'showRight2' );
				};
				function disableOther( button ) {
					if( button !== 'showRight2' ) {
						classie.toggle( showRight2, 'disabled' );
					}
				}
			</script>

			<div id="footer-end"><img src="/images/smart/footer.png" /></div>

		<?php } ?>

	<?php } ?>

</footer>
