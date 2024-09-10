<?php
namespace Catpow\input;

class UploadMedia extends UI {
	use FileInputTrait;
	public static 
		$validation=array('filetype','filesize'),
		$defaultProps=array('accept'=>null,'filesize'=>null,'text'=>null);
}
?>