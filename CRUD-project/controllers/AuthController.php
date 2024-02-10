<?php

namespace app\controllers;


use app\engine\App;

class AuthController extends Controller
{
    //action="/auth/login"
    public function actionLogin() {
        $login = App::call()->request->getParams()['login'];
        $pass = App::call()->request->getParams()['pass'];
        if (App::call()->usersRepository->Auth($login, $pass)) {
            header("Location: /");
            die();
        } else {
            die("Не верный логин пароль");
        }
    }

    public function actionLogout()
    {
        //TODO Переписать на Session
        session_regenerate_id();
        session_destroy();
        header("Location: /");
        die();
    }
}