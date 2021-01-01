<?php
/**
 * Iris: Библиотека для работы с веерными матрицами
 *
 * @copyright   Copyright (c) 2021, Zoviet <alexandr@asustem.ru>
 * @license     GNU GENERAL PUBLIC LICENSE Version 3
 */

/**
 * Matrix: класс представления веерной матрицы.  * 
 * Функционал: рекурсивное создание веерной матрицы любой глубины. Матрицы разных уровней являются экземплярами этого же класса с теми же методами и свойствами.
 * Таким образом, каждыя веерная матрица может быть рассмотрена как отдельно, так и как раскрытие родительской веерной матрицы той или иной степени глубины.
 */
 
/**
 * Свойства
 * @var  public int ID Идентификатор веерной матрицы.
 * @var  public int version Версия редакции веерной матрицы.
 * @var  public string name Название веерной матрицы.
 * @var  public string desc Описание веерной матрицы.
 * @var  public array comments Комментарии к веерной матрице.
 * @var  public string scheme Схема представления феноменов.
 * @var  public array of matrix matrixs Массив всех матриц феноменов веерной матрицы.
 * 
 * Конструктор
 * @matrix $matrix = NULL Принимает 
 * 
 * Статистические методы
 * @method  static mixed depth($depth=NULL) Глубина веерной матрицы. Без аргументов выдает количество уровней раскрытия (INT), с аргументами - матрицы феноменов запрошенного уровня.
 * @method  static array levels($matrix_id=NULL) Уровни организации (структуры) для запрашиваемой матрицы феноменов. При отсутствии аргументов - уровни организации матрицы нулевого уровня. 
 *
 * Сохранение и загрузка матриц.
 * @method  public string export() Экспорт веерной матрицы в iris json. Возвращает строку json формата. 
 * @method  public bool import() Импорт веерной матрицы из iris json. Возвращает true в случае успеха, выбрасывает исключения в случае неудачи. 
 *
 * Конвейер создания матриц феноменов.
 * @method  public int start($depth=0) Создание матрицы феноменов: глубина и размерность (не обязательно).
 * @method  public void add_level($name) Registers a class to a framework method.
 *
 * Filtering.
 * @method  static void before($name, $callback) Adds a filter before a framework method.
 * @method  static void after($name, $callback) Adds a filter after a framework method.
 *
 * Variables.
 * @method  static void set($key, $value) Sets a variable.
 * @method  static mixed get($key) Gets a variable.
 * @method  static bool has($key) Checks if a variable is set.
 * @method  static void clear($key = null) Clears a variable.
 *
 * Views.
 * @method  static void render($file, array $data = null, $key = null) Renders a template file.
 * @method  static \flight\template\View view() Returns View instance.
 *
 * Request & Response.
 * @method  static \flight\net\Request request() Returns Request instance.
 * @method  static \flight\net\Response response() Returns Response instance.
 * @method  static void redirect($url, $code = 303) Redirects to another URL.
 * @method  static void json($data, $code = 200, $encode = true, $charset = "utf8", $encodeOption = 0, $encodeDepth = 512) Sends a JSON response.
 * @method  static void jsonp($data, $param = 'jsonp', $code = 200, $encode = true, $charset = "utf8", $encodeOption = 0, $encodeDepth = 512) Sends a JSONP response.
 * @method  static void error($exception) Sends an HTTP 500 response.
 * @method  static void notFound() Sends an HTTP 404 response.
 *
 * HTTP Caching.
 * @method  static void etag($id, $type = 'strong') Performs ETag HTTP caching.
 * @method  static void lastModified($time) Performs last modified HTTP caching.
 */

namespace iris;

class Matrix {
	 /**
     * @var mixed[]
     */
    protected static $data = array();    
    protected static $config_file = 'config.json';  
	
	public static function init() //значения по умолчанию
	{
		self::set('base_url','http://'.$_SERVER['SERVER_NAME']);	
		self::set('title','Стенд для испытаний амортизаторов');	
		self::set('bedding_timeout',10); //время отсечения для стадии приработки	
		self::set('save_log',FALSE); //сохранять логи работы
		self::set('log_file','logs/testing.log'); //файл логов работы
		self::set('pagination',50);//количество проектов на страницу архива
		self::set('ammos_dir',__DIR__.'/ammos');	
		self::set('ammos_filename', 'ammos.json');		
		self::set('results_dir', __DIR__.'/results'); 		
		self::set('dir_permission',0777);
		self::set('config_filename','config.xml');
		self::set('ammos_collection_errors',[
			'Для записи амортизатора в базу необходимо указать тип амортизатора',
			'Необходимо указать усилия сжатия и отбоя',
			'Не найден запрошенный тип амортизатора'
		]);

		self::set('connect_errors',[
			'Передано не булево значение',			
		]);
		self::set('connect_messages',[
			'Тест соединения',
			'Начальный адрес',
			'Смещение',
			'Полученное значение',
			'Преобразованное значение',
			'Передаваемое значение'
		]);
		self::set('test_types',[
			'Определение плавности перемещения подвижных деталей',
			'Снятие рабочих диаграмм',
			'Построение характеристик амортизатора',
			'Снятие температурных характеристик',
			'Ресурсные испытания'			
		]);
		self::set('tests_errors',[
			'Не указан тип испытаний',
			'Ошибка в данных референтного амортизатора',
			'Ошибка при попытке подключения к устройству',
			'Не получен положительный ответ об окочании действия за (сек)',
			'Не удалось отправить сигнал начала испытания',
			'Данные не были получены, хотя сигнал об окончании испытания был',
			'Температура амортизатора перед началом испытаний вне норм',
			'Ошибка в данных снятия характеристик амортизатора'	
			
		]);
		self::set('tests_messages',[
			'Амортизатор подведен к верхней точке и прокачан',
			'Испытание закончено успешно',	
			'Испытание завершено принудительно. Логи сохранены, протокол не сохранен. Можно запустить заново',
			'Испытание стартовало',
			'Сигнал на начало испытания подан успешно на адрес',
			'Подача сигнала на калибровку и прокачку',
			'Подача сигнала на начало испытания',
			'Температура амортизатора в процессе измерений была в норме',
			'Температура амортизатора при испытаниях была превышена на ',
			'Температура амортизатора при испытаниях была ниже нормы на ',
			'Температура амортизатора составляет',
			'Результаты снятия зависимости сопротивления от скорости'		
		]);
		
	}
	
