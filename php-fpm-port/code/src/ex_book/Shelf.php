<?php

namespace App\Oop\Book;
class Shelf
{
    private int $id;
    private int $size;
    private array $books;

    /**
     * @param int $id
     * @param int $size
     */
    public function __construct(int $id, int $size)
    {
        $this->id = $id;
        $this->size = $size;
        $this->books = array_fill(0, $size, "пусто");
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBooks(): array
    {
        return $this->books;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function addBook(PaperBook $newBook): string
    {
        $index = array_search("пусто", $this->books);
        if ($index !== false) {
            $this->books[$index] = $newBook;
            return "{$newBook->getInfo()}ДОБАВЛЕНА" . PHP_EOL;
        }
        return "ОШИБКА - шкаф заполнен" . PHP_EOL;
    }
}