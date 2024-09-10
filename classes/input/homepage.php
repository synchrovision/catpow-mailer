<?php
namespace Catpow\input;

class homepage extends text{
	
	public function output(){
		$val=$this->value;
		if(empty($val)){return $val;}
		return sprintf('<a class="homepage" href="%1$s">%s</a>',$val);
	}
}
?>