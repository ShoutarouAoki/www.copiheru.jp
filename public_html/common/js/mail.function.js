/**************************************************************
**
**	mail.function.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	メール用 function群
**	
**
**************************************************************/


/************************************
**
**	javascript内の変数定義
**
************************************/

function constructDefine(send_id,parent_id,mail_id,before_id,item_check,colorbox,point_s,point_r,point_i,user_status,sleep_flg,ticket,send,more) {

	post_send_id	= send_id;
	post_parent_id	= parent_id;
	first_mail		= mail_id;
	next_before_id	= before_id;
	item_using		= item_check;
	colorbox_width	= colorbox;
	point_send		= point_s;
	point_read		= point_r;
	point_image		= point_i;
	status			= user_status;
	sleep			= sleep_flg;
	ticket_name		= ticket;
	sender			= send;
	more_path		= more;

	return true;

}



/************************************
**
**	おやすみ画像切り替えチェック
**
************************************/

function checkSleep() {
	if(!num_atap){
		num_atap++;
		$(".loading").fadeIn();

		// 日中は動かさない
		let now			= new Date();
		let hours		= now.getHours();
		let minutes		= now.getMinutes();
		let seconds		= now.getSeconds();

		if(hours > 6 && hours < 23){
			stop		= 0;
			return false;
		}

		stop			= 1;

		$.ajax({
			type:'POST',
			url: "/mail/sleep/",
			dataType: 'json',
			data : {id : post_send_id, sleep : sleep },
			timeout:10000,
			cache: false,
			// 通信成功
			success: function(data) {

				let error = data.error;

				// OK
				if (error == 0) {

					if(data.animation){

						$("#character-image").remove();
						$("#animation-display").remove();
						$("#mail-detail").append("<section id=\"animation-display\"></section>");
						$("#mail-detail").append("<section id=\"character-image\"></section>");

						$("#character-image").html(data.media);
						$("#animation-display").html(data.animation);
						var frame			= new ParaParaData();
						$("#character-image img").each(function (i) {
							frame.data[ParaPara.zeroPadding(i, 4)] = this;
						});
						var anime			= new ParaPara(frame, $("#animation-display"));
						anime.repeat		= true;
						anime.play();

						// ダイアログ
						if(data.message){
							$("#dialog-box").html(data.message);
							$("#dialog-box").dialog({
								resizable: false,
								modal: true,
								title: data.title,
								show: {
									effect: 'bounce',
									delay: 500,
									duration: 1200,
								},
								buttons: {
									"　ＯＫ　": function() {
										$(this).dialog("close");
									}
								}
							});
						}

						sleep			= data.sleep;
					}

					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();

				// ERROR
				} else {
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();

				}

			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			}

		});
	}
	return false;

}



/************************************
**
**	メッセージチェックタイマー
**
************************************/

function checkNewMessage() {

	// 他の処理が動いてる時は動かさない
	if(stop == 1){
		//stop			= 0;
		return false;
	}

	if(!num_atap){
		num_atap++;
		stop				= 1;
		$(".loading").fadeIn();

		// class : chara-mail内の最新メールIDを取得
		$($(".chara-mail").get().reverse()).each(function(i) {
			last_mail_id	= $(this).attr("id");
		});

		if (post_parent_id > 0) {
			var post_id		= post_parent_id;
		} else {
			var post_id		= post_send_id;
		}
		
		$.ajax({
			type:'POST',
			url: "/mail/check/",
			data : {id : post_id, last_mail_id : last_mail_id },
			timeout:10000,
			cache: false,
			// 通信成功
			success: function(html) {
				
				if(html){

					// 最新メールあり
					$("#add-area").prepend(html);

					var new_mail_id		= 0;

					$($(".chara-mail").get().reverse()).each(function(i) {
						new_mail_id	= $(this).attr("id");
					});

					var new_mail_line	= "#" + new_mail_id;
					$(new_mail_line).fadeIn(1000);

				}

				$(".hide").fadeIn(1000);
				
				//マニーチェック
				stop				= 0;
				if(manii_use_flg){
					checkManii();
				}else{
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				}
			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//マニーチェック
				stop				= 0;
				if(manii_use_flg){
					checkManii();
				}else{
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				}
			}

		});

		stop				= 0;
	}
	return false;

}

/************************************
**
**	マニーチェック＆リセットチェック(返信画面)
**	===============================
**	setIntervalで使用
**
************************************/

