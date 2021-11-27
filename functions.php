<?php
use Catpow\util\I18n;
use Catpow\util\Debug;

function __($str){
	return I18n::translate($str);
}
function _e($str){
	echo __($str);
}
function _h($str){
	return htmlspecialchars($str);
}
function _d($data){
	Debug::dump($data);
}