<?php

namespace app\model\entities;

use app\model\Model;

class OrderItem extends Model
{
    protected $id;
    protected $orderId;
    protected $productId;
    protected $quantity;
    protected $price;


    protected $props = [
            'orderId' => false,
            'productId' => false,
            'quantity' => false,
            'price' => false,
    ];

    // Через это свойство реализуем связь один-к-одному с другими моделями
    //  ['model' => ['fieldName', 'className', 'instance']] 
    protected $realatedModels = [
        'product' => [
            'fieldName' => 'productId',
            'className' => '\\Products' 
        ],  
    ];    
    //!!! Понимаю отлично, что быстрее джоинить. Но решил сделать с точки зрения объектной модели, когда одна ссылется на другую !!! ///

    public function __construct($orderId = null, $productId = null, $quantity = 1, $price = null)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
    }


}