function checkManii() {
	// 他の処理が動いてる時は動かさない
	if(stop == 1){
		return false;
	}

	// タイマーストップ
	stop				= 1;

	$.ajax({
		type:'POST',
		url: "/manii/index/",
		dataType: 'json',
		data : {chara_id : post_send_id },
		timeout:10000,
		cache: false,
		// 通信成功
		success: function(data) {

			var error = data.error;
			
			// OK
			if (error == 0) {
				//リセット処理
				if(data.reset>0){
					$("#dialog-manii").html(data.message);
					$("#dialog-manii").dialog({
						resizable: false,
						modal: true,
						title: "マニーリセット",
						show: {
							effect: 'bounce',
							delay: 500,
							duration: 1200,
						},
						buttons: {
							"　ＯＫ　": function() {
								$(this).dialog("close");
							}
						}
					});

					//累積マニーが閾値を越えた場合
					manii_over_animation=0;
					$("#manii-gauge-flash").css("display", "none");
				}

				//その他表示変更等
				// マニーゲージアップ
				if(data.paid_manii<=data.threshould){
					$("#manii-gauge-screen").animate({ 
						height: (100-100*data.paid_manii/data.threshould)+"%"
					}, 1000 );
				}else{
					$("#manii-gauge-screen").animate({ 
						height: "0%"
					}, 1000 );
				}
				
				// マニー最大値情報を切り替え
				let str_over = data.paid_manii>=data.threshould ? "<br/>突破!!" : "";
				let display_max = "MAX<br/>" + data.threshould + str_over;
				$("#manii-crown").html(display_max);

				// 累積マニー数値を切り替え
				let display_manii = data.paid_manii + "マニー";
				$("#manii-percent").html(display_manii);

				//持ちマニー数
				$(".check-point-manii").html(data.having_manii);
				
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
				
			}else{//エラーあり
				// エラーメッセージ(ダイアログ)
				if(data.errormessage){
					showErrorDialog("エラー",data.errormessage);
				}
				
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			}
		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			//非同期の終了
			num_atap = 0;
			$(".loading").fadeOut();
		}

	});
	
	stop				= 0;

	return false;
}

/************************************
**
**	好感度＆マニーゲージ切替(返信画面)
**	===============================
**
************************************/
function onChangeGauge(){
	if(!gauge_flg){
		gauge_flg = 1;
		$("#favorite-area").removeClass("gauge-area-front");
		$("#favorite-area").addClass("gauge-area-back");
		$("#favorite-percent").css("display", "none");

		$("#manii-area").removeClass("gauge-area-back");
		$("#manii-area").addClass("gauge-area-front");
		$("#manii-percent").css("display", "block");
		//manii_form_open();
	}else{
		gauge_flg = 0;

		$("#manii-area").removeClass("gauge-area-front");
		$("#manii-area").addClass("gauge-area-back");
		$("#manii-percent").css("display", "none");

		$("#favorite-area").removeClass("gauge-area-back");
		$("#favorite-area").addClass("gauge-area-front");
		$("#favorite-percent").css("display", "block");
		//manii_form_close();
	}
}

/************************************
**
**	メニューバーON/OFF(返信画面)
**	===============================
**
************************************/
function menu_bar_open(){
	$("#menu-bar").removeClass("menu-bar-close").addClass("menu-bar-open");
}

function menu_bar_close(){
	$("#menu-bar").removeClass("menu-bar-open").addClass("menu-bar-close");
}

/************************************
**
**	マニーボタンON/OFF(返信画面)
**	===============================
**
************************************/
function manii_form_open(){
	$("#menu-bar").removeClass("menu-bar-close").addClass("menu-bar-open");
}

function manii_form_close(){
	$("#menu-bar").removeClass("menu-bar-open").addClass("menu-bar-close");
}

/************************************
**
**	マニー(返信画面)
**	===============================
**
************************************/

