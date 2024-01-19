<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
mb_internal_encoding('UTF-8');

$address = 'file.txt';

if(file_exists($address) && is_readable($address)) {
    $file = fopen($address, 'rb');

    $content = '';
    while (!feof($file)) {
        $content .= fread($file, 100);
    }
    fclose($file);
    echo $content;
} else {
    echo ("Файл невозможно открыть или он не существует");
}

