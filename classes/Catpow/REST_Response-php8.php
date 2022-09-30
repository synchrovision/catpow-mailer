<?php
namespace Catpow;
class REST_Response implements \ArrayAccess{
	public $status,$data=[];
	public function __construct(){
	}
	public function __toString(){
		return json_encode($this->data,0500);
	}

	public function offsetSet($offset,$value):void{
		if(empty($offset) || is_numeric($offset)){return;}
		$this->data[$offset]=$value;
	}
	public function offsetExists($offset):bool{return isset($this->data[$offset]);}
	public function offsetUnset($offset):void{unset($this->data[$offset]);}
	public function offsetGet($offset):mixed{
		if(isset($this->data[$offset])){return $this->data[$offset];}
	}
}