<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
mb_internal_encoding('UTF-8');

$address = 'file.txt';

$name = readline('Введите имя:');
$date = readline('Введите дату рождения в формате ДД-ММ-ГГГГ: ');

$data = $name . ', ' . $date . "\r\n";

$fileHandler = fopen($address, 'a');

if(file_exists($address) && is_writable($address)) {
    if(fwrite($fileHandler, $data)) {
        echo "Запись $data добавлена в файл $address";
    } else {
        echo "Произошла ошибка записи. Данные не сохранены";
    }
    fclose($fileHandler);
} else {
    echo ("В файл невозможно записать или он не существует");
}
