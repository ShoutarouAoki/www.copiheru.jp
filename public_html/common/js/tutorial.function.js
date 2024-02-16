/**************************************************************
**
**	tutorial.function.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	チュートリアル用 function群
**	
**
**************************************************************/


/************************************
**
**	javascript内の変数定義
**
************************************/

function constructDefine(nickname_max,message_max) {

	nickname_max_length	= nickname_max;
	message_max_length	= message_max;

	return true;

}



/************************************
**
**	チュートリアルページ遷移
**
************************************/

function nextPrevious(number,type) {

	if(number == ""){
		return false;
	}

	$("#loading").fadeIn();

	var path			= "/tutorial/page/";

	$.ajax({
		type:'POST',
		url: path,
		dataType: 'json',
		data : {number : number },
		timeout:10000,
		cache: false,
		// 通信成功
		success: function(data) {

			var error = data.error;

			// OK
			if (error == 0) {

				if(data.image){

					var image		= "<img src=\"" + data.image + "\" />";
			        var delaySpeed	= 100;
			        var fadeSpeed	= 250;

					$("#tutorial-image img").delay(delaySpeed).css({opacity:'1'}).animate({display:'none',opacity:'0'},fadeSpeed,function(){

						$("#tutorial-image img").remove();
						$("#tutorial-image").append(image);
						$("#tutorial-image img").css({opacity:'0'}).animate({display:'block',opacity:'1'},fadeSpeed);

						// NEXT
						if(data.next){
							$("#skip").fadeIn("slow");
							$("#tutorial-end").fadeOut("slow");
							$("#next").fadeIn("slow");
							$("#next").attr("name",data.next);
						// 最終ページだったら
						}else{
							$("#skip").fadeOut("slow");
							$("#next").fadeOut("slow");
							$("#tutorial-end").fadeIn("slow");
						}

						// PREVIOUS
						if(data.previous != null){
							if(!data.next)
								$("#skip").fadeOut("slow");
							else
								$("#skip").fadeIn("slow");
							$("#previous").fadeIn("slow");
							$("#previous").attr("name",data.previous);
						}else{
							if(!data.next)
								$("#skip").fadeOut("slow");
							else
								$("#skip").fadeIn("slow");
							$("#previous").fadeOut("slow");
						}

					});

				}

				$("#loading").fadeOut();

			// ERROR
			} else {
				var errormessage		= data.errormessage;
				$("#loading").fadeOut();
				showErrorDialog("エラー",errormessage);
			}

		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#loading").fadeOut();
			showErrorDialog("エラー","正常に処理できませんでした");
		}

	});

	return false;

}



/************************************
**
**	プロフィール作成
**
************************************/

function profileEdit(nickname,month,day,message) {

	$("#loading").fadeIn();

	var path			= "/tutorial/edit/";

	$.ajax({
		type:'POST',
		url: path,
		dataType: 'json',
		data : {nickname : nickname, month : month, day : day, message : message },
		timeout:10000,
		cache: false,
		// 通信成功
		success: function(data) {

			var error = data.error;

			// OK
			if (error == 0) {

				$("#loading").fadeOut();
				$("#screen").fadeIn("slow");
				setTimeout(function(){
					window.location.href	= "/tutorial/finish/";
					return false;
				}, 400);

			// ERROR
			} else {
				var errormessage		= data.errormessage;
				$("#loading").fadeOut();
				showErrorDialog("エラー",errormessage);
			}

		},
		// 通信失敗
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#loading").fadeOut();
			showErrorDialog("エラー","正常に処理できませんでした");
		}

	});

	return false;


}
