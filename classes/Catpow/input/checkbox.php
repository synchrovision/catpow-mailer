<?php
namespace Catpow\input;

class checkbox extends select{
	public static
		$input_type='checkbox';
	
	public function input(){
		$sels=self::get_selections($this);
		return self::get_input($this->name,$sels,$this->value);
	}
	
	public static function get_input($name,$sels,$vals){
		$rtn='<div class="cmf-checkbox">';
		if(empty($vals))$vals=array('');
		$item_format=
			'<span class="cmf-checkbox__item">'.
			'<input class="cmf-checkbox__item__input" id="%s" type="checkbox" name="%s" value="%s"%s>'.
			'<label class="cmf-label cmf-label_checkbox" for="%1$s">%s</label>'.
			'</span>';
		foreach((array)$sels as $i=>$s){
			if(is_array($s)){
				$rtn.=sprintf('<fieldset class="cmf-fieldset"><legend class="cmf-fieldset__ledgend">%s</legend>',$i);
				foreach($s as $ii=>$ss){
					$rtn.=sprintf(
						$item_format,
						"chechbox-{$name}-{$i}-{$ii}",$name,$ss,
						in_array($ss,$vals)?' checked="checked"':'',
						is_int($ii)?$ss:$ii
					);
				}
				$rtn.='</fieldset>';
			}else{
				$label_attr=isset($label_attrs[$s])?$label_attrs[$s]:'';
				$rtn.=sprintf(
					$item_format,
					"chechbox-{$name}-{$i}",$name,$s,
					in_array($s,$vals)?' checked="checked"':'',
					is_int($i)?$s:$i
				);
			}
		}
		$rtn.='</div>';
		return $rtn;
	}
}
?>