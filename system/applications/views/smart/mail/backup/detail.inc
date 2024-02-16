<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link href="/common/css/smart/mail.css" rel="stylesheet" type="text/css" />


<?php /* アニメーションプラグイン */ ?>
<script type="text/javascript" src="/plugins/ParaPara/src/parapara.js"></script>

<?php
/*************************************
**
**	CONTENTS MODAL / iOS
**	---------------------------------
**	メニュー用モーダルiOS用
**
**************************************/
if($os_number == 1){
?>
	<link rel="stylesheet" type="text/css" href="/plugins/ModalWindowEffects/css/component.css" />
<?php
/*************************************
**
**	CONTENTS MODAL / Android
**	---------------------------------
**	メニュー用モーダルAndroid用
**
**************************************/
}elseif($os_number == 2){
?>
	<link href="/plugins/colorbox-master/css/colorbox.css" rel="stylesheet" />
	<script src="/plugins/colorbox-master/js/jquery.colorbox.js" ></script>
<?php } ?>

<?php
/*************************************
**
**	デバッグ用
**
**************************************/
# システム部からスマフォデザインをPCで見た場合
if(defined("SYSTEM_CHECK") && $default_device == "pc"){
	$colorbox_width		= "460";
# 通常
}else{
	$colorbox_width		= "90%";
}
?>


