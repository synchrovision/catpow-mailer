<?php
namespace Catpow\input;

class number extends input{
	public static
		$value_type='NUMERIC',
		$data_type='FLOAT',
		$validation=array('number'),
		$can_search_with_range=true;
	
	public static function output($meta,$prm){
		return number_format((float)$meta->value);
	}
}
?>