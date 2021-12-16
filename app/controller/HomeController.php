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
use \Logger;

class HomeController
{
	
	public function delete() {
		$Matrix = new Matrix();
		$ids = $Matrix->select('id')->get()->toArray();
		var_dump($ids);
		foreach ($ids as $id) {
			$Matrix->delete_matrix($id['id']);
		}
	}
	
	public function install() {
		$Matrix = new Matrix();
		$log = new Logger('parse');
		$files = Iterator::RecursiveIterator(\Flight::get('temp.path'),\Flight::get('temp.ext'));
			foreach ($files as $file) {					
			$matrix_data = Factory::install($file);	
			if (!empty(Factory::$errors)) {
				$log->log($file->getFilename().' : '.array_pop(Factory::$errors));						
			} else {
				try {
					$Matrix->install($matrix_data);
				} catch (\Exception $e) {					
					var_dump($file);
					echo $e->getMessage();
					var_dump($matrix_data);
				}
				if (!empty($Matrix::$errors)) {
					var_dump($file);
					var_dump($Matrix::$errors);
				}
			}
		}	
	}
	
	
    public function showHome()
    {	
		$Matrix = new Matrix();	
		if (isset(\Flight::request()->data['submit_search'])) {
			$string = \Flight::request()->data['string'];		
			echo $string;
		} 	
		$matrixs = $Matrix->catalog();
        \Flight::render('index.php', ['matrixs' => $matrixs]);     		
    }
}
