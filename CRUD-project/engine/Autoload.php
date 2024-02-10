<?php

namespace app\engine;

class Autoload
{
    public function loadClass($className)
    {
        $fileName = str_replace('\\', DS, $className);
        $fileName = str_replace('app\\', ROOT . DS, $fileName) . ".php";

            if (file_exists($fileName)) {
                include $fileName;
        }
    }
}