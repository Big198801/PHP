<?php
$n = 2;

if ($n == 1) echo 1;
if ($n == 2) echo 2;
if ($n == 3) echo 3;
if ($n == 4) echo 4;

switch ($n) {
    case 1:
        echo 1;
        break;
    case 2:
        echo 2;
        break;
    case 3:
        echo 3;
        break;
    case 4:
        echo 4;
        break;
    default:
        echo "error";
}

echo match ($n) {
    1 => 1,
    2 => 2,
    3 => 3,
    4 => 4,
    default => "error",
};

