<?php

namespace App\Oop\Book;

use App\Oop\Book\Book;

class AudioBook extends Book
{
    private float $time;
    private string $audio;

    /**
     * @param string $title
     * @param string $author
     * @param int $yearPublication
     * @param string $genre
     * @param int $numberPages
     * @param float $time
     */
    public function __construct(float $time, string $title, string $author, int $yearPublication, string $genre, int $numberPages)
    {
        parent::__construct($title, $author, $yearPublication, $genre, $numberPages);
        $this->time = $time;
        $this->audio = "/audio/{$this->title}";
    }

    public function getInfo(): string
    {
        return parent::getInfo() . "Длительность - {$this->time}" . PHP_EOL;
    }

    public function setDownload(string $download): void
    {
        $this->audio = "/audio/{$this->title}";
    }

    public function setTime(float $time): void
    {
        $this->time = $time;
    }

    public function play(): void
    {
        $timer = 0.0;

        while ($timer < $this->time) {
            echo $timer . PHP_EOL;
            $timer += 0.1;
            usleep(1000000);
        }
    }

    public function getHand(): string
    {
        $this->counterReadings();
        return $this->audio;
    }
}