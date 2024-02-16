<?
#############################################################################
##
##	kanri_toujitu_uriuri.php
##	------------------------------------------------------------------------
##	スゲーファイル名だ
##
##
################################### CONF ####################################

require_once(dirname(__FILE__)."/CONF/config.php");

################################## CLASS ####################################

require_once(dirname(__FILE__)."/class/database.php");
require_once(dirname(__FILE__)."/class/main.php");
require_once(dirname(__FILE__)."/class/html_class.php");
require_once(dirname(__FILE__)."/class/siteinfos.php");
require_once(dirname(__FILE__)."/class/calc.php");

require(dirname(__FILE__)."/user_agent.php");


############################# DATABASE CONNECT ##############################

$db = new accessDb(0);
$db->connectDb();

################################ NEW CLASS ##################################

$adminMain	= new adminMain($db);
$html_class	= new htmlClass();
$calc 		= new calculation();
$siteinfos = new siteinfos();

################################ MAIN SET ###################################

$search_site	= SITE_CD;
$sta_date		= date("Ymd")."000000";
$end_date		= date("Ymd")."235959";

# SITE DATA
$sitedata	= $siteinfos->getSiteInfo($search_site);
$disp_site	= $siteinfos->getSiteName($search_site);
define("SITE_NAME",$disp_site);

################################ MAIN SQL ###################################


# 削りポイント
$pt_rtn  = $calc->getConsumption($sta_date,$end_date,$search_site,$_REQUEST['domain_flg']);

$nouser_m_cnt	= 0;
$nouser_f_cnt	= 0;
$nouser_n_cnt	= 0;
$payuser_m_cnt	= 0;
$payuser_f_cnt	= 0;
$payuser_n_cnt	= 0;
while($pt_data = $db->fetchAssoc($pt_rtn)){

	if($_REQUEST['ad_code_type']){

		$chk_table  = "members";
		$chk_select = "id";
		$chk_where  = "id = ".$pt_data['user_id']." ";
		# アドコード
		if($_REQUEST['ad_code_type'] == '1'){
			$chk_where	.= "AND ad_code = '".$_REQUEST['ad_code']."' ";
		}elseif($_REQUEST['ad_code_type'] == '2'){
			$chk_where	.= "AND ad_code like '".$_REQUEST['ad_code']."%' ";
		}elseif($_REQUEST['ad_code_type'] == '3'){
			$chk_where	.= "AND ad_code like '%".$_REQUEST['ad_code']."%' ";
		}elseif($_REQUEST['ad_code_type'] == '4'){
			$chk_where	.= "AND ad_code like '%".$_REQUEST['ad_code']."' ";
		}elseif($_REQUEST['ad_code_type'] == '5'){
			$chk_where	.= "AND ad_code != '".$_REQUEST['ad_code']."' ";
		}elseif($_REQUEST['ad_code_type'] == '6'){
			$chk_where	.= "AND ad_code not like '".$_REQUEST['ad_code']."%' ";
		}elseif($_REQUEST['ad_code_type'] == '7'){
			$chk_where	.= "AND ad_code not like '%".$_REQUEST['ad_code']."%' ";
		}elseif($_REQUEST['ad_code_type'] == '8'){
			$chk_where	.= "AND ad_code not like '%".$_REQUEST['ad_code']."' ";
		}

		$chk_rtn    = $db->selectDb($chk_table,$chk_select,$chk_where,"","");
		$db->errorDb("",$db->errno,__FILE__,__LINE__);
		$chk_rows   = $db->numRows($chk_rtn);

		if($chk_rows == 0){
			continue;
		}

	}

	if($pt_data['sex'] == 1){
		if($pt_data['pay_flg'] == 2){
			$nouser_m_cnt  += $pt_data['point'];
		}else{
			$payuser_m_cnt += $pt_data['point'];
		}
	}elseif($pt_data['sex'] == 2){
		if($pt_data['pay_flg'] == 2){
			$nouser_f_cnt  += $pt_data['point'];
		}else{
			$payuser_f_cnt += $pt_data['point'];
		}
	}else{
		if($pt_data['pay_flg'] == 2){
			$nouser_n_cnt  += $pt_data['point'];
		}else{
			$payuser_n_cnt += $pt_data['point'];
		}
	}

}

