<?php
namespace Catpow\input;
use Catpow\util\FileType;

class file extends input{
	public static
		$input_type="file",
		$validation=array('filetype','filesize','filemove'),
		$default_attr=array('accept'=>null);
	public function output(){
		return htmlspecialchars($this->value['name']);
	}
	public function input(){
		return sprintf(
			'<input class="%s" type="%s" name="%s"%s/>',
			$this->className,static::$input_type,
			$this->name,$this->attr
		);
	}
	public function get_log_value(){
		$value=$this->form->values[$this->name];
		return $value['file_name'];
	}
}
?>