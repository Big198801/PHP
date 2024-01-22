<?php

namespace App\Oop\Hero;

use App\Oop\Hero\Hero;

class Priest extends Magic implements Heal
{
    function healing(Hero $target): void
    {
        $target->healed(40);
    }

}