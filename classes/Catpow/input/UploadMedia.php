<?php
namespace Catpow\input;

class UploadMedia extends UI{
	public static 
		$validation=array('filetype','filesize','filemove'),
		$defaultProps=array('accept'=>null,'filesize'=>null,'text'=>null);
}
?>