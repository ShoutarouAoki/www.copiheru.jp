

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">お客様アクセス情報</div>

    <div id="form">
		<?php if(!empty($error)){ ?>
			<div id="warning">
				<?php print($error); ?><br />
				上記のエラーが見つかりました。<br />
			</div>
		<?php }else{ ?>
			<div id="check-comment">現状ではお客様の情報に不備は御座いませんので再度ゲームにアクセスして下さい。</div>
		<?php } ?>

		<ul>
			<li>
				<p>お客様にじよめID</p>
				<label><?php print($_POST['user_id']); ?></label>
			</li>
			<li>
				<p>にじよめデータ</p>
				<?php if(!empty($user_check)){ ?>
					<label><span style="color: #FF0000;">ERROR</span></label>
					<div style="color: #FF0000;">にじよめIDが間違っていないかご確認下さい。またゲーム内お問い合わせよりお問い合わせ下さい</div>
				<?php }else{ ?>
					<label>OK</label>
				<?php } ?>
			</li>
			<li>
				<p>きゃばへるデータ</p>
				<?php if(!empty($members_check)){ ?>
					<label><span style="color: #FF0000;">ERROR</span></label>
					<div style="color: #FF0000;">きゃばへる内お客様データが正常に取得できておりません。<br />ゲーム内お問い合わせよりお問い合わせ下さい。</div>
				<?php }else{ ?>
					<label>OK<br />ゲーム内ニックネーム : <?php print($members_data['nickname']); ?></label>
				<?php } ?>
			</li>
		</ul>
	</div>



<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

