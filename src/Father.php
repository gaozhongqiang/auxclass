<?php
namespace Auxiliary;
class Father{
	protected $config=array();
    protected $object_data;
	public function __construct($config=array()){
		$this->config=$config;
        /**************************
         * 判断条件    空     null
         * is_null  false   true
         * empty    true    true
         ***************************/
		$this->object_data=null;
	}
	public function set_config_item($key,$value){
		$this->config[$key]=$value;
	}
	public function get_config_item($key){
		return isset($this->config[$key]) ? $this->config[$key] : "";
	}
	public function set_config_array($item_array){
		$this->config=array_merge($this->config,$item_array);
	}
	public function get_config_array(){
		return $this->config;
	}
	public function get_object_data(){
	    return $this->object_data;
    }

    public function set_object_data($object_data){
	    $this->object_data=$object_data;
    }
}

