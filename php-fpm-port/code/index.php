<?php

require_once('./vendor/autoload.php');

use Myproject\Application\Application\Application;
use Myproject\Application\Application\Render;

try {
    $app = new Application();
    echo $app->run();
} catch (\Exception $e) {
    echo (new Render())->renderExceptionPage($e->getMessage());
}