function payManii() {
	// タイマーストップ
	stop				= 1;
	
	$.ajax({
		type:'POST',
		url: "/manii/pay/",
		dataType: 'json',
		data : {chara_id : post_send_id },
		timeout:10000,
		cache: false,
		// 通信成功
		success: function(data) {
			
			var error = data.error;

			// OK
			if (error == 0) {
				
				//リセット処理がある場合は
				if(data.reset>0){
					$("#dialog-manii").html(data.message);
					$("#dialog-manii").dialog({
						resizable: false,
						modal: true,
						title: "スケジュール切替",
						show: {
							effect: 'bounce',
							delay: 500,
							duration: 1200,
						},
						buttons: {
							"　ＯＫ　": function() {
								$(this).dialog("close");
							}
						}
					});

					//累積マニーが閾値を越えた場合
					manii_over_animation=0;
					$("#manii-gauge-flash").css("display", "none");
				}
				
				//その他表示変更等
				// マニーゲージアップ
				if(data.paid_manii<=data.threshould){
					$("#manii-gauge-screen").animate({ 
						height: (100-100*data.paid_manii/data.threshould)+"%"
					}, 1000 );
				}else{
					$("#manii-gauge-screen").animate({ 
						height: "0%"
					}, 1000 );
				}

				// マニー最大値情報を切り替え
				let str_over = data.paid_manii>=data.threshould ? "<br/>突破!!" : "";
				let display_max = "MAX<br/>" + data.threshould + str_over;
				$("#manii-crown").html(display_max);

				// 累積マニー数値を切り替え
				let display_manii = data.paid_manii + "マニー";
				$("#manii-percent").html(display_manii);

				//持ちマニー数
				$(".check-point-manii").html(data.having_manii);
				manii_amount = data.having_manii;
				
				//累積マニーが閾値を越えた場合
				if(data.paid_manii >= data.threshould && !manii_over_animation){
					manii_over_animation=1;
					$("#manii-gauge-flash").css("display", "block");
				}

				//アイテム欄操作
				let item_id_line	= "#item-id-" + data.itembox_data_id;
				let item_num_line	= "#item-num-" + data.itembox_data_id;

				//$(".loading").fadeOut();

				if(data.having_manii == 0){
					$(item_num_line).remove();
					$(item_id_line).addClass("item-last");
				}else{
					$(item_num_line).html(data.having_manii/1000);
				}
				
				//WebGLアニメ
				//リセット処理がない場合
				if(!data.reset){
					startParticles();
				}
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();

			}else{//エラーあり
				$("#dialog-manii").html(data.error_message);
				$("#dialog-manii").dialog({
					resizable: false,
					modal: true,
					title: "エラー",
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

						}
					}
				});
			}
		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			//console.log("textStatus:"+textStatus);
			//非同期の終了
			num_atap = 0;
			$(".loading").fadeOut();
		}

	});
	
	stop				= 0;
	return false;
}

/************************************
**
**	アイテムリスト表示(返信画面)
**	===============================
**	未使用
**
************************************/

