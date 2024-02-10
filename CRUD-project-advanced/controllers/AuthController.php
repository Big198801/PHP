<?php

namespace app\controllers;

use app\model\entities\User;

class AuthController extends Controller
{

    public function actionLogin($params) {
        if (isset($params['login']) && isset($params['password'])) {
            \Auth::auth($params['login'], $params['password'], $params['save']);    
            if (!\Auth::isAuth()) {
                $params['message'] = 'Не верная пара логин/пароль!'; 
            } else {
                header('Location: /');
                Die();
            }
        }
        echo $this->render('auth', $params);
    }

    protected function updateUserProfile($params) {
        $result = [];
        $user = \Auth::getUserByLoginPassword($params['login'], $params['current-password']);
        if (isset($user)) {
            $user->name = $params['name'];
            if ($params['new-password']) {
                $user->setPasswordHash($params['new-password']);
            }
            if (\Users::save($user)) {
                $result['message'] = 'Данные учетной записи успешно изменены';
                $result['no_error'] = '_no';
                \Auth::auth($params['login'], ($params['new-password']) ?: $params['current-password']);
            } else {
                $result['message'] = 'Произошла ошибка при обновлении данных';
            };

        } else {
            $result['message'] = 'Не верный пароль';
        }

        return $result;
    }

    public function actionProfile($params) {
        if (!\Auth::isAuth()) {
            header('Location: /login');
            Die();            
        }
        
        $profileInfo = [
            'login' => $params['login'],
            'userName' => $params['userName']
        ];

        if (isset($params['current-password']) && \Auth::getUserInfo()['login'] == $params['login']) {
            //Пытаемся обновить профиль и результат присоединим к массиву паратмеров передаваемых в шаблон!
            $profileInfo = array_merge($profileInfo, $this->updateUserProfile($params));
        } 

        echo $this->render('profile', $profileInfo);
    }


    public function actionRegister($params) {
        $message = "";
        $header = "";

        if (empty($params['login']) || empty($params['name']) || empty($params['password'])) {
            $message = "Не заполнены все поля необходимые для регистрации пользователя!";
        } elseif (\Auth::isLoginExist($params['login'])) {
            $message = "Пользователь с таким логином уже зарегистирован!";
        } else {
            $user = new User($params['login'], $params['password'], $params['name']);
            \Users::save($user);
            if ($user->id) {
                $header = "Пользователь {$params['login']} успешно зарегистирован. Вы можете авторизоваться на сайте.";
            } else {
                $message = "Произошла ошибка. Пользователь не был зарегистрирован. Повторите попытку!";
            }
        }
        echo $this->render('auth', [
            'header' => $header,
            'message' => $message
        ]);
    }


}