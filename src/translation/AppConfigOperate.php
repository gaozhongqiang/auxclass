<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/23
 * Time: 15:30
 * 多语言配置
 */
namespace Auxclass\Translation;
use Auxclass\App\Arrays;
use Auxclass\App\Version;
use Auxclass\Father;
use Auxclass\Cache\Redis;
use Auxclass\Cache\Memcache;

class AppConfigOperate extends Father {
    const AREA_CACHE_ALL = 'getAreaALlZbqaAYNXAgz5JgLFd5';//地区缓存key【全部】
    const AREA_CACHE_SINGLE = 'getAreaSINGLEZbqaAYNXAgz5JgLFd5';//地区缓存key【部分】
    const LANGUAGE_PREG_EX = '#@[(a-z\d)_?]{1,}_\d{1,}@#i';//语言匹配规则
    //语言类型配置
    public static $languageType = array(
        1 => array('id' => 1, 'language' => '中文简体'),
        2 => array('id' => 2, 'language' => '中文繁体'),
        3 => array('id' => 3, 'language' => '英文'),
        4 => array('id' => 4, 'language' => '泰文'),
    );

    /**
     * 获取地区配置
     * @param $_this
     * @param int $is_all 是否返回全部的数据
     * @param null $is_foreign 返回单个海外的数据
     * @param bool $update_cache 是否强制更新缓存
     * @return array|mixed
     */
    public static function getAreaType($_this,$is_all = 1,$is_foreign = null,$update_cache = false){
        $out_arr = Memcache::get_all_domain_cache(self::AREA_CACHE_ALL);
        $not_all_arr = Memcache::get_all_domain_cache(self::AREA_CACHE_SINGLE);
        if(empty($out_arr) || empty($not_all_arr) || $update_cache) {
            $default_arr = array(
                -10 => array('id' => -10, 'value' => '未定义地区', 'search_field' => array(-10)),
                -2 => array('id' => -2, 'value' => '所有海外', 'search_field' => array(-2)),
            );
            $default = array(0=>array('id' => 0, 'value' => '所有地区', 'search_field' => array(0)));
            $base_arr = array(-1 => array('value' => '大陆', 'id' => -1));

            $_this->getmodel('I18n_regions_model');
            $area_data = $_this->I18n_regions_model->get_have_data(array(), "distinct(name) as value,is_foreign as id", true, 'order by is_foreign ASC');
            $area_data = array_merge($base_arr, $area_data);
            $area_data = array_values(ReturnSetArrKey($area_data,'id'));//数据表有可能添加大陆的  --- 20191015 gzq
            $area_data_length = count($area_data);
            $out_arr = $not_all_arr = array();
            for ($i = 0; $i < $area_data_length; $i++) {
                //基础数据遍历
                $not_all_arr[$area_data[$i]['id']] = array(
                    'id' => $area_data[$i]['id'],
                    'value' => $area_data[$i]['value'],
                    'search_data' => array($area_data[$i]['id']),
                    'search_field' => array($area_data[$i]['id'],0)
                );
            }
            //字典生成
            for ($i = 0; $i < $area_data_length; $i++) {
                //基础数据遍历
                $out_arr[$area_data[$i]['id']] = array(
                    'id' => $area_data[$i]['id'],
                    'value' => $area_data[$i]['value'],
                    'search_data' => array($area_data[$i]['id']),
                    'search_field' => array($area_data[$i]['id'],0)
                );

                $step = 1;
                while ($step < $area_data_length) {
                    for ($j = $i + 1; $j < $area_data_length; $j++) {
                        $key = min(array_keys($out_arr)) - 1;
                        if (in_array($key, array(-2, -10))) {
                            $key = $key - 1;
                        }
                        $temp_arr = array_slice($area_data, $j, $step);
                        $temp_value = implode('+', array_merge(array($area_data[$i]['value']), array_column_default($temp_arr, 'value')));
                        if (!in_array($temp_value, array_column_default($out_arr, 'value'))) {
                            $temp_search_field = array_merge(array($area_data[$i]['id'], $key,0), array_column_default($temp_arr, 'id'));
                            $out_arr[$key] = array(
                                'id' => $key,
                                'value' => $temp_value,
                                'search_data' => array($key),
                                'search_field' => $temp_search_field
                            );
                            //字典查询字段填入
                            foreach ($temp_search_field as $value) {
                                if (array_key_exists($value, $not_all_arr) && !in_array($key, $not_all_arr[$value]['search_field'])) {
                                    $not_all_arr[$value]['search_field'][] = $key;
                                }
                            }
                        }
                    }
                    $step = $step + 1;
                }

            }
            //查询字段生成
            foreach ($not_all_arr as $key => $value) {
                $out_arr[$key]['search_field'] = $value['search_field'];
            }
            $out_arr = $default_arr+$out_arr;
            krsort($out_arr);
            $out_arr = $default + $out_arr;
            Memcache::set_all_domain_cache(self::AREA_CACHE_ALL,$out_arr);
            Memcache::set_all_domain_cache(self::AREA_CACHE_SINGLE,$not_all_arr);
        }
        //var_dump($not_all_arr,$out_arr);
        $real_out_arr = $is_all == 1 ? $out_arr : $not_all_arr;

        if(!is_null($is_foreign)){
            return $real_out_arr[$is_foreign];
        }
        return $real_out_arr;
    }

