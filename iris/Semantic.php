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

class Semantic {	
	
	/*
	Ошибки класса
	*/	
	protected const ERRORS = array(
		'S01' => 'Для семантической обработки необходимо передать строку',
	);
	
	/*
	Знаки алфавита
	* 	
	*/
	private const ALPHABET = 'АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя';
	
	/*
	Группы окончаний:
	* 	
	*/	
	private const LEMMS = [
		/*
		Прилагательные
		*/	
		'ADJS' => array ('ее','ие','ые','ое','ими','ыми','ей','ий','ый','ой','ем','им','ым','ом', 'его','ого','ему','ому','их','ых','ую','юю','ая','яя','ою','ею'),
		/*
		Причастия
		*/	
		'PARTS' => array ('ивш','ывш','ующ','ем','нн','вш','ющ','ущи','ющи','ящий','щих','щие','ляя'), 
		/*
		Глаголы
		*/	
		'VERBS' => array ('ила','ыла','ена','ейте','уйте','ите','или','ыли','ей','уй','ил','ыл','им','ым','ен', 'ило','ыло','ено','ят','ует','уют','ит','ыт','ены','ить','ыть','ишь','ую','ю','ла','на','ете','йте', 'ли','й','л','ем','н','ло','ет','ют','ны','ть','ешь','нно'),
		/*
		Существительные
		*/	
		'NOUNS' => array ('а','ев','ов','ье','иями','ями','ами','еи','ии','и','ией','ей','ой','ий','й','иям','ям','ием','ем','ам','ом','о','у','ах','иях','ях','ы','ь','ию','ью','ю','ия','ья','я','ок', 'мва', 'яна', 'ровать','ег','ги','га','сть','сти','ики','ик'),
		/*
		Наречия
		*/	
		'ADVS' => array ('чно', 'еко', 'соко', 'боко', 'роко', 'имо', 'мно', 'жно', 'жко','ело','тно','льно','здо','зко','шо','хо','но'),
		/*
		Числительные
		*/	
		'NUMS' => array ('чуть','много','мало','еро','вое','рое','еро','сти','одной','двух','рех','еми','яти','ьми','ати','дного','сто','ста','тысяча','тысячи','две','три','одна','умя','тью','мя','тью','мью','тью','одним'),
		/*
		Союзы
		*/	
		'UNIS' => array ('более','менее','очень','крайне','скоре','некотор','кажд','други','котор','когд','однак', 'если','чтоб','хот','смотря','как','также','так','зато','что','или','потом','эт','тог','тоже','словно',	'ежели','кабы','коли','ничем','чем'),
		/*
		Предлоги
		*/	
		'PRES' => array ('в','на','по','из')
    ];
    
    /*
	Интерпретация результатов
	* 	
	*/    
    public const TYPES = [
		'UNKN' => 'Не определено',
		'PRES' => 'Предлог',
		'NUMS' => 'Числительное',
		'ADVS' => 'Наречие',
		'NOUNS' => 'Существительное',
		'VERBS' => 'Глагол',
		'PARTS' => 'Причастие',
		'ADJS' => 'Прилагательное',
		'UNIS' => 'Союзы'
    ];
        
	/**
	* Свойства
	* @var  protected string string Принимаемая строка для обработки.
	* @var  public string words Слова строки.
	* @var  public mixed result Результат.
	* 
	*/	
	protected $string;
	public $words;
	public $result;
		
	/**	
	 * 	Конструктор 	 
	 * @param string данные для обработки      
     * @return void
	*/
	
	public function __construct($string) {
		mb_internal_encoding('UTF-8');
		if (!is_string($string) or empty($string)) {
			throw new \Exception(self::ERRORS['S01']);
		} else {
			$this->string = trim($string);
			$this->words();
		}
	}
	
	/**	
	 * Отбрасывание из строки скобок вместе с содержимым
	*/
	
	public function remove_braces() {
		$this->string = preg_replace('#\(.*?\)#is','',$this->string);
		return $this;
	}
	
	/**	
	 * Преобразование в массив слов
	*/
	
	public function words() {
		$this->words = str_word_count($this->string,1,self::ALPHABET);	
		return $this;
	}
	
