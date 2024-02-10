<?php

namespace app\engine;

use app\models\repositories\BasketRepository;
use app\models\repositories\ProductRepository;
use app\models\repositories\UserRepository;
use app\engine\Session;
use app\traits\TSingletone;

/**
 * Class App
 * @property Request $request
 * @property BasketRepository $basketRepository
 * @property UserRepository $usersRepository
 * @property ProductRepository $productRepository
 * @property Db $db
 * @property Session $session
 */
class App
{
    use TSingletone;

    public $config;
    private $components;
    private $controller;
    private $action;

    public function run($config)
    {
        $this->config = $config;
        $this->components = new Storage();
        $this->runController();
    }

    protected function runController() {
        $this->controller = $this->request->getControllerName() ?: 'product';
        $this->action = $this->request->getActionName();
        $controllerClass = $this->config['controllers_namespaces'] . ucfirst($this->controller) . "Controller";
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass(new Render());
            $controller->runAction($this->action);
        } else {
            echo "404";
        }
    }

    public function createComponent($name)
    {
        if (isset($this->config['components'][$name])) {
            $params = $this->config['components'][$name];
            $class = $params['class'];
            if (class_exists($class)) {
                unset($params['class']);

                $reflection = new \ReflectionClass($class);
                return $reflection->newInstanceArgs($params);
            } else {
                Die("Нет класса компонента");
            }
        } else {
            Die("Нет такого компонента");
        }
    }

    /**
     * @return static
     */
    public static function call()
    {
        return static::getInstance();
    }

    public function __get($name)
    {
        return $this->components->get($name);
    }
}