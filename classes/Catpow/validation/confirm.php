<?php
namespace Catpow\validation;

class confirm extends validation{
	public static $phase=self::CONFIRM_PHASE;
	
	public static function is_valid(&$val,$input){
		if(!isset($input->conf['reflect'])){return false;}
		return $val===$input->form->inputs[$input->conf['reflect']]->value;
	}
	public static function get_message($conf){
		return __('入力内容が異なります');
	}
}

?>