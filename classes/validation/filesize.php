<?php
namespace Catpow\validation;

class filesize extends validation{
	public static function is_valid(&$val,$input){
		if(isset($input->conf['filesize'])){
			if($val['size']>static::filesize_string_to_int($input->conf['filesize'])){
				return false;
			}
		}
		return true;
	}
	public static function get_karma($val,$input){
		if(isset($input->conf['filesize'])){
			if($val['size']>static::filesize_string_to_int($input->conf['filesize'])*2){
				return 'heavy_file_upload';
			}
		}
		return 0;
	}
	public static function get_message_format($conf){
		return sprintf(__('ファイルサイズは%s以下にしてください'),$conf['filesize']);
	}
	private static function filesize_string_to_int($size){
		if(preg_match('/(\d[\d,]*(?:\.\d+)?)([KMG])B/i',$size,$matches)){
			return (int)($matches[1]*['K'=>2<<10,'M'=>2<<20,'G'=>2<<30][$matches[2]]);
		}
		return (int)$size;
	}
}

?>