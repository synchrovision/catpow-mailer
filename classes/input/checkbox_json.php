<?php
namespace Catpow\input;

class checkbox_json extends select_json{
	public static
		$input_type='checkbox';
	
	public function input($input){
		$sels=self::get_selections($input);
		return checkbox::get_input($input->name,$input->conf,$sels,$input->value);
	}
}
?>