	public static function default_connect() //загрузка параметров настройки соединения по умолчанию
	{
		self::set('connect',array(
			'IP'=>'127.0.0.1', //IP-адрес PLC 
			'Port'=>502, //Порт
			'UnitID'=>1, //ID устройства
			'Endianess'=>'BIG_ENDIAN_LOW_WORD_FIRST', //Порядок байт
			'Address1'=>[1,16,'WriteSingleCoilRequest','Адрес передачи сигнала на начало испытаний','включается мотор-редуктор и подводит амортизатор к ВМТ'], //coils запись
			'Address2'=>[2,16,'ReadInputDiscretesRequest','Адрес приема сигнала об окончании прокачки','Вновь включается привод, процесс испытания синхронизируется по стробу ВМТ с датчика угла поворота КШМ и в течение четырех циклов происходит прокачка амортизатора'], //Discrete inputs чтение
			'Address3'=>[3,16,'WriteSingleCoilRequest','Адрес передачи сигнала о начале испытаний амортизатора с отрывающимися клапанами',''], //coils запись
			'Address4'=>[4,16,'ReadInputDiscretesRequest','Адрес приема сигнала об окончании испытания (любого из)',''], //Discrete inputs чтение
			'Address5'=>[5,6,'ReadInputRegistersRequest','Адрес приема данных о перемещении',''], //Input registers чтение
			'Address6'=>[6,6,'ReadInputRegistersRequest','Адрес приема данных об усилии',''], //Input registers чтение
			'Address7'=>[7,6,'ReadInputRegistersRequest','Адрес приема данных о температуре',''], //Input registers чтение
			'Address8'=>[8,6,'ReadInputRegistersRequest','Адрес приема данных о частоте',''], //Input registers чтение
			'Address9'=>[9,16,'WriteSingleCoilRequest','Адрес передачи аварийного сигнала (что-то пошло не так)',''], //coils запись
			'Address10'=>[10,1,'WriteSingleCoilRequest','Адрес передачи сигнала на медленное перемещение',''], //coils запись
			'Address11'=>[11,1,'WriteSingleCoilRequest','Адрес передачи сигнала на снятие рабочих диаграмм',''], //coils запись
			'Address12'=>[12,1,'WriteSingleCoilRequest','Адрес передачи сигнала на снятие характеристик амортизатора',''], //coils запись
		));			
	} 
	
	public static function connect_types() //допустимые типы соединений
	{
		self::set('connect_types',[
		'ReadCoilsRequest',
		'ReadInputDiscretesRequest',
		'ReadHoldingRegistersRequest',
		'ReadInputRegistersRequest',
		'WriteSingleCoilRequest'
		]);		
	}
	
	public static function connect_addrs() //возвращает адреса соединений
	{
		$addrs = array();
		$connect = self::get('connect');	
		foreach ($connect as $key=>$data) { //выделяем из конфига адреса соединений
			if (is_array($data)) {
				$addrs[$key]= $data;
			}		
		}
		return $addrs;
	}
	
    /**
     * Добавляет значение в конфиг
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
		self::pull();
        self::$data[$key] = $value;
        self::push();
    }

    /**
     * Возвращает значение из конфига по ключу
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
		self::pull();
        return isset(self::$data[$key]) ? self::$data[$key] : null;
    }    
    
    protected static function push()
    {
		return file_put_contents(__DIR__.'/'.self::$config_file,json_encode(self::$data));
	}
	
	protected static function pull()
    {
		$data = file_get_contents(__DIR__.'/'.self::$config_file);
		if ($data) self::$data = (array) json_decode($data);		
	}
    
    public static function list()
    {
		self::pull();
		return self::$data;
	}    
    /**
     * Удаляет значение из конфига по ключу
     *
     * @param string $key
     * @return void
     */
    final public static function remove($key)
    {
		self::pull();
        if (array_key_exists($key, self::$data)) {
            unset(self::$data[$key]);
        }
        self::push();
    }
	
}
