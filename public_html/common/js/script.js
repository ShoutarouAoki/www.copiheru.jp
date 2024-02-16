/**************************************************************
**
**	script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	WEB 各種javascript設定
**	
**
**************************************************************/


/************************************************
**
**	BODY FADE IN
**	--------------------------------------------
**	WRAPPER
**
************************************************/

window.onload = function(){
	$(function() {
		$("#loading").fadeOut("slow");
		$("#screen").fadeOut("slow");
		//$("#wrapper").fadeIn("slow");
		//$(".colorbox").colorbox({rel:'colorbox'});
	    jQuery("#wrapper a img").hover(function(){
	       jQuery(this).fadeTo("normal", 0.6);
	    },function(){
	       jQuery(this).fadeTo("normal", 1.0);
	    });
		$('a[target="_blank"]').on('click', function(e){ nijiyome.util.requestExternalNavigateTo(this.href); e.preventDefault(); });
		$.ajaxSetup({
			cache: false
		});
	});
}



/************************************************
**
**	SCROLL 
**	--------------------------------------------
**	POSITION
**
************************************************/

$(function(){

	$("a[href^=#]").click(function(){

		var speed = 1000;  
		var href= $(this).attr("href");
		var target = $(href == "#" || href == "" ? 'html' : href);
		var position = $(this.hash).offset().top;

		nijiyome.ui(
			{
				method: 'scroll',
				x: 0,
				y: position
			}
		);

		$($.browser.safari ? 'body' : 'html').animate({scrollTop:position}, { duration: speed, easing:'swing' });

		return false;

	});

	$('object').closest('div')
		.on('mouseover', function(){ nijiyome.window.scroll.suspend(); })
		.one('wheel mousewheel DOMMouseScroll', function(e){ nijiyome.window.scroll.suspend(); e.preventDefault();
	});

});

scroll_y = 0;

// HEAD POINST
nijiyome.ui(
	{
		'method': 'scroll',
		'x': 0,
		'y': scroll_y
	}
);



/************************************************
**
**	DIALOG 
**	--------------------------------------------
**	常設表示用ダイアログ
**
************************************************/

function showDialog(title,message) {

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
		buttons: {
			"　ＯＫ　": function() {
				$(this).dialog("close");
			}
		}
	});

}



/************************************************
**
**	LOGIN BONUS 
**	--------------------------------------------
**	ログインボーナス用ダイアログ
**
************************************************/

function loginBonusDialog(title,message) {

	$("#dialog-box").html(message);
	$("#dialog-box").dialog({
		resizable: false,
		modal: true,
		title: title,
		show: {
			effect: 'bounce',
			delay: 500,
			duration: 1200,
		},
		position: {
			of : '#main-image',
			at: 'center center',
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

function showErrorDialog(title,message) {

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
		buttons: {
			"　ＯＫ　": function() {
				$(this).dialog("close");
			}
		}
	});

}



/************************************************
**
**	ERROR POINT
**	--------------------------------------------
**	ポイント不測時エラー表示用ダイアログ
**
************************************************/

function pointErrorDialog(title,message,position) {

	if(position){

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
				of : '.' + position,
				at: 'center bottom',
				my: 'center top'
			},
			buttons: {
				"　ＯＫ　": function() {
					$(this).dialog("close");
				},
				"ショップで購入": function() {
					$( this ).dialog( "close" );
					window.location.href = "/shop/list/point/";
					return false;
				}
			}
		});

	} else {

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
			buttons: {
				"　ＯＫ　": function() {
					$(this).dialog("close");
				},
				"ショップで購入": function() {
					$( this ).dialog( "close" );
					window.location.href = "/shop/list/point/";
					return false;
				}
			}
		});

	}

}

