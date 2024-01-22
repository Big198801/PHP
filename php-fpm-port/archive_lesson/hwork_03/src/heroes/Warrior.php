<?php

namespace App\Oop\Hero;

use App\Oop\Hero\Hero;

abstract class Warrior extends Hero
{
    protected int $stamina;

    /**
     * @param string $name
     * @param int $hp
     * @param int $maxHp
     * @param int $stamina
     */
    public function __construct(string $name, int $hp, int $maxHp, int $stamina)
    {
        parent::__construct($name, $hp, $maxHp);
        $this->stamina = $stamina;
    }

    public function getInfo(): string
    {
        return Warrior . phpprintf("%s Stamina %d", parent::getInfo(), $this->stamina) . PHP_EOL;
    }
}