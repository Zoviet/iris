<?php

namespace Directory;


class Iterator {	
	
	//рекурсивное получение всех файлов с объектами типа splfileinfo из директории, которые имеют расширение из массива ext
	
	public static function RecursiveIterator($path,$ext=[]) {
		$files = [];
		$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object){			
			if (in_array($object->getExtension(),$ext)) {
				$files[$name] = $object;		
			}
		}
		return $files;
	}
}
