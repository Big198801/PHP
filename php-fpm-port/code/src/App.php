<?php

namespace App\Oop;

class App
{
    public static Config $config;

    public function __construct()
    {
        self::$config = new Config();
    }

    public function run()
    {
        return App::$config->getConfiguration()['storage']['address'];
    }
}
