<?php
/*************************************************************************
应用版本号判断，包含应用市场和SDK的版本号判断
**************************************************************************/
namespace Auxclass\App;
use Auxclass\Cache\Memcache;
use Auxclass\Translation\AppConfigOperate;

class Version{
	/****************************************************************************************************************************************
	$compareVersion 要进行比较的版本号。如果为空，返回当前的版本号。
	但是比较版本号的时候，小于$compareVersion 返回-1，大于$compareVersion返回1，等于$compareVersion就返回0
	这里是比较SDK的版本
	-----------------------------------------------------------------------------------------------------------------------------------------
	在SDK2.0之前版本（最后一段表示IDAV）：
	IOS：		SMSDK/1.0/版本号（没有v的）/iPhone OS/8.4/iPhone/BF9EB548-ED98-4288-8C3F-EAA220C2ED31/96A7A2F5-1E11-414A-964A-EC8C176212C3
	Android：	SMSDK/1.0.1/v1.0.6/Android/6.0/aloneuuid/aloneidav
	------------------------------------------------------------------------------------------------------------------------------------------
	在SDK2.0之后和2.0版本：
	IOS：		SMSDK    /2.5.3  /iOS     /10.2       /iPhone    /iPhone 6     			/uuid      	/idav
	Android：	SMSDK    /2.5.3  /Android /5.1.1      /phone     /philippeltechn        /uuid      /idav
	===================================================================================================================================
	IOS：		SMSDK    /2.5.3  /iOS     /10.2       /iPhone    /iPhone 6              /uuid       /idav
	Android：	SMSDK   /2.5.3   /Android /5.1.1      /phone     /philippeltechn        /kooyuyuuuid/kooyuyuidav
	========================================================================================================================
	(SMSDK)    (sdk版本不需要加v)   (ios/android)  (系统版本)   (phone/pad) (设备型号)  (uuid)  (idfa)
	*****************************************************************************************************************************************/
	public static function get_compare_sdk_version($compareVersion=""){
		if(empty($_SERVER) || !isset($_SERVER["HTTP_USER_AGENT"])){
			if(empty($compareVersion)) return "";
			return -1;
		}
		$userAgentString=$_SERVER["HTTP_USER_AGENT"];
		$userAgentArr=explode("/",$_SERVER["HTTP_USER_AGENT"]);
		/****************************************************************
		对于旧版本的SDK，无论是IOS的还是Android的都是第三截作为版本号码。
		*****************************************************************/
		if(IsApple($userAgentString)){
			$versionString=preg_match("/SMSDK\/[\d\.]+\/[\d\.]+\/iPhone\s+OS\/[\d\.]+\/iPhone\/[\w\-]+\/[\w\-]+/i",$userAgentString) ? $userAgentArr[2] : $userAgentArr[1];
			return self::version_num_compare($versionString,$compareVersion);
		}
		/*****************************************************************************
		判断是否是旧版本的Android，SDK。对于旧版本的AndroidSDK是取得第三位。作为版本号
		******************************************************************************/
		$versionString=preg_match("/SMSDK\/[\d\.]+\/v[\d\.]+\/Android\/[\d\.]+\/\w+\/\w+/i",$userAgentString) ? $userAgentArr[2] : $userAgentArr[1];
		return self::version_num_compare($versionString,$compareVersion);
	}

	/****************************************************************************
	$compareVersion 要进行比较的版本号。如果为空，返回当前的版本号。
	但是比较版本号的时候，小于$compareVersion 返回-1，大于$compareVersion返回1
	这里是比较应用市场App的版本
	*****************************************************************************/
	public static function get_compare_app_version($compareVersion=""){
		if(empty($_SERVER) || !isset($_SERVER["HTTP_USER_AGENT"])){
			if(empty($compareVersion)) return "";
			return -1;
		}
		$userAgentArr=explode("/",$_SERVER["HTTP_USER_AGENT"]);
		$version1=$userAgentArr[1];
		if(empty($compareVersion)){
			return $version1;
		}
		return self::version_num_compare($version1,$compareVersion);
	}

