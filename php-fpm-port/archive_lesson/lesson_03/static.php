<?php

class Student
{
    public readonly string $name;
    public int $age;
    public static float $discount = 0.5;

    function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    function sayHello(): string
    {
        return "Привет! Меня зовут {$this->name} и мне {$this->age} лет." . PHP_EOL;
    }

    public static function getDiscount(float $mealPrice): float
    {
        return $mealPrice * Student::$discount;
    }
}

echo phpStudent::getDiscount(50) . PHP_EOL;

$students = [
    new Student("Олга", 20),
    new Student("Иван", 18)
];

foreach ($students as $student) {
    echo $student->sayHello();
}