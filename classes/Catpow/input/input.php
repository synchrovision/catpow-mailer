<?php
namespace Catpow\input;
abstract class input{
	public static
		$input_type=null,
		$output_type=null,
		$validation=array(),
		$default_attr=array('placeholder'=>null,'size'=>null,'maxlength'=>null,'autocomplete'=>null,'min'=>null,'max'=>null,'step'=>null,'pattern'=>null);
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
		return sprintf(
			'<input type="%s" name="%s" value="%s"%s/>',
			isset(static::$input_type)?static::$input_type:'text',
			$this->name,$this->value,$this->attr
		);
	}
	
	public function __get($name){
		switch($name){
			case 'type':return isset(static::$input_type)?static::$input_type:$this->conf['type'];
			case 'label':return isset($this->conf['label'])?$this->conf['label']:$this->name;
			case 'received':return isset($this->form->reserved[$this->name])?$this->form->reserved[$this->name]:'';
			case 'value':
				if(isset($this->form->reserved[$this->name]))return $this->form->reserved[$this->name];
				if(isset($this->form->values[$this->name]))return $this->form->values[$this->name];
				return '';
			case 'required':return !empty($this->conf['required']) || substr($this->label,-1)==='*';
			case 'validation':return isset($this->conf['validation'])?$this->conf['validation']:static::$validation;
			case 'attr':{
				$attr='';
				foreach(array_merge(static::$default_attr,array_intersect_key($this->conf,static::$default_attr)) as $attr_name=>$value){
					if($value){$attr.=' '.$attr_name.'="'.$value.'"';}
				}
				if(!isset($this->conf['placeholder']))$attr.=' placeholder="'.(isset($this->conf['label'])?$this->conf['label']:'').'"';
				if(isset($this->conf['attr'])){
					foreach($this->conf['attr'] as $attr_name=>$attr_val){
						$attr.=' '.$attr_name.'="'.(is_array($attr_val)?implode(' ',$attr_val):$attr_val).'"';
					}
				}
				if($this->required){$attr.=' required';}
				return $attr;
			}
				
		}
		if(isset($this->conf[$name])){return $this->conf[$name];}
	}
	
}
?>