function getItemList(device) {

	// タイマーストップ
	stop				= 1;

	$.ajax({
		type: "POST",
		url: "/item/mail/",
		data : {id : post_send_id },
		timeout:10000,
		cache: false,
		// 成功
		success: function(html){
			$("#item-area").html(html);
			if (device == 1) {
				$("#modal-overlay").fadeIn("fast");
			} else if (device == 2){
			    $.colorbox({
					inline:true,
					width:colorbox_width,
					href:"#item-use",
					opacity: 0.7,
					returnFocus: false
				});
			}

		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {

			// エラーメッセージ(ダイアログ)
			showErrorDialog("エラー","読み込みできませんでした");
			$(".more-read").remove();

		}
	});

	stop				= 0;
	return false;

}



/************************************
**
**	アイテム使用処理 : Ajax
**
************************************/

function itemUse(item_id,item_count) {

	if (item_id && item_count) {

		// タイマーストップ
		stop			= 1;

		$(".loading").fadeIn();

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

					var item_id_line	= "#item-id-" + item_id;
					var item_num_line	= "#item-num-" + item_id;
					var item_use_line	= "#item-use-" + item_id;

					// 残りアイテム数計算
					var item_count_new	= item_count - 1;

					//$(".md-close").trigger("click");
					$(".loading").fadeOut();

					var type		 = data.type;
					var limit_time	 = data.limit_time;
					var limit_count	 = data.limit_count;

					var display		 = "<p class=\"item-using\">現在使用中</p>";
					var item_check	 = "<span class=\"item-using-check\">使用中</span>";

					// アイテム使用中表示に変更
					$(item_id_line).attr("name","using");
					$(item_id_line).removeClass("item-list");
					$(item_id_line).addClass("item-list-using");
					$(item_use_line).html(display);
					$("#item-using-check").html(item_check);

					if(item_count_new == 0){
						$(item_num_line).remove();
						$(item_id_line).addClass("item-last");
					}else{
						$(item_num_line).html(item_count_new);
					}

					// アイテム使用中文言表示
					if(data.word){
						var use_id			= "item-word-" + data.itemuse_id;
						var use_word		= "<div id=\"" + use_id + "\">" + data.word + "</div>";
						$("#word-area").append(use_word);
					}

					// 使用時メッセージ(ダイアログ)
					if(data.message){
						var dialog_title	= data.name + "を使用しました！";
						$("#dialog-item-use").html(data.message);
						$("#dialog-item-use").dialog({
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
									$(this).dialog("close");
									//非同期の終了
									num_atap = 0;
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
					
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				}

			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {

				showErrorDialog("エラー","正常に処理できませんでした");
				
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			}

		});

	} else {

		showErrorDialog("エラー","正常に処理できませんでした");
		//非同期の終了
		num_atap = 0;
	}

	stop				= 0;
	return false;

}



/************************************
**
**	アイテム終了処理
**
************************************/

function itemUseEnd(count,list,word,remaining,message){

	var item_count		= count;
	var item_list		= list;
	var item_word		= word;
	var item_remaining	= remaining;

	// 有効期限、利用回数が切れたアイテムがあれば
	if(item_count > 0){

		var check_id		= "";

		// 複数
		if(item_count > 1){

			// カンマで分割し配列に格納
			var idArray		= item_list.split(',');
			var wordArray	= item_word.split(',');

			// 配列数分（切り出しされた文字列数分）ループして内容を表示する
			for (var i=0; i<idArray.length; i++) {

				check_id		= idArray[i];
				word_id			= wordArray[i];
				itemUseEndExection(check_id,word_id);

			}

		// ひとつ
		}else{

			check_id		= item_list;
			word_id			= item_word;
			itemUseEndExection(check_id,word_id);

		}

		if(message){

			// 有効期限終了告知(ダイアログ)
			var dialog_title	= "有効期限終了";
			$("#dialog-item-use").html(message);
			$("#dialog-item-use").dialog({
				resizable: false,
				modal: true,
				title: dialog_title,
				show: {
					effect: 'bounce',
					delay: 500,
					duration: 1200,
				},
				buttons: {
					"　ＯＫ　": function() {
						$(this).dialog("close");
					}
				}
			});
		}


	}

	// 使用中のアイテムが1つもなくなれば【使用中】を消す
	if(item_remaining == 0){
		$("#item-using-check").html("");
	}

}



/************************************
**
**	アイテム終了実行処理
**
************************************/

function itemUseEndExection(itembox_id,word_id){

	if(itembox_id){

		var itembox_id_line		= "";
		var limit_line			= "";
		var	check_itembox_id	= "";

		itembox_id_line			= "#item-id-" + itembox_id;
		limit_line				= "#item-use-" + itembox_id;
		$(itembox_id_line).removeClass("item-list-using");
		$(itembox_id_line).addClass("item-list");
		$(itembox_id_line).removeAttr("name");
		$(limit_line).html("");

		if(word_id){
			var item_use_word	= "#item-word-" + word_id;
			$(item_use_word).remove();
		}

		// 所持数もなくなったら
		$($(".item-last").get()).each(function(i) {
			check_itembox_id	= $(this).attr("id");
			if(itembox_id == check_itembox_id){
				$(itembox_id_line).remove();
			}
		});

	}

}



/************************************
**
**	アイテム利用状況 : Ajax
**
************************************/

function getItemUsingData(itembox_id) {

	if (itembox_id) {

		$(".loading").fadeIn();

		$.ajax({
			type:'POST',
			url: "/item/using/",
			dataType: 'json',
			data : {id : post_send_id, itembox_id : itembox_id },
			timeout:10000,
			cache: false,
			// 通信成功
			success: function(data) {

				var error = data.error;

				// OK
				if (error == 0) {

					$(".loading").fadeOut();

					var itembox_id_line = "#item-id-" + itembox_id;
					var type		 = data.type;
					var limit_time	 = data.limit_time;
					var limit_count	 = data.limit_count;


					var display		 = "<span class=\"style-item-using\">現在使用中</span><br /><br />";
					display			+= "【有効期限】<br />残り";

					if(type == 1) {
						display		+= "<span class=\"limit-count\">" + limit_time + "</span>分";
					} else if (type == 2) {
						display		+= "<span class=\"limit-count\">" + limit_count + "</span>回";
					} else if (type == 3) {
						display		+= "<span class=\"limit-count\">" + limit_time + "</span>分";
					}

					// アイテム状況詳細(ダイアログ)
					var dialog_title	= data.name;
					$("#dialog-item-use").html(display);
					$("#dialog-item-use").dialog({
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
								$(this).dialog("close");
								//非同期の終了
								num_atap = 0;
							}
						}
					});

				// ERROR
				} else {

					// エラーメッセージ(ダイアログ)
					if(data.errormessage){
						showErrorDialog("エラー",data.errormessage);
					}
					
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				}

			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {

				showErrorDialog("エラー","正常に処理できませんでした");
				
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			}

		});

	} else {

		showErrorDialog("エラー","正常に処理できませんでした");
		//非同期の終了
		num_atap = 0;
	}

	return false;

}



