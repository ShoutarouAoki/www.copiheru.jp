<?php
################################ FILE MANAGEMENT ################################
##
##	presentboxController.php
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
##	page : index		-> 所持プレゼントリスト
##	page : acceptance	-> 受け取り処理
##
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: KARAT SYSTEM
##	CREATE DATE : 2016/05/31
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

$value_array				= array('page','set','id');
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

# ID
if(!empty($_POST['id'])){
	$data['id']				= $_POST['id'];
}

# 一覧の表示件数
$list						= PRESENTBOX_LIST_UNIT;

# LIST SET
if(isset($_POST['set'])){
	$set					= $_POST['set'];
}elseif(!empty($data['set'])){
	$set					= $data['set'];
}

# SET EMPTY
if(!isset($set)){
	$set					= 0;
}

# PAGE PATH
$next_previous_path			= $page_path."index/";


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
	**	プレゼントボックス リスト
	**
	************************************************/

	if($data['page'] == "index"){


		$presentbox_conditions						= array();
		$presentbox_conditions						= array(
			'site_cd'								=> $members_data['site_cd'],
			'user_id'								=> $members_data['id'],
			'limit_over'							=> date("YmdHis"),
			'status'								=> 0,
			'order'									=> "acceptance_date DESC",
			'list'									=> $list,
			'set'									=> $set
		);

		# COUNT
		$presentbox_rows							= $presentboxModel->getPresentboxCount($presentbox_conditions);
		$next_previous								= $htmlClass->makeNextPreviousLink($next_previous_path,$presentbox_rows,$list,$set,$hidden_data);

		$presentbox_rtn								= $presentboxModel->getPresentboxList($presentbox_conditions);

		$i=0;
		while($presentbox_data = $database->fetchAssoc($presentbox_rtn)){


			# TICKET
			if($presentbox_data['type'] == $number_ticket){

				$present_data						= $shopModel->getShopDataById($presentbox_data['target_id'],"id,name,image");

			# ITEM
			}elseif($presentbox_data['type'] == $number_item){

				$present_data						= $itemModel->getItemDataById($presentbox_data['target_id'],"id,name,image");

			# IMAGE
			}elseif($presentbox_data['type'] == $number_image){

				$image_data							= $imageModel->getImageDataById($presentbox_data['target_id'],"id,img_name,img_key");
				$present_data['id']					= $image_data['id'];
				$present_data['name']				= $image_data['img_key'];
				$present_data['image']				= "thumb/".$image_data['img_name'];

			}

			# 受け取り期限
			$limit_time								= date("His",strtotime($presentbox_data['limit_date']));

			if($limit_time == "235959"){
				$limit_date							= date("Y年m月d日",strtotime($presentbox_data['limit_date']));
			}elseif($limit_time == "000000"){
				$limit_date							= date("Y年m月d日",strtotime("-1 day",strtotime($presentbox_data['limit_date'])));
			}else{
				$limit_date							= date("Y年m月d日 H時i分",strtotime($presentbox_data['limit_date']));
			}

			if(!empty($presentbox_data['message'])){
				$display_name						= "【".$presentbox_data['message']."】";
			}else{
				$display_name						= NULL;
			}

			$presentbox_list['id'][$i]				= $presentbox_data['id'];
			$presentbox_list['unit'][$i]			= $presentbox_data['unit'];
			$presentbox_list['name'][$i]			= $display_name.$present_data['name'];
			$presentbox_list['image'][$i]			= $present_data['image'];
			$presentbox_list['limit_date'][$i]		= $limit_date;

			$i++;

		}

		$database->freeResult($presentbox_rtn);


	/************************************************
	**
	**	ACCEPTANCE
	**	============================================
	**	プレゼントボックス 受け取り処理
	**
	************************************************/

	}elseif($data['page'] == "acceptance"){


		$error										= NULL;
		$errormessage								= NULL;

		$media_flg_update							= NULL;

		# 受け取り処理
		if(!empty($data['id'])){

			/************************************************
			**
			**	MASTER DATABASE切り替え
			**
			************************************************/

			# AUTHORITY / 既にマスターに接続してるかチェック
			$db_auth												 = $database->checkAuthority();

			# DATABASE CHANGE / スレーブだったら
			if(empty($db_auth)){

				# CLOSE DATABASE SLAVE
				$database->closeDb();

				# CONNECT DATABASE MASTER
				$database->connectDb(MASTER_ACCESS_KEY);

			}


			/************************************************
			**
			**	トランザクション開始
			**	============================================
			**	このページではトランザクション処理をする
			**	何か問題が起きたら全てロールバック
			**
			************************************************/

			# トランザクションスタート
			$database->beginTransaction();

			$point_recv[1]										= array();
			$point_recv[2]										= array();
			$points_insert_flg									= NULL;

			# 本日
			$today												= date("YmdHis");


			# 一括受け取り
			if($data['id'] == "all"){

				$update_s_point									= 0;
				$update_f_point									= 0;

				$acceptance_conditions							= array();
				$acceptance_conditions							= array(
					'site_cd'									=> $members_data['site_cd'],
					'user_id'									=> $members_data['id'],
					'limit_over'								=> $today,
					'status'									=> 0,
					'order'										=> "acceptance_date DESC",
				);

				$acceptance_rtn									= $presentboxModel->getPresentboxList($acceptance_conditions);

				$i=0;
				while($acceptance_data = $database->fetchAssoc($acceptance_rtn)){

					# 受け取り済み&削除なら除外
					if($acceptance_data['status'] >= 8){
						continue;
					}

					# 受け取り期限過ぎてたら除外
					if($acceptance_data['limit_date'] < $today){
						continue;
					}

					$acceptance									= NULL;

					# TICKET
					if($acceptance_data['type'] == $number_ticket){

						$ticket_data							= $shopModel->getShopDataById($acceptance_data['target_id'],"type");

						# ログイン無料配布
						if($ticket_data['type'] == 1){

							# f_pointに加算
							$update_f_point						+= $acceptance_data['unit'];
							$point_recv[2][]					 = $acceptance_data['unit'];


						# プレゼント配布
						}elseif($ticket_data['type'] == 2){

							# s_pointに加算
							$update_s_point						+= $acceptance_data['unit'];
							$point_recv[1][]					 = $acceptance_data['unit'];


						# もしそれ以外があったらs_pointに加算
						}else{

							# s_pointに加算
							$update_s_point						+= $acceptance_data['unit'];
							$point_recv[1][]				 	 = $acceptance_data['unit'];

						}

						$points_insert_flg						= 1;
						$acceptance								= 1;

					# ITEM
					}elseif($acceptance_data['type'] == $number_item){

						# 所持確認
						$itembox_conditions						= array();
						$itembox_conditions						= array(
							'user_id'							=> $members_data['id'],
							'item_id'							=> $acceptance_data['target_id'],
							'status'							=> 0
						);
						$itembox_data							= $itemboxModel->getItemboxData($itembox_conditions,"id,unit");

						# 持ってれば加算
						if(!empty($itembox_data['id'])){

							$update_unit						= $itembox_data['unit'] + $acceptance_data['unit'];

							$itembox_update['unit']				= $update_unit;
							$itembox_update_where				= "id = :id";
							$itembox_update_conditions[':id']	= $itembox_data['id'];

							# 【UPDATE】 / itembox
							$return								= $database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

							if(!empty($return)){
								$acceptance						= 1;
							}

						# なければ追加
						}else{

							$itembox_insert						= array();
							$itembox_insert						= array(
								'user_id'						=> $members_data['id'],
								'item_id'						=> $acceptance_data['target_id'],
								'unit'							=> $acceptance_data['unit'],
								'status'						=> 0
							);

							# 【INSERT】 / itembox
							$acceptance							= $database->insertDb("itembox",$itembox_insert);

						}

					# IMAGE
					}elseif($acceptance_data['type'] == $number_image){

						$attachment_data						= $imageModel->getImageDataById($acceptance_data['target_id'],"id,img_name,img_key");

						if(!empty($attachment_data['id'])){

							$albums_insert						= array();
							$albums_insert						= array(
								'user_id'						=> $members_data['id'],
								'image'							=> $attachment_data['img_name'],
								'name'							=> $attachment_data['img_key'],
								'acceptance_date'				=> $today,
								'status'						=> 0
							);

							# 【INSERT】 / albums
							if($acceptance_data['unit'] > 1){

								for($j=0;$j<$acceptance_data['unit'];$j++){
									$acceptance					= $database->insertDb("albums",$albums_insert);
								}

							}else{

								$acceptance						= $database->insertDb("albums",$albums_insert);

							}

							if($members_data['media_flg'] != 10){
								$media_flg_update				= 1;
							}

						}

					}

					# 正常に処理されなければエラー
					if(empty($acceptance)){
						$error									= 2;
						break;
					}

					$i++;

				}

				$database->freeResult($acceptance_rtn);


				# 各処理が終わったらmembersのアップデートと該当プレゼントボックスレコードを受け取り済みにする
				if(empty($error) && $i > 0){

					# 一括はここで処理
					if(!empty($update_s_point) || !empty($update_f_point)){

						if(!empty($update_s_point)){
							$members_update['s_point']			= $members_data['s_point'] + $update_s_point;
						}

						if(!empty($update_f_point)){

							$members_update['f_point']			= $members_data['f_point'] + $update_f_point;

							# ユーザーf_pointは毎日ログイン配布される分だけしか所持できない為
							if($members_update['f_point'] > FREE_POINT_LIMIT){
								$members_update['f_point']		= FREE_POINT_LIMIT;
							}

						}

						# アルバムに画像追加したら
						if(!empty($media_flg_update)){
							$members_update['media_flg']		= 10;
						}

						$members_update_where					= "id = :id";
						$members_update_conditions[':id']		= $members_data['id'];

						# 【UPDATE】 / members
						$return									= $database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

						if(empty($return)){
							$error								 = 3;
						}

					# アルバムに画像を追加したら
					}elseif(!empty($media_flg_update)){

						$members_update['media_flg']			= 10;

						$members_update_where					= "id = :id";
						$members_update_conditions[':id']		= $members_data['id'];

						# 【UPDATE】 / members
						$return									= $database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

					}

					$presentbox_update							= array();
					$presentbox_update							= array(
						'status'								=> 8
					);

					$presentbox_update_where					 = "site_cd = :site_cd ";
					$presentbox_update_where					.= "AND user_id = :user_id ";
					$presentbox_update_where					.= "AND limit_date >= :limit_date ";
					$presentbox_update_where					.= "AND status = :default_status";

					$presentbox_update_conditions				= array();
					$presentbox_update_conditions				= array(
						':site_cd'								=> $members_data['site_cd'],
						':user_id'								=> $members_data['id'],
						':limit_date'							=> $today,
						':default_status'						=> 0
					);

					# 【UPDATE】 / mails
					$return										 = $database->updateDb("presentbox",$presentbox_update,$presentbox_update_where,$presentbox_update_conditions);
					if(empty($return)){
						$error									 = 4;
					}

				}


			# 個別受け取り
			}elseif(is_numeric($data['id'])){

				# PRESENTBOX DATA
				$acceptance_data								= $presentboxModel->getPresentboxDataById($data['id']);

				# OK
				if(!empty($acceptance_data['id'])){

					# 受け取り済み&削除なら除外
					if($acceptance_data['status'] >= 8){
						$error									= 4;
						$errormessage							= "このアイテムは削除、もしくは受け取り済みです。";
					}

					# 受け取り期限過ぎてたら除外
					if($acceptance_data['limit_date'] < $today){
						$error									= 5;
						$errormessage							= "このアイテムの受け取り期限は過ぎています。";
					}

					# OK
					if(empty($error)){

						# TICKET
						if($acceptance_data['type'] == $number_ticket){

							$ticket_data						= $shopModel->getShopDataById($acceptance_data['target_id'],"type");

							# ログイン無料配布
							if($ticket_data['type'] == 1){

								# f_pointに加算
								$update_point					= $members_data['f_point'] + $acceptance_data['unit'];
								$members_update['f_point']		= $update_point;
								$point_recv[2][]				= $acceptance_data['unit'];

								# ユーザーf_pointは毎日ログイン配布される分だけしか所持できない為
								if($members_update['f_point'] > FREE_POINT_LIMIT){
									$members_update['f_point']	= FREE_POINT_LIMIT;
								}

							# プレゼント配布
							}elseif($ticket_data['type'] == 2){

								# s_pointに加算
								$update_point					= $members_data['s_point'] + $acceptance_data['unit'];
								$members_update['s_point']		= $update_point;
								$point_recv[1][]				= $acceptance_data['unit'];

							# もしそれ以外があったらs_pointに加算
							}else{

								# s_pointに加算
								$update_point					= $members_data['s_point'] + $acceptance_data['unit'];
								$members_update['s_point']		= $update_point;
								$point_recv[1][]				= $acceptance_data['unit'];

							}

							# 個別はここで処理
							$members_update_where				= "id = :id";
							$members_update_conditions[':id']	= $members_data['id'];

							# 【UPDATE】 / members
							$return								= $database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

							if(!empty($return)){
								$acceptance						= 1;
								$points_insert_flg				= 1;
							}

						# ITEM
						}elseif($acceptance_data['type'] == $number_item){

							# 所持確認
							$itembox_conditions					= array();
							$itembox_conditions					= array(
								'user_id'						=> $members_data['id'],
								'item_id'						=> $acceptance_data['target_id'],
								'status'						=> 0
							);
							$itembox_data						= $itemboxModel->getItemboxData($itembox_conditions,"id,unit");

							# 持ってれば加算
							if(!empty($itembox_data['id'])){

								$update_unit					= $itembox_data['unit'] + $acceptance_data['unit'];

								$itembox_update['unit']			= $update_unit;
								$itembox_update_where			= "id = :id";
								$itembox_update_conditions[':id']	= $itembox_data['id'];

								# 【UPDATE】 / itembox
								$return							= $database->updateDb("itembox",$itembox_update,$itembox_update_where,$itembox_update_conditions);

								if(!empty($return)){
									$acceptance					= 1;
								}

							# なければ追加
							}else{

								$itembox_insert					= array();
								$itembox_insert					= array(
									'user_id'					=> $members_data['id'],
									'item_id'					=> $acceptance_data['target_id'],
									'unit'						=> $acceptance_data['unit'],
									'status'					=> 0
								);

								# 【INSERT】 / itembox
								$acceptance						= $database->insertDb("itembox",$itembox_insert);

							}

						# IMAGE
						}elseif($acceptance_data['type'] == $number_image){

							$attachment_data					= $imageModel->getImageDataById($acceptance_data['target_id'],"id,img_name,img_key");

							if(!empty($attachment_data['id'])){

								$albums_insert					= array();
								$albums_insert					= array(
									'user_id'					=> $members_data['id'],
									'image'						=> $attachment_data['img_name'],
									'name'						=> $attachment_data['img_key'],
									'acceptance_date'			=> $today,
									'status'					=> 0
								);

								# 【INSERT】 / albums
								if($acceptance_data['unit'] > 1){

									for($j=0;$j<$acceptance_data['unit'];$j++){
										$acceptance				= $database->insertDb("albums",$albums_insert);
									}

								}else{

									$acceptance					= $database->insertDb("albums",$albums_insert);

								}

								# アルバムに画像を追加した場合
								if($members_data['media_flg'] != 10){

									$members_update['media_flg']		= 10;

									$members_update_where				= "id = :id";
									$members_update_conditions[':id']	= $members_data['id'];

									# 【UPDATE】 / members
									$members_return						= $database->updateDb("members",$members_update,$members_update_where,$members_update_conditions);

								}

							}

						}


						# 正常に処理されなければエラー
						if(empty($acceptance)){

							$error								= 2;

						# プレゼントボックスを受け取り済みにアップデート
						}else{

							$presentbox_update					= array();
							$presentbox_update					= array(
								'status'						=> 8
							);

							$presentbox_update_where			 = "id = :id ";
							$presentbox_update_conditions[':id']	= $data['id'];

							# 【UPDATE】 / mails
							$return								 = $database->updateDb("presentbox",$presentbox_update,$presentbox_update_where,$presentbox_update_conditions);
							if(empty($return)){
								$error							 = 3;
							}

						}

					}


				# ERROR
				}else{

					$error										= 4;
					$errormessage								= "このアイテムは削除、もしくは既に受け取り済みです。";

				}


			}else{

				$error											= 1;
				$errormessage									= "正常に受け取りできませんでした";

			}


			# ポイント付与の場合はpointsにインサート
			if(empty($error) && !empty($points_insert_flg)){

				# pay_flg 判定 無料ユーザー
				if($members_data['status'] == 3) {
					$pay_flg									= 2;
				# 定額ユーザー
				}elseif($members_data['status'] == 2) {
					$pay_flg									= 3;
				# 通常ユーザー
				}elseif($members_data['status'] != 0){

					# 無課金
					if($members_data['pay_count'] == 0){
						$pay_flg								= 2;
					# 課金
					}else{
						$pay_flg								= 1;
					}

				# その他
				}else{
					$pay_flg									= 0;
				}

				# ログインボーナス
				$login_recv_point_no_id							= $point_no_array[$point_name_array['login_recv']][2];

				# インセンティブ
				$present_recv_point_no_id						= $point_no_array[$point_name_array['present_recv']][2];

				foreach($point_recv as $point_type => $point_value){

					# インセンティブ
					if($point_type == 1){
						$point_no_id							= $present_recv_point_no_id;
					# ログインボーナス
					}elseif($point_type == 2){
						$point_no_id							= $login_recv_point_no_id;
					# 念のため
					}else{
						$point_no_id							= $present_recv_point_no_id;
					}

					foreach($point_value as $key => $value){

						$points_insert							= array();
						$points_insert							= array(
							'user_id'							=> $members_data['id'],
							'site_cd'							=> $members_data['site_cd'],
							'sex'								=> $members_data['sex'],
			                'ad_code'							=> $members_data['ad_code'],
			                'domain_flg'						=> $members_data['domain_flg'],
							'point'								=> $value,
							'point_no_id'						=> $point_no_id,
							'campaign_id'						=> 0,
			                'point_type'						=> $point_type,
							'log_date'							=> date("YmdHis"),
			                'pay_flg'							=> $pay_flg
						);

						# 【insert】points
						$database->insertDb("points",$points_insert);

					}

				}

			}




			/************************************************
			**
			**	コミット
			**	============================================
			**	正常にきたらコミットする
			**	何か問題があればロールバック
			**
			************************************************/

			# COMMIT : 一括処理
			if(empty($error)){
				$database->commit();
				$result											 = 1;
			# ROLLBACK : 巻き戻し
			}else{
				if(empty($errormessage)){
					$database->rollBack();
					$errormessage								= "正常に処理できませんでした";
				}
			}


		# ERROR
		}else{

			$error												= 1;
			$errormessage										= "正常に受け取りできませんでした";

		}


		/************************************************
		**
		**	リスト再生成
		**	============================================
		**	プレゼントボックス リスト
		**
		************************************************/

		# 一括受け取りが正常に終わったら何もしない
		if($data['id'] == "all" && empty($error) && !empty($result)){

			$presentbox_rows							= 0;

		}else{

			$presentbox_conditions						= array();
			$presentbox_conditions						= array(
				'site_cd'								=> $members_data['site_cd'],
				'user_id'								=> $members_data['id'],
				'limit_over'							=> date("YmdHis"),
				'status'								=> 0,
				'order'									=> "acceptance_date DESC",
				'list'									=> $list,
				'set'									=> $set
			);

			# COUNT
			$presentbox_rows							= $presentboxModel->getPresentboxCount($presentbox_conditions);
			$next_previous								= $htmlClass->makeNextPreviousLink($next_previous_path,$presentbox_rows,$list,$set,$hidden_data);

			$presentbox_rtn								= $presentboxModel->getPresentboxList($presentbox_conditions);

			$i=0;
			while($presentbox_data = $database->fetchAssoc($presentbox_rtn)){


				# TICKET
				if($presentbox_data['type'] == $number_ticket){

					$present_data						= $shopModel->getShopDataById($presentbox_data['target_id'],"id,name,image");

				# ITEM
				}elseif($presentbox_data['type'] == $number_item){

					$present_data						= $itemModel->getItemDataById($presentbox_data['target_id'],"id,name,image");

				# IMAGE
				}elseif($presentbox_data['type'] == $number_image){

					$image_data							= $imageModel->getImageDataById($presentbox_data['target_id'],"id,img_name,img_key");
					$present_data['id']					= $image_data['id'];
					$present_data['name']				= $image_data['img_key'];
					$present_data['image']				= "thumb/".$image_data['img_name'];

				}

				# 受け取り期限
				$limit_date								= date("Y年m月d日",strtotime($presentbox_data['limit_date']));

				$presentbox_list['id'][$i]				= $presentbox_data['id'];
				$presentbox_list['unit'][$i]			= $presentbox_data['unit'];
				$presentbox_list['name'][$i]			= $present_data['name'];
				$presentbox_list['image'][$i]			= $present_data['image'];
				$presentbox_list['limit_date'][$i]		= $limit_date;

				$i++;

			}

			$database->freeResult($presentbox_rtn);

		}


		/************************************************
		**
		**	DATABASE 切断
		**
		************************************************/

		# CLOSE DATABASE
		$database->closeDb();
		$database->closeStmt();


		/************************************************
		**
		**	acceptance.incをここで読んで終了
		**
		************************************************/

		# VIEW FILE チェック & パス生成
		$view_directory							= $mainClass->getViewDirectory($directory,$data['page'],$device_file);

		# 読み込み
		include_once($view_directory);


		# DEBUG
		if(defined("SYSTEM_CHECK") && !empty($_REQUEST['debug'])){

			# SYSTEM DEBUG
			$mainClass->debug($result);
			$mainClass->outputDebugSystem();

		}

		# 終了
		exit();


	}

}


################################## FILE END #####################################
?>