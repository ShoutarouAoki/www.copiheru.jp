/**************************************************************
**
**	pc.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	PC用 javascript設定
**	
**
**************************************************************/


$(function() {

	/* アイテムモーダル PC */
	$(".item-menu").click(function(){
	    $.colorbox({
			inline:true,
			width:460,
			top:100,
			href:"#user-item",
			opacity: 0.7,
			returnFocus: false
		});
	});

	/* アイテムモーダル バグ回避 */
	$(".item-menu-mail").click(function(){
	    $.colorbox({
			inline:true,
			width:460,
			top:100,
			href:"#item-use",
			opacity: 0.7,
			returnFocus: false
		});
	});

	/* チケットモーダル PC */
	$(".ticket-menu").click(function(){
	    $.colorbox({
			inline:true,
			width:460,
			top:100,
			href:"#user-ticket",
			opacity: 0.7,
			returnFocus: false
		});
	});

	/* チケットモーダル PC */
	$(".ticket-menu-mail").click(function(){
	    $.colorbox({
			inline:true,
			width:460,
			top:100,
			href:"#ticket-area",
			opacity: 0.7,
			returnFocus: false
		});
	});

});


