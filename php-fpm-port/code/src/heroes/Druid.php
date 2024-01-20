<?php

namespace App\Oop\Hero;

use App\Oop\Hero\Hero;

class Druid extends Magic implements Heal
{
    function healing(Hero $target): void
    {
        $target->healed(30);
    }

}