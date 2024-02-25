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
        'actionIndexRefresh' => ['admin', 'user'],
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
        $currentPage = $_GET['page'] ?? 1;
        $alert = $_GET['alert'] ?? false;
        $message = $_SESSION['alert_message'] ?? "База пуста";

        return $this->render->renderPage(
            'layout-user/user-index.twig',
            [
                'alert_message' => $message,
                'alert' => $alert,
                'alert_head' => 'Результат',
                'users' => $this->repository->getAllUsersFromStorage($currentPage),
                'pages' => $this->repository->generatePageNumbers($currentPage),
                'current_page' => $currentPage
            ]);
    }

    public function actionIndexRefresh(): string
    {
        $limit = null;

        if (isset($_POST['maxId']) && ($_POST['maxId'] > 0)) {
            $limit = $_POST['maxId'];
        }

        $users = $this->repository->getAllUsersFromStorage(1, $limit);

        $usersData = [];

        if (count($users) > 0) {
            foreach ($users as $user) {
                $usersData[] = $user->getUserDataArray();
            }
        }
        return json_encode($usersData);
    }

    public function actionSave(): string|false
    {
        $user = new User();
        $name = $_POST['name'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $birthday = $_POST['birthday'] ?? '';
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $validData = [
            'name' => $name,
            'lastname' => $lastname,
            'birthday' => $birthday,
            'login' => $login,
            'password' => [$password, $confirm_password]];

        if ($this->validate->validateRequestData($validData)) {
            $user->setParamsFromRequestData($name, $lastname, $birthday, $login, $password);
            $this->repository->saveUserFromStorage($user);

            return json_encode($user->getUserDataArray());

        } else {
            return json_encode(['error' => $_SESSION['error_message']]);
        }
    }

    #[NoReturn] public function actionDelete(): string|false
    {
        $id = $_GET['id'];
        if ($this->repository->exists($id)) {
            $_SESSION['alert_message'] = $this->repository->deleteUserFromStorage($id);

            return json_encode(['message' => $_SESSION['alert_message']]);

        } else {
            $logMessage = 'При удалении пользователь в базе осутствует';
            $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
            Application::$logger->error($logMessage);
            return json_encode(['message' => 'Пользователь в базе осутствует']);
        }
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
            'layout-user/user-index.twig',
            [
                'users' => $this->repository->searchTodayBirthday()
            ]);
    }

    public function actionUpdate(): void
    {
        $id = $_GET['id'];
        if ($this->repository->exists($id)) {

            $login = $_GET['login'] ?? '';
            $name = $_GET['name'] ?? '';
            $lastname = $_GET['lastname'] ?? '';
            $birthday = $_GET['birthday'] ?? '';

            $arrayData = $this->validate->validateUserData($login, $name, $lastname, $birthday);
            $arrayKey['id_user'] = $id;

            if ($this->repository->updateData('users', $arrayData, $arrayKey)) {
                $_SESSION['alert_message'] = "Пользователь изменен";
                header("Location: /user/index/?alert=true");
                die();

            } else {
                $logMessage = 'При изменении пользователя не верные данные';
                $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                Application::$logger->error($logMessage);
                throw new \Exception("Пользователь не изменен, проверьте данные");
            }
        } else {
            $logMessage = 'При изменении пользователь в базе осутствует';
            $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
            Application::$logger->error($logMessage);
            throw new \Exception("Данный пользователь не найден");
        }
    }

    public function actionAuth(): string
    {
        return $this->render->renderPageWithForm([
            'title' => 'Авторизация',
        ]);
    }

    public function actionLogin(): string
    {
        $result = false;
        if (isset($_POST['login']) && isset($_POST['password'])) {
            $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);

            if($result &&
                isset($_POST['remember']) && $_POST['remember'] == 'remember'){
                $token = Application::$auth->generateToken();

                UserRepository::setToken($_SESSION['auth']['id_user'], $token);
            }
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

    #[NoReturn] public function actionLogout(): void {
        UserRepository::destroyToken();
        session_destroy();
        unset($_SESSION['auth']);
        header("Location: /");
        die();
    }
}