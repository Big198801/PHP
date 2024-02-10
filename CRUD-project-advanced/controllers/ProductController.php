<?php

namespace app\controllers;

use app\model\Products;

class ProductController extends Controller
{

    public function actionIndex() {
        echo $this->render('catalog', ['page_size' => \App::getConfig('pageSize')]);
    }

    public function actionCard($params) {
        echo $this->actionByIdCard('\Products', 'card', $params, ['groupId' => $params['id'], 'categoryFeedback' => 'product']);
    }

    public function actionApiDynamicList($params) {
        $query = \Products::orderBy('price');
        echo $this->getJSONDynamicList($query, $params);
    }

}