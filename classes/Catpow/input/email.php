<?php
namespace Catpow\input;

class email extends input{
	public static
		$validation=['email'];
	public static function fill_conf(&$conf){
		$conf['size']=24;
	}
}
?>