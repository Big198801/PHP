<?php

require_once('../vendor/autoload.php');

use Myproject\Application\Application\Application;
use Myproject\Application\Application\Render;
use Myproject\Application\Domain\Models\UserRepository;

try {
    $app = new Application();
    $result = $app->runApp();

    echo $result;

} catch (\Exception $e) {

    $_SESSION['error_message'] = $e->getMessage();

    $redirectURL = '/' . explode('/', $_SERVER['REQUEST_URI'])[1] . '/index/?error=1';
    header("Location: $redirectURL");

    die();
}
