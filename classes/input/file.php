<?php
namespace Catpow\input;
use Catpow\util\FileType;

class file extends input{
	use FileInputTrait;
	public static
		$input_type="file",
		$validation=array('filetype','filesize'),
		$default_attr=array('accept'=>null);
	public function input(){
		return sprintf(
			'<input class="%s" type="%s" name="%s"%s/>',
			$this->className,static::$input_type,
			$this->name,$this->attr
		);
	}
}
?>