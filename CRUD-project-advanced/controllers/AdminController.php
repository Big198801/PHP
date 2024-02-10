<?php

namespace app\controllers;

class AdminController extends Controller
{

    public function actionIndex() {
        if (\Auth::isAdmin()) {
            echo $this->render('admin', [
                'page_size' => \App::getConfig('pageSize'),
                'partOrders' => 'all'
                ]);
        } else {
            echo $this->render('accessDenited', []);
        }    
    }

}