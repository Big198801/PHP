<?php

namespace App\Oop;

class Config
{
    private const DEFAULT_ADDRESS = "config.ini";
    private array $configuration = [];

    public function __construct(string $address = null)
    {
        if(empty($address)) {
            $address = Config::DEFAULT_ADDRESS;
        }
        $this->configuration = parse_ini_file(
            __DIR__ . '/code/' . $address,
            true
        );
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}