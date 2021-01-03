<?php
/**
 * @author   Zoviet
 */ 
namespace iris\Helpers;

class Times {			
	
	public static function timeID() {
		$stamp = microtime(true);
		return (int) round($stamp/1000000);		
	}

}
