<?php

namespace Myproject\Application;

use Myproject\Application\Controllers\ErrorController;

class Application
{
    private const APP_NAMESPACE = 'Myproject\Application\Controllers\\';

    private string $controllerName;
    private string $methodName;

    public function run(): string
    {
        $routeArray = explode('/', $_SERVER['REQUEST_URI']);

        if (isset($routeArray[1]) && $routeArray[1] != '') {
            $controllerName = $routeArray[1];
        } else {
            $controllerName = 'page';
        }

        $this->controllerName = Application::APP_NAMESPACE . ucfirst($controllerName) . 'Controller';

        if (class_exists($this->controllerName)) {
            if (isset($routeArray[2]) && $routeArray[2] != '') {
                $methodName = $routeArray[2];
            } else {
                $methodName = 'index';
            }

            $this->methodName = 'action' . ucfirst($methodName);

            if (method_exists($this->controllerName, $this->methodName)) {
                $controllerInstance = new $this->controllerName();

                if (!empty($_SERVER['QUERY_STRING'])) {
                    parse_str($_SERVER['QUERY_STRING'], $getParam);
                    return call_user_func_array(
                        [$controllerInstance, $this->methodName],
                        [$getParam]
                    );
                } else {
                    return call_user_func_array(
                        [$controllerInstance, $this->methodName],
                        []
                    );
                }
            } else {
                return call_user_func_array([new ErrorController(), "error404"], []);
            }
        } else {
            return call_user_func_array([new ErrorController(), "error404"], []);
        }
    }
}