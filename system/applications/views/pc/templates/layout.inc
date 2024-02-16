<?php
/************************************************
**
**	INCLUED HTML HEADER
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
include_once(dirname(__FILE__)."/html_header.inc");
?>



<?php
/************************************************
**
**	LOADING / SCREEN OVERLAY
**
************************************************/
?>

<?php /* LOADING */ ?>
<div id="loading"><img src="/images/loading.gif" /></div>
<?php /* LOADING */ ?>

<?php /* MODAL OVERLAY SCREEN */ ?>
<div id="modal-overlay"></div>
<?php /* MODAL OVERLAY SCREEN */ ?>

<?php /* DEFAULT DIALOG */ ?>
<div id="dialog-box"></div>
<?php /* DEFAULT DIALOG */ ?>

<?php /* ERROR DIALOG */ ?>
<div id="dialog-error"></div>
<?php /* ERROR DIALOG */ ?>

<?php /* MAIL PAGE SCREEN */ ?>
<div id="screen-overlay">
	<div class="zoomout-button">
		<a href="javascript:void(0)" class="mail-frame-zoomout-pc"><img src="/images/smart/button/button-zoomout.png" class="button-zoomout" /></a>
		<div class="clear"></div>
	</div>
</div>
<?php /* MAIL PAGE SCREEN */ ?>

<div id="top"></div>

<div id="wrapper">


	<?php
	/************************************************
	**
	**	HEADER
	**	---------------------------------------------
	**	HTML VIEWS FILE READING
	**
	************************************************/
	?>
	<?php include_once(dirname(__FILE__)."/header.inc"); ?>


	<?php
	/************************************************
	**
	**	NAVIGATION
	**	---------------------------------------------
	**	NAVIGATION AREA
	**
	************************************************/
	?>
	<div id="navigationArea">

		<?php if($directory == "mail" && $data['page'] == "detail"){ ?>

			<section>
				<ul>
					<li><a href="/main/"><img src="/images/pc/button/button-home.png" /></a></li>
					<li><a href="/character/"><img src="/images/pc/button/button-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
					<li><a href="/gacha/"><img src="/images/pc/button/button-gacha.png" /></a></li>
					<li><a href="/shop/"><img src="/images/pc/button/button-shop.png" /></a></li>
				</ul>
				<ul>
					<li id="present-menu"><a href="/presentbox/"><img src="/images/pc/button/button-present.png" /></a><?php if(!empty($newarrival_present_count)){ ?><p class="check-present" id="present-unit"><?php print($newarrival_present_count); ?></p><?php } ?></li>
					<li><a href="/ranking/"><img src="/images/pc/button/button-ranking.png" /></a></li>
					<li><a href="/album/"><img src="/images/pc/button/button-album.png" /></a></li>
					<li><a href="/exchange/"><img src="/images/pc/button/button-exchange.png" /></a></li>
				</ul>
				<div class="clear"></div>
			</section>


		<?php }else{ ?>
			<section>
				<ul>
					<li><a href="/main/"><img src="/images/pc/button/button-home.png" /></a></li>
					<li><a href="/character/"><img src="/images/pc/button/button-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
					<li><a href="/gacha/"><img src="/images/pc/button/button-gacha.png" /></a></li>
					<li><a href="/shop/"><img src="/images/pc/button/button-shop.png" /></a></li>
				</ul>
				<ul>
					<li id="present-menu"><a href="/presentbox/"><img src="/images/pc/button/button-present.png" /></a><?php if(!empty($newarrival_present_count)){ ?><p class="check-present" id="present-unit"><?php print($newarrival_present_count); ?></p><?php } ?></li>
					<li><a href="/ranking/"><img src="/images/pc/button/button-ranking.png" /></a></li>
					<li><a href="/album/"><img src="/images/pc/button/button-album.png" /></a></li>
					<li><a href="/exchange/"><img src="/images/pc/button/button-exchange.png" /></a></li>
				</ul>
				<ul>
					<li><a href="/event/"><img src="/images/pc/button/button-event.png" /></a></li>
					<li><a href="/news/"><img src="/images/pc/button/button-news.png" /></a></li>
					<?php if($directory == "mail" && $data['page'] == "detail"){ ?>
						<li><a href="javascript:void(0)" class="ticket-menu-mail" data-tor-smoothScroll="noSmooth"><img src="/images/pc/button/button-tickethome.png" /></a></li>
						<li><a href="javascript:void(0)" class="item-menu-mail" data-tor-smoothScroll="noSmooth"><img src="/images/pc/button/button-itemlist.png" /></a></li>
					<?php }else{ ?>
						<li><a href="javascript:void(0)" class="ticket-menu" data-tor-smoothScroll="noSmooth"><img src="/images/pc/button/button-tickethome.png" /></a></li>
						<li><a href="javascript:void(0)" class="item-menu" data-tor-smoothScroll="noSmooth"><img src="/images/pc/button/button-itemlist.png" /></a></li>
					<?php } ?>
				</ul>
				<ul>
					<li><a href="/mypage/"><img src="/images/pc/button/button-mypage.png" /></a></li>
					<li><a href="/information/guide/"><img src="/images/pc/button/button-guide.png" /></a></li>
					<li><a href="/tutorial/"><img src="/images/pc/button/button-tutorial.png" /></a></li>
					<li><a href="/information/help/"><img src="/images/pc/button/button-help.png" /></a></li>
				</ul>
				<div class="clear"></div>
			</section>
			<div id="navigationFrameArea"><iframe name="navigationFrame" height="100%" id="navigationFrame" src="/ranking/frame/" scrolling="no">ブラウザの互換性がありません</iframe></div>
		<?php } ?>

	</div>
	<?php /*** NAVIGATION AREA END ***/ ?>


	<?php
	/************************************************
	**
	**	MAIN
	**	---------------------------------------------
	**	MAIN AREA
	**
	************************************************/
	?>
	<div id="mainArea">

		<?php /* START FADE SCREEN */ ?>
		<div id="screen"></div>
		<?php /* START FADE SCREEN */ ?>


		<?php
		/************************************************
		**
		**	INCLUED CONTENTS COLUMN
		**	---------------------------------------------
		**	HTML VIEWS FILE READING
		**
		************************************************/
		include_once($view_directory);
		?>


		<?php
		/*************************************
		**
		**	ポイント / アイテム 共通モーダルファイル
		**
		**************************************/
		?>
		<?php if($directory != "mail" || $data['page'] != "detail"){ ?>
		<?php
		include_once(dirname(__FILE__)."/../modal.inc");
		?>
		<?php } ?>

	</div>
	<?php /*** MAIN AREA END ***/ ?>

	<div class="clear"></div>


	<?php
	/************************************************
	**
	**	INCLUED FOOTER
	**	---------------------------------------------
	**	HTML VIEWS FILE READING
	**
	************************************************/
	?>
	<?php include_once(dirname(__FILE__)."/footer.inc"); ?>

</div>
<?php /*** WRAPPER END ***/ ?>



<script>
gadgets.window.adjustHeight();
</script>
