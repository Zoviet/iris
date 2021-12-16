<?php
namespace Dataparser;

class Parser {
	
	public $strict = FALSE; //строка, которая считается окончанием заголовка листа
	public $offset = NULL; //номер строки с которой считается окончание заголовка	
	public $canons = []; //каноны разбора заголовков в массив
	public $global_canon; //сковзной шаблон разбора листов без шапки
	
	//данные всех листов, строка, которая считается концом заголовка, канон разбора
	public function parse_sheet($data,$offset=NULL,$canon=[]) 
	{	
		$return = new \STDClass;
		if (empty($data)) return $return->body = NULL;			
		if (is_array($canon) and !empty($canon)) {
			$this->canon = $canon;
			if (empty($offset)) $this->offset = 0; //если установлен канон разбора, но при этом не установлен отступ, то подозреваем, что его нет
		} else {
			$this->canon=NULL;
		}
		if (is_numeric($offset)) $this->offset = $offset;
		if (is_string($offset)) $this->strict = $offset;		
		$header = $this->header($data);
		$return->titles = $header->titles;
		$return->head = $header->head;
		$return->body = (!empty($header->key)) ? $this->clear(array_slice($data,$header->key,count($data))) : $data;			
		//если у нас страницы без заголовка (вторичный, то используется последний установленный глобальный шаблон)
		if ((!empty($this->canon) and !empty($return->head))) $this->global_canon = $this->canon($return->head)[0];		
		//если канон установился, то обрабатываем лист им
		//var_dump($return->titles);
		//var_dump($this->global_canon);
		
		if (!empty($this->global_canon) and !empty($return->body)) { //если у нас есть шаблон и то, что им обрабатывать			
			$return->data = $this->mask($this->global_canon,$return->body);
			$return->canon = $this->global_canon;
		} 
		
		return $return;	
	}	
	
	//обработка по канону	
	public function mask($canon,$data) 
	{
		$return = [];
		foreach ($data as $line=>$columns) {		
			foreach ($columns as $key=>$one) {
				$name = array_search($key,$canon);
				if ($name!==FALSE) {				
					$return[$line][$name] = $one;
				} else {
					$return[$line][$key] = $one;
				}
			}	
		}
		return $return;
	}
	
	
	/*
	 * Получение количества строк заголовков листов с жестким и нежестким критерием выборки. 
	 * Нежесткий критерий (FALSE): выборка до первой пустой строки или до строки с номерами столбцов.
	 * Жесткий критерий (string) - до первой строки, в которой встречается переданная строка
	 * @return номер строки, с которой начинаются основные данные
	 * 
	*/
	public function toplines($sheet) 
	{   
		$return = NULL;			
		if (!empty($this->offset)) return $this->offset; 
		if($this->strict!==FALSE) return $this->check_top_strict($sheet);
		$return = $this->check_top_nums($sheet);
		if (empty($return)) $return = $this->check_top_empty($sheet);
		if (empty($return)) $return = $this->check_top_titles($sheet);
		return $return;
	}
	
	//выделение заголовка по критерию наличия строки нумерации столбцов
	private function check_top_nums($sheet) {
		foreach ($sheet as $key=>$line) {
			$canon = array_keys(array_fill(1, count($line), 0));										
			$diff = array_diff($canon,$line);
			if (count($line)>10 and count($diff)<2) {
				$canon = array_keys(array_fill(1,count($line)-1,0));
				$diff = array_diff($canon,$line);
			}
			if (empty($diff) and count($line)>1) {				
				return $key;
			}	
		}
		return NULL;
	}
	
	//выделение заголовка по наличию пустой строки
	private function check_top_empty($sheet) {		
		foreach ($sheet as $key=>$line) {
			if (isset($sheet[$key+1]) and isset($sheet[$key-1]) and empty(self::array_delete($line)) and count(self::array_delete($sheet[$key+1]))>count(self::array_delete($sheet[$key-1]))) return $key+1;
		}
		return NULL;
	}
	
	//выделение заголовка по жесткому критерию
	private function check_top_strict($sheet) {
		foreach ($sheet as $key=>$line) {
			if (array_search($this->strict,$line)!==FALSE) return $key; 
		}
		return NULL;
	}
	
