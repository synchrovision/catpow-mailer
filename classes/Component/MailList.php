<?php
namespace Catpow\Component;
class MailList extends Component{
	static $defaultMarker='●';
	public static function translate($el,$doc){
		$items=[];
		$marker=$el->hasAttribute('marker')?$el->getAttribute('marker'):static::$defaultMarker;
		$level=self::getLevel($el);
		foreach($el->childNodes as $child){
			if(!is_a($child,\DOMElement::class)){continue;}
			if($child->tagName==='item'){
				$items[]=['tr',['class'=>$el->tagName.'-item is-level-'.$level],[
					['td',['class'=>$el->tagName.'-item-marker'],[(string)$marker]],
					['td',['class'=>$el->tagName.'-item-content'],[$child->childNodes]]
				]];
			}
		}
		return self::arrayToNode(['table',['class'=>$el->tagName.' is-level-'.$level,'level'=>$level],[
			['tbody',['class'=>$el->tagName.'__tbody'],$items]
		]],$doc);
	}
}