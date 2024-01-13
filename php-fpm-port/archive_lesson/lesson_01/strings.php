<?php

$str = "{}{}{}";

echo strings . phpstrlen($str) . PHP_EOL;

$str = "Привет!";

echo strings . phpmb_substr($str, 0, 1) . PHP_EOL;
echo strings . phpmb_strlen($str) . PHP_EOL;

print_r(mb_str_split($str));