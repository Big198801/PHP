<?php

namespace Myproject\Application\Controllers;

use Myproject\Application\Models\Validate;
use Myproject\Application\Render;
use Myproject\Application\Models\User;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionIndex(): string
    {
        $users = User::getAllUsersFromStorage();

        return $this->render->renderPage(
            'user-index.twig',
            [
                'title' => 'Список пользователей в хранилище',
                'message' => "Список не найден",
                'users' => $users
            ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionSave(): string
    {
        $name = $_GET['name'] ?? '';
        $birthday = $_GET['birthday'] ?? '';

        $user = new User($name);
        $user->setUserBirthday($birthday);

        $result = $user->saveUserFromStorage();

        return $this->render->renderPage(
            'user-index.twig',
            [
                'title' => 'Статус записи в хранилище',
                'message' => $result
            ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionDelete(): string
    {
        $name = $_GET['name'] ?? '';

        $user = new User($name);

        $result = $user->deleteUserFromStorage();

        return $this->render->renderPage(
            'user-index.twig',
            [
                'title' => 'Статус записи в хранилище',
                'message' => $result
            ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionClear(): string
    {
        $result = User::clearUsersFromStorage();

        return $this->render->renderPage(
            'user-index.twig',
            [
                'title' => 'Список пользователей в хранилище',
                'message' => $result
            ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionSearch(): string
    {
        $users = User::searchTodayBirthday();

        return $this->render->renderPage(
            'user-index.twig',
            [
                'title' => 'Сегодня день рождения',
                'message' => "некого поздравлять",
                'users' => $users
            ]);

    }
}