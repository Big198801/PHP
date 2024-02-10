<?php

class ProductTest extends PHPUnit\Framework\TestCase
{
    public function testProductConstructor() {
        $name = "Чай";
        $product = new \app\models\entities\Product($name);
        $this->assertEquals($name, $product->name);
    }
}