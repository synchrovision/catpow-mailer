<?php
namespace Catpow\Component;
abstract class Component{
	public static function convert($html){
		$doc=new \DOMDocument();
		$doc->loadHTML(mb_encode_numericentity($html,[0x80,0xffff,0,0xffff],'UTF-8'),\LIBXML_HTML_NOIMPLIED|\LIBXML_HTML_NODEFDTD|\LIBXML_NOERROR);
		foreach($doc->childNodes??[] as $el){
			self::_convert($el,$doc);
		}
		$html=mb_decode_numericentity($doc->saveHTML(),[0x80,0xffff,0,0xffff],'UTF-8');
		return $html;
	}
	private static function _convert($el,$doc){
		if(!is_a($el,\DOMElement::class)){return;}
		if(strpos($el->tagName,'-')!==false){
			$name=implode('',array_map('ucfirst',explode('-',$el->tagName)));
			$class_name="\\Catpow\\Component\\{$name}";
			if(class_exists($class_name)){
				$newEl=$class_name::translate($el,$doc);
				$el->parentNode->replaceChild($newEl,$el);
				$el=$newEl;
			}
		}
		if($el->hasChildNodes()){
			for($i=0;$i<$el->childNodes->length;$i++){
				self::_convert($el->childNodes->item($i),$doc);
			}
		}
	}
	public static function translate($el,$doc){
		$children=[
			'header'=>null,
			'body'=>['tbody',['class'=>$el->tagName.'__tbody'],[]],
			'footer'=>null
		];
		foreach($el->childNodes??[] as $child){
			if(is_a($child,\DOMText::class) && empty(trim($child->wholeText))){continue;}
			if(is_a($child,\DOMElement::class)){
				if($child->tagName==='header'){
					$children['header']=['thead',['class'=>$el->tagName.'__thead',$child->attributes],[
						['tr',['class'=>$el->tagName.'__thead-tr'],[
							['th',['class'=>$el->tagName.'__thead-tr-th'],[$child->childNodes]]
						]]
					]];
				}
				elseif($child->tagName==='row'){
					$children['body'][2][]=['tr',['class'=>$el->tagName.'__tbody-tr',$child->attributes],[
						['td',['class'=>$el->tagName.'__tbody-tr-td'],[$child->childNodes]]
					]];
				}
				elseif($child->tagName==='footer'){
					$children['footer']=['tfoot',['class'=>$el->tagName.'__tfoot',$child->attributes],[
						['tr',['class'=>$el->tagName.'__tfoot-tr'],[
							['td',['class'=>$el->tagName.'__tfoot-tr-td'],[$child->childNodes]]
						]]
					]];
				}
			}
			else{
				$children['body'][2][]=['tr',['class'=>$el->tagName.'__tbody-tr'],[
					['td',['class'=>$el->tagName.'__tbody-tr-td'],[$child]]
				]];
			}
		}
		$newEl=self::arrayToNode(['table',['class'=>$el->tagName],$children],$doc);
		return $newEl;
	}
	public static function arrayToNode($array,$doc){
		$el=$doc->createElement($array[0]);
		if(!empty($array[1])){
			foreach($array[1] as $key=>$val){
				if(is_a($val,\DOMNamedNodeMap::class)){
					foreach($val as $attr){
						if($attr->name==='class' && $el->hasAttribute('class')){
							$el->setAttribute('class',$el->getAttribute('class').' '.$attr->value);
						}
						else{
							$el->setAttribute($sub_key,$sub_val);
						}
					}
				}
				else{
					$el->setAttribute($key,$val);
				}
			}
		}
		if(!empty($array[2])){
			foreach((array)$array[2] as $child){
				if(empty($child)){continue;}
				if(is_a($child,\DOMNodeList::class)){
					while($child->length){
						$el->append($child->item(0));
					}
				}
				elseif(is_array($child)){
					$el->append(self::arrayToNode($child,$doc));
				}
				else{
					$el->append($child);
				}
			}
		}
		return $el;
	}
	public static function getLevel($el){
		if(is_a($el,\DOMElement::class) && $el->hasAttribute('level')){
			return $el->getAttribute('level');
		}
		if(is_a($el->parentNode,\DOMElement::class)){
			if($el->parentNode->hasAttribute('level')){
				return $el->parentNode->getAttribute('level')+1;
			}
			return self::getLevel($el->parentNode);
		}
		return 1;
	}
}

?>