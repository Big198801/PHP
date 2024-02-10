<?php


namespace app\controllers;

use app\interfaces\IController;
use app\interfaces\IRenderer;


class Controller implements IController
{
    protected $action;
    protected $defaultAction = 'index';
    protected $layout = 'main';
    protected $useLayout = true;
    protected $globalParams = [];

    protected $renderer;

    /**
     * Controller constructor.
     * @param $action
     */
    public function __construct(IRenderer $renderer)
    {
         $this->renderer = $renderer;

    }

    public function setGlobalParams() {
        $this->globalParams = [
            "isAuth" => \Auth::isAuth(),
            "isAdmin" => \Auth::isAdmin(),    
            "countInCart" => \Cart::getBaseQuery()->sum('quantity'),      
        ];

        if ($this->globalParams['isAuth']) {
            $this->globalParams = array_merge($this->globalParams, \Auth::getUserInfo());
        }
    }


    public function actionError() {
        header('HTTP/1.0 404 Not Found');
        header('Status: 404 Not Found');
        echo $this->render('404', []);
    }

    public function render($template, $params = []) {
        $this->setGlobalParams();
        $params = array_merge($params, $this->globalParams);
        if ($this->useLayout) {
            return $this->renderTemplate("layouts/{$this->layout}", [
                'menu' => $this->renderTemplate('menu', $params),
                'content' => $this->renderTemplate($template, $params)
            ]);
        } else {
            return $this->renderTemplate($template, $params);
        }
    }

    public function actionByIdCard($ClassName, $tmplName, $params, $pageParams = []) {
        $item = $ClassName::find($params['id']);

        if ($item) {
            $id = $item->getKeyFieldName();
            if ($item->$id) {
                return $this->render($tmplName, array_merge([
                    'item' => $item
                ], $pageParams));
            }
        }

        return $this->errorAction();
    }

    public function getJSONDynamicList($queryDB, $params, $fields=[]) {
        $count = $queryDB->count();   
        $list = $queryDB->get($params['count'], $params['offset']);
        $items = [];
        foreach ($list as $item) {
            $items[] = $item->getDataFields($fields);
            if (!end($items)['id']) {
                $id = $item->getKeyFieldName();
                end($items)['id'] = $item->$id;
            }    
        }

        $answer = [
            'items' => $items,
            'totalCount' => $count
        ];

        return json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }


    public function renderTemplate($template, $params = []) {
        return $this->renderer->renderTemplate($template, $params);
    }

}