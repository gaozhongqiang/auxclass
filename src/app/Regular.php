<?php
/************************************
正则表达式工具类
The Author Of ALone 2017-11-09 10:36
*************************************/
namespace Auxclass\App;
class Regular{
	/*************************************************************************************
	判断是否是一个数字。
	----------------------2017-06-15由ALone添加
	当前方法是对regular_helper.php文件中的function isNumber函数进行修改得到。
    --------------------------------------------------------------------------------------
    当前的判断只能判断正整数而不能判断负数，所以对于判断负数使用Regular_tools::upgrade_is_number代替。
    -------------------------------------------------------------------2018-11-08 16:39由ALone添加
	***************************************************************************************/
	public static function is_number($num){
		return self::is_integer_number($num) || self::is_float_number($num);
	}

    /*********************************************
     *判断是否是一个数字，包含负数。
     * @param $num
     * @return bool
     * 当前方法用于代替Regular_tools::is_number方法。
     *********************************************/
	public static function upgrade_is_number($num){
        return self::upgrade_is_integer_number($num) || self::upgrade_is_float_number($num);
    }

	/*******************************************************************************
	判断是否是一个整数数字
	----------------------2017-06-15由ALone添加
	当前方法是对regular_helper.php文件中的function isIntegerNumber函数进行修改得到。
	********************************************************************************/
	public static function is_integer_number($num){
		return preg_match("/^\d+$/",$num);
	}

    /***********************************************************************************
     * 判断是否是一个整数数字包括负数证书。当前方法是用于代替Regular_tools::is_integer_number方法。
     * @param $num
     * @return false|int
     * ----------------------------------------------The Author Of ALone 2018-11-08 16:56
     ************************************************************************************/
	public static function upgrade_is_integer_number($num){
        return preg_match("/^\-?\d+$/",$num);
    }

	/*****************************************************************************
	判断是否是一个浮点数字
	----------------------2017-06-15由ALone添加
	当前方法是对regular_helper.php文件中的function isFloatNumber函数进行修改得到。
	******************************************************************************/
	public static function is_float_number($num){
		return preg_match("/^\d+\.\d+$/",$num);
	}

    /************************************************************
    判断是否是一个浮点数字包括判断是否是一个浮点负数
    ----------------------2017-06-15由ALone添加
    当前方法是用于代替Regular_tools::is_float_number方法。
    -----------------------The Author Of ALone 2018-11-08 16:56
     ************************************************************/
    public static function upgrade_is_float_number($num){
        return preg_match("/^\-?\d+\.\d+$/",$num);
    }

	/********************************************************************
	判断是否是一个手机号码，
	当前是对regular_helper.php文件中的function isMobilePhone函数进行修改。
	---------------------------------The Author Of ALone 2018-07-31 16:32
	*********************************************************************/
	public static function is_mobile_phone($phone){
		return preg_match("/^\d{1,4}\-\d{7,12}$/",$phone) || self::is_china_mobile_phone($phone);
	}

    /************************
     * 判断是否是中国的手机。
     * @param $phone
     * @return false|int
     *************************/
	public static function is_china_mobile_phone($phone){
	    return preg_match("/^1\d{10}$/",preg_replace("/^(86)\-(\d+)$/","$2",ltrim($phone,"0")));
    }

	/************************************************************************
	判断当前用户名称是否符合设置。
	使用当前方法代替regular_helper.php文件中的function isMemberUserName方法。
	-------------------------------------The Author Of ALone 2018-07-31 16:32
	*************************************************************************/
	public static function is_username($username){
		return preg_match("/^[a-z]{1}[a-z\_0-9]{3,15}$/",$username);
	}
	
	/*********************************************
	判断是否是一个版本号码
	---------The Author Of ALone 2018-01-05 16:19
	*********************************************/
	public static function is_version($version){
		return preg_match("/^[\d\.]+\d$/",$version);
	}
	
	/**********************************************************************
	判断是否是QQ
	当前方法是对regular_helper.php文件中的function isQQNumber方法修改得到。
	-------------------------------------------2018-01-19 16:54由ALone添加
	***********************************************************************/
	public static function is_qq_number($qq){
		return preg_match("/^\d{4,15}$/",$qq);
	}
	
	/**************************************
	表示判断是否是一个32位的md5值
	-------------2018-01-26 13:59由ALone添加
	***************************************/
	public static function is_md5_32($appkey){
		return preg_match("/^[0-9a-z]{32}$/",$appkey);
	}
	/**************************************
	判断是否是一个正确的密码
	------------2018-01-26 13:59由ALone添加
	***************************************/
	public static function is_pwd($pwd){
		return preg_match("/^.{6,16}$/",$pwd);
	}
	/**************************************************************
	判断是否是一个正确的密码，在2018-08-09 16:06中设定密码为8-16位
	------------------------------------2018-08-09 16:07由ALone添加
	***************************************************************/
	public static function is_password($pwd){
		return preg_match("/^.{6,16}$/",$pwd);
	}

    /***************************
     * 判断是否精确到小数点后面两位
     * @param $num
     * @return bool
     *****************************/
    public static function is_accurate_price($num){
        return preg_match("/^\d+\.\d{2}$/",$num) ? true : false;
    }
}





















































