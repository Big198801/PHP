<?php

namespace App\Oop\Book;

class Room
{
    private int $id;
    private array $shelf;

    /**
     * @param int $id
     * @param array $shelf
     */
    public function __construct(int $id, array $shelf)
    {
        $this->id = $id;
        $this->shelf = $shelf;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShelf(): array
    {
        return $this->shelf;
    }
}