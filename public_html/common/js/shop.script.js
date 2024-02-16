/**************************************************************
**
**	shop.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	ショップ用 javascript設定
**	
**
**************************************************************/

$(function() {

	/* 購入結果 */
	if($("#shop-result-box").length){
		setTimeout(function(){
			$("#shop-result-box").animate({top:"0px",opacity:"1"},1000,"easeOutElastic");
	        var delaySpeed = 100;
	        var fadeSpeed = 1000;
			if($('#shop-result-item').length){
		        $('#shop-result-item ul li').each(function(i){
		            $(this).delay(i*(delaySpeed)).css({display:'block',opacity:'0'}).animate({opacity:'1'},fadeSpeed);
		        });
			}

		}, 400);
	}

});




