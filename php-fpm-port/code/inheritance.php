<?php

abstract class Person
{
    protected string $name;
    protected array $access = [];
    protected static string $writableName = "Персона";

    /**
     * @param string $name
     * @param array $access
     */
    public function __construct(string $name, array $access = [])
    {
        $this->name = $name;
        $this->access = $access;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if ($this->validateName($name)) {
            $this->name = $name;
        }
    }

    /** не даст переопределить */
    public final function checkAccess(string $room): bool
    {
        return in_array($room, $this->access);
    }

    abstract public function goToLunch(): string;

    public function whoAmI(): string
    {
        return "Я - " . static::$writableName;
    }

    private function validateName(string $name): bool
    {
        return true;
    }
}

class Teacher extends Person
{
    protected static string $writableName = "Учитель";

    public function __construct(string $name)
    {
        parent::__construct($name, ["classroom", "teachersroom"]);
    }

    public function goToLunch(): string
    {
        return "Учитель любит ходить в диетическую столовую";
    }

    public function guideLecture()
    {
    }
}

class Pupil extends Person
{
    protected static string $writableName = "Школьник";

    public function __construct(string $name)
    {
        parent::__construct($name, ["classroom"]);
    }

    public function goToLunch(): string
    {
        return "Студенту важна экономия!";
    }
}

$student = new Pupil("Иван");
$teacher = new Teacher("Ольга Ивановна");

var_dump($student->checkAccess("teachersroom")); // false
var_dump($teacher->checkAccess("teachersroom")); // true

var_dump($student->whoAmI());
var_dump($teacher->whoAmI());

$persons = [
    new Pupil("Олег"),
    new Teacher("Мария Ивановна")
];

foreach ($persons as $person) {
    if ($person instanceof Person) echo $person->goToLunch();
}