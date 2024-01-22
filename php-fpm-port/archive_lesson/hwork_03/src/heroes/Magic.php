<?php

namespace App\Oop\Hero;

use App\Oop\Hero\Hero;

class Magic extends Hero
{
    protected int $mana;

    /**
     * @param string $name
     * @param int $hp
     * @param int $maxHp
     * @param int $mana
     */
    public function __construct(string $name, int $hp, int $maxHp, int $mana)
    {
        parent::__construct($name, $hp, $maxHp);
        $this->mana = $mana;
    }

    public function getInfo(): string
    {
        return Magic . phpprintf("%s Mana %d", parent::getInfo(), $this->mana) . PHP_EOL;
    }
}