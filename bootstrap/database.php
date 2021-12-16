<?php

$capsule = new \Illuminate\Database\Capsule\Manager;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'iris',
    'username' => 'iris',
    'password' => '645zzz',
//     'port' => getenv('DB_PORT'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_general_ci',
    'prefix' => ''
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
