<?php
namespace Catpow;
if(\PHP_VERSION_ID>=80000){
	include __DIR__.'/REST_Response-php8.php';
	return;
}
class REST_Response implements \ArrayAccess{
	public $status,$data=array();
	public function __construct(){
	}
	public function __toString(){
		return json_encode($this->data,0500);
	}
	
	public function offsetSet($offset,$value){
		if(empty($offset) || is_numeric($offset)){return;}
		$this->data[$offset]=$value;
	}
	public function offsetExists($offset){return isset($this->data[$offset]);}
	public function offsetUnset($offset){unset($this->data[$offset]);}
	public function offsetGet($offset){
		if(isset($this->data[$offset])){return $this->data[$offset];}
	}
}

?>