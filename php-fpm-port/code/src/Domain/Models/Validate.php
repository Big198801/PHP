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
                    return false;
                }
            } elseif ($fieldName === 'birthday') {
                if ($this->validateDate($fieldValue)) {
                    $validFields++;
                } else {
                    $logMessage = 'При добавлении пользователя неверно указали Дату рождения';
                    $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    $_SESSION['error_message'] = 'Неверная Дата рождения';
                    return false;
                }
            } elseif ($fieldName === 'login') {
                if ($this->validateLogin($fieldValue)) {
                    $validFields++;
                } else {
                    return false;
                }
            } elseif ($fieldName === 'password') {
                if ($fieldValue[0] === $fieldValue[1]) {
                    if ($this->validatePassword($fieldValue[0])) {
                        $validFields++;
                    } else {
                        return false;
                    }
                } else {
                    $logMessage = 'Проверка Пароля: Пароли не равны' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    $_SESSION['error_message'] = 'Пароль: пароли не равны';
                    return false;
                }
            }
        }

        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] != $_POST['csrf_token']) {
            $logMessage = 'При добавлении пользователя не совпал csrf-token ' . $_SESSION['csrf_token'] . " " . $_POST['csrf_token'];
            $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
            Application::$logger->error($logMessage);
            $_SESSION['error_message'] = 'Не совпал csrf-token';
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
        if (!empty($data)) {
            if (preg_match("/[a-zA-Z0-9_-]/", $data)) {
                if (preg_match("/^\S{3,20}$/", $data)) {
                    if (!preg_match("/<[^>]*>/", $data)) {
                        return true;
                    } else {
                        $logMessage = 'Проверка логина: передаете тэги' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                        Application::$logger->error($logMessage);
                        $_SESSION['error_message'] = 'Логин: не соотевтсвие длины';
                        return false;
                    }
                } else {
                    $logMessage = 'Проверка логина: не соотевтсвие длины' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    $_SESSION['error_message'] = 'Логин: не соотевтсвие длины';
                    return false;
                }
            } else {
                $logMessage = 'Проверка логина: неверные знаки' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                Application::$logger->error($logMessage);
                $_SESSION['error_message'] = 'Логин: отсутствие букв';
                return false;
            }
        } else {
            $logMessage = 'Проверка логина: пустое значение' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
            Application::$logger->error($logMessage);
            $_SESSION['error_message'] = 'Логин: пусто';
            return false;
        }
    }

    private function validateNameOrLastname(string $data): bool
    {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        if (!empty($data)) {
            if (preg_match("/[-A-Za-zА-Яа-яЁёЖж]+$/", $data)) {
                if (!preg_match("/<[^>]*>/", $data)) {
                    return true;
                } else {
                    $logMessage = 'Проверка ФИО: передаете тэги' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    $_SESSION['error_message'] = 'ФИО: не соотевтсвие длины';
                    return false;
                }
            } else {
                $logMessage = 'Проверка ФИО: неверные знаки' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                Application::$logger->error($logMessage);
                $_SESSION['error_message'] = 'ФИО: отсутствие букв';
                return false;
            }
        } else {
            $logMessage = 'Проверка ФИО: пустое значение' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
            Application::$logger->error($logMessage);
            $_SESSION['error_message'] = 'ФИО: пусто';
            return false;
        }
    }

    private function validateDate(string $date): bool
    {
        if (!empty($date)) {
            if (preg_match('/^(\d{2}-\d{2}-\d{4})$/', $date)) {

                $dateBlocks = explode("-", $date);
                if (count($dateBlocks) === 3) {
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
                } else {
                    $logMessage = 'Проверка даты: неферный формат' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    $_SESSION['error_message'] = 'Дата неферный формат';
                    return false;
                }
            } else {
                $logMessage = 'Проверка даты: неверные знаки' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                Application::$logger->error($logMessage);
                $_SESSION['error_message'] = 'Дата: неверные знаки';
                return false;
            }
        } else {
            $logMessage = 'Проверка даты: пустое значение' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
            Application::$logger->error($logMessage);
            $_SESSION['error_message'] = 'Дата: пусто';
            return false;
        }
    }

    private function validatePassword(string $password): bool
    {
        if (!empty($password)) {
            if (preg_match("/\d/", $password)) {
                if (preg_match("/[A-Za-z]/", $password)) {
                    if (preg_match("/[^\s\w]/", $password)) {
                        if (preg_match("/^\S{8,16}$/", $password)) {
                            return true;
                        } else {
                            $logMessage = 'Проверка пароля: не соотевтсвие длины' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                            Application::$logger->error($logMessage);
                            $_SESSION['error_message'] = 'Пароль: не соотевтсвие длины';
                            return false;
                        }
                    } else {
                        $logMessage = 'Проверка пароля: отсутствие спецзнака' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                        Application::$logger->error($logMessage);
                        $_SESSION['error_message'] = 'Пароль: отсутствие спецзнака';
                        return false;
                    }
                } else {
                    $logMessage = 'Проверка пароля: отсутствие букв' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                    Application::$logger->error($logMessage);
                    $_SESSION['error_message'] = 'Пароль: отсутствие букв';
                    return false;
                }
            } else {
                $logMessage = 'Проверка пароля: отсутствие цифр' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                Application::$logger->error($logMessage);
                $_SESSION['error_message'] = 'Пароль: отсутствие цифр';
                return false;
            }
        } else {
            $logMessage = 'Проверка пароля: пустое значение' . " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
            Application::$logger->error($logMessage);
            $_SESSION['error_message'] = 'Пароль: пусто';
            return false;
        }
    }
}