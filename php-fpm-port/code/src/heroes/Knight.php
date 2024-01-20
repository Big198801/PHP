<?php

namespace App\Oop\Hero;

use App\Oop\Hero\Hero;
use Random\RandomException;

class Knight extends Warrior implements Attack
{

    /**
     * @throws RandomException
     */
    function attack(Hero $target): void
    {
        $target->GetDamage(random_int(20, 40));
    }
}