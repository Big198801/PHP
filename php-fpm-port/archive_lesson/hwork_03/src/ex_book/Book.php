<?php

namespace App\Oop\Book;

abstract class Book
{
    protected string $title;
    protected string $author;
    protected int $yearPublication;
    protected string $genre;
    protected int $numberPages;
    protected int $countRead = 0;

    /**
     * @param string $title
     * @param string $author
     * @param int $yearPublication
     * @param string $genre
     * @param int $numberPages
     */
    public function __construct(string $title, string $author, int $yearPublication, string $genre, int $numberPages)
    {
        $this->title = $title;
        $this->author = $author;
        $this->yearPublication = $yearPublication;
        $this->genre = $genre;
        $this->numberPages = $numberPages;
    }

    public function getInfo(): string
    {
        $info = 'КНИГА: ';
        $info .= "Название - {$this->title}," . PHP_EOL;
        $info .= "Автор - {$this->author}," . PHP_EOL;
        $info .= "Год публикации - {$this->yearPublication}," . PHP_EOL;
        $info .= "Жанр - {$this->genre}," . PHP_EOL;
        $info .= "Колличество страниц - {$this->numberPages}," . PHP_EOL;
        return $info;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function setYearPublication(int $yearPublication): void
    {
        $this->yearPublication = $yearPublication;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function setNumberPages(int $numberPages): void
    {
        $this->numberPages = $numberPages;
    }

    // 
    public function counterReadings(): void
    {
        $this->countRead++;
    }

    public abstract function getHand();
}