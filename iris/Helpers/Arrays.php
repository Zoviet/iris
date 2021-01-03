<?php
/**
 * @author   Zoviet
 */ 
namespace iris\Helpers;

class Arrays {			
	
	public static function multi_search(array $array, $item) //поиск в двухмерном массиве
	{
		$return = NULL;
		foreach ($array as $key=>$data) {
			$index = array_search($item,$data);
			$return = ($index) ? $array[$key][$index];
		}
		return $return;
	}

	public static function pop(array $array) //последний элемент массива
	{
		return array_pop($array);
	}

	public static function key_first(array $arr) //первый ключ массива
	{
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }

	public static function test($array) //проверка размерности массива. 
	{
		$test = FALSE;
		$value = array_pop($array); 
		if (is_array($value)) {
				$test = TRUE;
		}
		return $test;
	}

	public static function random($lenght,$min,$max) //генерация массива уникальных случайных чисел в диапазоне от мин до макс размером lenght
	{	
		$random=array();
		for ($i=0; $i<$lenght; $i++) {			
			$rand = random_int($min,$max);
			$random[$i] = (!in_array($rand,$random)) ? $rand : $i=$i-1;			
		}	
		return $random;
	}
	
	public static function dump(array $array) //вывод двухмерного массива в формате "ключ:значение" 
	{
		$dump = '';
		foreach ($array as $key=>$value)
		{
			$dump = $dump.$key.' : '.$value.' <br/>';
		}
		return $dump;
	}
	
	public static function d_dump(array $array) //вывод массива массивов в формате "ключ:значение|значение" 
	{
		$dump = '';
		foreach ($array as $key=>$value)
		{
			$dump = $dump.$key.' : '.implode('| ',$value).' <br/>';
		}
		return $dump;
	}
	
	
	public static function delete(array $array, array $symbols = array(0)) //удаление пустых элементов массива
	{
		return array_diff($array, $symbols);
	}

	public static function delete_sub(array $array, array $symbols = array(0),$reverse=FALSE) //удаление значений с набором символов
	{
		$out = array();
		foreach ($array as $key=>$value) {
			foreach ($symbols as $symbl) {																				
				if (strpos((string) $value, $symbl)>0) {					
					$out[$key] = $value;
				}
			}				
		}
		$out = ($reverse==FALSE) ? array_diff($array, $out) : $out;
		return $out;
	}
	
	public static function json(array $keys,array $values) //создание массива массива значений (например, для apexcharts)
	{
		$array = array();
		foreach ($keys as $i=>$key)
		{
			$value = (isset($values[$i])) ? $values[$i] : NULL;
			$array[] = array($key,$value);
		}
		return $array;
	}
	
	public static function nulled(array $array) //обнуление значений массива с сохранением ключей
	{
		return array_map(function(){return 0;},$array);
	}

	public static function positive(array $array) //выделение из массива части с положительными значениями
	{
		return array_filter($array, function($var) {return (bool)(abs($var)==$var);});
	}

	public static function negative(array $array) //выделение из массива части с отрицательными значениями
	{
		return array_filter($array, function($var) {return (bool)(abs($var)!==$var);});
	}

}
