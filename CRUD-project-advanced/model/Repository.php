<?php

namespace app\model;

use app\interfaces\IRepository;
use app\engine\Db;


abstract class Repository implements IRepository
{

    protected $query = null;
    protected $db = null;
    protected $systemProps = [];

    public function __construct(Db $db)
    {
        $this->db = $db;
        $this->newQuery();
    }


    public function __call($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        } else {
            return call_user_func_array([$this->newQuery(), $method], $parameters);
        }    
    }

    public function __isset($name)
    {
        return array_search($name, $this->systemProps) !== false;     
    }

    public function __get($name) {
        if (array_search($name, $this->systemProps) !== false) {
            return $this->$name;
        }
    }

    public function setSystemProp($name, $value) {
        $this->$name = $value;
    }

    public function __set($name, $value) {
        if (array_search($name, $this->systemProps) !== false) {
            $this->$name = $value;
        }
    }

    public function newQuery() {
        $this->query = new QueryBuilder($this);
        return new QueryBuilder($this);
    }


    public function getDb() {
        return $this->db;
    }

    protected function insert(Model $entity) {

        $params = [];

        foreach ($entity->props as $key=>$value) {
            $params["{$key}"] = $entity->$key;
        }
        
        foreach ($this->systemProps as $prop) {
            if ($this->$prop) {
                $params["{$prop}"] = $this->$prop;  
            }    
        }

        $columns = "`" . implode("`, `", array_keys($params)) . "`";
        $values = ":" . implode(", :", array_keys($params));

        $sql = "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$values})";
        
        if ($this->db->execute($sql, $params)) {
            $entity->setKeyValue($this->db->lastInsertId());
            return true;
        } else {
            return false;
        }
    }

    protected function update(Model $entity) {
        $sets = [];
        $params = [];
        $result = true;
        foreach ($entity->props as $key=>$value) {
            if ($value) {
                $params["{$key}"] = $entity->$key;
                $sets[] = "`{$key}` = :{$key}";
            }    
        }
        if ($entity->getKeyValue() && count($sets) > 0) {
            $set_str = implode(", ", $sets);
            $where = [];
            $where[] = ['field' => $entity->getKeyFieldName(), 'operator' => '=', 'value' => $entity->getKeyValue()];
            //Защита от записи в чужую сессию и т.п.
            foreach ($this->systemProps as $prop) {
                if ($this->$prop) {
                    $where[] = ['field' => $prop, 'operator' => '=', 'value' => $this->$prop];  
                }    
            }
            $w = $this->query->getWhereStrAndParams($where);
            $params = array_merge($params, $w['params']);
            $sql = "UPDATE {$this->getTableName()} SET {$set_str} {$w['str']}";
            if ($this->db->execute($sql, $params)) {
                $entity->clearProps();
            } else {
                $result = false;
            }
        }

        return $result;
    }


    public function save(Model $entity) {
        if (is_null($entity->getKeyValue()))
            return $this->insert($entity);
        else
            return $this->update($entity);
    }

    public function delete(Model $entity) {
        $id = $entity->getKeyFieldName();
        $sql = "DELETE FROM {$this->getTableName()} WHERE {$id} = :id";
        return $this->db->execute($sql, ['id' => $entity->getKeyValue()])->rowCount();
    }

    public function getSystemProps() {
        return $this->systemProps;
    }

    abstract public function getEntityClass();
    abstract public function getTableName();


}