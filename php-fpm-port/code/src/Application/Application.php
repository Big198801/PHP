<?php

namespace Myproject\Application\Application;

use Myproject\Application\Domain\Controllers\Controller;
use Myproject\Application\Infrastructure\Config;

final class Application
{
    private const APP_NAMESPACE = 'Myproject\Application\Domain\Controllers\\';

    private string $controllerName;
    private string $methodName;

    public static Config $config;
    public static Auth $auth;

    public function __construct()
    {
        Application::$config = new Config();
        Application::$auth = new Auth();
    }

    public function run(): ?string
    {
        session_start();

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

                if ($controllerInstance instanceof Controller) {

                    if ($this->checkAccessToMethod($controllerInstance, $this->methodName)) {
                        return call_user_func_array([$controllerInstance, $this->methodName], []);
                    } else {
                        throw new \Exception('Нет доступа к методу');
                    }
                } else {
                    return call_user_func_array([$controllerInstance, $this->methodName], []);
                }
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
                return header("Location: /404.html");
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            return header("Location: /404.html");
        }
    }

    private function checkAccessToMethod(Controller $controllerInstance, string $methodName): bool
    {
        $userRoles = $controllerInstance->getUserRoles();
        $rules = $controllerInstance->getActionsPermissions($methodName);
        $isAllowed = false;

        if (!empty($rules)) {
            foreach ($rules as $rolePermission) {
                if (in_array($rolePermission, $userRoles)) {
                    $isAllowed = true;
                    break;
                }
            }
        }
        return $isAllowed;
    }
}