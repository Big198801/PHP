<?php

function handleError(string $errorText): string
{
    return "\033[31m" . $errorText . PHP_EOL . " \033[97m";
}

function handleHelp(): string
{
    $help = "Программа работы с файловым хранилищем" . PHP_EOL;

    $help .= "Порядок вызова" . PHP_EOL . PHP_EOL;

    $help .= "php /code/app.php [COMMAND]" . PHP_EOL . PHP_EOL;

    $help .= "Доступные команды:" . PHP_EOL;
    $help .= "read-all - чтение всего файла" . PHP_EOL;
    $help .= "add - добавление записи" . PHP_EOL;
    $help .= "clear - очистка файла" . PHP_EOL;
    $help .= "help - помощь" . PHP_EOL;

    return $help;
}