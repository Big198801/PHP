<?php

$str = "{}{}{}";

echo strlen($str) . PHP_EOL;

$str = "Привет!";

echo mb_substr($str, 0, 1) . PHP_EOL;
echo mb_strlen($str) . PHP_EOL;

print_r(mb_str_split($str));