<?php


define('BASE_DIR', getcwd());

Flight::set('base.url', 'http://iris/'); // Базовый путь
Flight::set('flight.views.path', BASE_DIR . '/resources/views');
Flight::set('flight.log_errors', true); //сохраняем ошибки в логах сервера
Flight::set('libs.path', realpath('./app/libraries')); // Путь к библиотекам
Flight::set('temp.path', realpath('./temp')); // Путь к папке temp
Flight::set('temp.ext', ['xls','ods','xlsx','csv']); // Допустимые расширения файлов для папки temp
Flight::set('docs', realpath('./docs')); // Путь к документации
Flight::set('pagination', 20); // Количество выводимых матриц в каталоге

Flight::render('common/header.php', array('base_url'=>Flight::get('base.url')), 'header');
Flight::render('common/footer.php', array('base_url'=>Flight::get('base.url')), 'footer');
Flight::render('common/menu.php', array('base_url'=>Flight::get('base.url')), 'menu');

Flight::render('common/login_header.php', array('base_url'=>Flight::get('base.url')), 'login_header');

Flight::render('common/admin_header.php', array('base_url'=>Flight::get('base.url')), 'admin_header');
Flight::render('common/admin_footer.php', array('base_url'=>Flight::get('base.url')), 'admin_footer');
Flight::render('common/admin_menu.php', array('base_url'=>Flight::get('base.url')), 'admin_menu');

Flight::set('parse_log',false); //сохранять логи или выводить на экран при парсинге
Flight::set('parse_log_path',realpath('./logs').'/parse.log'); //файл логгирования парсинга

?>