<?php
/*************************************
**
**	jQuery / Ajax
**
**************************************/
?>
<script type="text/javascript" >

	/* CHARA ID */
	var post_send_id	= <?php print($post_send_id); ?>;
	var first_mail		= <?php print($first_children_id); ?>;
	var last_mail_id	= 0;
	var	confirmation	= 0;

	$(function() {

		/* ANIMATION */
        var frame		= new ParaParaData();
        $("#character-image img").each(function (i) {
            frame.data[ParaPara.zeroPadding(i, 4)] = this;
        });

        var anime		= new ParaPara(frame, $("#animation-display"));
        anime.repeat	= true;
		anime.play();

		/* アイテムモーダル Android */
		$("#item-button").click(function(){
		    $.colorbox({
				inline:true,
				width:"<?php print($colorbox_width); ?>",
				href:"#item-use",
				opacity: 0.7,
				returnFocus: false
			});
		});

		/* メニューモーダル Android */
		$("#menu-button").click(function(){
		    $.colorbox({
				inline:true,
				width:"<?php print($colorbox_width); ?>",
				href:"#menu-list",
				opacity: 0.7,
				returnFocus: false
			});
		});

		/* アイテム / メニューモーダル iOS */
		$(".item-menu").click(function() {
			$("#modal-overlay").fadeIn("fast");
			return false;
		});

		$(".md-close").click(function() {
			$("#modal-overlay").fadeOut("fast");
			return false;
		});

		/* 非表示 */
		$(".front-hide").click(function() {
			$(".front").fadeOut("fast");
			$("#controll-back").fadeIn("fast");
			$(".button-show").fadeIn("fast");
			return false;
		});

		/* 表示 */
		$(".front-show").click(function() {
			$(".front").fadeIn("fast");
			$(".button-show").fadeOut("fast");
			$("#controll-back").fadeOut("fast");
			return false;
		});

		/* メールログBOX拡大 */
		$(".mail-frame-zoomin").click(function() {
			$("#screen-overlay").fadeIn("fast");
			$(".out").fadeOut("fast");
			$(".button-zoomout").show();
			$("#mail-area").animate({ 
				top: "32px"
			}, 250 );
			$("#mail-frame").animate({ 
				height: "450px"
			}, 250 );
			//$("#mail-area").css("top","32px");
			//$("#mail-frame").css("height","450px");
			return false;
		});

		/* メールログBOX縮小 */
		$(".mail-frame-zoomout").click(function() {
			$("#screen-overlay").fadeOut("fast");
			$(".out").fadeIn("fast");
			$(".button-zoomout").hide();
			$("#mail-area").animate({ 
				top: "290px"
			}, 250 );
			$("#mail-frame").animate({ 
				height: "160px"
			}, 250 );
			//$("#mail-area").css("top","290px");
			//$("#mail-frame").css("height","160px");
			return false;
		});


		/* アイテム利用 */
		$(".item-list").click(function() {

			var element						= $(this);
			var item_id						= element.attr("id");
			var item_num_line				= "#item-num-" + item_id;
			var item_count					= $(item_num_line).text();

			if(item_count == 0){
				item_count					= null;
			}

			if(item_count){

				var item_name_line			= "#item-name-" + item_id;
				var item_description_line	= "#item-description-" + item_id;
				var item_name				= $(item_name_line).text();
				var item_description		= $(item_description_line).text();

				/* ここでダイアログ */
				var dialog_message			= item_description + "<br />残り数 : " + item_count + " 個<br /><br />このアイテムを使用しますか？";
				$( "#dialog-item-confirm" ).html(dialog_message);

				$( "#dialog-item-confirm" ).dialog({
					modal: true,
					title: item_name,
					buttons: {
						"OK": function() {
							$(this).dialog("close");
							itemUse(item_id,item_count);
						},
						"キャンセル": function() {
							$(this).dialog("close");
							return false;
						}
					}
				});

			}

		});


		/* もっと読む */
		$(".first-more-button").click(function() {

			var next_before_id	= <?php print($next_before_id); ?>;

			$.ajax({
				type: "POST",
				url: "<?php print($more_path); ?>",
				data : {id : post_send_id, next_before_id : next_before_id },
				timeout:10000,
				cache: false,
				// 成功
				success: function(html){
					$(".first-more-button").remove();
					$("#more-button").remove();
					$("#more-area").append(html);
				},
				// 通信失敗
				error: function(XMLHttpRequest, textStatus, errorThrown) {

					// エラーメッセージ(ダイアログ)
					showErrorDialog("エラー","読み込みできませんでした");
					$(".more-read").remove();

				}
			});

			return false;

		});

		/* 未読クリック */
		$(".mail-title").click(function() {

			var element					= $(this);
			var mails_id				= element.attr("id");
			var read_type				= element.attr("name");

			/* チェックボックス確認 初期化 */
			confirmation				= 0;

			/* チケット消費ダイアログ表示 */
			if(read_type == "read-confirm-on"){

					/* 常にチェックをつける */
					//$("[name=confirm]").prop("checked",true);

					/* ここでダイアログ */
					var dialog_title	= "チケット確認";

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
								$(this).dialog("close");
								mailRead(mails_id);
							},
							"キャンセル": function() {
								$(this).dialog("close");
								return false;
							}
						}
					});

			} else {

				/* 既読処理 */
				mailRead(mails_id);

			}

		});

		/* 送信ボタンクリック */
		$("#button-send").click(function() {

			var message					= $("#message").val();

			if (message) {

				/* 確認 */
				var send_type			= $("#button-send").attr("name");

				/* チェックボックス確認 初期化 */
				confirmation			= 0;

				/* チケット消費ダイアログ表示 */
				if(send_type == "send-confirm-on"){

					/* 常にチェックをつける */
					//$("[name=confirm]").prop("checked",true);

					/* ここでダイアログ */
					var dialog_title	= "チケット確認";

					$("#dialog-ticket").dialog({
						resizable: false,
						modal: true,
						title: dialog_title,
						buttons: {
							"OK": function() {
								/* チェックボックス */
								var check			= $("[name=confirm]").prop("checked");
								if(check){
									confirmation	= 1;
									$("#button-send").attr("name","send-confirm-off");
								}
								$("#dialog-ticket").dialog( "close" );
								mailSend();
							},
							"キャンセル": function() {
								$("#dialog-ticket").dialog( "close" );
								return false;
							}
						}
					});

				/* 既にチェック済み */
				} else {

					/* 送信処理 */
					mailSend();

				}

			} else {

				// エラーメッセージ(ダイアログ)
				showErrorDialog("エラー","メッセージを入力して下さい");

			}

			return false;

		});

	});


	<?php
	/********************************************
	**
	**	アイテム利用
	**
	********************************************/
	?>
	function itemUse(item_id,item_count) {

		if (item_id && item_count) {

			$(".loading").fadeIn();

			<?php /* $directory には mail が入る */ ?>
			var	sender = "<?php print($directory); ?>";

			$.ajax({
				type:'POST',
				url: "/item/use/",
				dataType: 'json',
				data : {id : post_send_id, itembox_id : item_id, sender : sender },
				timeout:10000,
				cache: false,
				// 通信成功
				success: function(data) {

					var error = data.error;

					// OK
					if (error == 0) {

						var item_line		= "#" + item_id;
						var item_num_line	= "#item-num-" + item_id;

						// 残りアイテム数計算
						var item_count_new	= item_count - 1;

						//$(".md-close").trigger("click");
						$(".loading").fadeOut();

						// 残りアイテム数
						if(item_count_new == 0){
							// アイテム無し状態にする
							$(item_num_line).remove();
							$(item_line).remove();
						}else{
							// アイテム残数を表示
							$(item_num_line).html(item_count_new);
						}

						// 使用時メッセージ(ダイアログ)
						if(data.message){
							var dialog_title	= data.name + "を使用しました！";
							$("#dialog-item-use").html(data.message);
							$("#dialog-item-use").dialog({
								resizable: false,
								modal: true,
								title: dialog_title,
								buttons: {
									"OK": function() {
										$(this).dialog("close");
									}
								}
							});
						}

					// ERROR
					} else {

						// エラーメッセージ(ダイアログ)
						if(data.errormessage){
							showErrorDialog("エラー",data.errormessage);
						}
						$(".loading").fadeOut();

					}

				},
				// 通信失敗
				error: function(XMLHttpRequest, textStatus, errorThrown) {

					showErrorDialog("エラー","正常に処理できませんでした");
					$(".loading").fadeOut();

				}

			});

		} else {

			showErrorDialog("エラー","正常に処理できませんでした");

		}

	}


	<?php
	/********************************************
	**
	**	既読処理
	**
	********************************************/
	?>
	function mailRead(mails_id) {

			if (mails_id) {

				$(".loading").fadeIn();

				$.ajax({
					type:'POST',
					url: "/mail/read/",
					dataType: 'json',
					data : {id : post_send_id, mails_id : mails_id, confirmation : confirmation },
					timeout:10000,
					cache: false,
					// 通信成功
					success: function(data) {

						var error = data.error;

						// OK
						if (error == 0) {

							var mail_line			= "#" + mails_id;
							var read_space			= "#read-space-" + data.id;
							var read_contents		= "<div class=\"mail-contents\"><span class=\"unread\">既読</span><br /><div class=\"mail-inner\">" + data.title + data.message + "</div></div>";

							$(read_space).append(read_contents);
							$(mail_line).remove();
							$(".loading").fadeOut();

							// チケット消費
							$(".check-point").html(data.ticket);

							// 未読メールカウントを減らす
							var unread_count		= $(".check-unread").text();
							var unread_count_new	= unread_count - 1;
							$(".check-unread").html(unread_count_new);

						// ERROR
						} else {

							alert(data.errormessage);
							$(".loading").fadeOut();

						}

					},
					// 通信失敗
					error: function(XMLHttpRequest, textStatus, errorThrown) {

						showErrorDialog("エラー","続きを取得できませんでした");
						$(".loading").fadeOut();

					}

				});

			} else {

				showErrorDialog("エラー","正常に処理できませんでした");

			}

	}



	<?php
	/********************************************
	**
	**	メール送信処理
	**
	********************************************/
	?>
	function mailSend() {

		$(".loading").fadeIn();

		var message			= $("#message").val();

		$.ajax({
			type:'POST',
			url: "/mail/send/",
			dataType: 'json',
			data : {id : post_send_id, first_mail : first_mail, message : message, confirmation : confirmation },
			timeout:10000,
			cache: false,
			// 通信成功
			success: function(data) {

				var error = data.error;

				// OK
				if (error == 0) {

					if($("#exection").length){
						$("#exection").css("display","none");
					}

					$("#add-area").prepend("<div class=\"user-mail hide\">" + data.message + "</div>");
					$(".loading").fadeOut();
					$(".hide").fadeIn(1000);
					$(".hide").scrollTop(1000);
					//$('#add-area').animate({scrollTop: $('#add-area')[0].scrollHeight}, 'fast');
					$("#message").val("");

					// 初期化
					first_mail		= 0;

					// 好感度操作
					var favorite_level		= "Lv." + data.favorite_level;
					var favorite_percent	= data.favorite_percent + "％";
					var favorite_gauge		= data.favorite_gauge + "%";

					// 表示変更
					if(data.level_up){

						// 好感度ゲージオーバレイを一旦0%にする(表示上100%)
						$("#favorite-gauge-screen").animate({ 
							height: "0%"
						}, 1000 );

						// 好感度レベルアップ
						$("#favorite-level").html(favorite_level);

						// 好感度ゲージを100%に0%にする(表示上0%)
						$("#favorite-gauge-screen").animate({ 
							height: "100%"
						}, 0 );

						// 好感度ゲージアップ(残り差分)
						$("#favorite-gauge-screen").animate({ 
							height: favorite_gauge
						}, 1000 );

						// 好感度数値を切り替え
						$("#favorite-percent").html(favorite_percent);

					}else{

						// 好感度ゲージアップ
						$("#favorite-gauge-screen").animate({ 
							height: favorite_gauge
						}, 1000 );

						// 好感度レベル(据え置き)
						$("#favorite-level").html(favorite_level);

						// 好感度数値を切り替え
						$("#favorite-percent").html(favorite_percent);

					}

					// チケット消費
					$(".check-point").html(data.ticket);

					/* LEVEL UP */
					if(data.level_up){

						$("#dialog-favorite").html(data.level_up_message);
						$("#dialog-favorite").dialog({
							resizable: false,
							modal: true,
							title: data.level_up_title,
							buttons: {
								"OK": function() {
									$(this).dialog("close");
								}
							}
						});

					}

				// OK
				} else {

					showErrorDialog("エラー",data.errormessage);
					$(".loading").fadeOut();

				}

			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {

				showErrorDialog("エラー","送信できませんでした");
				<?php if(defined("SYSTEM_CHECK")){ ?>alert(errorThrown);<?php } ?>
				$(".loading").fadeOut();

			}

		});

	}


	<?php
	/********************************************
	**
	**	メッセージチェック タイマー
	**
	********************************************/
	?>

	/* チェック間隔 15秒 */
	var timer			= 15000;

	function checkNewMessage() {

		// class : chara-mail内の最新メールIDを取得
		$($(".chara-mail").get().reverse()).each(function(i) {
			last_mail_id	= $(this).attr("id");
		});

		$.ajax({
			type:'POST',
			url: "/mail/check/",
			dataType: 'json',
			data : {id : post_send_id, last_mail_id : last_mail_id },
			timeout:10000,
			cache: false,
			// 通信成功
			success: function(data) {

				var result = data.result;

				// 最新メールあり
				if (result > 0) {
					$("#add-area").prepend("<div class=\"chara-mail hide\" id=\"" + data.id + "\">" + data.title + "</div>");
					$(".hide").fadeIn(1000);
				}

			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {

			}

		});

	}
	setInterval("checkNewMessage()",timer);

</script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>


	<div id="mail-detail">

		<?php
		/********************************
		**
		**	画像
		**
		*********************************/
		?>
		<section id="character-image">
			<?php if($attaches_count > 0){ ?>
				<?php
				$count		= count($display_image['id']);
				for($i=0;$i<$count;$i++){
				?>
					<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($display_image['image'][$i]); ?>" />
				<?php } ?>
			<?php } else{ ?>
				<img src="<?php print(HTTP_ATTACHES); ?>/noimage.png" />
			<?php } ?>
		</section>
		<section id="animation-display">
			<?php if($attaches_count > 0){ ?>
				<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($display_image['image'][0]); ?>" />
			<?php } else{ ?>
				<img src="<?php print(HTTP_ATTACHES); ?>/noimage.png" />
			<?php } ?>
		</section>


		<?php
		/********************************
		**
		**	コントロールボタン(非表示対応)
		**
		*********************************/
		?>
		<section id="controll-back">
			<a href="javascript:void(0)" class="front-show"><img src="/images/smart/button/button-show.png" class="button-show" /></a>
			<div class="clear"></div>
		</div>


		<?php
		/********************************
		**
		**	好感度エリア
		**
		*********************************/
		?>
		<section id="favorite-area" class="front out">
			<div id="favorite-level">Lv.<?php print($favorite_level); ?></div>
			<div id="favorite-gauge"><div id="favorite-gauge-screen" style="height: <?php print($favorite_gauge); ?>%;"></div></div>
			<div id="favorite-percent"><?php print($favorite_percent); ?>％</div>
		</section>

		<?php /* 好感度レベルアップダイアログ */ ?>
		<div id="dialog-favorite"></div>


		<?php
		/********************************
		**
		**	メニュー
		**	=============================
		**	モーダルウィンドウで開く
		**	一番下にモーダルメニュー部分書いてある
		**
		*********************************/
		?>
		<section id="menu-area" class="front out">
			<?php if($os_number == 1){ ?>
				<a class="md-trigger item-menu" data-modal="modal-0001" id="0001"><img src="/images/smart/button/button-item.png" /></a>
				<a class="md-trigger item-menu" data-modal="modal-0002" id="0002"><img src="/images/smart/button/button-menu.png" /></a>
			<?php }elseif($os_number == 2){?>
				<a href="javascript:void(0)" id="item-button" data-tor-smoothScroll="noSmooth"><img src="/images/smart/button/button-item.png" /></a>
				<a href="javascript:void(0)" id="menu-button" data-tor-smoothScroll="noSmooth"><img src="/images/smart/button/button-menu.png" /></a>
			<?php } ?>
			<div id="menu-ticket"><img src="/images/smart/button/button-ticket.png" /><span class="check-point"><?php print($members_data['total_point']); ?></span></div>
		</section>


		<?php
		/*************************************
		**
		**	CONTENTS MODAL / iOS
		**	==================================
		**	アイテムメニュー iOS
		**
		**************************************/
		if($os_number == 1){
		?>


			<div class="md-modal md-effect-8" id="modal-0001">
				<div class="md-content">
					<div class="modal-box">
						<div class="modal-bottom"><a class="md-close"><img src="/images/smart/button/button-close.png" /></a></div>
						<div class="clear"></div>
						<div class="modal-title"><img src="/images/smart/title-item.png" /></div>
						<?php
						$count			= count($item_list['id']);
						$count_none		= count($item_list_nouse['id']);
						if($count > 0 || $count_non > 0){
						?>
							<div class="modal-list">
								<ul>
								<?php
								/*************************************
								**
								**	使えるアイテム
								**
								**************************************/
								for($i=0;$i<$count;$i++){
									$item_id		= $item_list['id'][$i];
									$item_count		= $item_list['unit'][$i];
									if($item_count > 0){
										$class		= "item-list";
									}else{
										$class		= "item-list-none";
									}
								?>
									<li class="<?php print($class); ?>" id="<?php print($item_id); ?>">
										<?php if(!empty($item_list['image'][$i])){ ?>
										<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list['image'][$i]); ?>" />
										<?php } ?>
										<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list['name'][$i]); ?></span>
										<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list['description'][$i]); ?></span>
										<?php if(!empty($item_count)){ ?>
											<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
										<?php } ?>
									</li>
								<?php } ?>

								<?php
								/*************************************
								**
								**	使えないアイテム
								**
								**************************************/
								for($i=0;$i<$count_none;$i++){
									$item_id		= $item_list_nouse['id'][$i];
									$item_count		= $item_list_nouse['unit'][$i];
								?>
									<li class="item-list-nouse" id="<?php print($item_id); ?>">
										<?php if(!empty($item_list_nouse['image'][$i])){ ?>
										<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list_nouse['image'][$i]); ?>" />
										<?php } ?>
										<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list_nouse['name'][$i]); ?></span>
										<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list_nouse['description'][$i]); ?></span>
										<?php if(!empty($item_count)){ ?>
											<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
										<?php } ?>
										<p class="item-no-use">ここでは使えません</p>
									</li>
								<?php } ?>
								</ul>
							</div>
						<?php } ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<div class="md-modal md-effect-8" id="modal-0002">
				<div class="md-content">
					<div class="modal-box">
						<div class="modal-bottom"><a class="md-close"><img src="/images/smart/button/button-close.png" /></a></div>
						<div class="clear"></div>
						<div class="modal-title"><img src="/images/smart/title-menu.png" /></div>
						<div class="modal-list">
							<ul>
								<li><a href="/main/"><img src="/images/smart/button/button-home.png" /></a></li>
								<li><a href="/character/"><img src="/images/smart/button/button-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
								<li><a href="/gacha/"><img src="/images/smart/button/button-gacha.png" /></a></li>
								<li><a href="/shop/"><img src="/images/smart/button/button-shop.png" /></a></li>
							</ul>
							<ul>
								<li><a href="/mypage/present/"><img src="/images/smart/button/button-present.png" /></a><?php if(!empty($newarrival_present_count)){ ?><p class="check-present"><?php print($newarrival_present_count); ?></p><?php } ?></li>
								<li><a href="/mypage/"><img src="/images/smart/button/button-mypage.png" /></li>
								<li><a href="/information/guide/"><img src="/images/smart/button/button-guide.png" /></li>
								<li><a href="/information/help/"><img src="/images/smart/button/button-help.png" /></li>
							</ul>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>

			<?php /* <div class="md-overlay"></div> */ ?>

			<script src="/plugins/ModalWindowEffects/js/classie.js"></script>
			<script src="/plugins/ModalWindowEffects/js/modalEffects.js"></script>


		<?php
		/*************************************
		**
		**	CONTENTS MODAL / ANDROID
		**	==================================
		**	アイテムメニュー ANDROID
		**
		**************************************/
		}elseif($os_number == 2){
		?>

			<div id="item-box">
				<div id="item-use" class="modal-box">
					<div class="modal-title"><img src="/images/smart/title-item.png" /></div>
					<div class="modal-list">
						<?php
						$count			= count($item_list['id']);
						$count_none		= count($item_list_nouse['id']);
						if($count > 0 || $count_non > 0){
						?>
							<div class="modal-list">
								<ul>
								<?php
								/*************************************
								**
								**	使えるアイテム
								**
								**************************************/
								for($i=0;$i<$count;$i++){
									$item_id		= $item_list['id'][$i];
									$item_count		= $item_list['unit'][$i];
									if($item_count > 0){
										$class		= "item-list";
									}else{
										$class		= "item-list-none";
									}
								?>
									<li class="<?php print($class); ?>" id="<?php print($item_id); ?>">
										<?php if(!empty($item_list['image'][$i])){ ?>
										<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list['image'][$i]); ?>" />
										<?php } ?>
										<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list['name'][$i]); ?></span>
										<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list['description'][$i]); ?></span>
										<?php if(!empty($item_count)){ ?>
											<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
										<?php } ?>
									</li>
								<?php } ?>

								<?php
								/*************************************
								**
								**	使えないアイテム
								**
								**************************************/
								for($i=0;$i<$count_none;$i++){
									$item_id		= $item_list_nouse['id'][$i];
									$item_count		= $item_list_nouse['unit'][$i];
								?>
									<li class="item-list-nouse" id="<?php print($item_id); ?>">
										<?php if(!empty($item_list_nouse['image'][$i])){ ?>
										<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list_nouse['image'][$i]); ?>" />
										<?php } ?>
										<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list_nouse['name'][$i]); ?></span>
										<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list_nouse['description'][$i]); ?></span>
										<?php if(!empty($item_count)){ ?>
											<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
										<?php } ?>
										<p class="item-no-use">ここでは使えません</p>
									</li>
								<?php } ?>
								</ul>
							</div>
						<?php } ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<div id="menu-box">
				<div id="menu-list" class="modal-box">
					<div class="modal-title"><img src="/images/smart/title-menu.png" /></div>
					<div class="modal-list">
						<ul>
							<li><a href="/main/"><img src="/images/smart/button/button-home.png" /></a></li>
							<li><a href="/character/"><img src="/images/smart/button/button-mail.png" /></a><?php if(!empty($newarrival_mail_count)){ ?><p class="check-unread"><?php print($newarrival_mail_count); ?></p><?php } ?></li>
							<li><a href="/gacha/"><img src="/images/smart/button/button-gacha.png" /></a></li>
							<li><a href="/shop/"><img src="/images/smart/button/button-shop.png" /></a></li>
						</ul>
						<ul>
							<li><a href="/mypage/present/"><img src="/images/smart/button/button-present.png" /></a><?php if(!empty($newarrival_present_count)){ ?><p class="check-present"><?php print($newarrival_present_count); ?></p><?php } ?></li>
							<li><a href="/mypage/"><img src="/images/smart/button/button-mypage.png" /></li>
							<li><a href="/information/guide/"><img src="/images/smart/button/button-guide.png" /></li>
							<li><a href="/information/help/"><img src="/images/smart/button/button-help.png" /></li>
						</ul>
						<div class="clear"></div>
					</div>
				</div>
			</div>

		<?php } ?>

		<?php /* アイテム利用確認ダイアログ */ ?>
		<div id="dialog-item-confirm"></div>


		<?php /* アイテム利用後ダイアログ */ ?>
		<div id="dialog-item-use"></div>


		<?php
		/********************************
		**
		**	コントロールボタン
		**
		*********************************/
		?>
		<section id="controll-area" class="front out">
			<div class="controll-button">
				<a href="javascript:void(0)" class="mail-frame-zoomin"><img src="/images/smart/button/button-zoomin.png" /></a>
				<a href="javascript:void(0)" class="front-hide"><img src="/images/smart/button/button-hide.png" /></a>
				<div class="clear"></div>
			</div>
		</section>


		<?php
		/********************************
		**
		**	メールエリア
		**
		*********************************/
		?>
		<section name="mail-area" id="mail-area" class="front">

			<?php
			/********************************
			**
			**	メールログ
			**
			*********************************/
			?>
			<div id="mail-frame">

				<div id="add-area"><img src="/images/loading.gif" class="loading" /></div>

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

						?>
							<div class="chara-mail" id="<?php print($mail_list['id'][$i]); ?>">
								<?php
								# 未読
								if($mail_list['recv_flg'][$i] == 1){
								?>
								<div id="read-space-<?php print($mail_list['id'][$i]); ?>"></div>
								<div class="mail-title read-check" id="mails-id-<?php print($mail_list['id'][$i]); ?>"<?php print($name); ?>>
									<span class="read">未読</span><br />
									<div class="mail-inner"><?php print($mail_list['title'][$i]); ?><span class="display-read">【続きを読む】</span></div>
								</div>
								<?php
								# 既読
								}else{
								?>
								<div class="mail-contents">
									<span class="unread">既読</span><br />
									<div class="mail-inner">
										<?php print($mail_list['title'][$i]); ?><?php print nl2br($mail_list['message'][$i]); ?>
									</div>
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
						<div id="more-read"  class="first-more-button">
							<a id="more-button" class="first-more-button"><img src="/images/smart/button/button-more.png" /></a>
						</div>
					<?php } ?>

					<div class="clear"></div>

				<?php
				}else{
				?>

					<div id="exection">早速メールしてみよう！<?php print($post_send_id); ?></div>
					<div class="clear"></div>


				<?php } ?>

			</div>

			<?php
			/********************************
			**
			**	送信フォーム
			**
			*********************************/
			?>
			<div id="mail-form">
				<div id="message-area">
					<form method="post">
						<input type="text" name="message" id="message" value="" /></div>
						<?php
						# 送信チェック チケット消費確認ダイアログ
						if(MAIL_SEND_CONFIRM == "ON"){
						?>
							<?php
							# membersのconfirmationで判別
							if($members_data['confirmation'] == 0 || $members_data['confirmation'] == 2){
							?>
								<input type="button" id="button-send" name="send-confirm-on" value="&nbsp;" />
							<?php }else{ ?>
								<input type="button" id="button-send" name="send-confirm-off" value="&nbsp;" />
							<?php } ?>
						<?php }else{ ?>
							<input type="button" id="button-send" name="send-confirm-off" value="&nbsp;" />
						<?php } ?>
						<div class="clear"></div>
					</form>
				</div>
				<div id="dialog-ticket">
					<?php print(TICKET_NAME); ?>を1枚消費しますが宜しいですか？<br />
					<?php
					# チケット消費確認ダイアログ 継続チェックする
					if(MAIL_CONFIRM_CHECK == "ON"){
					?>
					<span class="confirm-check"><label><input type="checkbox" name="confirm" value="1" />今後表示しない</label></span>
					<?php
					# 継続チェックしない(常に表示する)
					}else{
					?>
					<input type="hidden" name="confirm" value="0" />
					<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
		</section>

	</div>


<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

