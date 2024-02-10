<?php

namespace app\engine;

class Autoload
{

    public function loadClass($className) {
        //Сначала ищем в фасадах
        if (strpos($className,'\\') === false) {
            $fileName = FACADE_DIR . DS . $className . ".php";
        } else 
        {
            $fileName = str_replace(['app', '\\'], [ROOT_DIR, DS], $className) . ".php";
        }
        if (file_exists($fileName)) {
            include $fileName;
        }        
    }

}