	//выделение заголовка по наличию заголовочных строк
	private function check_top_titles($sheet) {
		$return = NULL;		
		$counts = array_map(function($line) {
			$keys = array_keys($line);
			$first = array_shift($keys);
			return (!empty($line[$first])) ? 
			count(self::array_delete($line)) : count($line);
		}, $sheet);
		foreach ($sheet as $key=>$line) {
			if (isset($sheet[$key-1]) and $counts[$key-1]==1 and array_sum(array_slice($counts,0,$key-1)) == $key-1 and $counts[$key]>$counts[$key-1]) {
				$return = $key;
			}
		}
		return $return;		
	}	
	
	/*
	 * Выделение шапки листа
	 * 
	 */
	public function header($sheet) 
	{
		$return = new \STDClass;
		$return->titles = []; //заголовки листа
		$return->head = []; //шапка листа
		$return->key = $this->toplines($sheet);	
		if (empty($return->key)) return $return;
		$head = array_slice($sheet,0,$return->key+1);				
		if (!is_array($head)) return $return;
		$j=0;	
		foreach ($head as $line) {
			if (!empty($line)) {
				$test = array_values(self::array_delete($line));			
				if (count($test)==1 and is_string($test[0])) {				
					$return->titles[]=$test[0]; //получаем заголовки листа				
				} else {
					$line = (empty($this->canon)) ? array_merge(array_slice($line,0,1),self::array_delete(array_slice($line,1))) : $line;//решаем проблему размерности массива: больше, чем массив со значимой информацией					
					//$line = array_values($line);				
					for ($i=array_key_first($line); $i<=array_key_last($line); $i++) { //меняем столбцы на строки, приводя к матрице
						if (empty($line[$i]) and !empty($return->head[$i-1][$j])) {
							$return->head[$i][$j] = $return->head[$i-1][$j];
						} else {
							$return->head[$i][$j]= $line[$i];					
						}
					}					
				}
			}
			$j++;
		}		
		if (!empty($return->head)) $return->head = array_map(function($var){return trim(implode(' : ',self::array_delete($var)));},$return->head);
		return $return;		
	}
	
	/*
	 * Разбор заголовка по шаблону
	 * */
	
	public function canon($columns)
	{
		$return = array();
		$other = NULL;	
		$canons = $this->canon;
		foreach ($columns as $id=>$column) {
			foreach ($canons as $key=>$canon) {
				if (empty($canon)) $other=$key;			
				foreach ($canon as $key2=>$selector) {
					foreach ((array) $selector as $mask) {
						$position = strpos($column,$mask);
						if ($position!==FALSE) {																						
							$return[$key][$key2] = (int) $id;				
							unset ($columns[$id]);
							unset ($canons[$key][$key2]); //из канона убираем на случай скрытых полей
							break(2);							
						}	
					}
				}
			}			
		}	
		if (!empty($columns) and !empty($other)) {  //обрабатываем неохваченные шаблоном столбцы
			foreach ($columns as $key=>$value) {
				$return[$other][$value] = $key;
			}
		}
		usort($return, function($one,$two) {
			 if ($one == $two) {
				return 0;
			}
			return ($one < $two) ? -1 : 1;
		});
		return $return;
	}	

	
	/*
	 * Очистка листа данных от пустых строк и прочего мусора
	 * */
	private function clear($data) 
	{	$return = array();
		foreach ($data as $key=>$line) {
			$canon = (count($line)>0) ? array_keys(array_fill(1, count($line)-1, 0)) : array();						
			if (count(self::array_delete($line))>=1 and !empty(array_diff($canon,$line))) {
				$return[]=$line;
			} 
		}
		return $return;
	}
    
    static function array_delete(array $array, array $symbols = array(0,'',' ',NULL)) //delete null values
	{
		return array_diff($array, $symbols);
	}
	
	static function array_merge_keys($array1,$array2) {		
		for ($i=0;$i<max([count($array1),count($array2)]);$i++) {
			if (!isset($array1[$i]) or empty($array1[$i])) $array1[$i] = (isset($array2[$i])) ? $array2[$i] : NULL;
		}
		return $array1;
	}

} ?>
