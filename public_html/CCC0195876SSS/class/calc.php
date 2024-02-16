<?
/********************************************************
**
**	calc.php
**	-----------------------------------------------------
**	集計に関するCLASS
**	-----------------------------------------------------
**	2010.03.19 kuma
*********************************************************/

require_once(dirname(__FILE__).'/../class/siteinfos.php');

/* CONF FILE */
require_once(dirname(__FILE__).'/../CONF/config.php');

class calculation{

	var $pt_table = 'points';
	var $pt_colum = array('id','user_id','site_cd','sex','point','point_no_id','campaign_id','log_date','chara_id','op_id','owner_id','domain_flg','pay_flg');

	var $pay_table = 'pays';
	var $pay_colum = array('id','user_id','sex','site_cd','ad_code','reg_date','pay_date','pay_amount','fix_flg','def_flg','def_date','settlement_id','store','sid','kid','tel_num','clear','limit_date','status','domain_flg','def_amount');

	var $user_table = 'members';
	var $user_colum = array('id','sex','pay_count','mail_flg','status','entry_date','reg_date','site_cd','device','pay_amount','op_id','domain_flg');

	var $promo_table = 'promotions';
	var $promo_colum = array('id','site_cd','ad_code','reg_date','m_entry','m_reg','f_entry','f_reg','page_cd','view_count1','view_count2','view_count3','view_count4');

	# 削りポイント取得
	function getConsumption($date1,$date2,$site_cd,$domain_flg=NULL,$op_id=NULL){

		global $db;

		$select = $this->pt_colum[1].','.$this->pt_colum[3].','.$this->pt_colum[4].','.$this->pt_colum[12];
		$where  = $this->pt_colum[5].' > 20 AND '.$this->pt_colum[7].' >= '.$date1.' AND '.$this->pt_colum[7].' <= '.$date2.' ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->pt_colum[11].' = '.$domain_flg.' ';
		}
		if($op_id != ""){
			$where	.= "AND op_id in (".$op_id.") ";
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->pt_colum[2].' = '.$site_cd;
		}
		$rtn    = $db->selectDb($this->pt_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		return($rtn);

	}

	# 削りポイントSUM
	function SumConsumption($date1,$date2,$site_cd,$op_id='',$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL,$op_group=NULL){

		global $db;

		$select = $this->pt_colum[1].','.$this->pt_colum[4];
		$where  = $this->pt_colum[7].' >= '.$date1.' AND '.$this->pt_colum[7].' <= '.$date2.' ';
		$where.=  'AND '.$this->pt_colum[5].' > 20 ';

		if($domain_flg != ""){
			$where .= 'AND '.$this->pt_colum[11].' = '.$domain_flg.' ';
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->pt_colum[2].' = '.$site_cd.' ';
		}
		if($op_id != '' && !$op_group){
			$where .= 'AND op_id = '.$op_id;
		}elseif($op_id != '' && $op_group){
			$where .= "AND op_id in (".$op_id.") ";
		}

		$rtn    = $db->selectDb($this->pt_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);
		while($data = $db->fetchAssoc($rtn)){

			if($ad_code_type){

				$chk_table  = "members";
				$chk_select = "id";
				$chk_where  = "id = ".$data['user_id']." ";
				# アドコード
				if($ad_code_type == '1'){
					$chk_where	.= "AND ad_code = '".$ad_code."' ";
				}elseif($ad_code_type == '2'){
					$chk_where	.= "AND ad_code like '".$ad_code."%' ";
				}elseif($ad_code_type == '3'){
					$chk_where	.= "AND ad_code like '%".$ad_code."%' ";
				}elseif($ad_code_type == '4'){
					$chk_where	.= "AND ad_code like '%".$ad_code."' ";
				}elseif($ad_code_type == '5'){
					$chk_where	.= "AND ad_code != '".$ad_code."' ";
				}elseif($ad_code_type == '6'){
					$chk_where	.= "AND ad_code not like '".$ad_code."%' ";
				}elseif($ad_code_type == '7'){
					$chk_where	.= "AND ad_code not like '%".$ad_code."%' ";
				}elseif($ad_code_type == '8'){
					$chk_where	.= "AND ad_code not like '%".$ad_code."' ";
				}

				$chk_rtn    = $db->selectDb($chk_table,$chk_select,$chk_where,"","");
				$db->errorDb("",$db->errno,__FILE__,__LINE__);
				$chk_rows   = $db->numRows($chk_rtn);

				if($chk_rows == 0){
					continue;
				}

			}

			$sum_pt += $data['point'];

		}

		$db->free_result($rtn);

		return($sum_pt);

		/*
		$select = 'SUM('.$this->pt_colum[4].') AS sum_pt';
		$where  = $this->pt_colum[5].' > 20 AND '.$this->pt_colum[7].' >= '.$date1.' AND '.$this->pt_colum[7].' <= '.$date2.' ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->pt_colum[11].' = '.$domain_flg.' ';
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->pt_colum[2].' = '.$site_cd.' ';
		}
		if($op_id != ''){
			$where .= 'AND op_id = '.$op_id;
		}
		$rtn    = $db->selectDb($this->pt_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);
		$data = $db->fetchAssoc($rtn);

		$db->free_result($rtn);

		return($data['sum_pt']);
		*/

	}

