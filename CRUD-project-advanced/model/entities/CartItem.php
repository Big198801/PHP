<?php

namespace app\model\entities;

use app\model\Model;

class CartItem extends Model
{
    protected $id;
    protected $product_id;
    protected $quantity;  

    protected $props = [
            'product_id' => false,
            'quantity' => false
    ];

    // Через это свойство реализуем связь один-к-одному с другими моделями
    //  ['model' => ['fieldName', 'className', 'instance']] 
    protected $realatedModels = [
        'product' => [
            'fieldName' => 'product_id',
            'className' => '\\Products' 
            ]
    ];    
    //!!! Понимаю отлично, что быстрее джоинить. Но решил сделать с точки зрения объектной модели, когда одна ссылется на другую !!! ///

    public function __construct($product_id = null, $quantity = 1)
    {
        $this->product_id = $product_id;
        $this->quantity = $quantity;
    }


}