    /**
     * 根据传入的游戏信息进行海外游戏过滤【地区】
     * @param $_this
     * @param array $param_arr 接口接收到的值，is_foreign值不能为空
     * @param boolean $return_first 是否返回第一个数据
     * @param array $game_data 手游的数据
     * @param array $h5game_data h5的数据
     * @return array
     */
    public static function flushGameArea($_this,$param_arr,$return_first = false,$game_data = array(),$h5game_data = array()){
        if(empty($game_data) && empty($h5game_data) || $param_arr['is_foreign'] == -1){
            if($return_first){  //同样做单游戏处理
                $game_data = empty($game_data) ? $game_data : array_shift($game_data);
                $h5game_data = empty($h5game_data) ? $h5game_data : array_shift($h5game_data);
            }
            return array($game_data,$h5game_data);
        }
        $_this->getmodel('App_game_area_model');
        $game_area_all_data = $_this->App_game_area_model->get_have_data(array(
            "(gid in (".format_sql_in($game_data,-1,2,'gid').") AND os_type != 3 or gid in (".format_sql_in($h5game_data,-1,2,'gid').") AND os_type=3)",
            "examine_state = 1",
            "is_foreign = '{$param_arr['is_foreign']}'"
        ),"*",true);
        $game_area_all_data = ReturnSetSmallValueToKey($game_area_all_data,'os_type',array(),array(1,2,3));
        $game_area_data = ReturnSetArrKey(array_merge($game_area_all_data[1],$game_area_all_data[2]),"gid");
        $h5game_area_data = ReturnSetArrKey($game_area_all_data[3],"gid");
        $game_data = array_reduce($game_data,function ($result,$item) use ($game_area_data){
            if(array_key_exists($item['gid'],$game_area_data)){
                $temp_arr = $game_area_data[$item['gid']];
                foreach ($item as $key => $value){
                    if(array_key_exists($key,$temp_arr)){
                        $item[$key] = $temp_arr[$key];
                    }
                }
                $result[] = $item;
            }
            return $result;
        },array());
        $h5game_data = array_reduce($h5game_data,function ($result,$item) use ($h5game_area_data){
            if(array_key_exists($item['gid'],$h5game_area_data)){
                $temp_arr = $h5game_area_data[$item['gid']];
                foreach ($item as $key => $value){
                    if(array_key_exists($key,$temp_arr)){
                        $item[$key] = $temp_arr[$key];
                    }
                }
                $result[] = $item;
            }
            return $result;
        },array());
        //单游戏处理
        if($return_first){
            $game_data = empty($game_data) ? $game_data : $game_data[0];
            $h5game_data = empty($h5game_data) ? $h5game_data : $h5game_data[0];
        }
        return array($game_data,$h5game_data);
    }

