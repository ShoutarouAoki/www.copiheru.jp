/**************************************************************
**
**	script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	管理側 各種javascript設定
**	# SLIDE MENU MAIN
**
**************************************************************/


/************************************************
**
**	BODY FADE IN
**	--------------------------------------------
**	WRAPPER
**
************************************************/

$(document).ready(function() {
	$('#wrapper').fadeIn("slow");
    jQuery("#wrapper a img").hover(function(){
       jQuery(this).fadeTo("normal", 0.6);
    },function(){
       jQuery(this).fadeTo("normal", 1.0);
    });
});



