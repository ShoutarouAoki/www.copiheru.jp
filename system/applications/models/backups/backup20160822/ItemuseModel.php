<?php
/********************************************************************************
**	
**	ItemuseModel.php
**	=============================================================================
**
**	■PAGE / 
**	ITEM USE MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	ITEM USE CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	ITEM USE CLASS
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: KARAT SYSTEM
**	CREATE DATE : 2015/05/31
**	CREATER		:
**
**	=============================================================================
**
**	■ REWRITE (改修履歴)
**
**
**
**
**
**
**
**
**
**
**
**
*********************************************************************************/


# CLASS DEFINE
class ItemuseModel{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	private	$database;
	private	$output;

	# CONSTRUCT
	function __construct($database=NULL,$main=NULL){
		$this->database		= $database;
		$this->output		= $main;
		$this->table		= "itemuse";
    }

	# DESTRUCT
	function __destruct(){
		
    }


	/**************************************************
	**
	**	getItemuseList
	**	----------------------------------------------
	**	所持アイテムリスト
	**
	**************************************************/

	public function getItemuseList($post_data,$column=NULL){


		if(empty($post_data['user_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column					 = "*";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;
		$array[':user_id']			 = $post_data['user_id'];
		

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['item_id'])){
			$where					.= "AND item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}

		$where						.= "AND user_id = :user_id ";

		if(isset($post_data['character_id'])){
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		# 有効期限切れ
		if(isset($post_data['time_over'])){
			$where					.= "AND limit_time <= :time_over ";
			$array[':time_over']	 = $post_data['time_over'];
		}

		# 有効期限前
		if(isset($post_data['time_under'])){
			$where					.= "AND limit_time >= :time_under ";
			$array[':time_under']	 = $post_data['time_under'];
		}

		# 利用回数切れ
		if(isset($post_data['count_over'])){
			$where					.= "AND limit_count = :count_over ";
			$array[':count_over']	 = 0;
		}

		# 利用回数あり
		if(isset($post_data['count_under'])){
			$where					.= "AND limit_count > :count_under ";
			$array[':count_under']	 = 0;
		}

		# 通常利用回数
		if(isset($post_data['limit_count'])){
			$where					.= "AND limit_count > :limit_count ";
			$array[':limit_count']	 = $post_data['count'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		if(!empty($post_data['order'])){
			$order					 = $post_data['order'];
		}

		if(!empty($post_data['limit'])){
			$limit					 = $post_data['limit'];
		}

		if(!empty($post_data['group'])){
			$group					 = $post_data['group'];
		}

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getItemuseList",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		return $rtn;


	}



	/**************************************************
	**
	**	getItemuseCount
	**	----------------------------------------------
	**	所持アイテムリスト カウント
	**
	**************************************************/

	public function getItemuseCount($post_data,$column=NULL){

		if(empty($post_data['user_id'])){
			return FALSE;
		}

		if(empty($column)){
			$column					 = "id";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':site_cd']			 = SITE_CD;
		$array[':user_id']			 = $post_data['user_id'];
		

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		$where						 = "site_cd = :site_cd ";

		if(isset($post_data['item_id'])){
			$where					.= "AND item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}

		$where						.= "AND user_id = :user_id ";

		if(isset($post_data['character_id'])){
			$where					.= "AND character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		# 有効期限切れ
		if(isset($post_data['time_over'])){
			$where					.= "AND limit_time <= :time_over ";
			$array[':time_over']	 = $post_data['time_over'];
		}

		# 有効期限前
		if(isset($post_data['time_under'])){
			$where					.= "AND limit_time >= :time_under ";
			$array[':time_under']	 = $post_data['time_under'];
		}

		# 利用回数切れ
		if(isset($post_data['count_over'])){
			$where					.= "AND limit_count = :count_over ";
			$array[':count_over']	 = 0;
		}

		# 利用回数あり
		if(isset($post_data['count_under'])){
			$where					.= "AND limit_count > :count_under ";
			$array[':count_under']	 = 0;
		}

		# 通常利用回数
		if(isset($post_data['limit_count'])){
			$where					.= "AND limit_count > :limit_count ";
			$array[':limit_count']	 = $post_data['count'];
		}

		if(isset($post_data['status'])){
			$where					.= "AND status = :status ";
			$array[':status']		 = $post_data['status'];
		}

		$order						 = NULL;
		$limit						 = NULL;
		$group						 = NULL;

		$rtn						 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error						 = $database->errorDb("getItemuseCount",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$rows						= $database->numRows($rtn);

		$database->freeResult($rtn);

		return $rows;

	}



	/**************************************************
	**
	**	getItemuseDataById
	**	----------------------------------------------
	**	所持アイテム取得
	**
	**************************************************/

	public function getItemuseDataById($id,$column=NULL){

		if(empty($id)){
			return FALSE;
		}

		if(empty($column)){
			$column				 = "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':id']			 = $id;
		$array[':status']		 = 0;

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "id = :id ";
		$where					.= "AND status = :status ";
		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getItemuseDataById",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	getItemuseListJoinOnItems
	**	----------------------------------------------
	**	ユーザー利用中アイテムの取得(ここでループ生成)
	**	----------------------------------------------
	**	itemsとJOIN
	**	ユーザー利用中アイテム専用
	**	-----------------------------------------------
	** 【発行SQLサンプル】
	**	SELECT u.id, u.limit_time, u.limit_count, i.id as items_id, i.category, i.type, i.effect, i.name FROM itemuse u INNER JOIN items i ON u.item_id = i.id AND u.user_id = 1 AND u.character_id = 5 AND u.status = 0
	**
	**************************************************/

	public function getItemuseListJoinOnItems($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column					 = "u.id as itemuse_id, u.limit_time, u.limit_count, u.status as itemuse_status, ";
			$column					.= "i.id as items_id, i.name, i.image, i.word, i.category, i.type, i.effect";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# AUTHORITY
		$db_auth					 = $database->checkAuthority();
		$db_check					 = NULL;

		# DATABASE CHANGE
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check				 = 1;

		}

		# PARAMETER
		$array						 = array();
		$array[':user_id']			 = $post_data['user_id'];

		# SELECT
		$sql						 = "SELECT ".$column." FROM ".$this->table." u ";

		# JOIN
		$sql						.= "INNER JOIN items i ";

		# ON
		$sql						.= "ON u.item_id = i.id ";

		# ITEM ID
		if(isset($post_data['item_id'])){
			$sql					.= "AND u.item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}

		# USER ID
		$sql						.= "AND u.user_id = :user_id ";

		# CHARACTER ID
		if(!empty($post_data['character_id'])){
			$sql					.= "AND u.character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		# STATUS
		if(isset($post_data['status'])){
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = 0;
		}

		# GROUP
		if(!empty($post_data['group'])){
			$sql					.= " GROUP BY ".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql					.= " ORDER BY ".$post_data['order'];
		}

		# LIMIT
		if(!empty($post_data['limit'])){
			$sql					.= " LIMIT ".$post_data['limit'];
		}

		$rtn						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = $database->removeTags($array);

		try{
			$rtn					 = $database->prepare($sql,$array,$debug=1);
	 		$result					 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}

		# CHECK
		$list								 = array();
		$list								 = NULL;
		$nowtime							 = date("YmdHis");

		$i=0;
		while($data = $database->fetchAssoc($rtn)){


			/**************************************
			**
			**	既に有効期限や使用回数が切れたものがあればここで処理
			**	(基本は使用時/mail/detail/内で行う)
			**
			***************************************/

			$end								= NULL;


			# ここでアイテム効果変動キャンペーンがあるかチェック(アイテム効果変動はcampaign_type = 3)
			if(!empty($post_data['campaign_id']) && $post_data['campaign_type'] >= 3){

				$items_campaign_conditions		= array();
				$items_campaign_conditions		= array(
					'item_id'					=> $data['items_id'],
					'campaign_id'				=> $post_data['campaign_id'],
					'status'					=> 0,
					'order'						=> 'id'
				);

				$items_campaign_data			= $this->getItemDataFromThisModel($items_campaign_conditions,"id,word");

				# あれば設定上書き
				if(!empty($items_campaign_data['id'])){

					if(!empty($items_campaign_data['word'])){
						$data['word']			= $items_campaign_data['word'];
					}

				}

			}


			# 時間カウントで有効期限が切れててstatusが0のもの
			if($data['type'] == 1 && $data['limit_time'] < $nowtime && $data['itemuse_status'] == 0){

				$end							= 1;

			# 回数カウントで利用可能回数がゼロでstatusが0のもの
			}elseif($data['type'] == 2 && $data['limit_count'] == 0 && $data['itemuse_status'] == 0){

				$end							= 1;

			# 有効期限ありで有効期限が切れててstatusが0のもの
			}elseif($data['type'] == 3 && $data['limit_time'] < $nowtime && $data['itemuse_status'] == 0){

				$end							= 1;

			}

			# 使用できないものは status を 8 にアップデートして処理抜け
			if(!empty($end)){

				$itemuse_update['status']			= 8;

				# UPDATE WHERE
				$itemuse_update_where				= "id = :id";
				$itemuse_update_conditions[':id']	= $data['itemuse_id'];

				# 【UPDATE】 / itembox
				$database->updateDb($this->table,$itemuse_update,$itemuse_update_where,$itemuse_update_conditions);

				continue;

			}

			# 表示ページチェック
			if(!empty($post_data['page'])){

				# 返信画面
				if($post_data['page'] == "mail"){
					if($data['category'] != 0 && $data['category'] != 1){
						continue;
					}
				}

			}

			$list['id'][$i]				= $data['itemuse_id'];
			$list['item_id'][$i]		= $data['items_id'];
			$list['name'][$i]			= $data['name'];
			$list['image'][$i]			= $data['image'];
			$list['word'][$i]			= $data['word'];
			$list['type'][$i]			= $data['type'];
			$list['limit_time'][$i]		= $data['limit_time'];
			$list['limit_count'][$i]	= $data['limit_count'];

			# 使用チェック
			$list[$data['items_id']]['id']			= $data['itemuse_id'];
			$list[$data['items_id']]['type']		= $data['type'];
			$list[$data['items_id']]['limit_time']	= $this->getLimitTime($nowtime,$data['limit_time']);
			$list[$data['items_id']]['limit_count']	= $data['limit_count'];

			$i++;

		}

		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}

		return $list;

	}



	/**************************************************
	**
	**	getItemuseDataJoinOnItems
	**	----------------------------------------------
	**	ユーザー利用中アイテムの取得
	**	----------------------------------------------
	**	itemsとJOIN
	**	ユーザー利用中アイテム専用
	**	-----------------------------------------------
	** 【発行SQLサンプル】
	**	SELECT u.id, u.limit_time, u.limit_count, i.id as items_id, i.category, i.type, i.effect, i.name FROM itemuse u INNER JOIN items i ON u.item_id = i.id AND u.user_id = 1 AND u.character_id = 5 AND u.status = 0
	**
	**************************************************/

	public function getItemuseDataJoinOnItems($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column					 = "u.id as itemuse_id, u.limit_time, u.limit_count, u.status as itemuse_status, ";
			$column					.= "i.id as items_id, i.name, i.image, i.word, i.category, i.type, i.effect";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# PARAMETER
		$array						 = array();
		$array[':user_id']			 = $post_data['user_id'];

		# SELECT
		$sql						 = "SELECT ".$column." FROM ".$this->table." u ";

		# JOIN
		$sql						.= "INNER JOIN items i ";

		# ON
		$sql						.= "ON u.item_id = i.id ";

		# ITEM ID
		if(isset($post_data['item_id'])){
			$sql					.= "AND u.item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}

		# USER ID
		$sql						.= "AND u.user_id = :user_id ";

		# CHARACTER ID
		if(!empty($post_data['character_id'])){
			$sql					.= "AND u.character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		# STATUS
		if(isset($post_data['status'])){
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = 0;
		}

		# GROUP
		if(!empty($post_data['group'])){
			$sql					.= " GROUP BY ".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql					.= " ORDER BY ".$post_data['order'];
		}

		# LIMIT
		$sql						.= " LIMIT 1";

		$rtn						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = $database->removeTags($array);

		try{
			$rtn					 = $database->prepare($sql,$array,$debug=1);
	 		$result					 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}


		$data						= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	/**************************************************
	**
	**	checkMatchItemuseTypeListJoinOnItems
	**	----------------------------------------------
	**	ユーザー利用中アイテムの同一種類重複チェック
	**	----------------------------------------------
	**	itemsとJOIN
	**	ユーザー利用中アイテム専用
	**	-----------------------------------------------
	** 【発行SQLサンプル】
	**	SELECT u.id as itemuse_id, u.limit_time, u.limit_count, u.status as itemuse_status, i.id as items_id, i.type, i.effect, i.name FROM itemuse u INNER JOIN items i ON u.item_id = i.id AND u.user_id = 1 AND u.character_id = 5 AND u.status = 0
	**
	**************************************************/

	public function checkMatchItemuseTypeListJoinOnItems($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id']) || empty($post_data['effect'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column					 = "u.id as itemuse_id, u.limit_time, u.limit_count, u.status as itemuse_status, ";
			$column					.= "i.id as items_id, i.name, i.type, i.effect";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# AUTHORITY
		$db_auth					 = $database->checkAuthority();
		$db_check					 = NULL;

		# DATABASE CHANGE
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check				 = 1;

		}

		# PARAMETER
		$array						 = array();
		$array[':user_id']			 = $post_data['user_id'];

		# SELECT
		$sql						 = "SELECT ".$column." FROM ".$this->table." u ";

		# JOIN
		$sql						.= "INNER JOIN items i ";

		# ON
		$sql						.= "ON u.item_id = i.id ";

		# ITEM ID
		if(isset($post_data['item_id'])){
			$sql					.= "AND u.item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}

		# USER ID
		$sql						.= "AND u.user_id = :user_id ";

		# CHARACTER ID
		if(!empty($post_data['character_id'])){
			$sql					.= "AND u.character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		# STATUS
		if(isset($post_data['status'])){
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = 0;
		}

		# GROUP
		if(!empty($post_data['group'])){
			$sql					.= " GROUP BY ".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql					.= " ORDER BY ".$post_data['order'];
		}

		# LIMIT
		if(!empty($post_data['limit'])){
			$sql					.= " LIMIT ".$post_data['limit'];
		}

		$rtn						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = $database->removeTags($array);

		try{
			$rtn					 = $database->prepare($sql,$array,$debug=1);
	 		$result					 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}

		# CHECK
		$check						 = array();
		$check						 = NULL;
		$nowtime					 = date("YmdHis");

		$i=0;
		while($data = $database->fetchAssoc($rtn)){

			# 効果タイプが違うアイテムは弾く
			if($post_data['effect'] != $data['effect']){
				continue;
			}

			/**************************************
			**
			**	既に有効期限や使用回数が切れたものがあればここで処理
			**	(基本は使用時/mail/detail/内で行う)
			**
			***************************************/

			$end						= NULL;

			# 時間カウントで有効期限が切れててstatusが0のもの
			if($data['type'] == 1 && $data['limit_time'] < $nowtime && $data['itemuse_status'] == 0){

				$end					= 1;

			# 回数カウントで利用可能回数がゼロでstatusが0のもの
			}elseif($data['type'] == 2 && $data['limit_count'] == 0 && $data['itemuse_status'] == 0){

				$end					= 1;

			# 有効期限ありで有効期限が切れててstatusが0のもの
			}elseif($data['type'] == 3 && $data['limit_time'] < $nowtime && $data['itemuse_status'] == 0){

				$end					= 1;

			}

			# 使用できないものは status を 8 にアップデートして処理抜け
			if(!empty($end)){

				$itemuse_update['status']			= 8;

				# UPDATE WHERE
				$itemuse_update_where				= "id = :id";
				$itemuse_update_conditions[':id']	= $data['itemuse_id'];

				# 【UPDATE】 / itembox
				$database->updateDb($this->table,$itemuse_update,$itemuse_update_where,$itemuse_update_conditions);

				continue;

			}

			# 同じ効果タイプのアイテムを使ってた場合
			$check['id'][$i]			= $data['itemuse_id'];
			$check['item_id'][$i]		= $data['items_id'];
			$check['name'][$i]			= $data['name'];
			$check['effect'][$i]		= $data['effect'];

			$i++;

		}

		if($i > 0){
			$check['result']			= 1;
		}

		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}

		return $check;

	}



	/**************************************************
	**
	**	checkItemUseLimit
	**	----------------------------------------------
	**	ユーザー利用中アイテムの有効期限チェック&アップデート
	**	----------------------------------------------
	**	itemsとJOIN
	**	ユーザー利用中アイテム専用
	**	----------------------------------------------
	**	※頻繁に使うためメソッド化しちゃうね
	**
	**	-----------------------------------------------
	** 【発行SQLサンプル】
	**	SELECT u.id, u.limit_time, u.limit_count, i.id as items_id, i.category, i.type, i.effect, i.name FROM itemuse u INNER JOIN items i ON u.item_id = i.id AND u.user_id = 1 AND u.character_id = 5 AND u.status = 0
	**
	**************************************************/

	public function checkItemUseLimit($post_data,$column=NULL){

		# ERROR
		if(empty($post_data['user_id'])){
			return FALSE;
		}

		# COLUMN
		if(empty($column)){
			$column					 = "u.id as itemuse_id, u.limit_time, u.limit_count, u.status as itemuse_status, ";
			$column					.= "i.id as items_id, i.name, i.image, i.word, i.category, i.type, i.effect";
		}

		# DB / MAIN CLASS
		$database					 = NULL;
		$database					 = $this->database;
		$output						 = NULL;
		$output						 = $this->output;

		# AUTHORITY
		$db_auth					 = $database->checkAuthority();
		$db_check					 = NULL;

		# DATABASE CHANGE
		if(empty($db_auth)){

			# CLOSE DATABASE SLAVE
			$database->closeDb();

			# CONNECT DATABASE MASTER
			$database->connectDb(MASTER_ACCESS_KEY);

			$db_check				 = 1;

		}

		# PARAMETER
		$array						 = array();
		$array[':user_id']			 = $post_data['user_id'];

		# SELECT
		$sql						 = "SELECT ".$column." FROM ".$this->table." u ";

		# JOIN
		$sql						.= "INNER JOIN items i ";

		# ON
		$sql						.= "ON u.item_id = i.id ";

		# ITEM ID
		if(isset($post_data['item_id'])){
			$sql					.= "AND u.item_id = :item_id ";
			$array[':item_id']		 = $post_data['item_id'];
		}

		# USER ID
		$sql						.= "AND u.user_id = :user_id ";

		# CHARACTER ID
		if(!empty($post_data['character_id'])){
			$sql					.= "AND u.character_id = :character_id ";
			$array[':character_id']	 = $post_data['character_id'];
		}

		# STATUS
		if(isset($post_data['status'])){
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = $post_data['status'];
		}else{
			$sql					.= "AND u.status = :status ";
			$array[':status']		 = 0;
		}

		# GROUP
		if(!empty($post_data['group'])){
			$sql					.= " GROUP BY ".$post_data['group'];
		}

		# ORDER
		if(!empty($post_data['order'])){
			$sql					.= " ORDER BY ".$post_data['order'];
		}

		# LIMIT
		if(!empty($post_data['limit'])){
			$sql					.= " LIMIT ".$post_data['limit'];
		}

		$rtn						 = NULL;
		$result						 = NULL;

		# REMOVE TAGS
		$array						 = $database->removeTags($array);

		try{
			$rtn					 = $database->prepare($sql,$array,$debug=1);
	 		$result					 = $rtn->execute($array);
			if(empty($result)){ throw new Exception(); }
		}catch(Exception $e){
			if(defined("SYSTEM_CHECK")){
				$database->debug_query	.= print_r($e->getTrace());
				$database->debug_query	.= "\n<hr class=\"query_line\" />\n";
			}
		}

		# CHECK
		$list						 = array();
		$list						 = NULL;
		$list['end_list']			 = NULL;
		$list['end_id']				 = NULL;
		$list['end_name']			 = NULL;
		$list['remaining']			 = 0;
		$nowtime					 = date("YmdHis");
		$count						 = 0;

		$i=0;
		while($data = $database->fetchAssoc($rtn)){


			/**************************************
			**
			**	既に有効期限や使用回数が切れたものがあればここで処理
			**	(基本は使用時/mail/detail/内で行う)
			**
			***************************************/

			$end						= NULL;

			# 時間カウントで有効期限が切れててstatusが0のもの
			if($data['type'] == 1 && $data['limit_time'] < $nowtime && $data['itemuse_status'] == 0){

				$end					= 1;

			# 回数カウントで利用可能回数がゼロでstatusが0のもの
			}elseif($data['type'] == 2 && $data['limit_count'] == 0 && $data['itemuse_status'] == 0){

				$end					= 1;

			# 有効期限ありで有効期限が切れててstatusが0のもの
			}elseif($data['type'] == 3 && $data['limit_time'] < $nowtime && $data['itemuse_status'] == 0){

				$end					= 1;

			}

			# 使用できないものは status を 8 にアップデート
			if(!empty($end)){

				$itemuse_update['status']			= 8;

				# UPDATE WHERE
				$itemuse_update_where				= "id = :id";
				$itemuse_update_conditions[':id']	= $data['itemuse_id'];

				# 【UPDATE】 / itembox takai
				$database->updateDb($this->table,$itemuse_update,$itemuse_update_where,$itemuse_update_conditions);

				# 処理したものを配列に入れて返す
				$list['items_id'][$i]				= $data['items_id'];
				$list['itemuse_id'][$i]				= $data['itemuse_id'];

				# JSON用にカンマ区切りも作成
				$list['end_list']					.= $data['items_id'].",";
				$list['end_id']						.= $data['itemuse_id'].",";

				# アイテム名も入れる
				$list['end_name']					.= "【".$data['name']."】";

				$count++;

			}

			$i++;


		}

		# 終了処理した件数カウントも格納
		$list['count']								= $count;

		# 差分計算 (使えるアイテム残り数)
		if($i > 0){
			$list['remaining']						= $i - $count;
		}

		# 末尾のカンマを削除
		if(!empty($list['end_list'])){
			$list['end_list']						= substr_replace($list['end_list'],'', -1,1);
		}
		if(!empty($list['end_id'])){
			$list['end_id']							= substr_replace($list['end_id'],'', -1,1);
		}

		# DATABASE CHANGE
		if(!empty($db_check)){

			# CLOSE DATABASE MASTER
			$database->closeDb();

			# CONNECT DATABASE SLAVE
			$database->connectDb();

		}

		return $list;

	}



	/**************************************************
	**
	**	calculatePoint
	**	----------------------------------------------
	**	各種加算ポイントの乗算チェック・計算
	**	----------------------------------------------
	**
	**
	**************************************************/

	public function calculatePoint($itemuse_data,$items_data,$calculation_data){

		if(empty($itemuse_data['id']) || empty($items_data['id']) || empty($calculation_data)){
			return $calculation_data;
		}

		# DB / MAIN CLASS
		$database						= NULL;
		$database						= $this->database;
		$output	 						= NULL;
		$output	 						= $this->output;

		# 初期化
		$calculation					= NULL;
		$update							= NULL;
		$end							= NULL;
		$count_down						= NULL;

		# 現在時刻
		$nowtime						= date("YmdHis");


		/************************************************
		**
		**	type : 1
		**	===========================================
		**	時間適用 / $items_data['count'] が 時間カウントになる(minutes)
		**
		************************************************/

		if($items_data['type'] == 1){

			# 有効期限NG
			if($itemuse_data['limit_time'] < $nowtime){

				# まだstatusが0だったら8にupdateして処理抜け
				if($itemuse_data['status'] == 0){
					$update				= 1;
					$end				= 1;
				}

			# 有効期限OK
			}else{

				# 計算処理フラグ
				$calculation			= 1;

			}


		/************************************************
		**
		**	type : 2
		**	===========================================
		**	回数適用 / $items_data['count'] が 回数カウントになる
		**
		************************************************/

		}elseif($items_data['type'] == 2){

			# 利用回数NG
			if($itemuse_data['limit_count'] == 0){

				# まだstatusが0だったら8にupdateして処理抜け
				if($itemuse_data['status'] == 0){
					$update				= 1;
					$end				= 1;
				}

			# 利用回数OK
			}else{

				# 計算処理フラグ
				$calculation			= 1;

				# アップデートフラグ
				$update					= 1;

				# カウントダウンフラグ
				$count_down				= 1;

			}



		/************************************************
		**
		**	type : 3
		**	===========================================
		**	有効期限 / $items_data['end_date']を適用
		**
		************************************************/

		}elseif($items_data['type'] == 3){

			# 有効期限NG
			if($itemuse_data['limit_time'] < $nowtime){

				# まだstatusが0だったら8にupdateして処理抜け
				if($itemuse_data['status'] == 0){
					$update				= 1;
					$end				= 1;
				}

			}else{

				# 計算処理フラグ
				$calculation			= 1;

			}





		/************************************************
		**
		**	type : NULL
		**	===========================================
		**	その他はスルー
		**
		************************************************/

		}else{



		}



		/************************************************
		**
		**	計算OK
		**	===========================================
		**	itemsの効果の効果から各ポイント乗算
		**
		************************************************/

		if(!empty($calculation)){

			# 好感度アップ
			if($items_data['effect'] == 1){

				if($items_data['magnification'] > 0){

					$calculation_data['favorite']	= $calculation_data['favorite'] * $items_data['magnification'];

				}

			# 応援ポイントアップ
			}elseif($items_data['effect'] == 2){

					$calculation_data['ranking']	= $calculation_data['ranking'] * $items_data['magnification'];

			# その他はスルー
			}else{



			}


		}


		if(!empty($update)){

			# AUTHORITY
			$db_auth								 = $database->checkAuthority();
			$db_check								 = NULL;

			# DATABASE CHANGE
			if(empty($db_auth)){

				# CLOSE DATABASE SLAVE
				$database->closeDb();

				# CONNECT DATABASE MASTER
				$database->connectDb(MASTER_ACCESS_KEY);

				$db_check							 = 1;

			}


			/************************************************
			**
			**	使用アイテムが回数利用なら
			**	===========================================
			**	limit_countを
			**
			************************************************/

			if(!empty($count_down)){

				# 回数処理の場合はここでカウントを減らす
				$limit_count						= $itemuse_data['limit_count'] - 1;

				# 残り回数が0になったら
				if($limit_count == 0){
					$end							= 1;
				}

				# UPDATE itemuse
				$itemuse_update['limit_count']		= $limit_count;

			}


			/************************************************
			**
			**	使用アイテムの利用制限を越えたら
			**	===========================================
			**	itemuse : status を 8 へアップデート
			**
			************************************************/

			if(!empty($end)){

				# UPDATE itemuse
				$itemuse_update['status']			= 8;

				# 使い切ったアイテムデータ渡す
				$calculation_data['end']			= $itemuse_data['item_id'];
				$calculation_data['end_id']			= $itemuse_data['id'];

			}


			$itemuse_update_where				= "id = :id";
			$itemuse_update_conditions[':id']	= $itemuse_data['id'];

			# 【UPDATE】 / itembox
			$database->updateDb($this->table,$itemuse_update,$itemuse_update_where,$itemuse_update_conditions);



			# DATABASE CHANGE
			if(!empty($db_check)){

				# CLOSE DATABASE MASTER
				$database->closeDb();

				# CONNECT DATABASE SLAVE
				$database->connectDb();

			}

		}

		return $calculation_data;

	}


	/**************************************************
	**
	**	getItemDataByIdFromThisModel
	**	----------------------------------------------
	**	itemsテーブルから アイテム取得
	**
	**************************************************/

	public function getItemDataFromThisModel($post_data,$column=NULL){

		if(empty($column)){
			$column				 = "*";
		}

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		# PARAMETER
		$array					 = array();
		$array[':site_cd']		 = SITE_CD;

		# DB / MAIN CLASS
		$database				 = NULL;
		$database				 = $this->database;
		$output					 = NULL;
		$output					 = $this->output;

		$where					 = "site_cd = :site_cd ";

		if(isset($post_data['item_id'])){
			$where				.= "AND item_id = :item_id ";
			$array[':item_id']	 = $post_data['item_id'];
		}else{
			$where				.= "AND item_id = :item_id ";
			$array[':item_id']	 = 0;
		}

		if(isset($post_data['category'])){
			$where				.= "AND category = :category ";
			$array[':category']	 = $post_data['category'];
		}

		if(isset($post_data['campaign_id'])){
			$where				.= "AND campaign_id = :campaign ";
			$array[':campaign']	 = $post_data['campaign_id'];
		}else{
			$where				.= "AND campaign_id = :campaign ";
			$array[':campaign']	 = 0;
		}

		if(isset($post_data['status'])){
			$where				.= "AND status = :status ";
			$array[':status']	 = $post_data['status'];
		}else{
			$where				.= "AND status = :status ";
			$array[':status']	 = 0;
		}

		$order					 = NULL;
		$limit					 = 1;
		$group					 = NULL;

		if(!empty($post_data['order'])){
			$order				 = $post_data['order'];
		}

		if(!empty($post_data['group'])){
			$group				 = $post_data['group'];
		}

		$rtn					 = $database->selectDb("items",$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getItemDataByIdFromThisModel",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$data					= $database->fetchAssoc($rtn);

		$database->freeResult($rtn);

		return $data;


	}



	public function getLimitTime($start_date,$end_date){

		if(empty($end_date)){
			return FALSE;
		}

		if(empty($start_date)){
			$start_date		= date("YmdHis");
		}


		$start_date			= strtotime($start_date);
		$end_date			= strtotime($end_date);

		$time				= ($end_date - $start_date);

		$hour				= (int)($time /3600);
		$minutes			= (int)($time % 3600 / 60);

		if($hour > 0){
			$hour_minutes	= $hour * 60;
		}else{
			$hour_minutes	= 0;
		}


		$result				= $minutes + $hour_minutes;

		return $result;

	}


}

?>