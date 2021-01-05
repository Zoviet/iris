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
		'PRES' => array ('в','на','по','из','и')
    ];
    
    /*
	Интерпретация результатов
	* 
	* Части речи	
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
    
    /*
	* Члены предложения
	*/   
    
    public const PARTS = [
		'UNKN' => 'Не определено',
		'SUBJ' => 'Подлежащее',
		'ADDN' => 'Дополнение',
		'PRED' => 'Сказуемое',
		'DFN' => 'Определение',
		'CIRC' => 'Обстоятельство'
    ];
    
        
	/**
	* Свойства
	* @var  protected string string Принимаемая строка для обработки.
	* @var  public string words Слова строки.
	* @var  public array result Массив слов по частям речи (в зависимости от глубины обработки: лишенных оснований или нет).
	* 
	*/	
	protected $string;
	public $words = array();
	public $result = array();
		
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
		if (empty($this->words)) $this->words();
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
		$result[0] = 'UNKN'; //результат по умолчанию: 'не определено'
		$word = self::prepare_word($word);
		$lenght = mb_strlen($word);
		foreach (self::LEMMS as $name=>$set) {			
			foreach ($set as $lemma) {
				$lemma_len = mb_strlen($lemma);
				$ver = round(($lemma_len/$lenght)*100); //доверительная вероятность каждого результата				
				switch ($name) {					
					case 'PARTS': //причастие
						if (mb_strpos($word,$lemma)>=(round(2*$lenght)/5)) {
							$result[$ver] = $name; //результаты храним в массиве, где длина совпадения леммы = доверительной вероятности
							break 2;
						}
					break;
					case 'UNIS': //союзы
						if (mb_substr($word,0,$lemma_len)==$lemma) {
							$result[$ver] = $name;
							break 2;
						}
					break;
					case 'PRES': //предлоги	
						if ($word == $lemma) {						
							$result[$ver] = $name;
							break 2;
						}
					break;
					default: //во всех остальных случаях					
						if ($word == $lemma or mb_substr($word,-mb_strlen($lemma)) == $lemma) {						
							$result[$ver] = $name;
							break 2;
						}
				}
			}
		}									
		ksort($result); //выбираем результат с наибольшей доверительной вероятностью		
		$result = array_pop($result); 
		$result = ($interpretate) ? self::TYPES[$result] : $result;
		return $result;	
	}	

	/**	
	 * Избавление слов из массива слов по типам от окончаний по словарю по массиву типов
	*/
	
	protected function remove_endings() {
		if(empty($this->result)) $this->explore();
		foreach ($this->result as $type=>$set) {
			foreach ($set as $key=>$word) {
				$this->result[$type][$key] = self::remove_ending($word,$type);				
			}
		}
		return $this;
	}
	
	/**	
	 * Избавление слов из массива слов по типам от окончаний по словарю по массиву слов
	*/
	
	protected function remove_words_endings() {
		if(empty($this->words)) $this->words();
		foreach ($this->words as $word) {
			$this->words[] = self::remove_ending($word);	
		}
		return $this;
	}
	
	
	/**	
	 * Обертка для методов избавления от окончаний, не чувствительная к типу обработки
	*/
	
	public function stemming() {
		if(!empty($this->result)) {
			$this->remove_endings();
		} else {
			$this->remove_words_endings();
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
		if ($type!=='UNKN' and $type!=='PARTS') { 
			foreach (self::LEMMS[$type] as $lemma) {
				if (mb_substr($word,-mb_strlen($lemma)) == $lemma) {
					$w_end = mb_substr($word,0, mb_strlen($word)-mb_strlen($lemma));
					if (mb_strlen($w_end) == 0) $w_end = $word;
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
	 * Выделение значимого существительного из массива слов. 
	 * Если не найдено, возвращает NULL.	 
	*/
	
	public function find_noun() {
		$result = NULL;
		if(empty($this->result)) $this->explore(); //если нет никакого предварительного результата, не с окончаниями, ни без
		if (isset($this->result['NOUNS'])) {
			$result = $this->result['NOUNS'][0];
		}
		return $result;
	}
	
	/**	
	 * Выделение значимого прилагательного из массива слов. 
	 * Если не найдено, возвращает NULL.	 
	*/
	
	public function find_adj() {
		$result = NULL;
		if(empty($this->result)) $this->explore(); //если нет никакого предварительного результата, не с окончаниями, ни без
		if (isset($this->result['ADJS'])) {
			$count_nouns = count($this->result['NOUNS']); //количество существительных
			$count_adjs = count($this->result['ADJS']); //количество прилагательных
			$count_verbs = count($this->result['VERBS']); //количество глаголов
			$words = count($this->words); //длина фразы	
			$result = $this->result['ADJS'][0];
		}
		return $result;
	}
	
	/**	
	 * Выделение значимого глагола из массива слов. 
	 * Если не найдено, возвращает NULL.	 
	*/
	
	public function find_verb() {
		$result = NULL;
		if(empty($this->result)) $this->explore(); //если нет никакого предварительного результата, не с окончаниями, ни без
		if (isset($this->result['VERBS'])) {	
			$result = $this->result['VERBS'][0];
		}
		return $result;
	}
	
	/**	
	 * Получение удаленного окончания слова сравнением строк (т.к. могут использоваться разные методы избавления от окончаний)
	*/
	
	protected function get_ending($word,$stem) {
		return trim(str_replace($stem,'',$word));
	}
		
	/**	
	 * Получение прилагательного из значимого существительного
	 * Если не найдено, возвращает NULL.	 
	*/
	
	public function get_adj() {
		$result = NULL;
		$suffixs = array('н', 'ан', 'ин', 'ий', 'ов', 'ск', 'ист', 'чат', 'лив');
		$adj = $this->find_adj();
		if (!empty($adj)) {
			$stem = self::remove_ending($adj,'ADJS'); //убираем окончание
			$ending = $this->get_ending($adj,$stem); //получаем убранное окончание
			
		}
		return $result;
	}
	
}
