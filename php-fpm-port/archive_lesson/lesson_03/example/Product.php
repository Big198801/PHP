<?php

/*
 * Есть абстрактный товар.
 * Есть цифровой товар, штучный физический товар и товар на вес.
 * У каждого есть метод подсчёта финальной стоимости.
 * У цифрового товара стоимость постоянная и дешевле штучного товара в два раза, у штучного товара обычная стоимость,
 * у весового – в зависимости от продаваемого количества в килограммах. У всех формируется в конечном итоге доход с продаж.
 * Что можно вынести в абстрактный класс, наследование?
*/

namespace example;

interface IProduct
{
    public function getFinalPrice() : float;
}

abstract class Product implements IProduct
{
    protected string $name;
    protected float $price;

    /**
     * @param string $name
     * @param float $price
     */
    public function __construct(string $name, float $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

}

class RealProduct extends Product
{

    protected int $count;

    /**
     * @param string $name
     * @param float $price
     * @param int $count
     */
    public function __construct(string $name, float $price, int $count)
    {
        parent::__construct($name, $price);
        $this->count = $count;
    }

    function getFinalPrice(): float
    {
        return $this->count * $this->price;
    }
}

class DigitalProduct extends RealProduct
{
    protected string $link;

    /**
     * @param string $name
     * @param float $price
     * @param int $count
     * @param string $link
     */
    public function __construct(string $name, float $price, int $count, string $link)
    {
        parent::__construct($name, $price, $count);
        $this->link = $link;
    }

    function getFinalPrice(): float
    {
        return parent::getFinalPrice() * 0.5;
    }
}

class WeightProduct extends Product
{
    protected float $weight;

    /**
     * @param string $name
     * @param float $price
     * @param float $weight
     */
    public function __construct(string $name, float $price, float $weight)
    {
        parent::__construct($name, $price);
        $this->weight = $weight;
    }

    function getFinalPrice(): float
    {
        return $this->weight * $this->price;
    }
}