<?php

namespace app\engine;

use app\traits\TSingletone;

class Application extends Container
{
    protected static $app;
    protected $config = [];

    public function __construct() {
        $this->bind('app', get_class($this), true);
    }

    protected function loadConfigs() {
        include_once $this->config['root_dir'] . "/config/binding.php";
        include_once $this->config['root_dir'] . "/routes/map.php";        
    }

    
    public function getConfig($name = '') {
        return (isset($name)) ? $this->config[$name] : $this->config;
    }

    public function callMethodController($name, $params) {
        $nameAndMethod = explode('.', $name);
        if (count($nameAndMethod) != 2) {
            throw new \Exception("Первый параметр метода имеет не верный формат");
        } 

        $controllerClass = $this->config['controllers_namespaces'] . ucfirst($nameAndMethod[0]) . "Controller";
        if (!class_exists($controllerClass)) {
            throw new \Exception("Ошибка, контроллер {$controllerClass} не существует.");
        }
    
        $action = 'action'. ucfirst($nameAndMethod[1]);
        if (!method_exists($controllerClass, $action)) {
            throw new \Exception("Ошибка, метод {$nameAndMethod[1]} в контроллере {$controllerClass} не существует.");
        }
    
        $this->make($controllerClass)->$action($params);

    }

    public function initCart() {
        \Cart::setSystemProp('session_id', \Session::getId()); 
        $userInfo = \Auth::getUserInfo();
        if (isset($userInfo) && $userInfo['userId']) {
            \Cart::setSystemProp('user_id', $userInfo['userId']);
        }
    }

    public function run($config) {
        $this->config = $config;
        $this->loadConfigs();

        \Session::start();
        $params = \Request::getParams();

        if (array_key_exists('logout', $params)) {
            \Auth::logout();
        };

        $this->initCart();

        $controllerInfo = \Route::getControllerNameAndParams() ?: [
            'controller' => $this->config['defaultAction'],
            'params' => []
        ];

        $this->callMethodController(
            $controllerInfo['controller'], 
            array_merge($params, $controllerInfo['params'])
        );

    }

}