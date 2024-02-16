<?php
/********************************************************************************
**	
**	AppendixModel.php
**	=============================================================================
**
**	■PAGE / 
**	APPENDIX MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	SHOP CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	SHOP CLASS
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: KARAT SYSTEM
**	CREATE DATE : 2016/10/03
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
class AppendixModel
{
	# VAR
	private $db;
	private $site_cd;
	private $table;
	
	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**	@database接続クラス	読み込み
	**	@site_cd			読み込み
	**
	**************************************************/

	# CONSTRUCT
	public function __construct($database=NULL,$main=NULL){

		$this->db			= $database;
		$this->output		= $main;
		$this->table = "appendixes";
    }

	# DESTRUCT
	function __destruct(){

    }
	
	/*********************************************
	**
	**	appendixデータ取得（キャンペーンIDで）
	**
	*********************************************/

	public function getAppendixData($campaign_id,$shop_id){

		if(empty($campaign_id)){
			return FALSE;
		}

		# DB / MAIN CLASS
		$database = NULL;
		$database = $this->db;
		$output = NULL;
		$output = $this->output;

		# PARAMETER
		$array = array();
		$array[':site_cd'] = SITE_CD;
		$array[':campaign_id'] = $campaign_id;
		$array[':shop_id'] = $shop_id;

		$column	= "*";
		$where					 = "site_cd = :site_cd AND campaign_id = :campaign_id AND shop_id = :shop_id ";
		$order					 = "id";
		$limit					 = NULL;
		$group					 = NULL;
		$rtn					 = $database->selectDb($this->table,$column,$where,$array,$order,$limit,$group);
		$error					 = $database->errorDb("getAppendixData",$rtn->errorCode(),__FILE__,__LINE__);
		if(!empty($error)){ $output->outputError($error); }

		$list = array();
		while($data = $this->db->fetchAssoc($rtn)){
			$list[] = $data;
		}

		$database->freeResult($rtn);
		
		return $list;

	}
	
	//CLASS-END
}

?>