<?php

namespace Myproject\Application\Application;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Myproject\Application\Domain\Controllers\Controller;
use Myproject\Application\Domain\Models\UserRepository;
use Myproject\Application\Infrastructure\Config;
use Myproject\Application\Infrastructure\Storage;

final class Application
{
    private const APP_NAMESPACE = 'Myproject\Application\Domain\Controllers\\';

    private string $controllerName;
    private string $methodName;

    public static Config $config;
    public static Auth $auth;
    public static Logger $logger;

    public function __construct()
    {
        Application::$config = new Config();
        Application::$auth = new Auth();
        Application::$logger = new Logger('application_logger');
        Application::$logger->pushHandler(new StreamHandler(
            $_SERVER['DOCUMENT_ROOT'] . '/../' . "/log/" . Application::$config->get()['log']['LOGS_FILE'] . '-' . date("Y-m-d") . '.log',
            Level::Debug
        ));
        Application::$logger->pushHandler(new FirePHPHandler());
    }

    public function runApp(): string {
        $memory_start = memory_get_usage();

        $result = $this->run();

        $memory_end = memory_get_usage();
        if (Application::$config->get()['log']['DB_MEMORY_LOG']) {
            $this->saveMemoryLog($memory_end - $memory_start);
        }
        return $result;
    }

    private function saveMemoryLog(int $memory): void {
        $logSql = "INSERT INTO memory_log(`user_agent`, `log_datetime`, `url`, `memory_volume`) 
            VALUES (:user_agent, :log_datetime, :url, :memory_volume)";


        $handler = Storage::getInstance()->prepare($logSql);
        $handler->execute([
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'log_datetime' => date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']),
            'url' => $_SERVER['REQUEST_URI'],
            'memory_volume' => $memory
        ]);
    }

    public function run(): ?string
    {
        session_start();
        Application::$auth->restoreSession();

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
                        $logMessage = 'Нет доступа к методу ' . $this->methodName . ' в контроллере ' . $this->controllerName;
                        $logMessage .= " | " . "Попытка вызова адреса " . $_SERVER['REQUEST_URI'];
                        Application::$logger->error($logMessage);

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
        $userRoles = (new UserRepository())->getUserRoles();
        $rules = $controllerInstance->getActionsPermissions($methodName);

        if (!empty($rules)) {
            $intersect = array_intersect($rules, $userRoles);
            if (!empty($intersect)) {
                return true;
            }
        }

        return false;
    }
}