    /**
     * 根据传入的游戏信息进行海外游戏过滤【语言】
     * @param $_this
     * @param array $param_arr 接口接收到的值，language值不能为空
     * @param boolean $return_first 是否返回第一个数据
     * @param array $game_data 手游的数据
     * @param array $h5game_data h5的数据
     * @return array
     */
    public static function flushGameLanguage($_this,$param_arr,$return_first = false,$game_data = array(),$h5game_data = array()){
        if(empty($game_data) && empty($h5game_data) || $param_arr['language'] == 1){
            if($return_first){ //同样做单游戏处理
                $game_data = empty($game_data) ? $game_data : array_shift($game_data);
                $h5game_data = empty($h5game_data) ? $h5game_data : array_shift($h5game_data);
            }

            return array($game_data,$h5game_data);
        }
        $_this->getmodel('App_game_language_model');
        $game_language_all_data = $_this->App_game_language_model->get_have_data(array(
            "(gid in (".format_sql_in($game_data,-1,2,'gid').") AND os_type != 3 or gid in (".format_sql_in($h5game_data,-1,2,'gid').") AND os_type=3)",
            "examine_state = 1",
            "language = '{$param_arr['language']}'"
        ),"*",true);
        $game_language_all_data = ReturnSetSmallValueToKey($game_language_all_data,'os_type',array(),array(1,2,3));
        $game_language_data = ReturnSetArrKey(array_merge($game_language_all_data[1],$game_language_all_data[2]),"gid");
        $h5game_language_data = ReturnSetArrKey($game_language_all_data[3],"gid");
        $game_data = array_reduce($game_data,function ($result,$item) use ($game_language_data){
            if(array_key_exists($item['gid'],$game_language_data)){
                $temp_arr = $game_language_data[$item['gid']];
                foreach ($item as $key => $value){
                    if(array_key_exists($key,$temp_arr)){
                        $item[$key] = $temp_arr[$key];
                    }
                }
            }
            $result[] = $item;
            return $result;
        },array());
        $h5game_data = array_reduce($h5game_data,function ($result,$item) use ($h5game_language_data){
            if(array_key_exists($item['gid'],$h5game_language_data)){
                $temp_arr = $h5game_language_data[$item['gid']];
                foreach ($item as $key => $value){
                    if(array_key_exists($key,$temp_arr)){
                        $item[$key] = $temp_arr[$key];
                    }
                }
            }
            $result[] = $item;
            return $result;
        },array());
        //单游戏处理
        if($return_first){
            $game_data = empty($game_data) ? $game_data : $game_data[0];
            $h5game_data = empty($h5game_data) ? $h5game_data : $h5game_data[0];
        }
        return array($game_data,$h5game_data);
    }

    /**
     * 根据传入的手游礼包进行语言翻译
     * @param $_this
     * @param array $paramArr  接口接收到的值，language值不能为空
     * @param bool $returnFirst 是否返回第一个数据
     * @param array $cardData 手游礼包数据
     * @return array|mixed
     */
    public static function flushGameCardLanguage($_this, $paramArr, $returnFirst = false,$cardData = array())
    {
        if (empty($cardData) || $paramArr['language'] == 1) {
            if ($returnFirst) {
                return empty($cardData) ? $cardData : array_shift($cardData);
            }
            return $cardData;
        }
        $_this->getmodel('I18n_coupon_card_language_model');
        $cardLanguageInfo = $_this->I18n_coupon_card_language_model->get_have_data(array(
            "type = 2",
            "type_id in (".format_sql_in($cardData,-1,2,'id').")",
            "language = '{$paramArr['language']}'"
        ), 'type_id as id,name as cardname, user_agent, description, instruction', true);

        $cardLanguageInfo = Arrays::return_set_arr_key($cardLanguageInfo, 'id');

        $returnData = array_reduce($cardData, function($result, $item) use($cardLanguageInfo) {
            if (array_key_exists($item['id'], $cardLanguageInfo)) {
                foreach ($cardLanguageInfo[$item['id']] as $key => $value) {
                    if (array_key_exists($key, $item)) {
                        $item[$key] = $value;
                    }
                }
            }

            array_push($result, $item);
            return $result;
        }, array());

        if ($returnFirst) {
            return array_shift($returnData);
        }

        return $returnData;
    }

