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
$check_date		= date("Ymd");

# SITE DATA
$sitedata	= $siteinfos->getSiteInfo($search_site);
$disp_site	= $siteinfos->getSiteName($search_site);
define("SITE_NAME",$disp_site);

################################ MAIN SQL ###################################


$select = 'id,op_name';
$where  = 'status = 0';
if($search_site != 'all'){
	$where .= ' AND site_cd = '.$search_site;
}
$order  = 'op_group ASC';
$limit  = '';
$rtn    = $db->selectDb('optbls',$select,$where,$order,$limit);
$db->errorDb('',$db->errno,__FILE__,__LINE__);
$i=0;
while($data = $db->fetchAssoc($rtn)){

	$disp_data .= "<font color=\"#009966\">【".$data['op_name']."】</font><br />\n";

	$op_total	= 0;
	for($h=0;$h<24;$h++){
		$hour = $h;
		if($h < 10){ $hour = "0".$h; }

		# 削りポイント
		$sumpt  = $calc->SumConsumption($check_date.$hour.'0000',$check_date.$hour.'5959',$search_site,$data['id'],$_REQUEST['domain_flg'],$_REQUEST['ad_code'],$_REQUEST['ad_code_type']);
		$op_total += $sumpt;
		$total_pt += $sumpt;
		$hour_pt[$h] += $sumpt;

		$disp_data .= "[".$hour."時]<font color=\"#000000\">".$sumpt."</font>PT<br />\n";

		if($h == 24){ die("回りすぎ"); }
	}

	$disp_data .= "<font color=\"#FF0000\">TOTAL : ".$op_total."PT</font><br />\n";
	$disp_data .= "<hr size=\"1\" color=\"#CCCCCC\" />";


}



################################ CLOSE DATABASE #################################

$db->closeDb();

################################# HTML HEADER ###################################

$html_class->htmlHeader();

################################## HTML BODY ####################################
?>

<div align="center">
<font size="3">
<? print(date("Y")); ?>年<? print(date("m")); ?>月<? print(date("d")); ?>日<br />
<? print(SITE_NAME); ?>時間毎削り一覧
</font>
</div>
<hr />


<font size="2" color="#666666">
<div align="center"><font color="#FF3366" size="3">全体</font></div>
<hr />
<?
$count	= count($hour_pt);
for($i=0;$i<$count;$i++){

	print($i."時 -- ");
	print("<font color=\"#000000\">".$hour_pt[$i]."</font>PT<br />");

}
?>
<br />

<div align="center"><font color="#FF0000">TOTAL : <? print($total_pt); ?>PT</font></div><br />

<hr />
<div align="center"><font color="#FF3366" size="3">アタッカー別</font></div>
<hr />
<? print($disp_data); ?>

</font>

<?
################################# HTML FOOTER ###################################

$html_class->htmlFooter();

##################################### END #######################################
?>
