<?php

namespace Myproject\Application\Domain\Controllers;

use JetBrains\PhpStorm\NoReturn;
use Myproject\Application\Application\Application;
use Myproject\Application\Application\Auth;
use Myproject\Application\Domain\Models\User;
use Myproject\Application\Domain\Models\UserRepository;

class UserController extends Controller
{
    protected UserRepository $repository;

    protected array $actionsPermissions = [
        'actionIndex' => ['admin', 'user'],
        'actionHash' => ['admin', 'user'],
        'actionAuth' => ['admin', 'user'],
        'actionLogin' => ['admin', 'user'],
        'actionLogout' => ['admin', 'user'],
        'actionDelete' => ['admin'],
        'actionClear' => ['admin'],
        'actionUpdate' => ['admin'],
        'actionSearch' => ['admin', 'user'],
        'actionSave' => ['admin']];

    public function __construct()
    {
        parent::__construct();
        $this->repository = new UserRepository();
    }


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
                'users' => $this->repository->getAllUsersFromStorage($currentPage),
                'pages' => $this->repository->generatePageNumbers($currentPage),
                'current_page' => $currentPage
            ]);
    }

    public function actionSave(): void
    {
        $user = new User();
        $name = $_POST['name'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $birthday = $_POST['lastname'] ?? '';

        if ($this->validate->validateRequestData($name, $lastname, $birthday)) {
            $user->setParamsFromRequestData($name, $lastname, $birthday);
            $this->repository->saveUserFromStorage($user);

            $_SESSION['alert_message'] = "Пользователь добавлен";
            header("Location: /user/index/?alert=true");
            die();
        } else {
            throw new \Exception("Данные не корректны");
        }
    }

    #[NoReturn] public function actionDelete(): void
    {
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

        $_SESSION['alert_message'] = $this->repository->deleteUserFromStorage($id);
        header("Location: /user/index/?alert=true");
        die();
    }

    #[NoReturn] public function actionClear(): void
    {
        $_SESSION['alert_message'] = $this->repository->clearUsersFromStorage();
        header("Location: /user/index/?alert=true");
        die();
    }

    public function actionSearch(): string
    {
        $_SESSION['alert_message'] = "Пусто";
        return $this->render->renderPage(
            'user-index.twig',
            [
                'users' => $this->repository->searchTodayBirthday()
            ]);
    }

    public function actionUpdate(): void
    {
        $user = new User();
        if ($this->repository->exists($_GET['id'])) {

            $name = $_GET['name'] ?? '';
            $lastname = $_GET['lastname'] ?? '';
            $birthday = $_GET['birthday'] ?? '';

            $arrayData = [];

            $arrayKey['id_user'] = $_GET['id'];

            if ($this->validate->validateNameOrLastname($name)) {
                $arrayData['user_name'] = $name;
            }

            if ($this->validate->validateNameOrLastname($lastname)) {
                $arrayData['user_lastname'] = $lastname;
            }

            if ($this->validate->validateDate($birthday)) {
                $user->setUserBirthday($birthday);
                $arrayData['user_birthday_timestamp'] = $user->getUserBirthday();
            }

            if ($this->repository->updateData('users', $arrayData, $arrayKey)) {

                $_SESSION['alert_message'] = "Пользователь изменен";
                header("Location: /user/index/?alert=true");
                die();

            } else {
                throw new \Exception("Пользователь не изменен, проверьте данные");
            }
        } else {
            throw new \Exception("Данный пользователь не найден");
        }
    }

    public function actionHash(): string
    {
        return Auth::getPasswordHash($_GET['pass_string']);
    }

    public function actionAuth(): string
    {
        $remember = $_COOKIE['remember_token'] ?? '';
        $user = $this->repository->getAllUserCookie($remember);

        if (!empty($user)) {

            $_SESSION['user_name'] = $user[0]['user_name'];
            $_SESSION['user_lastname'] = $user[0]['user_lastname'];
            $_SESSION['id_user'] = $user[0]['id_user'];

            header('Location: /');
            die();
        } else {
            return $this->render->renderPageWithForm([
                'title' => 'Авторизация',
            ]);
        }
    }

    public function actionLogin(): string
    {
        $result = false;
        if (isset($_POST['login']) && isset($_POST['password'])) {
            $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
        }
        if (!$result) {
            return $this->render->renderPageWithForm([
                'title' => 'Авторизация',
                'alert_message' => 'Неверные логин или пароль',
                'alert' => true,
                'alert_head' => 'Ошибка'
            ]);
        } else {
            header('Location: /');
            die();
        }
    }

    #[NoReturn] public function actionLogout(): void
    {

        unset($_SESSION['user_authorized']);

        session_destroy();

        header("Location: /");
        die();
    }
}