    /**
     * 根据传入的H5游戏礼包进行语言翻译
     * @param $_this
     * @param array $paramArr  接口接收到的值，language值不能为空
     * @param bool $returnFirst 是否返回第一个数据
     * @param array $cardData h5游戏礼包数据
     * @return array|mixed
     */
    public static function flushH5GameCardLanguage($_this, $paramArr, $returnFirst = false, $cardData = array())
    {
        if (empty($cardData) || $paramArr['language'] == 1) {
            if ($returnFirst) {
                return empty($cardData) ? $cardData : array_shift($cardData);
            }
            return $cardData;
        }
        $_this->getmodel('I18n_coupon_card_language_model');
        $cardLanguageInfo = $_this->I18n_coupon_card_language_model->get_have_data(array(
            "type = 3",
            "type_id in (".format_sql_in($cardData,-1,2,'id').")",
            "language = '{$paramArr['language']}'"
        ), 'type_id as id,name as cardname, user_agent, description, instruction', true);

        $cardLanguageInfo = Arrays::return_set_arr_key($cardLanguageInfo, 'id');

        $returnData = array_reduce($cardData, function($result, $item) use($cardLanguageInfo) {
            if (array_key_exists($item['id'], $cardLanguageInfo)) {
                foreach ($cardLanguageInfo[$item['id']] as $key => $value) {
                    if (array_key_exists($key, $item)) {
                        $item[$key] = $value;
                    }
                }
            }

            array_push($result, $item);
            return $result;
        }, array());

        if ($returnFirst) {
            return array_shift($returnData);
        }

        return $returnData;
    }

