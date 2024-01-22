<?php

namespace App\Oop\Hero;
abstract class Hero
{
    protected string $status = Status::ALIVE;
    protected string $name;
    protected int $hp;
    protected int $maxHp;

    /**
     * @param string $name
     * @param int $hp
     * @param int $maxHp
     */
    public function __construct(string $name, int $hp, int $maxHp)
    {
        $this->name = $name;
        $this->hp = $hp > 0 ? $hp : 100;
        $this->maxHp = $maxHp > $hp && $maxHp > 0 ? $maxHp : 100;
    }

    public function getInfo(): string
    {
        return printf("Name: %s HP: %d Type: %s", $this->name, $this->hp, static::class);
    }

    public function healed(int $hp): void
    {
        $this->hp = ($hp + $this->hp) > $this->maxHp ? $this->maxHp : $hp + $this->hp;
    }

    public function GetDamage(int $damage): void
    {
        if ($this->hp - $damage > 0) $this->hp -= $damage;
        else $this->status = Status::DEAD;
    }
}