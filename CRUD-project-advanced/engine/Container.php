<?php

namespace app\engine;

class Container
{
    protected $bindings = [];

    protected $singletons = [];
    protected $instatces = [];

    protected $buildStack = [];

    public function bind($abstract, $concrete = null, $singleton = false) {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if ($singleton && !($concrete instanceof \Closure) && class_exists($concrete)) {
            $reflector = new \ReflectionClass($concrete);
            $this->singletons[$reflector -> getName()] = null; 
        }

        $this->bindings[$abstract] = $concrete;
    }

    protected function createClosure($className) {
        return \Closure::bind(function ($app) use ($className) {
            return $app->getSingleton($className) ?: new $className;
        } , null);
    }

    public function singleton($abstract, $concrete = null) {
        $this->bind($abstract, $concrete, true);
    }


    protected function getConcrete($abstract) {
        return ($this->bindings[$abstract]) ?: $abstract;
    }

    public function make($abstract) {
        $this->buildStack = [];
        $result = $this->build($abstract);
        if (isset($result)){
            $this->setSingleton($result);
        } 
 
        return $result;
    }

    protected function addToBuildStack($new) {
        $this->buildStack[] = $new;
        if (count($this->buildStack) > 20) {
            throw new \Exception("Ошибка, переполнен стек bindings экземпляра класса.");
        }
    }

    public function fixResultBuild($abstract, $instance) {
        array_pop($this->buildStack);

        if (strpos($abstract,'\\') === false) 
        {
            $this->instances[$abstract] = $instance;
        }
        return $instance;
    }

    public function build($abstract) {
        $this->addToBuildStack($abstract);
        
        $concrete = $this->getConcrete($abstract);  

        if ($concrete instanceof \Closure) {
            if (isset($this->instances[$abstract])) {
                array_pop($this->buildStack);
                return $this->instances[$abstract];
            }
            return $this->fixResultBuild($abstract, $concrete($this));
        }

        if (array_key_exists($concrete, $this->bindings)) {
            $result = $this->build($concrete);   
            array_pop($this->buildStack);         
            return $result;
        }

        if (isset($this->instances[$abstract])) {
            array_pop($this->buildStack);
            return $this->instances[$abstract];
        } 

        if ($concrete == get_class($this)) {
            array_pop($this->buildStack);
            return $this;
        }
     
        if (isset($this->singletons[$concrete])) {
            array_pop($this->buildStack);
            return $this->singletons[$concrete ];
        }

        try {
            $reflector = new \ReflectionClass($concrete);
        } catch (\Exception $e) {
            throw new \Exception("Ошибка, класс {$concrete} не найден.");
        }

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Ошибка, не возможно создать класс {$concrete}.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return $this->fixResultBuild($abstract, new $concrete);
        }

        $dependencies = $constructor->getParameters();

        try {
            $instances = $this->resolveDependencies($dependencies);
            $instance = $reflector->newInstanceArgs($instances);
        } catch (\Exception $e) {
            array_pop($this->buildStack);
            throw $e;
        }

        return $this->fixResultBuild($abstract, $instance);
    }

    protected function setSingleton($instance) {
        $className = get_class($instance);
        if (array_key_exists($className, $this->singletons)) {
            $this->singletons[$className] = $instance;   
        }

    }

    public function getSingleton($className) {
        return (array_key_exists($className, $this->singletons)) ? $this->singletons[$className] = $instance : NULL;   
    }

    protected function resolveDependencies($dependencies)
    {
        $instances = [];
        try {
            foreach ($dependencies as $param) {
                $type = $param->getType()->getName();
                $this->buildStack[] = $type;
                $concrete = $this->getConcrete($type);
                if (class_exists($type) || $type !== $concrete) {
                    $instances[$param->name] = $this->build($type);
                    $this->setSingleton($instances[$param->name]);
                } 
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $instances;
    }

}