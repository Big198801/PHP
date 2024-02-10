<?php

namespace app\engine;

use app\interfaces\IRenderer;

class Router
{
    const METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    public $routes = [];
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function __call($method, $parameters) {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        }   
        $up_method = strtoupper($method);
        if (array_search($up_method, static::METHODS) !== false) {
            return $this->match([$up_method], $parameters[0], $parameters[1]);
        }
    }
    
    protected function checkUriWithRegEx($regEx) {
        preg_match($regEx, $this->request->requestString, $matches);
        return count($matches) > 0;
    }

    protected function getParamsForControllerAction($route) {
        $params = [];
        preg_match($route['regEx'], $this->request->requestString, $matches, PREG_OFFSET_CAPTURE);
        for ($i = 1; $i < count($matches); $i++) {
            $params[$route['paramsName'][$i - 1]] = $matches[$i][0];
        }
        return $params;
    }

    protected function getControllerNameAndParams() {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $this->request->method)  {
                continue;
            }

            if ($this->checkUriWithRegEx($route['regEx'])) {
                return [
                    'controller' => $route['controller'],
                    'params' => $this->getParamsForControllerAction($route)
               ];
            }
        }
    }

    protected function getRexExAndParams($uri) {
        $regEx = "/^" . str_replace(['/'],['\/'], $uri) . "$/";
        $paramsName = [];
        preg_match_all("/{([A-Za-z]+[A-Za-z0-9]*)}/", $regEx, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $paramsName[] = $match[1];
        }
        $regEx = preg_replace("/{[A-Za-z]+[A-Za-z0-9]*}/", "([A-Za-z0-9\.]+)", $regEx);
        return compact("regEx", "paramsName");
    } 

    protected function match($methods, $uri, $controllerAndMethod) {
        foreach ($methods as $method) {
            $ar = explode('.', $controllerAndMethod);
            if (count($ar) != 2) {
                throw new \Exception('Верный формат последнего параметра: Controller.Method');
            }

            $up_method = strtoupper($method);
            if (array_search($up_method, static::METHODS) !== false) {
                $result = $this->getRexExAndParams($uri);
                $this->routes[] = [
                    'method' => $up_method,
                    'regEx' => $result['regEx'],
                    'controller' => $controllerAndMethod,
                    'paramsName' => $result['paramsName'],
                ];
            }
        }

    }

}