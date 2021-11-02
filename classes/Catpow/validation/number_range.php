<?php
namespace Catpow\validation;

class number_length extends validation{
	public static $message_keys=['min','max'];
	
	public static function is_valid(&$val,$input){
		return (float)$val>=(float)$input->conf['min'] and (float)$val<=(float)$input->conf['max'];
	}
	
	public static function get_message_format($conf){
		return _('%d〜%dの数値で入力してください');
	}
}

?>