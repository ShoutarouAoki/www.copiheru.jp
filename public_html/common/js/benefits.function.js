/**************************************************************
**
**	benefits.function.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	特典コード用 function群
**	
**
**************************************************************/

/************************************
**
**	特典受け取り
**	===============================
**	未使用
**
************************************/
function getBenefits() {

	$.ajax({
		type: "POST",
		url: "/benefits/gift/",
		dataType: 'json',
		data : {site_cd : $("#site_cd").val(), user_id : $("#user_id").val(), bcode : $("#bcode").val() },
		timeout:10000,
		cache: false,
		// 成功
		success: function(data){

			if(data.error>0){//異常系
				$("#dialog-benefits").html(data.error_msg);
				$("#dialog-benefits").dialog({
					resizable: false,
					modal: true,
					title: "エラー",
					show: {
						effect: 'fold',
						delay: 500,
						duration: 1200,
					},
					buttons: {
						"　ＯＫ　": function() {
							$(this).dialog("close");
						}
					}
				});
			}else{//正常系
				$("#dialog-benefits").html(data.html);
				$("#dialog-benefits").dialog({
					resizable: false,
					modal: true,
					title: "特典プレゼント",
					show: {
						effect: 'fold',
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
			
		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log("XMLHttpRequest : " + XMLHttpRequest.status);
			console.log("errorThrown    : " + errorThrown.message);
			console.log("textStatus:" + textStatus.status);
			// エラーメッセージ(ダイアログ)
			showErrorDialog("エラー","読み込みできませんでした");

		}
	});

	return false;

}

