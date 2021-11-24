<?php
namespace Catpow\input;

class select extends input{
	static $input_type='select';
	public function input(){
		$val=$this->value;
		$rtn=sprintf(
			'<select name="%s"%s>',
			$this->name,$this->attr
		);
		$rtn.=sprintf('<option value="">%s</option>',isset($conf['defaultLabel'])?$conf['defaultLabel']:'---');
		foreach(static::get_selections($this) as $i=>$s){
			if(is_array($s)){
				$rtn.=sprintf('<optgroup label="%s">',$i);
				foreach($s as $ii=>$ss){
					$rtn.=sprintf('<option value="%s"%s>%s</option>',$ss,($ss==$val)?' selected="selected"':'',is_int($ii)?$ss:$ii);
				}
				$rtn.='</optgroup>';
			}else{
				$rtn.=sprintf('<option value="%s"%s>%s</option>',$s,($s==$val)?' selected="selected"':'',is_int($i)?$s:$i);
			}
		}
		$rtn.='</select>';
		return $rtn;
	}
	public static function get_selections($input){
		$rtn=$input->conf['value'];
		if(isset($meta->conf['addition'])){
			if(is_array($meta->conf['addition'])){$rtn=array_merge($rtn,$meta->conf['addition']);}
			else{$rtn[$meta->conf['addition']]=0;}
		}
		return $rtn;
	}
}
?>