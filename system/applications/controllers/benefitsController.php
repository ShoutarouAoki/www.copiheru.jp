<?php
################################ FILE MANAGEMENT ################################
##
##	benefitsController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	PRESENTBOX PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	PRESENTBOX PAGE 各種処理
##
##	page : index	-> 特典コードTOPページ
##	page : gift		-> アイテムゲット
##
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2018/06/04
##	CREATER		:
##
##	=============================================================================
##
##	■ REWRITE (改修履歴)
##
##
##
##
##
##
##
##
##
##
################################# REQUIRE MODEL #################################

/************************************************
**
**	MODEL FILE REQUIRE
**	---------------------------------------------
**	MODEL CLASS FILE READING
**
************************************************/

/** SHOP MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ShopModel.php");

/** IMAGE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ImageModel.php");

/** ITEM MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/ItemModel.php");


################################# POST ARRAY ####################################

$value_array				= array('page');
$data						= $mainClass->getArrayContents($value_array,$values);

############################## INDIVIDUAL SETTING ###############################


/************************************************
**
**	THIS PAGE INDIVIDUAL SETTING
**	---------------------------------------------
**	DATABASE / PATH / CATEGORY ...etc
**
************************************************/

# PAGE
if(empty($data['page'])){
	$data['page']			= "index";
}


################################# MODEL CLASS ###################################

/************************************************
**
**	BASIC MODEL CLASS CALL
**	---------------------------------------------
**	PHP SCRIPT MODEL CLASS CALL
**
************************************************/


# SHOP MODEL
$shopModel					= new ShopModel($database,$mainClass);

# IMAGE MODEL
$imageModel					= new ImageModel($database,$mainClass);

# ITEM MODEL
$itemModel					= new ItemModel($database,$mainClass);


################################## MAIN SQL #####################################


/************************************************
**
**	PAGE SEPALATE
**	---------------------------------------------
**	DISPLAY
**	---------------------------------------------
**	PAGE CONTROLL
**
**	$exectionがNULLなら
**	表示処理開始
**	---------------------------------------------
**	PAGE :	
**
************************************************/

