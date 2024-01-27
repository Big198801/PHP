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

        if (!$users) {
            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список не найден"
                ]);
        } else {
            return $this->render->renderPage(
                'user-index.twig',
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
    public function actionSave(): string
    {
        $name = $_GET['name'];
        $birthday = $_GET['birthday'];

        if (isset($name) && isset($birthday) &&
            Validate::validateDate($birthday)) {

            $user = new User($name);
            $user->setUserBirthday($birthday);

            $result = $user->saveUserFromStorage();

            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Статус записи в хранилище',
                    'message' => $result
                ]);
        } else {
            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Статус записи в хранилище',
                    'message' => 'Ошибка в запросе'
                ]);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionDelete(): string
    {
        $name = $_GET['name'];

        if (isset($name)) {
            $user = new User($name);

            $result = $user->deleteUserFromStorage();

            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Статус записи в хранилище',
                    'message' => $result
                ]);
        } else {
            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Статус записи в хранилище',
                    'message' => 'Ошибка в запросе'
                ]);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionClear(): string
    {
        $result = User::clearUsersFromStorage();

        if (!$result) {
            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список не найден"
                ]);
        } else {
            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => $result
                ]);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionSearch(): string
    {
        $users = User::searchTodayBirthday();

        if (!$users) {
            return $this->render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Сегодня день рождения',
                    'message' => "Список не найден"
                ]);
        } else {
            return $this->render->renderPage(
                'user-index.twig',
                [
                    'title' => 'Сегодня день рождения',
                    'text' => 'Список пользователей',
                    'users' => $users
                ]);
        }
    }
}