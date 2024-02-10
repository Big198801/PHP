<?php
/*define("ROOT", dirname(__DIR__));
define("DS", DIRECTORY_SEPARATOR);
define("CONTROLLER_NAMESPACE", 'app\\controllers\\');
define("VIEWS_DIR", '../views/');*/

use app\engine\Db;
use app\engine\Request;
use app\models\repositories\BasketRepository;
use app\models\repositories\UserRepository;
use app\engine\Session;
use app\models\repositories\ProductRepository;

return [
    'root' => dirname(__DIR__),
    'controllers_namespaces' => 'app\\controllers\\',
    'product_per_page' => 2,
    'views_dir' => dirname(__DIR__) . "/views/",
    'components' => [
        'db' => [
            'class' => Db::class,
            'driver' => 'mysql',
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'shop',
            'charset' => 'utf8'
        ],
        'request' => [
            'class' => Request::class
        ],
        'basketRepository' => [
            'class' => BasketRepository::class
        ],
        'productRepository' => [
            'class' => ProductRepository::class
        ],
        'usersRepository' => [
            'class' => UserRepository::class
        ],
        'session' => [
            'class' => Session::class
        ]
    ]
];
//['prodcuct', 'id', ']