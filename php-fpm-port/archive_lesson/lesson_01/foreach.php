<?php

$students = [
    [
      'name' => 'Иван',
      'score' => 4.5
    ],
    [
      'name' => 'Мария',
      'score' => 5
    ],  
    [
      'name' => 'Петр',
      'score' => 3.7
    ]
  ];
  
$summ = 0;

foreach ($students as $key => $value) {
    $summ += $value['score'];
}

echo 'Средний балл: ' . $summ / count($students);
