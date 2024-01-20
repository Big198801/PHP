<?php

namespace App\Oop\Hero;

use Random\RandomException;

class Team
{
    private array $team;

    /**
     * @param array $team
     */
    public function __construct(array $team = [])
    {
        $this->team = $team;
    }

    public function getTeam(): array
    {
        return $this->team;
    }
}