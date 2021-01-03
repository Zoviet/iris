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
 * Таким образом, каждая веерная матрица может быть рассмотрена как отдельно, так и как раскрытие родительской веерной матрицы той или иной степени глубины.
 */

namespace iris;

use iris\Config\Defaults;
use iris\Config\Errors;

class Matrix {
	
	/**
	* Свойства
	* @var  public int id Идентификатор веерной матрицы.
	* @var  public int parent Идентификатор родительской веерной матрицы.
	* @var  public int depth Глубина веерной матрицы.
	* @var  public string name Название веерной матрицы.
	* @var  public string desc Описание веерной матрицы.
	* @var  public string scheme Схема представления феноменов.
	* @var  public reestr Реестр матриц веерной матрицы - объект типа Reestr этого же пространства имен iris.
	* @var  public array data Феномены и уровни матрицы
	* @var  protected int current Идентификатор текущей (в работе конвейера) матрицы из массива матриц.
	* @var  protected array errors Ошибки класса
	* 
	*/
	
	public $id;
	public $parent;
	public $depth;
	public $name;
	public $desc;
	public $scheme;
	public $reestr;
	public $data;
	protected $current;
	
	/**	
	 * 	Конструктор матрицы	 
	 * @param array данные для инициалиазции матрицы: см blank_data           
     * @return void
     * По умолчанию использует данные по умолчанию
	*/
	
	public function __construct($init_data=NULL) {
		if (empty($init_data)) 	$init_data = $this->blank_data();		
		try {		
			$this->init($init_data);
		} catch (\Exception $e) {
			throw new \Exception(Errors::matrix['M03'].' :'.$e->getMessage());
			error_log($e->getMessage(), 0);					
		}
	}
	
	/**	
	 * Инициализация матрицы начальными данными по умолчанию	
	*/
	
	protected function blank_data($data=NULL) {
		if (empty($data)) {
			$data = new \stdClass();
			$data->id = Helpers\Times::timeID();
			$data->name = Defaults::matrix['name'];
			$data->desc = Defaults::matrix['desc'];	
			$data->scheme = Defaults::matrix['scheme'];		
			$data->reestr = NULL;
			$data->data = array();			
		} 
		return $data;
	}
		
	/**	
	 * Инициализация матрицы 	
	*/
	
	public function init($init) {
		if (!isset($init->id)) {
			throw new \Exception(Errors::matrix['M01']);
		} else {
			$this->id = $init->id;		
			$this->parent = (!empty($init->parent)) ? $init->parent : NULL;
			$this->depth = (!empty($this->parent)) ? $init->depth : NULL;
			$this->name = $init->name;
			$this->desc = $init->desc;
			$this->scheme = (!empty($init->scheme)) ? $init->scheme : Defaults::matrix['scheme']; //загрузка схемы данных
			if (!empty($init->reestr)) { //инициализация реестра
				try {
					$this->reestr = new Reestr($init->reestr);
				} catch (\Exception $e) {
					throw new \Exception(Errors::matrix['M02']);
					error_log($e->getMessage(), 0);					
				}
			} else {
				$this->reestr = new Reestr();
			}
			$this->data = (!empty($init->data)) ? $init->data : array('items'=>array(),'levels'=>array(),'data'=>array());			
		} 
	}
	
	/**	
	 * Методы работы с иерархией уровней и соответствующих им специалистов (предметной области). 
	 * Формат датасета - двухмерный массив с числовыми координатами: нулевая строка - предметы 
	*/
	
	/**	
	 * Проверка размерности веерной матрицы
	*/
	
	public function test_dimension() {
		if (count($this->data['levels']) == count($this->data['items'])) {
			return TRUE;
		} else {
			throw new \Exception(Errors::matrix['M04']);
		}
	}
	
	/**	
	 * Получение размерности веерной матрицы
	*/
	
	public function get_dimension() {
		if ($this->test_dimension()) {
			return count($this->data['levels']);
		}
	}
	
	/**	
	 * Приведение размерности веерной матрицы к нормальному
	*/
	
	protected function cast() {
		$r = count($this->data['levels']) - count($this->data['items']);
		if ($r>0) $this->data['items'] = array_merge($this->data['items'],array_fill(0,$r,''));		
		if ($r<0) $this->data['levels'] = array_merge($this->data['levels'],array_fill(0,abs($r),''));							
	}
	
	/**	
	 * Перестройка матрицы при удалении элементов уровня или предмета, принимает координату x уровня или предмета
	*/
	
	protected function rebuild($x) {
		if (!empty($this->reestr->data)) { //Если матрица имеет производные
			throw new \Exception(Errors::matrix['M05']);
		} else { //если матрица не имеет производных
			$this->delete_agregat($x); //то удаляем элементарный агрегат
			if ($this->depth==0) { //если при этом она является матрицей нулевого уровня
				$this->reindex(); //то проводим её переиндексацию
			}
		}
	}
	
	/**	
	 * Удаление элементарного агрегата со всеми производными без переиндексации индексов
	*/
	
	protected function delete_agregat($x) {
		unset($this->data['items'][$x]); //удаление предмета
		unset($this->data['levels'][$x]); //удаление уровня
		unset($this->data['data'][$x]); //удаление столбца соответствующих феноменов			
		foreach ($this->data['data'] as $key=>$value) { //удаление строки соответствующих феноменов	
			unset ($this->data['data'][$key][$x]);
		}	
	}
	
