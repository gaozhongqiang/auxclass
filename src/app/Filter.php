<?php
/************************************************************
过滤类
-------------------------The Author Of ALone 2017-11-09 10:28
*************************************************************/
namespace Auxclass\App;
class Filter{
	
	/*********************************************************************************
	大小格式化，当前方法是对tool_helper.php中的function ReturnFormatSize函数修改得到。
	$bit_size		Integer	文件大小，使用字节为单位
	$size_unit	String  要转换成的单位 BYTE、KB、MB、GB（忽略大小写）
	-----------------------------------------------The Author Of ALone 2018-06-25 11:01
	***********************************************************************************/
	public static function format_size($bit_size,$size_unit="mb"){
		$size_unit_arr=array("BYTE"=>0,"KB"=>1,"MB"=>2,"GB"=>3);
		$size_unit=strtoupper($size_unit);
		if(!isset($size_unit_arr[$size_unit])) return $bit_size;
		return round($bit_size/pow(1024,$size_unit_arr[$size_unit]),2);
	}
	
	/***************************************************
	获取指定文件的大小
	---------------The Author Of ALone 2018-06-25 11:01
	****************************************************/
	public static function get_file_size($abs_file,$size_unit="mb"){
		if(!is_file($abs_file)) return 0;
		return self::format_size(filesize($abs_file),"mb");
	}
	
	public static function string($data){
		return addslashes(stripslashes(self::htmlspecialchars_noamp(preg_replace("/^\s+|\s+$/","",$data))));
	}
	/*************************************
	The Author Of ALone 2018-05-29 11:06
	**************************************/
	public static function recursion_string($data){
		$method=__METHOD__;
		return !is_array($data) ? self::string($data) : array_map(function ($item) use ($method){
			return call_user_func($method,$item);
		},$data);
	}
	
	/**********************************************
	比较两个值是否相同。目前只能够比较字符串和数组
	***********************************************/
	public static function equals($data1,$data2){
		if(is_numeric($data1) && !is_numeric($data2) || !is_numeric($data1) && is_numeric($data2)){
			return false;
		}
		if(is_string($data1) && !is_string($data2) || !is_string($data1) && is_string($data2)){
			return false;
		}
		if(is_array($data1) && !is_array($data2) || !is_array($data1) && is_array($data2)){
			return false;
		}
		if(is_numeric($data1)) return $data1===$data2;
		if(is_string($data1)) return $data1===$data2;
		if(is_array($data1)) return Arrays::array_key_value_same($data1,$data2);
	}
	
