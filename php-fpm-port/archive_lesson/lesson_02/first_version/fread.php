<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
mb_internal_encoding('UTF-8');

$file = fopen('file.txt', 'rb');
$data = fread($file, 100);
fclose($file);
echo $data;
