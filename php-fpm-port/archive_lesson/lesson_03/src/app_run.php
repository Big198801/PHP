<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use App\Oop\App;

$app = new App();
echo $app->run();