/************************************
**
**	キャンペーン開始・終了処理
**
************************************/

function setCampaignDisplay(campaign_status,campaign_send,campaign_read,campaign_all,check_status){

	if (!campaign_status) {
		return false;
	}

	// キャンペーンスタート
	if (campaign_status == 2) {

		// 通常ステータスなら表示変更
		if(check_status == 1){

			var icon			= "";

			if(campaign_all){
				var icon		= "<div class=\"mail-status-check\" name=\"all\"><img src=\"/images/icon/icon-free-all.png\" /></div>";
			} else if (campaign_send) {
				var icon		= "<div class=\"mail-status-check\" name=\"send\"><img src=\"/images/icon/icon-free-send.png\" /></div>";
			} else if (campaign_read) {
				var icon		= "<div class=\"mail-status-check\" name=\"read\"><img src=\"/images/icon/icon-free-read.png\" /></div>";
			}

			if(icon){
				$("#menu-area").append(icon);
			}

		}

		var dialog_title	= "キャンペーンスタート！";
		var dialog_message	= "キャンペーンがスタートしました！";

	// キャンペーン終了
	} else if (campaign_status == 3) {

		// 通常ステータスなら表示変更
		if(check_status == 1){
			$(".mail-status-check").remove();
		}

		var dialog_title	= "キャンペーン終了";
		var dialog_message	= "キャンペーン開催期間が終了致しました。";

	// その他は終了
	} else {

		return false;

	}

	// ダイアログ
	$("#dialog-box").html(dialog_message);
	$("#dialog-box").dialog({
		resizable: false,
		modal: true,
		title: dialog_title,
		show: {
			effect: 'bounce',
			delay: 500,
			duration: 1200,
		},
		buttons: {
			"　ＯＫ　": function() {
				$(this).dialog("close");
			}
		}
	});


	return true;

}



/************************************
**
**	メール添付画像切り替え処理
**
************************************/

