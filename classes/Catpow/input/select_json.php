<?php
namespace Catpow\input;

class select_json extends select{
	public static function get_selections($meta){
		$rtn=\Catpow\MailForm::get_json($meta->conf['value']??$meta->conf['name']);
		if(isset($meta->conf['addition'])){
			if(is_array($meta->conf['addition'])){$rtn=array_merge($rtn,$meta->conf['addition']);}
			else{$rtn[$meta->conf['addition']]=0;}
		}
		return $rtn;
	}
}
?>