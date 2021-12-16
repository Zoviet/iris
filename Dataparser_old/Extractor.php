<?php
namespace Dataparser;

class Extractor {
	
	//выбор данных из таблиц по всем листам, если names установлено в FALSE, возвращает массив листов по номерам, если в true - то по названиям листов
	public static function get($inputFile='', $dir ='') 
	{ 
		if (empty($dir)) {
			$dir = sys_get_temp_dir().'/'.md5($inputFile);        	
		}
		$zip = new \ZipArchive;			
		$zip->open($inputFile);	
		$arch=$zip->extractTo($dir);
		$zip->close();	
		$arr=array();
		$i=1;
		if ($arch===FALSE) return FALSE;		
		$sheetnames  = simplexml_load_file($dir . '/xl/workbook.xml');
		foreach ($sheetnames->sheets->sheet as $sheet) { //ни к чему нам тут xpath, приведем к массиву лучше							
				//$sname[(int)$sheet['sheetId']] = trim((string)$sheet['name']);
				$sname[$i++] = trim((string)$sheet['name']);
		}										
		$counter = self::countfiles($dir . '/xl/worksheets/');
		$strings = simplexml_load_file($dir . '/xl/sharedStrings.xml');
		for ($x=1; $x<=$counter; $x++) {
			$sheet = simplexml_load_file($dir . '/xl/worksheets/sheet'.$x.'.xml');		
				$parser_data = self::parser($sheet,$strings);			
				if(!empty($parser_data)) $arr[$sname[$x]] = $parser_data; 								
			}		
		self::redir($dir);			
		return $arr;
	}    	
	
	// парсинг листа таблицы в массив, использована часть кода https://gist.github.com/searbe/3284011
	static function parser($sheet,$strings) 
	{ 
		$xlrows = $sheet->sheetData->row;	
		$values = [];
		foreach ($xlrows as $xlrow) {
			$arr = array();
			foreach ($xlrow->c as $cell) {
				$v = (string) $cell->v;
				if (isset($cell['t']) && $cell['t'] == 's') {
					$s  = array();
					$si = $strings->si[(int) $v];          
					$si->registerXPathNamespace('n', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');           
					foreach($si->xpath('.//n:t') as $t) {
						$s[] = (string) $t;
					}
					$v = implode($s);
				}        
				$arr[] = $v;
			}
			$values[] = $arr;    		
		}
		return $values;	
	}	
	
	static function redir($path) //рекурсивное удаление каталога
	{
		if (is_file($path)) return unlink($path);
		if (is_dir($path)) {
			foreach(scandir($path) as $p) if (($p!='.') && ($p!='..'))
				self::redir($path.DIRECTORY_SEPARATOR.$p);
			return rmdir($path); 
		}		
		return false;
	}	
	
	//подсчет количества файлов в папке
	static function countfiles($dir) 
	{ 
		$files = array_diff(scandir($dir), array('.','..','_rels')); $x = count($files);
		return $x; 
	}

} ?>
