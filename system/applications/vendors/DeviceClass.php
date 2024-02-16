<?php
/********************************************************************************
**	
**	DeviceClass.php
**	=============================================================================
**
**	■PAGE / 
**	DEVICE MODELS
**
**	=============================================================================
**
**	■MEANS / 
**	DEVICE CLASS FUNCTION 処理 / 読み込み / 呼び出し
**	DEVICE判別処理
**
**	=============================================================================
**
**	■ CHECK / 
**	AUTHOR		: AKITOSHI TAKAI
**	CREATE DATE : 2012/12/01
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


# CLASS DEFINE
class DeviceClass{


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**************************************************/

	# VAR
	private	$device;
	private	$num;
	private	$lang;
	private $os;

	# CONSTRUCT
	function __construct(){

		# ACCESS CARRIER
		$user_agent			= $_SERVER['HTTP_USER_AGENT'];
		$this->browser		= $user_agent;

		# DOCOMO
		if(preg_match("/^DoCoMo\//",$user_agent)){
			$this->device	= "DoCoMo";
			$this->num		= "-wap-input-format:&quot;*&lt;ja:n&gt;&quot;";
			$this->lang		= "-wap-input-format:&quot;*&lt;ja:en&gt;&quot;";
			$this->os		= 0;
		# J-PHONE
		}elseif(preg_match("/J-PHONE\//",$user_agent)){
			$this->device	= "JPHONE";
			$this->num		= "-wap-input-format:&quot;*&lt;ja:n&gt;&quot;";
			$this->lang		= "-wap-input-format:&quot;*&lt;ja:en&gt;&quot;";
			$this->os		= 0;
		# Vodafone
		}elseif(preg_match("/Vodafone/",$user_agent)){
			$this->device	= "JPHONE";
			$this->num		= "-wap-input-format:&quot;*&lt;ja:n&gt;&quot;";
			$this->lang		= "-wap-input-format:&quot;*&lt;ja:en&gt;&quot;";
			$this->os		= 0;
		# SoftBank
		}elseif(preg_match("/SoftBank/",$user_agent)){
			$this->device	= "SoftBank";
			$this->num		= "-wap-input-format:&quot;*&lt;ja:n&gt;&quot;";
			$this->lang		= "-wap-input-format:&quot;*&lt;ja:en&gt;&quot;";
			$this->os		= 0;
		# AU
		}elseif(preg_match("/^KDDI-/",$user_agent)){
			$this->device	= "KDDI";
			$this->num		= "-wap-input-format:*N;";
			$this->lang		= "-wap-input-format:*m;";
			$this->os		= 0;
		# EMOBILE
		}elseif(preg_match("/^emobile/",$user_agent)){
			$this->device	= "emobile";
			$this->num		= "-wap-input-format:*N;";
			$this->lang		= "-wap-input-format:*m;";
			$this->os		= 0;
		# WILLCOM
		}elseif(preg_match("/^Mozilla.+(DDIPOCKET|WILLCOM)/",$user_agent)){
			$this->device	= "Willcom";
			$this->num		= "-wap-input-format:*N;";
			$this->lang		= "-wap-input-format:*m;";
			$this->os		= 0;
		# OTHER
		}elseif(preg_match("/^UP.Browser\//",$user_agent)){
			$this->device	= "KDDI";
			$this->num		= "-wap-input-format:*N;";
			$this->lang		= "-wap-input-format:*m;";
			$this->os		= 0;
		# IPHONE
		}elseif(preg_match("/iPhone|iPod/i",$user_agent)){
			$this->device	= "iPhone";
			$this->num		= NULL;
			$this->lang		= NULL;
			$this->os		= 1;
		# SMART
		}elseif(preg_match("/Opera Mini/i",$user_agent)){
			$this->device	= "SmartPhone";
			$this->num		= NULL;
			$this->lang		= NULL;
			$this->os		= 2;
		# ANDROID
		}elseif(preg_match("/Android/",$user_agent)){
			$this->device	= "Android";
			$this->num		= NULL;
			$this->lang		= NULL;
			$this->os		= 2;
		# IPAD
		}elseif(preg_match("/iPad/i",$user_agent)){
			$this->device	= "PC";
			$this->num		= NULL;
			$this->lang		= NULL;
			$this->os		= 0;
		# PC
		}else{
			$this->device	= "PC";
			$this->num		= NULL;
			$this->lang		= NULL;
			$this->os		= 0;
		}

    }

	# DESTRUCT
	function __destruct(){
		
    }



	/************************************************
	**
	**	getDeviceType
	**	---------------------------------------------
	**	RETURN DEVICE TYPE
	**
	************************************************/

	public function getDeviceType(){

		return $this->device;

	}



	/************************************************
	**
	**	getDeviceMode
	**	---------------------------------------------
	**	RETURN DEVICE MODE
	**
	************************************************/

	public function getDeviceMode(){

		$result['numeric']	= $this->num;
		$result['alphabet']	= $this->lang;

		return $result;

	}



	/************************************************
	**
	**	getIncludeDirectry
	**	---------------------------------------------
	**	GET INCLUDE DIR
	**
	************************************************/

	public function getIncludeDirectry(){

		if($this->device == "PC"){
			$result	= "pc";
		}elseif($this->device == "iPhone" || $this->device == "SmartPhone" || $this->device == "Android"){
			$result	= "smart";
		}else{
			$result	= "mobile";
		}

		return $result;

	}



	/************************************************
	**
	**	getDeviceNumber
	**	---------------------------------------------
	**	GET DEVICE NUMBER
	**
	************************************************/

	public function getDeviceNumber(){

		if($this->device == "PC"){
			$result	= 1;
		}elseif($this->device == "iPhone" || $this->device == "SmartPhone" || $this->device == "Android"){
			$result	= 2;
		}else{
			$result	= 3;
		}

		return $result;

	}



	/************************************************
	**
	**	getDeviceNumberByFile
	**	---------------------------------------------
	**	GET DEVICE NUMBER
	**
	************************************************/

	public function getDeviceNumberByFile($device){

		if($device == "pc"){
			$result	= 1;
		}elseif($device == "smart"){
			$result	= 2;
		}else{
			$result	= 3;
		}

		return $result;

	}



	/************************************************
	**
	**	getOsNumberByFile
	**	---------------------------------------------
	**	GET OS NUMBER
	**
	************************************************/

	public function getOsNumberByFile($device,$os){

		$result			= 0;

		if($device == "smart"){
			if($os == 1){
				$result	= 1;
			}elseif($os == 2){
				$result	= 2;
			}else{
				$result	= 1;
			}
		}

		return $result;

	}



	/************************************************
	**
	**	getOsNumber
	**	---------------------------------------------
	**	GET OS NUMBER
	**
	************************************************/

	public function getOsNumber(){

		return $this->os;

	}



	/************************************************
	**
	**	getOsVersion
	**	---------------------------------------------
	**	GET OS VERSION
	**
	************************************************/

	public function getOsVersion(){

		$result		= 0;

		if($this->device == "iPhone"){

			preg_match("/(iPhone\sOS\s([0-9\.]*))/", $this->browser, $str);

			if (count($str)){



				$version				= $str[2];
				$version				= trim($version);

				if(preg_match("/_/", $version)){
					list($major,$minor)	= explode("_", $version);
				}else{
					$major				= $version;
				}

				if(!empty($major)){
					$result				= $major;
				}

			}

		}

		return $result;

	}



	/************************************************
	**
	**	getMobileDevice
	**	---------------------------------------------
	**	MOBILE DEVICE
	**
	************************************************/

	public function getMobileDevice(){

		global	$mobile_domain_array;

		$count	= count($mobile_domain_array);

		$result	= "<select name=\"mobile_domain\">\n";
		for($i=0;$i<$count;$i++){
			$result	.= "<option value=\"".$mobile_domain_array[$i][0]."\">".$mobile_domain_array[$i][0]."</option>\n";
		}

		$result	.= "</select>\n";

		print($result);

	}



	/************************************************
	**
	**	getUtnCode
	**	---------------------------------------------
	**	GET MOBILE UTN CODE
	**
	************************************************/

	public function getUtnCode(){

		if($_SERVER[HTTP_X_JPHONE_MSNAME]){

			if(ereg("/SN", $_SERVER[HTTP_USER_AGENT])){ 
				list($ka, $kb) = split("/SN", $_SERVER[HTTP_USER_AGENT]); list($result, $kg) = split(" ", $kb);
			}else{
				return FALSE;
			}

		}elseif(ereg("DoCoMo", $_SERVER[HTTP_USER_AGENT])){

			$result	= $_SERVER['HTTP_X_DCMGUID'];

		}else{

			if($_SERVER[HTTP_X_UP_SUBNO]){ 
				list($result, $kb) = explode("_", $_SERVER[HTTP_X_UP_SUBNO], 2) ;
			}else{
				return FALSE;
			}

		}

		return $result;

	}



	/************************************************
	**
	**	getUserBrowser
	**	---------------------------------------------
	**	GET USER BROWSER
	**
	************************************************/

	public function getUserBrowser(){

		if(preg_match('/MSIE/i',$this->browser) && !preg_match('/Opera/i',$this->browser)){
		    $result['name']		= 'Internet Explorer';
		    $result['browser']	= "ie";
		}elseif(preg_match('/Trident/i',$this->browser) && !preg_match('/Opera/i',$this->browser)){
		    $result['name']		= 'Internet Explorer';
		    $result['browser']	= "ie";
		}elseif(preg_match('/Firefox/i',$this->browser)){
		    $result['name']		= 'Mozilla Firefox';
		    $result['browser']	= "firefox";
		}elseif(preg_match('/Chrome/i',$this->browser)){
		    $result['name']		= 'Google Chrome';
		    $result['browser']	= "chrome";
		}elseif(preg_match('/Safari/i',$this->browser)){
		    $result['name']		= 'Apple Safari';
		    $result['browser']	= "safari";
		}elseif(preg_match('/Opera/i',$this->browser)){
		    $result['name']		= 'Opera';
		    $result['browser']	= "opera";
		}elseif(preg_match('/Netscape/i',$this->browser)){
		    $result['name']		= 'Netscape';
		    $result['browser']	= "netscape";
		}else{
			$result['name']		= NULL;
			$result['browser']	= NULL;
		}

		return $result;

	}


}

?>