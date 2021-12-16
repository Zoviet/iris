<?php
/**
 * Контроллер главной страницы
 */

namespace Controller;
use \Models\Users;
use \Logger;

class UsersController
{
	public function list() {
		$users = Users::orderBy('id','ASC')->get();
		\Flight::render('userlist.php', ['users' => $users]);   
	}
	
	public function edit($id=NULL) {
		if (isset(\Flight::request()->data['delete'])) {
			Users::where('id',\Flight::request()->data['data']['id'])->delete();
			$id = NULL;		
		}
		if (isset(\Flight::request()->data['submit'])) {	
			$id = \Flight::request()->data['data']['id'];	
			$data = \Flight::request()->data['data'];
			if (strlen($data['password'])<40) {
				$data['password'] =  password_hash($data['password'],PASSWORD_DEFAULT);
			}
			if (empty(Users::where('id',$id)->first())) { 	
				unset($data['id']);			
				$id = Users::insertGetId($data);				
			} else {
				Users::where('id',$id)->update($data);
			}
		}		
		if (!empty($id)) {		
			$user = Users::where('id',$id)->first()->toArray();
		} else {
			$user = array_map(function($var) {return NULL;}, Users::first()->toArray());			
		}
		\Flight::render('useredit.php',['user'=>$user]);   
	}


	
}
