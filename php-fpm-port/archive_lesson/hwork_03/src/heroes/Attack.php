<?php

namespace App\Oop\Hero;

use App\Oop\Hero\Hero;

interface Attack
{
    function attack(Hero $target);
}