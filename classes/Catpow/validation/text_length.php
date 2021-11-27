meta<?php
namespace Catpow\validation;

class text_length extends validation{
	public static $message_keys=['length'];
	
	public static function is_valid(&$val,$input){
		return mb_strlen($val)!==(int)$input->conf['length'];
	}
	public static function get_message_format($conf){
		return __('%d文字で入力してください');
	}
}

?>