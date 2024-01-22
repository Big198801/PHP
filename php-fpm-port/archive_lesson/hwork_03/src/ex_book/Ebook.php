<?php

namespace App\Oop\Book;

use App\Oop\Book\Book;

class Ebook extends Book
{
    private float $sizeFile;
    private string $download;

    /**
     * @param string $title
     * @param string $author
     * @param int $yearPublication
     * @param string $genre
     * @param int $numberPages
     * @param float $sizeFile
     */
    public function __construct(float $sizeFile, string $title, string $author, int $yearPublication, string $genre, int $numberPages)
    {
        parent::__construct($title, $author, $yearPublication, $genre, $numberPages);
        $this->sizeFile = $sizeFile;
        $this->download = "/download/{$this->title}";
    }

    public function getInfo(): string
    {
        return parent::getInfo() . "Размер файла - {$this->sizeFile}" . PHP_EOL;
    }

    public function setDownload(string $download): void
    {
        $this->download = "/download/{$this->title}";
    }

    public function setSizeFile(float $sizeFile): void
    {
        $this->sizeFile = $sizeFile;
    }

    public function downloadBook(): void
    {
        echo "Книга '{$this->title}' размером {$this->sizeFile} скачена успешно";
    }

    public function getHand(): string
    {
        $this->counterReadings();
        return $this->download;
    }
}