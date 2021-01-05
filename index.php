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
//var_dump($reestr);
$items = array('Рукожопы', 'Пиздоболы', 'Мудаки', 'Твари масочные');
$matrix = new iris\Matrix();
$matrix->add_items($items)->autogenerate();

$semantic = new iris\Semantic('Товарищи, филологический сегодня нам предстоит решить очень важный, в некотором смысле судьбоносный, спорный и, кхм, давно назревший вопрос');
$semantic->remove_braces();
echo $semantic->get_noun();
//var_dump($semantic);
echo '-------------------------------------------------------<br/>';
$speller = new iris\Speller('cпециалист');

//$semantic->explore();//remove_endings();
//var_dump($semantic->result);

//var_dump(str_word_count('dsad jjkj олоол воыло',2));
//var_dump($semantic);

//var_dump($matrix);
	
});



Flight::start();
