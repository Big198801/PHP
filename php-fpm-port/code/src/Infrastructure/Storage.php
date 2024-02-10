<?php

namespace Myproject\Application\Infrastructure;

use Myproject\Application\Application\Application;
use \PDO;

class Storage
{
    private static ?PDO $connection = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (static::$connection === null) {
            $dsn = Application::$config->get()['db_dsn']['DSN'];
            $user = Application::$config->get()['db_user']['USER'];
            $password = Application::$config->get()['db_pass']['PASSWORD'];

            static::$connection = new PDO($dsn, $user, $password, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]);
        }
        return static::$connection;
    }
}