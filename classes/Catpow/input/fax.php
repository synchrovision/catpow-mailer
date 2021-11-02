<?php
namespace Catpow\input;

class fax extends input{
	
	public static function output($meta,$prm){
		$val=$meta->value;
		if(empty($val)){return $val;}
		return sprintf('<span class="fax">%s</a>',$val);
	}
}
?>