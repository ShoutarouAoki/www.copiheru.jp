<?
/********************************************************
**
**	points.php
**	-----------------------------------------------------
**	pointsに関するCLASS
**	-----------------------------------------------------
**	2010.06.10 takai
*********************************************************/


class points
{


	/*********************************************
	**
	**	USER ポイント消費処理
	**
	*********************************************/

	function userPointUse($site_cd,$use_point,$point_no_id,$user_data){

		global $db;

		if(!$site_cd || !$point_no_id || !$user_data){
			return FALSE;
		}

		if(!$use_point || $use_point == ""){
			$use_point	= 0;
		}

		# 入金ポイントOK
		if($user_data['point'] >= $use_point){

			# MEMBERS POINT UPDATE
			$update_point			= $user_data['point'] - $use_point;
			$update_data['point']	= $update_point;

			# INSERT POINTS
			$insert_data['site_cd']		= $site_cd;
			$insert_data['point']		= $use_point;
			$insert_data['point_no_id']	= $point_no_id;
			$insert_data['user_id']		= $user_data['user_id'];
			$insert_data['domain_flg']	= $user_data['domain_flg'];
			$insert_data['sex']			= $user_data['sex'];
			$insert_data['ad_code']		= $user_data['ad_code'];
			$insert_data['pay_flg']		= $user_data['pay_flg'];
			$insert_data['point_type']	= 0;
			$result	= $this->insertPoint($insert_data);

			if(!$result){ return FALSE; }

		# 入金ポイント不足
		}else{

			# 差分
			$minus_point	 = $use_point - $user_data['point'];

			# 配布ポイント処理
			if($user_data['s_point'] < $minus_point){
				return FALSE;
			}else{
				$update_point			= $user_data['s_point'] - $minus_point;
				$update_data['point']	= '0';
				$update_data['s_point']	= $update_point;
			}

			# INSERT POINTS -> NORMAL POINT
			$insert_data['site_cd']		= $site_cd;
			$insert_data['point']		= $user_data['point'];
			$insert_data['point_no_id']	= $point_no_id;
			$insert_data['user_id']		= $user_data['user_id'];
			$insert_data['domain_flg']	= $user_data['domain_flg'];
			$insert_data['sex']			= $user_data['sex'];
			$insert_data['ad_code']		= $user_data['ad_code'];
			$insert_data['pay_flg']		= $user_data['pay_flg'];
			$insert_data['point_type']	= 0;
			$result	= $this->insertPoint($insert_data);
			if(!$result){ return FALSE; }

			# INSERT POINTS -> SERVICE POINT
			$minus_data['site_cd']		= $site_cd;
			$minus_data['point']		= $minus_point;
			$minus_data['point_no_id']	= $point_no_id;
			$minus_data['user_id']		= $user_data['user_id'];
			$minus_data['domain_flg']	= $user_data['domain_flg'];
			$minus_data['sex']			= $user_data['sex'];
			$minus_data['ad_code']		= $user_data['ad_code'];
			$minus_data['pay_flg']		= $user_data['pay_flg'];
			$minus_data['point_type']	= 1;
			$result	= $this->insertPoint($minus_data);
			if(!$result){ return FALSE; }

		}

		$where	 = "site_cd = ".$site_cd." ";
		$where	.= "AND id = ".$user_data['user_id'];

		$db->updateDb("members",$update_data,$where);
		$db->errorDb("",$db->errno,___,__LINE__);
		if($db_err){ print($db_err); exit; }


		return TRUE;


	}



	/*********************************************
	**
	**	ポイントINSERT
	**
	*********************************************/

	function insertPoint($data){

		global $db;

		foreach( $data as $cols => $value ){
			$ins_data .= $cols." = '".$value."',";
		}

		$insert  = "INSERT INTO points SET ";
		$insert .= $ins_data;
		$insert .= "log_date = '".date('YmdHis')."'";

		$rtn	 = $db->query($insert);

		$db->errorDb($insert,$db->errno,__FILE__,__LINE__);
		$ins_id  = mysql_insert_id();

		return $ins_id;

	}


}

?>
