<?php

namespace app\controllers;

class SimplePageController extends Controller
{

    public function actionIndex($params) {

        echo $this->render('index', $params);
    }


}