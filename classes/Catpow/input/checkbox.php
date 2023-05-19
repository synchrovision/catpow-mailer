<?php
namespace Catpow\input;

class checkbox extends select{
	public static
		$input_type='checkbox';
	
	public function input(){
		return self::get_input($this->name,self::get_selections($this),$this->value);
	}
	
	public static function get_input($name,$sels,$vals,$className="cmf-input-checkbox"){
		$rtn=sprintf('<div class="%s">',$className);
		if(empty($vals))$vals=array('');
		$vals=(array)$vals;
		$item_format=
			'<label class="%s__item%6$s">'.
			'<input class="%1$s__item-input" type="checkbox" name="%s" value="%s"%s>'.
			'<span class="%1$s__item-text" for="%1$s">%s</label>'.
			'</label>';
		foreach((array)$sels as $i=>$s){
			if(is_array($s)){
				$rtn.=sprintf('<fieldset class="cmf-fieldset"><legend class="cmf-fieldset__ledgend">%s</legend>',$i);
				foreach($s as $ii=>$ss){
					$rtn.=sprintf(
						$item_format,
						$className,$name,$ss,
						in_array($ss,$vals)?' checked="checked"':'',
						is_int($ii)?$ss:$ii,
						($ii==='')?' is-bared':''
					);
				}
				$rtn.='</fieldset>';
			}else{
				$label_attr=isset($label_attrs[$s])?$label_attrs[$s]:'';
				$rtn.=sprintf(
					$item_format,
					$className,$name,$s,
					in_array($s,$vals)?' checked="checked"':'',
					is_int($i)?$s:$i,
					($i==='')?' is-bared':''
				);
			}
		}
		$rtn.='</div>';
		return $rtn;
	}
}
?>