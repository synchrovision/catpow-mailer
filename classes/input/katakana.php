<?php
namespace Catpow\input;

class katakana extends text{
	public static $useScripts=array('kana_reflection');
	static $validation=array('katakana');
	
	public function input(){
		return sprintf(
			'<input class="%s" type="%s" name="%s" value="%s"%s%s/>',
			$this->className,isset(static::$input_type)?static::$input_type:'text',
			$this->name,$this->value,$this->attr,
			empty($this->conf['reflect'])?'':' data-katakana-reflection="'.$this->conf['reflect'].'"'
		);
	}
}
?>