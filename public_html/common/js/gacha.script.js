/**************************************************************
**
**	gacha.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	ガチャ用 javascript設定
**	
**
**************************************************************/

var user_point			= 0;
var point_single		= 0;
var point_multi			= 0;
var ticket_name			= null;

$(function() {


	/* ガチャバナークリック */
	$(".gachar-start").click(function(){

		var element					= $(this);
		var type					= element.attr("id");
		var name					= element.attr("name");
		var campaign_id				= 0;
		var campaign_point			= 0;
		var error					= 0;

		var screenWidth				= screen.width;
		var screenHeight			= screen.height;

		if(name){
			var nameArray			= name.split('-');
			campaign_id				= nameArray[0];
			point_single			= nameArray[1];
			point_multi				= nameArray[1];
		}

		/* 無料ガチャ */
		if (type == "free") {

			var path				= "/gacha/start/free/";
			var dialog_title		= "嬢指名ガチャ(無料ガチャ)";
			var dialog_message		= "無料ガチャを回しますか？<br /><span style=\"color: #FF0000;\">※1日1回限定です</span>";

		/* シングルガチャ */
		} else if (type == "single") {

			if(user_point < point_single){

				error				= 1;

			} else {

				var path			= "/gacha/start/single/point/" + campaign_id + "/";
				var dialog_title	= ticket_name + "確認";
				var dialog_message	= ticket_name + "を" + point_single + "枚消費しますが宜しいですか？<br /><br />";
				dialog_message		+= "所持" + ticket_name + " : " + user_point + "枚";

			}

		/* マルチガチャ */
		} else if (type == "multi") {

			if(user_point < point_multi){

				error				= 1;

			} else {

				var path			= "/gacha/start/multi/point/" + campaign_id + "/";
				var dialog_title	= ticket_name + "確認";
				var dialog_message	= ticket_name + "を" + point_multi + "枚消費しますが宜しいですか？<br /><br />";
				dialog_message		+= "所持" + ticket_name + " : " + user_point + "枚";

			}

		} else {

			error					= 1;

		}


		if(error == 0){

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
				position: {
					of : '.title-nomargin',
					at: 'center bottom',
					my: 'center top'
				},
				buttons: {
					"　ＯＫ　": function() {
						$("#dialog-box").dialog( "close" );
						window.location.href	= path;
						return false;

					},
					"キャンセル": function() {
						$("#dialog-box").dialog( "close" );
						return false;
					}
				}
			});

		} else {

			var errormessage		= ticket_name + "が不足しております。";
	 	   pointErrorDialog("エラー",errormessage,"title-nomargin");

		}

		return false;

	});



	/* ボタンフロート */
	if($('#start').length){
		$("#start").jqFloat({
			width: 5,
			height: 30,
			speed: 1000
		});
	}



	/* ERROR */
	$(".error-point").click(function(){
		var errormessage			= ticket_name + "が不足しております。";
	    pointErrorDialog("エラー",errormessage,"title-nomargin");
	});



});




