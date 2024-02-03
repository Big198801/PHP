<?php

namespace Myproject\Application\Domain\Models;

use Myproject\Application\Infrastructure\Storage;
use \PDO;

class User
{
    private ?int $id_user;
    private ?string $user_name;
    private ?string $user_lastname;
    private ?int $user_birthday_timestamp; // ? - также может быть null

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
        $this->id_user = $userId;
        $this->user_name = $userName;
        $this->user_lastname = $userLastname;
        $this->user_birthday_timestamp = $userBirthday;

        $sql = "SELECT COUNT(*) FROM users";
        $handler = Storage::get()->query($sql);

        static::$userCount = $handler->fetchColumn();
        static::$lastPage = ceil(User::$userCount / 10);
    }

    public function getUserId(): ?int
    {
        return $this->id_user;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): void
    {
        $this->user_name = $user_name;
    }

    public function getUserLastname(): ?string
    {
        return $this->user_lastname;
    }

    public function setUserLastname(string $user_lastname): void
    {
        $this->user_lastname = $user_lastname;
    }

    public function getUserBirthday(): ?int
    {
        return $this->user_birthday_timestamp;
    }

    public function setUserBirthday(string $user_birthday_timestamp): void
    {
        $this->user_birthday_timestamp = strtotime($user_birthday_timestamp);
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
        $result = true;

        if (!(
            !empty($_POST['name']) &&
            !empty($_POST['lastname']) &&
            !empty($_POST['birthday'])
        )) {
            $result = false;
        }

        if (!preg_match('/^(\d{2}-\d{2}-\d{4})$/', $_POST['birthday']) &&
            !$this->validateDate($_POST['birthday'])) {
            $result = false;
        }

        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] != $_POST['csrf_token']) {
            $result = false;
        }

        return $result;
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
        $this->user_name = htmlspecialchars($_POST['name']);
        $this->user_lastname = htmlspecialchars($_POST['lastname']);
        $this->setUserBirthday($_POST['birthday']);
    }

    public function getAllUsersFromStorage(int $currentPage): ?array
    {
        $itemsPerPage = 10;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $sql = "SELECT * FROM users ORDER BY id_user DESC LIMIT :limit OFFSET :offset";

        $handler = Storage::get()->prepare($sql);
        $handler->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $handler->bindValue(':offset', $offset, PDO::PARAM_INT);
        $handler->execute();

        return $handler->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Myproject\Application\Domain\Models\User');
    }

    public function saveUserFromStorage(): void
    {
        $sql = "INSERT INTO users(user_name, user_lastname, user_birthday_timestamp) VALUES (:user_name, :user_lastname, :user_birthday)";

        $handler = Storage::get()->prepare($sql);

        $handler->execute([
            'user_name' => $this->user_name,
            'user_lastname' => $this->user_lastname,
            'user_birthday' => $this->user_birthday_timestamp
        ]);
    }

    public function deleteUserFromStorage(): string
    {
        $sql = "DELETE FROM users WHERE id_user = :id_user";

        $handler = Storage::get()->prepare($sql);

        $handler->execute([
            'id_user' => $this->id_user
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
        $sql = "DELETE FROM users";

        $handler = Storage::get()->query($sql);

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

        $handler = Storage::get()->prepare($sql);
        $handler->execute([
            'current_date' => $currentMonthDay,
            'ten_days_later' => $tenDaysLater
        ]);

        return $handler->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Myproject\Application\Domain\Models\User');
    }

    public function updateUserFromStorage(): string
    {
        $updateFields = array();
        $updateValues = array();

        if (!empty($this->user_name)) {
            $updateFields[] = 'user_name = :user_name';
            $updateValues['user_name'] = $this->user_name;
        }

        if (!empty($this->user_lastname)) {
            $updateFields[] = 'user_lastname = :user_lastname';
            $updateValues['user_lastname'] = $this->user_lastname;
        }

        if (!empty($this->user_birthday_timestamp)) {
            $updateFields[] = 'user_birthday_timestamp = :user_birthday';
            $updateValues['user_birthday'] = $this->user_birthday_timestamp;
        }

        if (!empty($updateFields)) {
            $sql = 'UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id_user = :id_user';

            $handler = Storage::get()->prepare($sql);

            $updateValues['id_user'] = $this->id_user;
            $handler->execute($updateValues);

            return true;
        } else {
            return false;
        }
    }
}