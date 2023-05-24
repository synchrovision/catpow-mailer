<?php
namespace Catpow\input;
use Catpow\util\MimeType;

trait FileInputTrait{
	public function output(){
		return htmlspecialchars($this->value['name']);
	}
	public function output_as_text(){
		return $this->value['name'];
	}
	public function render($index=0){
		if(
			empty($fname=$this->value['file_name']) || 
			!file_exists($file=\TMP_DIR.'/'.$fname)
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
	public function reflect_to_log(&$log){
		$save_as=sprintf('%s-%03d.%s',$this->name,$log['id'],MimeType::mime_to_ext($this->value['type']));
		$this->form->save_file($this->value['file_name'],$save_as);
		$log[$this->conf['label']]=$save_as;
		$log[$this->conf['label'].' type']=$this->value['type'];
		$log[$this->conf['label'].' size']=$this->value['size'];
		$log[$this->conf['label'].' name']=htmlspecialchars($this->value['name']);
		return $save_as;
	}
}
?>