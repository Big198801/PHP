<?php

namespace Myproject\Application\Controllers;

use Myproject\Application\Models\Validate;
use Myproject\Application\Render;
use Myproject\Application\Models\User;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionIndex(): string
    {
        $users = User::getAllUsersFromStorage();
        $render = new Render();

        if (!$users) {
            return $render->renderPage(
                'user-empty.tpl',
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список не найден"
                ]);
        } else {
            return $render->renderPage(
                'user-index.tpl',
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users
                ]);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionSave(array $getParams): string
    {
        $render = new Render();

        if (array_key_exists('name', $getParams) &&
            array_key_exists('birthday', $getParams) &&
            Validate::validateDate($getParams['birthday'])) {

            $user = new User($getParams['name']);
            $user->setUserBirthday($getParams['birthday']);

            $result = $user->saveUserFromStorage();

            return $render->renderPage(
                'user-empty.tpl',
                [
                    'title' => 'Статус записи в хранилище',
                    'message' => $result
                ]);
        } else {
            return $render->renderPage(
                'user-empty.tpl',
                [
                    'title' => 'Статус записи в хранилище',
                    'message' => 'Ошибка в запросе'
                ]);
        }
    }
}