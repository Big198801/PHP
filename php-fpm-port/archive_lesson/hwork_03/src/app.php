<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use App\Oop\Book\PaperBook;
use App\Oop\Book\Shelf;
use App\Oop\Book\Room;
use App\Oop;

$book1 = new PaperBook("Гарри Поттер", "Роулинг", 1997, "фентези", 432);
$book2 = new PaperBook("Гарри Поттер", "Роулинг", 1997, "фентези", 432);
$book3 = new PaperBook("Гарри Поттер", "Роулинг", 1997, "фентези", 432);
$book4 = new PaperBook("Гарри Поттер", "Роулинг", 1997, "фентези", 432);
$book5 = new PaperBook("Гарри Поттер", "Роулинг", 1997, "фентези", 432);
$book6 = new PaperBook("Гарри Поттер", "Роулинг", 1997, "фентези", 432);

$shelf = new Shelf(1, 5);

echo $shelf->addBook($book1);
echo $shelf->addBook($book2);
echo $shelf->addBook($book3);
echo $shelf->addBook($book4);
echo $shelf->addBook($book5);
echo $shelf->addBook($book6); // ошибка, шкаф полон

$room = new Room(1, [$shelf]);

class A {
    public function foo() {
        static $x = 0;
        echo ++$x;
    }
}

class B extends A {
}
$a1 = new A();
$a2 = new A();
$b1 = new B();
$a1->foo();
$a2->foo();
$a1->foo();
$a2->foo();
// 1234 статика прнадлежит классу, при каждом вызове в экземплярах класса будет увеличение на 1.
$a1->foo();
$b1->foo();
$a1->foo();
$b1->foo();
// 1122 класс B хоть и наследуется, но имеет свою статику.