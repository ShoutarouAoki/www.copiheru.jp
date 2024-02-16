<?php
/****************************
**
**	CONTENT TYPE 宣言
**
*****************************/
?>
<?php header("Content-type: text/html; charset=".SITE_CHARSET.""); ?>

<?php
/****************************
**
**	jQuery / Ajax
**
*****************************/
?>
<script type="text/javascript" >

	/* CHARA ID */
	var post_send_id	= <?php print($post_send_id); ?>;
	var	title_class		= ".mail-title-" + <?php print($set); ?>;
	var	confirmation	= 0;

	var status			= <?php print($mail_status['free']); ?>;
	var point_send		= <?php print($point_data['send']); ?>;
	var point_read		= <?php print($point_data['read']); ?>;
	var point_image		= <?php print($point_data['image']); ?>;
	var ticket_name		= "<?php print(TICKET_NAME); ?>";

	$(function() {

		/* 画像切り替え */
		$(".image-read").click(function() {

			var element					= $(this);
			var mails_image_id			= element.attr("id");

			if(mails_image_id){
				imageRead(mails_image_id);
			}

			return false;

		});

		/* 閲覧処理 */
		$(title_class).click(function() {

			var element					= $(this);
			var mails_id				= element.attr("id");
			var read_type				= element.attr("name");

			/* 画像添付チェック */
			var image_check				= 0;
			var class_name				= element.attr("class");
			var classArray				= class_name.split(" ");

			for( var i=0;i<classArray.length;i++ ){
				if(classArray[i] == "mail-image"){
					image_check			= 1;
				}
			}

			/* チェックボックス確認 初期化 */
			confirmation				= 0;

			/* チケット消費ダイアログ表示 */
			if(read_type == "read-confirm-on"){

				/* 有料開封 */
				if(point_read > 0){

					/* 残りチケット数を取得 */
					var user_point				= $(".check-point").text();

					/* 消費ポイント計算 */
					if (image_check == 0) {
						var	point				= point_read;
						var add_message			= "";
					} else {
						if(point_image > 0){
							var point			= point_read + point_image;
							var add_message		= "<br /><span style=\"color: #FF0000;\">添付画像がある場合は" + ticket_name + "が<br />＋" + point_image + "枚加必要です。</span>";
						} else {
							var	point			= point_read;
							var add_message		= "";
						}
					}

					/* ここでダイアログ */
					var dialog_title			= "チケット確認";
					var dialog_message			= ticket_name + "を" + point + "枚消費しますが宜しいですか？" + add_message;
					dialog_message				+= "<br /><br />残り" + ticket_name + "枚数 : " + user_point + "枚";


				/* 無料開封 */
				} else {

					/* ここでダイアログ */
					var dialog_title			= "メール開封確認";
					var dialog_message			= "このメールの続きを読みますか？<br />";

					/* 有料ユーザー */
					if(status == 0){
						dialog_message		+= "<span style=\"color: red;\">※" + ticket_name + "は必要ありません</span>";
					}

				}


				$("#dialog-ticket").html(dialog_message);
				$( "#dialog-ticket" ).dialog({
					modal: true,
					title: dialog_title,
					buttons: {
						"　ＯＫ　": function() {
							/* チェックボックス */
							var check			= $("[name=confirm]").prop("checked");
							if(check){
								confirmation	= 1;
								$(".read-check").attr("name","read-confirm-off");
							}
							$( this ).dialog( "close" );
							mailRead(mails_id,image_check);
						},
						"キャンセル": function() {
							$( this ).dialog( "close" );
							return false;
						}
					}
				});

			} else {

				/* 既読処理 */
				mailRead(mails_id,image_check);

			}

		});

		/* もっと読む */
		$(".next-more-button").click(function() {

			var next_before_id	= <?php print($next_before_id); ?>;
			$("#more-read").html("<img src=\"/images/loading.gif\" class=\"more-read\" />");

			$.ajax({
				type: "POST",
				url: "<?php print($more_path); ?>",
				data : {id : post_send_id, next_before_id : next_before_id },
				timeout:10000,
				cache: false,
				// 成功
				success: function(html){
					$("#more-area").append(html);
					$(".more-button" + next_before_id).remove();
					
				},
				// 通信失敗
				error: function(XMLHttpRequest, textStatus, errorThrown) {

					alert("読み込みできませんでした");
					$(".more-read").remove();

				}
			});

			return false;

		});


	});

</script>


<?php
if(!empty($mail_list['id'])){
?>

	<?php
	$count	= count($mail_list['id']);
	for($i=0;$i<$count;$i++){
	?>

		<?php
		# キャラのメール
		if($mail_list['send_type'][$i] == 1){

			# 未読チェック チケット消費確認ダイアログ
			if(MAIL_READ_CONFIRM == "ON"){
				if($members_data['confirmation'] == 0 || $members_data['confirmation'] == 2){
					$name		= " name=\"read-confirm-on\"";
				}else{
					$name		= " name=\"read-confirm-off\"";
				}
			}else{
				$name			= " name=\"read-confirm-off\"";
			}

			# 画像あり
			if(!empty($mail_list['media'][$i] && $mail_list['media_flg'][$i] == 1)){
				$class			= " mail-image";
			}else{
				$class			= NULL;
			}

		?>
			<div class="chara-mail" id="<?php print($mail_list['id'][$i]); ?>">
				<?php
				# 未読
				if($mail_list['recv_flg'][$i] == 1){
				?>
					<div id="read-space-<?php print($mail_list['id'][$i]); ?>"></div>
					<div class="mail-title-<?php print($set); ?> read-check<?php print($class); ?>" id="mails-id-<?php print($mail_list['id'][$i]); ?>"<?php print($name); ?>>
						<span class="unread">未読</span><br />
						<div class="mail-inner">
							<?php print($mail_list['title'][$i]); ?>
							<?php if(!empty($mail_list['present'][$i])){ ?>
								<span class="mail-point">【プレゼントあり】</span>
							<?php } ?>
							<span class="display-read">【続きを読む】</span>
						</div>
						<?php if(!empty($mail_list['media'][$i] && $mail_list['media_flg'][$i] == 1)){ ?>
						<div class="image-unread">【画像あり】</div>
						<?php } ?>
					</div>
				<?php
				# 既読
				}else{
				?>
				<div class="mail-contents">
					<span class="read">既読</span><br />
					<div class="mail-inner"><?php print($mail_list['title'][$i]); ?><?php print nl2br($mail_list['message'][$i]); ?></div>
					<?php if(!empty($mail_list['media'][$i] && $mail_list['media_flg'][$i] == 1)){ ?>
					<div class="image-read" id="mails-image-<?php print($mail_list['id'][$i]); ?>">【画像を見る】</div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		<?php
		# ユーザーのメール
		}else{
		?>
			<div class="user-mail">
			<?php print nl2br($mail_list['message'][$i]); ?>
			</div>
		<?php } ?>

	<?php } ?>

	<?php
	# もっと読むボタン
	if(!empty($more_button)){
	?>
	<div class="clear"></div>
	<div id="more-area"></div>
	<div id="more-read" class="more-button<?php print($next_before_id); ?>">
		<a id="more-button" class="next-more-button"><img src="/images/smart/button/button-more.png" /></a>
	</div>
	<?php } ?>

	<div class="clear"></div>

<?php } ?>