# 登録数計算
$registnum  = $calc->getRegistNum($sta_date,$end_date,$search_site,$_REQUEST['domain_flg'],$_REQUEST['ad_code'],$_REQUEST['ad_code_type']);

# 入金額計算
$paydata    = $calc->getPaydata($sta_date,$end_date,$search_site,$_REQUEST['domain_flg'],$_REQUEST['ad_code'],$_REQUEST['ad_code_type']);

# 後払回収
$defdata    = $calc->getDefdata($sta_date,$end_date,$search_site,$_REQUEST['domain_flg'],$_REQUEST['ad_code'],$_REQUEST['ad_code_type']);


# 閲覧用集計
$entry_all = $registnum['sum_all']-$registnum['reg_all']; # 総仮登録数
$m_entry = $registnum['m_all']-$registnum['m_reg'];	# 男性仮登録数
$f_entry = $registnum['f_all']-$registnum['f_reg'];	# 女性仮登録数
$n_entry = $registnum['n_all']-$registnum['n_reg'];	# 不明仮登録数

# 削り
$total_point_m = $nouser_m_cnt+$payuser_m_cnt;
$total_point_f = $nouser_f_cnt+$payuser_f_cnt;
$total_point_n = $nouser_n_cnt+$payuser_n_cnt;
$total_point   = $total_point_m+$total_point_f+$total_point_n;

# NAVIGATION
$navigation	=$calc->calcNavigation($search_site,$_REQUEST['domain_flg'],$post_data);

################################ CLOSE DATABASE #################################

$db->closeDb();

################################# HTML HEADER ###################################

$html_class->htmlHeader();

################################## HTML BODY ####################################
?>

<div align="center">
<font size="3">
<? print(date("Y")); ?>年<? print(date("m")); ?>月<? print(date("d")); ?>日 
<? print(SITE_NAME); ?>集計
</font>
</div>
<hr color="#CCCCCC" size="1" />

<font size="2">

■本登録数<br />
<font color="#FF0000">TOTAL</font>：<? print($registnum['reg_all']); ?>人<br />
<font color="#009966">男性</font>：<? print($registnum['m_reg']); ?>人<br />
<font color="#FF3366">女性</font>：<? print($registnum['f_reg']); ?>人<br />
不明：<? print($registnum['n_reg']); ?>人<br />



<hr color="#CCCCCC" size="1" />
■仮登録数<br />
<font color="#FF0000">TOTAL</font>：<? print($registnum['entry_all']); ?>人<br />
<font color="#009966">男性</font>：<? print($registnum['m_entry']); ?>人<br />
<font color="#FF3366">女性</font>：<? print($registnum['f_entry']); ?>人<br />
不明：<? print($registnum['n_entry']); ?>人<br />


<hr color="#CCCCCC" size="1" />
■本登録率<br />
<font color="#FF0000">TOTAL</font>：<? print($registnum['ratio_all']); ?>％<br />
<font color="#009966">男性</font>：<? print($registnum['m_ratio']); ?>％<br />
<font color="#FF3366">女性</font>：<? print($registnum['f_ratio']); ?>％<br />
不明：<? print($registnum['n_ratio']); ?>％<br />


<hr color="#CCCCCC" size="1" />
<font color="#009966">入金有男性消費Pt</font><br />
<? print($payuser_m_cnt); ?>Pt<br />
<font color="#FF3366">入金有女性消費Pt</font><br />
<? print($payuser_f_cnt); ?>Pt<br />
<font color="#333333">入金有不明消費Pt</font><br />
<? print($payuser_n_cnt); ?>Pt<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">入金無男性消費Pt</font><br />
<? print($nouser_m_cnt); ?>Pt<br />
<font color="#FF3366">入金無女性消費Pt</font><br />
<? print($nouser_f_cnt); ?>Pt<br />
<font color="#333333">入金無不明消費Pt</font><br />
<? print($nouser_n_cnt); ?>Pt<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966"><b>合計男性消費Pt</b></font><br />
<font color="#009966"><? print($total_point_m); ?>Pt</font><br />
<font color="#FF3366"><b>合計女性消費Pt</b></font><br />
<font color="#FF3366"><? print($total_point_f); ?>Pt</font><br />
<font color="#000000"><b>合計不明消費Pt</b></font><br />
<font color="#000000"><? print($total_point_n); ?>Pt</font><br />