	public static function integer($data){
		return intval($data);
	}
	public static function htmlspecialchars_noamp($data){
		return str_replace(array("'","\""),array('&#039;','&quot;'),$data);
	}
	/*******************************************
	获取GET方式的值。
	------The Author Of ALone 2018-03-07 18:48
	$is_num boolean 表示是否是一个数字
	-------The Author Of ALone 2018-04-26 20:12
	*******************************************/
	public static function get_url_data($key_name,$is_num=false){
		if($is_num) return intval(@$_GET[$key_name]);
		return self::string(isset($_GET[$key_name]) ? $_GET[$key_name] : "");
	}
	/*******************************************
	获取POST方式的值。
	------The Author Of ALone 2018-03-07 18:48
	$is_num boolean 表示是否是一个数字
	-------The Author Of ALone 2018-04-26 20:12
	*******************************************/
	public static function get_post_data($key_name,$is_num=false){
		if($is_num){
			if(is_callable($is_num)) {
				return call_user_func($is_num,@$_POST[$key_name]);
			}
			return intval(@$_POST[$key_name]);
		}
		return self::string(isset($_POST[$key_name]) ? $_POST[$key_name] : "");
	}
	public static function upgrade_post_data($key_name,$is_num=false){
		if($is_num) return self::get_post_data($key_name,$is_num);
		return self::recursion_string(isset($_POST[$key_name]) ? $_POST[$key_name] : "");
	}
	/**********************************************************************************************
	获取REQUEST方式的值。
	------The Author Of ALone 2018-03-07 18:48
	$is_num boolean 表示是否是一个数字
	-------The Author Of ALone 2018-04-26 20:12
	$is_num 在2018-05-15 18:45时被设置成另外的一个意思，表示是一个可以传递一个可调用的函数进行处理。
	-----------------------------------------------------------The Author Of ALone 2018-05-15 18:46
	************************************************************************************************/
	public static function get_request_data($key_name,$is_num=false){
		if($is_num){
			if(is_callable($is_num)) {
				return call_user_func($is_num,@$_REQUEST[$key_name]);
			}
			return intval(@$_REQUEST[$key_name]);
		}
		return self::string(isset($_REQUEST[$key_name]) ? $_REQUEST[$key_name] : "");
	}
	/***********************************************************************************
	将传递过来的xxx|xxx|xxxx|xx 或者xxx,xxx,xxx,xx 形式的数据过滤以数组的形式返回。
	$delimiter_string	xxx|xxx|xxxx|xx 或者xxx,xxx,xxx,xx 形式的数据
	$delimiter				分隔符
	当前是对filter_helper.php文件中的function filterRequestStringData函数进行修改得到的。
	------------------------------------------------The Author Of ALone 2018-02-01 13:57
	*************************************************************************************/
	public static function delimiter_string($delimiter_string="",$delimiter=","){
		if(empty($delimiter_string)) return $delimiter_string;
		$arr=array_unique(explode($delimiter,$delimiter_string));
		return array_unique(array_filter(array_map(function ($value){
							return self::string(preg_replace("/^\s+|\s+$/","",$value));
						},$arr)));
	}
	/***************************************************************************************
	当前函数是delimiter_string($delimiter_string="",$delimiter=",")的专门用于数字类型处理的
	将传递过来的xxx|xxx|xxxx|xx 或者xxx,xxx,xxx,xx 形式的数据过滤以数组的形式返回。
	$delimiter_string	xxx|xxx|xxxx|xx 或者xxx,xxx,xxx,xx 形式的数据
	$delimiter				分隔符
	当前是对filter_helper.php文件中的function filterRequestNumberData函数进行修改得到的。
	----------------------------------------------------The Author Of ALone 2018-02-01 13:57
	*****************************************************************************************/
	public static function delimiter_number($delimiter_string="",$delimiter=","){
		if(empty($delimiter_string)) return $delimiter_string;
		$arr=array_unique(explode($delimiter,$delimiter_string));
		return array_unique(array_filter($arr,function ($value){
			return Regular::is_number($value);
		}));
	}
	
	/****************************************************************************************************
	$arr				Array		要进行操作的数组
	$default			String		如果数组为空的时候的默认值
	$is_linear			Integer		是一维数组还是二维数组，1表示一维数组，2表示二维数组，默认是1
	$keyname			String		如果当前是二维数组才有效表示要取得的二维数组中的指定keyname的值，默认是空
	$remove_empty		Integer		表示是否需要移除数组中的空值，1表示需要移除空值，-1表示不需要，默认是1
	$is_unique			Integer		表示是否需要保持数组中的唯一性，1表示需要，-1表示不需要，默认是1
	$leftSymbol			String		内容的左边包围的内容，如果是需要保持空，那么写个空字
	$rightSymbol		String		内容的右边包围的内容，如果是需要保持空，那么写个空字
	$separate			String		每个内容的分割，如果是需要保持空，那么写个空字
	-----------------------------------------------------------------The Author Of ALone 2018-03-14 10:32
	当前方法是对filter_helper.php文件中的function format_sql_in_new函数修改得到的。
	******************************************************************************************************/
	public static function format_item_in_new($arr,$default=-1,$is_linear=1,$keyname="",$remove_empty=1,$is_unique=1,$leftSymbol="'",$rightSymbol="'",$separate=",",callable $CallBackFunction=null){
		if(empty($arr)){
			return "'{$default}'";
		}
		/*********************************************************
		ReturnLinearArrayNew 函数中 后面的11 表示唯一、去掉空元素
		**********************************************************/
		if($is_linear==2 && !empty($keyname)){
			$returnArr=Arrays::return_linear_array($arr,$keyname,$is_unique,$remove_empty);
		}else{
			$remove_empty	!=-1 && $arr=array_filter($arr);
			$is_unique 		!=-1 && $arr=array_unique($arr);
			$returnArr=$arr;
		}
		$leftSymbol	=="空" && $leftSymbol="";
		$rightSymbol=="空" && $rightSymbol="";
		$separate		=="空" && $separate="";
		$result_data=!empty($returnArr) ? $leftSymbol.implode($rightSymbol.$separate.$leftSymbol,$returnArr).$rightSymbol : $leftSymbol.$default.$rightSymbol;
		/************************
		callback中使用的参数：
		1、原数组；
		2、整合后返回的字符串；
		3、分隔符；
		*************************/
		return is_null($CallBackFunction) ? $result_data : call_user_func_array($CallBackFunction,array($arr,$result_data,$separate));
	}
	
