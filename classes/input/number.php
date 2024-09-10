<?php
namespace Catpow\input;

class number extends text{
	public static $validation=array('number');
	
	public function output(){
		return number_format((float)$this->value);
	}
}
?>