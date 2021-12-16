<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 2:37 PM
 */

namespace Controller;
use \Models\Matrix;
use \Models\MatrixAddons;
use \Models\MatrixMethod;
use \Models\MatrixObject;
use \Models\MatrixCell;
use \Models\MatrixDataset;
use \Models\MatrixAutor;
use \Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

class MatrixController
{
	//вывод каталога имеющихся матриц
	public function catalog($page=0)
	{
		$Matrix = new Matrix();	
		$matrixs = $Matrix->catalog($page);
		$pages = ceil($Matrix::count()/\Flight::get('pagination'));
		\Flight::render('catalog.php', ['matrixs' => $matrixs,'pages'=>$pages,'page'=>$page]);   
	}
	
	//загрузка матрицы
	public function upload($errors=NULL)
	{	$content = \Parsedown::instance()->text(file_get_contents(\Flight::get('docs').'/docs/'.'format.md'));
		$formats = \Parsedown::instance()->text(file_get_contents(\Flight::get('docs').'/docs/'.'formats.md'));
		\Flight::render('upload.php', ['content'=>$content,'formats'=>$formats,'errors'=>$errors]);   
	}
	
	// отображение веерной матрицы
	
	public function show($matrix_id)
    {	
		$Matrix = new Matrix();			
		$matrix_data = $Matrix->get($matrix_id);		
        \Flight::render('show.php', ['matrix' => $matrix_data]);     		
    }
	
	// редактирование веерной матрицы
	
    public function edit($matrix_id)
    {	
		$Matrix = new Matrix();			
		
		if (isset(\Flight::request()->data['submit'])) {
			$type = \Flight::request()->data['type'];
			$id = \Flight::request()->data['id'];
			$data = \Flight::request()->data['data'];
			$title = \Flight::request()->data['title'];
			$addons_id = \Flight::request()->data['addons_id'];
			$controller = \Flight::request()->data['controller'];
			$name = '\Models\Matrix'.$controller;				
			if (!empty($data)) {
				$result = MatrixAddons::where('id',$addons_id)->update(['type'=>$type,'data'=>$data]);				
			}
			$name::where('id',$id)->update(['title'=>$title]);	
			$Matrix->where('id',$matrix_id)->update(['updated_at'=>Carbon::now()->toDateTimeString()]);		
		} 	
			
		if (isset(\Flight::request()->data['mainsubmit'])) {
			$title = \Flight::request()->data['maintitle'];
			$level = \Flight::request()->data['mainlevel'];
			$autor = \Flight::request()->data['mainautor'];
			$description = \Flight::request()->data['maindescription'];
			$autor_id = MatrixDataset::where('matrix_id',$matrix_id)->where('level',$level)->select('autor_id')->first()->autor_id;
			MatrixAutor::where('id',$autor_id)->update(['name'=>$autor]);
			MatrixDataset::where('matrix_id',$matrix_id)->where('level',$level)->update(['title'=>$title,'description'=>$description]);
			$Matrix->where('id',$matrix_id)->update(['updated_at'=>Carbon::now()->toDateTimeString()]);
		} 
		
		$matrix_data = $Matrix->get($matrix_id);	
		//var_dump($matrix_data);	
		
		\Flight::render('edit.php', ['matrix' => $matrix_data]);  
        //\Flight::render('matrix.php', ['matrix' => $matrix_data,'status'=>'edit','matrix_id'=>$matrix_id]);     		
    }
   
    
    // получение значения addon через ajax
    
    public function get_addon($addon_id) {
		if (\Flight::request()->ajax) {	
			$addon = MatrixAddons::where('id',$addon_id)->select('data','type')->first();
			\Flight::json($addon);
		}
	}
	
