<?php

namespace Myproject\Application\Domain\Models;

use Myproject\Application\Application\Application;
use Myproject\Application\Application\Auth;
use Myproject\Application\Infrastructure\Storage;

class User
{
    private ?int $id_user;
    private ?string $user_name;
    private ?string $user_lastname;
    private ?int $user_birthday_timestamp;
    private ?string $login;
    private ?string $password_hash;
    private ?string $remember_token;

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
    }

    public function setIdUser(?int $id_user): void
    {
        $this->id_user = $id_user;
    }

    public function getToken(): ?string
    {
        return $this->remember_token;
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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setParamsFromRequestData(string $name, string $lastname, string $date, string $login, string $password): void
    {
        $this->user_name = htmlspecialchars($name);
        $this->user_lastname = htmlspecialchars($lastname);
        $this->setUserBirthday($date);
        $this->login = htmlspecialchars($login);
        $this->password_hash = Auth::getPasswordHash($password);
        $this->remember_token = Application::$auth->generateToken();
    }

    public function getUserDataArray() : array
    {
        return [
            'id' => $this->id_user,
            'login' => $this->login,
            'user_name' => $this->user_name,
            'user_lastname' => $this->user_lastname,
            'user_birthday' => date('d-m-Y', $this->user_birthday_timestamp)
        ];
    }
}