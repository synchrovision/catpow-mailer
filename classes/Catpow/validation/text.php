<?php
namespace Catpow\validation;

class text extends validation{
	public static function is_valid(&$val,$input){
		$val=mb_convert_kana(htmlspecialchars($val),'aKV');
		return true;
	}
	public static function get_message_format($conf){
		return _('不正な値です');
	}
}

?>