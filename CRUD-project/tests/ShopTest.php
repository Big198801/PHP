<?php

class ShopTest extends PHPUnit\Framework\TestCase
{
    public function testAdd()
    {
        $x = 1;
        $y = 2;
        $this->assertEquals(3, $x + $y );
    }
    public function testSub()
    {
        $x = 5;
        $y = 2;
        $this->assertEquals(3, $x - $y);
    }

}