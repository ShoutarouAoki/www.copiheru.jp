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

var num_atap			= 0;

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


	/* メールログBOX拡大 */
	$(".mail-frame-zoomin-pc").click(function() {
		$("#screen-overlay").fadeIn("fast");
		$(".out").fadeOut("fast");
		$(".button-zoomout").show();
		$("#mail-area").animate({
			top: "10px",
			left: "-330px",
			width: "800px",
		}, 250 );
		$("#mail-frame").animate({
			height: "500px"
		}, 250 );
		$("#mail-form").css("position","relative");
		$("#mail-form").css("width","95%");
		$("#mail-form").animate({
			left: "0",
			bottom: "-15px",
		}, 250 );
		return false;
	});


	/* メールログBOX縮小 */
	$(".mail-frame-zoomout-pc").click(function() {
		$("#screen-overlay").fadeOut("fast");
		$(".out").fadeIn("fast");
		$(".button-zoomout").hide();
		$("#mail-area").animate({
			top: "210px",
			left: "-270px"
		}, 250 );
		$("#mail-frame").animate({
			height: "390px"
		}, 250 );
		$("#mail-area").css("width","");
		$("#mail-form").css("position","absolute");
		$("#mail-form").css("width","330px");
		$("#mail-form").animate({
			left: "-70px",
			bottom: "-95px",
		}, 250 );
		return false;
	});


	/* 画像切り替え */
	$(".image-read").click(function() {
		if(!num_atap){
			num_atap++;

			let element					= $(this);
			let mails_image_id			= element.attr("id");

			if(mails_image_id){
				imageRead(mails_image_id);
			}else{
				//非同期の終了
				num_atap = 0;
			}
		}
		return false;

	});


	/* 画像切り替え 既読処理直後 */
	$(".read-space").click(function() {
		if(!num_atap){
			num_atap++;

			let element					= $(this);
			let check_name				= element.attr("name");

			if (check_name == "image") {

				let mails_id_line		= element.attr("id");
				let idArray				= mails_id_line.split('-');
				let mails_id			= idArray[2];
				let mails_image_id		= "mails-image-" + mails_id;

				if(mails_image_id){
					imageRead(mails_image_id);
				}else{
					//非同期の終了
					num_atap = 0;
				}

			}
		}
		return false;

	});


	/* もっと読む */
	$(".first-more-button").click(function() {
		if(!num_atap){
			num_atap++;
			$(".loading").fadeIn();

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
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				},
				// 通信失敗
				error: function(XMLHttpRequest, textStatus, errorThrown) {

					// エラーメッセージ(ダイアログ)
					showErrorDialog("エラー","読み込みできませんでした");
					$(".more-read").remove();
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				}
			});
		}
		return false;

	});


	/* アイテム利用 */
	$(".item-list").click(function() {
		if(!num_atap){
			//num_atap++;
			
			let element						= $(this);
			let itembox_id_line				= element.attr("id");
			let idArray						= itembox_id_line.split('-');
			let itembox_id					= idArray[2];
			let item_num_line				= "#item-num-" + itembox_id;
			let item_count					= $(item_num_line).text();

			let check_name					= element.attr("name");

			if (check_name == "using") {

				getItemUsingData(itembox_id);

			} else {

				if(item_count == 0){
					item_count					= null;
				}

				if(item_count){

					let item_name_line			= "#item-name-" + itembox_id;
					let item_description_line	= "#item-description-" + itembox_id;
					let item_name				= $(item_name_line).text();
					let item_description		= $(item_description_line).text();

					/* ここでダイアログ */
					let dialog_message			= item_description + "<br />残り数 : " + item_count + " 個<br /><br />このアイテムを使用しますか？";
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
								num_atap++;
								itemUse(itembox_id,item_count);
							},
							"キャンセル": function() {
								$(this).dialog("close");
								//非同期の終了
								num_atap = 0;
								return false;
							}
						}
					});

				}else{
					//非同期の終了
					num_atap = 0;
				}

			}
		}
		return false;

	});


	/* アイテム利用状況確認 */
	$(".item-list-using").click(function() {
		if(!num_atap){
			//num_atap++;
			
			let element						= $(this);
			let itembox_id_line				= element.attr("id");
			let idArray						= itembox_id_line.split('-');
			let itembox_id					= idArray[2];
			let item_class					= element.attr("class");
			let check_name					= element.attr("name");
			let item_num_line				= "#item-num-" + itembox_id;
			let item_count					= $(item_num_line).text();

			// if (check_name == "using") {

				getItemUsingData(itembox_id);

			// }else{

			// 	if(item_count == 0){
			// 		item_count					= null;
			// 	}

			// 	if(item_count){

			// 		let item_name_line			= "#item-name-" + itembox_id;
			// 		let item_description_line	= "#item-description-" + itembox_id;
			// 		let item_name				= $(item_name_line).text();
			// 		let item_description		= $(item_description_line).text();

			// 		/* ここでダイアログ */
			// 		let dialog_message			= item_description + "<br />残り数 : " + item_count + " 個<br /><br />このアイテムを使用しますか？";
			// 		$( "#dialog-item-confirm" ).html(dialog_message);

			// 		$( "#dialog-item-confirm" ).dialog({
			// 			modal: true,
			// 			title: item_name,
			// 			show: {
			// 				effect: 'drop',
			// 				duration: 250,
			// 			},
			// 			hide: {
			// 				effect: 'drop',
			// 				duration: 250,
			// 			},
			// 			buttons: {
			// 				"　ＯＫ　": function() {
			// 					$(this).dialog("close");
			// 					num_atap++;
			// 					itemUse(itembox_id,item_count);
			// 				},
			// 				"キャンセル": function() {
			// 					$(this).dialog("close");
			// 					//非同期の終了
			// 					num_atap = 0;
			// 					return false;
			// 				}
			// 			}
			// 		});

			// 	}else{
			// 		//非同期の終了
			// 		num_atap = 0;
			// 	}

			// }
		}
		return false;

	});


	/* 未読クリック */
	$(".mail-title").click(function() {
		if(!num_atap){
			//num_atap++;

			let dialog_title		= "";
			let dialog_message		= "";
			
			let element					= $(this);
			let mails_id				= element.attr("id");
			let read_type				= element.attr("name");

			let error					= 0;

			/* 画像添付チェック */
			let image_check				= 0;
			let class_name				= element.attr("class");
			let classArray				= class_name.split(" ");

			for(let i=0;i<classArray.length;i++ ){
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
					let user_point				= $(".check-point").text();

					/* 消費ポイント計算 */
					let	point;
					let add_message;
					if (image_check == 0) {
						point				= point_read;
						add_message			= "";
					} else {
						if(point_image > 0){
							point			= point_read + point_image;
							add_message		= "<br /><span style=\"color: #FF0000;\">添付画像がある場合は" + ticket_name + "が<br />＋" + point_image + "個必要です。</span>";
						} else {
							point			= point_read;
							add_message		= "";
						}
					}

					if(user_point < point){

						error					= 1;

					} else {

						/* ここでダイアログ */
						dialog_title		= ticket_name + "確認";
						dialog_message		= ticket_name + "を" + point + "個消費しますが宜しいですか？" + add_message;
						dialog_message			+= "<br /><br />所持" + ticket_name + " : " + user_point + "個";

					}

				/* 無料開封 */
				} else {

					/* ここでダイアログ */
					dialog_title			= "メール開封確認";
					dialog_message			= "このメールの続きを読みますか？<br />";

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
								num_atap++;
								mailRead(mails_id,image_check);
							},
							"キャンセル": function() {
								$(this).dialog("close");
								//非同期の終了
								num_atap = 0;
								return false;
							}
						}
				});

				/* ポイントエラー */
				} else {

					let error_message	= ticket_name + "が不足しております。";
					pointErrorDialog("エラー",error_message);
					//非同期の終了
					num_atap = 0;
					return false;

				}


			} else {

				/* 既読処理 */
				num_atap++;
				mailRead(mails_id,image_check);

			}
		}
		return false;
	});


	/* 送信ボタンクリック */
	$("#button-send").click(function() {
		if(!num_atap){
			//num_atap++;
			//$(".loading").fadeIn();

			let dialog_title		= "";
			let dialog_message		= "";
			
			let message					= $("#message").val();
			let message_count			= message.length;
			let message_max_length		= $("#message-max-length").val();
			let error					= 0;

			if (!message) {
				error					= 1;
			}

			if(message_count > message_max_length){
				error					= 2;
			}

			if (error == 0) {

				/* 確認 */
				let send_type			= $("#button-send").attr("name");

				/* チェックボックス確認 初期化 */
				confirmation			= 0;

				/* チケット消費ダイアログ表示 */
				if(send_type == "send-confirm-on"){


					/* 有料送信 */
					if(point_send > 0){

						/* 常にチェックをつける */
						//$("[name=confirm]").prop("checked",true);

						/* 残りチケット数を取得 */
						let user_point			= $(".check-point").text();

						if(user_point < point_send){

							error				= 1;

						} else {

							/* ここでダイアログ */
							dialog_title	= ticket_name + "確認";

							dialog_message	= ticket_name + "を" + point_send + "個消費しますが宜しいですか？<br /><br />";
							dialog_message		+= "所持" + ticket_name + " : " + user_point + "個";

						}


					/* 無料開封 */
					} else {

						/* ここでダイアログ */
						dialog_title		= "メール送信確認";
						dialog_message		= "送信してもよろしいですか？<br />";

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
									num_atap++;
									mailSend();
								},
								"キャンセル": function() {
									$("#dialog-ticket").dialog( "close" );
									//非同期の終了
									num_atap = 0;
									//$(".loading").fadeOut();
									return false;
								}
							}
						});

					/* ポイントエラー */
					} else {

						let error_message	= ticket_name + "が不足しております。";
						pointErrorDialog("エラー",error_message);
						//非同期の終了
						num_atap = 0;
						//$(".loading").fadeOut();
						return false;

					}


				/* 既にチェック済み */
				} else {

					/* 送信処理 */
					num_atap++;
					mailSend();

				}

			} else {

				// エラーメッセージ(ダイアログ)
				if(error == 1){
					showErrorDialog("エラー","メッセージを入力して下さい");
				} else if (error == 2){
					var error_message	= "送信できるの最大文字数は<span style=\"color: #FF0000;\">" + message_max_length + "</span>までです。";
					showErrorDialog("エラー",error_message);
				} else {
					showErrorDialog("エラー","送信できません");
				}
				//非同期の終了
				num_atap = 0;
				//$(".loading").fadeOut();
			}
		}
		return false;

	});

	/* マニー */
	$(".button-manii-pay").on('click', function() {
		if(!num_atap){
			//無料キャラの時、使用できない
			let dialog_message = "";
			let dialog_error_message = "";
			if(!manii_use_flg){
				/* エラーダイアログ */
				dialog_error_message			= "ここではマニーできません";
				$("#dialog-manii").html(dialog_error_message);
				$("#dialog-manii").dialog({
					resizable: false,
					modal: true,
					title: "マニー",
					show: {
						effect: 'bounce',
						delay: 500,
						duration: 1200,
					},
					buttons: {
						"　ＯＫ　": function() {
							$(this).dialog("close");
							//非同期の終了
							num_atap = 0;
							$(".loading").fadeOut();
							return false;
						},
						"キャンセル": function() {
							$(this).dialog("close");
							//非同期の終了
							num_atap = 0;
							$(".loading").fadeOut();
							return false;
						}
					}
				});
			}else{

				if(manii_amount>=1000){
					/* ここでダイアログ */
					dialog_message			= "マニーしますか？（１０００マニー消費）";
					$("#dialog-manii").html(dialog_message);
					$("#dialog-manii").dialog({
						resizable: false,
						modal: true,
						title: "マニー",
						show: {
							effect: 'bounce',
							delay: 500,
							duration: 1200,
						},
						buttons: {
							"　ＯＫ　": function() {
								//非同期処理の開始
								num_atap++;
								$(".loading").fadeIn();
								$(this).dialog("close");
								payManii();
							},
							"キャンセル": function() {
								$(this).dialog("close");
								//非同期の終了
								num_atap = 0;
								$(".loading").fadeOut();
								return false;
							}
						}
					});
				}else{
					/* エラーダイアログ */
					dialog_error_message			= "マニーが足りません（１０００マニー以上必要）";
					$("#dialog-manii").html(dialog_error_message);
					$("#dialog-manii").dialog({
						resizable: false,
						modal: true,
						title: "マニー",
						show: {
							effect: 'bounce',
							delay: 500,
							duration: 1200,
						},
						buttons: {
							"　ＯＫ　": function() {
								$(this).dialog("close");
								//非同期の終了
								num_atap = 0;
								$(".loading").fadeOut();
								return false;
							},
							"キャンセル": function() {
								$(this).dialog("close");
								//非同期の終了
								num_atap = 0;
								$(".loading").fadeOut();
								return false;
							}
						}
					});
				}
			}
		}
		return false;
	});

	$(".item-list-manii").on('click', function() {
		if(!num_atap){
			//無料キャラの時、使用できない
			let dialog_message = "";
			let dialog_error_message			= "";
			if(!manii_use_flg){
				/* エラーダイアログ */
				dialog_error_message			= "ここではマニーできません";
				$("#dialog-manii").html(dialog_error_message);
				$("#dialog-manii").dialog({
					resizable: false,
					modal: true,
					title: "マニー",
					show: {
						effect: 'bounce',
						delay: 500,
						duration: 1200,
					},
					buttons: {
						"　ＯＫ　": function() {
							$(this).dialog("close");
							//非同期の終了
							num_atap = 0;
							$(".loading").fadeOut();
							return false;
						},
						"キャンセル": function() {
							$(this).dialog("close");
							//非同期の終了
							num_atap = 0;
							$(".loading").fadeOut();
							return false;
						}
					}
				});
			}else{

				if(manii_amount>=1000){
					/* ここでダイアログ */
					dialog_message			= "マニーしますか？（１０００マニー消費）";
					$("#dialog-manii").html(dialog_message);
					$("#dialog-manii").dialog({
						resizable: false,
						modal: true,
						title: "マニー",
						show: {
							effect: 'bounce',
							delay: 500,
							duration: 1200,
						},
						buttons: {
							"　ＯＫ　": function() {
								//非同期処理の開始
								num_atap++;
								$(".loading").fadeIn();
								$(this).dialog("close");
								payManii();
								//$.colorbox.close();
							},
							"キャンセル": function() {
								//非同期の終了
								num_atap = 0;
								$(".loading").fadeOut();
								$(this).dialog("close");
								return false;
							}
						}
					});
				}
			}
		}
		return false;
	});
	
	/* 文字数チェック */
	$("#message").bind("change keyup",function(){
		let message_max_length	= $("#message-max-length").val();
		let count				= $(this).val().length;
		let remaining			= message_max_length - count;
		$("#text-length").text(remaining);
	});

	/* テキストエリア選択時のEnterキーの動作を無視させる */
	$("input[type=text]").on("keydown", function(e) {
		if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
			return false;
		} else {
			return true;
		}
	});

});


