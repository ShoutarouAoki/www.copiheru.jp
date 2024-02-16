<?
/********************************************************
**
**	members.php
**	-----------------------------------------------------
**	ユーザー情報に関するCLASS
**	-----------------------------------------------------
**	2010.03.03 kuma
*********************************************************/

class members{

	/*********************************************
	**
	**	MEMBERS USER DATA 取得
	**	------------------------------------------
	**	members / userinfosを一括SELECT
	**
	*********************************************/

	function getUser($member_column='',$member_where,$info_column='',$user_order=NULL){

		global $db;

		if(!$member_where){
			return('error');
		}

		# ユーザーデータ
		$user_table		= "members";
		# REQUEST
		if($member_column){
		$user_select	= $member_column;
		# DEFAULT
		}else{
		$user_select	= "id,nickname,sex,reg_date,site_cd,op_id,chikuwa,owner_id,status,domain_flg,pay_count";
		}
		$user_where		= $member_where;
		$user_limit		= "1";
		$user_rtn		= $db->selectDb($user_table,$user_select,$user_where,$user_order,$user_limit);
		$db->errorDb("",$db->errno,__FILE__,__LINE__);

		$user_num  		= $db->numRows($user_rtn);

		if($user_num != 0){

			$user_data		= $db->fetchAssoc($user_rtn);

			# 取得していれば
			if($user_data['site_cd'] && $user_data['id']){

				# ユーザーインフォ取得
				$info_table		= "userinfos";
				# REQUEST
				if($info_column){
				$info_select	= $info_column;
				# DEFAULT
				}else{
				$info_select	= "comment";
				}
				$info_where		= "site_cd = ".$user_data['site_cd']." AND user_id = ".$user_data['id'];
				$info_limit		= "1";
				$info_rtn		= $db->selectDb($info_table,$info_select,$info_where,$info_order,$info_limit);
				$db->errorDb("",$db->errno,__FILE__,__LINE__);

				$info_data		= $db->fetchAssoc($info_rtn);

				# USER INFOをUSER DATAに格納
				$user_data['info_id']	= $info_data['id'];
				$user_data['title']		= $info_data['title'];
				$user_data['message']	= $info_data['message'];
				$user_data['comment']	= $info_data['comment'];
				$user_data['parameter'] = $info_data['parameter'];

			}

		}else{
			$user_data	= "";
		}

		return($user_data);

	}

	/*********************************************
	** 
	**	ATTACHES CSV登録インサート処理
	**
	*********************************************/

	function csvAttaches($attach_data){

		global $db;

		/* attachesの処理 */

		foreach( $attach_data as $acols => $avalue ){
			$ins_a .= $acols." = '".$avalue."',";
		}

		$insert  = "INSERT INTO attaches SET ";
		$insert .= $ins_a;
		$insert .= "reg_date = NOW()";

		$rtn     = $db->query($insert);

		$db->errorDb($insert,$db->errno,__FILE__,__LINE__);

		$ins_id  = mysql_insert_id();

		return($ins_id);

	}



	/*********************************************
	**
	**	MEMBERS ACCESS 更新 処理
	**
	*********************************************/

	function getUserData($user_id,$column=NULL){

		global $db;

		if($user_id == ''){
			return("error");
		}

		if(!$column){
			$column	= "*";
		}

		$sql	= "SELECT ".$column." FROM members WHERE id = ".$user_id;
		$rtn	= $db->query($sql);
		$db->errorDb("",$db->errno,__FILE__,__LINE__);
		$data = $db->fetchAssoc($rtn);

		return $data;

	}


}

?>
