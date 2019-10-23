<?php
/**********************************************
数组工具类
--------The Author Of ALone 2017-11-10 16:46
***********************************************/
namespace Auxclass\App;
class Arrays{
	/**************************************************************************************
		$arr				二维数组
		$keyname			要返回数据的$keyname
		$is_unique			是否返回唯一元素，默认是1表示唯一
		$remove_empty		是否需要去掉空元素，默认是去掉空元素，1表示去掉空元素
		------------------------------------------------The Author Of ALone 2017-04-17 10:49
	***************************************************************************************/
	public static function return_linear_array($arr=array(),$keyname="",$is_unique=1,$remove_empty=1){
		if(empty($arr) || empty($keyname)){
			return array();
		}
		$retArr=array();
		foreach($arr as $k => $v){
			if(isset($v[$keyname])){
				if($remove_empty==-1 || ($remove_empty==1 && !empty($v[$keyname]))){
					$retArr[]=$v[$keyname];
				}
			}
		}
		return $is_unique!=-1 ? array_values(array_unique(array_filter($retArr))) : $retArr;
	}

	/****************************************************************
	在数组中查询是否存在指定（键名的）元素。
	example for ALone：
	$arr=array(
	"alone1"=>array("id_name"=>"kooyuyu1"),
	"alone2"=>array("id_name"=>"kooyuyu2"),
	"alone3"=>array("id_name"=>"kooyuyu3"),
	"alone4"=>array("id_name"=>"kooyuyu4"),
	"alone5"=>array("id_name"=>"kooyuyu5"),
	"alone6"=>array("id_name"=>"kooyuyu6")
	);
	Arrays::search_linear_array($arr,"kooyuyu5",2,"id_name")
	example for ALone return ：alone5
	----------------------------------------------------------------
	$arr=array("alone1","alone2","alone3","alone4","alone5","alone6");
	Arrays::search_linear_array($arr,"alone5")
	example for ALone return : 4;
	----------------------------The Author Of ALone 2018-04-13 10:50
	*****************************************************************/
	public static function search_linear_array($arr=array(),$value="",$linear=1,$keyname=""){
		if(empty($arr) || empty($value)) return false;
		if($linear==1 || empty($keyname)) return array_search($value,$arr);
		foreach($arr as $key => $val){
			if(isset($val[$keyname]) && $value==$val[$keyname]){
				return $key;
			}
		}
		return false;
	}

	/**************************************************************
		设置一个数组的二维数组中的key的值作为这个数组中的key返回
		指定keyname对应的值是不一样的如果一样的话就会被覆盖
	**************************************************************/
	public static function return_set_arr_key($arr,$keyname){
		if(empty($arr)){
			return array();
		}
		$newArr=array();
		foreach($arr as $key => $value){
			if(isset($value[$keyname])){
				$newArr[$value[$keyname]]=$value;
			}
		}
		return $newArr;
	}


    /**************************************************************
    设置一个数组的二维数组中的key的值作为这个数组中的key返回
    指定keyname对应的值是不一样的如果一样的话就会被覆盖
     **************************************************************/
    public static function return_set_arr_key_two($arr,$keyname1,$keyname2,$connect_str="-"){
        if(empty($arr)){
            return array();
        }
        $newArr=array();
        foreach($arr as $key => $value){
            if(isset($value[$keyname1]) && isset($value[$keyname2])){
                $newArrKey = $keyname1 . $connect_str . $keyname2;
                $newArr[$newArrKey]=$value;
            }
        }
        return $newArr;
    }

	/***********************************************************
		设置一个数组的二维数组中的key的值作为这个数组中的key返回
		指定keyname对应的值是可以不一样的，做数组累加的方式
	************************************************************/
	public static function return_add_arr_key($arr,$keyname){
		if(empty($arr)){
			return array();
		}
		$newArr=array();
		foreach($arr as $key => $value){
			if(isset($value[$keyname])){
				$newArr[$value[$keyname]][]=$value;
			}
		}
		return $newArr;
	}
	
	/*******************************************************
	单纯比较数组的value，表示判断当前的两个数组的值是否一致。
	--------------------The Author Of ALone 2018-04-30 16:26
	********************************************************/
	public static function array_value_same($arr1,$arr2){
		if(empty($arr1) && empty($arr2)) return true;
		if(empty($arr1) && !empty($arr2)) return false;
		if(!empty($arr1) && empty($arr2)) return false;
		$temp=array_diff($arr1,$arr2);
		if(!empty($temp)) return false;
		$temp=array_diff($arr2,$arr1);
		if(!empty($temp)) return false;
		return true;
	}
	
