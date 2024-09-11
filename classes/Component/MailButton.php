<?php
namespace Catpow\Component;
class MailButton extends Component{
	public static function translate($el,$doc){
		return self::arrayToNode(['table',['class'=>$el->tagName],[
			['tbody',['class'=>$el->tagName.'__tbody'],[
				['tr',['class'=>$el->tagName.'__tbody-tr'],[
					['td',['class'=>$el->tagName.'__tbody-tr-td'],[
						['a',['class'=>$el->tagName.'-button','href'=>$el->getAttribute('href')],[$el->childNodes]]
					]]
				]]
			]]
		]],$doc);
	}
}