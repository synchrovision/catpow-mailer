<?php
namespace Catpow\validation;
/**
* ユーザー入力の検証
* $confと$input_idを元に
* 入力検証で問題があれば$errorsにメッセージを追加する
*/

class validation{
	const UPLOAD_PHASE=1,INPUT_PHASE=2,CONFIRM_PHASE=4;
	public static $message_keys=array('label'),$phase=self::INPUT_PHASE;
	
	public static function is_valid(&$val,$input){return true;}
	
	public static function get_message_format($conf){
		return __('%sの入力が正しくありません');
	}
	public static function get_message($conf){
		$class_name=get_called_class();
		$base_class_name=substr($class_name,strrpos($class_name,'\\')+1);
		$message=isset($conf['validation_message'][$base_class_name])?$conf['validation_message'][$base_class_name]:static::get_message_format($conf);
		$message_vals=array();
		foreach(static::$message_keys as $message_key){
			$message_vals[]=isset($conf[$message_key])?$conf[$message_key]:'';
		}
		return vsprintf($message,$message_vals);
	}
}

?>