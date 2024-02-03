<?php

namespace Myproject\Application\Domain\Controllers;

use Myproject\Application\Domain\Models\User;
use Random\RandomException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws RandomException
     */
    public function actionIndex(): string
    {
        $user = new User();
        $currentPage = $_GET['page'] ?? 1;
        $alert = $_GET['alert'] ?? false;
        $message = $_SESSION['alert_message'] ?? "База пуста";

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

    public function actionSave(): void
    {
        $user = new User();
        if ($user->validateRequestData()) {
            $user->setParamsFromRequestData();
            $user->saveUserFromStorage();

            $_SESSION['alert_message'] = "Пользователь добавлен";
            header("Location: /user/index/?alert=true");
            die();
        } else {
            throw new \Exception("Данные не корректны");
        }
    }

    public function actionDelete(): void
    {
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
        $user = new User($id);

        $_SESSION['alert_message'] = $user->deleteUserFromStorage();
        header("Location: /user/index/?alert=true");
    }

    public function actionClear(): void
    {
        $_SESSION['alert_message'] = (new User())->clearUsersFromStorage();
        header("Location: /user/index/?alert=true");
        die();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws RandomException
     */
    public function actionSearch(): string
    {
        $_SESSION['alert_message'] = "Пусто";
        return $this->render->renderPage(
            'user-index.twig',
            [
                'users' => (new User())->searchTodayBirthday()
            ]);
    }

    public function actionUpdate(): void
    {
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

        $user = new User($id);

        if (isset($_GET['name']))
            $user->setUserName($_GET['name']);

        if (isset($_GET['lastname'])) {
            $user->setUserLastname($_GET['lastname']);
        }

        if (isset($_GET['birthday']) && $user->validateDate($_GET['birthday'])) {
            $user->setUserBirthday($_GET['birthday']);
        }

        if ($user->updateUserFromStorage()) {

            $_SESSION['alert_message'] = "Пользователь изменен";
            header("Location: /user/index/?alert=true");
            die();
        } else {
            throw new \Exception("Пользователь не изменен, проверьте данные");
        }
    }
}