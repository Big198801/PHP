<?php

namespace Myproject\Application\Domain\Controllers;

use Myproject\Application\Domain\Models\User;
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
        $user = new User();
        $currentPage = $_GET['page'] ?? 1;
        $alert = $_GET['alert'] ?? false;
        $message = isset($_GET['alert_message']) ? urldecode($_GET['alert_message']) : "База пуста";

        return $this->render->renderPage(
            'user-index.twig',
            [
                'alert_message' => $message,
                'alert' => $alert,
                'alert_head' => 'Результат',
                'users' => $user->getAllUsersFromStorage($currentPage),
                'pages' => $user->generatePageNumbers($currentPage),
                'current_page' => $currentPage
            ]);
    }

    public function actionSave(): string
    {
        $user = new User();
        if ($user->validateRequestData()) {
            $user->setParamsFromRequestData();
            $user->saveUserFromStorage();

            $message = urlencode("Пользователь добавлен");
            return header("Location: /user/index/?alert=true&alert_message=" . $message);
        } else {
            throw new \Exception("Данные не корректны");
        }
    }

    public function actionDelete(): string
    {
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
        $user = new User($id);
        $message = urlencode($user->deleteUserFromStorage());

        return header("Location: /user/index/?alert=true&alert_message=" . $message);
    }

    public function actionClear(): string
    {
        $message = urlencode((new User())->clearUsersFromStorage());

        return header("Location: /user/index/?alert=true&alert_message=" . $message);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionSearch(): string
    {
        return $this->render->renderPage(
            'user-index.twig',
            [
                'alert_message' => "Пусто",
                'users' => (new User())->searchTodayBirthday()
            ]);
    }

    public function actionEdit(): string
    {
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
        $user = new User($id, $_GET['name'], $_GET['lastname']);
        $user->setUserBirthday($_GET['birthday']);

        if ($user->updateUserFromStorage() && $user->validateDate($_GET['birthday'])) {
            $message = urlencode("Пользователь изменен");

            return header("Location: /user/index/?alert=true&alert_message=" . $message);
        } else {
            throw new \Exception("Данные не корректны");
        }
    }
}