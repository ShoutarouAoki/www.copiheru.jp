/**************************************************************
**
**	benefits.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	メール用 javascript設定
**	
**
**************************************************************/

$(function() {

	/* 送信ボタンクリック */
	$("#button-benefits").click(function() {

		getBenefits();

		return false;

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


