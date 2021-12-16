<?php


Flight::route('/', ['\Controller\HomeController', 'showHome']);

//логин

Flight::route('/login', ['\Controller\AdminController', 'login']);

//пользователи

Flight::route('/admin/user/add', ['\Controller\UsersController', 'edit']);
Flight::route('/admin/user/', ['\Controller\UsersController', 'list']);
Flight::route('/admin/user/edit(/@id)', ['\Controller\UsersController', 'edit']);

//админка

Flight::route('/admin/', ['\Controller\AdminController', 'admin']);
Flight::route('GET /admin/delete', ['\Controller\HomeController', 'delete']);
Flight::route('GET /admin/install', ['\Controller\HomeController', 'install']);
Flight::route('GET /admin/catalog(/@page)', ['\Controller\MatrixController', 'catalog']);
Flight::route('/admin/edit/@matrix_id', ['\Controller\MatrixController', 'edit']);
Flight::route('/admin/show/@matrix_id', ['\Controller\MatrixController', 'show']);
Flight::route('/admin/upload', ['\Controller\MatrixController', 'upload']);

//ключи API

Flight::route('/admin/keys/add', ['\Controller\KeysController', 'add']);
Flight::route('/admin/keys/', ['\Controller\KeysController', 'list']);
Flight::route('/admin/keys/delete(/@id)', ['\Controller\KeysController', 'delete']);


//AJAX

Flight::route('GET /addon/@addon_id', ['\Controller\MatrixController', 'get_addon']);
Flight::route('POST /saveaddon', ['\Controller\MatrixController', 'save_addon']);
Flight::route('POST /deletelevel', ['\Controller\MatrixController', 'delete_level']);
Flight::route('POST /savelevel', ['\Controller\MatrixController', 'save_level']);
Flight::route('POST /savematrix', ['\Controller\MatrixController', 'save_matrix']);
Flight::route('POST /deletematrix', ['\Controller\MatrixController', 'delete_matrix']);

//API

Flight::route('GET /json/@method/@matrix_id(/@level)', ['\Controller\ApiController', 'json']);
//generate XLSX matrix
Flight::route('/export/@matrix_id', ['\Controller\ApiController', 'export']);

//UPLOAD MATRIX FROM CSV OR XLSX

Flight::route('POST /admin/fileupload', ['\Controller\InstallController', 'upload']);

Flight::route('POST /search', ['\Controller\SearchController', 'search']);

//LEXICON

//form controller- ststus: stat,find itc
Flight::route('/text/@status', ['\Controller\LexiconController', 'text']);
Flight::route('/test/', ['\Controller\LexiconController', 'test']);

//ajax
Flight::route('POST /textstat', ['\Controller\LexiconController', 'textstat']);
Flight::route('POST /textfind', ['\Controller\LexiconController', 'textfind']);
