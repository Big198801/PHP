<?php

namespace Myproject\Application\Application;

use Myproject\Application\Domain\Models\UserRepository;
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

        $handler = Storage::getInstance()->prepare($sql);

        $handler->execute([
            'login' => $login
        ]);

        $result = $handler->fetchAll();

        if (!empty($result) && password_verify($password, $result[0]['password_hash'])) {

            $_SESSION['user_name'] = $result[0]['user_name'];
            $_SESSION['id_user'] = $result[0]['id_user'];

            if (isset($_POST['remember'])) {
                (new UserRepository())->setCookie();
            }

            return true;
        } else {
            return false;
        }
    }
}