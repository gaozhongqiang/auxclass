<?php
/***************
url处理函数文件
****************/
namespace Auxiliary\App;
class Url{
	public static $proxy_web_path_arr=array(
                                            "api"	=>"api_web_x7sy",
                                            "buy"	=>"buy_web_x7sy",
                                            "user"=>"user_web_x7sy",
                                        );
	/***********************************************************************
		$url 					表示包含控制器和方法 如login/index
		$param 				表示参数数组，键值对方式传递
		当前是对uri_helper.php文件中的function locationUrl函数进行修改。
	************************************************************************/
	public static function  location_url($url,$param=array()){
		redirect(self::url_recombination($url,$param),'location');
	}
	/***********************************************************************
		$url 					表示包含控制器和方法 如login/index
		$param 				表示参数数组，键值对方式传递
		$AdminFolder	表示后台目录文件夹，空的话表示前台操作
		当前是对uri_helper.php文件中的function UrlRecombination函数进行修改。
	************************************************************************/
	public static function url_recombination($url,$param=array(),$AdminFolder=ManageFolder){
		if(empty($url) || preg_match("/^https?\:\/\//i",$url)){
			return $url;
		}
		$query_strings=config_item("enable_query_strings");
		$directory=config_item("directory_trigger");
		$separator=$query_strings ? "&" : "/";
		$paramStr="";
		if(!empty($param)){
			$paramStr="?".http_build_query($param);
		}
		//判断是否是后台
		if(!empty($AdminFolder)){
			$url=$query_strings ? $directory."=".$AdminFolder."&".$url.$paramStr : $AdminFolder."/".trim($url,"/").$paramStr;
		}else{
			$url=$url.$paramStr;
		}
		return site_url($url);
	}
	
	/*************************************************************************
		返回当前页面的url，包含QueryString
		当前是对uri_helper.php文件中的function ReturnCurrentUrl函数进行修改。
	*************************************************************************/
	public static function get_current_url(){
		$nowurl=current_url();
		isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"]) && $nowurl.="?".$_SERVER["QUERY_STRING"];
		return $nowurl;
	}
	
	/*************************************************************************************************************************
	返回目前网站的主机地址，其实主要是为了区分是正式站还是测试站。
	就比如我在tg域名需要调pay域名的地址，按目前的情况只能返回当前域的主机比如当前是tg域名，通过WebSite返回tg.x7sy.com，
	所以我们要调用pay域名地址，只能是直接写上去，pay.msshuo.cn/xx/xxx 看到了吗？发现问题了吗？
	因为还需要在测试站中测试所以通常情况是写测试站地址，到正式站的时候还要将地址修改成正式站，但是如果我忘记了呢？
	----------------------------------------------------------------------------------The Author Of ALone 2017-08-04 11:57
	下面两个方法是对tool_helper.php文件中的function GetWebHost和function GetDomainWebSite 修改得到。
	**************************************************************************************************************************/
	public static function get_web_host($http_host=""){
		$http_host=empty($http_host) ? @$_SERVER["HTTP_HOST"] : $http_host;
		if(empty($http_host)) return "";
		return str_replace(DomainIdent.".","",$http_host);
	}
	public static function get_domain_website($domain="",$urlPath=""){
		$domain=empty($domain) ? "www" : $domain;
		return HTTP_Pre.$domain.".".self::get_web_host().$urlPath;
	}
	
	/*******************************************************************************
		将指定的数组的元素转换成querystring拼接到url后面
		$url				String		指定的url字符串
		$query_arr	Array			要进行转换成querystring的数组
		$is_encode	Integer		表示是否需要对querystring进行编码，默认是0表示不编码
		当前方法是对uri_helper.php文件中的function ReturnQueryStringUrl修改得到。
	*********************************************************************************/
	public static function get_query_string_url($url,$query_arr=array(),$is_encode=0){
		if(empty($url) || empty($query_arr)){
			return $url;
		}
		$query_string=!empty($is_encode) ? http_build_query($query_arr) : self::http_build_query_noencode($query_arr);
		return strripos($url,"?")!==false ? trim($url,"&")."&".$query_string : $url."?".$query_string;
	}
	
	/***************************************************************************************
	$queryArr			要进行处理的数组数据
	$unset_empty	判断是否如果为空的时候就不加入querystring
	$is_sort			是否是正序排序-------------------------------2018-03-01 10:59 由ALone添加。
	$delimiter		分隔符
	当前方法是对uri_helper.php文件中的function http_build_query_noencode_upgrade修改得到。
	****************************************************************************************/
	public static function http_build_query_noencode($query_arr,$unset_empty=-1,$is_sort=-1,$delimiter="&"){
		if(empty($query_arr)){
			return "";
		}
		$is_sort==1 && ksort($query_arr,SORT_STRING);
		$return_arr=array();
		array_walk($query_arr,function ($value,$item) use (&$return_arr,$unset_empty){
			$return_arr[$item]="{$item}={$value}";
			if($unset_empty==1){
				if(empty($value) && !is_numeric($value)) unset($return_arr[$item]);
			}
		});
		return !empty($return_arr) ? implode($delimiter,$return_arr) : "";
	}
	

	
	/*************
	返回Http信息。
	**************/
	public static function set_status_header($code=200,$text=''){
		set_status_header($code,$text);
		exit();
	}
	
	/******************
	判断是否是一个url
	*******************/
	public static function is_url($url){
		return preg_match("/^https?/i",$url);
	}
	
	/***************************************
	返回当前代理通知的完整地址
	$path  需要带前斜杠
	---The Author Of ALone 2018-08-20 18:09
	***************************************/
	public static function get_proxy_web_url($domain_ident,$path){
		if(!isset(self::$proxy_web_path_arr[$domain_ident])){
			return "";
		}
		return ProxyNotifyUrl."/".self::$proxy_web_path_arr[$domain_ident].$path;
	}

    /***********************
     * 返回上一个页面的地址。
     * @return mixed
     ***********************/
	public static function get_previous_url(){
	    return @$_SERVER["HTTP_REFERER"];
    }

    /*****************************************
     * 返回一个url中的域名地址包含https?和端口号
     * @param $url
     * @return null|string|string[]
     * ----The Author Of ALone 2018-10-31 10:46
     *******************************************/
    public static function get_url_hosts($url){
        $parse_url_arr=parse_url($url);
        $pathinfo_url=isset($parse_url_arr["path"]) ? $parse_url_arr["path"] : "";
        isset($parse_url_arr["query"]) && $pathinfo_url.="?".$parse_url_arr["query"];
        if(empty($pathinfo_url)) return $url;
        return preg_replace("#".preg_quote($pathinfo_url)."$#","",$url);
    }

    /************************
     * 判断指定URL是否是https
     * @param $url
     * @return bool
     *************************/
    public static function is_https($url){
        return preg_match("/^https\:\/\//i",$url) ? true : false;
    }
    //获取url控制器名称  --- 20190320 gzq
    public static function get_controller_name(){
        $ci = & get_instance();
        return $ci->router->fetch_class();
    }
    //获取url控制器方法  --- 20190320 gzq
    public static function get_controller_method(){
        $ci = & get_instance();
        return $ci->router->fetch_method();
    }

    //获取url域名
    public static function get_url_host($url){
        if(!self::is_url($url)) return '';
        $urlInfo = parse_url($url);
        return $urlInfo['host'];
    }

}










