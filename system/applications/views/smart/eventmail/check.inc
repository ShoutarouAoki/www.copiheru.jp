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

	$(function() {

		<?php
		/****************************
		**
		**	新着メールがあった場合のjQuery処理
		**
		*****************************/
		if(!empty($result['result'])){
		?>

		/* CHARA ID */
		var post_send_id	= <?php print($character_id); ?>;
		var	confirmation	= 0;

		var status			= <?php print($mail_status['free']); ?>;
		var point_send		= <?php print($point_data['send']); ?>;
		var point_read		= <?php print($point_data['read']); ?>;
		var point_image		= <?php print($point_data['image']); ?>;
		var ticket_name		= "<?php print(TICKET_NAME); ?>";

		if($("#exection").length){
			$("#exection").css("display","none");
		}

		/* 未読クリック */
		$(".mail-title").click(function() {

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
						"OK": function() {
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

		<?php } ?>

		<?php
		/****************************
		**
		**	アイテム終了があった場合のjQuery処理
		**
		*****************************/
		if(!empty($result['itemuse_end_count'])){
		?>

		var count			= <?php print($result['itemuse_end_count']); ?>;
		var list			= "<?php print($itemuse_list['end_list']); ?>";
		var word			= "<?php print($itemuse_list['end_id']); ?>";
		var remaining		= <?php print($itemuse_list['remaining']); ?>;
		var message			= "<?php print($result['itemuse_end_message']); ?>";
		itemUseEnd(count,list,word,remaining,message);

		<?php } ?>



		<?php
		/****************************
		**
		**	キャンペーン開始/終了処理があった場合のjQuery処理
		**
		*****************************/
		if($campaign_check >= 2){
		?>

		var campaign_status	= <?php print($campaign_check); ?>;
		var campaign_send	= <?php print($mail_status['send']); ?>;
		var campaign_read	= <?php print($mail_status['read']); ?>;
		var campaign_all	= <?php print($mail_status['all']); ?>;
		var check_status	= <?php print($normal_status); ?>;

		setCampaignDisplay(campaign_status,campaign_send,campaign_read,campaign_all,check_status);

		<?php } ?>


	});

</script>


<?php
/****************************
**
**	新着メールがあった場合のHTML処理
**
*****************************/
if(!empty($result['result'])){
?>

	<?php
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
	if(!empty($result['media'] && $result['media_flg'] == 1)){
		$class			= " mail-image";
	}else{
		$class			= NULL;
	}
	?>
	<div class="chara-mail" id="<?php print($result['id']); ?>" style="display: none;">
		<div id="read-space-<?php print($result['id']); ?>"></div>
		<div class="mail-title read-check<?php print($class); ?>" id="mails-id-<?php print($result['id']); ?>"<?php print($name); ?>>
			<span class="unread">未読</span><br />
			<div class="mail-inner"><?php print($result['title']); ?><span class="display-read">【続きを読む】</span></div>
			<?php if(!empty($result['media'] && $result['media_flg'] == 1)){ ?>
			<div class="image-unread">【画像あり】</div>
			<?php } ?>
		</div>
	</div>

<?php } ?>

