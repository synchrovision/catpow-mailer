<?php
namespace Catpow\Component;
class MailAlt extends Component{
	public static function translate($el,$doc){
		return self::arrayToNode(['altbody',[],[$el->childNodes]],$doc);
	}
}