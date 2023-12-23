<?php

$counter = 0;

function incrementCounter(int &$counterInternal): int {
    $counterInternal++;

    return $counterInternal;
}

echo $counter . "\r\n";
incrementCounter($counter);
echo $counter . "\r\n";