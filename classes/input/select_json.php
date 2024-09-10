<?php
namespace Catpow\input;

class select_json extends select{
	public static function get_selections($input){
		$rtn=\Catpow\MailForm::get_json(isset($input->conf['value'])?$input->conf['value']:$input->conf['name']);
		if(isset($input->conf['addition'])){
			if(is_array($input->conf['addition'])){$rtn=array_merge($rtn,$input->conf['addition']);}
			else{$rtn[$input->conf['addition']]=0;}
		}
		return $rtn;
	}
}
?>