<hr color="#CCCCCC" size="1" />
<font color="#FF0000"><b>総合計消費Pt</b></font><br />
<font color="#FF0000"><? print($total_point); ?>Pt</font><br />
<font color="#FF0000"><b>TOTAL売り上げ</b></font><br />
<font color="#FF0000"><? print($paydata['total_amount']); ?>円</font><br />


<? if($sitedata['use_bank'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[1]); ?><br />
<? print($paydata['m_bank']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[1]); ?><br />
<? print($paydata['f_bank']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[1]); ?><br />
<? print($paydata['n_bank']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[1]); ?>清算</font><br />
<? print($paydata['def_m_bank']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[1]); ?>清算</font><br />
<? print($paydata['def_f_bank']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[1]); ?>清算</font><br />
<? print($paydata['def_n_bank']); ?>円<br />

<? } ?>
<? if($sitedata['use_credit'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[2]); ?></font><br />
<? print($paydata['m_cre']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[2]); ?></font><br />
<? print($paydata['f_cre']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[2]); ?></font><br />
<? print($paydata['n_cre']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[2]); ?>清算</font><br />
<? print($paydata['def_m_cre']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[2]); ?>清算</font><br />
<? print($paydata['def_f_cre']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[2]); ?>清算</font><br />
<? print($paydata['def_n_cre']); ?>円<br />

<? } ?>
<? if($sitedata['use_bit'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[3]); ?></font><br />
<? print($paydata['m_bit']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[3]); ?></font><br />
<? print($paydata['f_bit']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[3]); ?></font><br />
<? print($paydata['n_bit']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[3]); ?>清算</font><br />
<? print($paydata['def_m_bit']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[3]); ?>清算</font><br />
<? print($paydata['def_f_bit']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[3]); ?>清算</font><br />
<? print($paydata['def_n_bit']); ?>円<br />

<? } ?>
<? if($sitedata['use_ccheck'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[5]); ?></font><br />
<? print($paydata['m_ccheck']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[5]); ?></font><br />
<? print($paydata['f_ccheck']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[5]); ?></font><br />
<? print($paydata['n_ccheck']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[5]); ?>清算</font><br />
<? print($paydata['def_m_ccheck']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[5]); ?>清算</font><br />
<? print($paydata['def_f_ccheck']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[5]); ?>清算</font><br />
<? print($paydata['def_n_ccheck']); ?>円<br />

<? } ?>
<? if($sitedata['use_direct'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[4]); ?></font><br />
<? print($paydata['m_direct']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[4]); ?></font><br />
<? print($paydata['f_direct']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[4]); ?></font><br />
<? print($paydata['n_direct']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[4]); ?>清算</font><br />
<? print($paydata['def_m_direct']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[4]); ?>清算</font><br />
<? print($paydata['def_f_direct']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[4]); ?>清算</font><br />
<? print($paydata['def_n_direct']); ?>円<br />

<? } ?>
<? if($sitedata['use_fregi'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[6]); ?></font><br />
<? print($paydata['m_fregi']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[6]); ?></font><br />
<? print($paydata['f_fregi']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[6]); ?></font><br />
<? print($paydata['n_fregi']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[6]); ?>清算</font><br />
<? print($paydata['def_m_fregi']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[6]); ?>清算</font><br />
<? print($paydata['def_f_fregi']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[6]); ?>清算</font><br />
<? print($paydata['def_n_fregi']); ?>円<br />

<? } ?>

