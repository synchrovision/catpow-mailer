<?php
namespace Catpow\input;

class submit_json extends select_json{
	public static
		$input_type='submit';
	public static function input($meta,$prm){
		$sels=self::get_selections($meta);
		return submit::get_input($meta->the_data_path,$meta->conf,$sels,$meta->value);
	}
}
?>