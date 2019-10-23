<?php
namespace Auxclass\App;
class Common{

    /*****************************************************************************
     * 当前方法是为了对如果传递过来的是一个空字符串，那么得到的结果是array(0=>)
     * 而我们需要得到的结果是array()。
     * @param string $string
     * @param string $delimiter
     * @return array
     * --------------------------------------The Author Of ALone 2018-04-30 15:23
     *****************************************************************************/
	public static function get_string_explode_arr($string="",$delimiter=","){
		if(empty($string)) return array();
		if(is_null($delimiter)) return preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
		return explode($delimiter,$string);
	}
    public static function get_arr_implode_string($arr=array(),$delimiter=""){
        if(empty($arr)) return "";
        if(!is_array($arr)) return $arr;
        return implode($delimiter,$arr);
    }
}




