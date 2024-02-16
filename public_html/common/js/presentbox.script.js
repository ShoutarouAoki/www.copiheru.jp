/**************************************************************
**
**	presentbox.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	プレゼントボックス用 javascript設定
**	
**
**************************************************************/



$(function() {

	/* プレゼント一括受け取り */
	$(".acceptance-all").click(function() {

		$(".loading").fadeIn();

		var element						= $(this);
		var present_id_line				= element.attr("id");
		var idArray						= present_id_line.split('-');
		var presentbox_id				= idArray[0];
		var page_set					= idArray[1];

		/* ここでダイアログ */
		var dialog_message				= "全て受け取りますか？";

		$( "#dialog-box" ).html(dialog_message);
		$( "#dialog-box" ).dialog({
			modal: true,
			title: "受け取り確認",
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
					$.ajax({
						type: "POST",
						url: "/presentbox/acceptance/",
						data : {id : presentbox_id, set : page_set },
						timeout:10000,
						cache: false,
						// 成功
						success: function(html){
							$(".loading").fadeOut();
							$("#container").html(html);
							$(".check-present").remove();
						},
						// 通信失敗
						error: function(XMLHttpRequest, textStatus, errorThrown) {

							// エラーメッセージ(ダイアログ)
							$(".loading").fadeOut();
							showErrorDialog("エラー","読み込みできませんでした");

						}
					});
					$(this).dialog("close");
					return false;
				},
				"キャンセル": function() {
					$(".loading").fadeOut();
					$(this).dialog("close");
					return false;
				}
			}
		});

		$(".loading").fadeOut();
		return false;

	});


	/* プレゼント個別受け取り */
	$(".list-line").click(function() {

		$(".loading").fadeIn();

		var element						= $(this);
		var present_id_line				= element.attr("id");
		var idArray						= present_id_line.split('-');
		var presentbox_id				= idArray[0];
		var page_set					= idArray[1];
		var present_name_line			= "#present-name-" + presentbox_id;
		var present_name				= $(present_name_line).text();

		/* ここでダイアログ */
		var dialog_message				= present_name + "<br />を受け取りますか？";

		$( "#dialog-box" ).html(dialog_message);
		$( "#dialog-box" ).dialog({
			modal: true,
			title: "受け取り確認",
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
					$.ajax({
						type: "POST",
						url: "/presentbox/acceptance/",
						data : {id : presentbox_id, set : page_set },
						timeout:10000,
						cache: false,
						// 成功
						success: function(html){
							$(".loading").fadeOut();
							$("#container").html(html);
							$(".hide").fadeIn("fast");
							var present_unit	= $("#present-unit").text();
							present_unit		= Number(present_unit);
							var present_count	= present_unit - 1;
							if(present_count == 0){
								$(".check-present").remove();
							}else{
								$(".check-present").html(present_count);
							}
						},
						// 通信失敗
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							$(".loading").fadeOut();
							// エラーメッセージ(ダイアログ)
							showErrorDialog("エラー","読み込みできませんでした");
						}
					});
					$(this).dialog("close");
					return false;
				},
				"キャンセル": function() {
					$(".loading").fadeOut();
					$(this).dialog("close");
					return false;
				}
			}
		});

		return false;

	});


});


