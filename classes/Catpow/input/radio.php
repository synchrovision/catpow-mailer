<?php
namespace Catpow\input;

class radio extends select{
	public static
		$input_type='radio';
	public function input(){
		return self::get_input($this->name,self::get_selections($this),$this->value);
	}
	public static function get_input($name,$sels,$val,$className="cmf-input-radio"){
		$rtn=sprintf('<div class="%s">',$className);
		foreach($sels as $i=>$s){
			$rtn.=sprintf(
				'<label class="%s__item">'.
				'<input class="%1$s__item-input" type="radio" name="%s" value="%s"%s>'.
				'<span class="%1$s__item-text">%s</span>'.
				'</label>',
				$className,$name,$s,
				($s==$val)?' selected="selected"':'',is_int($i)?$s:$i
			);
		}
		$rtn.='</div>';
		return $rtn;
	}
}
?>