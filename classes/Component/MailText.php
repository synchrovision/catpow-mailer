<?php
namespace Catpow\Component;
class MailText extends Component{
	public static function translate($el,$doc){
		$level=self::getLevel($el);
		return self::arrayToNode(['div',['class'=>$el->tagName.' is-level-'.$level,'level'=>$level],[$el->childNodes]],$doc);
	}
}