	# 登録数
	function getRegistNum($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		# 追加
		$registnum['m_entry'] = 0;
		$registnum['f_entry'] = 0;
		$registnum['n_entry'] = 0;
		$registnum['m_all'] = 0;
		$registnum['m_reg1'] = 0;
		$registnum['m_reg2'] = 0;
		$registnum['m_reg3'] = 0;
		$registnum['f_all'] = 0;
		$registnum['f_reg1'] = 0;
		$registnum['f_reg2'] = 0;
		$registnum['f_reg3'] = 0;
		$registnum['n_all'] = 0;
		$registnum['n_reg'] = 0;
		$registnum['n_reg1'] = 0;
		$registnum['n_reg2'] = 0;
		$registnum['n_reg3'] = 0;
		$registnum['nomail_pay'] = 0;
		$registnum['nomail_nopay'] = 0;
		$registnum['nomail_all'] = 0;
		$registnum['sum_all'] = 0;
		$registnum['reg_all'] = 0;
		$registnum['m_ratio'] = 0;
		$registnum['f_ratio'] = 0;
		$registnum['n_ratio'] = 0;
		$registnum['ratio_all'] = 0;
		$registnum['m_pc'] = 0;
		$registnum['m_mo'] = 0;
		$registnum['f_pc'] = 0;
		$registnum['f_mo'] = 0;
		$registnum['n_pc'] = 0;
		$registnum['n_mo'] = 0;

		$select = $this->user_colum[1].','.$this->user_colum[2].','.$this->user_colum[3].','.$this->user_colum[4].','.$this->user_colum[8];
		$where  = $this->user_colum[10].' = 0 AND '.$this->user_colum[5].' >= '.$date1.' AND '.$this->user_colum[5].' <= '.$date2.' ';

		if($domain_flg != ""){
			$where .= 'AND '.$this->user_colum[11].' = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}


		if($site_cd != 'all'){
			$where .= 'AND '.$this->user_colum[7].' = '.$site_cd;
		}
		$rtn    = $db->selectDb($this->user_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		#print("SELECT ".$select." FROM ".$this->user_table." WHERE ".$where."<br>");



		while($data = $db->fetchAssoc($rtn)){

			if($data[$this->user_colum[1]] == 1){
				$registnum['m_all']++;
				if($data[$this->user_colum[4]] == 1){
					$registnum['m_reg1']++;
				}
				if($data[$this->user_colum[4]] == 2){
					$registnum['m_reg2']++;
				}
				if($data[$this->user_colum[4]] == 3){
					$registnum['m_reg3']++;
				}

				$registnum['m_entry']++;

			}elseif($data[$this->user_colum[1]] == 2){
				$registnum['f_all']++;
				if($data[$this->user_colum[4]] == 1){
					$registnum['f_reg1']++;
				}
				if($data[$this->user_colum[4]] == 2){
					$registnum['f_reg2']++;
				}
				if($data[$this->user_colum[4]] == 3){
					$registnum['f_reg3']++;
				}

				$registnum['f_entry']++;

			}elseif($data[$this->user_colum[1]] == 3){
				$registnum['n_all']++;
				if($data[$this->user_colum[4]] == 1){
					$registnum['n_reg1']++;
				}
				if($data[$this->user_colum[4]] == 2){
					$registnum['n_reg2']++;
				}
				if($data[$this->user_colum[4]] == 3){
					$registnum['n_reg3']++;
				}

				$registnum['n_entry']++;

			}

			if($data[$this->user_colum[3]] == 4 || $data[$this->user_colum[3]] == 5 || $data[$this->user_colum[3]] == 9){
				if($data[$this->user_colum[2]] != 0){
					$registnum['nomail_pay']++;
				}else{
					$registnum['nomail_nopay']++;
				}
				$registnum['nomail_all']++;
			}

		}

		$registnum['m_reg']	= $registnum['m_reg1'] + $registnum['m_reg2'] + $registnum['m_reg3'];
		$registnum['f_reg']	= $registnum['f_reg1'] + $registnum['f_reg2'] + $registnum['f_reg3'];
		$registnum['n_reg']	= $registnum['n_reg1'] + $registnum['n_reg2'] + $registnum['n_reg3'];

		$registnum['sum_all']	= $registnum['m_all'] + $registnum['f_all'] + $registnum['n_all'];
		$registnum['reg_all']	= $registnum['m_reg'] + $registnum['f_reg'] + $registnum['n_reg'];
		$registnum['entry_all']	= $registnum['m_entry'] + $registnum['f_entry'] + $registnum['n_entry'];

		# 登録率
		if($registnum['m_all'] > 0 ){ $registnum['m_ratio'] = 100*$registnum['m_reg']/$registnum['m_all']; }
		if($registnum['f_all'] > 0 ){ $registnum['f_ratio'] = 100*$registnum['f_reg']/$registnum['f_all']; }
		if($registnum['n_all'] > 0 ){ $registnum['n_ratio'] = 100*$registnum['n_reg']/$registnum['n_all']; }
		if($registnum['sum_all'] > 0 ){ $registnum['ratio_all'] = 100*$registnum['reg_all']/$registnum['sum_all']; }

		(int)$registnum['m_ratio'];
		(int)$registnum['f_ratio'];
		(int)$registnum['n_ratio'];
		(int)$registnum['ratio_all'];

		$registnum['m_ratio']	= round($registnum['m_ratio']);
		$registnum['f_ratio']	= round($registnum['f_ratio']);
		$registnum['n_ratio']	= round($registnum['n_ratio']);
		$registnum['ratio_all']	= round($registnum['ratio_all']);

		return($registnum);

	}

	# 本登録ユーザ デバイス別人数
	function getPayuserNum($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL,$csv=NULL){

		global $db;

		$payuser['m_pc'] = 0;
		$payuser['m_mo'] = 0;
		$payuser['f_pc'] = 0;
		$payuser['f_mo'] = 0;
		$payuser['n_pc'] = 0;
		$payuser['n_mo'] = 0;
		$payuser['m_pc_pay'] = 0;
		$payuser['m_mo_pay'] = 0;
		$payuser['f_pc_pay'] = 0;
		$payuser['f_mo_pay'] = 0;
		$payuser['n_pc_pay'] = 0;
		$payuser['n_mo_pay'] = 0;
		$payuser['m_pc_amount'] = 0;
		$payuser['m_mo_amount'] = 0;
		$payuser['f_pc_amount'] = 0;
		$payuser['f_mo_amount'] = 0;
		$payuser['n_pc_amount'] = 0;
		$payuser['n_mo_amount'] = 0;

		$select  = $this->user_colum[0].','.$this->user_colum[1].','.$this->user_colum[8].','.$this->user_colum[9].','.$this->user_colum[2];
		$where   = $this->user_colum[6].' >= '.$date1.' AND '.$this->user_colum[6].' <= '.$date2.' ';
		$where  .= 'AND '.$this->user_colum[4].' >= 1 AND '.$this->user_colum[4].' <= 7 AND '.$this->user_colum[10].' = 0 ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->user_colum[11].' = '.$domain_flg.' ';
		}
		if($csv != ""){
			$where	.= "AND csv = 0 ";
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->user_colum[7].' = '.$site_cd.' ';
		}

		$rtn    = $db->selectDb($this->user_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);


		while($data = $db->fetchAssoc($rtn)){

			if($data[$this->user_colum[8]] == 4 && $data[$this->user_colum[1]] == 1){
				$payuser['m_pc']++;
				if($data[$this->user_colum[2]] > 0){
					$payuser['m_pc_pay']++;
					$payuser['m_pc_amount'] += $data[$this->user_colum[9]];
				}
			}elseif($data[$this->user_colum[8]] != 4 && $data[$this->user_colum[1]] == 1){
				$payuser['m_mo']++;
				if($data[$this->user_colum[2]] > 0){
					$payuser['m_mo_pay']++;
					$payuser['m_mo_amount'] += $data[$this->user_colum[9]];
				}
			}elseif($data[$this->user_colum[8]] == 4 && $data[$this->user_colum[1]] == 2){
				$payuser['f_pc']++;
				if($data[$this->user_colum[2]] > 0){
					$payuser['f_pc_pay']++;
					$payuser['f_pc_amount'] += $data[$this->user_colum[9]];
				}
			}elseif($data[$this->user_colum[8]] != 4 && $data[$this->user_colum[1]] == 2){
				$payuser['f_mo']++;
				if($data[$this->user_colum[2]] > 0){
					$payuser['f_mo_pay']++;
					$payuser['f_mo_amount'] += $data[$this->user_colum[9]];
				}
			}elseif($data[$this->user_colum[8]] == 4 && $data[$this->user_colum[1]] == 3){
				$payuser['n_pc']++;
				if($data[$this->user_colum[2]] > 0){
					$payuser['n_pc_pay']++;
					$payuser['n_pc_amount'] += $data[$this->user_colum[9]];
				}
			}elseif($data[$this->user_colum[8]] != 4 && $data[$this->user_colum[1]] == 3){
				$payuser['n_mo']++;
				if($data[$this->user_colum[2]] > 0){
					$payuser['n_mo_pay']++;
					$payuser['n_mo_amount'] += $data[$this->user_colum[9]];
				}
			}

		}

		return($payuser);

	}



	# ROW PT 生き残り
	function getNewuserNum($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL,$point){

		global $db;

		$select  = "id,point,s_point";
		$where   = "reg_date >= '".$date1."' AND reg_date <= '".$date2."' ";
		$where  .= 'AND '.$this->user_colum[4].' >= 1 AND '.$this->user_colum[4].' <= 7 AND '.$this->user_colum[10].' = 0 ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->user_colum[11].' = '.$domain_flg.' ';
		}


		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->user_colum[7].' = '.$site_cd.' ';
		}

