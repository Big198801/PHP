<?php

include "config/const.php";

use app\engine\Application;

abstract class BaseTest extends \PHPUnit\Framework\TestCase {
    
    protected function setUp():void {
        $app = new Application();
        Facade::setFacadeApplication($app);
        include "config/binding.php";
    }


}