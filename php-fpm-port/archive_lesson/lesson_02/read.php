<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
mb_internal_encoding('UTF-8');

$fileContents = file_get_contents('file.txt');
echo $fileContents;
