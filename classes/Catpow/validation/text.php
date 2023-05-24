<?php
namespace Catpow\validation;

class text extends validation{
	public static function is_valid(&$val,$input){
		$val=mb_convert_kana(htmlspecialchars(preg_replace("/(\r\n|\r)/","\n",$val)),'aKV');
		return true;
	}
	public static function get_message_format($conf){
		return __('不正な値です');
	}
}

?>