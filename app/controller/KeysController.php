<?php
/**
 * Контроллер Api
 */

namespace Controller;
use \Models\Api_keys;
use \Models\Users;
use \Logger;

class KeysController
{
	public function list() {
		$keys = Api_keys::join('users', 'api_keys.user_id', '=', 'users.id')->orderBy('users.id','ASC')->get();
		\Flight::render('keyslist.php', ['keys' => $keys]);   
	}
	
	public function add() {
		if (isset(\Flight::request()->data['submit'])) {
			$data = \Flight::request()->data['data'];
			$data['key'] = md5((string)rand(5,10500));
			Api_keys::insert($data);
			\Flight::redirect('\admin\keys');
		}
		$users = Users::where('role','client')->get();
		\Flight::render('keyadd.php', ['users' => $users]);   
	}
	
	public function delete($id) {
		Api_keys::where('id',$id)->delete();
		\Flight::redirect('\admin\keys');	 
	}
	
}
