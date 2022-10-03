<?php
namespace Catpow\input;

class textarea extends input{
	public static
		$validation=array('text'),
		$default_attr=array('placeholder'=>null,'rows'=>null,'cols'=>null,'autocomplete'=>null,'pattern'=>null);
	
	public function output(){
		return nl2br($this->value);
	}
	public function input(){
		return sprintf(
			'<textarea class="%s" name="%s"%s>%s</textarea>',
			$this->className,$this->name,$this->attr,$this->value
		);
	}
}
?>