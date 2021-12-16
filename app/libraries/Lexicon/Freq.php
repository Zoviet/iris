<?php
/**
 * Lexicon: Библиотека для автоматической генерации иерархии понятийных уровней.
 *
 * @copyright   Copyright (c) 2021, Zoviet <alexandr@asustem.ru>
 * @version 0.1
 * @link http://github.com/Zoviet/Lexicon
 * @author Zoviet (Alexandr Pavlov  / @Zoviet)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @site https://Zoviet.github.io/
 */

/**
 * Класс частотности фраз.  * 
 */

namespace Lexicon;

class Freq {	
	
	public static function prepare($text) {
		$good_parts = ['ADJS','PARTS','VERBS','NOUNS','ADVS','NUMS'];
		$Semantic = new Semantic();
		$text = mb_strtolower($text);	
		$text = str_replace(PHP_EOL,' ',$text);	
		$text = preg_replace('/[^ a-zа-яё\d]/ui','',$text);
		$text = explode(' ',$text);
		foreach ($text as $key=>$word) {
			$result = Semantic::test_word($word);
			if (!in_array($result,$good_parts) or mb_strlen($word)<2) unset ($text[$key]);			
		}
		return $text;
	}	
	
	public static function get($text,$deep=2) {
		$result = [];
		$data = self::prepare($text);
		for ($i=0;$i<count($data)-$deep;$i++) {
			$data[$i];
		}
		
	}
	

		
}