	/***************************************************************
	key和value比较，表示判断当前数组的key和值是否一致并且对应是一致。
	----------------------------The Author Of ALone 2018-04-30 16:26
	****************************************************************/
	public static function array_key_value_same($arr1,$arr2){
		if(empty($arr1) && empty($arr2)) return true;
		if(empty($arr1) && !empty($arr2)) return false;
		if(!empty($arr1) && empty($arr2)) return false;
		$temp=array_diff_assoc($arr1,$arr2);
		if(!empty($temp)) return false;
		$temp=array_diff_assoc($arr2,$arr1);
		if(!empty($temp)) return false;
		return true;
	}
	
	/**********************
	递归对数组进行url编码
	***********************/
	public static function encode_array($arr){
		return self::encode_decode_array($arr,1);
	}
	
	/************************
	递归对数组进行url反编码
	*************************/
	public static function decode_array($arr){
		return self::encode_decode_array($arr,-1);
	}
	
	/*************************************************
	当前函数是对encode_array和decode_array的核心处理。
	*************************************************/
	private static function encode_decode_array($arr,$is_encode=1){
		if(empty($arr)) return $arr;
		$self_call=__METHOD__;
		$encode_call=$is_encode==1 ? "urlencode" : "urldecode";
		return array_map(function ($value) use ($self_call,$encode_call){
							return is_array($value) ? call_user_func($self_call,$value,$encode_call) : $encode_call($value);
					 },$arr);
	}
	
	/*******************
	将数组转换成xml格式
	*******************/
	public static function array_to_xml($arr){
		$xml="";
		foreach($arr as $key => $value){
			$xml.=is_numeric($value) ? "<".$key.">".$value."</".$key.">" : "<".$key."><![CDATA[".$value."]]></".$key.">";
		}
		return sprintf("<xml>%s</xml>",$xml);
	}
	
