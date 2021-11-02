<?php
namespace Catpow\util;
class I18n{
	public static function setup($locale,$domain='messages'){
		setlocale(\LC_ALL,$locale);
		putenv("LANGUAGE={$locale}");
		bind_textdomain_codeset($domain,'UTF-8');
		bindtextdomain($domain, \FORM_DIR."/locale/");
		textdomain($domain);
	}
}

?>