	public function save_addon() {
		if (\Flight::request()->ajax) {	
			$id = \Flight::request()->data['id'];				
			if (!empty($id)) {
				$controller = '\Models\Matrix'.\Flight::request()->data['controller'];	
				$controller = new $controller();
				MatrixAddons::where('id',$id)->update([
					'data'=> \Flight::request()->data['data'],
					'type'=> \Flight::request()->data['type'],
				]);
				$controller::where('addons_id',$id)->update(['title'=>\Flight::request()->data['title']]);					
			} 
			/*
			else {
				$controller = \Flight::request()->data['controller'];
				$matrix_id = \Flight::request()->data['matrix_id'];
				$level = \Flight::request()->data['level'];
				$dataset = MatrixDataset::where('matrix_id',$matrix_id)->where('level',$level)->select('id')->first();
				$dataset_id = $dataset->id;
				$addon_id = MatrixAddons::insertGetId([
					'data'=> \Flight::request()->data['data'],
					'type'=> \Flight::request()->data['type'],
				]);
				if ($controller == 'Method') {
					$row = \Flight::request()->data['method_id'];	
					$method_id = MatrixMethod::insertGetId(['title'=>\Flight::request()->data['title'], 'dataset_id'=>$dataset_id, 'row'=>$row,'addons_id'=>$addon_id]);	
					$check = MatrixObject::where('dataset_id',$dataset_id)->where('col',$row)->select('id')->first();					
					$object_id = (empty($check->id)) ? MatrixObject::insertGetId(['title'=>'Добавить', 'dataset_id'=>$dataset_id, 'col'=>$row,'addons_id'=>$addon_id]) : $check->id;									
				}
				if ($controller == 'Object') {
					$col = \Flight::request()->data['object_id'];	
					$object_id = MatrixObject::insertGetId(['title'=>\Flight::request()->data['title'], 'dataset_id'=>$dataset_id, 'col'=>$col,'addons_id'=>$addon_id]);
					$check = MatrixMethod::where('dataset_id',$dataset_id)->where('row',$col)->select('id')->first();
					$method_id = (empty($check->id)) ? MatrixMethod::insertGetId(['title'=>'Добавить', 'dataset_id'=>$dataset_id, 'row'=>$col,'addons_id'=>$addon_id]) : $check->id;								
				}
				$methods = MatrixMethod::where('dataset_id',$dataset_id)->select('id')->get();
				$objects = MatrixObject::where('dataset_id',$dataset_id)->select('id')->get();
				foreach ($methods as $method) {
					$addon_id = MatrixAddons::insertGetId(['data'=> '','type'=> NULL,]);
					MatrixCell::insert(['title'=>'','method_id'=>$method->id,'object_id'=>$object_id]);					
				}		
				foreach ($objects as $object) {
					$addon_id = MatrixAddons::insertGetId(['data'=> '','type'=> NULL,]);
					MatrixCell::insert(['title'=>'','object_id'=>$object->id,'method_id'=>$method_id]);					
				}			
			}
			* */
			http_response_code(200);
			\Flight::json(['addon'=>$id]);
		}
	}
	
	public function delete_level(){
		if (\Flight::request()->ajax) {	
			$id = \Flight::request()->data['id'];
			$level = \Flight::request()->data['level'];
			$dataset_id = MatrixDataset::where('matrix_id',$id)->where('level',$level)->select('id')->first();
			$dataset_id = $dataset_id->id;
			DB::select('DELETE FROM matrix_cell WHERE method_id IN (SELECT id FROM matrix_method WHERE dataset_id='.$dataset_id.') OR object_id IN (SELECT id FROM matrix_object WHERE dataset_id='.$dataset_id.')');
			MatrixObject::where('dataset_id',$dataset_id)->delete();
			MatrixMethod::where('dataset_id',$dataset_id)->delete();
			MatrixDataset::where('id',$dataset_id)->delete();			
			http_response_code(200);
			\Flight::json('success');			
		}
		
	}
	
	public function delete_matrix(){
		if (\Flight::request()->ajax) {	
			$id = \Flight::request()->data['id'];
			Matrix::delete_matrix($id);		
			http_response_code(200);
			\Flight::json('success');			
		}		
	}
	
	public function save_level() {
		if (\Flight::request()->ajax) {	
			$id = \Flight::request()->data['id'];
			$level = \Flight::request()->data['level'];
			$dataset_id = MatrixDataset::where('matrix_id',$id)->where('level',$level)->select('id')->first();
			$dataset_id = $dataset_id->id;
			$autor = MatrixAutor::where('name',\Flight::request()->data['autor'])->first();
			if (isset($autor->name)) {
				$autor_id = $autor->id;
			} else {
				$autor_id = MatrixAutor::insertGetId(['name'=>\Flight::request()->data['autor']]);
			}
			MatrixDataset::where('id',$dataset_id)->update(['title'=> \Flight::request()->data['title'],
				'description'=> \Flight::request()->data['description'],
				'autor_id'=> $autor_id
				]);
			http_response_code(200);
			\Flight::json('success');
		}
	}
	
	public function save_matrix() {
		if (\Flight::request()->ajax) {	
			$id = \Flight::request()->data['id'];	
			$autor = MatrixAutor::where('name',\Flight::request()->data['autor'])->first();
			$public = (\Flight::request()->data['public']==1) ? TRUE : FALSE;
			if (isset($autor->name)) {
				$autor_id = $autor->id;
			} else {
				$autor_id = MatrixAutor::insertGetId(['name'=>\Flight::request()->data['autor']]);
			}
			Matrix::where('id',$id)->update(['title'=> \Flight::request()->data['title'],
				'description'=> \Flight::request()->data['description'],
				'autor_id'=> $autor_id,
				'public'=>  $public,
				]);
			http_response_code(200);
			\Flight::json('success');
		}
	}

    
}
