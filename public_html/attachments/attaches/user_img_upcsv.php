<?php
################################## ファイル概要 #################################
##
##	user_upcsv.php
##	----------------------------------------------------------------------------
##	ユーザcsvアップロードページ
##	----------------------------------------------------------------------------
##
##################################### CONF ######################################

set_time_limit(300);
ini_set("max_execution_time",300);

/** CONF FILE **/
require_once(dirname(__FILE__)."/../CONF/config.php");

/** CLASS FILE **/
require_once(dirname(__FILE__).'/../class/html_class.php');
require_once(dirname(__FILE__).'/../class/database.php');
require_once(dirname(__FILE__).'/../class/main.php');
require_once(dirname(__FILE__).'/../class/members.php');

################################ DATABASE CONNECT ###############################
/**  パスワードの認証 **/
$adminMain	= new adminMain();
#$certify	= $adminMain->adminCertify($_REQUEST['op_id'],$_REQUEST['sec']);


$db = new accessDb(0);
$db->connectDb();

################################## MAIN SQL #####################################

$action = $_REQUEST['action'];

if($action == 'upload'){

	$members = new members();
	$html_class	= new htmlClass();

	# 拡張子ﾁｪｯｸ
	preg_match('/\.[^.]*$/i',$_FILES[upfile][name],$matches);
	$ext = $matches[0];
	if($ext != ".csv"){
		$error_msg .= "CSV形式のFILEをUPLOADしてください。<br />";
	}

	if($error_msg != ''){ $html_class->outputError($error_msg); exit; }

	if($_FILES[upfile][name] == '') {
		$error_msg .= 'UPLOADするCSVが選択されていません。<br />';
	}else{
		if(!copy($_FILES[upfile][tmp_name],'./csv/'.$_FILES[upfile][name])){
			$error_msg .= 'FILEがCOPYできませんでした。<br />';
		}
	}

	$fp = fopen('./csv/'.$_FILES[upfile][name],'r');
	if($fp == false){ $error_msg .= 'FILEが開けません。<br />'; }

	if($error_msg != ''){ $html_class->outputError($error_msg); exit; }


	$cnt=0;
	$total=0;
	while (!feof($fp)) {

		$data = $adminMain->fgetcsv_reg($fp,1000000,',','"');

		if($data[0] != ''){

			for($b=0;$b<count($data);$b++){
					$data[$b] = mb_convert_encoding($data[$b],'UTF-8','SJIS-win');
			}
			
			$sha_id     = $data[0];
			$shame		= $data[1];
			$shame_name = $data[2];
			$type		= $data[3];
			$keizi	   	= $data[4];
			$site_cd	= $data[5];

			# $shameの中のAを取り除く
			$shame = preg_replace("/A/","",$shame);

			# いったん動画continue
#			if($type == "video/3gpp"){
#				continue;
#			}

			$user_id = "id,user_id,site_cd";
			$where 	 = "user_id =".$sha_id." AND site_cd =".$site_cd;
			
			$user_data = $members->getUser($user_id,$where);
			
			
			if(!empty($user_data['user_id'])){


				if($type == "video"){
					# 動画は事前にコピーしてあるのでそのまま
					$gazou = "http://file.goojam.jp/file/profmovie/".$shame;
					$types = "2";
				}else{
					$shame = $shame.".jpg";
					$jpg   = ".jpg";
					if($site_cd == 4){
						$gazou = "http://ame-chan.jp/PICT/".$shame;
					}elseif($site_cd == 5){
						$gazou = "http://swdms.jp/PICT/".$shame;
					}else{
						$gazou = "http://0177.jp/PICT/".$shame;
					}
					$types = "1";
/*
					$hozon ="/usr/local/apache2/htdocs/img/attaches/";//画像の保存場所

					$img = file_get_contents($gazou) ;//画像を取得し、
					$fullpath = $hozon.basename($gazou);//画像の保存フルパスを整形し
					file_put_contents($fullpath, $img);//保存。
					#$timedate = date(YmdHis);
					$timedate = date(Ymd);
*/
				}

				# 2011/07/13 同一名の写メが作成されてしまうので名前はそのまま使用
#				rename($hozon.$shame,$hozon.$timedate.$cnt.$user_data['id'].$jpg);

				$attache_data['user_id']  = $user_data['id'];
				$attache_data['site_cd']  = $user_data['site_cd'];
#				$attache_data['attached'] = $timedate.$cnt.$user_data['id'].$jpg;
				$attache_data['attached'] = $shame;
				$attache_data['name']     = $shame_name;
				$attache_data['category'] = $types;
				$attache_data['use_flg']  = $keizi;
				$attache_data['status']  =  "1";

				print($cnt.":".$type."<br>");


				print("<pre>");
				print_r($attache_data);
				print("</pre>");


				$attache_get = $members->csvAttaches($attache_data);

			}

		}

		$cnt++;

	}

			print("<br><br>END:".date("Y-m-d H:i:s")."<br>");

			$result = fclose($fp);
			exit();



}

?>


<?
	print("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
	print("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");
	print("<head>\n");
	print("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n");
	print("<meta http-equiv=\"Content-Language\" content=\"ja\">\n");
	print("<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n");
	print("<meta http-equiv=\"Content-Script-Type\" content=\"text/javascrip\" />\n");
	if($page == "main"){
	print("<link href=\"./CONF/css/import.css\" rel=\"stylesheet\" type=\"text/css\">\n");
	}else{
	print("<link href=\"../CONF/css/import.css\" rel=\"stylesheet\" type=\"text/css\">\n");
	print("<script type=\"text/javascript\" src=\"../CONF/js/jquery.js\"></script>\n");
	print("<script type=\"text/javascript\" src=\"../CONF/js/script.js\"></script>\n");
	}
	print("<title>".D_SITE_NAME."</title>\n");
	print("</head>\n\n");

	print("<body>\n");
?>

<div id="main_contents">

<? if($action == 'upload'){ ?>

	<table cellspacing="0" cellpadding="0" border="0" class="table_frame" style="width: 400px;">
	<tr>
	<td class="table_contents" style="text-align:center;">
	<? print($cnt); ?> / <? print($total); ?> 登録完了しました。
	</td>
	</tr>
	</table>

	<br />

<? }else{ ?>

	<form action="user_img_upcsv.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="upload">
	<? print($form_sec_data); ?>

	<table cellspacing="0" cellpadding="0" border="0" class="table_frame" style="width: 720px;">

	<tr>
		<td class="table_title">CSV FILE</td>
		<td class="table_contents">
			<input type="hidden" name="MAX_FILE_SIZE" value="50000000">
			<input type="file" name="upfile">
		</td>
	</tr>

	<tr>
		<td class="table_contents" colspan="2">
		<div align="center">
			<input type="submit" value="UPLOAD" class="submit" onClick="return confirm('UPLOADします。よろしいですか？')" />
		</div>
		</td>
	</tr>
	</table>
	</form>
<br />

<? } ?>

</div>
<?
	print("</body>\n");
	print("</html>\n");
?>