<? if($sitedata['use_edy'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[8]); ?></font><br />
<? print($paydata['m_edy']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[8]); ?></font><br />
<? print($paydata['f_edy']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[8]); ?></font><br />
<? print($paydata['n_edy']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[8]); ?>清算</font><br />
<? print($paydata['def_m_edy']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[8]); ?>清算</font><br />
<? print($paydata['def_f_edy']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[8]); ?>清算</font><br />
<? print($paydata['def_n_edy']); ?>円<br />

<? } ?>

<? if($sitedata['use_smoney'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[10]); ?></font><br />
<? print($paydata['m_smoney']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[10]); ?></font><br />
<? print($paydata['f_smoney']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[10]); ?></font><br />
<? print($paydata['n_smoney']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[10]); ?>清算</font><br />
<? print($paydata['def_m_smoney']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[10]); ?>清算</font><br />
<? print($paydata['def_f_smoney']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[10]); ?>清算</font><br />
<? print($paydata['def_n_smoney']); ?>円<br />

<? } ?>


<? if($sitedata['use_giga'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[11]); ?></font><br />
<? print($paydata['m_giga']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[11]); ?></font><br />
<? print($paydata['f_giga']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[11]); ?></font><br />
<? print($paydata['n_giga']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[11]); ?>清算</font><br />
<? print($paydata['def_m_giga']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[11]); ?>清算</font><br />
<? print($paydata['def_f_giga']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[11]); ?>清算</font><br />
<? print($paydata['def_n_giga']); ?>円<br />

<? } ?>

<? if($sitedata['use_convenic'] == 1){ ?>
<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[12]); ?></font><br />
<? print($paydata['m_convenic']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[12]); ?></font><br />
<? print($paydata['f_convenic']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[12]); ?></font><br />
<? print($paydata['n_convenic']); ?>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■<? print($settle_name_array[12]); ?>清算</font><br />
<? print($paydata['def_m_convenic']); ?>円<br />
<font color="#FF3366">■<? print($settle_name_array[12]); ?>清算</font><br />
<? print($paydata['def_f_convenic']); ?>円<br />
<font color="#333333">■<? print($settle_name_array[12]); ?>清算</font><br />
<? print($paydata['def_n_convenic']); ?>円<br />

<? } ?>


<hr color="#CCCCCC" size="1" />
<font color="#009966">■後払い利用額</font><br />
<? print($defdata['m_def']); ?></b>円<br />
<font color="#FF3366">■後払い利用額</font><br />
<? print($defdata['f_def']); ?></b>円<br />
<font color="#333333">■後払い利用額</font><br />
<? print($defdata['n_def']); ?></b>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■回収額</font><br />
<? print($defdata['m_def_paid']); ?></b>円<br />
<font color="#FF3366">■回収額</font><br />
<? print($defdata['f_def_paid']); ?></b>円<br />
<font color="#333333">■回収額</font><br />
<? print($defdata['n_def_paid']); ?></b>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#009966">■回収率</font><br />
<? print($defdata['m_ratio']); ?></b>％<br />
<font color="#FF3366">■回収率</font><br />
<? print($defdata['f_ratio']); ?></b>％<br />
<font color="#333333">■回収率</font><br />
<? print($defdata['n_ratio']); ?></b>％<br />




<hr color="#CCCCCC" size="1" />
メールしない入無<br />
<? print($registnum['nomail_nopay']); ?>人<br />
メールしない入有<br />
<? print($registnum['nomail_pay']); ?>人<br />

<hr color="#CCCCCC" size="1" />
<font color="#FF0000"><b>総合後払い金</b></font><br />
<font color="#FF0000"><? print($defdata['def_total']); ?></font>円<br />
<font color="#FF0000"><b>総合回収額</b></font><br />
<font color="#FF0000"><? print($defdata['def_paid_total']); ?></font>円<br />

<hr color="#CCCCCC" size="1" />
<font color="#FF0000"><b>総合回収率</b></font><br />
<font color="#FF0000"><? print($defdata['def_total_ratio']); ?></font>％<br />



</font>

<?
################################# HTML FOOTER ###################################

$html_class->htmlFooter();

##################################### END #######################################
?>
