<?php
namespace Catpow;

class MailFormException extends \Exception{
	public $errors;
	public function __construct($errors){
		$this->errors=$errors;
		parent::__construct('MailFormException');
	}
}
?>