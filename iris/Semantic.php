<?php
/**
 * Iris: Библиотека для работы с веерными матрицами
 *
 * @copyright   Copyright (c) 2021, Zoviet <alexandr@asustem.ru>
 * @license     GNU GENERAL PUBLIC LICENSE Version 3
 */

/**
 * Semantic: класс обработки понятий уровней и предметов.  * 
 * Функционал: выделение стемов, поиск окончаний, разбор предложений, генерация существительных из прилагательных и прилагательных из существительных
 */

namespace iris;

use \Stem\LinguaStemRu;
use iris\Config\Errors;

class Semantic {
	
	/**
	* Свойства
	* @var  protected string string Принимаемая строка для обработки.
	* @var  public string return Возвращаемая строка.
	* 
	*/
	
	public $string;
	public $return;
	
	/**	
	 * 	Конструктор 	 
	 * @param string данные для обработки      
     * @return void
	*/
	
	public function __construct($string) {
		if (!is_string($string)) {
			throw new \Exception(Errors::semantic['S01']);
		} else {
			$this->string = $string;
		}
	}
	
	/**	
	 * Отбрасывание скобок	вместе с содержимым
	*/
	
	public function remove_braces() {
		$this->string = preg_replace('#\(.*?\)#is','',$this->string);
		return $this;
	}
	
	/**	
	 * Преобразование в массив слов с отбрасыванием предлогов
	*/
	
	public function words() {
		$words = str_word_count($this->string,1,'АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя');
		var_dump($words);
		foreach ($words as $key=>$value) {
			if (strlen($value)<5) unset($words[$key]);
		}
		return array_values($words);
	}
	
	/**	
	 * Поиск значимого слова для преобразования
	*/
	
	public function word() {
	
	}
	
	
	
}