function imageRead(mails_image_id) {

	if (mails_image_id) {

		// タイマーストップ
		stop				= 1;

		$(".loading").fadeIn();

		$.ajax({
			type:'POST',
			url: "/mail/image/",
			dataType: 'json',
			data : {id : post_send_id, mails_image_id : mails_image_id },
			timeout:10000,
			cache: false,
			// 通信成功
			success: function(data) {

				var error = data.error;

				// OK
				if (error == 0) {

					if(data.animation){

						$("#character-image").remove();
						$("#animation-display").remove();
						$("#mail-detail").append("<section id=\"animation-display\"></section>");
						$("#mail-detail").append("<section id=\"character-image\"></section>");

						$("#character-image").html(data.media);
						$("#animation-display").html(data.animation);
					    var frame			= new ParaParaData();
					    $("#character-image img").each(function (i) {
					        frame.data[ParaPara.zeroPadding(i, 4)] = this;
					    });
					    var anime			= new ParaPara(frame, $("#animation-display"));
					    anime.repeat		= true;
						anime.play();

					}

					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				// ERROR
				} else {

					showErrorDialog("エラー",data.errormessage);
					
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				}

			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {

				showErrorDialog("エラー","続きを取得できませんでした");
				
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			}

		});

	} else {

		showErrorDialog("エラー","正常に処理できませんでした");
		//非同期の終了
		num_atap = 0;
		$(".loading").fadeOut();
	}

	stop				= 0;
	return false;

}

/************************************
**
**	メール閲覧・送信時のダイアログ出力 : mailRead,mailSend
**
************************************/

/* レベルアッププレゼントがあったら */
function dialog_levelup_present(data,dialog_call01){
	if(data.present_check && data.present_dialog){

		if ($(".check-present").length) {
			var present_count		= $(".check-present").text();
			var present_count_new	= parseInt(present_count) + parseInt(data.present_check);
			$(".check-present").html(present_count_new);
		} else {
			var text	= '<p class="check-present">' + data.present_check + '</p>';
			$("#present-menu").append(text);
		}

		$("#dialog-box").html(data.present_dialog);
		$("#dialog-box").dialog({
			resizable: false,
			modal: true,
			title: "レベルアッププレゼント！",
			show: {
				effect: 'bounce',
				delay: 500,
				duration: 1200,
			},
			buttons: {
				"　ＯＫ　": function() {
					$(this).dialog("close");
					/* 応援報酬、次に呼ぶ関数はメール添付プレゼント */
					dialog_call01(data,dialog_mail_present);
				}
			}
		});

	}else{
		/* 応援報酬、次に呼ぶ関数はメール添付プレゼント */
		dialog_call01(data,dialog_mail_present);
	}/* レベルアッププレゼントがあったら */
}

/* 応援ポイント報酬 */
function dialog_support_reward(data,dialog_call01){
	if(data.support_rewards_title && data.support_rewards_message ){
		if ($(".check-present").length) {
			var present_count		= $(".check-present").text();
			var present_count_new	= parseInt(present_count) + parseInt(data.support_rewards_count);
			$(".check-present").html(present_count_new);
		} else {
			var text	= '<p class="check-present">' + data.support_rewards_count + '</p>';
			$("#present-menu").append(text);
		}

		$("#dialog-rewards").html(data.support_rewards_message);
		$("#dialog-rewards").dialog({
			resizable: false,
			modal: true,
			title: data.support_rewards_title,
			show: {
				effect: 'bounce',
				delay: 500,
				duration: 1200,
			},
			buttons: {
				"　ＯＫ　": function() {
					$(this).dialog("close");
					/* プレゼントタグ */
					dialog_call01(data);
				}
			}
		});
	}else{
		/* プレゼントタグ */
		dialog_call01(data);
	}
}

/* メール添付特別プレゼント */
function dialog_mail_present(data){
	if(data.dialog_mail_present_title.length){
		if ($(".check-present").length) {
			var present_count		= $(".check-present").text();
			var present_count_new	= parseInt(present_count) + parseInt(data.dialog_mail_present_count);
			$(".check-present").html(present_count_new);
		} else {
			var text	= '<p class="check-present">' + data.dialog_mail_present_count + '</p>';
			$("#present-menu").append(text);
		}

		$("#dialog-mail-presents").html(data.dialog_mail_present_message);
		$("#dialog-mail-presents").dialog({
			resizable: false,
			modal: true,
			title: data.dialog_mail_present_title,
			show: {
				effect: 'bounce',
				delay: 500,
				duration: 1200,
			},
			buttons: {
				"　ＯＫ　": function() {
					$(this).dialog("close");
				}
			}
		});

	}
}

/************************************
**
**	メール開封/既読処理 : Ajax
**
************************************/
function mailRead(mails_id,image_check) {

	if (mails_id) {

		// タイマーストップ
		stop				= 1;

		$(".loading").fadeIn();

		$.ajax({
			type:'POST',
			url: "/mail/read/",
			dataType: 'json',
			data : {id : post_send_id, first_mail : first_mail, mails_id : mails_id, image_check : image_check, confirmation : confirmation },
			timeout:10000,
			cache: false,
			// 通信成功
			success: function(data) {
				
				let error = data.error;

				// OK
				if (error == 0) {

					let mail_line			= "#" + mails_id;
					let read_space			= "#read-space-" + data.id;
					let read_contents		= "<div class=\"mail-contents\">";
					read_contents			+= "<span class=\"read\">既読</span><br />";
					read_contents			+= "<div class=\"mail-inner\">" + data.title + data.message + "</div>";
					if(data.animation){
						let mail_image		= "id=\"mails-image-" + data.id + "\"";
						read_contents		+= "<div " + mail_image + ">【画像を見る】</div>";
					}
					read_contents			+= "</div>";

					$(read_space).append(read_contents);
					$(mail_line).remove();

					if(data.animation){

						$(read_space).attr("name","image");
						$("#character-image").remove();
						$("#animation-display").remove();
						$("#mail-detail").append("<section id=\"animation-display\"></section>");
						$("#mail-detail").append("<section id=\"character-image\"></section>");

						$("#character-image").html(data.media);
						$("#animation-display").html(data.animation);
					    var frame			= new ParaParaData();
					    $("#character-image img").each(function (i) {
					        frame.data[ParaPara.zeroPadding(i, 4)] = this;
					    });
					    var anime			= new ParaPara(frame, $("#animation-display"));
					    anime.repeat		= true;
						anime.play();

					}

					$(".loading").fadeOut();


					// 初期化
					first_mail				= 0;
					post_parent_id			= 0;
					
					//----
					// 好感度操作
					let favorite_level		= "Lv." + data.favorite_level;
					let favorite_percent	= data.favorite_percent + "％";
					let favorite_gauge		= data.favorite_gauge + "%";

					
					// 表示変更
					if(data.level_up){

						// 好感度ゲージオーバレイを一旦0%にする(表示上100%)
						$("#favorite-gauge-screen").animate({ 
							height: "0%"
						}, 1000 );

						// 好感度レベルアップ
						$("#favorite-level").html(favorite_level);

						// 好感度ゲージを100%にする(表示上0%)
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
					//----
					
					// ポイント消費
					if(status == 0){

						$(".check-point").html(data.ticket);

						if(data.user_point != null){
							$("#user-point").html(data.user_point);
						}

						if(data.free_point != null){
							$("#free-point").html(data.free_point);
						}

					}

					// 未読メールカウントを減らす
					let unread_count		= $(".check-unread").text();
					let unread_count_new	= unread_count - 1;

					if (unread_count_new == 0) {
						$(".check-unread").remove();
					} else {
						$(".check-unread").html(unread_count_new);
					}

					// プレゼントポイント
					if(data.present_point){

						$("#dialog-present").html(data.present_message);
						$("#dialog-present").dialog({
							resizable: false,
							modal: true,
							title: data.present_title,
							show: {
								effect: 'bounce',
								delay: 500,
								duration: 1200,
							},
							buttons: {
								"　ＯＫ　": function() {
									$(this).dialog("close");
								}
							}
						});

					}
					
					/* LEVEL UP */
					if(data.level_up){
						/* レベルアップエフェクト */
						startParticles_Sakura();

						/* 称号ランクアップ */
						if(data.degree_up_name){

							var level_up_message		= data.level_up_message + "";
							level_up_message			+= data.degree_up_message;

							$("#degree-name").html(data.degree_up_name);

						}else{

							var level_up_message		= data.level_up_message;

						}

						/* キャラ画像ランクアップ */
						if(data.animation){

							$("#character-image").remove();
							$("#animation-display").remove();
							$("#mail-detail").append("<section id=\"animation-display\"></section>");
							$("#mail-detail").append("<section id=\"character-image\"></section>");

							$("#character-image").html(data.media);
							$("#animation-display").html(data.animation);
							var frame					= new ParaParaData();
							$("#character-image img").each(function (i) {
								frame.data[ParaPara.zeroPadding(i, 4)] = this;
							});
							var anime					= new ParaPara(frame, $("#animation-display"));
							anime.repeat				= true;
							anime.play();

							if(data.image_up_message){
								level_up_message		+= data.image_up_message;
							}

						}

						$("#dialog-favorite").html(level_up_message);
						$("#dialog-favorite").dialog({
							resizable: false,
							modal: true,
							title: data.level_up_title,
							show: {
								effect: 'bounce',
								delay: 500,
								duration: 1200,
							},
							buttons: {
								"　ＯＫ　": function() {

									$(this).dialog("close");

									/* レベルアッププレゼントがあったら */
									dialog_levelup_present(data,dialog_support_reward);
								}
							}
						});
					}else{
						/* 応援ポイント報酬 */
						dialog_support_reward(data,dialog_mail_present);
					}
					
					// 有効期限、利用回数が切れたアイテムがあれば
					if(data.item_end_count > 0){

						var count			= data.item_end_count;
						var list			= data.item_end;
						var word			= data.item_end_id;
						var remaining		= data.item_using_count;

						// 文字列に変換
						list				= String(list);
						word				= String(word);

						// アイテム終了処理
						itemUseEnd(count,list,word,remaining,"");

					}
					//非同期の終了
					num_atap = 0;
					
				// ERROR
				} else {

					// POINT ERROR
					if (error == 2) {
						pointErrorDialog("エラー",data.errormessage);
					} else {
						showErrorDialog("エラー",data.errormessage);
					}
					
					//非同期の終了
					num_atap = 0;
					$(".loading").fadeOut();
				}

			},
			// 通信失敗
			error: function(XMLHttpRequest, textStatus, errorThrown) {

				showErrorDialog("エラー","続きを取得できませんでした"+"/"+textStatus+"/"+errorThrown);
				
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			}

		});

	} else {

		showErrorDialog("エラー","正常に処理できませんでした");
		//非同期の終了
		num_atap = 0;
	}

	stop				= 0;
	return false;

}



/************************************
**
**	メール送信処理 : Ajax
**
************************************/

function mailSend() {

	$(".loading").fadeIn();

	// タイマーストップ
	stop				= 1;

	let message			= $("#message").val();

	$.ajax({
		type:'POST',
		url: "/mail/send/",
		dataType: 'json',
		data : {id : post_send_id, first_mail : first_mail, message : message, confirmation : confirmation },
		timeout:10000,
		cache: false,
		// 通信成功
		success: function(data) {
			
			let error = data.error;

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
				first_mail				= 0;
				post_parent_id			= 0;

				// 好感度操作
				let favorite_level		= "Lv." + data.favorite_level;
				let favorite_percent	= data.favorite_percent + "%";
				let favorite_gauge		= data.favorite_gauge + "%";

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

				// ポイント消費
				if(status == 0){

					$(".check-point").html(data.ticket);

					if(data.user_point != null){
						$("#user-point").html(data.user_point);
					}

					if(data.free_point != null){
						$("#free-point").html(data.free_point);
					}

				}

				/* LEVEL UP */
				if(data.level_up){
					/* レベルアップエフェクト */
					startParticles_Sakura();

					/* 称号ランクアップ */
					let level_up_message = "";
					if(data.degree_up_name){

						level_up_message		= data.level_up_message + "";
						level_up_message			+= data.degree_up_message;

						$("#degree-name").html(data.degree_up_name);

					}else{

						level_up_message		= data.level_up_message;

					}

					/* キャラ画像ランクアップ */
					if(data.animation){

						$("#character-image").remove();
						$("#animation-display").remove();
						$("#mail-detail").append("<section id=\"animation-display\"></section>");
						$("#mail-detail").append("<section id=\"character-image\"></section>");

						$("#character-image").html(data.media);
						$("#animation-display").html(data.animation);
					    var frame					= new ParaParaData();
					    $("#character-image img").each(function (i) {
					        frame.data[ParaPara.zeroPadding(i, 4)] = this;
					    });
					    var anime					= new ParaPara(frame, $("#animation-display"));
					    anime.repeat				= true;
						anime.play();

						if(data.image_up_message){
							level_up_message		+= data.image_up_message;
						}

					}

					$("#dialog-favorite").html(level_up_message);
					$("#dialog-favorite").dialog({
						resizable: false,
						modal: true,
						title: data.level_up_title,
						show: {
							effect: 'bounce',
							delay: 500,
							duration: 1200,
						},
						buttons: {
							"　ＯＫ　": function() {

								$(this).dialog("close");

								/* レベルアッププレゼントがあったら */
								dialog_levelup_present(data,dialog_support_reward);
							}
						}
					});

				}else{
					/* 応援ポイント報酬 */
					dialog_support_reward(data,dialog_mail_present);
				}

				// 有効期限、利用回数が切れたアイテムがあれば
				if(data.item_end_count > 0){

					let count			= data.item_end_count;
					let list			= data.item_end;
					let word			= data.item_end_id;
					let remaining		= data.item_using_count;

					// 文字列に変換
					list				= String(list);
					word				= String(word);

					// アイテム終了処理
					itemUseEnd(count,list,word,remaining,"");

				}
				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			// OK
			} else {

				// POINT ERROR
				if (error == 2) {
					pointErrorDialog("エラー",data.errormessage);
				} else {
					showErrorDialog("エラー",data.errormessage);
				}

				//非同期の終了
				num_atap = 0;
				$(".loading").fadeOut();
			}

		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {

			showErrorDialog("エラー","送信できませんでした"+"/"+textStatus+"/"+errorThrown);
			
			//非同期の終了
			num_atap = 0;
			$(".loading").fadeOut();
		}

	});

	stop				= 0;
	return false;

}