	/**********************************************************************
	版本号比较
	$version1	版本号1 可以包含字母
	$version2	版本号2	可以包含字母
	返回值 -1 和1 和0
	版本信息比较，$version1 > $version2 返回1 $version1 < $version2 返回-1
	***********************************************************************/
	public static function version_num_compare($version1,$version2,$separator="."){
		$version1=preg_replace("/^\s+|[a-z]|\s+$/i","",$version1);
		$version2=preg_replace("/^\s+|[a-z]|\s+$/i","",$version2);
		//排除版本号相同的情况
		if($version1==$version2){
			return 0;
		}
		$version1Arr=explode($separator,$version1);
		$version2Arr=explode($separator,$version2);
		$max_length=count($version1Arr) > count($version2Arr) ? count($version1Arr) : count($version2Arr);
		$version1Arr=array_pad($version1Arr,$max_length,0);
		$version2Arr=array_pad($version2Arr,$max_length,0);
		for($i=0;$i<count($version1Arr);$i++){
			if($version1Arr[$i]==$version2Arr[$i]){
				continue;
			}
			return $version1Arr[$i]>$version2Arr[$i] ? 1 : -1;
		}
		return 0;
	}

	/***********************
	判断是应用市场
	************************/
	public static function is_market_app(){
		$userAgentString=@$_SERVER["HTTP_USER_AGENT"];
		if(empty($userAgentString)) return false;
		return preg_match("/^X7Market/i",$userAgentString) ? true : false;
	}

    /***********************
    判断是否是海外应用市场
     ************************/
    public static function is_hwmarket_app(){
        $userAgentString=@$_SERVER["HTTP_USER_AGENT"];
        if(empty($userAgentString)) return false;
        return preg_match("/^X7HWMarket/i",$userAgentString) ? true : false;
    }

	/***********************
	判断是否是SDK
	************************/
	public static function is_sdk_app(){
		$userAgentString=@$_SERVER["HTTP_USER_AGENT"];
		if(empty($userAgentString)) return false;
		return preg_match("/^SMSDK/i",$userAgentString) ? true : false;
	}

    /****************************************
     * 判断是否是香港SDK
     * -The Author Of ALone 2018-09-07 10:18
     ****************************************/
    public static function is_hksdk_app(){
        $userAgentString=UserAgent::get_user_agent();
        if(empty($userAgentString)) return false;
        return preg_match("/^HKSDK/i",$userAgentString) ? true : false;
    }

    /****************************************
     * 判断是否是海外SDK
     * -The Author Of ALone 2019-03-18 10:53
     ****************************************/
    public static function is_hwsdk_app(){
        $userAgentString=UserAgent::get_user_agent();
        if(empty($userAgentString)) return false;
        return preg_match("/^HWSDK/i",$userAgentString) ? true : false;
    }

    /******************************************
     * 获取当前SDK的版本号码
     * @return string
     * ---The Author Of ALone 2019-03-01 15:54
     *****************************************/
    public static function get_sdk_version(){
        $useragent_string=$_SERVER["HTTP_USER_AGENT"];
        $useragent_arr=Common::get_string_explode_arr($useragent_string,"/");
        if(empty($useragent_arr[1]) || empty($useragent_arr[2])){
            return "0.00";
        }
        if(UserAgent::is_apple($useragent_string)){
            return preg_match("/SMSDK\/[\d\.]+\/[\d\.]+\/iPhone\s+OS\/[\d\.]+\/iPhone\/[\w\-]+\/[\w\-]+/i",$useragent_string) ? $useragent_arr[2] : $useragent_arr[1];
        }
        return preg_match("/SMSDK\/[\d\.]+\/v[\d\.]+\/Android\/[\d\.]+\/\w+\/\w+/i",$useragent_string) ? $useragent_arr[2] : $useragent_arr[1];
    }

    /****************************************
     * 获取当前应用市场的版本号
     * @return string
     * -The Author Of ALone 2019-03-01 15:54
     ****************************************/
    public static function get_market_version(){
        $useragent_arr=Common::get_string_explode_arr($_SERVER["HTTP_USER_AGENT"],"/");
        !isset($useragent_arr[1]) && $useragent_arr[1]="0.00";
        return $useragent_arr[1];
    }

    /**
     * 判断当前是否为海外app （应用市场或sdk）
     * @return bool
     */
    private static function is_hw_app()
    {
        if (self::is_hwmarket_app() || self::is_hwsdk_app()) {   //如果不是海外的就默认返回大陆地区
            return true;
        } else {
            return false;
        }
    }

