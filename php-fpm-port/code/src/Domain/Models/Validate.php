<?php

namespace Myproject\Application\Domain\Models;

use Myproject\Application\Application\Application;

class Validate
{
    public function validateRequestData(array $requestData): bool
    {
        $validFields = 0;

        foreach ($requestData as $fieldName => $fieldValue) {
            if ($fieldName === 'name' || $fieldName === 'lastname') {
                if ($this->validateNameOrLastname($fieldValue)) {
                    $validFields++;
                } else {
                    $logMessage = 'При добавлении пользователя неверно указали Имя или Фамилию';
                    $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    throw new \Exception("Неверное Имя или Фамилия");
                }
            } elseif ($fieldName === 'birthday') {
                if ($this->validateDate($fieldValue)) {
                    $validFields++;
                } else {
                    $logMessage = 'При добавлении пользователя неверно указали Дату рождения';
                    $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    throw new \Exception("Неверная Дата рождения");
                }
            } elseif ($fieldName === 'login') {
                if ($this->validateLogin($fieldValue)) {
                    $validFields++;
                } else {
                    $logMessage = 'При добавлении пользователя неверно указали Логин';
                    $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    throw new \Exception("Неверный Логин");
                }
            } elseif ($fieldName === 'password') {
                if ($fieldValue[0] === $fieldValue[1]) {
                    if ($this->validatePassword($fieldValue[0])) {
                        $validFields++;
                    } else {
                        $logMessage = 'При добавлении пользователя неверно указали Пароль';
                        $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                        Application::$logger->error($logMessage);
                        throw new \Exception("Неверный пароль");
                    }
                } else {
                    $logMessage = 'При добавлении пользователя пароли не совпали';
                    $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    throw new \Exception("Пароли не совпадают");
                }
            }
        }

        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] != $_POST['csrf_token']) {
            return false;
        }

        return $validFields === count($requestData);
    }

    public function validateUserData(string $login, string $name, string $lastname, string $birthday): array
    {
        $validatedData = [];

        if ($this->validateLogin($login)) {
            $validatedData['login'] = $login;
        }

        if ($this->validateNameOrLastname($name)) {
            $validatedData['user_name'] = $name;
        }

        if ($this->validateNameOrLastname($lastname)) {
            $validatedData['user_lastname'] = $lastname;
        }

        if ($this->validateDate($birthday)) {
            $validatedData['user_birthday_timestamp'] = strtotime($birthday);
        }

        return $validatedData;
    }

    private function validateLogin(string $data): bool
    {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return (!empty($data) && preg_match("/^[a-zA-Z0-9_-]{3,20}$/", $data) && !preg_match("/<[^>]*>/", $data));
    }

    private function validateNameOrLastname(string $data): bool
    {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return (!empty($data) && preg_match("/^[-A-Za-zА-Яа-яЁё]+$/u", $data) && !preg_match("/<[^>]*>/", $data));
    }

    private function validateDate(string $date): bool
    {
        if (empty($date) && !preg_match('/^(\d{2}-\d{2}-\d{4})$/', $date)) {
            return false;
        }

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

    private function validatePassword(string $password): bool
    {
        $pattern = "/^(?=.*\d)(?=.*[A-Za-z])(?=.*[^\s\w])(^\S{8,16})$/";
        return (!empty($password) && preg_match($pattern, $password));
    }
}