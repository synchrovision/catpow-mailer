<?php
namespace Catpow\Component;
class MailButton extends Component{
	public static function translate($el,$doc){
		$level=self::getLevel($el);
		return self::arrayToNode(['table',['class'=>$el->tagName.' is-level-'.$level],[
			['tbody',['class'=>$el->tagName.'__tbody'],[
				['tr',['class'=>$el->tagName.'__tbody-tr'],[
					['td',['class'=>$el->tagName.'-item is-level-'.$level],[
						['a',['class'=>$el->tagName.'-item-button','href'=>$el->getAttribute('href')],[$el->childNodes]]
					]]
				]]
			]]
		]],$doc);
	}
}