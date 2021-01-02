<?php
/**
 * Iris: Библиотека для работы с веерными матрицами
 *
 * @copyright   Copyright (c) 2021, Zoviet <alexandr@asustem.ru>
 * @license     GNU GENERAL PUBLIC LICENSE Version 3
 */

/**
 * Reestr: класс ведения реестра матриц (объектов класса Matrix), в том числе и вложенных (зависимых по глубине).  * 
 * Класс фрактален к любой глубине представления матриц: может быть использован для ведения реестров любой глубины:
 * Реестр первого уровня - список объектов родительских матриц
 * Реестр второго уровня - список объектов матриц-детей от родительской.
 * Реестр третьего и далее уровней - список объектов матриц-внуков и так далее. 
 * Фрактальность: любой уровень раскрытия может быть рассмотрен как самостоятельный реестр (как самостоятельное представление онтологии). 
 * 
 * Формат хранения реестра: массив $data. Ключи - ID матрицы, значение: объект класса Matrix. Глубина раскрытия матрицы - свойство не реестра, а объекта типа Matrix. 
 */
 
/**
 * Свойства
 * 
 * @var  public int ID Идентификатор веерной матрицы, с которой ведется работа.
 * @var  private array data Реестр.
 * @var  public array depths Массив матриц по глубине.
 * @var  protected array errors Ошибки класса
 * 
 * 
 * Работа с реестром матриц.
 * 
 * @method  public void add($matrix) Добавляет объект типа matrix в реестр. устанавливает свойство ID. Ничего не возвращает.
 * @method  public Matrix choise($id) - Выбор матрицы из реестра Устанавливает ID. Возвращает объект типа Matrix или NULL, если запрашиваемой матрицы нет.
 * @method  public void delete($id) - Удаление матрицы из реестра. Убивает объект Matrix.
 * @method  public array depth($depth) - Получение матриц запрашиваемой глубины. 
 * @method  public void remove_depth($depth) - Удаление всех матриц заданной глубины. 
 * @method  public int max_depth() - Получение максимальной глубины матриц реестра. 
 * @method  public bool test_gap() - Тест на связность матрицы: проверка реестра на разрывы по глубине: отсутствие промежуточного уровня при наличии сопредельных. TRUE - если тест на отсутствие разрывов пройден, FALSE - если нет.
*/

namespace iris;

class Reestr {

	public $ID = NULL;   
	private $data = array();
	public $depths = array(); 
	protected $errors = Config\Errors::reestr_errors;
	
	//Добавление матрицы в реестр
	
	public function add($matrix) {		
		if (!isset($matrix->ID)) {
			throw new \Exception($this->errors['R01']);
		} else {
			$this->ID = $matrix->ID;
			$this->data[$this->ID] = $matrix;	
			$this->depths[$matrix->depth][] = $this->ID;			
		}
	}
	
	//Выбор матрицы из реестра
	
	public function choise($id) {
		$matrix = (isset($this->data[$id])) ? $this->data[$id] : NULL; 
		return $matrix;
	}
	
	//Удаление матрицы из реестра
	
	public function delete($id) {
		unset ($this->data[$id]);
		unset (Helpers\Array::multi_search($this->depths,$id));
	}
	
	//Получение списка матриц запрашиваемой глубины
	
	public function depth($depth) {
		return $this->depths[$depth];
	}
	
	//Удаление всех матриц одной глубины
	
	public function remove_depth($depth) {
		unset ($this->depths[$depth]);
	}
	
	//Получение максимальной глубины матриц в реестре
	
	public function max_depth() {
		return max(array_keys($this->depths));
	}
	
	//Проверка связности реестра
	
	public function test_gap() {
		return (count($this->depths) == $this->max_depth()+1) ? TRUE : FALSE;
	}	
}
