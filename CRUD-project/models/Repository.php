<?php

namespace app\models;

use app\engine\Db;
use app\interfaces\IRepository;
use app\engine\App;

abstract class Repository implements IRepository
{
    protected abstract function getTableName();
    abstract protected function getEntityClass();

    //SELECT from users where login = admin
    public function getWhere($name, $value) {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE {$name} = :value";
        return App::call()->db->queryOneObject($sql, ['value' => $value], $this->getEntityClass());
    }

    //WHERE session_id = '111' return 2
    public function getCountWhere($name, $value) {
        $tableName = $this->getTableName();
        $sql = "SELECT count(id) as count FROM {$tableName} WHERE {$name} = :value";
        return App::call()->db->queryOne($sql, ['value' => $value])['count'];
    }

    public function insert(Model $entity)
    {
        $params = [];
        $columns = [];

        foreach ($entity->props as $key => $value) {
            $params[":" . $key] = $entity->$key;
            $columns[] = $key;
        }

        $columns = implode(', ', $columns);
        $values = implode(', ', array_keys($params));


        $tableName = $this->getTableName();

        $sql = "INSERT INTO `{$tableName}`($columns) VALUES ($values)";


        App::call()->db->execute($sql, $params);
        $entity->id = App::call()->db->lastInsertId();
        return $this;
    }

    public function update(Model $entity)
    {
        $params = [];
        $colums = [];

        $tableName = static::getTableName();

        foreach ($entity->props as $key => $value) {
            if (!$value) continue;
            $params["{$key}"] = $entity->$key;
            $colums[] .= "`{$key}` = :{$key}";
            $entity->props[$key] = false;
        }
        $colums = implode(", ", $colums);
        $params['id'] = $entity->id;

        $sql = "UPDATE `{$tableName}` SET {$colums} WHERE `id` = :id";
        App::call()->db->execute($sql, $params);
        return $this;
    }

    public function save(Model $entity)
    {
        if (is_null($entity->id)) {
            $this->insert($entity);
        } else {
            $this->update($entity);
        }
    }

    public function delete(Model $entity)
    {
        $tableName = $this->getTableName();
        $sql = "DELETE FROM $tableName WHERE id = :id";
        return App::call()->db->execute($sql, ['id' => $entity->id]);
    }


    public function getOne($id)
    {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE id = :id";
        //  return Db::getInstance()->queryOne($sql, ['id' => $id]);
        return App::call()->db->queryOneObject($sql, ['id' => $id], $this->getEntityClass());
    }

    public function getAll()
    {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName}";
        return App::call()->db->queryAll($sql);
    }

    public function getLimit($limit)
    {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} LIMIT 0, ?";
        return App::call()->db->queryLimit($sql, $limit);
    }
}