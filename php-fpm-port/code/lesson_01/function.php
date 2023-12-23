<?php

$x = 1;

function inc(&$x) {
    $x++;
}

inc($x);
echo $x . PHP_EOL;



function add(int $x = 0, int $y = 0):int|string {

    return $x + $y;
}

$add = fn($x, $y) => $x + $y;

$result = add(2,2);

echo $add(2,3) . PHP_EOL;
echo $result . PHP_EOL;