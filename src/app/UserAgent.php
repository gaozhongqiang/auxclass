<?php
/************************************************************
Useragent工具类
-------------------------The Author Of ALone 2017-10-31 18:30
*************************************************************/
namespace Auxclass\App;
class UserAgent{
	/********************************************************
	表示判断是否是苹果设备。
	当前方式是对tool_helper.php中的function IsApple修改得到。
	*********************************************************/
	public static function is_apple($user_agent=""){
		$user_agent=empty($user_agent) ? @$_SERVER["HTTP_USER_AGENT"] : $user_agent;
		if(empty($user_agent)) return false;
		return preg_match("/iPhone|iPod|iPad/i",$user_agent);
	}
	/********************
	返回上一个的请求地址
	**********************/
	public static function get_prev_url(){
		return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
	}
	/***********************************************
	返回当前用户是IOS端还是Android端。
	------------The Author Of ALone 2018-05-17 17:30
	*************************************************/
	public static function get_os_type(){
		return self::is_apple() ? 1 : 2;
	}

	/*********************************************
	 * 取得userAgent。
	 * ------The Author Of ALone 2018-09-07 10:13
	 *********************************************/
	public static function get_user_agent(){
	    return isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
    }




}





