<?php
namespace Catpow\Component;
class MailMediaText extends Component{
	public static function translate($el,$doc){
		$children=[];
		$level=self::getLevel($el);
		foreach($el->childNodes as $child){
			if(!is_a($child,\DOMElement::class)){continue;}
			if($child->tagName==='media'){
				$children[]=['td',['class'=>$el->tagName.'__tbody-tr-td is-media'],[$child]];
			}
			elseif($child->tagName==='text'){
				$children[]=['td',['class'=>$el->tagName.'__tbody-tr-td is-text'],[$child]];
			}
		}
		return self::arrayToNode(['table',['class'=>$el->tagName.' is-level-'.$level,'level'=>$level],[
			['tbody',['class'=>$el->tagName.'__tbody'],[
				['tr',['class'=>$el->tagName.'__tbody-tr'],$children]
			]]
		]],$doc);
	}
}