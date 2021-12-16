<?php
namespace Dataparser;
use \SimpleXLS;
use \ParseCsv\Csv;


class Factory {

	public static $errors = []; //ошибки класса
	
	//проверка файла на существование
	
	public static function check_file($file) {
		$file = (is_string($file)) ? new \SplFileInfo($file) : $file;
				
		if (!$file->isReadable()) {
			self::$errors[] = 'У файла нет прав на чтение';
			return FALSE;
		}
		return $file;
	}
	
	// очистка листа xls от пустых строк
	
	public static function clear_xls($rows) {
		return $rows;
	}
	
	//получение первого листа файла в любом формате, только ПЕРВОГО
	
	public static function get_sheets($file)
	{
		$sheets = []; //листы таблицы
		
		self::$errors=[]; //сбрасываем ошибки
		
		$file = self::check_file($file);
		
		if (empty($file)) return FALSE;		
		
		$file_path = $file->getRealPath();
		try {
		switch ($file->getExtension()) {
			case 'xls':
				if ($data = SimpleXLS::parseFile($file_path)) $sheets[] = self::clear_xls($data->rows()); else self::$errors[] = SimpleXLS::parseError();
			break;
			case 'xlsx':
				$sheets = Extractor::get($file_path);			
			break;
			case 'csv':
				$csv = new Csv();
				$csv->auto($file_path);
				$sheets[] = $csv->data;				
			break;
			case 'ods':
				$ods = new \ODS();			
				$ods->Parse($file_path);								
				foreach ($ods->Sheets as $sheet) {
					if (!empty($sheet['cells'])) {
						$cells = $sheet['cells']; 
						$rows = [];
						for ($i=0;$i<count($cells)-1;$i++) {
							if (!isset($cells[$i+1])) {
								self::$errors[] = 'Неправильная размерность матрицы';
								return FALSE;					
							}
							for ($j=0;$j<count($cells[$i+1])-1;$j++) {
								$rows[$i][$j] = (isset($cells[$i+1][$j+1]['val'])) ? $cells[$i+1][$j+1]['val']: NULL;
							}
						}					
						if (!empty($rows)) $sheets[$sheet["atr"]["table:name"]] = $rows;
					}				
				}
			break;
			default:
				self::$errors[] = 'Поддерживаются только файлы xls,xlsx,ods и csv';		
		}
		} catch (\Exception $e) {
			self::$errors[] = 'Неизвестная ошибка парсинга: '.$e->getMessage();
			return FALSE;
		}
		return $sheets;
	}
	
	//проверка на формат веерной матрицы. если все ок, то возвращает разобранный лист
	
	public static function check($rows) {
		$parser = new Parser();
		$sheet = $parser->parse_sheet($rows);					
		if (!is_array($sheet->body)) {
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
		$sheets = self::get_sheets($file);
		if (!empty(self::$errors)) return FALSE;
		$matrix = []; $i = 0;
		foreach ($sheets as $name=>$sheet) {		
			$data = self::check($sheet);					
			if (empty($data)) {
				self::$errors[] = 'Ошибка в листе '.$name;
				return FALSE;
			}				
			$list = self::matrix($data);
			if (!empty($list)) {						
				if (empty($list->title)) $list->title = $name;
				$matrix[$i++] = $list;			
			}
		}
		return $matrix;
	}
	
	
	


} ?>
