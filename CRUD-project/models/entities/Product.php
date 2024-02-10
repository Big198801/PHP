<?php
namespace app\models\entities;


use app\models\Model;

class Product extends Model
{
    protected $id;
    protected $name;
    protected $description;
    protected $price;

    protected $props = [
        'name' => false,
        'description' => false,
        'price' => false
    ];


    public function __construct($name = null, $description = null, $price = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }


}