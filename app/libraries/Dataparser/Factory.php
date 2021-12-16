<?php
namespace Dataparser;
use \SimpleXLS;
use \ParseCsv\Csv;

class Factory {
	use Matrix;

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
	
	public static function clear_xls($sheets) {
		foreach ($sheets as $sheet_key=>$rows) {
			foreach ($rows as $key=>$row) {
				if (empty($row)) unset ($rows[$key]);
			}
			if (empty($rows)) unset ($sheets[$sheet_key]);
		}
		return $sheets;
	}
	
	//получение листа файла в любом формате
	//если strict = false, то для xls файлов пытаемся читать как xml, т.е. как excel2003
	public function get_sheets($file,$strict=TRUE)
	{
		$sheets = []; //листы таблицы
		
		self::$errors=[]; //сбрасываем ошибки
		
		$file = self::check_file($file);
		
		if (empty($file)) return FALSE;		
		
		$file_path = $file->getRealPath();
		try {
		switch ($file->getExtension()) {
			case 'xls':	
				$excel = new \MSXLS($file_path);					
				if($excel->error) {
					self::$errors[] =  'Файл '.$file_path.': ошибка чтения xls '.$excel->err_msg;
					if ($strict!==TRUE) {$sheets = Xml::get($file_path);
						try {						
							if ($sheets===FALSE) throw new \Exception('Ошибка чтения XML');						
						} catch (\Exception $e) {
							self::$errors[] =  'Файл '.$file_path.': ошибка чтения xls '.$excel->err_msg.' ,пробуем прочитать как валидный xml ';
							self::$errors[] = 'Чтения файла '.$file_path.' как Excel2003 XML, ошибка: '.$e->getMessage();
						}
					}
				} else {				
					if ($data = SimpleXLS::parseFile($file_path)) 
						$sheets = self::clear_xls($data->sheets); 
						else {
							self::$errors[] = 'Ошибка чтения XLS как BLOOB даты листа '.SimpleXLS::parseError();		
							break;
						}
					foreach ($excel->get_valid_sheets() as $key=>$sheet) {
						if (!empty($sheet['name'])) {
							$sheets[$sheet['name']] = $sheets[$key]['cells'];
							unset($sheets[$key]);
						}
					}				
				}				
			break;
			case 'xlsx':
				$sheets = Extractor::get($file_path);			
			break;
			case 'csv':
				$csv = new Csv();
				$csv->auto($file_path);
				$sheets[] = $csv->data;				
			break;
			case 'xml':
				$sheets = Xml::get($file_path);
			break;
			case 'ods':
				$ods = new \ODS();			
				$ods->Parse($file_path);								
				foreach ($ods->Sheets as $sheet) {
					if (!empty($sheet['cells'])) {
						$cells = $sheet['cells']; 
						$rows = [];
						for ($i=0;$i<count($cells)-1;$i++) {						
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

} ?>