	/**	
	 * Очистка массива слов от предлогов
	*/
	
	public function remove_pres() {
		$this->remover(self::LEMMS['PRES']);
		return $this;
	}
	
	/**	
	 * Очистка массива слов от союзов
	*/
	
	public function remove_unis() {
		$this->remover(self::LEMMS['UNIS']);
		return $this;
	}
	
	/**	
	 * Очистка массива слов от числительных
	*/
	
	public function remove_nums() {
		$this->remover(self::LEMMS['NUMS']);
		return $this;
	}
	
	/**	
	 * Очистка массива слов от союзов, числительных и предлогов
	*/
	
	public function remove_all() {
		$this->remove_nums()->remove_pres()->remove_unis();
		return $this;
	}
		
	/**	
	 * Очистка массива слов от элементов, входящих в переданный массив
	*/
	
	public function remover($array) {
		if (isset($this->words)) { 
			foreach ($this->words as $key=>$value) {
				if (in_array($value,$array)) unset($this->words[$key]);
			}
			$this->words = array_values($this->words);
		} 
		return $this;
	}	
	
	/**	
	 * Разбор массива слов на части речи
	*/
	
	public function explore() {
		foreach ($this->words as $word) {
			$this->result[self::test_word($word)][] = $word;
		}
		return $this;
	}
	
	/**	
	 * Базовая обработка слова
	*/
	
	public static function prepare_word($word) {
		$word = trim(mb_strtolower($word));
		return str_replace('ё', 'е', $word);
	}
	
	/**	
	 * Определение части речи слова, вторым параметром передается необходимость интерпретации в текстовом виде
	*/
	
	public static function test_word($word,$interpretate=FALSE) {
		$result = 'UNKN'; //результат по умолчанию: 'не определено'
		$word = self::prepare_word($word);
		$lenght = mb_strlen($word);
		foreach (self::LEMMS as $name=>$set) {
			foreach ($set as $lemma) {
				switch ($name) {
					case 'PARTS': //причастие
						if (mb_strpos($word,$lemma)>=(round(2*$lenght)/5)) {
							$result = $name;
							break 2;
						}
					break;
					case 'UNIS': //союзы
						if (mb_substr($word,0,mb_strlen($lemma))==$lemma) {
							$result = $name;
							break 2;
						}
					break;
					case 'PRES': //предлоги
						if ($word == $lemma) {
							$result = $name;
							break 2;
						}
					break;
					default: //во всех остальных случаях
						if ($word == $lemma or mb_substr($word,-mb_strlen($lemma)) == $lemma) {
							$result = $name;
							break 2;
						}
				}
			}
		}		
		$result = ($interpretate) ? self::TYPES[$result] : $result;
		return $result;	
	}	

	/**	
	 * Избавление слов из массива слов по типам от окончаний по словарю 
	*/
	
	public function remove_endings() {
		if(empty($this->result)) $this->explore();
		foreach ($this->result as $type=>$set) {
			foreach ($set as $key=>$word) {
				$this->result[$type][$key] = self::remove_ending($word,$type);
			}
		}
		return $this;
	}
	
	/**	
	 * Избавление слова от окончаний по словарю, второй параметр - указатель на тип слова
	 * Возвращает обрезанное слово
	*/
	
	public static function remove_ending($word,$type=FALSE) {
		$word = self::prepare_word($word);
		$w_end = '';
		if (empty($type)) $type = self::test_word($word);
		if ($type!=='UNKN') { 
			foreach (self::LEMMS[$type] as $lemma) {
				if (mb_substr($word,-mb_strlen($lemma)) == $lemma) {
					$w_end = mb_substr($word,0, mb_strlen($word)-mb_strlen($lemma));
					break;
				}				
			}
		} else {
			$w_end = self::stem($word);
		}
		return $w_end;
	}
	
	/**	
	 * Стемминг слова по алгоритму Мартина Портера
	*/
	
	public static function stem($word) {
		$stem = new \Stem\LinguaStemRu;
		return $stem->stem_word($word);
	}
	
	/**	
	 * Поиск значимого слова для преобразования
	*/
	
	public function word() {
	
	}
	
	
}
