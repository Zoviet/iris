<?php
namespace Dataparser;

trait Matrix {

	//проверка на формат веерной матрицы. если все ок, то возвращает разобранный лист
	
	public static function check($rows) {
		$parser = new Parser();		
		$sheet = $parser->parse_sheet($rows);				
		if (!isset($sheet->body) or !is_array($sheet->body)) {
			self::$errors[] = 'Матрица не должна быть пустой'; return FALSE;
		} 
		$max = 0;
		foreach ($sheet->body as $row) {
			if (count($row)>$max) $max = count($row);
		}
		if ($max!==count($sheet->body)) {
			self::$errors[] = 'Неправильная размерность матрицы';
			return FALSE;
		}
		return $sheet;	
	}
	
	//разбор уже подготовленных полей для матрицы
	
	public static function matrix($sheet) {
		$matrix = new \STDClass();		
		$count = count($sheet->titles);	
		if ($count<=3) {
			$sheet->titles = array_merge($sheet->titles, array_fill($count, 3-$count, NULL));	
		} else {		
			return NULL;
		}
		list($matrix->title,$matrix->autor,$matrix->description) = $sheet->titles;
		$matrix->data = $sheet->body;
		return $matrix;
	}
	
	//установка новой веерной матрицы
		
	public static function install($file) {			
		self::$errors = [];	
		$sheets = self::get_sheets($file,false);
		if (!empty(self::$errors)) return FALSE;
		$matrix = []; $i = 0;
		foreach ($sheets as $name=>$sheet) {		
			$data = self::check($sheet);					
			if (empty($data)) {
				self::$errors[] = 'Ошибка в листе '.$name;			
			} else {
			$list = self::matrix($data);		
			if (!empty($list)) {						
				if (empty($list->title)) $list->title = $name;
				$matrix[$i++] = $list;			
			}
			}
		}
		return $matrix;
	}
	

} ?>
