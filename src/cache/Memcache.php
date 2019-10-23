<?php
/*************************************************************************
缓存操作工具类
**************************************************************************/
namespace Auxclass\Cache;
use Auxclass\App\Url;

class Memcache{
	public static $max_cache_time=2592000;
	/**********************
		初始化缓存
	***********************/
	public static function init_cache(){
		if(!isOpenCache){
			return false;
		}
		$CI= & get_instance();
		$CI->load->driver('cache',array('adapter' => 'memcached', 'backup' => 'alibaba'));
	}

	/**************************
		取得指定key的内容
		$key			指定key的内容
	***************************/
	public static function get_cache($key){
		if(!isOpenCache){
			return array();
		}
		$CI= & get_instance();
		return $CI->cache->get(WebSiteIp.$key);
	}

	/*************************************
		设置指定缓存内容
		$key			指定缓存的key值
		$value		指定缓存内容
		$time			指定缓存时间 使用秒为单位
	***************************************/
	public static function set_cache($key,$value,$time=3600){
		if(!isOpenCache){
			return false;
		}
		$CI= & get_instance();
		return $CI->cache->save(WebSiteIp.$key,$value,$time);
	}

	/**************************
		删除指定key的缓存信息
		$key			缓存中的key值
	***************************/
	public static function del_cache($key){
		if(!isOpenCache){
			return false;
		}
		$CI= & get_instance();
		return $CI->cache->delete(WebSiteIp.$key);
	}

	/***************************************************************************************************
	$add_cache_key	缓存的key
	$cache_time			缓存的时间，使用秒为单位
	$errormsg				返回的错误信息
	$cache_type			表示是否是全局缓存，1表示是局部缓存，2表示是全局缓存
	当前方法是对cache_helper.php文件中的function IsHasSubmitCache方法修改得到。
	----------------------------------------------------------------The Author Of ALone 2018-07-31 14:42
	****************************************************************************************************/		
	public static function is_has_submit_cache($add_cache_key,$cache_time=60,$errormsg="当前正在提交中，请稍后！",$cache_type=1){
		$add_cache=$cache_type==1 ? self::get_cache($add_cache_key) : self::get_all_domain_cache($add_cache_key);
		if(!empty($add_cache)){
			return array(-1,$errormsg);
		}
		//设置提交数据缓存保存一分钟
		$cache_type==1 && self::set_cache($add_cache_key,$add_cache_key,$cache_time);
		$cache_type!=1 && self::set_all_domain_cache($add_cache_key,$add_cache_key,$cache_time);
		return array(0,$add_cache_key);
	}

	/****************************************************************************
		取得指定key的内容，是全局cache是不与域名相关
		$key			指定key的内容
		当前方法是对cache_helper.php文件中的function GetAllDomainCache函数修改得到。
		---------------------------------------The Author Of ALone 2018-01-24 17:20
	******************************************************************************/
	public static function get_all_domain_cache($key){
		if(!isOpenCache){
			return array();
		}
		$CI= & get_instance();
		return $CI->cache->get(PayNotifyPreNameIp.$key);
	}

	/*******************************************************************************************************
		设置指定缓存内容，是全局cache是不与域名相关
		$key			指定缓存的key值
		$value		指定缓存内容
		$time			指定缓存时间 使用秒为单位
	********************************************************************************************************/
	public static function set_all_domain_cache($key,$value,$time=3600){
		if(!isOpenCache){
			return false;
		}
		$CI= & get_instance();
		return $CI->cache->save(PayNotifyPreNameIp.$key,$value,$time);
	}

	/*******************************************************************************************************
		删除指定key的缓存信息，是全局cache是不与域名相关
		$key			缓存中的key值
		当前方法是对cache_helper.php文件中的function DelAllDomainCache函数修改得到。
		---------------------------------------The Author Of ALone 2018-01-25 11:33
	********************************************************************************************************/
	public static function del_all_domain_cache($key){
		if(!isOpenCache){
			return false;
		}
		$CI= & get_instance();
		return $CI->cache->delete(PayNotifyPreNameIp.$key);
	}

    /*********************************************************************************
    设置指定域名的缓存。
    $domain	需要设置的域
    $key		缓存key
    $value		指定缓存内容
    $time			指定缓存时间 使用秒为单位
    当前方法是对cache_helper.php文件中的function SetAppointDomainCache函数修改得到。
    ---------------------------------------The Author Of ALone 2018-11-27 15:20
     **********************************************************************************/
    public static function set_appoint_domain_cache($domain,$key,$value,$time=3600){
        if(!isOpenCache){
            return array();
        }
        $CI= & get_instance();
        $host_domain=Url::get_domain_website($domain);
        return $CI->cache->save($host_domain.$key,$value,$time);
    }

    /*********************************************************************************
    取得指定域名的缓存。
    $domain	需要设置的域
    $key		缓存key
    当前方法是对cache_helper.php文件中的function GetAppointDomainCache函数修改得到。
    ---------------------------------------The Author Of ALone 2018-01-24 17:20
     **********************************************************************************/
    public static function get_appoint_domain_cache($domain,$key){
        if(!isOpenCache){
            return array();
        }
        $CI= & get_instance();
        $host_domain=Url::get_domain_website($domain);
        return $CI->cache->get($host_domain.$key);
    }

    /********************************************************************************
    删除指定域名的缓存信息
    $domain	需要设置的域
    $key		缓存key
    当前方法是对cache_helper.php文件中的function DelAppointDomainCache函数修改得到。
    ---------------------------------------The Author Of ALone 2018-01-25 11:33
     ********************************************************************************/
    public static function del_appoint_domain_cache($domain,$key){
        if(!isOpenCache){
            return false;
        }
        $CI= & get_instance();
        $hostDomain=Url::get_domain_website($domain);
        return $CI->cache->delete($hostDomain.$key);
    }


}

