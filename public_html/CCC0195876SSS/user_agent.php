<?
#############################################################################
##
##	user_agent.php
##	------------------------------------------------------------------------
##	ユーザーキャリア認証
##	超改変　2012/08/15 終戦記念日　山本　韓国は滅びろ
##
################################### CONF ####################################

################################ MAIN SET ###################################

$agent	= $_SERVER['HTTP_USER_AGENT'];




#############################################################################

# キャリア取るよ
# DOCOMO
if(preg_match("/^DoCoMo/i", $agent)){
# SOFTBANK
}elseif(preg_match("/^(J\-PHONE|Vodafone|MOT\-[CV]|SoftBank)/i", $agent)){
# AU
}elseif(preg_match("/^KDDI\-/i", $agent) || preg_match("/UP\.Browser/i", $agent)){
# IPHONE
}elseif(preg_match("/iPhone|iPod|ipad/i",$agent)){
$smaho = 1;
# SMARTPHONE
}elseif(preg_match("/Opera Mini/i",$agent)){
## ANDROID
$smaho=1;
}elseif(preg_match("/Android/",$agent)){
$smaho=1;
}else{
$smaho=1;
//	header("Location:../");
//	exit();

}

#############################################################################

# 固体識別空だったら

if(empty($_REQUEST[guid])){


	if(!empty($_REQUEST[tells]) ){
	
	}else{
?>

<p align="center"><a href="<?= $_SERVER["PHP_SELF"]; ?>?guid=ON">Guidがありません。<br />PUSH取得して下さい</a></p>

<?

exit();
	}
}
?>

<?
if(!empty($_REQUEST["tells"])){

	//電話番号とデータを拾う
	if(file_exists("./teldate/tel.dat")){

		//FILEを開く
		$tellog	= file("./teldate/tel.dat"); 
		$fp		= fopen( "./teldate/tel.dat" , "r+");

		if(!$fp){
			print ( "ファイルを開く事に失敗しました。システム部まで連絡下さい。" ) ;
			exit;
		}

		flock($fp,2);
		
		
		$subscribe = $_REQUEST["sub"];

		for($i=0;$i<count($tellog);$i++){
			$tel_date = $tellog[$i];
			$tel_date = explode(",",$tel_date);

			if($tel_date[0] == $_REQUEST["tells"]){
				
				
				//sumaho 対応
				if($_REQUEST["sub"] == "PC"){
		
							$value = "19801112,".$tel_date[0].",inabahimeko";
							$timeout = time() + 365 * 86400;
							setcookie( "kokoroconnect" ,$value,$timeout,'/',$_SERVER[HTTP_HOST]);
							$subscribe = $tel_date[0];
			
				}
				

				if( $tel_date[2] == "" ){
					$put_file = trim($tellog[$i]).",".$subscribe."\n";
					fputs($fp,$put_file);
					$ok_flg = 1;
				}else{
					$err_msg = "既に個体識別コードが登録されています。そんなバカな！の方は山本まで連絡下さい<br />";
					fputs($fp,$tellog[$i]);
				}

			}else{
				fputs($fp,$tellog[$i]);
			}

			$tel_date = "";
		}

	rewind($fp);
	flock($fp,3);
	fclose($fp);

	}

	if($ok_flg == 1){
		
		header("Location:".$_SERVER[PHP_SELF]."?guid=ON");
		exit("ここを通る１");
	}


}

// USER AGENT 個体識別コード取得

if($_SERVER[HTTP_X_JPHONE_MSNAME]){

	if(preg_match("/SN/", $_SERVER[HTTP_USER_AGENT])){ 
		list($ka, $kb) = split("/SN", $_SERVER[HTTP_USER_AGENT]); list($sub, $kg) = split(" ", $kb);
	}else{ 
		$err_msg .= "端末情報を送信して下さい1<br>";
	}

}elseif($_SERVER[HTTP_X_JPHONE_UID]){

	$sub = $_SERVER[HTTP_X_JPHONE_UID];

	}elseif(preg_match("/DoCoMo/", $_SERVER[HTTP_USER_AGENT])){

		$sub = $_SERVER['HTTP_X_DCMGUID'];

	}elseif($_SERVER[HTTP_X_UP_SUBNO]){ 

		list($sub, $kb) = explode("_", $_SERVER[HTTP_X_UP_SUBNO], 2);

	}else{
		
		if($smaho == 1){
		
			$sub = "PC";
			
			$cookie = explode(",",$_COOKIE["kokoroconnect"]);
			
			if($cookie[0] == "19801112"){
				$sub = $cookie[1];
				$yes = 1;
				$name = "スマホ閲覧者";
			}
			
		
		}else{
		//VODA 3G対応
		preg_match("/^.+\/SN([0-9a-zA-Z]+).*$/", $_SERVER[HTTP_USER_AGENT], $match);
		$sub = $match[1];
		}
	}
	

	//電話番号とデータを拾う

if( $sub == ""){

}else{

	if( file_exists("./teldate/tel.dat" ) ){

		//FILEを開く
		$fp = fopen( "./teldate/tel.dat" , "r");

	if(!$fp){
		print ( "ファイルを開く事に失敗しました。システム部まで連絡下さい。" );
		exit;
	}
		flock($fp,2);

		while( !feof($fp) ){
			$tel_date = fgets($fp);
			$tel_date = explode(",",$tel_date);
			
			
			
			if(trim($tel_date[2]) == $sub){
					$yes = 1;
					$name = $tel_date[1];
				}
			}
}
rewind($fp);
flock($fp,3);
fclose($fp);


}

if($yes != 1){



?>

<form action="<?= $_SERVER[PHP_SELF] ?>" method="post">
<? print($err_msg); ?><br><br>
売上を確認する場合は携帯番号を090からお入れ下さい。一度入れますと次回からはいりません。<br>
携帯番号<input type="text" class="type" name="tells" value="" size="5" maxlength="30" istyle="4" format="*N" mode="numeric">
<input type="hidden" name="sub" value="<? print($sub);?>">
<input type="submit" name="submit1" value="登録" />
</form>

<?

	exit();
}

print("<div align=\"center\">ようこそ".$name."様</div>");
?>