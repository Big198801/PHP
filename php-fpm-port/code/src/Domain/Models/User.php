<?php

namespace Myproject\Application\Domain\Models;

use Myproject\Application\Application\Application;
use Myproject\Application\Infrastructure\Storage;
use \PDO;

class User
{
    private ?int $userId;
    private ?string $userName;
    private ?string $userLastname;
    private ?int $userBirthday; // ? - также может быть null

    private static int $lastPage = 1;
    private static int $userCount = 0;

    /**
     * @param int|null $userId
     * @param string|null $userName
     * @param string|null $userLastname
     * @param int|null $userBirthday
     */
    public function __construct(
        ?int    $userId = null,
        ?string $userName = null,
        ?string $userLastname = null,
        ?int    $userBirthday = null)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userLastname = $userLastname;
        $this->userBirthday = $userBirthday;

        $sql = "SELECT COUNT(*) FROM users";
        $handler = Application::$storage->get()->query($sql);

        static::$userCount = $handler->fetchColumn();
        static::$lastPage = ceil(User::$userCount / 10);
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getUserLastname(): ?string
    {
        return $this->userLastname;
    }

    public function setUserLastname(string $userLastname): void
    {
        $this->userLastname = $userLastname;
    }

    public function getUserBirthday(): ?int
    {
        return $this->userBirthday;
    }

    public function setUserBirthday(string $userBirthday): void
    {
        $this->userBirthday = strtotime($userBirthday);
    }

    public function generatePageNumbers(int $currentPage): array
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

    public function validateRequestData(): bool
    {
        if (
            !empty($_GET['name']) &&
            !empty($_GET['lastname']) &&
            !empty($_GET['birthday']) &&
            $this->validateDate($_GET['birthday'])
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function validateDate(string $date): bool
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

    public function setParamsFromRequestData(): void
    {
        $this->userName = $_GET['name'];
        $this->userLastname = $_GET['lastname'];
        $this->setUserBirthday($_GET['birthday']);
    }

    public function getAllUsersFromStorage(int $currentPage): ?array
    {
        $itemsPerPage = 10;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $sql = "SELECT * FROM users ORDER BY id_user DESC LIMIT :limit OFFSET :offset";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $handler->bindValue(':offset', $offset, PDO::PARAM_INT);
        $handler->execute();

        $result = $handler->fetchAll();
        $users = [];

        foreach ($result as $item) {
            $user = new User(
                $item['id_user'],
                $item['user_name'],
                $item['user_lastname'],
                $item['user_birthday_timestamp']
            );
            $users[] = $user;
        }

        return $users;
    }

    public function saveUserFromStorage(): void
    {
        $storage = new Storage();

        $sql = "INSERT INTO users(user_name, user_lastname, user_birthday_timestamp) VALUES (:user_name, :user_lastname, :user_birthday)";

        $handler = $storage->get()->prepare($sql);

        $handler->execute([
            'user_name' => $this->userName,
            'user_lastname' => $this->userLastname,
            'user_birthday' => $this->userBirthday
        ]);
    }

    public function deleteUserFromStorage(): string
    {
        $storage = new Storage();

        $sql = "DELETE FROM users WHERE id_user = :id_user";

        $handler = $storage->get()->prepare($sql);

        $handler->execute([
            'id_user' => $this->userId
        ]);

        $rowCount = $handler->rowCount();

        if ($rowCount === 0) {
            return "Запись не существует";
        } else {
            return "Запись удалена успешно";
        }
    }

    public function clearUsersFromStorage(): string
    {
        $storage = new Storage();

        $sql = "DELETE FROM users";

        $handler = $storage->get()->prepare($sql);

        $handler->execute();

        return "База очищена";
    }

    public function searchTodayBirthday(): ?array
    {
        $currentMonthDay = date('m-d');
        $tenDaysLater = date('Y-m-d', strtotime('+10 days'));

        $sql = "SELECT * FROM users 
            WHERE DATE_FORMAT(FROM_UNIXTIME(user_birthday_timestamp), '%m-%d') 
            BETWEEN :current_date AND :ten_days_later
            ORDER BY DATE_FORMAT(FROM_UNIXTIME(user_birthday_timestamp), '%m-%d') ASC";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'current_date' => $currentMonthDay,
            'ten_days_later' => $tenDaysLater
        ]);

        $result = $handler->fetchAll();

        $users = [];

        foreach ($result as $item) {
            $user = new User(
                $item['id_user'],
                $item['user_name'],
                $item['user_lastname'],
                $item['user_birthday_timestamp']
            );
            $users[] = $user;
        }

        return $users;
    }

    public function updateUserFromStorage(): string
    {
        $storage = new Storage();

        $updateFields = array();
        $updateValues = array();

        if (!empty($this->userName)) {
            $updateFields[] = 'user_name = :user_name';
            $updateValues['user_name'] = $this->userName;
        }

        if (!empty($this->userLastname)) {
            $updateFields[] = 'user_lastname = :user_lastname';
            $updateValues['user_lastname'] = $this->userLastname;
        }

        if (!empty($this->userBirthday)) {
            $updateFields[] = 'user_birthday_timestamp = :user_birthday';
            $updateValues['user_birthday'] = $this->userBirthday;
        }

        if (!empty($updateFields)) {
            $sql = 'UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id_user = :id_user';

            $handler = $storage->get()->prepare($sql);

            $updateValues['id_user'] = $this->userId;
            $handler->execute($updateValues);

            return true;
        } else {
            return false;
        }
    }
}