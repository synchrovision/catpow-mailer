<?php
namespace Catpow\input;

trait FileInputTrait{
	public function output(){
		return htmlspecialchars($this->value['name']);
	}
	public function render($index=0){
		if(
			empty($fname=$this->value['file_name']) || 
			!file_exists($file=\UPLOADS_DIR.'/'.$fname)
		){
			header("HTTP/1.1 404 Not Found");
			return;
		}
		$mime=mime_content_type($file);
		$type=strstr($mime,'/',true);
		header('Content-type: '.$mime);
		switch($type){
			case 'video':
			case 'audio':
				$size = filesize($file);
				$fp = fopen($file,"rb");
				$etag = md5($_SERVER["REQUEST_URI"]).$size;
				if(@$_SERVER["HTTP_RANGE"]){
					list($start,$end) = sscanf($_SERVER["HTTP_RANGE"],"bytes=%d-%d");
					if(empty($end)) $end = $start + 1000000 - 1;
					if($end>=($size-1)) $end = $size - 1;
					header("HTTP/1.1 206 Partial Content");
					header("Content-Range: bytes {$start}-{$end}/{$size}");
					$size = $end - $start + 1;
					fseek($fp,$start);
				}
				header("Accept-Ranges: bytes");
				header("Content-Length: {$size}");
				header("Etag: \"{$etag}\"");

				if($size) echo fread($fp,$size);

				fclose($fp);

				break;
			default:
				readfile($file);
		}
	}
	public function get_log_value(){
		$fname=$this->form->values[$this->name]['file_name'];
		$this->form->save_file($this->name);
		return $fname;
	}
}
?>