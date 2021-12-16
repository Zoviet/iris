<?php
namespace Dataparser;

class Xlsx {		
	
	public static $version = 'Парсер xslt: версия 1.5: ';
	
	public static $errors = [];	
	
	public static $header = [];

	public function __construct($file=NULL,$offset=NULL,$canon=[])
	{		        
		if (!empty($file)) self::parse($file,$offset,$canon);		
	}
	
	public static function parse($file,$offset=NULL,$canon=[]) 
	{
		$dir = self::dir($file);
		if (self::exists($file) and $dir!==FALSE) {
			$data = Extractor::get($file,$dir.'/'.md5_file($file));
			$parser = new Parser();
			foreach ($data as $name=>$sheet) {
				$data[$name] = $parser->parse_sheet($sheet,$offset,$canon);
			}			
		}
	}
	
	//проверка существования файла
	public static function exists($filename) 
	{
		$path = realpath($filename); 
		if($path===FALSE) {
			self::$errors[] = 'Файл не найден';
			return FALSE;
		}
		if(strpos($path,'.xlsx')===FALSE) {			
			self::$errors[] = 'Файл не формата xlsx';
			return FALSE;
		}			
		return TRUE;
	}
	
	//получение директории файла
	public static function dir($filename) 
	{
		$path = realpath($filename); 
		$paths = explode('/',$path);		
		$dir = str_replace(array_pop($paths),'',$path);
		if(strrpos(self::perms($dir),'7')<1) {
			self::$errors[] = 'Нет прав на запись в директорию файла';
			return FALSE;
		}
		return $dir;
	}
	
	//проверка прав на файл или директорию
	public static function perms($filename) 
	{
		return substr(decoct(fileperms($filename)),-3);
	}

} ?>
