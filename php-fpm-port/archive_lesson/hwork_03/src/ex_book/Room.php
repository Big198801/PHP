<?php

namespace App\Oop\Book;

class Room
{
    private int $id;
    private array $shelf;
    private int $size;

    /**
     * @param int $id
     * @param int $size
     * @param int $startIdShelf
     * @param int $sizeShelf
     */
    public function __construct(int $id, int $size, int $startIdShelf, int $sizeShelf)
    {
        $this->id = $id;
        $this->size = $size;

        $id = $startIdShelf;
        $shelf = [];
        for ($i = 0; $i < $size; $i++) {
            $shelf = new Shelf($id++, $sizeShelf);
        }
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