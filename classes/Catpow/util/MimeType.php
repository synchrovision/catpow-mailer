<?php
namespace Catpow\util;
class MimeType{
	public static $mime_types=array(
		'txt'=>'text/plain',
		'htm'=>'text/html',
		'html'=>'text/html',
		'md'=>'text/markdown',
		'php'=>'text/html',
		'csv'=>'text/csv',
		'css'=>'text/css',
		'js'=>'application/javascript',
		'json'=>'application/json',
		'xml'=>'application/xml',
		'swf'=>'application/x-shockwave-flash',
		'flv'=>'video/x-flv',

		// images
		'png'=>'image/png',
		'jpe'=>'image/jpeg',
		'jpeg'=>'image/jpeg',
		'jpg'=>'image/jpeg',
		'gif'=>'image/gif',
		'bmp'=>'image/bmp',
		'webp'=>'image/webp',
		'heic'=>'image/heic',
		'heif'=>'image/heif',
		'ico'=>'image/vnd.microsoft.icon',
		'tiff'=>'image/tiff',
		'tif'=>'image/tiff',
		'svg'=>'image/svg+xml',
		'svgz'=>'image/svg+xml',

		// archives
		'zip'=>'application/zip',
		'rar'=>'application/x-rar-compressed',
		'exe'=>'application/x-msdownload',
		'msi'=>'application/x-msdownload',
		'cab'=>'application/vnd.ms-cab-compressed',

		// audio/video
		'mp3'=>'audio/mpeg',
		'ogg'=>'audio/ogg',
		'ogv'=>'video/ogg',
		'qt'=>'video/quicktime',
		'mov'=>'video/quicktime',
		'mp4'=>'video/mpeg',
		'webm'=>'video/webm',

		// adobe
		'pdf'=>'application/pdf',
		'psd'=>'image/vnd.adobe.photoshop',
		'ai'=>'application/postscript',
		'eps'=>'application/postscript',
		'ps'=>'application/postscript',

		// ms office
		'doc'=>'application/msword',
		'rtf'=>'application/rtf',
		'xls'=>'application/vnd.ms-excel',
		'ppt'=>'application/vnd.ms-powerpoint',
		
		//fonts
		'ttf'=>'font/ttf',
		'woff'=>'font/woff',
		'woff2'=>'font/woff2',

		// open office
		'odt'=>'application/vnd.oasis.opendocument.text',
		'ods'=>'application/vnd.oasis.opendocument.spreadsheet',
	);

	public static function ext_to_mime($ext){
		substr($ext,1);
		return isset(static::$ext_mime_map[$ext])?static::$ext_mime_map[$ext]:null;
	}
	public static function mime_to_ext($mime){
		return array_search($mime,static::$mime_types);
	}
	public static function test_filetype($file,$accept){
		list($fileType,$fileSubtype)=explode('/',$file['type']);
		list($acceptType,$acceptSubtype)=explode('/',$accept);
		if($fileType!==$acceptType){return false;}
		if($acceptSubtype!=='*' && $fileSubtype!==$acceptSubtype){return false;}
		if($fileType==='image' && getimagesize($file['tmp_name'])===false){return false;}
		$finfo=new \finfo(\FILEINFO_MIME_TYPE);
		if($finfo->file($file['tmp_name'])!==$file['type']){return false;}
		return true;
	}
}

?>