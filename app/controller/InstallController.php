<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 2:37 PM
 */

namespace Controller;
use \Directory\Iterator;
use \Dataparser\Factory;
use \Models\Matrix;

class InstallController
{	
	public function install_all() {
		$Matrix = new Matrix();
		$files = Iterator::RecursiveIterator(\Flight::get('temp.path'),\Flight::get('temp.ext'));
			foreach ($files as $file) {					
			$matrix_data = Factory::install($file);	
			if (!empty(Factory::$errors)) {
				var_dump($file);
				var_dump(Factory::$errors);				
			} else {
				$Matrix->install($matrix_data);
				if (!empty($Matrix::$errors)) {
					var_dump($file);
					var_dump($Matrix::$errors);
				}
			}
		}	
	}
	
	public function upload() {
		if (isset(\Flight::request()->data['submit_upload'])) {
			$errors = [];	
			$filename = NULL;
			try {
				$uploader = new \Sokil\Upload\Handler([
					'fieldName' => 'file',
					'supportedFormats' => ['ods','xls','xlsx','csv']
				]);
				$uploader->moveLocal(\Flight::get('temp.path'));	
				$filename = $uploader->getFile()->getOriginalBasename();
				$file = \Flight::get('temp.path').'/'.$filename;
				$Matrix = new Matrix();
				$matrix_data = Factory::install($file);	
				if (!empty(Factory::$errors)) {
					$errors[] = 'Ошибки разбора файла: '.implode(', ',Factory::$errors);
				} else {
					$matrix_id = $Matrix->install($matrix_data);
					if (!empty($Matrix::$errors)) {
						$errors[] = 'Ошибки создания матрицы: '.implode(', ',$Matrix::$errors);
					}
				}				
			} catch (\Exception $e) {
				$errors[] = 'Ошибка загрузки файла: '.$e->getMessage();
			}			
			if (!empty($errors)) {	
				$ctrl = new MatrixController();
				$ctrl->upload($errors);	
			} else {
				\Flight::redirect('/admin/edit/'.$matrix_id); 
			}
		} else {
			\Flight::redirect('/admin/upload/'); 
		}
	}
	
    
}
