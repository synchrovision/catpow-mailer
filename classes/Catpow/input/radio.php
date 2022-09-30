<?php
namespace Catpow\input;

class radio extends select{
	public static
		$input_type='radio';
	public function input(){
		$val=$this->value;
		$rtn=sprintf(
			'<div %s>',
			$this->attr
		);
		foreach(static::get_selections($this) as $i=>$s){
			$rtn.=sprintf(
				'<label><input type="radio" name="%s" value="%s"%s>%s</label>',
				$this->name,$s,
				($s==$val)?' selected="selected"':'',is_int($i)?$s:$i
			);
		}
		$rtn.='</div>';
		return $rtn;
	}
}
?>