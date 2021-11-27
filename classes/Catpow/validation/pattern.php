<?php
namespace Catpow\validation;

class pattern extends validation{
	public static function is_valid(&$val,$input){
		$pattern=isset(static::$pattern)?static::$pattern:$input->conf['pattern'];
		return preg_match($pattern,$val);
	}
	public static function get_message_format($conf){
		return __('入力形式が正しくありません');
	}
}

?>