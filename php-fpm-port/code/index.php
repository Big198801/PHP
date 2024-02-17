<?php

require_once('./vendor/autoload.php');

use Myproject\Application\Application\Application;
use Myproject\Application\Application\Render;

try {
    $app = new Application();
    $result = $app->runApp();

    echo $result;
} catch (\Exception $e) {

    $_SESSION['error_message'] = $e->getMessage();

    header("Location: /page/index/?error=1");
    die();
}
