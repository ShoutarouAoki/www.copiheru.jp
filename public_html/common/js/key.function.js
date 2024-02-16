/************************************
**
**	指名権＆GG交換の確認
**
************************************/
function generateExchangeDialog(itembox_id, keyname, exchange, unit, ticketname){

	let dialog_title	= "指名権<=>" + ticketname + "交換";

	let exchange_sum = (parseInt(exchange)*parseInt(unit)).toString(10);
	let dialog_message = keyname + "<br/>";
	dialog_message += "単価:" + exchange + "<br/>";
	dialog_message += "所持数:" + unit + "<br/>";
	dialog_message += "合計：" + exchange_sum + "<br/>";
	dialog_message += exchange_sum + ticketname + "と交換してもよろしいですか？";
	
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
		},/*
		position: {
			of : '.title-nomargin',
			at: 'center bottom',
			my: 'center top'
		},*/
		buttons: [
			{
				text: "　OK　",
				click : function() {
					$("#dialog-box").dialog( "close" );

					$.ajax({
						type:'POST',
						url: "/key/exection/",
						dataType: 'json',
						data : {itembox_id : itembox_id, exchange: exchange},
						timeout:10000,
						cache: false,
						// 通信成功
						success: function(data) {
							var error = data.error;

							// OK
							if (error == 0) {
								let message = keyname + ",&nbsp;" + unit + "個を" + exchange_sum + ticketname + "と交換しました。<br/>";
								message += data.success_message;
								$("#dialog-key-exchange").html(message);
								$("#dialog-key-exchange").dialog({
									resizable: false,
									modal: true,
									title: "指名権<=>" + ticketname + "交換完了",
									show: {
										effect: 'bounce',
										delay: 500,
										duration: 1200,
									},
									buttons: {
										"　ＯＫ　": function() {
											$(this).dialog("close");
											window.location.href	= "/key/";
										}
									}
								});
								
								
							}else{//エラーあり
								$("#dialog-key-exchange").html(data.error_message);
								$("#dialog-key-exchange").dialog({
									resizable: false,
									modal: true,
									title: "指名権<=>" + ticketname + "交換エラー",
									show: {
										effect: 'bounce',
										delay: 500,
										duration: 1200,
									},
									buttons: {
										"　ＯＫ　": function() {
											$(this).dialog("close");
											window.location.href	= "/key/";
										}
									}
								});
							}
						},
						// 通信失敗
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							console.log("textStatus:"+textStatus);
						}

					});


					
					return false;
				}
			},
			{
				text:"キャンセル",
				click: function() {
					$("#dialog-box").dialog("close");
					return false;
				}
			}
		]
		
	});
	return false;

}