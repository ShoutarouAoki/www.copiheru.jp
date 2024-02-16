/**************************************************************
**
**	mail.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	メール用 javascript設定
**	
**
**************************************************************/

var post_send_id		= 0
var post_parent_id		= 0
var first_mail			= 0;
var last_mail_id		= 0;
var next_before_id		= 0;
var	confirmation		= 0;
var item_using			= 0;
var	colorbox_width		= 0;
var point_send			= 0;
var point_read			= 0;
var point_image			= 0;
var status				= 0;
var sleep				= 0;
var sleep_check			= 0;
var stop				= 0;
var message_max_length	= 80;
var ticket_name			= null;
var sender				= null;
var more_path			= null;

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
			width:colorbox_width,
			href:"#item-use",
			opacity: 0.7,
			returnFocus: false
		});
	});


	/* メニューモーダル Android */
	$("#menu-button").click(function(){
	    $.colorbox({
			inline:true,
			width:colorbox_width,
			href:"#menu-list",
			opacity: 0.7,
			returnFocus: false
		});
	});


	/* チケットモーダル Android */
	$("#ticket-button").click(function(){
	    $.colorbox({
			inline:true,
			width:colorbox_width,
			href:"#ticket-area",
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


	/* 画像切り替え */
	$(".image-read").click(function() {

		var element					= $(this);
		var mails_image_id			= element.attr("id");

		if(mails_image_id){
			imageRead(mails_image_id);
		}

		return false;

	});


	/* 画像切り替え 既読処理直後 */
	$(".read-space").click(function() {

		var element					= $(this);

		var check_name				= element.attr("name");

		if (check_name == "image") {

			var mails_id_line		= element.attr("id");
			var idArray				= mails_id_line.split('-');
			var mails_id			= idArray[2];
			var mails_image_id		= "mails-image-" + mails_id;

			if(mails_image_id){
				imageRead(mails_image_id);
			}

		}

		return false;

	});


	/* もっと読む */
	$(".first-more-button").click(function() {

		$.ajax({
			type: "POST",
			url: more_path,
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


	/* アイテム利用 */
	$(".item-list").click(function() {

		var element						= $(this);
		var item_id_line				= element.attr("id");
		var idArray						= item_id_line.split('-');
		var item_id						= idArray[2];
		var item_num_line				= "#item-num-" + item_id;
		var item_count					= $(item_num_line).text();

		var check_name					= element.attr("name");

		if (check_name == "using") {

			getItemUsingData(item_id);

		} else {

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
					show: {
						effect: 'drop',
						duration: 250,
					},
					hide: {
						effect: 'drop',
						duration: 250,
					},
					buttons: {
						"　ＯＫ　": function() {
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

		}

		return false;

	});


	/* アイテム利用状況確認 */
	$(".item-list-using").click(function() {

		var element						= $(this);
		var item_id_line				= element.attr("id");
		var idArray						= item_id_line.split('-');
		var item_id						= idArray[2];
		var item_class					= element.attr("class");
		var check_name					= element.attr("name");
		var item_num_line				= "#item-num-" + item_id;
		var item_count					= $(item_num_line).text();

		if (check_name == "using") {

			getItemUsingData(item_id);

		}else{

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
					show: {
						effect: 'drop',
						duration: 250,
					},
					hide: {
						effect: 'drop',
						duration: 250,
					},
					buttons: {
						"　ＯＫ　": function() {
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

		}

		return false;

	});


	/* 未読クリック */
	$(".mail-title").click(function() {

		var element					= $(this);
		var mails_id				= element.attr("id");
		var read_type				= element.attr("name");

		var error					= 0;

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

				/* 常にチェックをつける */
				//$("[name=confirm]").prop("checked",true);

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

				if(user_point < point){

					error					= 1;

				} else {

					/* ここでダイアログ */
					var dialog_title		= ticket_name + "確認";
					var dialog_message		= ticket_name + "を" + point + "枚消費しますが宜しいですか？" + add_message;
					dialog_message			+= "<br /><br />残り" + ticket_name + "枚数 : " + user_point + "枚";

				}

			/* 無料開封 */
			} else {

				/* ここでダイアログ */
				var dialog_title			= "メール開封確認";
				var dialog_message			= "このメールの続きを読みますか？<br />";

				/* 有料ユーザー */
				if(status == 0){
					dialog_message			+= "<span style=\"color: red;\">※" + ticket_name + "は必要ありません</span>";
				}


			}

			if(error == 0){

				$("#dialog-ticket").html(dialog_message);
				$("#dialog-ticket").dialog({
					modal: true,
					title: dialog_title,
					show: {
						effect: 'drop',
						duration: 250,
					},
					hide: {
						effect: 'drop',
						duration: 250,
					},
					buttons: {
						"　ＯＫ　": function() {
							/* チェックボックス */
							var check			= $("[name=confirm]").prop("checked");
							if(check){
								confirmation	= 1;
								$(".read-check").attr("name","read-confirm-off");
							}
							$(this).dialog("close");
							mailRead(mails_id,image_check);
						},
						"キャンセル": function() {
							$(this).dialog("close");
							return false;
						}
					}
			});

			/* ポイントエラー */
			} else {

				var error_message	= ticket_name + "が不足しております。";
				pointErrorDialog("エラー",error_message);
				return false;

			}


		} else {

			/* 既読処理 */
			mailRead(mails_id,image_check);

		}

		return false;


	});


	/* 送信ボタンクリック */
	$("#button-send").click(function() {

		var message					= $("#message").val();
		var error					= 0;

		if (message) {

			/* 確認 */
			var send_type			= $("#button-send").attr("name");

			/* チェックボックス確認 初期化 */
			confirmation			= 0;

			/* チケット消費ダイアログ表示 */
			if(send_type == "send-confirm-on"){


				/* 有料送信 */
				if(point_send > 0){

					/* 常にチェックをつける */
					//$("[name=confirm]").prop("checked",true);

					/* 残りチケット数を取得 */
					var user_point			= $(".check-point").text();

					if(user_point < point_send){

						error				= 1;

					} else {

						/* ここでダイアログ */
						var dialog_title	= ticket_name + "確認";

						var dialog_message	= ticket_name + "を" + point_send + "枚消費しますが宜しいですか？<br /><br />";
						dialog_message		+= "残り" + ticket_name + "枚数 : " + user_point + "枚";

					}


				/* 無料開封 */
				} else {

					/* ここでダイアログ */
					var dialog_title		= "メール送信確認";
					var dialog_message		= "送信してもよろしいですか？<br />";

					/* 有料ユーザー */
					if(status == 0){
						dialog_message		+= "<span style=\"color: red;\">※" + ticket_name + "は必要ありません</span>";
					}

				}

				/* 処理OK */
				if(error == 0){

					$("#dialog-ticket").html(dialog_message);
					$("#dialog-ticket").dialog({
						resizable: false,
						modal: true,
						title: dialog_title,
						show: {
							effect: 'drop',
							duration: 250,
						},
						hide: {
							effect: 'drop',
							duration: 250,
						},
						buttons: {
							"　ＯＫ　": function() {
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

				/* ポイントエラー */
				} else {

					var error_message	= ticket_name + "が不足しております。";
					pointErrorDialog("エラー",error_message);
					return false;

				}


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


