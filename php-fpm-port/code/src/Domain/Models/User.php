<?php

namespace Myproject\Application\Domain\Models;

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

    public function setParamsFromRequestData(string $name, string $lastname, string $date): void
    {
        $this->user_name = htmlspecialchars($name);
        $this->user_lastname = htmlspecialchars($lastname);
        $this->setUserBirthday($date);
    }
}