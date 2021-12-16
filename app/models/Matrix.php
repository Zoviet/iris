<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 3:43 PM
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Capsule\Manager as DB;


class Matrix extends Model
{
	protected $table = 'matrix';
	
	protected $fillable = [
		'id',
		'autor_id',
		'title',
		'description',	
		'created_at',
		'updated_at'		
	];	
	
	public static $errors=[];
	
	//загрузка из файла
	
	public function install($sheets) {
		$level = 0;
		self::$errors=[];
		foreach ($sheets as $data) {			
			$autor_id = MatrixAutor::firstOrCreate(['name' => trim($data->autor)])['id'];
			if (empty($level)) {
				$title = $data->title;
				$description = $data->description;
				$matrix_id = $this->insertGetId(['autor_id'=> $autor_id, 'title'=>$title, 'description' => $description]);
			} 
			$title = (empty($data->title)) ? $title : $data->title;
			$description = (empty($data->title)) ? $description : $data->description;
			$dataset_id = MatrixDataset::firstOrCreate(['autor_id'=> $autor_id, 'title'=>$title, 'description' => $description,'level'=>$level,'matrix_id'=>$matrix_id])['id'];
			
			if (!isset($data->data[array_key_first($data->data)])) {				
				self::$errors[] = 'Для матрицы не установлены данные';
				return FALSE;
			}
			
			$row=array_key_first($data->data);
			while (isset($data->data[$row])) {
				$col=array_key_first($data->data[$row]);
				while (isset($data->data[$row][$col])) { 				
					$addon = $this->addon($data->data[$row][$col]);
					if ($row==0) {
						if (array_key_first($data->data[$row])>0) $object_id[0] = MatrixObject::firstOrCreate(['title'=>'', 'addons_id'=>$this->addon(NULL)->id, 'dataset_id'=>$dataset_id,'col'=>0])['id'];
						$object_id[$col] = MatrixObject::firstOrCreate(['title'=>$addon->title, 'addons_id'=>$addon->id, 'dataset_id'=>$dataset_id,'col'=>$col])['id'];																											
					}
					if (empty($col)) $method_id[$row] = MatrixMethod::firstOrCreate(['title'=>$addon->title, 'addons_id'=>$addon->id, 'dataset_id'=>$dataset_id,'row'=>$row])['id'];				
					if (!empty($col) and !empty($row)) $list_id = MatrixCell::firstOrCreate(['title'=>$addon->title, 'method_id'=>$method_id[$row],'object_id'=>$object_id[$col],'addons_id'=>$addon->id])['id'];  									
					$col++;
				}			
				$row++;
			}
			$level++;			
		}
		return $matrix_id;
	}
	
	//получение матрицы
	
	public function get($matrix_id) {			
		$matrix = DB::table('matrix')->where('matrix.id', '=', $matrix_id)->join('matrix_autor', 'matrix.autor_id', '=', 'matrix_autor.id')->select('matrix.id','matrix.title','matrix.description','matrix_autor.name','matrix.public')->first();
		$matrix->data = [];	
		$datasets =  DB::table('matrix_dataset')->where('matrix_id', '=', $matrix_id)->join('matrix_autor', 'matrix_dataset.autor_id', '=', 'matrix_autor.id')->select('matrix_dataset.id', 'matrix_dataset.level','matrix_dataset.title','matrix_dataset.description', 'matrix_autor.name')->get();
		foreach ($datasets as $dataset) {
			$matrix->data[$dataset->level] = new \STDClass();
			$matrix->data[$dataset->level]->title = $dataset->title;
			$matrix->data[$dataset->level]->description = $dataset->description;
			$matrix->data[$dataset->level]->autor = $dataset->name;
			$matrix->data[$dataset->level]->methods = $this->rowscols(DB::table('matrix_method')->where('dataset_id', '=', $dataset->id)->join('matrix_addons', 'matrix_method.addons_id', '=', 'matrix_addons.id')->select('matrix_method.title','matrix_method.id','matrix_method.row','matrix_addons.type','matrix_addons.data','matrix_method.addons_id')->get());
			$matrix->data[$dataset->level]->objects =  $this->rowscols(DB::table('matrix_object')->where('dataset_id', '=', $dataset->id)->join('matrix_addons', 'matrix_object.addons_id', '=', 'matrix_addons.id')->select('matrix_object.title', 'matrix_object.id','matrix_object.col','matrix_addons.type','matrix_addons.data','matrix_object.addons_id')->get());
			$matrix->data[$dataset->level]->count = count($matrix->data[$dataset->level]->methods);			
			$matrix->data[$dataset->level]->cells = DB::table('matrix_cell')->whereIn('method_id', array_keys($matrix->data[$dataset->level]->methods))->whereIn('object_id', array_keys($matrix->data[$dataset->level]->objects))->join('matrix_addons', 'matrix_cell.addons_id', '=', 'matrix_addons.id')->select('matrix_cell.title','matrix_cell.id','matrix_addons.type', 'matrix_cell.addons_id', 'matrix_addons.data','matrix_cell.method_id','matrix_cell.object_id')->get();
			//собираем в таблицу		
			$matrix->data[$dataset->level]->table =[];
			$matrix->data[$dataset->level]->table[0] = $this->rowscols($matrix->data[$dataset->level]->objects,'col');		
			$methods =  $this->rowscols($matrix->data[$dataset->level]->methods,'row');		
			foreach ($matrix->data[$dataset->level]->cells as $cell) {
				$row = $matrix->data[$dataset->level]->methods[$cell->method_id]->row;
				$col = $matrix->data[$dataset->level]->objects[$cell->object_id]->col;
				$matrix->data[$dataset->level]->table[$row][0] = $methods[$row];
				$matrix->data[$dataset->level]->table[$row][$col] = $cell;				
			}
			
		}
		return $matrix;
	}
	
