<?php
namespace Catpow\input;

class UI extends input{
	public static $ui=null,$defaultProps=array();
	
	public function input(){
		return sprintf('<div class="cmf-ui" data-ui="%s">%s</div>',$this->ui,json_encode($this->props,0500));
	}
	public function __get($name){
		switch($name){
			case 'ui':{
				if(isset($this->conf[$name])){return $this->conf[$name];}
				if(isset(static::$ui))return static::$ui;
				return substr(strrchr(get_class($this),'\\'),1);
			}
			case 'props':{
				return array_merge(array_intersect_key($this->conf,static::$defaultProps),array('name'=>$this->name,'value'=>$this->value));
			}
		}
		return parent::__get($name);
	}
}
?>