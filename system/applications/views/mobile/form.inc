<img src="/images/mobile/title/title_<?php print($data['page']); ?>.gif" style="border: 0px;" />
<?php print($spacer); ?>
<?php print($contents_table); ?>

<?
/*********************************************
**
**	CONTACT
**
**********************************************/
if($data['page'] == "contact"){
?>

	<?
	/*********************************************
	**
	**	CUSTOMERS
	**
	**********************************************/
	if(empty($exection)){
	?>

		
		お問い合わせフォームページです。<br />
		注意事項をよくお読みになり必須項目にご記入の上お問い合わせ下さい。

		<?php $htmlClass->getMobileTitle("お問い合わせフォーム",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>

		<?
		/*********************************************
		**
		**	ERROR WARNING
		**
		**********************************************/
		if(!empty($post_data['error'])){
			include_once(dirname(__FILE__)."/templates/warning.inc");
		}
		?>

		<div style="text-align: right;"><span style="color: #FF0000;">※</span>は必須項目です</div><br />

		<form action="<? print($page_path); ?>" method="post">
		<input type="hidden" name="purpose" value="1" />

		<span style="<?php print($sub_title_color); ?>">[お名前]</span> <span style="color: #FF0000;">※</span><br />
		<input type="text" name="user_name" value="<? print($post_data['user_name']); ?>" /><br />
		<br />

		<span style="<?php print($sub_title_color); ?>">[メールアドレス]</span> <span style="color: #FF0000;">※</span><br />
		<input type="text" name="mail_address" style="<?php print($style_mail); ?>" value="<? print($post_data['mail_address']); ?>" /><br />
		<br />

		<span style="<?php print($sub_title_color); ?>">[お問い合わせ件名] </span><span style="color: #FF0000;">※</span><br />
		<input type="text" name="title" value="<? print($post_data['title']); ?>" /><br />
		<br />

		<span style="<?php print($sub_title_color); ?>">[お問い合わせ内容] </span><span style="color: #FF0000;">※</span><br />
		<textarea name="content"><?php print($post_data['content']); ?></textarea><br />
		<br />
		<div style="text-align: center;">
		<input type="submit" value="お問い合わせ内容を確認" />
		</div>
		</form>
		<br />
		<br />

		<?php $htmlClass->getMobileTitle("注意事項",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>
		<div>
		<?php print nl2br($warning_data['content']); ?>
		</div>


	<?
	/*********************************************
	**
	**	SITES
	**
	**********************************************/
	}elseif($exection == 1){
	?>

		<?php $htmlClass->getMobileTitle("お問い合わせフォーム",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>

		<?
		/*********************************************
		**
		**	ERROR WARNING
		**
		**********************************************/
		if(!empty($post_data['error'])){
			include_once(dirname(__FILE__)."/templates/warning.inc");
		}
		?>

		<div style="text-align: left;"><span style="color: #FF0000;">修正する場合は下記『戻る』ボタンにて入力フォームへお戻り下さい</span></div><br />

		<form action="<? print($page_path); ?>" method="post">
		<input type="hidden" name="purpose" value="2" />
		<?php print($input_hidden); ?>

		<span style="<?php print($sub_title_color); ?>">[お名前]</span> <span style="color: #FF0000;">※</span><br />
		<? print($post_data['user_name']); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[メールアドレス]</span> <span style="color: #FF0000;">※</span><br />
		<? print($post_data['mail_address']); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[お問い合わせ件名]</span> <span style="color: #FF0000;">※</span><br />
		<? print($post_data['title']); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[お問い合わせ内容]</span> <span style="color: #FF0000;">※</span><br />
		<? print nl2br($post_data['content']); ?><br />
		<br />

		<div style="text-align: center;">
		<input type="submit" value="お問い合わせ内容を送信" />
		</div>
		</form>
		<br />

		<div style="text-align: right;">
		<form action="<? print($page_path); ?>" method="post">
		<?php print($input_hidden); ?>
		<input type="submit" value="戻る" />
		</form>
		</div>
		<br />
		<br />

		<?php $htmlClass->getMobileTitle("注意事項",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>
		<div>
		<?php print nl2br($warning_data['content']); ?>
		</div>

	<?
	/*********************************************
	**
	**	END
	**
	**********************************************/
	}elseif($exection == 2){
	?>

		<div id="exection" style="text-align: center;">
		<? if(!empty($post_data['error'])){ ?>
		<? print($post_data['error']); ?><br /><br />
		<? }else{ ?>
		<? print nl2br($file_data['content']); ?>
		<? } ?>
		</div>
		<?php print($hr); ?>
		<div style="text-align: right;">
		<a href="/">戻る</a>
		</div>

	<?php } ?>




<?
/*********************************************
**
**	ENTRY
**
**********************************************/
}elseif($data['page'] == "entry"){
?>

	<?
	/*********************************************
	**
	**	CUSTOMERS
	**
	**********************************************/
	if(empty($exection)){
	?>

		
		登録申請情報入力ページです。<br />
		注意事項をよくお読みになり必須項目にご記入の上登録申請を行なって下さい。
		

		<?php $htmlClass->getMobileTitle("申請情報入力フォーム",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>

		<?
		/*********************************************
		**
		**	ERROR WARNING
		**
		**********************************************/
		if(!empty($post_data['error'])){
			include_once(dirname(__FILE__)."/templates/warning.inc");
		}
		?>

		<div style="text-align: right;"><span style="color: #FF0000;">※</span>は必須項目です</div><br />

		<form action="<? print($page_path); ?>" method="post">
		<input type="hidden" name="purpose" value="1" />
		<input type="hidden" name="pref" value="1" />

		<span style="<?php print($sub_title_color); ?>">[企業名]</span><br />
		<input type="text" name="company_name" value="<? print($post_data['company_name']); ?>" /><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[契約者氏名]</span> <span style="color: #FF0000;">※</span><br />
		<input type="text" name="user_name" value="<? print($post_data['user_name']); ?>" /><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[メールアドレス]</span> <span style="color: #FF0000;">※</span><br />
		<input type="text" name="mail_address1" style="<?php print($style_mail); ?>" value="<? print($post_data['mail_address1']); ?>" /><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[メールアドレス(確認用)]</span> <span style="color: #FF0000;">※</span><br />
		<input type="text" name="mail_address2" style="<?php print($style_mail); ?>" value="<? print($post_data['mail_address2']); ?>" /><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[電話番号]</span> <span style="color: #FF0000;">※</span><br />
		<input type="text" name="tel_number" style="<?php print($style_tel); ?>" value="<? print($post_data['tel_number']); ?>" /><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[郵便番号]</span> <span style="color: #FF0000;">※</span><br />
		〒<input type="text" name="zip_number" style="<?php print($style_zip); ?>" size="10" value="<? print($post_data['zip_number']); ?>" /><br />
		例)123-4567<br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[都道府県]</span><br />
		北海道<br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[市区町村]</span> <span style="color: #FF0000;">※</span><br />
		<select name="city">
		<? print($city_select_mobile); ?>
		</select>
		<br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[詳細住所]</span> <span style="color: #FF0000;">※</span><br />
		<input type="text" name="address" value="<? print($post_data['address']); ?>" /><br />
		例)1-2-3 ○○ビル 1F<br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[利用規約]</span> <span style="color: #FF0000;">※</span><br />
		<textarea style="width: 100%;" height="30" readonly>
		<?php print($rule_data['content']); ?>
		</textarea><br />
		<input type="checkbox" name="accept" value="1"<?php print($accept); ?> />利用規約に同意する<br />
		<br />

		<div style="text-align: center;">
		<input type="submit" value="記入情報を確認" />
		</div>
		</form>
		<br />
		<br />

		<?php $htmlClass->getMobileTitle("注意事項",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>
		<div>
		<?php print nl2br($warning_data['content']); ?>
		</div>


	<?
	/*********************************************
	**
	**	SITES
	**
	**********************************************/
	}elseif($exection == 1){
	?>

		<?php $htmlClass->getMobileTitle("申請情報入力チェック",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>

		<?
		/*********************************************
		**
		**	ERROR WARNING
		**
		**********************************************/
		if(!empty($post_data['error'])){
			include_once(dirname(__FILE__)."/templates/warning.inc");
		}
		?>

		<div style="text-align: left;"><span style="color: #FF0000;">修正する場合は下記『戻る』ボタンにて入力フォームへお戻り下さい</span></div><br />

		<form action="<? print($page_path); ?>" method="post">
		<input type="hidden" name="purpose" value="2" />
		<?php print($input_hidden); ?>

		
		<?php if(!empty($post_data['company_name'])){ ?>
		<span style="<?php print($sub_title_color); ?>">[企業名]</span><br />
		<? print($post_data['company_name']); ?><br />
		<br />
		<?php } ?>
		
		<span style="<?php print($sub_title_color); ?>">[契約者氏名]</span> <span style="color: #FF0000;">※</span><br />
		<? print($post_data['user_name']); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[メールアドレス]</span> <span style="color: #FF0000;">※</span><br />
		<? print($post_data['mail_address1']); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[電話番号]</span> <span style="color: #FF0000;">※</span><br />
		<? print($post_data['tel_number']); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[郵便番号]</span> <span style="color: #FF0000;">※</span><br />
		〒<? print($post_data['zip_number']); ?><br />
		<br />

		<span style="<?php print($sub_title_color); ?>">[都道府県]</span> <span style="color: #FF0000;">※</span><br />
		<? print($display_pref); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[市区町村]</span> <span style="color: #FF0000;">※</span><br />
		<? print($display_city); ?><br />
		<br />
		
		<span style="<?php print($sub_title_color); ?>">[詳細住所]</span> <span style="color: #FF0000;">※</span><br />
		<? print($post_data['address']); ?><br />
		<br />


		<div style="text-align: center;">
		<input type="submit" value="登録申請" />
		</div>
		</form>
		<br />

		<div style="text-align: right;">
		<form action="<? print($page_path); ?>" method="post">
		<?php print($input_hidden); ?>
		<input type="submit" value="戻る" />
		</form>
		</div>
		<br />
		<br />

		<?php $htmlClass->getMobileTitle("注意事項",$title_background,$title_color,$font_size); ?>
		<?php print($spacer); ?>
		<div>
		<?php print nl2br($warning_data['content']); ?>
		</div>

	<?
	/*********************************************
	**
	**	END
	**
	**********************************************/
	}elseif($exection == 2){
	?>

		<div id="exection" style="text-align: center;">
		<? if(!empty($post_data['error'])){ ?>
		<? print($post_data['error']); ?><br /><br />
		<? }else{ ?>
		<? print nl2br($file_data['content']); ?>
		<? } ?>
		</div>
		<?php print($hr); ?>
		<div style="text-align: right;">
		<a href="/">戻る</a>
		</div>

	<?php } ?>


<?
/*********************************************
**
**	PASSWORD
**
**********************************************/
}elseif($data['page'] == "passwprd"){
?>




<?php } ?>

<?php print($table_end); ?>
<?php print($hr); ?>
