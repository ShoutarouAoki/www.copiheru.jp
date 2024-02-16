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
	message_max_length		= <?php print(PROFILE_MESSAGE_MAX_LENGTH); ?>;
	constructDefine(message_max_length);
</script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div id="tutorial-area">

		<div class="tutorial-frame">
			<div id="tutorial-image"><img src="<?php print($result['image']); ?>" /></div>
				<div id="previous" class="next-previous-button" name="<?php print($previous_number); ?>"><img src="/images/icon/icon-left-arrow.png" /></div>
			<?php if(isset($next_number)){ ?>
				<div id="next" class="next-previous-button" name="<?php print($next_number); ?>"><img src="/images/icon/icon-right-arrow.png" /></div>
			<?php } ?>
			<div id="skip">
				<?php if($members_data['tutorial'] == 1){ ?>
					<a href="/main/"><img src="/images/pc/button/button-return.png" /></a>
				<?php }else{ ?>
					<a href="/tutorial/profile/"><img src="/images/pc/button/button-skip.png" /></a>
				<?php } ?>
			</div>
			<div id="tutorial-end">
				<?php if($members_data['tutorial'] == 1){ ?>
					<a href="/main/"><img src="/images/pc/button/button-return.png" /></a>
				<?php }else{ ?>
					<a href="/tutorial/profile/"><img src="/images/pc/button/button-profile-edit.png" /></a>
				<?php } ?>
			</div>
		</div>

	</div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

