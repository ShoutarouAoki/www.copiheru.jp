/************************************
**
**	Aタグのコントロール
**
************************************/
var num_atap=0;
$(function(){
	$("a").on("click", function(){
		let eid = $(this).attr("id");
		let eclass = $(this).attr("class");
		if(num_atap>0){
			if(eid === void 0 && eclass === void 0){
				return false;
			}else{
				return true;
			}
		}else{
			if(eid === void 0 && eclass === void 0){
				num_atap++;
				$("#loading").fadeIn();
				
			}
			return true;
		}
	});
});