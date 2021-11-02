<?php
namespace Catpow\validation;

class phone_number extends pattern{
	public static $pattern='/^0\d{1,4}\-?\d{1,4}\-?\d{4}$/';
	
	public static function get_message($conf){
		return _('電話番号が正しくありません');
	}
}

?>