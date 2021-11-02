<?php
namespace Catpow\input;
abstract class input{
	public static
		$input_type=null,
		$output_type=null,
		$validation=array();
	public $name,$conf,$form;
	
	public function __construct($name,$conf,$form){
		$this->name=$name;
		$this->conf=$conf;
		$this->form=$form;
	}
	public function output(){
		return $this->value;
	}
	public function input(){
		$attr='';
		foreach(array('placeholder','size','rows','cols','maxlength','autocomplete','min','max','step','pattern') as $attr_name){
			if(isset($this->conf[$attr_name]))$attr.=' '.$attr_name.'="'.$this->conf[$attr_name].'"';
		}
		if(!isset($this->conf['placeholder']))$attr.=' placeholder="'.(isset($this->conf['label'])?$this->conf['label']:'').'"';
		if(isset($this->conf['attr'])){
			foreach($this->conf['attr'] as $attr_name=>$attr_val){
				$attr.=' '.$attr_name.'="'.(is_array($attr_val)?implode(' ',$attr_val):$attr_val).'"';
			}
		}
		if($this->required){$attr.=' required';}
		return sprintf(
			'<input type="%s" name="%s" value="%s"%s/>',
			isset(static::$input_type)?static::$input_type:'text',
			$this->name,$this->value,$attr
		);
	}
	
	public function __get($name){
		switch($name){
			case 'received':return isset($this->form->reserved[$this->name])?$this->form->reserved[$this->name]:'';
			case 'value':
				if(isset($this->form->reserved[$this->name]))return $this->form->reserved[$this->name];
				if(isset($this->form->values[$this->name]))return $this->form->values[$this->name];
				return '';
			case 'required':return !empty($this->conf['required']) || substr($this->conf['label'],-1)==='*';
			case 'validation':return isset($this->conf['validation'])?$this->conf['validation']:static::$validation;
		}
		if(isset($this->conf[$name])){return $this->conf[$name];}
	}
	
}
?>