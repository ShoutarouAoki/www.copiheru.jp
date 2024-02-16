/**************************************************************
**
**	common.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	WEB 各種javascript設定
**	
**
**************************************************************/



/************************************************
**
**	submitData()
**	--------------------------------------------
**	DATA送信
**
************************************************/

function submitData(directory , page , message) {

	var confirm_title				= "確認";
	if (message) {
		var	confirm_message			= message;
	} else {
		var	confirm_message			= "この内容でよろしいですか？";
	}

	$("#dialog-box").html(confirm_message);
	$("#dialog-box").dialog({
		resizable: false,
		modal: true,
		title: confirm_title,
		show: {
			effect: 'drop',
			duration: 250,
		},
		hide: {
			effect: 'drop',
			duration: 250,
		},
		position: {
			of : '.submit-button',
			at: 'center top',
			my: 'center bottom'
		},
		buttons: {

			"　ＯＫ　": function() {

				$("#dialog-box").dialog( "close" );

				// HTMLでの送信をキャンセル
				event.preventDefault();

				// 操作対象のフォーム要素を取得
				var $form	=	$("#editForm");

				$.ajax({
			        url: $form.attr('action'),
			        type: $form.attr('method'),
					dataType: 'json',
					data: $form.serialize(),
					timeout:10000,
					cache: false,

					// 通信成功
					success: function(data) {

						var success		= data.success;
						var error		= data.error;

						// OK
						if (success == 1 && error == 0) {

							if ( data.redirect ) {

								window.location.href			= data.redirect;
								return false;

							} else {

								var success_title				= data.success_title;
								var	success_message				= data.success_message;

								if ( data.return ) {
									var return_path				= "/" + data.return + "/";
								} else {
									var return_path				= "/" + directory + "/";
								}


								$("#dialog-box").html(success_message);
								$("#dialog-box").dialog({
									resizable: false,
									modal: true,
									title: success_title,
									show: {
										effect: 'drop',
										duration: 250,
									},
									hide: {
										effect: 'drop',
										duration: 250,
									},
									position: {
										of : '.submit-button',
										at: 'center top',
										my: 'center top'
									},
									buttons: {
										"　ＯＫ　": function() {
											$(this).dialog("close");
											window.location.href	= return_path;
											return false;
										}
									}
								});

							}


						// ERROR
						} else {

							if ( data.errormessage ) {
								showErrorSubmitDialog("処理エラー",data.errormessage);
							} else {
								showErrorSubmitDialog("処理エラー","正常に処理できませんでした。");
							}

						}

					},

					// 通信失敗
					error: function(XMLHttpRequest, textStatus, errorThrown) {

						showErrorSubmitDialog("通信エラー","正常に処理できませんでした");

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


/************************************************
**
**	DIALOG 
**	--------------------------------------------
**	成功表示用ダイアログ
**
************************************************/

function showSuccessDialog(title,message) {

	$("#dialog-box").html(message);
	$("#dialog-box").dialog({
		resizable: false,
		modal: true,
		title: title,
		show: {
			effect: 'drop',
			duration: 250,
		},
		hide: {
			effect: 'drop',
			duration: 250,
		},
		position: {
			of : '.title',
			at: 'center bottom',
			my: 'center top'
		},
		buttons: {
			"　ＯＫ　": function() {
				$(this).dialog("close");
			}
		}
	});

}


/************************************************
**
**	ERROR 
**	--------------------------------------------
**	エラー表示用ダイアログ
**
************************************************/

function showErrorSubmitDialog(title,message) {

	$("#dialog-error").html(message);
	$("#dialog-error").dialog({
		resizable: false,
		modal: true,
		title: title,
		show: {
			effect: 'drop',
			duration: 250,
		},
		hide: {
			effect: 'drop',
			duration: 250,
		},
		position: {
			of : '.submit-button',
			at: 'center top',
			my: 'center bottom'
		},
		buttons: {
			"　ＯＫ　": function() {
				$(this).dialog("close");
			}
		}
	});

}
