<?php

namespace app\engine;

class Session
{

    protected $started = false;
    protected $id = null;

    public function start() {
        if (!$this->started) {
            session_start();
            $this->id = session_id();
            $this->started = true;
        }    
    }

    public function __get($name) {
        return ($this->$name) ?: $_SESSION[$name];
    }

    public function __isset($name) {
        return property_exists($this, $name)  || isset($_SESSION[$name]);
    }

    public function __set($name, $value) { 
        $_SESSION[$name] = $value;
    }

    public function deleteParam($param) {
        unset($_SESSION[$param]);
    }

    public function getId() {
        return $this->id;
    }

}