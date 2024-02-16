<?php
################################## ファイル概要 #################################
##
##	user_upcsv.php
##	----------------------------------------------------------------------------
##	ユーザcsvアップロードページ
##	----------------------------------------------------------------------------
##
##################################### CONF ######################################

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
$certify	= $adminMain->adminCertify($_REQUEST['op_id'],$_REQUEST['sec']);


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

		$data = $adminMain->fgetcsv_reg($fp,10000,',','"');

		if($data[0] != ''){

			for($b=0;$b<count($data);$b++){
					$data[$b] = mb_convert_encoding($data[$b],'UTF-8','SJIS');
			}
			
			$sha_id     = $data[0];
			$shame		= $data[1];
			$shame_name = $data[2];
			$type		= $data[3];
			$keizi	   	= $data[4];
			
			
			$user_id = "id,user_id,site_cd";
			$where 	 = "user_id =".$sha_id;
			
			$user_data = $members->getUser($user_id,$where);
			
			
			if(!empty($user_data['user_id'])){
			
				if(empty($type)){
				$shame = $shame.".jpg";
				$jpg   = ".jpg";
				$gazou = "http://0177.jp/PICT/".$shame;
				$types = "1";
				
				}else{
				$gazou = "http://0177.jp/PICT/".$shame;
				$types = "2";
				}
				
				$hozon ="/usr/local/apache2/htdocs/img/attaches/";//画像の保存場所
					
				$img = file_get_contents($gazou) ;//画像を取得し、
				$fullpath = $hozon.basename($gazou);//画像の保存フルパスを整形し
				file_put_contents($fullpath, $img);//保存。
				$timedate = date(YmdHis);
				print("sha_id = ".$sha_id."\n\n");
				print("id = ".$user_data['id']."\n");
				print("user_id = ".$user_data['user_id']."\n");
				
				rename($hozon.$shame,$hozon.$timedate.$user_data['id'].$jpg);
				
				$attache_data['user_id']  = $user_data['id'];
				$attache_data['site_cd']  = $user_data['site_cd'];
				$attache_data['attached'] = $timedate.$user_data['id'].$jpg;
				$attache_data['name']     = $shame_name;
				$attache_data['category'] = $types;
				$attache_data['use_flg']  = $keizi;
				$attache_data['status']  =  "1";
				
				print("attached = ".$attache_data['attached']."\n");
				
				$attache_get = $members->csvAttaches($attache_data);
			
			}
			
			
		}
			

			}
		
			$result = fclose($fp);
			exit();
}