		$rtn    = $db->selectDb($this->user_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		$i=0;
		$ii=0;
		while($data = $db->fetchAssoc($rtn)){

			$check_point	= $data['point']+$data['s_point'];

			if($check_point <= $point){
				$i++;
			}

			//check unique send
			$uni_select	= "COUNT(id) AS count";
			$uni_where	= "send_id = ".$data['id'];
			$uni_rtn    = $db->selectDb("mails",$uni_select,$uni_where,'','');
			$db->errorDb('',$db->errno,__FILE__,__LINE__);
			$uni_data	= $db->fetchAssoc($uni_rtn);

			if($uni_data['count'] != "0"){
				$ii++;
			}

		}

		$result['low']		= $i;
		$result['unique']	= $ii;

		return($result);

	}



	# ROW PT 生き残り
	function getUserAccess($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		$select  = "id,sex,pay_count,point,s_point";
		$where   = "access_date >= '".$date1."' AND access_date <= '".$date2."' ";
		$where  .= 'AND '.$this->user_colum[4].' >= 1 AND '.$this->user_colum[4].' <= 7 AND '.$this->user_colum[10].' = 0 ';

		if($domain_flg != ""){
			$where .= 'AND '.$this->user_colum[11].' = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->user_colum[7].' = '.$site_cd.' ';
		}

		$rtn    = $db->selectDb($this->user_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		$result['m_count']=0;
		$result['n_count']=0;
		$result['f_count']=0;
		$result['m_count_no']=0;
		$result['n_count_no']=0;
		$result['f_count_no']=0;
		$result['m_point']=0;
		$result['n_point']=0;
		$result['f_point']=0;
		$result['m_point_no']=0;
		$result['n_point_no']=0;
		$result['f_point_no']=0;
		while($data = $db->fetchAssoc($rtn)){

			$user_point	= $data['point']+$data['s_point'];

			# 入金有り
			if($data['pay_count'] > 0){

				if($data['sex'] == 1){
					$result['m_count']++;
					$result['m_point']	+= $user_point;
				}elseif($data['sex'] == 2){
					$result['f_count']++;
					$result['f_point']	+= $user_point;
				}elseif($data['sex'] == 3){
					$result['n_count']++;
					$result['n_point']	+= $user_point;
				}

			# 入金無し
			}else{

				if($data['sex'] == 1){
					$result['m_count_no']++;
					$result['m_point_no']	+= $user_point;
				}elseif($data['sex'] == 2){
					$result['f_count_no']++;
					$result['f_point_no']	+= $user_point;
				}elseif($data['sex'] == 3){
					$result['n_count_no']++;
					$result['n_point_no']	+= $user_point;
				}

			}

		}

		$result['total_count']		= $result['m_count']+$result['f_count']+$result['n_count'];
		$result['total_count_no']	= $result['m_count_no']+$result['f_count_no']+$result['n_count_no'];
		$result['total_point']		= $result['m_point']+$result['f_point']+$result['n_point'];
		$result['total_point_no']	= $result['m_point_no']+$result['f_point_no']+$result['n_point_no'];

		# 入有り平均PT
		if($result['m_count'] == 0){
			$result['m_int']	= 0;
		}else{
			$result['m_int']	= $result['m_point']/$result['m_count'];
			$result['m_int']	= round($result['m_int'],1);
		}

		if($result['f_count'] == 0){
			$result['f_int']	= 0;
		}else{
			$result['f_int']	= $result['f_point']/$result['f_count'];
			$result['f_int']	= round($result['f_int'],1);
		}

		if($result['n_count'] == 0){
			$result['n_int']	= 0;
		}else{
			$result['n_int']	= $result['n_point']/$result['n_count'];
			$result['n_int']	= round($result['n_int'],1);
		}

		if($result['total_count'] == 0){
			$result['total_int']	= 0;
		}else{
			$result['total_int']	= $result['total_point']/$result['total_count'];
			$result['total_int']	= round($result['total_int'],1);
		}

		# 入無し平均PT
		if($result['m_count_no'] == 0){
			$result['m_int_no']	= 0;
		}else{
			$result['m_int_no']	= $result['m_point_no']/$result['m_count_no'];
			$result['m_int_no']	= round($result['m_int_no'],1);
		}

		if($result['f_count_no'] == 0){
			$result['f_int_no']	= 0;
		}else{
			$result['f_int_no']	= $result['f_point_no']/$result['f_count_no'];
			$result['f_int_no']	= round($result['f_int_no'],1);
		}

		if($result['n_count_no'] == 0){
			$result['n_int_no']	= 0;
		}else{
			$result['n_int_no']	= $result['n_point_no']/$result['n_count_no'];
			$result['n_int_no']	= round($result['n_int_no'],1);
		}

		if($result['total_count_no'] == 0){
			$result['total_int_no']	= 0;
		}else{
			$result['total_int_no']	= $result['total_point_no']/$result['total_count_no'];
			$result['total_int_no']	= round($result['total_int_no'],1);
		}

		return($result);

	}



	# 入金 TAKAI 取り急ぎ作り直し 2011/02/15
	function getPaydata($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		$paydata['m_bank'] = 0; $paydata['m_cre'] = 0; $paydata['m_bit'] = 0; $paydata['m_direct'] = 0;
		$paydata['m_ccheck'] = 0; $paydata['m_fregi'] = 0; $paydata['m_netbank'] = 0; $paydata['m_edy'] = 0;
		$paydata['m_gmoney'] = 0; $paydata['m_smoney'] = 0; $paydata['m_giga'] = 0; $paydata['m_convenic'] = 0;
		$paydata['f_bank'] = 0; $paydata['f_cre'] = 0; $paydata['f_bit'] = 0; $paydata['f_direct'] = 0;
		$paydata['f_ccheck'] = 0; $paydata['f_fregi'] = 0; $paydata['f_netbank'] = 0; $paydata['f_edy'] = 0;
		$paydata['f_gmoney'] = 0; $paydata['f_smoney'] = 0; $paydata['f_giga'] = 0; $paydata['f_convenic'] = 0;
		$paydata['n_bank'] = 0; $paydata['n_cre'] = 0; $paydata['n_bit'] = 0; $paydata['n_direct'] = 0;
		$paydata['n_ccheck'] = 0; $paydata['n_fregi'] = 0; $paydata['n_netbank'] = 0; $paydata['n_edy'] = 0;
		$paydata['n_gmoney'] = 0; $paydata['n_smoney'] = 0; $paydata['n_giga'] = 0; $paydata['n_convenic'] = 0;
		$paydata['def_m_bank'] = 0; $paydata['def_m_cre'] = 0; $paydata['def_m_bit'] = 0; $paydata['def_m_direct'] = 0; 
		$paydata['def_m_ccheck'] = 0; $paydata['def_m_fregi'] = 0; $paydata['def_m_netbank'] = 0; $paydata['def_m_edy'] = 0;
		$paydata['def_m_gmoney'] = 0; $paydata['def_m_smoney'] = 0; $paydata['def_m_giga'] = 0; $paydata['def_m_convenic'] = 0;
		$paydata['def_f_bank'] = 0; $paydata['def_f_cre'] = 0; $paydata['def_f_bit'] = 0; $paydata['def_f_direct'] = 0;
		$paydata['def_f_ccheck'] = 0; $paydata['def_f_fregi'] = 0; $paydata['def_f_netbank'] = 0; $paydata['def_f_edy'] = 0;
		$paydata['def_f_gmoney'] = 0; $paydata['def_f_smoney'] = 0; $paydata['def_f_giga'] = 0; $paydata['def_f_convenic'] = 0;
		$paydata['def_n_bank'] = 0; $paydata['def_n_cre'] = 0; $paydata['def_n_bit'] = 0; $paydata['def_n_direct'] = 0;
		$paydata['def_n_ccheck'] = 0; $paydata['def_n_fregi'] = 0; $paydata['def_n_netbank'] = 0; $paydata['def_n_edy'] = 0;
		$paydata['def_n_gmoney'] = 0; $paydata['def_n_smoney'] = 0; $paydata['def_n_giga'] = 0; $paydata['def_n_convenic'] = 0;
		$paydata['total_amount'] = 0; $paydata['m_total'] = 0; $paydata['f_total'] = 0; $paydata['n_total'] = 0;

		$select = $this->pay_colum[7].','.$this->pay_colum[11].','.$this->pay_colum[2].','.$this->pay_colum[9].','.$this->pay_colum[20];
#		$where  = $this->pay_colum[6].' >= '.$date1.' AND '.$this->pay_colum[6].' <= '.$date2.' AND '.$this->pay_colum[18].' = 0 ';
		$where  = $this->pay_colum[6].' >= '.$date1.' AND '.$this->pay_colum[6].' <= '.$date2.' AND '.$this->pay_colum[18].' = 0 AND '.$this->pay_colum[16].' = 1 ';
		$where	.= 'AND '.$this->pay_colum[9].' = 0 ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}

		if($site_cd != 'all'){
			$where .= 'AND '.$this->pay_colum[3].' = '.$site_cd;
		}
		$rtn    = $db->selectDb($this->pay_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		while($data = $db->fetchAssoc($rtn)){

		#	$normal_pay		= $data[$this->pay_colum[7]] - $data[$this->pay_colum[20]];
			$normal_pay		= $data[$this->pay_colum[7]];
			$def_pay		= $data[$this->pay_colum[20]];

			switch($data[$this->pay_colum[11]]){

				case('1'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_bank']			+= $normal_pay;
					$paydata['def_m_bank']		+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_bank']		+= $normal_pay;
					$paydata['def_f_bank']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_bank']		+= $normal_pay;
					$paydata['def_n_bank']	+= $def_pay;
				}
				break;

				case('2'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_cre']		+= $normal_pay;
					$paydata['def_m_cre']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_cre']		+= $normal_pay;
					$paydata['def_f_cre']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_cre']		+= $normal_pay;
					$paydata['def_n_cre']	+= $def_pay;
				}
				break;

				case('3'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_bit']		+= $normal_pay;
					$paydata['def_m_bit']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_bit']		+= $normal_pay;
					$paydata['def_f_bit']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_bit']		+= $normal_pay;
					$paydata['def_n_bit']	+= $def_pay;
				}
				break;

				case('4'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_direct']		+= $normal_pay;
					$paydata['def_m_direct']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_direct']		+= $normal_pay;
					$paydata['def_f_direct']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_direct']		+= $normal_pay;
					$paydata['def_n_direct']	+= $def_pay;
				}
				break;

				case('5'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_ccheck']		+= $normal_pay;
					$paydata['def_m_ccheck']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_ccheck']		+= $normal_pay;
					$paydata['def_f_ccheck']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_ccheck']		+= $normal_pay;
					$paydata['def_n_ccheck']	+= $def_pay;
				}
				break;

				case('6'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_fregi']		+= $normal_pay;
					$paydata['def_m_fregi']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_fregi']		+= $normal_pay;
					$paydata['def_f_fregi']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_fregi']		+= $normal_pay;
					$paydata['def_n_fregi']	+= $def_pay;
				}
				break;

				case('7'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_netbank']		+= $normal_pay;
					$paydata['def_m_netbank']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_netbank']		+= $normal_pay;
					$paydata['def_f_netbank']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_netbank']		+= $normal_pay;
					$paydata['def_n_netbank']	+= $def_pay;
				}
				break;

				case('8'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_edy']		+= $normal_pay;
					$paydata['def_m_edy']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_edy']		+= $normal_pay;
					$paydata['def_f_edy']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_edy']		+= $normal_pay;
					$paydata['def_n_edy']	+= $def_pay;
				}
				break;

				case('9'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_gmoney']		+= $normal_pay;
					$paydata['def_m_gmoney']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_gmoney']		+= $normal_pay;
					$paydata['def_f_gmoney']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_gmoney']		+= $normal_pay;
					$paydata['def_n_gmoney']	+= $def_pay;
				}
				break;

				case('10'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_smoney']		+= $normal_pay;
					$paydata['def_m_smoney']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_smoney']		+= $normal_pay;
					$paydata['def_f_smoney']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_smoney']		+= $normal_pay;
					$paydata['def_n_smoney']	+= $def_pay;
				}
				break;

				case('11'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_giga']		+= $normal_pay;
					$paydata['def_m_giga']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_giga']		+= $normal_pay;
					$paydata['def_f_giga']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_giga']		+= $normal_pay;
					$paydata['def_n_giga']	+= $def_pay;
				}
				break;

				case('12'):
				if($data[$this->pay_colum[2]] == 1){
					$paydata['m_convenic']		+= $normal_pay;
					$paydata['def_m_convenic']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 2){
					$paydata['f_convenic']		+= $normal_pay;
					$paydata['def_f_convenic']	+= $def_pay;
				}elseif($data[$this->pay_colum[2]] == 3){
					$paydata['n_convenic']		+= $normal_pay;
					$paydata['def_n_convenic']	+= $def_pay;
				}
				break;


			}

			if($data[$this->pay_colum[2]] == 1 && $data[$this->pay_colum[11]] != "99"){
				$paydata['m_total'] += $data[$this->pay_colum[7]];
			}elseif($data[$this->pay_colum[2]] == 2 && $data[$this->pay_colum[11]] != "99"){
				$paydata['f_total'] += $data[$this->pay_colum[7]];
			}elseif($data[$this->pay_colum[2]] == 3 && $data[$this->pay_colum[11]] != "99"){
				$paydata['n_total'] += $data[$this->pay_colum[7]];
			}

			$paydata['total_amount']	= $paydata['m_total'] + $paydata['f_total'] + $paydata['n_total'];

			$paydata['bank_total']		= $paydata['m_bank'] + $paydata['f_bank'] + $paydata['n_bank'];
			$paydata['cre_total']		= $paydata['m_cre'] + $paydata['f_cre'] + $paydata['n_cre'];
			$paydata['bit_total']		= $paydata['m_bit'] + $paydata['f_bit'] + $paydata['n_bit'];
			$paydata['direct_total']	= $paydata['m_direct'] + $paydata['f_direct'] + $paydata['n_direct'];
			$paydata['ccheck_total']	= $paydata['m_ccheck'] + $paydata['f_ccheck'] + $paydata['n_ccheck'];
			$paydata['fregi_total']		= $paydata['m_fregi'] + $paydata['f_fregi'] + $paydata['n_fregi'];
			$paydata['netbank_total']	= $paydata['m_netbank'] + $paydata['f_netbank'] + $paydata['n_netbank'];
			$paydata['edy_total']		= $paydata['m_edy'] + $paydata['f_edy'] + $paydata['n_edy'];
			$paydata['gmoney_total']	= $paydata['m_gmoney'] + $paydata['f_gmoney'] + $paydata['n_gmoney'];
			$paydata['smoney_total']	= $paydata['m_smoney'] + $paydata['f_smoney'] + $paydata['n_smoney'];
			$paydata['giga_total']		= $paydata['m_giga'] + $paydata['f_giga'] + $paydata['n_giga'];
			$paydata['convenic_total']	= $paydata['m_convenic'] + $paydata['f_convenic'] + $paydata['n_convenic'];

		}

		return($paydata);

	}


	# 本登録ユーザ デバイス別人数
	function getAlivedata($date,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		$select	= "count(id) AS count";
		$where  = "access_date >= '".$date."' AND pay_count > 0 AND op_id = 0 ";

		if($domain_flg != ""){
			$where .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}

		if($site_cd != 'all'){
			$where .= 'AND '.$this->pay_colum[3].' = '.$site_cd;
		}

		$rtn    = $db->selectDb("members",$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		$data	= $db->fetchAssoc($rtn);

		if(!$data){
			$result	= 0;
		}else{
			$result	= $data['count'];
		}

		return $result;

	}


	# ポイント別生き残り
	function getAlivedataPoint($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		$select	= "id,point,s_point,pay_count";
		$where  = "access_date >= '".$date1."' AND access_date <= '".$date2."' AND op_id = 0 ";
		if($domain_flg != ""){
			$where .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}

		if($site_cd != 'all'){
			$where .= 'AND '.$this->pay_colum[3].' = '.$site_cd;
		}

		$rtn    = $db->selectDb("members",$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		$result['23pt']		= 0;
		$result['100pt']	= 0;
		$result['300pt']	= 0;
		$result['overpt']	= 0;
		$result['no23pt']	= 0;
		$result['no100pt']	= 0;
		$result['no300pt']	= 0;
		$result['nooverpt']	= 0;

		while($data	= $db->fetchAssoc($rtn)){

			$check_point	= $data['point']+$data['s_point'];

			# 入金無し
			if($data['pay_count'] == 0){

				# 0 ～ 23
				if($check_point >= 0 && $check_point <= 23){
					$result['no23pt']++;
				# 24 ～ 100
				}elseif($check_point >= 24 && $check_point <= 100){
					$result['no100pt']++;
				# 101 ～ 300
				}elseif($check_point >= 101 && $check_point <= 300){
					$result['no300pt']++;
				# 101 ～ 300
				}elseif($check_point >= 301){
					$result['nooverpt']++;
				}else{
					$result['nooverpt']++;
				}

			# 入金有り
			}else{

				# 0 ～ 23
				if($check_point >= 0 && $check_point <= 23){
					$result['23pt']++;
				# 24 ～ 100
				}elseif($check_point >= 24 && $check_point <= 100){
					$result['100pt']++;
				# 101 ～ 300
				}elseif($check_point >= 101 && $check_point <= 300){
					$result['300pt']++;
				# 101 ～ 300
				}elseif($check_point >= 301){
					$result['overpt']++;
				}else{
					$result['overpt']++;
				}

			}


		}

		return $result;

	}


	# 送信カウント取得
	function getMailcounts($date1,$date2,$site_cd,$op_id=NULL,$staff_id=NULL,$send_id=NULL){

		global $db;

		$select = "SUM(count) AS mail_count";
		$where  = "send_date >= '".$date1."' AND send_date <= '".$date2."' ";
		if($op_id != ""){
			$where	.= "AND op_id = ".$op_id." ";
		}
		if($staff_id != ""){
			$where	.= "AND staff_id = ".$staff_id." ";
		}
		if($send_id != ""){
			$where	.= "AND send_id = ".$send_id." ";
		}
		if($site_cd != 'all'){
			$where .= "AND site_cd = ".$site_cd;
		}
		$rtn    = $db->selectDb("mailcounts",$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		$data	= $db->fetchAssoc($rtn);

		return($data['mail_count']);

	}



	# 入金
	/*
	function getPaydata($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		$paydata['m_bank'] = 0; $paydata['m_cre'] = 0; $paydata['m_bit'] = 0; $paydata['m_direct'] = 0;
		$paydata['m_ccheck'] = 0; $paydata['m_fregi'] = 0;
		$paydata['f_bank'] = 0; $paydata['f_cre'] = 0; $paydata['f_bit'] = 0; $paydata['f_direct'] = 0;
		$paydata['f_ccheck'] = 0; $paydata['f_fregi'] = 0;
		$paydata['n_bank'] = 0; $paydata['n_cre'] = 0; $paydata['n_bit'] = 0; $paydata['n_direct'] = 0;
		$paydata['n_ccheck'] = 0; $paydata['n_fregi'] = 0;
		$paydata['def_m_bank'] = 0; $paydata['def_m_cre'] = 0; $paydata['def_m_bit'] = 0; $paydata['def_m_direct'] = 0; 
		$paydata['def_m_ccheck'] = 0; $paydata['def_m_fregi'] = 0;
		$paydata['def_f_bank'] = 0; $paydata['def_f_cre'] = 0; $paydata['def_f_bit'] = 0; $paydata['def_f_direct'] = 0;
		$paydata['def_f_ccheck'] = 0; $paydata['def_f_fregi'] = 0;
		$paydata['def_n_bank'] = 0; $paydata['def_n_cre'] = 0; $paydata['def_n_bit'] = 0; $paydata['def_n_direct'] = 0;
		$paydata['def_n_ccheck'] = 0; $paydata['def_n_fregi'] = 0;
		$paydata['total_amount'] = 0; $paydata['m_total'] = 0; $paydata['f_total'] = 0; $paydata['n_total'] = 0;

		$select = $this->pay_colum[7].','.$this->pay_colum[11].','.$this->pay_colum[2].','.$this->pay_colum[9].','.$this->pay_colum[20];
		$where  = $this->pay_colum[6].' >= '.$date1.' AND '.$this->pay_colum[6].' <= '.$date2.' AND '.$this->pay_colum[18].' = 0 ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}

		if($site_cd != 'all'){
			$where .= 'AND '.$this->pay_colum[3].' = '.$site_cd;
		}
		$rtn    = $db->selectDb($this->pay_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		while($data = $db->fetchAssoc($rtn)){

			if($data[$this->pay_colum[2]] == 1){

				if($data[$this->pay_colum[9]] == 0 && $data[$this->pay_colum[20]] != 0){
					switch($data[$this->pay_colum[11]]){
						# 後払清算入金
						case('1'):$paydata['def_m_bank']   += $data[$this->pay_colum[20]]; break;
						case('2'):$paydata['def_m_cre']    += $data[$this->pay_colum[20]]; break;
						case('3'):$paydata['def_m_bit']    += $data[$this->pay_colum[20]]; break;
						case('4'):$paydata['def_m_direct'] += $data[$this->pay_colum[20]]; break;
						case('5'):$paydata['def_m_ccheck'] += $data[$this->pay_colum[20]]; break;
						case('6'):$paydata['def_m_fregi']  += $data[$this->pay_colum[20]]; break;
					}
				}else{
					switch($data[$this->pay_colum[11]]){
						# 通常入金
						case('1'):$paydata['m_bank']   += $data[$this->pay_colum[7]]; break;
						case('2'):$paydata['m_cre']    += $data[$this->pay_colum[7]]; break;
						case('3'):$paydata['m_bit']    += $data[$this->pay_colum[7]]; break;
						case('4'):$paydata['m_direct'] += $data[$this->pay_colum[7]]; break;
						case('5'):$paydata['m_ccheck'] += $data[$this->pay_colum[7]]; break;
						case('6'):$paydata['m_fregi']  += $data[$this->pay_colum[7]]; break;
					}
				}
				$paydata['m_total'] += $data[$this->pay_colum[7]];

			}elseif($data[$this->pay_colum[2]] == 2){

				if($data[$this->pay_colum[9]] == 1){
					switch($data[$this->pay_colum[11]]){
						# 後払清算入金
						case('1'):$paydata['def_f_bank']   += $data[$this->pay_colum[7]]; break;
						case('2'):$paydata['def_f_cre']    += $data[$this->pay_colum[7]]; break;
						case('3'):$paydata['def_f_bit']    += $data[$this->pay_colum[7]]; break;
						case('4'):$paydata['def_f_direct'] += $data[$this->pay_colum[7]]; break;
						case('5'):$paydata['def_f_ccheck'] += $data[$this->pay_colum[7]]; break;
						case('6'):$paydata['def_f_fregi']  += $data[$this->pay_colum[7]]; break;
					}
				}else{
					switch($data[$this->pay_colum[11]]){
						# 通常入金
						case('1'):$paydata['f_bank']   += $data[$this->pay_colum[7]]; break;
						case('2'):$paydata['f_cre']    += $data[$this->pay_colum[7]]; break;
						case('3'):$paydata['f_bit']    += $data[$this->pay_colum[7]]; break;
						case('4'):$paydata['f_direct'] += $data[$this->pay_colum[7]]; break;
						case('5'):$paydata['f_ccheck'] += $data[$this->pay_colum[7]]; break;
						case('6'):$paydata['f_fregi']  += $data[$this->pay_colum[7]]; break;
					}
				}
				$paydata['f_total'] += $data[$this->pay_colum[7]];

			}elseif($data[$this->pay_colum[2]] == 3){
				if($data[$this->pay_colum[9]] == 1){
					switch($data[$this->pay_colum[11]]){
						# 後払清算入金
						case('1'):$paydata['def_n_bank']   += $data[$this->pay_colum[7]]; break;
						case('2'):$paydata['def_n_cre']    += $data[$this->pay_colum[7]]; break;
						case('3'):$paydata['def_n_bit']    += $data[$this->pay_colum[7]]; break;
						case('4'):$paydata['def_n_direct'] += $data[$this->pay_colum[7]]; break;
						case('5'):$paydata['def_n_ccheck'] += $data[$this->pay_colum[7]]; break;
						case('6'):$paydata['def_n_fregi']  += $data[$this->pay_colum[7]]; break;
					}
				}else{
					switch($data[$this->pay_colum[11]]){
						# 通常入金
						case('1'):$paydata['n_bank']   += $data[$this->pay_colum[7]]; break;
						case('2'):$paydata['n_cre']    += $data[$this->pay_colum[7]]; break;
						case('3'):$paydata['n_bit']    += $data[$this->pay_colum[7]]; break;
						case('4'):$paydata['n_direct'] += $data[$this->pay_colum[7]]; break;
						case('5'):$paydata['n_ccheck'] += $data[$this->pay_colum[7]]; break;
						case('6'):$paydata['n_fregi']  += $data[$this->pay_colum[7]]; break;
					}
				}
				$paydata['n_total'] += $data[$this->pay_colum[7]];
			}

			$paydata['total_amount'] = $paydata['m_total'] + $paydata['f_total'] + $paydata['n_total'];

		}

		return($paydata);

	}
	*/






	function getPaydataById($id){

		global $db;

		$paydata['bank'] = 0; $paydata['credit'] = 0; $paydata['bit'] = 0;
		$paydata['direct'] = 0; $paydata['ccheck'] = 0; $paydata['fregi'] = 0;
		$paydata['total'] = 0;

		$select  = $this->pay_colum[7].','.$this->pay_colum[11];
		$where   = $this->pay_colum[9].' = 0 ';
		$where  .= 'AND '.$this->pay_colum[18].' = 0 ';
		$where  .= 'AND '.$this->pay_colum[16].' = 1 ';
		$where	.= 'AND '.$this->pay_colum[1].' = '.$id;

		$rtn    = $db->selectDb($this->pay_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		while($data = $db->fetchAssoc($rtn)){

			switch($data[$this->pay_colum[11]]){
				case 1: $paydata['bank']   += $data[$this->pay_colum[7]]; break;
				case 2: $paydata['credit'] += $data[$this->pay_colum[7]]; break;
				case 3: $paydata['bit']    += $data[$this->pay_colum[7]]; break;
				case 4: $paydata['direct'] += $data[$this->pay_colum[7]]; break;
				case 5: $paydata['ccheck'] += $data[$this->pay_colum[7]]; break;
				case 6: $paydata['fregi']  += $data[$this->pay_colum[7]]; break;
			}
			$paydata['total'] += $data[$this->pay_colum[7]];

		}

		return($paydata);

	}

	function getPaydataByCode($date1,$date2,$site_cd,$ad_code,$type,$domain_flg=NULL){

		$paydata['bank'] = 0; $paydata['credit'] = 0; $paydata['bit'] = 0;
		$paydata['direct'] = 0; $paydata['ccheck'] = 0; $paydata['fregi'] = 0;
		$paydata['total'] = 0;

		global $db;

		$select = $this->pay_colum[7].','.$this->pay_colum[11];
		$where  = $this->pay_colum[18].' = 0 ';
		$where .= 'AND '.$this->pay_colum[16].' = 1 ';
		$where .= 'AND '.$this->pay_colum[9].' = 0 ';
		# $type 1:完全一致 2:前方一致 3:後方一致 4:中間一致
		if($type == 1){
			$where .= 'AND '.$this->pay_colum[4].' = \''.$ad_code.'\' ';
		}elseif($type == 2){
			$where .= 'AND '.$this->pay_colum[4].' LIKE \''.$ad_code.'%\' ';
		}elseif($type == 3){
			$where .= 'AND '.$this->pay_colum[4].' LIKE \'%'.$ad_code.'\' ';
		}elseif($type == 4){
			$where .= 'AND '.$this->pay_colum[4].' LIKE \'%'.$ad_code.'%\' ';
		}else{
			$where .= 'AND '.$this->pay_colum[4].' = \''.$ad_code.'\' ';
		}
		$where .= 'AND '.$this->pay_colum[6].' >= '.$date1.' AND '.$this->pay_colum[6].' <= '.$date2.' ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->pay_colum[3].' = '.$site_cd.' ';
		}
		$rtn    = $db->selectDb($this->pay_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		while($data = $db->fetchAssoc($rtn)){

			switch($data[$this->pay_colum[11]]){
				case 1: $paydata['bank']   += $data[$this->pay_colum[7]]; break;
				case 2: $paydata['credit'] += $data[$this->pay_colum[7]]; break;
				case 3: $paydata['bit']    += $data[$this->pay_colum[7]]; break;
				case 4: $paydata['direct'] += $data[$this->pay_colum[7]]; break;
				case 5: $paydata['ccheck'] += $data[$this->pay_colum[7]]; break;
				case 6: $paydata['fregi']  += $data[$this->pay_colum[7]]; break;
			}
			$pay_data['total'] += $data[$this->pay_colum[7]];

		}

		return($paydata);

	}


	# 後払い 当日
	function getDefdata($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		$defdata['m_def'] = 0;
		$defdata['f_def'] = 0;
		$defdata['n_def'] = 0;
		$defdata['m_def_paid'] = 0;
		$defdata['f_def_paid'] = 0;
		$defdata['n_def_paid'] = 0;
		$defdata['def_total'] = 0;
		$defdata['def_paid_total'] = 0;
		$defdata['def_total_ratio'] = 0;
		$defdata['m_ratio'] = 0;
		$defdata['f_ratio'] = 0;
		$defdata['n_ratio'] = 0;

		$select = $this->pay_colum[7].','.$this->pay_colum[16].','.$this->pay_colum[2].',def_amount';
		$where  = $this->pay_colum[10].' >= '.$date1.' AND '.$this->pay_colum[10].' <= '.$date2.' AND '.$this->pay_colum[18].' = 0 ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}

		if($site_cd != 'all'){
			$where .= 'AND '.$this->pay_colum[3].' = '.$site_cd;
		}
		$rtn    = $db->selectDb($this->pay_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		#print("SQL:SELECT ".$select." FROM pays WHERE ".$where."<br>");

		while($data = $db->fetchAssoc($rtn)){
			if($data[$this->pay_colum[2]] == 1){
				if($data[$this->pay_colum[7]] > 0){
					$defdata['m_def_paid'] += $data[$this->pay_colum[7]];
				}
				$defdata['m_def'] += $data['def_amount'];
			}elseif($data[$this->pay_colum[2]] == 2){
				if($data[$this->pay_colum[7]] > 0){
					$defdata['f_def_paid'] += $data[$this->pay_colum[7]];
				}
				$defdata['f_def'] += $data['def_amount'];
			}else{
				if($data[$this->pay_colum[7]] > 0){
					$defdata['n_def_paid'] += $data[$this->pay_colum[7]];
				}
				$defdata['n_def'] += $data['def_amount'];
			}
		}

		# 後払い利用額
		$defdata['def_total']      = $defdata['m_def'] + $defdata['f_def'] + $defdata['n_def'];
		# 後払い回収額
		$defdata['def_paid_total'] = $defdata['m_def_paid'] + $defdata['f_def_paid'] + $defdata['n_def_paid'];

		if($defdata['def_total'] > 0){ $defdata['def_total_ratio'] = round($defdata['def_paid_total'] / $defdata['def_total'] * 100,2); }
		if($defdata['m_def'] > 0){ $defdata['m_ratio'] = round($defdata['m_def_paid'] / $defdata['m_def'] *100,2); }
		if($defdata['f_def'] > 0){ $defdata['f_ratio'] = round($defdata['f_def_paid'] / $defdata['f_def'] *100,2); }
		if($defdata['n_def'] > 0){ $defdata['n_ratio'] = round($defdata['n_def_paid'] / $defdata['n_def'] *100,2); }

		return($defdata);

	}


	# 後払い 日付指定
	function getDefdataDate($date1,$date2,$pay_date1,$pay_date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		$defdata['m_def'] = 0;
		$defdata['f_def'] = 0;
		$defdata['n_def'] = 0;
		$defdata['m_def_paid'] = 0;
		$defdata['f_def_paid'] = 0;
		$defdata['n_def_paid'] = 0;
		$defdata['def_total'] = 0;
		$defdata['def_paid_total'] = 0;
		$defdata['def_total_ratio'] = 0;
		$defdata['m_ratio'] = 0;
		$defdata['f_ratio'] = 0;
		$defdata['n_ratio'] = 0;



		/*****************************************
		**
		**	後払い利用データ
		**
		*****************************************/

		$select = "*";
		$where  = 'def_date >= '.$date1.' AND def_date <= '.$date2.' AND def_flg = 1 AND status = 0 ';
		if($domain_flg != ""){
			$where .= 'AND domain_flg = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}

		if($site_cd != 'all'){
			$where .= 'AND site_cd = '.$site_cd." ";
		}
		$rtn    = $db->selectDb($this->pay_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		#print("SQL1:SELECT ".$select." FROM pays WHERE ".$where."<br>");

		while($data = $db->fetchAssoc($rtn)){

			# 支払い日指定
			$pay_select	= "SUM(def_amount) total";
			$pay_where	= "pay_date >= '".$pay_date1."' AND pay_date <= '".$pay_date2."' AND def_amount > 0 AND status = 0 AND user_id = ".$data['user_id']." ";
			$pay_where	.= "GROUP BY user_id";
			$pay_rtn	= $db->selectDb($this->pay_table,$pay_select,$pay_where,'','');
			$db->errorDb('',$db->errno,__FILE__,__LINE__);
			$pay_data	= $db->fetchAssoc($pay_rtn);

			if($data[$this->pay_colum[2]] == 1){
				if($pay_data['total'] > 0){
					$defdata['m_def_paid']	+= $pay_data['total'];
				}
				$defdata['m_def'] += $data['def_amount'];
			}elseif($data[$this->pay_colum[2]] == 2){
				if($pay_data['total'] > 0){
					$defdata['f_def_paid']	+= $pay_data['total'];
				}
				$defdata['f_def'] += $data['def_amount'];
			}else{
				if($pay_data['total'] > 0){
					$defdata['n_def_paid']	+= $pay_data['total'];
				}
				$defdata['n_def'] += $data['def_amount'];
			}

		}

		# 後払い利用額
		$defdata['def_total']      = $defdata['m_def'] + $defdata['f_def'] + $defdata['n_def'];

		# 後払い回収額
		$defdata['def_paid_total'] = $defdata['m_def_paid'] + $defdata['f_def_paid'] + $defdata['n_def_paid'];

		if($defdata['def_total'] > 0){ $defdata['def_total_ratio'] = round($defdata['def_paid_total'] / $defdata['def_total'] * 100,2); }
		if($defdata['m_def'] > 0){ $defdata['m_ratio'] = round($defdata['m_def_paid'] / $defdata['m_def'] *100,2); }
		if($defdata['f_def'] > 0){ $defdata['f_ratio'] = round($defdata['f_def_paid'] / $defdata['f_def'] *100,2); }
		if($defdata['n_def'] > 0){ $defdata['n_ratio'] = round($defdata['n_def_paid'] / $defdata['n_def'] *100,2); }

		return($defdata);

	}



	# 新規入金データ
	function getNewPaydata($date1,$date2,$site_cd,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL,$total_num=NULL,$m_num=NULL,$f_num=NULL,$n_num=NULL){

		global $db;

		$result['m_count']		= 0;
		$result['f_count']		= 0;
		$result['n_count']		= 0;
		$result['total_count']	= 0;

		$result['m_amount']		= 0;
		$result['f_amount']		= 0;
		$result['n_amount']		= 0;
		$result['total_amount']	= 0;

		$select = "id,pay_amount,sex";
		$where  = "reg_date >= '".$date1."' AND reg_date <= '".$date2."' AND pay_amount != 0 AND op_id = '0'";
		if($domain_flg != ""){
			$where .= 'AND domain_flg = '.$domain_flg.' ';
		}

		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}

		if($site_cd != 'all'){
			$where .= 'AND site_cd = '.$site_cd;
		}
		$rtn    = $db->selectDb("members",$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);

		#print("SQL:SELECT ".$select." FROM members WHERE ".$where."<br>");

		while($data = $db->fetchAssoc($rtn)){

			if($data['sex'] == 1){
				$result['m_count']++;
				$result['m_amount'] += $data['pay_amount'];
			}elseif($data['sex'] == 2){
				$result['f_count']++;
				$result['f_amount'] += $data['pay_amount'];
			}else{
				$result['n_count']++;
				$result['n_amount'] += $data['pay_amount'];
			}

		}

		$result['total_count']	= $result['m_count']+$result['f_count']+$result['n_count'];
		$result['total_amount']	= $result['m_amount']+$result['f_amount']+$result['n_amount'];


		if($total_num == 0){
			$result['total_parsent']	= 0;
		}else{
			$total_parsent				= 100*$result['total_count']/$total_num;
			$result['total_parsent']	= (int)$total_parsent;
		}

		if($m_num == 0){
			$result['m_parsent']	= 0;
		}else{
			$m_parsent				= 100*$result['m_count']/$m_num;
			$result['m_parsent']	= (int)$m_parsent;
		}

		if($f_num == 0){
			$result['f_parsent']	= 0;
		}else{
			$f_parsent				= 100*$result['f_count']/$f_num;
			$result['f_parsent']	= (int)$f_parsent;
		}

		if($n_num == 0){
			$result['n_parsent']	= 0;
		}else{
			$n_parsent				= 100*$result['n_count']/$n_num;
			$result['n_parsent']	= (int)$n_parsent;
		}

		return($result);

	}



	# 総合客単価
	function getUnitPrice($site_cd,$sex=0,$domain_flg=NULL,$ad_code=NULL,$ad_code_type=NULL){

		global $db;

		# 総入金額
		$select = 'SUM('.$this->pay_colum[7].') AS total_amount';
		$where  = $this->pay_colum[18].' = 0 ';
		$where .= 'AND '.$this->pay_colum[16].' = 1 ';
		if($domain_flg != ""){
			$where .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}
		if($site_cd != 'all'){
			$where .= 'AND '.$this->pay_colum[3].' = '.$site_cd.' ';
		}
		if($ad_code_type == '1'){
			$where	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where	.= "AND ad_code not like '%".$ad_code."' ";
		}
		if($sex != 0){
			$where .= 'AND '.$this->pay_colum[2].' = '.$sex.' ';
		}
		$rtn    = $db->selectDb($this->pay_table,$select,$where,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);
		$data = $db->fetchAssoc($rtn);
		$unit_data['total_amount'] = $data['total_amount'];

		# 入有
		$select2 = 'COUNT('.$this->user_colum[0].') AS payuser_cnt';
		$where2  = $this->user_colum[10].' = 0 AND '.$this->user_colum[2].' != 0 AND '.$this->user_colum[4].' >= 1 AND '.$this->user_colum[4].' <= 8 ';
		if($domain_flg != ""){
			$where2 .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}
		if($site_cd != 'all'){
			$where2 .= 'AND '.$this->user_colum[7].' = '.$site_cd.' ';
		}
		if($sex != 0){
			$where2 .= 'AND '.$this->user_colum[1].' = '.$sex.' ';
		}
		if($ad_code_type == '1'){
			$where2	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where2	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where2	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where2	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where2	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where2	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where2	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where2	.= "AND ad_code not like '%".$ad_code."' ";
		}
		$rtn2    = $db->selectDb($this->user_table,$select2,$where2,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);
		$data2 = $db->fetchAssoc($rtn2);
		$unit_data['pay_user'] = $data2['payuser_cnt'];



		# 総ユーザ
		$select3 = 'COUNT('.$this->user_colum[0].') AS alluser_cnt';
		$where3  = $this->user_colum[10].' = 0 AND '.$this->user_colum[4].' >= 1 AND '.$this->user_colum[4].' <= 8 ';
		if($domain_flg != ""){
			$where3 .= 'AND '.$this->pay_colum[19].' = '.$domain_flg.' ';
		}
		if($site_cd != 'all'){
			$where3 .= 'AND '.$this->user_colum[7].' = '.$site_cd.' ';
		}
		if($sex != 0){
			$where3 .= 'AND '.$this->user_colum[1].' = '.$sex.' ';
		}
		if($ad_code_type == '1'){
			$where3	.= "AND ad_code = '".$ad_code."' ";
		}elseif($ad_code_type == '2'){
			$where3	.= "AND ad_code like '".$ad_code."%' ";
		}elseif($ad_code_type == '3'){
			$where3	.= "AND ad_code like '%".$ad_code."%' ";
		}elseif($ad_code_type == '4'){
			$where3	.= "AND ad_code like '%".$ad_code."' ";
		}elseif($ad_code_type == '5'){
			$where3	.= "AND ad_code != '".$ad_code."' ";
		}elseif($ad_code_type == '6'){
			$where3	.= "AND ad_code not like '".$ad_code."%' ";
		}elseif($ad_code_type == '7'){
			$where3	.= "AND ad_code not like '%".$ad_code."%' ";
		}elseif($ad_code_type == '8'){
			$where3	.= "AND ad_code not like '%".$ad_code."' ";
		}
		$rtn3    = $db->selectDb($this->user_table,$select3,$where3,'','');
		$db->errorDb('',$db->errno,__FILE__,__LINE__);
		$data3	 = $db->fetchAssoc($rtn3);
		$unit_data['total_user'] = $data3['alluser_cnt'];

		# pay_up:入有単価
		if($unit_data['pay_user'] == 0){
			$unit_data['pay_up'] = 0;
		}else{
			$unit_data['pay_up'] = $unit_data['total_amount']/$unit_data['pay_user'];
			$unit_data['pay_up'] = (int)$unit_data['pay_up'];
		}

		# total_up:総単価 total_rate:入金率
		if($unit_data['total_user'] == 0){
			$unit_data['total_up']   = 0;
			$unit_data['total_rate'] = 0;
		}else{
			$unit_data['total_up'] = $unit_data['total_amount']/$unit_data['total_user'];
			$unit_data['total_up'] = (int)$unit_data['total_up'];
			$unit_data['total_rate'] = 100*$unit_data['pay_user']/$unit_data['total_user'];
			$unit_data['total_rate'] = (int)$unit_data['total_rate'];
		}

		return($unit_data);

	}



	function getAdCodeDomainFlg($site_cd,$ad_code){

		global	$db;

		if(!$ad_code){
			return FALSE;
		}

		$table	 = "promotionagencies";
		$column	 = "domain_flg";
		$where	.= "site_cd = ".$site_cd." ";
		$where	.= "AND ad_code = '".$ad_code."' ";
		$order	 = "";
		$limit	 = "";

		$rtn    = $db->selectDb($table,$column,$where,$order,$limit);
		$db->errorDb('',$db->errno,__FILE__,__LINE__);
		$data = $db->fetchAssoc($rtn);
		$result = $data['domain_flg'];

		return	$result;

	}


	# CALCURATION NAVIGATION
	function calcNavigation($search_site,$domain_flg,$post_data){

		global	$db;

		$siteinfos				= new siteinfos();
		$result['site_opt'] 	= $siteinfos->getAllSiteInfo($search_site);

		if($search_site == 'all'){

			$result['domain']	 = "<select name=\"domain_flg\">\n";
			$result['domain']	.= "<option value=\"\">選択できません</option>\n";
			$result['domain']	.= "</select>\n";

		}else{

			$domain_select = '*';
			$domain_where  = 'site_cd = '.$search_site.' AND url_ng = 0 AND status = 0';
			$domain_rtn    = $db->selectDb('domainlists',$domain_select,$domain_where,'domain_flg','');
			$db->errorDb('domain_reg1',$db->errno,__FILE__,__LINE__);

			$result['domain']	 = "<select name=\"domain_flg\">\n";
			$result['domain']	.= "<option value=\"\">全て</option>\n";

			while($domain_data = $db->fetchAssoc($domain_rtn)){

				if($domain_data['comment']){ $comment = "(".$domain_data['comment'].")"; }

				if($domain_flg == $domain_data['domain_flg']){
					$result['domain']	.= "<option value=\"".$domain_data['domain_flg']."\" selected>".$domain_data['domain']."【".$domain_data['site_name']."】".$comment."</option>\n";
					$result['site']		.= "(".$domain_data['domain'].")";
				}else{
					$result['domain']	.= "<option value=\"".$domain_data['domain_flg']."\">".$domain_data['domain']."【".$domain_data['site_name']."】".$comment."</option>\n";
				}

			}

			$result['domain']	 .= "</select>\n";

		}

		return $result;

	}



}

?>
