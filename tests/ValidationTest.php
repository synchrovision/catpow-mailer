<?php
namespace App\Phpunit;

use PHPUnit\Framework\TestCase;
use Catpow\validation\text;
use Catpow\validation\text_length;

class ValidationTest extends TestCase{
	public function test_text(){
		$text='０１２３ＡＢＣｱｲｳ';
		text::is_valid($text,null);
		$this->assertSame($text,'0123ABCアイウ');
	}
	public function test_text_length(){
		$text1='12345';
		$text2='あいうえお';
		$input=(object)['conf'=>['length'=>5]];
		$this->assertTrue(text_length::is_valid($text1,$input));
		$this->assertTrue(text_length::is_valid($text2,$input));
	}
}