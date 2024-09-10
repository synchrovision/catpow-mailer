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
				$el->parentNode->replaceChild($class_name::translate($el,$doc),$el);
			}
		}
		if($el->hasChildNodes()){
			foreach($el->childNodes as $child){
				self::_convert($child,$doc);
			}
		}
	}
	public static function translate($el,$doc){
		$newEl=self::arrayToNode(['table',['class'=>$el->tagName],[
			['tbody',['class'=>$el->tagName.'__tbody'],[
				['tr',['class'=>$el->tagName.'__tbody-tr'],[
					['td',['class'=>$el->tagName.'__tbody-tr-td'],$el->childNodes]
				]]
			]]
		]],$doc);
		return $newEl;
	}
	public static function arrayToNode($array,$doc){
		$el=$doc->createElement($array[0]);
		if(!empty($array[1])){
			foreach($array[1] as $key=>$val){
				$el->setAttribute($key,$val);
			}
		}
		if(!empty($array[2])){
			foreach($array[2] as $child){
				if(is_array($child)){
					$el->append(self::arrayToNode($child,$doc));
				}
				else{
					$el->append($child);
				}
			}
		}
		return $el;
	}
}

?>