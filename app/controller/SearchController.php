<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 2:37 PM
 */

namespace Controller;
use \Directory\Iterator;
use \Dataparser\Factory;
use \Models\Matrix;
use \Logger;

class SearchController
{
	
    public function search()
    {	
		$Matrix = new Matrix();	
		$results= NULL;
		$count = 10;
		if (isset(\Flight::request()->data['submit_search'])) {
			$string = \Flight::request()->data['string'];
			$query = mb_strtolower($string, 'UTF-8');
			$arr = explode(" ", $query); //разбивает строку на массив по разделителю
			/*
				* Для каждого элемента массива (или только для одного) добавляет в конце звездочку,
				* что позволяет включить в поиск слова с любым окончанием.
				* Длинные фразы, функция mb_substr() обрезает на 1-3 символа.
			*/
			$query = [];
			foreach ($arr as $word)
				{
					$len = mb_strlen($word, 'UTF-8');
					switch (true)
					{
						case ($len <= 3):
							{
								$query[] = $word . "*";
								break;
							}
						case ($len > 3 && $len <= 6):
							{
								$query[] = mb_substr($word, 0, -1, 'UTF-8') . "*";
								break;
							}
						case ($len > 6 && $len <= 9):
							{
								$query[] = mb_substr($word, 0, -2, 'UTF-8') . "*";
								break;
							}
						case ($len > 9):
							{
								$query[] = mb_substr($word, 0, -3, 'UTF-8') . "*";
								break;
							}
						default:
							{
								break;
							}
					}
				}
			$query = array_unique($query, SORT_STRING);
			$qQeury = implode(" ", $query); //объединяет массив в строку		
			$results = $Matrix->search($qQeury,$count);
		} 	
        \Flight::render('search.php', ['matrixs' => $results]);     		
    }
}
