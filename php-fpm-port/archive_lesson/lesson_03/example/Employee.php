<?php

namespace example;

class Employee
{
    private string $name;
    private int $age;
    private float $salary;

    /**
     * @param string $name
     * @param int $age
     * @param float $salary
     */
    public function __construct(string $name, int $age, float $salary)
    {
        $this->name = $name;
        $this->age = $age;
        $this->salary = $salary;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }

    public function setSalary(float $salary): void
    {
        $this->salary = $salary;
    }

    public static function getSum(Employee $emp1, Employee $emp2): float
    {
        return $emp1->salary + $emp2->salary;
    }

    public function biggerAge(Employee $emp): string
    {
        return $this->age > $emp->age ?
            "{$this->getName()} старше {$emp->getName()}" . PHP_EOL :
            "{$emp->getName()} старше {$this->getName()}" . PHP_EOL;
    }
}

$employee1 = new Employee("Олег", 25, 1000);
$employee2 = new Employee("Мария", 26, 2000);

echo Employee::getSum($employee1, $employee2);
echo $employee1->biggerAge($employee2);