	/*******************************************************************************************************************
	将xml转换成数组的形式
	********************************************************************************************************************/
	public static function xml_to_array($xml){
		/****************************************************
		禁用加载外部实体的能力
		使用此功能，您可以防止容易受到本地和远程文件包含攻击。
		*****************************************************/
    libxml_disable_entity_loader(true);
   	/****************************************************
   	simplexml_load_string 表示将xml解析成一个对象。
   	LIBXML_NOCDATA	参数表示合并空白节点
   	*****************************************************/
    return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);	
	}
	
	/****************************************************************
	数组内容转换成json对象字符串
	当前方法是对pay_helper.php中的function ArrayToObject方法修改得到。
	*****************************************************************/
	public static function array_to_json($arr,$is_encode=-1){
		if(empty($arr)) return "";
		return $is_encode==-1 ? json_encode($arr,JSON_UNESCAPED_UNICODE) : json_encode($arr);
	}
    
    /****************************************************************
     * JSON字符串转数组、对象 
     * Hong 20180408
    *****************************************************************/
    public static function json_to_array($json ,$is_array=1){
        if(empty($json)) return "";
        return $is_array==1 ? json_decode($json,TRUE) : json_decode($json,FALSE);
    }
    
	/***********************************************************************************************
	删除数组中的指定元素，如果传递的是一个索引数组，是使用array_splice函数处理删除，不会影响索引顺序
	如果传递的是一个关联数组，那么会使用unset操作。
	-----------------------------------------------------------The Author Of ALone 2018-01-31 16:21
	************************************************************************************************/ 
	public static function del_array_item($value,$arr){
		if(empty($arr) || !in_array($value,$arr)){
			return $arr;
		}
		$key=array_search($value,$arr);
		if(self::is_assoc_array($arr))
			unset($arr[$key]);
		else 
			array_splice($arr,$key,1);
		return $arr;
	}
	
	/*************************************************
	判断当前是否是关联数组。
	-------------The Author Of ALone 2018-01-31 16:21
	*************************************************/
	public static function is_assoc_array($arr){
		return array_keys($arr)!==range(0,count($arr)-1);
	}
	
	/******************************************************************************************************************
	数组内容转换成js中的object内容
	example to ALone：
	1、$arr=array("123"=>"alone123","345"=>"alone345","567"=>"alone567","789"=>"alone789");
		 Arrays::array_to_jsobject($arr)
		 {"123":"alone123","345":"alone345","567":"alone567","789":"alone789"}
		 ---------------------------------------------------------------------------------------------------------------
	2、$arr=array(
					"123"=>array("id_name"=>"id_name123"),
					"345"=>array("id_name"=>"id_name345"),
					"567"=>array("id_name"=>"id_name567"),
					"789"=>array("id_name"=>"id_name789")
				);
		 Arrays::array_to_jsobject($arr,2,"","id_name")
		 {"123":"id_name123","345":"id_name345","567":"id_name567","789":"id_name789"}
		 --------------------------------------------------------------------------------------------------------------
	3、$arr=array(
					"123"=>array("id_name"=>"id_name123","id_value"=>"id_value123"),
					"345"=>array("id_name"=>"id_name345","id_value"=>"id_value345"),
					"567"=>array("id_name"=>"id_name567","id_value"=>"id_value567"),
					"789"=>array("id_name"=>"id_name789","id_value"=>"id_value789")
				);
		 Arrays::array_to_jsobject($arr,2,"id_name","id_value")
		 {"id_name123":"id_value123","id_name345":"id_value345","id_name567":"id_value567","id_name789":"id_value789"}
		 -----------------------------------------------------------------------------The Author Of ALone 2018-04-12 17:32
	*********************************************************************************************************************/
	public static function array_to_jsobject($arr,$is_linear=1,$key_name="",$value_name=""){
		if($is_linear==1) return self::array_to_json($arr);
		$key_arr=!empty($key_name) ? Arrays::return_linear_array($arr,$key_name) : array_keys($arr);
		$value_arr=Arrays::return_linear_array($arr,$value_name);
		return self::array_to_json(array_combine($key_arr,$value_arr));
	}
		
	/**********************************************************************************************
	将指定格式的字符串转换成数组返回：
	{keyname1=[value1] keyname2=[value2] keyname5=[value5] keyname6=[value6]},
	{keyname1=[value3] keyname2=[value4] keyname5=[value55] keyname6=[value66]},
	{keyname1=[value5] keyname2=[value6] keyname5=[value57] keyname6=[value68]}
	Common::format2array($str,array("keyname1","keyname2","keyname5","keyname6"));
	得到结果：
	Array
	(
	    [keyname1] => Array
	        (
	            [0] => value1
	            [1] => value3
	            [2] => value5
	        )
	
	    [keyname2] => Array
	        (
	            [0] => value2
	            [1] => value4
	            [2] => value6
	        )
	
	    [keyname5] => Array
	        (
	            [0] => value5
	            [1] => value55
	            [2] => value57
	        )
	
	    [keyname6] => Array
	        (
	            [0] => value6
	            [1] => value66
	            [2] => value68
	        )
	)
	当前方法不再使用，请放弃当前方法，而使用upgrade2array方法。
	*************************************************************************************************************/
	public static function format2array($string,$keyname_arr){
		if(empty($keyname_arr)) return "";
		$keyname_arr=array_map(function ($value){
								 		return "({$value})=\\[(.*?)\\]";
								 },$keyname_arr);
		$pattern="/\{".Common::get_arr_implode_string($keyname_arr,"\\s+")."\}/";
		preg_match_all($pattern,$string,$match_arr,PREG_PATTERN_ORDER);
		if(!isset($match_arr[count($keyname_arr)])){
			return "";
		}
		array_shift($match_arr);
		$match_arr=array_chunk($match_arr,2);
		return array_reduce($match_arr,function ($carry,$item){
							@list($key_arr,$value_arr)=$item;
							foreach($value_arr as $key=> $value){
								$carry[$key_arr[0]][]=$value_arr[$key];
							}
							return $carry;
					 },array());
	}
	
	/*************************************************************************************
	将指定格式的数组格式转换成字符串格式返回：
	Array
	(
	    [keyname1] => Array
	        (
	            [0] => value1
	            [1] => value3
	            [2] => value5
	        )
	
	    [keyname2] => Array
	        (
	            [0] => value2
	            [1] => value4
	            [2] => value6
	        )
	
	    [keyname5] => Array
	        (
	            [0] => value5
	            [1] => value55
	            [2] => value57
	        )
	
	    [keyname6] => Array
	        (
	            [0] => value6
	            [1] => value66
	            [2] => value68
	        )
	)
	Common::format2array($arr);
	{keyname1=[value1] keyname2=[value2] keyname5=[value5] keyname6=[value6]},
	{keyname1=[value3] keyname2=[value4] keyname5=[value55] keyname6=[value66]},
	{keyname1=[value5] keyname2=[value6] keyname5=[value57] keyname6=[value68]}
	当前方法不再使用，请放弃当前方法，而使用upgrade2stringy方法。
	**********************************************************************************/
	public static function format2string($arr){
		if(empty($arr)) return "";
		$key_arr=array_keys($arr);
		$return_arr=array();
		foreach(current($arr) as $top_key => $top_value){
			$result_item_arr=array();
			foreach($key_arr as $value){
				$result_item_arr[]="{$value}=[{$arr[$value][$top_key]}]";
			}
			$return_arr[]="{".Common::get_arr_implode_string($result_item_arr," ")."}";
		}
		return Common::get_arr_implode_string($return_arr,",");
	}
	
	
	/************************************************************
	将数组形式：
	Array
	(
	    [0] => Array
	        (
	            [keyname1] => value1
	            [keyname2] => value2
	            [keyname3] => value3
	        )
	
	    [1] => Array
	        (
	            [keyname1] => value1
	            [keyname2] => value2
	            [keyname3] => value3
	        )
	)
	转换成下面字符串形式：
	{keyname1=[value1]|#!Space!#|keyname2=[value2]|#!Space!#|keyname3=[value3]}|#!ALone!#|
	{keyname1=[value1]|#!Space!#|keyname2=[value2]|#!Space!#|keyname3=[value3]}
	*************************************************************/
	public static function upgrade2string($arr,$dimension=2){
		if(empty($arr)) return "";
		if($dimension==1){
			$temp_key=array_keys($arr);
			$carry=array_reduce($temp_key,function ($carry,$item) use ($arr){
								$carry[]="{$item}=[{$arr[$item]}]";
								return $carry;
						 },array());
			return "{".Common::get_arr_implode_string($carry,"|#!Space!#|")."}";
		}
		$delimiter_arr=array();
		foreach($arr as $arr_key => $arr_value){
			$temp_key=array_keys($arr_value);
			$carry=array_reduce($temp_key,function ($carry,$item) use ($arr_value){
								$carry[]="{$item}=[{$arr_value[$item]}]";
								return $carry;
						 },array());
			$delimiter_arr[]="{".Common::get_arr_implode_string($carry,"|#!Space!#|")."}";
		}
		return Common::get_arr_implode_string($delimiter_arr,"|#!ALone!#|");
	}
	
	/*********************************************************
	将字符串形式：
	{keyname1=[value1]|#!Space!#|keyname2=[value2]|#!Space!#|keyname3=[value3]}|#!ALone!#|
	{keyname1=[value1]|#!Space!#|keyname2=[value2]|#!Space!#|keyname3=[value3]}
	转换成数组形式：
	Array
	(
	    [0] => Array
	        (
	            [keyname1] => value1
	            [keyname2] => value2
	            [keyname3] => value3
	        )
	
	    [1] => Array
	        (
	            [keyname1] => value1
	            [keyname2] => value2
	            [keyname3] => value3
	        )
	)
	*************************************************************/
	public static function upgrade2array($str,$dimension=2){
		if(empty($str)) return array();
		$split_string=stripos($str,"|#!ALone!#|")!==false ? "|#!ALone!#|" : ",";
		$space_string=stripos($str,"|#!Space!#|")!==false ? "|#!Space!#|" : " ";
		if($dimension==1){
			$line_string=substr($str,stripos($str,"{")+1,strripos($str,"}")-1);
			$line_arr=Common::get_string_explode_arr($line_string,$space_string);
			$item_array=array();
			foreach($line_arr as $value){
				if(preg_match("/(.*?)=\[(.*?)\]/s",$value,$match)){
					$item_array[$match[1]]=$match[2];
				}
			}
			return $item_array;
		}
		$all_data_array=Common::get_string_explode_arr($str,$split_string);
		$return_array=array();
		foreach($all_data_array as $key =>$all_data_value){
			$line_string=substr($all_data_value,stripos($all_data_value,"{")+1,strripos($all_data_value,"}")-1);
			$line_arr=Common::get_string_explode_arr($line_string,$space_string);
			$item_array=array();
			foreach($line_arr as $value){
				if(preg_match("/(.*?)=\[(.*?)\]/s",$value,$match)){
					$item_array[$match[1]]=$match[2];
				}
			}
			$return_array[]=$item_array;
		}
		return $return_array;
	}
	
	/****************************************
     * 数组合并
     * -The Author Of ALone 2018-09-03 11:06
	*****************************************/
	public static function unique_merge($arr1,$arr2,$is_unique=-1){
		return $is_unique=-1 ? array_merge($arr1,$arr2) : array_unique(array_merge($arr1,$arr2));
	}

    /*****************************************
     * 取得数组里面的最小值
     * @param $arr
     * @param string $keyname
     * @return mixed
     * ---The Author Of ALone 2018-09-03 11:06
     ******************************************/
	public static function get_min_value($arr,$keyname=""){
	    if(empty($keyname)) return min($arr);
	    $value_arr=Arrays::return_linear_array($arr,$keyname);
	    return min($value_arr);
    }

    /************************************************************
     * @param $arr      数组内容
     * @param $keyname  要进行比较的键名
     * @param $value    要进行比较的键值
     * example for ALone
     * $arr=array(
     * array("id"=>1,"name"=>"alone1","age"=>28),
     * array("id"=>2,"name"=>"alone2","age"=>28),
     * array("id"=>3,"name"=>"alone3","age"=>21),
     * array("id"=>4,"name"=>"alone4","age"=>22),
     * array("id"=>5,"name"=>"alone5","age"=>28),
     * array("id"=>6,"name"=>"alone6","age"=>23),
     * );
     * Arrays::search_keyname_linear_array($arr,age,28);
     * $arr=array(
     * array("id"=>1,"name"=>"alone1","age"=>28),
     * array("id"=>2,"name"=>"alone2","age"=>28),
     * array("id"=>5,"name"=>"alone5","age"=>28),
     * );
     * ------------------------The Author Of ALone 2018-09-17 13:51
     ***************************************************************/
    public static function search_keyname_linear_array($arr,$keyname,$value,$need_format=true){
        $key_arr=array_keys($arr);
        $return_arr=array_reduce($key_arr,function ($carry,$item) use ($keyname,$value,$arr){
                    if(isset($arr[$item][$keyname]) && $arr[$item][$keyname]==$value){
                        $carry[$item]=$arr[$item];
                    }
                    return $carry;
                },array());
        return $need_format ? array_values($return_arr) : $return_arr;
    }

    /**
     *
     */
    public static function array_multi_sort_key($arr,$keyname,$order_type=SORT_ASC){
        if(empty($arr) || empty($keyname)){
            return array();
        }
        $new_arr=array();
        foreach ($arr as $k=>$v){
            $new_arr[$k][$keyname]=$v[$keyname];
        }
        array_multisort($new_arr,$order_type,$arr);//SORT_DESC为降序，SORT_ASC为升序
        return $arr;
    }

    /****************************************************************************
     * 按照二维数组按照指定键名进行排序，只能处理数值型排序，所以$keyname对应的内容是数值。
     * @param $arr
     * @param $keyname
     * @param int $sort_type 1表示升序，-1表示倒序
     * @return mixed
     * ------------------------------------The Author Of ALone 2018-10-15 14:14
     ***************************************************************************/
    public static function array_keyname_sort($arr,$keyname,$sort_type=1){
        usort($arr,function ($val1,$val2) use ($keyname,$sort_type){
            $val=$val1[$keyname]-$val2[$keyname] > 0 ? 1 : -1;
            $val1[$keyname]-$val2[$keyname] == 0 && $val=0;
            return $sort_type==1 ? $val : -$val;
        });
        return $arr;
    }

    /********************************************************************************************************
     * 按照二维数组按照指定键名进行排序，只能处理数值型排序，所以$keyname对应的内容是数值。当前方法是
     * Arrays::array_keyname_sort 方法的升级处理，要求不再使用Arrays::array_keyname_sort 方法，
     * function array_keyname_sort
     * @param $arr
     * @param $keyname
     * @param string $sort_type 【ASC】表示升序，【desc】表示降序
     * @return array
     * ------------------------------------------------------------------The Author Of ALone 2019-02-25 12:38
     ********************************************************************************************************/
    public static function array_keyname_sort_upgrade($arr,$keyname,$sort_type="ASC"){
        if(empty($arr)){
            return array();
        }
        $i = 0;
        $arr = array_map(function($elt)use(&$i) {
            return [$i++, $elt];
        }, $arr);
        $sort_type=strtolower($sort_type);
        usort($arr, function($a,$b) use ($keyname,$sort_type){
            $val=$a[1][$keyname]-$b[1][$keyname];
            if(!empty($val)) return $sort_type=="asc" ? $val : -$val;
            return $a[0] - $b[0];
        });
        return array_column($arr, 1);
    }

    /*****************************************************************************
     * 按照二维数组按照指定键名进行排序，可以处理索引数组和键值对数组。
     * @param $arr
     * @param $keyname
     * @param int $sort_type 1表示升序，-1表示倒序
     * @return mixed
     * ------------------------------------The Author Of ALone 2019-01-17 17:21
     *****************************************************************************/
    public static function array_keyname_assoc_sort($arr,$keyname,$sort_type=1){
        $temp_key="alone_key_value";
        $result_arr=array();
        array_walk($arr,function ($value,$key) use ($temp_key,&$result_arr){
            $value[$temp_key]=$key;
            $result_arr[]=$value;
        });
        for($i=0;$i<count($result_arr)-1;$i++){
            for($j=$i;$j<count($result_arr);$j++){
                if(bccomp($result_arr[$i][$keyname],$result_arr[$j][$keyname])==$sort_type){
                    $temp=$result_arr[$i];
                    $result_arr[$i]=$result_arr[$j];
                    $result_arr[$j]=$temp;
                }
            }
        }
        $arr=array();
        array_walk($result_arr,function ($value,$key) use ($temp_key,&$arr){
            $temp_key_value=$value[$temp_key];
            unset($value[$temp_key]);
            $arr[$temp_key_value]=$value;
        });
        return $arr;
    }

    /************************************************
     * 查找到指定数据中指定keyname中的最小值所在的数组索引。
     * @param $arr
     * @param $keyname
     * @return int|null|string
     * ----------The Author Of ALone 2019-01-08 17:09
     *************************************************/
    public static function array_keyname_min($arr,$keyname){
        $min_index=key($arr);
        $min_value=$arr[$min_index][$keyname];
        foreach($arr as $key => $value){
            if($value[$keyname]<$min_value){
                $min_value=$value[$keyname];
                $min_index=$key;
            }
        }
        return $min_index;
    }

    /***************************
     * 格式化返回JS数组的形式
     * @param $arr
     * @param int $is_linear
     * @param string $keyname
     * @return string
     ****************************/
    public static function format_javascript_array($arr,$is_linear=1,$keyname=""){
        return !empty($arr) ? '['.Filter_tools::format_item_in_new($arr,-1,$is_linear,$keyname).']' : "";
    }

    /************************************
     * 找到数组1与数组2中不同的值
     * @param $arr1
     * @param $arr2
     * @param int $arr1_isline
     * @param int $arr2_is_line
     * @param string $arr1_keyname
     * @param string $arr2_keyname
     * @return array
     ****************************************/
    public static function get_array_diff($arr1,$arr2,$arr1_isline=1,$arr2_is_line=1,$arr1_keyname="",$arr2_keyname=""){
        $compare_arr1=$arr1;
        if($arr1_isline!=1 && !empty($arr1_keyname)){
            $compare_arr1=Arrays::return_linear_array($arr1,$arr1_keyname);
        }
        $compare_arr2=$arr2;
        if($arr2_is_line!=1 && !empty($arr2_keyname)){
            $compare_arr2=Arrays::return_linear_array($arr2,$arr2_keyname);
        }
        return array_diff($compare_arr1,$compare_arr2);
    }

    /**
     * 二维数组根据某个字段排序
     * @param array $array 要排序的数组
     * @param string $keys   要排序的键字段
     * @param string $sort  排序类型  SORT_ASC     SORT_DESC
     * @return array 排序后的数组
     * Hong 20190304
     */
    public static function array_sort($array, $keys, $sort = SORT_DESC) {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }

}


