<?php

require_once "tests/BaseTest.php";

use app\model\entities\CartItem;


class CartTest extends  BaseTest {
    
    /**
     * @dataProvider providerCategoriesController
     */
    public function testNewCartItem($params) {
        $cartItem = new CartItem($params['id']);
        $this->assertEquals($cartItem->quantity, 1);

        $product = $cartItem->product;
        $this->assertEquals($product->price, $params['price']);
    }

    public function providerCategoriesController(){
        return array (
            array (["id" => 66, 'price' => 47540]),
            array (["id" => 65, 'price' => 49800]),
            array (["id" => 70, 'price' => 57680])
        );
    }

}