<?php

namespace App\Oop\Book;

use App\Oop\Book\Book;

class PaperBook extends Book
{
    private bool $available;
    private string $address = 'Москва, ул.Центральная, д.1';

    /**
     * @param string $title
     * @param string $author
     * @param int $yearPublication
     * @param string $genre
     * @param int $numberPages
     */
    public function __construct(string $title, string $author, int $yearPublication, string $genre, int $numberPages)
    {
        parent::__construct($title, $author, $yearPublication, $genre, $numberPages);
        $this->available = true;
    }

    public function getInfo(): string
    {
        return parent::getInfo() . "В наличии - {$this->available}" . PHP_EOL;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function getHand(): string
    {
        $this->counterReadings();
        return $this->address;
    }
}