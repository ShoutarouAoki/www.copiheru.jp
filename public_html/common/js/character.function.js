/**************************************************************
**
**	character.function.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	キャラクター用用 function群
**	
**
**************************************************************/


/************************************
**
**	秘密鍵 チェック
**
************************************/

function secretKeyCheck(send_id,nickname,secret_key,key_name,key_image) {

	if(!send_id){
		return false;
	}

	if(!secret_key){
		return false;
	}

	if(!key_name){
		var key_name	 = "専用アイテム";
	}

	var path			 = "/character/open/" + send_id + "/";

	if(secret_key == 1){

		var dialog_title	 = "確認";
		var dialog_message	 = nickname + "とやり取りするには<br /><br /><div style=\"text-align: center;\">";

		if(key_image){
			dialog_message	+= "<img src=\"" + key_image + "\" /><br />";
		}

		dialog_message		+= "<span style=\"color: #FF0000;\">" + key_name + "</span></div><br />";
		dialog_message		+= "が必要です。";

		// DIALOG
		$("#dialog-box").html(dialog_message);
		$("#dialog-box").dialog({
			resizable: false,
			modal: true,
			title: dialog_title,
			buttons: {
				"　ＯＫ　": function() {
					$("#dialog-box").dialog( "close" );
					return false;
				}
			}
		});

	} else if (secret_key == 2){

		var dialog_title	 = "アイテム使用";
		var dialog_message	 = "<div style=\"text-align: center;\">";

		if(key_image){
			dialog_message	+= "<img src=\"" + key_image + "\" /><br />";
		}

		dialog_message		+= "<span style=\"color: #FF0000;\">" + key_name + "</span></div><br />";
		dialog_message		+= "を使用しますか？";

		// DIALOG
		$("#dialog-box").html(dialog_message);
		$("#dialog-box").dialog({
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

					$("#dialog-box").dialog("close");

					$.ajax({
						type:'POST',
						url: path,
						dataType: 'json',
						data : {id : send_id },
						timeout:10000,
						cache: false,
						// 通信成功
						success: function(data) {

							var error = data.error;

							// OK
							if (error == 0 && data.key_check) {

								var key			= "#key-" + data.send_id;
								var screen		= "#screen-" + data.send_id;
								var image		= "#image-" + data.send_id;
								var banner		= "#banner-" + data.send_id;
								var link		= "/mail/detail/" + data.send_id + "/";

								if($(key).length){
									$(key).removeAttr("onclick");
									$(key).attr("href",link);
								}

								if($(banner).length){
									$(banner).removeClass("key-back");
								}

								if($(screen).length){
									$(screen).removeClass("no-mail");
								}

								if($(image).length){
									$(image).remove();
								}

								// 使用時メッセージ(ダイアログ)
								if(data.message){
									var dialog_title	= "オープン！";
									$("#dialog-box").html(data.message);
									$("#dialog-box").dialog({
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
												//window.location.href	= "/mail/detail/" + data.send_id + "/";
												return false;
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

				},
				"キャンセル": function() {
					$("#dialog-box").dialog( "close" );
					return false;
				}
			}
		});

	}

	return false;

}




