<?php

namespace App\Oop\Hero;

use App\Oop\Hero\Hero;
use Random\RandomException;

class Paladin extends Magic implements Heal, Attack
{
    /**
     * @throws RandomException
     */
    function attack(Hero $target): void
    {
        $target->GetDamage(random_int(20, 30));
    }

    function healing(Hero $target): void
    {
        $target->healed(40);
    }

}