	//удаление матрицы
	
	public function delete_matrix($matrix_id) {
		DB::table('matrix')->where('id', '=', $matrix_id)->delete();
		$datasets =  DB::table('matrix_dataset')->where('matrix_id', '=', $matrix_id)->select('matrix_dataset.id', 'matrix_dataset.level')->get();
		foreach ($datasets as $dataset) {			
			DB::table('matrix_method')->where('dataset_id', '=', $dataset->id)->delete();	
			DB::table('matrix_object')->where('dataset_id', '=', $dataset->id)->delete();
			DB::table('matrix_dataset')->where('id', '=', $dataset->id)->delete();
		}
		DB::table('matrix_cell')->whereNotIn('method_id',  function ($query) {
			$query->select('id')->from('matrix_method');
			})->orWhereNotIn('object_id',  function ($query) {
				$query->select('id')->from('matrix_object');
		})->delete();
		
		DB::table('matrix_addons')->whereNotIn('id',  function ($query) {
			$query->select('addons_id')->from('matrix_object');
		})->orWhereNotIn('id',  function ($query) {
			$query->select('addons_id')->from('matrix_method');
		})->orWhereNotIn('id',  function ($query) {
			$query->select('addons_id')->from('matrix_cell');
		})->delete();		
	}
	
	public function catalog($page=0) {
		$limit = \Flight::get('pagination');
		return DB::select("SELECT matrix.id, matrix.title, matrix.description, matrix_autor.name , matrix.updated_at as updated, matrix.public as public, DATE_FORMAT(matrix.updated_at, '%d-%m-%Y') as updated_at FROM matrix JOIN matrix_autor ON matrix.autor_id = matrix_autor.id ORDER BY updated DESC LIMIT ".$limit." OFFSET ".$page*$limit);		
	}
	
	//общее количество матриц
	public static function count() {
		return self::where('id','!=',NULL)->count();
	}
	
	
	private function rowscols($data,$type=NULL) {
		$result = [];
		foreach ($data as $value) {
			$number = (empty($type)) ? $value->id : $value->$type;		
			//unset($value->$type);
			$result[$number] = $value;
		}
		return $result;
	}
	
	private function addon($data) {
		$return = new \STDClass();
		$set = explode('::',$data);
		$return->title = ($set!==FALSE) ? array_shift($set) : NULL;
		if (strlen($return->title)>6000) {
			self::$errors[] = 'Слишком большой заголовок в ячейке';
			$return->title = substr($return->title,0,5900).'[обрезано]';
		}
		$type = $set[0] ?? NULL;
		$data = $set[1] ?? NULL;
		$return->id = MatrixAddons::create(['type'=>$type,'data'=>$data])['id'];		
		return $return;
	}
	
	public function search($qQeury,$count) {
		$results = DB::table('matrix_cell')->whereRaw(
            "MATCH(title) AGAINST(? IN BOOLEAN MODE)", // name,email - поля, по которым нужно искать
            $qQeury)->paginate($count);		
        return $results;
	}
	
}
