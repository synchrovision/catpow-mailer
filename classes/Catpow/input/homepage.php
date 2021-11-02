<?php
namespace Catpow\input;

class homepage extends input{
	
	public static function output($meta,$prm){
		$val=$meta->value;
		if(empty($val)){return $val;}
		return sprintf('<a class="homepage" href="%1$s">%s</a>',$val);
	}
}
?>