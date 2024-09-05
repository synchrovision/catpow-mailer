<?php
namespace Catpow\input;
abstract class input{
	public static
		$input_type=null,
		$output_type=null,
		$inline=true,
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
	public function output_as_text(){
		return $this->value;
	}
	public function input(){
		return sprintf(
			'<input class="%s" type="%s" name="%s" value="%s"%s/>',
			$this->className,isset(static::$input_type)?static::$input_type:'text',
			$this->name,$this->value,$this->attr
		);
	}
	public function render(){
		header("Content-Type: text/plain; charset=utf-8");
		echo $this->output();
	}
	
	public function reflect_to_log(&$log){
		$log[$this->conf['label']]=$this->output_as_text();
	}
	
	public function __get($name){
		switch($name){
			case 'type':return isset(static::$input_type)?static::$input_type:$this->conf['type'];
			case 'className':return sprintf('cmf-input-%s',$this->type);
			case 'blockClassName':{
				$blockClassName=sprintf('is-type-%s is-%s',$this->type,($this->conf['inline']??static::$inline)?'inline':'block');
				if(is_a($this,'\Catpow\input\text')){
					$blockClassName.=' is-text-input';
					if(!empty($this->conf['size'])){$blockClassName.=' has-size';}
				}
				elseif(is_a($this,'\Catpow\input\textarea')){
					$blockClassName.=' is-textarea-input';
					if(!empty($this->conf['rows'])){$blockClassName.=' has-rows';}
					if(!empty($this->conf['cols'])){$blockClassName.=' has-cols';}
				}
				elseif(is_a($this,'\Catpow\input\select')){$blockClassName.=' is-select-input';}
				elseif(is_a($this,'\Catpow\input\UI')){$blockClassName.=' is-ui-input';}
				return $blockClassName;
			}
			case 'label':return isset($this->conf['label'])?$this->conf['label']:$this->name;
			case 'received':return isset($this->form->received[$this->name])?$this->form->received[$this->name]:'';
			case 'value':
				if(isset($this->form->received[$this->name])){return $this->form->received[$this->name];}
				if(isset($this->form->values[$this->name])){
					return $this->form->values[$this->name];
				}
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
			case 'useScripts':{
				if(isset(static::$useScripts))return static::$useScripts;
				return null;
			}
			case 'useStyles':{
				if(isset(static::$useStyles))return static::$useStyles;
				return null;
			}
				
		}
		if(isset($this->conf[$name])){return $this->conf[$name];}
	}
	
}
?>