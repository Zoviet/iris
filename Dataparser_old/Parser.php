<?php
namespace Dataparser;

class Parser {
	
	public $strict = FALSE; //строка, которая считается окончанием заголовка листа
	public $offset = NULL; //номер строки с которой считается окончание заголовка	
	public $canons = []; //каноны разбора заголовков в массив
	
	//данные всех листов, строка, которая считается концом заголовка, канон разбора
	public function parse_sheet($data,$offset=NULL,$canon=[]) 
	{	
		$return = new \STDClass;			
		if (is_array($canon) and !empty($canon)) {
			$this->canon = $canon;
			if (empty($offset)) $this->offset = 1; //если установлен канон разбора, но при этом не установлен отступ, то подозреваем, что это первая строка листа
		}
		if (is_numeric($offset)) $this->offset = $offset;
		if (is_string($offset)) $this->strict = $offset;		
		$header = $this->header($data);
		$return->titles = $header->titles;
		$return->head = $header->head;
		$return->body = ($header->key!==FALSE) ? $this->clear(array_slice($data,$header->key+1,count($data))) : $data;
		if ((!empty($this->canons) and !empty($return->head))) $return->canon = $this->canon($return->head);
		if (!empty($return->canon) and !empty($return->body)) { //если у нас есть шаблон и то, что им обрабатывать
			$return->data = $this->mask($return->canon,$return->body);
		} 
		var_dump($return);
		return $return;		
	}	
	
	//обработка по канону	
	public function mask($canon,$data) 
	{
		$return = [];
		foreach ($canon as $key=>$key2) {
			
		}
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
		$return = FALSE;
		$set = FALSE;
		if (!empty($this->offset)) return $this->offset; 
		while ($return==FALSE and key($sheet)!==NULL) { //не foreach для того, чтобы не обходить весь лист до конца, а лишь до первого вхождения
			$line = current($sheet);
			$key = key($sheet);			
			if($this->strict==FALSE and empty($return)) {
				if (count($line)>0) {
					$canon = array_keys(array_fill(1, count($line)-1, 0));				
					if (empty(array_diff($canon,$line))) {$return = $key-1;}
					$nextline = next($sheet);										
					if (count(self::array_delete($line))==1 and $key>0 and is_array($nextline) and count(self::array_delete(self::array_merge_keys($line,$nextline)))>1) $return = $key;
				} else {
					next($sheet);
				}			
			} else {				
				if (array_search($this->strict,$line)!==FALSE and empty($return)) $return = $key; 				
				next($sheet);
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
		if ($return->key===FALSE) return $return;
		$head = array_slice($sheet,0,$return->key+1);			
		if (!is_array($head)) return $return;
		$j=0;
		foreach ($head as $line) {
			if (!empty($line)) {
				$test = array_values(self::array_delete($line));			
				if (count($test)==1 and is_string($test[0])) {				
					$return->titles[]=$test[0]; //получаем заголовки листа				
				} else {
					$line = array_merge(array_slice($line,0,1),$this->array_delete(array_slice($line,1)));//решаем проблему размерности массива: больше, чем массив со значимой информацией									
					for ($i=0; $i<count($line); $i++) { //меняем столбцы на строки, приводя к матрице
						if (empty($line[$i]) and !empty($return->head[$i-1][$j])) {
							$return->head[$i][$j] = $return->head[$i-1][$j];
						} else {
							$return->head[$i][$j]= $line[$i];					
						}
					}
					$j++;
				}
			}
		}	
		if (!empty($return->head)) $return->head = array_map(function($var){return trim(implode(' : ',$this->array_delete($var)));},$return->head);
		return $return;		
	}
	
	/*
	 * Разбор заголовка по шаблону
	 * */
	
	public function canon($columns)
	{
		$return = array();
		$other = NULL;	
		foreach ($columns as $id=>$column) {
			foreach ($this->canons as $key=>$canon) {
				if (empty($canon)) $other=$key;
				foreach ($canon as $key2=>$selector) {
					foreach ((array) $selector as $mask) {
						if (strpos($column,$mask)!==FALSE) {
							$return[$key][$key2] = (int) $id;
							unset($columns[$id]);
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
