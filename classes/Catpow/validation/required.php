<?php
namespace Catpow\validation;

class required extends validation{
	public static function is_valid(&$val,$input){
		return !empty($val);
	}
	public static function get_message_format($conf){
		return _('入力して下さい');
	}
}

?>