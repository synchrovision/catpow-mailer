<?php
namespace Catpow\validation;

class number_length extends validation{
	public static $message_keys=array('length');
	
	public static function is_valid(&$val,$input){
		return preg_match(sprintf('/^[0-9]{%d}$/',$input->conf['length']),$val);
	}
	
	public static function get_message_format($conf){
		return __('%d桁の数字で入力してください');
	}
}

?>