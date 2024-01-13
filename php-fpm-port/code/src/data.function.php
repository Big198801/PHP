<?php

function validateDate(string $date): bool
{
    $dateBlocks = explode("-", $date);

    if (count($dateBlocks) !== 3) {
        return false;
    }
    // Как в js, возвращает значение первого false
    $day = isset($dateBlocks[0]) && $dateBlocks[0];
    $month = isset($dateBlocks[1]) && $dateBlocks[1];
    $year = isset($dateBlocks[2]) && $dateBlocks[2];

    if (!is_numeric($day) || $day < 1 || $day > 31) {
        return false;
    }

    if (!is_numeric($month) || $month < 1 || $month > 12) {
        return false;
    }

    if (!is_numeric($year) || $year < 1900 || $year > date('Y')) {
        return false;
    }

    return true;
}

function validateNameAndLastname(string $string): bool
{
    $length = mb_strlen($string, 'UTF-8');
    $count = count(explode(" ", $string));

    if ($length !== 0 && $length <= 100 && $count === 2) {
        return true;
    } else {
        return false;
    }
}