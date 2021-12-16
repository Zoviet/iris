<?php
/**
 */
require 'vendor/autoload.php';

Flight::before('route', function(&$params, &$output){
	$url = explode('/',$_SERVER['REQUEST_URI'])[1];			
	$Session = new Josantonius\Session\Session();
	$Session::init();
	$data = $Session::get();
	$role = (isset($data['login_role'])) ? $data['login_role'] : NULL;	
	if ($role !== 'admin' and $url=='admin') {		
		\Flight::render('notlogin.php');  
	} else {
		if (isset($data['login_first_name'])) {
			\Flight::set('first_name',$data['login_first_name']);
			\Flight::set('last_name',$data['login_last_name']);
		}
	}
});

include_once 'bootstrap/config.php';
include_once 'bootstrap/routes.php';
include_once 'bootstrap/database.php';



require_once Flight::get('libs.path').'/Dataparser/Matrix.inc';

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Flight::get('libs.path')), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $name => $object){
	if ($object->getExtension()=='php') {
		require $name;
	}
}


Flight::start();
