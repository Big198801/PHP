<?php

require_once ('./vendor/autoload.php');

use Myproject\Application\Application;

$app = new Application();
echo $app->run();
