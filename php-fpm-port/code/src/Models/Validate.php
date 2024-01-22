<?php

namespace Myproject\Application\Models;

class Validate
{
    public static function validateDate(string $date): bool
    {
        $dateBlocks = explode("-", $date);

        if (count($dateBlocks) !== 3) {
            return false;
        }

        $day = $dateBlocks[0];
        $month = $dateBlocks[1];
        $year = $dateBlocks[2];

        $leap = $year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0;

        if (is_numeric($day) && $day > 0 && $day < 32) {
            if (in_array($month, [4, 6, 9, 11]) && $day > 30) return false;
            elseif ($leap && $month == 2 && $day > 29) return false;
            elseif (!$leap && $month == 2 && $day > 28) return false;
        } else {
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
}