<?php

namespace Myproject\Application\Domain\Controllers;

use Myproject\Application\Application\Application;
use Myproject\Application\Application\Auth;
use Myproject\Application\Domain\Models\User;

class UserController extends Controller
{
    protected array $actionsPermissions = [
        'actionIndex' => ['admin'],
        'actionDelete' => ['admin'],
        'actionClear' => ['admin'],
        'actionUpdate' => ['admin'],
        'actionSearch' => ['admin'],
        'actionSave' => ['admin']];

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

    public function actionHash(): string
    {
        return Auth::getPasswordHash($_GET['pass_string']);
    }

    public function actionAuth(): string
    {
        return $this->render->renderPageWithForm(
            [
                'title' => 'Форма логина',
            ]);
    }

    public function actionLogin(): string
    {
        $result = false;
        if (isset($_POST['login']) && isset($_POST['password'])) {
            $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
        }
        if (!$result) {
            return $this->render->renderPageWithForm([
                'title' => 'Форма логина',
                'auth-success' => false,
                'auth-error' => 'Неверные логин или пароль'
            ]);
        } else {
            throw new \Exception("Нет доступа");
        }
    }
}