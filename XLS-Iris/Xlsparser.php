<?php
namespace App\Libraries;

class Xlsparser {

	public function __construct()
	{	
		$this->version = 'Парсер xslt: версия 1.5: ';
		$this->errors = array(
            'ERR00' => 'Ошибка чтения данных из файла',
            'ERR01' => 'Ошибка разархивирования файла',
            'ERR03' => 'Шапка листа не найдена',
            'ERR04' => 'Не удалось получить индексы столбцов из шапки листа',
        );
        
        //устанавливаем критерий для поиска окончания заголовка листа
        
        $strict = FALSE; //в этом случае используем встроенные критерии класса
        
        //Устанавливаем критерии для поиска в заголовках колонок и их разбора
		$canons = array(
			'profession'=>array(
				'code' => 'Код профессии',
				'title' => array('Наменование профессии','Наименование профессии'),
				),
			'firm'=> array(			
				'title' => array('Наименование работодателя','Наименование организации'),
				'address' => array('Адрес (место нахождения) работодателя','Юридический адрес'),
				'inn' => 'ИНН',
				'okved' => 'ОКВЭД',				
			),
			'data'=> array(
				'term' => 'срок, на который',
				'salary' => 'размер оплаты труда',
				'number' => 'Численность иностранных работников',
				'country_id' => 'ОКСМ',
			),
			'json' => array(), //для колонок, неохваченных щаблоном
		);
		
		// устанавливаем тип разбора даты создания файлов
		$datetype = 'string'; //для разбора названия файла или 'disk' для парсинга по дате создания файла
		$offset=0; // отступ для строгого парсера заголовков по метке окончания заголовка
		
		$this->set_offset($offset);
		$this->set_datetype($datetype);
		$this->set_canons($canons); 
		$this->set_strict($strict);
			        
	}
	
	public function set_offset($offset)
	{
		$this->offset = $offset;
	}
	
	public function set_datetype($datetype)
	{
		$this->datetype=$datetype;
	}
	
	public function set_canons($canons) 
	{
		$this->canons = $canons;
	}
	
	public function set_strict($strict) 
	{
		$this->strict = $strict;
	}

	public function getxls($folder,$type=TRUE,$period='') {	//распаршиваем xslt из папки folder в ассоциированный массив с ключами названий листов (true) или их номерами (false) за период period формата y-m-d (можно указывать только y-m или только y) или все года (по умолчанию)			
		$xlsdata = array();	
			foreach (scandir(APPPATH.'data/'.$folder) as $file)	{						
				if (strpos($file,'.xlsx')!==FALSE) {					
					//$fileyear = date("Y-m-d",filectime(APPPATH.'data/'.$folder.'/'.$file));//получаем дату создания документа
					$fileyear = ($this->datetype=='string')? explode('_',$file)[0] : date("Y-m-d",filectime(APPPATH.'data/'.$folder.'/'.$file));
					$period = str_replace('_',' ',$period);					
					if (empty($period) or strpos($file, $period)!==FALSE) {	
						self::redir(APPPATH.'data/'.$folder.'/temp'); //удаляем временную директорию	
						mkdir(APPPATH.'data/'.$folder.'/temp'); //создаем временную директорию
						$xlsd = self::xslextract(APPPATH.'data/'.$folder.'/'.$file,APPPATH.'data/'.$folder.'/temp',$type);															
						 self::redir(APPPATH.'data/'.$folder.'/temp'); //удаляем временную директорию
						if ($xlsd!==FALSE) {	
							if (!isset($xlsdata[$fileyear])) { //на случай нескольких файлов одного возраста
								$xlsdata[$fileyear]=$xlsd; 	
							} else {
								$xlsdata[$fileyear]=array_merge($xlsdata[$fileyear],$xlsd); 
							}	
						} else {
							throw new \Exception($this->version.': '.$this->errors['ERR00'].': '.$file);						
						}
					}
				}			
			}      		
		return ($xlsdata);
	}	

