/**************************************************************
**
**	index.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	INDEX用 javascript設定
**	
**
**************************************************************/

$(function() {

	$("#enter").click(function(){

		$("#screen").fadeIn("slow");
		setTimeout(function(){
			window.location.href	= "/enter/";
			return false;
		}, 500);

	});

	if($('#enter').length){
		$("#enter").jqFloat({
			width: 5,
			height: 30,
			speed: 1000
		});
	}


});




