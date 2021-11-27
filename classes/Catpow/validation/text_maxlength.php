meta<?php
namespace Catpow\validation;

class text_maxlength extends validation{
	public static $message_keys=['maxlength'];
	
	public static function is_valid(&$val,$input){
		return mb_strlen($val)<=(int)$input->conf['maxlength'];
	}
	public static function get_message_format($conf){
		return __('%d文字以内で入力してください');
	}
}

?>