<?php
namespace Catpow\input;
/**
* 受信メールを保存するデータベース
*/
class mail extends database{
	static $has_parent=false;
	
	public static function fill_conf(&$conf){
		$conf=array_merge(array(
			'meta'=>array(),'alias'=>'mails','alias_template'=>array('mailform')
		),$conf);
		$conf['meta']=array_merge(array(
			'name'=>['type'=>'text','label'=>__('お名前')],
			'email'=>['type'=>'email','label'=>__('メールアドレス'),'required'=>1]
		),$conf['meta']);
	}
}
?>