<?php
/**
 * Контроллер главной страницы
 */

namespace Controller;
use \Models\Users;
use \Logger;
use Josantonius\Session\Session;

class AdminController
{
	
	public function login() {
		$error = NULL;		
		if (isset(\Flight::request()->data['submit'])) {			
			$login = \Flight::request()->data['login'];
			$password = \Flight::request()->data['password'];		
			$users = Users::where('role','admin')->where('login',$login)->first();								
			if (empty($users)) $error = 'Такой пользователь не найден';		
			elseif (password_verify($password, $users->password)) {					
				Session::setPrefix('login_');	
				Session::init();
				$data = [
					'name' => $login,
					'first_name' => $users->first_name,
					'last_name' => $users->last_name,
					'role'=>'admin'			
				];
				Session::set($data);				
				\Flight::redirect('/admin');
			} else {
				$error = 'Неправильный пароль';
			}
		}
		\Flight::render('login.php',['error'=>$error]);   
	}
	
	public function admin() {
		$data = NULL;
		\Flight::render('admin.php',['data'=>$data,'first_name'=>\Flight::get('first_name'),'last_name'=>\Flight::get('last_name')]);  
	}

	
}
