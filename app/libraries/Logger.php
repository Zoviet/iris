<?php
/**
 * Логгирование
 * @author   Zoviet
 */ 

class Logger {			
	
	public $echo=FALSE; //выводить ли на экран
	
	public $log; //текущий лог
	
	public $path=NULL; //путь сохранения логов
	
	public function __construct($type=NULL) {
		if (!empty($type)) {
			$this->echo = \Flight::get($type.'_log');
			$this->path = \Flight::get($type.'_log_path');
		}
	}
	
	public function __invoke($string) {
		$this->log($string);
	}
	
	private function save_log($data) {
		file_put_contents($this->path,date("Y-m-d H:i:s").': '.$data. PHP_EOL, FILE_APPEND);
	}
	
	//логгирование	
	public function log($string)
	{
		$this->log = $this->log.PHP_EOL.$string;
		if ($this->echo==TRUE) {
			echo $string.'<br/>';
		} else {
			if (!empty($this->path)) $this->save_log($string);
		}
	}


}
