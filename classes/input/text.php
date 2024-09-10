<?php
namespace Catpow\input;
class text extends input{
	public static
		$validation=array('text');
	public function output_as_text(){
		return htmlspecialchars_decode($this->form->values[$this->name]);
	}
}
?>