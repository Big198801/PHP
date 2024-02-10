<?php

return [
    'root_dir' =>  dirname(__DIR__),
    'templates_dir' => dirname(__DIR__) . "/twigTemplates/",
    'controllers_namespaces' => 'app\\controllers\\',
    'defaultAction' => '.Error',
    'pageSize' => 10,
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'login' => 'root',
        'password' => 'root',
        'database' => 'geekbrains',
        'charset' => 'utf8'
    ],
];
