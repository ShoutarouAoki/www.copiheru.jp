/**************************************************************
**
**	modal.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	モーダル用 javascript設定
**	
**
**************************************************************/

var	colorbox_width		= 0;

$(function() {

	/* チケットモーダル Android */
	$("#ticket-button").click(function(){
	    $.colorbox({
			inline:true,
			width:colorbox_width,
			href:"#ticket-area",
			opacity: 0.7,
			returnFocus: false
		});
	});

	/* アイテムモーダル Android */
	$("#item-button").click(function(){
	    $.colorbox({
			inline:true,
			width:colorbox_width,
			href:"#item-area",
			opacity: 0.7,
			returnFocus: false
		});
	});

	/* ランキングモーダル Android */
	$("#myrank-button").click(function(){
	    $.colorbox({
			inline:true,
			width:colorbox_width,
			href:"#myrank-area",
			opacity: 0.7,
			returnFocus: false
		});
	});

	/* アイテム / メニューモーダル iOS */
	$(".dialog-button").click(function() {
		$("#modal-overlay").fadeIn("fast");
		return false;
	});

	/* モーダルクローズ iOS */
	$(".md-close").click(function() {
		$("#modal-overlay").fadeOut("fast");
		return false;
	});

	/* アイテム詳細 */
	$(".item-list").click(function() {

		var element						= $(this);
		var item_id_line				= element.attr("id");
		var idArray						= item_id_line.split('-');
		var item_id						= idArray[2];
		var item_num_line				= "#item-num-" + item_id;
		var item_count					= $(item_num_line).text();

		if(item_count == 0){
			item_count					= null;
		}

		if(item_count){

			var item_name_line			= "#item-name-" + item_id;
			var item_description_line	= "#item-description-" + item_id;
			var item_name				= $(item_name_line).text();
			var item_description		= $(item_description_line).text();

			/* ここでダイアログ */
			var dialog_message			= item_description;
			$( "#dialog-box" ).html(dialog_message);

			$( "#dialog-box" ).dialog({
				modal: true,
				title: item_name,
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
						return false;
					}
				}
			});

		}

		return false;

	});

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




