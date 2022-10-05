<?php
namespace Catpow\input;

class price extends text{
	public static $validation=array('number');
	
	public function output(){
		return number_format((int)$this->value);
	}
}
?>