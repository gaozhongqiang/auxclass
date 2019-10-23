<?php
/**
 * Created by PhpStorm.
 * User: gzq
 * Date: 2019/7/24
 * Time: 17:44
 * redis操作类
 * 1、直接调用redis本身的函数  Redis_tools::$redis->set()
 * 2、调用分装的函数 实例化调用
 */
namespace Auxclass\Cache;
class Redis{
    public static $redis = null ;
    public function __construct()
    {
        self::$redis = self::getInstance();
    }

    /**
     * redis初始化
     * @param int $timeOut 超时时间
     * @return null|Redis redis对象
     */
    public static function getInstance($timeOut = 30){
        if(null === self::$redis){
            self::$redis = new Redis();
            try{
                $success = self::$redis->connect(RedisHost,RedisPort,$timeOut);
                if(!$success){
                    throw new Exception(self::$redis->getLastError());
                }
                if(!empty(RedisPass) && !self::$redis->auth(RedisPass)){
                    throw new Exception(self::$redis->getLastError());
                }
                return self::$redis;
            }catch (Exception $e){
                echo $e->getMessage();
                log_message('error', $e->getMessage());
                return null;
            }
        }
        return self::$redis;
    }
    /**************************
    取得指定key的内容
    $key			指定key的内容
     ***************************/
    public static function get_cache($key){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->get(WebSite.$key);
    }
    /*************************************
    设置指定缓存内容
    $key			指定缓存的key值
    $value		指定缓存内容
    $time			指定缓存时间 使用秒为单位
     ***************************************/
    public static function set_cache($key,$value,$time=3600){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->set(WebSite.$key,$value,$time);
    }

    /**************************
    删除指定key的缓存信息
    $key			缓存中的key值
     ***************************/
    public static function del_cache($key){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->delete(WebSite.$key);
    }
    /****************************************************************************
    取得指定key的内容，是全局cache是不与域名相关
    $key			指定key的内容
    当前方法是对cache_helper.php文件中的function GetAllDomainCache函数修改得到。
    ---------------------------------------The Author Of ALone 2018-01-24 17:20
     ******************************************************************************/
    public static function get_all_domain_cache($key){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->get(PayNotifyPreName.$key);
    }
    /*******************************************************************************************************
    设置指定缓存内容，是全局cache是不与域名相关
    $key			指定缓存的key值
    $value		指定缓存内容
    $time			指定缓存时间 使用秒为单位
     ********************************************************************************************************/
    public static function set_all_domain_cache($key,$value,$time=3600){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->set(PayNotifyPreName.$key,$value,$time);
    }
    /*******************************************************************************************************
    删除指定key的缓存信息，是全局cache是不与域名相关
    $key			缓存中的key值
    当前方法是对cache_helper.php文件中的function DelAllDomainCache函数修改得到。
    ---------------------------------------The Author Of ALone 2018-01-25 11:33
     ********************************************************************************************************/
    public static function del_all_domain_cache($key){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->delete(PayNotifyPreName.$key);
    }

    /****************************************************************************
    取得指定hash表中key的内容，是全局cache是不与域名相关
    $key			指定key的内容
    当前方法是对cache_helper.php文件中的function GetAllDomainCache函数修改得到。
    ---------------------------------------The Author Of ALone 2018-01-24 17:20
     ******************************************************************************/
    public static function hget_all_domain_cache($hashTable, $key){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->hGet(PayNotifyPreName.$hashTable, $key);
    }
    /*******************************************************************************************************
    设置指定缓存内容，是全局cache是不与域名相关
    $key			指定缓存的key值
    $value		指定缓存内容
    $time			指定缓存时间 使用秒为单位
     ********************************************************************************************************/
    public static function hset_all_domain_cache($hashTable, $key,$value){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->hSet(PayNotifyPreName.$hashTable, $key, $value);
    }
    /*******************************************************************************************************
    判断hash表中key对应值是否存在，是全局cache是不与域名相关
    $key			指定缓存的key值
    $value		指定缓存内容
    $time			指定缓存时间 使用秒为单位
     ********************************************************************************************************/
    public static function hexists_all_domain_cache($hashTable, $key){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->hExists(PayNotifyPreName.$hashTable, $key);
    }
    /*******************************************************************************************************
    删除指定hash表key的缓存信息，是全局cache是不与域名相关
    $key			缓存中的key值
    当前方法是对cache_helper.php文件中的function DelAllDomainCache函数修改得到。
    ---------------------------------------The Author Of ALone 2018-01-25 11:33
     ********************************************************************************************************/
    public static function hdel_all_domain_cache($hashTable, $key){
        $redis = self::getInstance();
        if(is_null($redis)){
            return false;
        }
        return $redis->hDel(PayNotifyPreName.$hashTable, $key);
    }
}