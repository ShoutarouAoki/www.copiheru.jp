<?php
################################ FILE MANAGEMENT ################################
##
##	pcComponent.php
##	=============================================================================
##
##	■PAGE / 
##	MAG OFFICIAL ADMIN
##	COMPONENT SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	PC用 コンポーネント 常時読み込みファイル
##
##	=============================================================================
##
##	■ CHECK / 
##	AUTHOR		: AKITOSHI TAKAI
##	CREATE DATE : 2012/12/01
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
#################################################################################


if(!empty($members_data['id'])){

	/************************************************
	**
	**	所持アイテム
	**	===========================================
	**	ItemBox
	**	===========================================
	**	itemsとJOINして所持アイテム情報取得
	**
	************************************************/

	$user_item_list										= NULL;
	$user_item_list										= array();

	$user_itembox_conditions							= array(
		'user_id'										=> $members_data['id'],
		'status'										=> 0,
		'order'											=> 'i.name'
	);
	$user_itembox_rtn									= $itemboxModel->getItemboxListJoinOnItems($user_itembox_conditions);

	$i=0;
	$j=0;
	while($user_itembox_data = $database->fetchAssoc($user_itembox_rtn)){

		# 残り数ゼロで使用中データもなければ非表示
		if($user_itembox_data['unit'] == 0){
			continue;
		}

		$user_item_list['id'][$i]						= $user_itembox_data['itembox_id'];
		$user_item_list['unit'][$i]						= $user_itembox_data['unit'];

		if(!empty($user_itembox_data['name'])){
			$user_item_list['name'][$i]					= $user_itembox_data['name'];
		}

		if(!empty($user_itembox_data['image'])){
			$user_item_list['image'][$i]				= $user_itembox_data['image'];
		}

		if(!empty($user_itembox_data['description'])){
			$user_item_list['description'][$i]			= $user_itembox_data['description'];
		}

		$i++;

	}

	$database->freeResult($user_itembox_rtn);

}



################################## FILE END #####################################
?>