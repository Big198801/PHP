<?php

namespace Myproject\Application\Models;

class User
{
    private string $userName;
    private ?int $userBirthday; // ? - также может быть null

    private static string $storageAddress = '/storage/birthdays.txt';

    /**
     * @param string $userName
     * @param int|null $userBirthday
     */
    public function __construct(string $userName, ?int $userBirthday = null)
    {
        $this->userName = $userName;
        $this->userBirthday = $userBirthday;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getUserBirthday(): ?int
    {
        return $this->userBirthday;
    }

    public function setUserBirthday(string $userBirthday): void
    {
        $this->userBirthday = strtotime($userBirthday);
    }

    public static function getAllUsersFromStorage(): array|false
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        if (file_exists($address) && is_readable($address)) {
            $file = fopen($address, "r");

            $users = [];

            while (!feof($file)) {
                $userString = fgets($file);

                if ($userString == '') break;

                $userArray = explode(",", $userString);

                $user = new User(
                    $userArray[0]
                );
                $user->setUserBirthday($userArray[1]);

                $users[] = $user;
            }
            fclose($file);

            return $users;

        } else {
            return false;
        }
    }

    public function saveUserFromStorage(): string
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        $data = $this->userName . ', ' . date('d-m-Y', $this->userBirthday) . PHP_EOL;

        $fileHandler = fopen($address, 'a');

        if (file_exists($address) && is_writable($address)) {
            if (fwrite($fileHandler, $data)) {
                return "Запись добавлена в хранилище";
            } else {
                return "Произошла ошибка записи. Данные не сохранены";
            }
        } else {
            return "В файл невозможно записать или он не существует";
        }
    }

    public function deleteUserFromStorage(): string
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        $search = $this->userName;

        if (file_exists($address) && is_readable($address) && is_writable($address)) {
            $file = fopen($address, "rb");

            $content = '';

            while (!feof($file)) {
                $string = fgets($file);
                if (explode(', ', $string)[0] === $search) continue;
                $content .= $string;
            }
            fclose($file);

            $file = fopen($address, 'w');
            fwrite($file, $content);
            fclose($file);

            return "Удаление произошло успешно";
        } else {
            return "Список не найден";
        }

    }

    public static function clearUsersFromStorage(): string|false
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        if (file_exists($address) && is_writable($address)) {
            $file = fopen($address, 'w');

            fwrite($file, '');

            fclose($file);

            return "Список очищен";
        } else {
            return false;
        }
    }

    public static function searchTodayBirthday(): array|false
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        if (file_exists($address) && is_readable($address)) {
            $file = fopen($address, "r");

            $users = [];
            $today = date('Y-m-d');
            $tenDaysLater = date('Y-m-d', strtotime('+10 days'));

            $currentYear = date('Y');

            while (!feof($file)) {
                $userString = fgets($file);

                if ($userString == '') break;

                $userArray = explode(",", $userString);
                $user = new User($userArray[0]);
                $user->setUserBirthday($userArray[1]);
                $date = $currentYear . '-' . date('m-d', strtotime($userArray[1]));

                if ($date === $today || ($date > $today && $date <= $tenDaysLater)) {
                    $users[] = $user;
                }
            }

            fclose($file);

            return $users;
        } else {
            return false;
        }
    }
}