<?php

namespace Myproject\Application\Domain\Models;

use Myproject\Application\Infrastructure\Storage;
use \PDO;

class UserRepository
{
    private static int $lastPage = 1;
    private static int $userCount = 0;

    public function __construct()
    {
        $sql = "SELECT COUNT(*) FROM users";
        $handler = Storage::getInstance()->query($sql);

        static::$userCount = $handler->fetchColumn();
        static::$lastPage = ceil(UserRepository::$userCount / 10);
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

    public function getAllUsersFromStorage(int $currentPage): ?array
    {
        $itemsPerPage = 10;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $sql = "SELECT * FROM users ORDER BY id_user DESC LIMIT :limit OFFSET :offset";

        $handler = Storage::getInstance()->prepare($sql);
        $handler->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $handler->bindValue(':offset', $offset, PDO::PARAM_INT);
        $handler->execute();

        return $handler->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Myproject\Application\Domain\Models\User');
    }

    public function getAllUserCookie(string $cookie): ?array
    {
        if (!empty($cookie)) {
            $sql = 'SELECT * FROM users WHERE remember_token = :remember_token';

            $handler = Storage::getInstance()->prepare($sql);
            $handler->bindValue(':remember_token', $cookie);
            $handler->execute();
            return $handler->fetchAll();
        } else {
            return null;
        }
    }

    public function saveUserFromStorage(User $user): void
    {
        $sql = "INSERT INTO users(user_name, user_lastname, user_birthday_timestamp) VALUES (:user_name, :user_lastname, :user_birthday)";

        $handler = Storage::getInstance()->prepare($sql);

        $handler->execute([
            'user_name' => $user->getUserName(),
            'user_lastname' => $user->getUserLastname(),
            'user_birthday' => $user->getUserBirthday()
        ]);
    }

    public function deleteUserFromStorage(int $id_user): string
    {
        $sql = "DELETE FROM users WHERE id_user = :id_user";

        $handler = Storage::getInstance()->prepare($sql);

        $handler->execute([
            'id_user' => $id_user
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

        $handler = Storage::getInstance()->query($sql);

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

        $handler = Storage::getInstance()->prepare($sql);
        $handler->execute([
            'current_date' => $currentMonthDay,
            'ten_days_later' => $tenDaysLater
        ]);

        return $handler->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Myproject\Application\Domain\Models\User');
    }

    public function exists(int $id_user): bool
    {
        $sql = "SELECT count(id_user) as user_count FROM users WHERE id_user = :id_user";

        $handler = Storage::getInstance()->prepare($sql);
        $handler->execute([
            'id_user' => $id_user
        ]);

        $result = $handler->fetchAll();

        if (count($result) > 0 && $result[0]['user_count'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateData(string $tableName, array $userDataArray, array $whereData = []): bool
    {
        $count = count($userDataArray);
        if ($count != 0) {
            $sql = "UPDATE $tableName SET ";

            $counter = 0;
            foreach ($userDataArray as $key => $value) {
                $sql .= $key . " = :" . $key;

                if ($counter != $count - 1) {
                    $sql .= ",";
                } else if (count($whereData) == 1) {
                    $sql .= " WHERE " . key($whereData) . " = :" . key($whereData);
                    $userDataArray += $whereData;
                }
                $counter++;
            }

            $handler = Storage::getInstance()->prepare($sql);
            $handler->execute($userDataArray);

            return true;
        } else {
            return false;
        }
    }

    public function setCookie(): void
    {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + 3600 * 24 * 7, '/');

        $sql = 'UPDATE users SET remember_token = :remember_token WHERE id_user = :id_user';

        $handler = Storage::getInstance()->prepare($sql);

        $updateValues = [
            'remember_token' => $token,
            'id_user' => $_SESSION['id_user']
        ];

        $handler->execute($updateValues);
    }
}