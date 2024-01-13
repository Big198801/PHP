<?php

// function readAllFunction(string $address) : string до рефакторинга
function readAllFunction(array $config): string
{
    $address = $config['storage']['address'];
    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");

        $content = '';

        while (!feof($file)) {
            $content .= fread($file, 100);
        }

        return $content;
    } else {
        return handleError("Файл не существует");
    }
}

function addFunction(array $config): string
{
    $address = $config['storage']['address'];

    $name = trim(ucfirst(readline('Введите имя: ')));
    $name .= " " . trim(ucfirst(readline('Введите фамилию: ')));

    if (!validateNameAndLastname($name)) {
        return handleError("Неверно указано имя или фамилия");
    }

    $date = trim(readline('Введите дату рождения в формате ДД-ММ-ГГГГ: '));

    if (!validateDate($date)) {
        return handleError("Некорректная дата");
    }

    $data = $name . ', ' . $date . PHP_EOL;

    $fileHandler = fopen($address, 'a');

    if (file_exists($address) && is_writable($address)) {
        if (fwrite($fileHandler, $data)) {
            fclose($fileHandler);
            return "Запись $data добавлена в файл $address";
        } else {
            fclose($fileHandler);
            return handleError("Произошла ошибка записи. Данные не сохранены");
        }
    } else {
        fclose($fileHandler);
        return handleError("В файл невозможно записать или он не существует");
    }
}

function clearFunction(array $config): string
{
    $address = $config['storage']['address'];

    if (file_exists($address) && is_writable($address)) {
        $file = fopen($address, 'w');

        fwrite($file, '');

        fclose($file);

        return "Файл очищен";
    } else {
        return handleError("Файл не существует");
    }
}

function readProfilesDirectory(array $config): string
{
    $profilesDirectoryAddress = $config['profiles']['address'];

    if (!is_dir($profilesDirectoryAddress)) {
        mkdir($profilesDirectoryAddress);
    }

    $profiles = scandir($profilesDirectoryAddress);

    $result = '';

    if (count($profiles) > 2) {
        foreach ($profiles as $file) {
            if (in_array($file, ['.', '..'])) continue;
            $result .= $file . PHP_EOL;
        }
    } else {
        $result .= "Директория пуста" . PHP_EOL;
    }

    return $result;
}

function readProfile(array $config): string
{
    $profilesDirectoryAddress = $config['profiles']['address'];

    if (!isset($_SERVER['argv'][2])) {
        return handleError("Не указан файл профиля");
    }

    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";

    if (!file_exists($profileFileName)) {
        return handleError("Файл $profileFileName не существует");
    }

    $contentJson = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);

    $info = "Имя: " . $contentArray['name'] . PHP_EOL;
    $info .= "Фамилия: " . $contentArray['lastname'] . PHP_EOL;

    return $info;
}

function helpFunction(): string
{
    return handleHelp();
}

function readConfig(string $configAddress): array|false
{
    return parse_ini_file($configAddress, true);
}

function searchTodayBirthday(array $config): string
{
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");

        $today = date('d-m');

        $result = '';

        while (!feof($file)) {
            $string = fgets($file);

            if($string == '') break;

            $date = date('d-m', strtotime(explode(", ", $string)[1]));

            if ($date === $today) {
                $result .= $string;
            }
        }

        if ($result === '') {
            rewind($file);

            $tenDaysLater = date('d-m', strtotime('+10 days'));

            while (!feof($file)) {
                $string = fgets($file);
                $date = date('d-m', strtotime(explode(", ", $string)[1]));

                if ($date > $today && $date <= $tenDaysLater) {
                    $result .= $string;
                }
            }

            fclose($file);
            return "Сегодня некого поздравлять с днем рождения, вот ближайшие:" . PHP_EOL . $result;
        } else {
            fclose($file);
            return $result;
        }
    } else {
        return handleError("Файл не существует");
    }
}

function deleteFunction(array $config): string
{
    $address = $config['storage']['address'];

    $search = trim(ucfirst(readline('Введите имя: ')));
    $search .= " " . trim(ucfirst(readline('Введите фамилию: ')));

    if (!validateNameAndLastname($search)) {
        return handleError("Неверно указано имя или фамилия");
    }

    if (file_exists($address) && is_readable($address) && is_writable($address)) {
        $file = fopen($address, "rb");

        $content = '';

        while (!feof($file)) {
            $string = fgets($file);
            if (explode(', ', $string)[0] === $search) continue;
            $content .= $string;
        }
        fclose($file);

        if ($content !== '') {
            $file = fopen($address, 'w');
            fwrite($file, $content);
            fclose($file);
        } else {
            return handleError("Указанная строка не найдена");
        }

        return "Удаление произошло успешно";
    } else {
        return handleError("Файл не существует");
    }
}