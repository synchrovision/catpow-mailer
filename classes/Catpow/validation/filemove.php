<?php
namespace Catpow\validation;
use Catpow\util\FileType;

class filemove extends validation{
	public static function is_valid(&$val,$input){
		$fname=uniqid().strtolower(strrchr($val['name'],'.'));
		$f=\UPLOADS_DIR.'/'.$fname;
		if(!is_dir($d=dirname($f))){mkdir($d,0755,true);}
		if(move_uploaded_file($val['tmp_name'],$f)){
			$val['tmp_name']=$f;
			$val['file_name']=$fname;
			return true;
		}
		return false;
	}
	public static function get_message_format($conf){
		return __('ファイルを保存できません');
	}
}

?>