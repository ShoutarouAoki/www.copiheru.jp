/**************************************************************
**
**	gacha.function.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	ガチャ用 function群
**	
**
**************************************************************/


/************************************
**
**	javascript内の変数定義
**
************************************/

function constructDefine(point,single,multi,ticket) {

	user_point			= point;
	point_single		= single;
	point_multi			= multi;
	ticket_name			= ticket;

	return true;

}



/************************************
**
**	ボタン回転
**
************************************/

/* 早い回転を繰り返す */
var rotation = function (){
	if($('#start').length){
		$("#start").rotate({
			angle:0,
			animateTo:1440,
			callback: rotation
		});
	}
}



/************************************
**
**	ガチャ回す
**
************************************/

function gachaLottery(type,pay,campaign_id,pays_id) {

	rotation();
	$("#screen").fadeIn("slow");
	$.ajax({
		type: "POST",
		url: "/gacha/lottery/",
		data : {type : type, pay : pay, campaign_id :  campaign_id, pays_id : pays_id},
		timeout:10000,
		cache: false,
		// 成功
		success: function(html){

			setTimeout(function(){
				$("body").hide();
				$("#gacha-start-button").remove();
				$("#gacha-start-button img").remove();
				$("#screen").fadeOut("slow");
				$("#gacha-result").html(html);
				$("body").fadeIn("slow");
				setTimeout(function(){

					$("#gacha-result-box").animate({top:"0px",opacity:"1"},1000,"easeOutElastic");

			        var delaySpeed = 100;
			        var fadeSpeed = 1000;

					if($('#gacha-result-item-multi').length){
				        $('#gacha-result-item-multi ul li').each(function(i){
				            $(this).delay(i*(delaySpeed)).css({display:'block',opacity:'0'}).animate({opacity:'1'},fadeSpeed);
				        });
					}

					if($('#gacha-result-item-single').length){
				        $('#gacha-result-item-single ul li').each(function(i){
				            $(this).delay(i*(delaySpeed)).css({display:'block',opacity:'0'}).animate({opacity:'1'},fadeSpeed);
				        });
					}

				}, 400);
			}, 1000);

		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#screen").fadeOut("slow");
			showErrorDialog("エラー","読み込みできませんでした");
		}
	});

	return false;

}


/************************************
**
**	ガチャの消費を選択
**
************************************/

function askGachaServicePoint(type, pay, campaign_id, gachapoint, gachapo){
	let path			= "";
	let path_service	= "";
	
	if(!campaign_id.length){
		path			= "/gacha/buy/" + type + "/" + pay + "/0/";
		path_service	= "/gacha/start/" + type + "/gachapo/0/";
	}else{
		path			= "/gacha/buy/" + type + "/" + pay + "/" + campaign_id + "/";
		path_service	= "/gacha/start/" + type + "/gachapo/" + campaign_id + "/";
	}

	let dialog_title	= "嬢指名ガチャ(";
	dialog_title	+= type=='multi' ? "10連ガチャ)" : "単発ガチャ)";
	let dialog_message	= "";
	let gachapo_button_disabled = false;
	
	if(!gachapo){
		dialog_message	= "現在ガチャ玉が足りません。<br /><br />";
		dialog_message		+= "（所持ガチャ玉 : " + gachapoint + "個）";
		gachapo_button_disabled = true;
	}else{
		dialog_message	= "ガチャ玉を" + gachapo + "個消費して回せます。使いますか？<br /><br />";
		dialog_message		+= "（所持ガチャ玉 : " + gachapoint + "個）";
	}
	
	$("#dialog-gacha-coin-or-spoint").html(dialog_message);
	$("#dialog-gacha-coin-or-spoint").dialog({
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
		buttons: [
			{
				text: "　はい、ガチャ玉を使います　",
				disabled: gachapo_button_disabled,
				click : function() {
					$("#dialog-gacha-coin-or-spoint").dialog( "close" );
					window.location.href	= path_service;
					return false;
				}
			},
			{
				text: "　にじコインを使います　",
				click: function() {
					$("#dialog-gacha-coin-or-spoint").dialog( "close" );
					window.location.href	= path;
					return false;
				}
			},
			{
				text:"キャンセル",
					click: function() {
					$("#dialog-gacha-coin-or-spoint").dialog( "close" );
					return false;
				}
			}
		]
	});
	return false;

}


/************************************
**
**	ガチャの排出確率一覧
**
************************************/

function displayGachaPrizesList(campaign_title){
	//$("#dialog-gacha-prizes").html(gacha_prizes);
	$("#dialog-gacha-prizes").dialog({
		resizable: false,
		modal: true,
		title: campaign_title,
		show: {
			effect: 'highlight',
			duration: 500,
		},
		hide: {
			effect: 'highlight',
			duration: 500,
		},
		position: {
			of : '.title-nomargin',
			at: 'center bottom',
			my: 'center top'
		},
		buttons: [
			{
				text: "　ＯＫ　",
				click : function() {
					$("#dialog-gacha-prizes").dialog( "close" );
					return false;
				}
			}
		]
	});
	return false;

}