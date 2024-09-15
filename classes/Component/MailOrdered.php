<?php
namespace Catpow\Component;
class MailOrdered extends Component{
	public static function translate($el,$doc){
		$items=[];
		$index=0;
		$level=self::getLevel($el);
		foreach($el->childNodes as $child){
			if(!is_a($child,\DOMElement::class)){continue;}
			if($child->tagName==='item'){
				$index++;
				$items[]=['tr',['class'=>$el->tagName.'-item'],[
					['td',['class'=>$el->tagName.'-item-pref'],[(string)$index]],
					['td',['class'=>$el->tagName.'-item-content'],[$child->childNodes]]
				]];
			}
		}
		return self::arrayToNode(['table',['class'=>$el->tagName.' is-level-'.$level],[
			['tbody',['class'=>$el->tagName.'__tbody'],$items]
		]],$doc);
	}
}