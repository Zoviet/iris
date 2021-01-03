<?php
require 'vendor/autoload.php';
use flight\core\Loader;
Flight::set('flight.handle_errors', false);
$loader = new Loader();
$loader::addDirectory(__DIR__);
Flight::set('flight.views.path', __DIR__.'/views');
Flight::render('header', array('title'=>'Веерные матрицы', 'base_url'=>'http://iris/'), 'header_content');
Flight::render('sidebar', array(), 'sidebar_content');
Flight::render('footer', array('base_url'=>'http://iris/'), 'footer_content');


//Главная страница

Flight::route('/', function(){ //временный редирект на тест связи
echo 'start';
$reestr = new iris\Reestr();
var_dump($reestr);
$items = array('Рукожопы', 'Пиздоболы', 'Мудаки', 'Твари масочные');
$matrix = new iris\Matrix();
$matrix->add_items($items)->autogenerate();

var_dump($matrix);
	
});



Flight::start();
