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
echo $matrix->stem('Рукожопы');

$semantic = new iris\Semantic('Специалисты по охранному мастерству (спецы по пулям)');
$semantic->remove_braces();
var_dump($semantic->words());
foreach ($items as $word) {
	echo $word; echo ':'; echo $semantic->test_word($word,TRUE);
}
//var_dump(str_word_count('dsad jjkj олоол воыло',2));
//var_dump($semantic);

//var_dump($matrix);
	
});



Flight::start();
