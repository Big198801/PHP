<?php

use app\engine\Application;

abstract class Facade {
    
    protected static $app;

    public static function setFacadeApplication($app) {
        self::$app = $app;
    }

    public static function __callStatic($method, $parameters) {
        if (!isset(static::$app)) {
            self::$app = new Application;
        }
        
        $instance = self::$app->make(static::getFacadeAccesor());
        if ($instance && is_callable(array($instance, $method))) {
            return call_user_func_array([$instance, $method], $parameters);
        } else {
            throw new \Exception("Ошибка, метод {$method} не найден.");
        }
         
    }

    abstract protected static function getFacadeAccesor();

}