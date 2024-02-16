<?
# siteinfosに関するところ

require_once(dirname(__FILE__)."/../CONF/config.php");

class siteinfos{

	function getAllSiteInfo($site_cd=''){

		global $db;

		$select = "SELECT id,site_name FROM siteinfos WHERE status = '0'";
		$rtn    = $db->query($select);
		$db->errorDb($select,$db->errno,__FILE__,__LINE__);
		while($data = $db->fetchAssoc($rtn)){

			if($site_cd != ""){
				if($site_cd == $data['id']){
					$option .= "<option value=\"".$data['id']."\" selected>".$data['site_name']."</option>\n";
				}else{
					$option .= "<option value=\"".$data['id']."\">".$data['site_name']."</option>\n";
				}
			}else{
				$option .= "<option value=\"".$data['id']."\">".$data['site_name']."</option>\n";
			}
		}

		return($option);

	}


	function getSiteInfo($id){

		global $db;

		$select = "SELECT * FROM siteinfos WHERE id = '".$id."'";
		$rtn    = $db->query($select);
		$db->errorDb($select,$db->errno,__FILE__,__LINE__);
		$sitedata = $db->fetchAssoc($rtn);

		return($sitedata);

	}

	function getSiteName($id){

		global $db;

		if($id == ''){ return("Error"); }

		$select = "SELECT site_name FROM siteinfos WHERE id = '".$id."'";
		$rtn    = $db->query($select);
		$db->errorDb($select,$db->errno,__FILE__,__LINE__);
		$sitedata = $db->fetchAssoc($rtn);

		return($sitedata['site_name']);

	}


	function getSiteSettlement($id){

		global $db;

		$select = "SELECT use_bank,use_credit,use_bit,use_direct,use_ccheck,use_fregi FROM siteinfos WHERE id = '".$id."'";
		$rtn    = $db->query($select);
		$db->errorDb($select,$db->errno,__FILE__,__LINE__);
		$sitedata = $db->fetchAssoc($rtn);

		return($sitedata);

	}


}

?>
