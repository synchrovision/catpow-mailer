<?php
namespace Catpow\util;
class BasicAuth{
	public static function create($dir,$account,$password=null,$additional=null){
		if(empty($password)){$password=bin2hex(openssl_random_pseudo_bytes(8));}
		$txt_htaccess=sprintf(
			'AuthUserFile "%1$s/.htpasswd"'.chr(10).
			'AuthGroupFile /dev/null'.chr(10).
			'AuthName "Please enter your ID and password"'.chr(10).
			'AuthType Basic'.chr(10).
			'require valid-user'.chr(10),
			$dir
		);
		if($additional){$txt_htaccess.=$additional;}
		$txt_htpasswd=$account.':'.crypt($password,'zr').chr(10);
		file_put_contents($dir.'/.htaccess',$txt_htaccess);
		file_put_contents($dir.'/.htpasswd',$txt_htpasswd);
		return compact('accout','password');
	}
}

?>