    /**
     * 通过头信息获取是否海外字段  --- 20190531 gzq
     * 通过头信息获取当前地区  ---- 20190929 czj
     * @return int -1:大陆、1：台湾、2：香港
     */
    public static function get_foreign_type(){
        if (!self::is_hw_app()) {  //不是海外返回中国大陆
            return -1;
        }

        //先获取i18n -- by czj 20190929
        $i18nArr = self::getI18nInfo();

        //先查看当前app地区 再看设备地区
        if (!empty($i18nArr)) {
            $i18nForeign = -10; //-10为未知
            if (isset($i18nArr[4]) && !empty($i18nArr[4])) { //app选择的地区
                $i18nForeign = $i18nArr[4];
            }
            if ($i18nForeign == -10 && isset($i18nArr[2]) && !empty($i18nArr[2])) { //设备地区
                    $i18nForeign = $i18nArr[2];
            }
            
            $ci = & get_instance();
            if ($i18nForeign != -10 && array_key_exists($i18nForeign, AppConfigOperate::getAreaType($ci, -1))) {
                return $i18nForeign;
            }
        }

        //再兼容原本数据
        $userAgentString=@$_SERVER["HTTP_USER_AGENT"];
        //没有头信息或没有匹配到头信息或匹配到大陆的头信息
        if(empty($userAgentString) || !preg_match(UserAgentRegEx,$userAgentString) || preg_match(UserAgentDlRegEx,$userAgentString)){
            return -1;
        }
        //香港用户
        if(preg_match(UserAgentHkRegEx,$userAgentString)){
            return 2;
        }
        //台湾用户
        if(preg_match(UserAgentTwRegEx,$userAgentString)){
            return 1;
        }
        return -1;
    }

    /**
     * 获取当前时区
     * by czj 20190929
     * 约定值范围为 GMT+0到GMT-11
     * @return String
     */
    public static function getI18nTimezone()
    {
        $i18nArr = self::getI18nInfo();
        if (!empty($i18nArr) && isset($i18nArr[3]) && !empty($i18nArr[3])) {
            $i18nForeign = $i18nArr[3];
            if ($i18nForeign != -10 && preg_match("/^GMT([+|-]?)(([0-1][0-1])|([0]?[0-9]))(:00)?$/i", $i18nForeign)) {
                return $i18nForeign;
            }
        }

        return "GMT8";
    }

    /**
     * 获取当前系统语言  => (设备>地区默认语言>中文简体)
     *  by czj 20190929
     * @return int
     */
    public static function getI18nLanguage()
    {
        $language = 1;
        if (!self::is_hw_app()) {   //不是海外返回中文简体
            return $language;
        }

        $allLanguages = AppConfigOperate::$languageType;

        /**
         * 获取设备语言,如果系统支持设备语言就返回设备语言
         */
        $i18nArr = self::getI18nInfo();
        if (!empty($i18nArr) && isset($i18nArr[1]) && !empty($i18nArr[1])) {
            if ($i18nArr[1] != -10 && array_key_exists($i18nArr[1], $allLanguages)) {
                return $i18nArr[1];
            }
        }

        /**
         * 设备语言不支持，获取地区配置语言
         */
        //获取当前地区
        $foreign = self::get_foreign_type();

        //获取当前地区的语言
        $cacheKey = "i18nRegionsLanguageCache_czj";
        $regions = Memcache::get_all_domain_cache($cacheKey);
        if (empty($regions)) {
            $ci = &get_instance();
            $ci->getmodel('I18n_regions_model');
            $regions = $ci->I18n_regions_model->get_have_data_read_write(array(), "name, is_foreign, language_id", true);
            if (empty($regions)) {
                return $language;
            }
            Memcache::set_all_domain_cache($cacheKey,$regions);
        }
        $regionInfo = array_filter($regions, function ($value) use ($foreign) { //找到当前设备地区信息
            return $value['is_foreign'] == $foreign;
        });
        $regionInfo = array_shift($regionInfo);
        if (!empty($regionInfo) && array_key_exists($regionInfo['language_id'], $allLanguages)) {
            return $regionInfo['language_id'];
        }

        return $language;
    }

    /**
     *获取头信息i18n字段信息  1/1/-1/GMT8/-1
     * i18n:当前APP默认语言/设备系统语言/设备地区/设备时区/APP指定地区    App默认语言  = (设备>地区默认语言>中文简体)  App地区(APP指定地区>设备地区)
     * @return array(当前App默认语言,设备系统语言,设备地区,设备时区)        语言这块还要和产品确认下默认语言等信息
     */
    public static function getI18nInfo()
    {
        if (isset($_SERVER['HTTP_I18N']) && !empty($_SERVER['HTTP_I18N'])) {
            $i18nString = $_SERVER['HTTP_I18N'];
            $i18nArr = explode("/", $i18nString);
            return $i18nArr;
        } else { //客户端没有上传i18n头信息
            return array();
        }
    }


}

