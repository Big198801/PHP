<?php

namespace Myproject\Application\Application;

use Myproject\Application\Infrastructure\Storage;

class Auth
{
    public static function getPasswordHash(string $rawPassword): string
    {
        return password_hash($_GET['pass_string'], PASSWORD_BCRYPT);
    }

    public function proceedAuth(string $login, string $password): bool
    {
        $sql = "SELECT id_user, user_name, user_lastname, password_hash FROM users WHERE login = :login";

        $handler = Storage::get()->prepare($sql);

        $handler->execute([
            'login' => $login
        ]);

        $result = $handler->fetchAll();

        if (!empty($result) && password_verify($password, $result[0]['password_hash'])) {

            $_SESSION['user_name'] = $result[0]['user_name'];
            $_SESSION['user_lastname'] = $result[0]['user_lastname'];
            $_SESSION['id_user'] = $result[0]['id_user'];

            if (isset($_POST['remember'])) {
                $this->authCookie();
            }

            return true;
        } else {
            return false;
        }
    }

    public function authCookie(): void
    {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + 3600 * 24 * 7, '/');

        $sql = 'UPDATE users SET remember_token = :remember_token WHERE id_user = :id_user';

        $handler = Storage::get()->prepare($sql);

        $updateValues = [
            'remember_token' => $token,
            'id_user' => $_SESSION['id_user']
        ];

        $handler->execute($updateValues);
    }
}