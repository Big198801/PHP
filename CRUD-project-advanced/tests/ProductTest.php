<?php

require_once "tests/BaseTest.php";

class ProductTest extends  BaseTest {
    
    /**
     * @dataProvider providerCategoriesController
     */
    public function testReadProductById($params) {
        $this->assertEquals(Products::find($params['id'])->price, $params['price']);
    }

    public function providerCategoriesController(){
        return array (
            array (["id" => 66, 'price' => 47540]),
            array (["id" => 65, 'price' => 49800]),
            array (["id" => 70, 'price' => 57680])
        );
    }

}