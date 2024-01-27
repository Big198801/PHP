<?php

namespace Myproject\Application;

use Myproject\Application\Controllers\PageController;

final class Application
{
    private const APP_NAMESPACE = 'Myproject\Application\Controllers\\';

    private static array $config;

    private string $controllerName;
    private string $methodName;

    public static function getConfig(): array
    {
        return Application::$config;
    }

    public function run(): ?string
    {
        Application::$config = parse_ini_file('config.ini', true);
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

                return call_user_func_array(
                        [$controllerInstance, $this->methodName],
                        []
                    );
            } else {
                header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
                return header("Location: /404.html");
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            return header("Location: /404.html");
        }
    }
}