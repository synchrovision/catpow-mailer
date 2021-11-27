<?php
namespace Catpow\util;
use POMO\MO;

class I18n{
	public static $locale,$translations;
	public static function setup_for_gettext($locale,$domain='messages'){
		setlocale(\LC_ALL,$locale);
		putenv("LANGUAGE={$locale}");
		bind_textdomain_codeset($domain,'UTF-8');
		bindtextdomain($domain, \FORM_DIR."/locale/");
		textdomain($domain);
	}
	public static function translate($str){
		if(is_null(static::$translations)){
			global $form;
			$mo_files=static::get_mo_files();
			static::$locale=\Locale::lookup(
				array_keys($mo_files),
				isset($form->config['locale'])?$form->config['locale']:\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'])
			);
			if(!empty($mo_files[static::$locale])){
				static::$translations=new MO();
				foreach($mo_files[static::$locale] as $mo_file){
					static::$translations->import_from_file($mo_file);
				}
			}
			else{static::$translations=false;}
		}
		if(empty(static::$translations)){return $str;}
		return static::$translations->translate($str);
	}
	public static function get_mo_files(){
		$rtn=['ja'=>[]];
		foreach([\MAILER_DIR,\FORM_DIR] as $dir){
			foreach(scandir($dir.'/languages') as $fname){
				if(substr($fname,-3)==='.mo'){$rtn[substr($fname,0,-3)][]=$dir.'/languages/'.$fname;}
			}
		}
		return $rtn;
	}
}

?>