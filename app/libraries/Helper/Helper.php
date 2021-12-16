<?php
/**
 * @author   Zoviet
 */ 

class Helper {			
	
	public static function sign( $number ) {
		return ( $number > 0 ) ? 1 : ( ( $number < 0 ) ? -1 : 0 );
	} 

	public static function pop(array $array) //последний элемент массива
	{
		return array_pop($array);
	}

	public static function array_key_first(array $arr) //первый ключ массива
	{
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }

	public static function array_test($array) //проверка размерности массива. 
	{
		$test = FALSE;
		$value = array_pop($array); 
		if (is_array($value)) {
				$test = TRUE;
		}
		return $test;
	}

	public static function array_random($lenght,$min,$max) //генерация массива уникальных случайных чисел в диапазоне от мин до макс размером lenght
	{	
		$random=array();
		for ($i=0; $i<$lenght; $i++) {			
			$rand = random_int($min,$max);
			$random[$i] = $rand; 		
		}	
		return $random;
	}
	
	public static function array_dump(array $array) //вывод двухмерного массива в формате "ключ:значение" 
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
	
	
	public static function array_delete(array $array, array $symbols = array(0)) //удаление пустых элементов массива
	{
		return array_diff($array, $symbols);
	}

	public static function array_delete_sub(array $array, array $symbols = array(0),$reverse=FALSE) //удаление значений с набором символов
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
	
	public static function array_json(array $keys,array $values) //создание массива массива значений (например, для apexcharts)
	{
		$array = array();
		foreach ($keys as $i=>$key)
		{
			$value = (isset($values[$i])) ? $values[$i] : NULL;
			$array[] = array($key,$value);
		}
		return $array;
	}
	
	public static function array_nulled(array $array) //обнуление значений массива с сохранением ключей
	{
		return array_map(function(){return 0;},$array);
	}

	public static function array_positive(array $array) //выделение из массива части с положительными значениями
	{
		return array_filter($array, function($var) {return (bool)(abs($var)==$var);});
	}

	public static function array_negative(array $array) //выделение из массива части с отрицательными значениями
	{
		return array_filter($array, function($var) {return (bool)(abs($var)!==$var);});
	}
	
	public static function approximate($array,$key) //линейная аппроксимация значений графика 
	{
		$value = NULL;// возвращаемое значение
		reset ($array);
		while (key($array)!==NULL) {		
			$current = current($array);		
			$current_key = key($array);
			if ($current_key == $key) $value = $current;		
			$next = next($array);
			$next_key = key($array);
			if (($current_key < $key) and ($next_key > $key)) {					
				$grow = ($key-$current_key)/$current_key; //прирост значения относительного начального ключа
				$value = $current+$current*$grow;
			} 			
		}
		return $value;
	}
	
	//переворачивание ключей
	public static function array_series($array) {
		$dataset = [];
		foreach ($array as $i=>$value) {
			foreach ($value as $key=>$data) {
				$dataset[$key][$i] = $data;
			}
		}
		return $dataset;
	}
	
	//удаление нецифровых значений двухмерного массива
	public static function array_clear($array) {
		foreach ($array as $key=>$value) {
			foreach ($value as $key2=>$value2) {
				if (!is_numeric($value2)) {
					unset($array[$key]);
				}
			}
		}	
		return $array;
	}

}