	static function parser($sheet,$strings) { // парсинг листа таблицы в массив, использована часть кода https://gist.github.com/searbe/3284011
		$xlrows = $sheet->sheetData->row;	
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

	static function xslextract($inputFile='', $dir ='',$names=FALSE) { //выбор данных из таблиц по всем листам, если names установлено в FALSE, возвращает массив листов по номерам, если в true - то по названиям листов
		$zip = new \ZipArchive;			
		$zip->open($inputFile);	
		$arch=$zip->extractTo($dir);
		$zip->close();	
		$arr=array();
		$i=1;
		if ($arch!==FALSE) {			
			$sheetnames  = simplexml_load_file($dir . '/xl/workbook.xml');
			foreach ($sheetnames->sheets->sheet as $sheet) { //ни к чему нам тут xpath, приведем к массиву лучше							
				//$sname[(int)$sheet['sheetId']] = trim((string)$sheet['name']);
				$sname[$i++] = trim((string)$sheet['name']);
			}										
			$counter = self::countfiles($dir . '/xl/worksheets/');
			$strings = simplexml_load_file($dir . '/xl/sharedStrings.xml');
			for ($x=1; $x<=$counter; $x++) {
				$sheet = simplexml_load_file($dir . '/xl/worksheets/sheet'.$x.'.xml');							
				if ($names==FALSE) {
					$arr[$x]=self::parser($sheet,$strings); 
				} else { 
					$arr[$sname[$x]]=self::parser($sheet,$strings); 				
				}
			}					
			return $arr;
			
		} else {
			throw new \Exception($this->version.': '.$this->errors['ERR01']);	
			return FALSE;
		}
	}
	
	/*
	 * Получение количества строк заголовков листов с жестким и нежестким критерием выборки. 
	 * Нежесткий критерий (FALSE): выборка до первой пустой строки или до строки с номерами столбцов.
	 * Жесткий критерий (string) - до первой строки, в которой встречается переданная строка
	 * @return номер строки, с которой начинаются основные данные
	 * 
	*/
	public function toplines($sheet) 
	{   
		$return = FALSE;
		$set = FALSE;
		while ($return==FALSE and key($sheet)!==NULL) { //не foreach для того, чтобы не обходить весь лист до конца, а лишь до первого вхождения
			$line = current($sheet);
			$key = key($sheet);			
			if($this->strict==FALSE and empty($return)) {
				if (count($line)>0) {
					$canon = array_keys(array_fill(1, count($line)-1, 0));				
					if (empty(array_diff($canon,$line))) {$return = $key-1;}
					$nextline = next($sheet);
					if (count(self::array_delete($line))==1 and $key>0 and count(self::array_delete($nextline))>=count($line)) $return = $key;
				} else {
					next($sheet);
				}			
			} else {				
				if (array_search($this->strict,$line)!==FALSE and empty($return)) $return = $key-$this->offset; 				
				next($sheet);
			}			
		}
		return $return;
	}
	
	/*
	 * Выделение шапки листа
	 * 
	 */
	public function header($head) 
	{
		$return = new \STDClass;
		if (is_array($head)) {			
			$j=0;
			foreach ($head as $line) {
				if (!empty($line)) {
					if (is_string($line[0]) and count(self::array_delete($line))==1) {				
						$return->headers[]=$line[0]; //получаем заголовки листа				
					} else {
						$line = array_merge(array_slice($line,0,1),$this->array_delete(array_slice($line,1)));//решаем проблему размерности массива: больше, чем массив со значимой информацией									
						for ($i=0; $i<count($line); $i++) { //меняем столбцы на строки, приводя к матрице
							if (empty($line[$i]) and !empty($return->head[$i-1][$j])) {
								$return->head[$i][$j] = $return->head[$i-1][$j];
							} else {
								$return->head[$i][$j]= $line[$i];					
							}
						}
						$j++;
					}
				}
			}	
			if (empty($return->head)) {
				throw new \Exception($this->version.': '.$this->errors['ERR04']);	
			} else {				
				$return->head = array_map(function($var){return trim(implode(' : ',$this->array_delete($var)));},$return->head);
			}
		} else {
			throw new \Exception($this->version.': '.$this->errors['ERR03']);	
		}
		return $return;
	}
	
	/*
	 * Разбор заголовка по шаблону, устанавливаемому через set_canons
	 * */
	
	public function canon($columns)
	{
		$return = array();
		$other = NULL;	
		foreach ($columns as $id=>$column) {
			foreach ($this->canons as $key=>$canon) {
				if (empty($canon)) $other=$key;
				foreach ($canon as $key2=>$selector) {
					foreach ((array) $selector as $mask) {
						if (strpos($column,$mask)!==FALSE) {
							$return[$key][$key2] = (int) $id;
							unset($columns[$id]);
						}	
					}
				}
			}			
		}	
		if (!empty($columns) and !empty($other)) {  //обрабатываем неохваченные шаблоном столбцы
			foreach ($columns as $key=>$value) {
				$return[$other][$value] = $key;
			}
		}
		return $return;
	}	
	
	/*
	 * Разбор листа таблицы
	 */	
	public function prepary($sheet)
	{
		$return = new \STDClass;				
		try { //пробуем получить заголовки
			$top = $this->toplines($sheet);		
			$return->header = $this->header(array_slice($sheet,0,$top));
			$return->data = $this->clear(array_slice($sheet,$top+1,count($sheet)));
		} catch (\Exception $e) {
			error_log($e->getMessage(), 0);
		}		
		return $return;
	}
	
	/*
	 * Очистка листа данных от пустых строк и прочего мусора
	 * */
	private function clear($data) 
	{	$return = array();
		foreach ($data as $key=>$line) {
			$canon = (count($line)>0) ? array_keys(array_fill(1, count($line)-1, 0)) : array();						
			if (count(self::array_delete($line))>1 and !empty(array_diff($canon,$line))) {
				$return[]=$line;
			} 
		}
		return $return;
	}
	
	//Сервисные методы
	
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
	
	static function countfiles($dir) { //подсчет количества файлов в папке
		$files = array_diff(scandir($dir), array('.','..','_rels')); $x = count($files);
		return $x; 
	}
	
	static function array_delete(array $array, array $symbols = array(0,'',' ')) //delete null values
	{
		return array_diff($array, $symbols);
	}

} 