	/**	
	 * Переиндексация матрицы 
	*/
		
	protected function reindex() {
		$this->data['items'] = array_values($this->data['items']);
		$this->data['levels'] = array_values($this->data['levels']);
		$this->data['data'][0] = array();
		$this->data['data'] = array_values($this->data['data']);
		foreach ($this->data['data'] as $key=>$value) { 
			$this->data['data'][$key][0] = array(); 
			$this->data['data'][$key] = array_values($this->data['data'][$key]);
		}
	}
		
	/**	
	 * Получение всех уровней организации (структур). NULL при отсутствии уровней (пустой матрице)
	*/
	
	public function levels() {
		$levels = array();
		if (isset($this->data['levels'])) $levels = $this->data['levels']; 		
		return $levels;
	}
	
	/**	
	 * Получение всех предметов (специалистов). NULL при отсутствии специалистов (предметов) 
	*/
	
	public function items() {
		$items = array();
		if (isset($this->data['items'])) $items = $this->data['items']; 		
		return $items;
	}
			
	/**	
	 * Редактирование, получение и добавление уровня вниз иерархии уровней или по координате
	*/
	
	public function level($level=NULL,$x=NULL) {
		if (is_numeric($x) and !empty($level)) {
			$this->data['levels'][$x] = $level;
		} else {
			$this->data['levels'][] = $level;
		}
		return $this;
	}
		
	/**	
	 * Редактирование, получение и добавление предмета в конец или по координате
	*/
	
	public function item($item=NULL,$y=NULL) {
		if (is_numeric($y) and !empty($item)) {
			$this->data['items'][$y] = $item;
		} else {
			$this->data['items'][] = $item;
		}
		return $this;
	}
	
	/**	
	 * Добавление элементарного агрегата: уровень, предмет, феномен главной диагонали (не обязательно)
	*/
	
	public function add_agregat($level,$item,$phenomen=NULL) {
		$this->level($level);
		$dim = $this->get_dimension(); //получили размерность и проверили на квадратность
		$this->item($item,$dim-1); //предмет добавляем строго по координатам
		if (!empty($phenomen)) {
			$this->add_data($phenomen,$dim,$dim);
		}
		return $this;
	}
	
	/**	
	 * Добавление феномена
	*/
	
	public function add_data($content,$x,$y) {
	
		return $this;
	}
	
	/**	
	 * Добавление списка уровней в виде массива в иерархию уровней
	*/
	
	public function add_levels($add_levels) {
		$add_levels = array_values($add_levels);
		$levels = $this->levels();
		$this->data['levels'] = array_merge($levels,$add_levels);
		return $this;
	}
		
	/**	
	 * Добавление списка предметов в виде массива
	*/
	
	public function add_items($add_items) {
		$add_items = array_values($add_items);
		$items = $this->items();
		$this->data['items'] = array_merge($items,$add_items);
		return $this;
	}
	
	
	/**	
	 * Автогенерация предбазиса матрицы: простая или семантическая
	*/
	
	public function autogenerate($semantic=FALSE) {
		if (count($this->data['levels'])>count($this->data['items'])) {
			$this->generate_items($semantic);
		} else {
			$this->generate_levels($semantic);
		}		
		$this->cast();
		return $this;
	}
	
	/**	
	 * Генерация предметов
	*/
	
	public function generate_items($semantic) {
		foreach ($this->data['levels'] as $key=>$level)
		{
			if (is_string($level) and empty($this->data['items'][$key])) {
				$this->data['items'][$key] = ($semantic) ? $this->semantic($level) : Defaults::matrix['item'].' '.$level;
			} else {
				throw new \Exception(Errors::matrix['M06']);
			}
		}		
	}
	
	/**	
	 * Генерация уровней
	*/
	
	public function generate_levels($semantic) {
		foreach ($this->data['items'] as $key=>$item)
		{
			if (is_string($item) and empty($this->data['levels'][$key])) {
				$this->data['levels'][$key] = ($semantic) ? $this->semantic($item) : Defaults::matrix['level'].' '.$item;
			} else {
				throw new \Exception(Errors::matrix['M07']);
			}
		}		
	}
	
	/**	
	 * Семантическая генерация уровня организации из предмета
	*/
	
	/**	
	 * Выделение стемов
	*/
	
	public function stem($string) {
		
		$stem = new \Stem\LinguaStemRu;
		return $stem->stem_word($string);
	}
	
	
	
	/**	
	 * Добавление элементарных агрегатов массивами
	*/
	
	public function add_agregats($levels,$items) {
		$this->add_levels($levels);
		$this->add_items($items);
		$this->cast(); //приведение размерности массивов уровней и предметов
		return $this;
	}
	
	/**	
	 * Удаление элементов матрицы: если передано одно значение, то удаляется весь уровень и предмет, если два - то обнуляется феномен с координатами вектора
	*/
	
	public function delete($x, $y=NULL) {
		if (empty($y)) {
			$this->rebuild($x); 
		} else {
			unset ($this->data['data'][$x][$y]); 
		}		
		return $this;
	}
	
	
	
}
