<?php

namespace Myproject\Application\Models;

class User
{
    private ?string $userName;
    private ?int $userBirthday; // ? - также может быть null

    private static int $lastPage = 1;
    private static int $userCount = 0;
    private static string $storageAddress = '/storage/birthdays.txt';

    /**
     * @param string|null $userName
     * @param int|null $userBirthday
     */
    public function __construct(?string $userName = null, ?int $userBirthday = null)
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

    public function generatePageNumbers(int $currentPage) : array
    {
        $pageNumbers = array();

        $middleNumberIndex = 2;
        $startNumber = $currentPage - $middleNumberIndex;
        $endNumber = $currentPage + $middleNumberIndex;

        if ($startNumber < 1) {
            $endNumber += abs($startNumber) + 1;
            $startNumber = 1;
        }
        if ($endNumber > static::$lastPage) {
            $startNumber -= $endNumber - static::$lastPage;
            $endNumber = static::$lastPage;
            if ($startNumber < 1) {
                $startNumber = 1;
            }
        }

        for ($i = $startNumber; $i <= $endNumber; $i++) {
            $pageNumbers[] = $i;
        }

        return $pageNumbers;
    }

    public function getAllUsersFromStorage(int $currentPage): ?array
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        if (file_exists($address) && is_readable($address)) {
            $file = fopen($address, "r");

            User::$userCount = count(file($address));
            User::$lastPage = ceil(User::$userCount / 10);

            $users = [];

            $maxLineCount = $currentPage * 10;
            $lineCount = $maxLineCount - 10;
            $currentLine = 0;

            while (!feof($file)) {
                $userString = fgets($file);

                if ($userString == '') break;

                $currentLine++;
                if ($currentLine <= $lineCount) continue;
                if ($currentLine > $maxLineCount) break;

                $userArray = explode(",", $userString);

                $user = new User(
                    $userArray[0]
                );
                $user->setUserBirthday($userArray[1]);

                $users[] = $user;

                $lineCount++;
            }
            fclose($file);

            return $users;

        } else {
            return null;
        }
    }

    public function saveUserFromStorage(): string
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        $data = $this->userName . ', ' . date('d-m-Y', $this->userBirthday) . PHP_EOL;

        $fileHandler = fopen($address, 'a');

        if (!isset($this->userName) &&
            !isset($this->userBirthday) &&
            !Validate::validateDate($this->userBirthday)) {
            return 'Ошибка в запросе';
        }

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

        if (!isset($this->userName)) {
            return 'Ошибка в запросе';
        }

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

    public function clearUsersFromStorage(): string|false
    {
        $address = $_SERVER['DOCUMENT_ROOT'] . User::$storageAddress;

        if (file_exists($address) && is_writable($address)) {
            $file = fopen($address, 'w');

            fwrite($file, '');

            fclose($file);

            return "Список очищен";
        } else {
            return "Список не найден";
        }
    }

    public function searchTodayBirthday(): ?array
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
            return null;
        }
    }
}