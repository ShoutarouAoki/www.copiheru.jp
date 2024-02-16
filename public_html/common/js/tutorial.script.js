/**************************************************************
**
**	tutorial.script.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	チュートリアル用 javascript設定
**	
**
**************************************************************/

var nickname_max_length	= 10;
var message_max_length	= 100;


$(function() {


	/* スキップ/戻るボタンクリック */
	$(".next-previous-button").click(function(){

		var element					= $(this);
		var type					= element.attr("id");
		var number					= element.attr("name");

		if(number == ""){
			return false;
		}

		nextPrevious(number,type);

		return false;

	});


	/* 送信ボタンクリック */
	$("#send").click(function(){

		// DIALOG
		$("#dialog-box").html("この内容でよろしいですか？");
		$("#dialog-box").dialog({
			resizable: false,
			modal: true,
			title: "内容確認",
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

					$("#dialog-box").dialog( "close" );

					var nickname		= $('#nickname').val();
					var month			= $('#month').val();
					var day				= $('#day').val();
					var message			= $('#message').val();

					var nickname_count	= nickname.length;
					var message_count	= message.length;

					var error			= "";

					if(!nickname){
						error			+= "ニックネームをご記入下さい<br />";
					}

					if(nickname_count > nickname_max_length){
						error			+= "ニックネームの最大文字数は<span style=\"color: #FF0000;\">" + nickname_max_length + "</span>までです。<br />";
					}

					if(message_count > message_max_length){
						error			+= "自己紹介の最大文字数は<span style=\"color: #FF0000;\">" + message_max_length + "</span>までです。<br />";
					}

					if(error){

						showErrorDialog("エラー",error);
						return false;

					}else{

						profileEdit(nickname,month,day,message);
						return false;

					}


					return false;
				},
				"キャンセル": function() {
					$("#dialog-box").dialog( "close" );
					return false;
				}
			}
		});

		return false;

	});

	/* 文字数チェック */
	$("#message").bind("change keyup",function(){
		var count		= $(this).val().length;
		var remaining	= message_max_length - count;
		$("#text-length").text(remaining);
		/*
		if(remaining <= 0){
			$(this).attr('disabled', true);
		}else{
			$($this).removeAttr("disabled");
		}
		*/
	});

	/* 結果表示 */
	$(document).ready(function(){

		if($("#tutorial-result").length){

			setTimeout(function(){
				$("#tutorial-result-box").animate({top:"0px",opacity:"1"},1000,"easeOutElastic");
		        var delaySpeed = 100;
		        var fadeSpeed = 1000;
			}, 400);

		}

	});


});