if(empty($exection)){


	/************************************************
	**
	**	ページ毎にif文で処理分岐
	**
	************************************************/


	/************************************************
	**
	**	INDEX
	**	============================================
	**	ガチャトップ
	**
	************************************************/

	if($data['page'] == "index"){

		/************************************************
		**
		**	ページトップバナー 設定取得
		**
		************************************************/

		# BANNER
		$banner_conditions							= array();
		$banner_conditions							= array(
			'file_type'								=> 10,
			'category'								=> $banner_image_category,
			//'device'								=> $device_number,
			'site_cd'								=> $members_data['site_cd'],
			'target_id'								=> 0,
			'display_check'							=> 1,
			'status'								=> 0
		);

		$banner_rtn									= $imageModel->getImageList($banner_conditions);

		$i=0;
		while($banner_data = $database->fetchAssoc($banner_rtn)){

			$banner_list['id'][$i]					= $banner_data['id'];
			$banner_list['image'][$i]				= $banner_data['img_name'];
			$banner_list['link'][$i]				= $banner_data['img_key'];
			$i++;

		}

		$database->freeResult($banner_rtn);


	/************************************************
	**
	**	GIFT
	**	============================================
	**	アイテムを渡す
	**
	************************************************/
	}elseif($data['page'] == "gift"){
		####################
		## 変数セット
		####################
		$bcode = trim($_POST['bcode']);
		$error_msg = "";
		$error = 0;
		$html = "";
		$site_cd = trim($_POST["site_cd"]);
		$user_id = trim($_POST["user_id"]);

		####################
		## エラーチェック
		####################
		if(!strlen($user_id)){
			$error = 99;
			$error_msg	=	"ユーザIDがございません<br />\n";
		}
		
		if(!$error && empty($bcode)){
			$error = 1;
			$error_msg	=	"特典コードを記入して下さい<br />\n";
		}
		if(!$error && !preg_match("/^[a-zA-Z0-9]+$/", $bcode)){
			$error = 2;
			$error_msg	=	"特典コードは半角英数字のみです。<br/>";
		}
		if(!$error && (strlen($bcode)<7 || strlen($bcode)>12)){
			$error = 3;
			$error_msg	=	"特典コードは8文字～12文字にしてください。<br/>";
		}

		//echo "CODE=".$_REQUEST["code"];

		####################
		## 特典コードを探してプレセント（入力値にエラーなし）
		####################
		if(!$error){
			$table	= "benefits_code";
			$column	= "id,code,title,start_date,end_date";

			$array = array();
			$array[":site_cd"] = $site_cd;
			$array[":code"] = $bcode;
			//id,site_cd,code,title,comment,start_date,end_date,del_flg,

			$where	= "site_cd = :site_cd ";
			$where	.= "AND code = :code ";
			$where	.= "AND del_flg = 0";
			$order = "id DESC";
			$limit = 1;
			$group = NULL;
			$rtn = $database->selectDb($table, $column, $where, 
										$array, $order, $limit, $group);
			$error_db = $database->errorDb($table, $rtn->errorCode(), __FILE__,__LINE__);
			$data						 = $database->fetchAssoc($rtn);
			$database->freeResult($rtn);

			if(strlen($error_db)){
				$error = 4;
				$error_msg	=	"DB接続エラー<br/>";
			}else{
				if(empty($data["id"])){
					## 特典コードがない
					$error = 5;
					$error_msg	=	"入力された特典コードはございません。<br/>";
				}else{
					## 期間外
					$now = date("YmdHis");
					if($now < $data["start_date"] || $now > $data["end_date"]){
						$error = 6;
						$error_msg	=	"特典コード「".$bcode."」の期間は".date('Y年m月d日', strtotime($data["start_date"]));
						$error_msg	.=	"～".date('Y年m月d日', strtotime($data["end_date"]))."です。<br/>";
						$error_msg	.=	"期間中にご利用ください。<br/>";
					}else{
						####################
						## 特典コードがユーザに使用されたかチェックする
						####################
						$table_use	= "benefits_code_user";
						$column_use	= "id,site_cd,code_id,user_id";
						$array_use = array();
						$array_use[":site_cd"] = $site_cd;
						$array_use[":user_id"] = $user_id;
						$array_use[":code_id"] = $data["id"];
						//id,site_cd,code,title,comment,start_date,end_date,del_flg,

						$where_use	= "site_cd = :site_cd ";
						$where_use	.= "AND user_id = :user_id ";
						$where_use	.= "AND code_id = :code_id ";
						$order_use = "id DESC";
						$limit_use = 1;
						$group_use = NULL;
						
						$rtn_use = $database->selectDb($table_use, $column_use, $where_use, 
													$array_use, $order_use, $limit_use, $group_use);
						$error_db_use = $database->errorDb($table_use, $rtn_use->errorCode(), __FILE__,__LINE__);
						$data_use = $database->fetchAssoc($rtn_use);
						$database->freeResult($rtn_use);

						####################
						## MASTER DATABASE切り替え
						####################
						# AUTHORITY / 既にマスターに接続してるかチェック
						$db_auth								 = $database->checkAuthority();

						# DATABASE CHANGE / スレーブだったら
						if(empty($db_auth)){

							# CLOSE DATABASE SLAVE
							$database->closeDb();

							# CONNECT DATABASE MASTER
							$database->connectDb(MASTER_ACCESS_KEY);

						}

						# トランザクションスタート
						$database->beginTransaction();
				
						####################
						## 処理続投
						####################
						if(!empty($data_use["id"])){
							## 既に使用している
							$error = 7;
							$error_msg	=	"特典コード「".$bcode."」は既に使用されております<br/>";

							## 要注意ユーザのカウントを上げる
							$table_caution	= "benefits_code_caution_user";
							$column_caution	= "id,site_cd,user_id,caution_count";
							$array_caution = array();
							$array_caution[":site_cd"] = $site_cd;
							$array_caution[":user_id"] = $user_id;
							$where_caution	= "site_cd = :site_cd ";
							$where_caution	.= "AND user_id = :user_id ";
							$order_caution = "id DESC";
							$limit_caution = 1;
							$group_caution = NULL;
							
							$rtn_caution = $database->selectDb($table_caution, $column_caution, $where_caution, 
														$array_caution, $order_caution, $limit_caution, $group_caution);
							$error_db_caution = $database->errorDb($table_caution, $rtn_caution->errorCode(), __FILE__,__LINE__);
							$data_caution = $database->fetchAssoc($rtn_caution);
							$database->freeResult($rtn_caution);
							
							if(!empty($data_caution["id"])){
								$update_caution	= array();
								$update_caution	= array(
									'caution_count'	=> intval($data_caution['caution_count']) + 1
								);
								$where_caution						= "site_cd = :site_cd AND user_id = :user_id";
								$array_caution[':site_cd']	= $site_cd;
								$array_caution[':user_id']	= $user_id;

								# 【UPDATE】caution_user
								$database->updateDb($table_caution,$update_caution,$where_caution,$array_caution);
								
							}else{
								$insert_caution	= array();
								$insert_caution	= array(
									'site_cd'	=> $site_cd,
									'user_id'	=> $user_id,
									'caution_count'	=> 1,
									'reg_date'	=> date("YmdHis")
								);

								# 【INSERT】caution_user
								$insert_caution_user_id		= $database->insertDb($table_caution,$insert_caution);
							}

							## コミット
							$database->commit();

						}else{
							####################
							## プレゼント付与、コード使用履歴追加、
							####################

							## プレセントデータ取得
							$table_present	= "benefits_code_present";
							$column_present	= "id,type,targetid,unit";
							$array_present = array();
							$array_present[":site_cd"] = $site_cd;
							$array_present[":code_id"] = $data["id"];
							$where_present	= "site_cd = :site_cd ";
							$where_present	.= "AND code_id = :code_id ";
							$where_present	.= "AND del_flg = 0 ";
							$order_present = "id ASC";
							$limit_present = NULL;
							$group_present = NULL;

							$rtn_present = $database->selectDb($table_present, $column_present, $where_present, 
														$array_present, $order_present, $limit_present, $group_present);
							$error_db_present = $database->errorDb($table_present, $rtn_present->errorCode(), __FILE__,__LINE__);

							$presents = array();
							while($presentdata   = $database->fetchAssoc($rtn_present)){
								$presents[] = $presentdata;
							}
							$database->freeResult($rtn_present);

							## プレゼント付与
							$present_num = 0;
							foreach($presents as $val){
								//応援報酬は固定で90日にする。
								$limit_date = date("YmdHis",strtotime("+90 day"));

								if($val["type"]){
									switch($val["type"]){
										case 1:// TICKET
											//直接配布じゃなく、プレゼントBOXを介す
											// チケットデータ取得
											$mail_present_data = $shopModel->getShopDataById($val["targetid"],"id,type,name,image");

											//プレゼントBOXへ
											$presentbox_insert					= array(
												'site_cd'						=> $site_cd,
												'user_id'						=> $user_id,
												'present_id'					=> 0,//暫定的に"0"としておく
												'acceptance_date'				=> date("YmdHis"),
												'category'						=> $present_category_array['benefits_code_ticket'],//応援報酬のチケットは暫定的に"41"としておく
												'type'							=> $val["type"],
												'target_id'						=> $val["targetid"],
												'unit'							=> $val["unit"],
												'limit_date'					=> $limit_date,
												'status'						=> 0
											);
											break;
										case 2:// ITEM
											//直接配布じゃなく、プレゼントBOXを介す
											//表示用へ渡す
											$mail_present_data = $itemModel->getItemDataById($val["targetid"],"id,name,image");
											
											//プレゼントBOXへ
											$presentbox_insert					= array(
												'site_cd'						=> $site_cd,
												'user_id'						=> $user_id,
												'present_id'					=> 0,//暫定的に"0"としておく
												'acceptance_date'				=> date("YmdHis"),
												'category'						=> $present_category_array['benefits_code_item'],//応援報酬のチケットは暫定的に"42"としておく
												'type'							=> $val["type"],
												'target_id'						=> $val["targetid"],
												'unit'							=> $val["unit"],
												'limit_date'					=> $limit_date,
												'status'						=> 0
											);
											break;
										case 3:// IMAGE
											//直接配布じゃなく、プレゼントBOXを介す
											//画像データのチェック
											$image_data = $imageModel->getImageDataById($val["targetid"],"id,img_name,img_key");
											$mail_present_data['id'] = $image_data['id'];
											$mail_present_data['name'] = $image_data['img_key'];
											$mail_present_data['image'] = $image_data['img_name'];

											//プレゼントBOXへ
											$presentbox_insert					= array(
												'site_cd'						=> $site_cd,
												'user_id'						=> $user_id,
												'present_id'					=> 0,//暫定的に"0"としておく
												'acceptance_date'				=> date("YmdHis"),
												'category'						=> $present_category_array['benefits_code_picture'],//応援報酬のチケットは暫定的に"43"としておく
												'type'							=> $val["type"],
												'target_id'						=> $val["targetid"],
												'unit'							=> $val["unit"],
												'limit_date'					=> $limit_date,
												'status'						=> 0
											);
											break;
									}

									if(!empty($presentbox_insert)){
										$mail_present_insert_id							= $database->insertDb("presentbox",$presentbox_insert);

										if(!empty($mail_present_insert_id)){
											$html .= "<div style=\"text-align: center;\">";
											$html .= "<img src=\"".HTTP_ITEM_IMAGE."/".$mail_present_data["image"]."\" height=60 ><br/>";
											switch($val["type"]){
												case 1:// TICKET
													$html .= $mail_present_data['name']."&nbsp;".$val["unit"]."枚をプレゼントBOXにお送りしました。<br/>";
													break;
												case 2:// ITEM
													$html .= $mail_present_data['name']."&nbsp;".$val["unit"]."個をプレゼントBOXにお送りしました。<br/>";
													break;
												case 3:// IMAGE
													$html .= $mail_present_data['name']."をプレゼントBOXにお送りしました。<br/>";
													break;
											}
											
											$html .= "</div><br/>";
											$html .= "<br/>";

											$present_num++;
											
										}else{//ERROR吐いたらエラーメッセージ
											$error_msg .= "<div style=\"text-align: center;\">";
											$error_msg .= "<img src=\"".HTTP_ITEM_IMAGE."/".$mail_present_data["image"]."\"><br/>";
											$error_msg .= "通信エラーがあったため".$mail_present_data['name']."はお渡しできませんでした。お問い合わせフォームからご連絡ください。<br/>";
											$error_msg .= "</div><br/>";
											$error_msg .= "<br/>";

											$error = 8;
											
										}
									}
								}
							}

							## 特典受け取り済みユーザ登録
							if($present_num>0){
								$insert_user	= array();
								$insert_user	= array(
									'site_cd'	=> $site_cd,
									'code_id'	=> $data["id"],
									'user_id'	=> $user_id,
									'code'	=> $bcode,
									'reg_date'	=> date("YmdHis")
								);
							}

							# 【INSERT】benefits_code_user
							$insert_code_user_id	= $database->insertDb($table_use,$insert_user);
							
							if($error>0){
								## ロールバック
								$database->rollBack();
							}else{
								## コミット
								$database->commit();
							}
							
							
						}
					}
				}
			}
			
		}

		####################
		##	DATABASE 切断
		####################

		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();
		
		# DEBUG
/*		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();
			exit();

		}
*/

		/************************************************
		**
		**	jsonでリザルトを返す
		**
		************************************************/

		$result['html']		= $html;
		$result['error_msg']		= $error_msg;
		$result['error']		= $error;
//debug
//mail("eikoshi@k-arat.co.jp","benefits[mail_present_data]",json_encode($result),"From:info@mailanime.net");

		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($result));
		exit();
	}

}


################################## FILE END #####################################
?>