	/**************************************************************************************************************************
		多条sql语句更新重组
		$arr 				需要更新的数组，包含where条件中的字段	这里的字段是array(array('name'=>'alone','age'=>26,'id'=>15));
		$table_name	表名称								表的名称
		$need_arr		需要进行更新的字段名  这里加入需要更新的字段名称是name、age  这里是数组传递 array('name','age')
		$key_name		where条件中的字段名		这里假如where条件的字段名称是id 这里直接写id
	***************************************************************************************************************************/
	public static function batch_update_classify($arr,$table_name,$need_arr,$key_name){
		$new_arr=array();$str="";$str_arr=array();$key_arr=array();
		foreach($need_arr as $k => $v){
			$str="`{$v}`=CASE `{$key_name}` ";
			foreach($arr as $key => $value){
				$str.="WHEN '{$value[$key_name]}' THEN '{$value[$v]}' ";
				if(!in_array($value[$key_name],$key_arr)){
					$key_arr[]=$value[$key_name];
				}
			}
			$str.="END";
			$str_arr[]=$str;
		}
		return "UPDATE {$table_name} SET ".implode(',',$str_arr)." WHERE {$key_name} IN ('".implode("','",$keyArr)."')";
	}
	/*************************************************
	将HTML文本中的HTML去除留下文本内容。来源于DEDECMS
	**************************************************/
	public static function html2text($str){
		$str = preg_replace("/<sty(.*)/style>|<scr(.*)/script>|<!--(.*)-->/isU","",$str);
		$alltext = "";
		$start = 1;
		for($i=0;$i<strlen($str);$i++){
			if($start==0 && $str[$i]==">"){
				$start = 1;
			}
			else if($start==1){
				if($str[$i]=="<"){
					$start = 0;
					$alltext .= " ";
				}
				else if(ord($str[$i])>31){
					$alltext .= $str[$i];
				}
			}
		}
		$alltext = str_replace("　"," ",$alltext);
		$alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
		$alltext = preg_replace("/[ ]+/s"," ",$alltext);
		return $alltext;
	}
	
	/***********************************************
	去掉手机区号前面的零部分。
	-----------The Author Of ALone 2018-08-03 16:41
	************************************************/
	public static function phone_area_filter_zero($phone){
		if(stripos($phone,"-")===false){
			return $phone;
		}
		return ltrim($phone,"0");
	}
	
	/***************************
	过滤掉国内的手机号码86前缀
	***************************/
	public static function filter_china_phone_area($phone){
		return preg_replace("/^(86)\-(\d{7,12})$/","$2",$phone);
	}

    /***
     * sql中合并返回要查询的字段内容。
     * @param $fields_arr1
     * @param $fields_arr2
     * @return array|string
     */
	public static function sql_search_fields($fields_arr1,$fields_arr2){
	    return Common::get_arr_implode_string(Arrays::unique_merge($fields_arr1,$fields_arr2),",");
    }

    /*********************************************************
     * 接收到一个使用指定字符分隔的数字字符串，判断字符串中是否是纯数字。
     * @param $delimiter_number_string
     * @param string $delimiter_string
     * @return array
     * --------------------The Author Of ALone 2018-12-04 11:08
     **********************************************************/
    public static function compare_delimiter_number($delimiter_number_string,$delimiter_string=","){
        $delimiter_number_arr=Filter::delimiter_number($delimiter_number_string,$delimiter_string);
        if(!Arrays::array_value_same($delimiter_number_arr,Common::get_string_explode_arr($delimiter_number_string,$delimiter_string))){
            return array(-1,array_diff(Common::get_string_explode_arr($delimiter_number_string,$delimiter_string),$delimiter_number_arr));
        }
        return array(0,array());
    }
}

