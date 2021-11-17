<?php
namespace Catpow\validation;

class agreement extends validation{
	public static function is_valid(&$val,$input){
		return !empty($val);
	}
	public static function get_message_format($conf){
		return _('チェックしてください');
	}
}

?>