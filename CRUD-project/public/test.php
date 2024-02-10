<?php
function add($x, $y)
{
    return $x + $y;
}

if (5 == add(2, 3)) {
    echo "add ok";
} else {
    echo "error add";
}