<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\Model;
use app\model\entities\User;


class UsersRepository extends Repository
{
    protected function update(Model $entity) {
        if ($entity->props['login'] === True && $this->isLoginExist($entity->login)) {
            throw new \Exception("Пользователь с таким логином существует."); 
        } else {
            return parent::update($entity);
        }
    }

    protected function insert(Model $entity) {
        if ($this->isLoginExist($entity->login)) {
            throw new \Exception("Пользователь с таким логином существует."); 
        } else {
            return parent::insert($entity);
        }
    }

    protected function isLoginExist($login) {
        $result = $this->newQuery()->where('login', strtolower($login))->first();
        return ($result && $result->login == $login);    
    }

    public function getEntityClass()
    {
        return User::class;
    }

    public function getTableName()
    {
        return "users";
    }
}