    /**
     * 根据传入的代金券进行语言翻译
     * @param $_this
     * @param array $paramArr  接口接收到的值，language值不能为空
     * @param bool $returnFirst 是否返回第一个数据
     * @param array $couponData 代金券数据
     * @return array|mixed
     */
    public static function flushCouponLanguage($_this, $paramArr, $returnFirst = false, $couponData = array())
    {
        if (empty($couponData) || $paramArr['language'] == 1) {
            if ($returnFirst) {
                return empty($couponData) ? $couponData : array_shift($couponData);
            }
            return $couponData;
        }
        $_this->getmodel('I18n_coupon_card_language_model');
        $couponLanguageInfo = $_this->I18n_coupon_card_language_model->get_have_data(array(
            "type = 1",
            "type_id in (".format_sql_in($couponData,-1,2,'coupon_type_id').")",
            "language = '{$paramArr['language']}'"
        ), 'type_id as coupon_type_id,name as coupon_name, user_agent as desc_info', true);

        $couponLanguageInfo = Arrays::return_set_arr_key($couponLanguageInfo, 'coupon_type_id');

        $returnData = array_reduce($couponData, function($result, $item) use($couponLanguageInfo) {
            if (array_key_exists($item['coupon_type_id'], $couponLanguageInfo)) {
                foreach ($couponLanguageInfo[$item['coupon_type_id']] as $key => $value) {
                    if (array_key_exists($key, $item)) {
                        $item[$key] = $value;
                    }
                }
            }

            array_push($result, $item);
            return $result;
        }, array());

        if ($returnFirst) {
            return array_shift($returnData);
        }

        return $returnData;
    }
    /**
     * 根据传入的游戏公告进行语言翻译
     * @param $_this
     * @param array $paramArr  接口接收到的值，language值不能为空
     * @param bool $returnFirst 是否返回第一个数据
     * @param array $gameNoticesData 公告内容
     * @return array|mixed
     */
    public static function flushGameNoticesLanaguage($_this,$paramArr,$returnFirst = false,$gameNoticesData = array()){
        if (empty($gameNoticesData) || $paramArr['language'] == 1) {
            if ($returnFirst) {
                return empty($gameNoticesData) ? $gameNoticesData : array_shift($gameNoticesData);
            }
            return $gameNoticesData;
        }
        $id_arr = array_column_default($gameNoticesData,'id');
        if(empty($id_arr)){
            if ($returnFirst) {
                return empty($gameNoticesData) ? $gameNoticesData : array_shift($gameNoticesData);
            }
            return $gameNoticesData;
        }
        $_this->getmodel('I18n_game_notices_language_model');
        $gameNoticesLanguageData = $_this->I18n_game_notices_language_model->get_have_data(array("pid in (".format_sql_in($id_arr).")","language = '{$paramArr['language']}'","type = 1"),"pid,notice_title,content",true);
        $gameNoticesLanguageData = Arrays::return_set_arr_key($gameNoticesLanguageData,'pid');

        $gameNoticesData = array_map(function ($value) use ($gameNoticesLanguageData){
            if(array_key_exists($value['id'],$gameNoticesLanguageData)){
                foreach ($gameNoticesLanguageData[$value['id']] as $key => $val){
                    if(array_key_exists($key,$value)){
                        $value[$key] = $val;
                    }
                }
            }
            return $value;
        },$gameNoticesData);
        if ($returnFirst) {
            return empty($gameNoticesData) ? $gameNoticesData : array_shift($gameNoticesData);
        }
        return $gameNoticesData;
    }
    /**
     * 语言+标识 ==》 翻译
     * @param array|string $return_data 返回的数据
     * @return array|string 数据替换
     */
    public static function transformErrorMsg($return_data){
        $language = Version::getI18nLanguage();
        if(isset($return_data['errormsg']) && !empty($return_data['errormsg']) && is_string($return_data['errormsg']) && preg_match_all(self::LANGUAGE_PREG_EX,$return_data['errormsg'],$match)){
            if(!empty($match)){
                foreach ($match[0] as $key => $value){
                    $temp_key = $value."@{$language}@"; //对应语言的缓存
                    $languageCache = Redis::get_all_domain_cache($temp_key);
                    if (empty($languageCache)) {
                        $temp_key = $value."@1@"; //固定为大陆
                        $return_data['errormsg'] = str_replace($value,Redis::get_all_domain_cache($temp_key),$return_data['errormsg']);
                    } else {
                        $return_data['errormsg']= str_replace($value,Redis::get_all_domain_cache($temp_key),$return_data['errormsg']);
                    }

                }
            }
        }
        return $return_data;
    }

    /**
     * 翻译数据  ---- 20191023 gzq
     * @param string $string
     * @return mixed|string
     */
    public static function transformString($string = ''){
        if(!is_string($string) || empty($string)){
            return $string;
        }
        $language = Version::getI18nLanguage();
        if(preg_match_all(self::LANGUAGE_PREG_EX,$string,$match)){
            if(!empty($match)){
                foreach ($match[0] as $key => $value){
                    $temp_key = $value."@{$language}@"; //对应语言的缓存
                    $languageCache = Redis::get_all_domain_cache($temp_key);
                    if (empty($languageCache)) {
                        $temp_key = $value."@1@"; //固定为大陆
                        $string = str_replace($value,Redis::get_all_domain_cache($temp_key),$string);
                    } else {
                        $string= str_replace($value,Redis::get_all_domain_cache($temp_key),$string);
                    }

                }
            }
        }
        return $string;
    }
}