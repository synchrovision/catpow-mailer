<?php
namespace Catpow\validation;
use Catpow\util\MimeType;

class filetype extends validation{
	public static function is_valid(&$val,$input){
		$accept=isset(static::$accept)?static::$accept:$input->conf['accept'];
		if(empty($accept)){return true;}
		$fileMimeType=mime_content_type($val['tmp_name']);
		if(is_string($accept)){$accept=explode(',',$accept);}
		foreach($accept as $i=>$mime){
			if($mime[0]==='.'){$accept[$i]=$mime=MimeType::ext_to_mime($mime);}
			if(MimeType::test_filetype($val,$mime)){return true;}
		}
		return false;
	}
	public static function get_message_format($conf){
		return __('許可されないファイル形式です');
	}
}

?>