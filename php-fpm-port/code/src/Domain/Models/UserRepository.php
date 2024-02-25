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

    public function getAllUsersFromStorage(int $currentPage, ?int $limit = null): ?array
    {
        $itemsPerPage = 10;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $sql = "SELECT * FROM users";

        if (isset($limit) && $limit > 0) {
            $sql .= " WHERE id_user > " . (int)$limit;
            $handler = Storage::getInstance()->prepare($sql);
        } else {
            $sql .= " ORDER BY id_user DESC LIMIT :limit OFFSET :offset";
            $handler = Storage::getInstance()->prepare($sql);
            $handler->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
            $handler->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $handler->execute();

        return $handler->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Myproject\Application\Domain\Models\User');
    }

    public function saveUserFromStorage(User $user): void
    {
        $sql = "INSERT INTO users(user_name, user_lastname, user_birthday_timestamp, login, password_hash, remember_token) VALUES (:user_name, :user_lastname, :user_birthday, :login, :password, :token)";

        $handler = Storage::getInstance()->prepare($sql);

        $handler->execute([
            'user_name' => $user->getUserName(),
            'user_lastname' => $user->getUserLastname(),
            'user_birthday' => $user->getUserBirthday(),
            'login' => $user->getLogin(),
            'password' => $user->getPasswordHash(),
            'token' => $user->getRememberToken()
        ]);
    }

    public function deleteUserFromStorage(int $id_user): string
    {
        if ($id_user === $_SESSION['auth']['id_user']) {
            return "Ты что? Себя удалить нельзя";
        }

        $sql = "DELETE FROM users WHERE id_user = :id_user";

        $handler = Storage::getInstance()->prepare($sql);

        $handler->execute([
            'id_user' => $id_user
        ]);

        return "Запись удалена успешно";
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

    public function getUserDataByID(int $userID): array {
        $sql = "SELECT * FROM users WHERE id_user = :id";

        $handler = Storage::getInstance()->prepare($sql);
        $handler->execute(['id' => $userID]);
        return $handler->fetch();
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

    public static function destroyToken(): array
    {
        $userSql = "UPDATE users SET remember_token = :token WHERE id_user = :id";

        $handler = Storage::getInstance()->prepare($userSql);
        $handler->execute(['token' => md5(bin2hex(random_bytes(16))), 'id' => $_SESSION['auth']['id_user']]);
        $result = $handler->fetchAll();

        return $result[0] ?? [];
    }

    public static function verifyToken(string $token): array
    {
        $userSql = "SELECT * FROM users WHERE remember_token = :token";

        $handler = Storage::getInstance()->prepare($userSql);
        $handler->execute(['token' => $token]);
        $result = $handler->fetchAll();

        return $result[0] ?? [];
    }

    public static function setToken(int $userID, string $token): void
    {
        $userSql = "UPDATE users SET remember_token = :token WHERE id_user = :id";

        $handler = Storage::getInstance()->prepare($userSql);
        $handler->execute(['id' => $userID, 'token' => $token]);

        setcookie('auth_token', $token, time() + 60 * 60 * 24 * 30, '/');
    }

    public function getUserRoles(): array
    {
        $roles = [];

        if (isset($_SESSION['auth']['id_user'])) {
            $rolesSql = "SELECT * FROM user_roles WHERE id_user = :id";

            $handler = Storage::getInstance()->prepare($rolesSql);
            $handler->execute(['id' => $_SESSION['auth']['id_user']]);
            $result = $handler->fetchAll();

            if (!empty($result)) {
                foreach ($result as $role) {
                    $roles[] = $role['role'];
                }
                $_SESSION['auth']['hasAccess'] = $roles;
            }
        } else {
            $roles[] = 'user';
        }
        return $roles;
    }
}