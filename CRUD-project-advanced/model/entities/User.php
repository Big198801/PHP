<?php

namespace app\model\entities;

use app\model\Model;

class User extends Model
{
    protected $id;
    protected $login;
    protected $name;
    protected $password_hash;
    protected $cookie_hash;
    protected $is_admin;

    public $props = [
        'login' => false,
        'name' => false,
        'password_hash' => false,
        'cookie_hash' => false,
        'is_admin' => false
    ];

    public function __construct($login = null, $password = null, $name = null)
    {
        $this->login = strtolower($login);
        $this->name = $name;
        $this->is_admin = false;
        if (isset($password)) {
            $this->setPasswordHash($password);
        }
    }

    public function setPasswordHash($pass) {
        if (empty($pass)) {
            throw new \Exception('Пароль не может быть пустым');
        }
        $this->password_hash = password_hash($pass, PASSWORD_DEFAULT); 
        $this->props['password_hash'] = true;
    }


    public function __set($name, $value) {
        if ($name == 'login') {
            $value = strtolower($value);
        }
        parent::__set($name, $value);
    }

    
}
