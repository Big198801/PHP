<?php

namespace app\model\entities;

use app\model\Model;

class Order extends Model
{
    protected $id;
    protected $date;
    protected $userId;
    protected $status;
    protected $name;
    protected $phone;
    protected $address;
    protected $uId;



    protected $props = [
        'date' => false,
        'userId' => false,
        'status' => false,
        'name' => false,
        'phone' => false,
        'address' => false,
        'uId' => false       
    ];

    protected $protectedProps = [
        'uId',
        'date',
    ];

    /*
    protected $realatedModels = [
        'user' => [
            'fieldName' => 'userId',
            'className' => '\\Users' 
            ]
    ];  
    */

    protected function fillProtectedProps() {
        $this->date = date("Y-m-d H:i:s" , time());
        $this->uId = uniqid(rand(), true); 
    }

    public function __construct($name = null, $phone = null, $address = null, $userId = null, $status = 'Новый') 
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->address = $address;
        $this->userId = $userId;
        $this->status = $status;
        $this->fillProtectedProps();
    }

}