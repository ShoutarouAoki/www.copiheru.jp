<?php
################################ FILE MANAGEMENT ################################
##
##	mainController.php
##	=============================================================================
##
##	■PAGE / 
##	APP WEB
##	MAIN PAGE SCRIPT
##
##	=============================================================================
##
##	■MEANS / 
##	MAIN PAGE 各種処理
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

/** MAILUSER MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/MailUserModel.php");

/** ATTACHE MODEL **/
require_once(DOCUMENT_ROOT_MODELS."/AttacheModel.php");

/** EMOJI PLUGINS **/
require_once(DOCUMENT_SYSTEM_PLUGINS."/Emoji/lib/mobile_class_8.php");


################################# POST ARRAY ####################################

$value_array				= array('page','id');
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

# MAILUSER MODEL
$mailuserModel				= new MailUserModel($database,$mainClass);

# ATTACHE MODEL
$attacheModel				= new AttacheModel($database,$mainClass);


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

	# INDEX
	if($data['page'] == "index"){

		/************************************************
		**
		**	新着未読メール取得
		**	============================================
		**
		**	getMailList
		**	直近の未読メールのリスト取得
		**
		**	============================================
		**
		**	$post_data['type'] : 1
		**	ユーザーが受け取り
		**
		**	$post_data['type'] : 2
		**	キャラが受け取り
		**
		**
		**
		**
		************************************************/

		$mail_array			= array();
		$mail_array			= array(
			'user_id'		=> $member_data['id'],
			'recv_flg'		=> 1,
			'order'			=> 'send_date DESC',
			'limit'			=> 5,
			'group'			=> NULL,
			'type'			=> 1 // ユーザー受け取り
		);
		$mail_column		= "id,send_id,recv_id,title,recv_flg,age,send_date,media_flg";
		$mail_rtn			= $mailModel->getMailList($mail_array,$mail_column);

		$i=0;
		while($mail_data = $database->fetchAssoc($mail_rtn)){


			# CHARA DATA 取得
			$chara_data						= $memberModel->getMemberById($mail_data['send_id'],"id,nickname,age,pref,city,chikuwa,status");

			# MAIL USER DATA
			$mailuser_array					= array();
			$mailuser_array					= array('user_id' => $member_data['id'],'chara_id' => $mail_data['send_id'],'status' => 0);
			$mailuser_data					= $mailuserModel->getMailUserData($mailuser_array,"id,virtual_age,virtual_name");

			# サムネ画像取得
			$attache_array					= array();
			$attache_array					= array(
				'user_id'					=> $mail_data['send_id'],
				'category'					=> 1,
				'use_flg'					=> 1,
				'pay_count'					=> 0,
				'status'					=> 1,
				'order'						=> 'pay_count,reg_date DESC',
				'limit'						=> 1,
				'group'						=> NULL
			);
			$attache_data					= $attacheModel->getAttacheData($attache_array);

			# 絵文字セット タイトル
			$display_title					= $emoji_obj->emj_decode($mail_data['title']);

			# 絵文字セット ネーム
			if(!empty($mailuser_data['virtual_name'])){
				$display_name				= $emoji_obj->emj_decode($mailuser_data['virtual_name']);
			}else{
				$display_name				= $emoji_obj->emj_decode($chara_data['nickname']);
			}


			# 結果セット
			$mail_list['id'][$i]			= $mail_data['id'];
			$mail_list['title'][$i]			= $display_title['web'];
			$mail_list['name'][$i]			= $display_name['web'];
			$mail_list['recv_flg'][$i]		= $mail_data['recv_flg'];
			$mail_list['send_date'][$i]		= date("Y年m月d日 H時i分",strtotime($mail_data['send_date']));

			# サムネイル画像
			if(!empty($attache_data)){
				$mail_list['image'][$i]		= $attache_data['attached'];
			}

			# 年齢セット
			if($mailuser_data['virtual_age'] > 0){
				$mail_list['age'][$i]		= $mailuser_data['virtual_age'];
			}else{
				$mail_list['age'][$i]		= $mail_data['age'];
			}

            # 添付系セット
            if ($mail_data['media_flg'] == 1 || $mail_data['media_flg'] == 3) {
                $mail_list['media'][$i]		= "画像アリ";
            } else if($mail_data['media_flg'] == 2 || $mail_data['media_flg'] == 4) {
                $mail_list['media'][$i]		= "動画アリ";
            }

			$i++;

		}

		$database->freeResult($mail_rtn,1);












		# MEMBER DATA
		$mainClass->debug($member_data);




	}

}


################################## FILE END #####################################
?>