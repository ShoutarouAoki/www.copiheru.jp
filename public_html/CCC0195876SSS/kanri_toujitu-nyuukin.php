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

require_once(dirname(__FILE__)."/user_agent.php");


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
$sta_date2		= date("Ymd")."000000";
$end_date2		= date("Ymd")."235959";

# SITE DATA
$sitedata	= $siteinfos->getSiteInfo($search_site);
$disp_site	= $siteinfos->getSiteName($search_site);
define("SITE_NAME",$disp_site);

################################ MAIN SQL ###################################


$payselect = "COUNT(id) AS payscnt,user_id,settlement_id,SUM(pay_amount) AS amount,pay_date";
$paywhere  = "pay_date >= ".$sta_date." AND pay_date <= ".$end_date." AND def_flg = 0 AND clear = 1 ";
$paywhere .= "AND site_cd = ".$search_site." ";
#$paywhere .= "AND reg_date >= ".$sta_date_2." AND reg_date <= ".$end_date_2." AND pay_amount > 0 ";
$paywhere .= "AND status = 0 GROUP BY user_id ";
$payrtn    = $db->selectDb('pays',$payselect,$paywhere,$payorder,"");
$db->errorDb("",$db->errno,__FILE__,__LINE__);

$paycnt = $db->numRows($payrtn);

$i=0;
while($paydata = $db->fetchAssoc($payrtn)){

	$memselect = "nickname,point,site_cd,sex,def_flg,ad_code,pay_count,reg_date,last_pay_date,id,first_pay_date";
	$memwhere  = "id = ".$paydata['user_id'];
	$memberrtn = $db->selectDb('members',$memselect,$memwhere,"","");
	$db->errorDb("",$db->errno,__FILE__,__LINE__);
	$memberdata = $db->fetchAssoc($memberrtn);

	$first_pay	= date("YmdHis", strtotime($memberdata['first_pay_date']));

	if($first_pay >= $sta_date && $first_pay <= $end_date){
		$member_id = "<font color=\"#FF0000\">".$paydata['user_id']."</font>";
	}else{
		$member_id = $paydata['user_id'];
	}

	if($memberdata['sex'] == 1){
		$user_name	= "<font color=\"#0099CC\">".$memberdata['nickname']."</font>";
	}elseif($memberdata['sex'] == 2){
		$user_name	= "<font color=\"#FF3366\">".$memberdata['nickname']."</font>";
	}else{
		$user_name	= $memberdata['nickname'];
	}


	/*
	if($paydata['amount'] >= 100000){
		$user_payamount	= "<marquee SCROLLDELAY=\"200\" SCROLLAMOUNT=\"80\" width=\"80\">&nbsp;<span class=\"bold_pink\">".$paydata['amount']."</span>円</marquee>";
	}elseif($paydata['amount'] >= 50000){
		$user_payamount	= "<span class=\"bold_green\">".$paydata['amount']."</span>円";
	}elseif($paydata['amount'] >= 30000){
		$user_payamount	= "<span class=\"bold_blue\">".$paydata['amount']."</span>円";
	}elseif($paydata['amount'] >= 10000){
		$user_payamount	= "<span class=\"bold_red\">".$paydata['amount']."</span>円";
	}else{
		$user_payamount	= $paydata['amount'];
	}
	*/

	$disp_data	.= "【ID】".$member_id."【NAME】".$user_name."<br />【登録】".$memberdata['reg_date']."<br />【CODE】".$memberdata['ad_code']."<hr />";


	$i++;

}

################################ CLOSE DATABASE #################################

$db->closeDb();

################################# HTML HEADER ###################################

$html_class->htmlHeader();

################################## HTML BODY ####################################
?>

<div align="center">
<font size="3">
<? print(date("Y")); ?>年<? print(date("m")); ?>月<? print(date("d")); ?>日 
<? print(SITE_NAME); ?>日毎入金者
</font>
</div>
<hr color="#CCCCCC" size="1" />

<font size="2">

<? print($disp_data); ?>

</font>

<?
################################# HTML FOOTER ###################################

$html_class->htmlFooter();

##################################### END #######################################
?>
