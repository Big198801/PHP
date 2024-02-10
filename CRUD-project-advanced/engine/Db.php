<?php

namespace app\engine;


use app\interfaces\IDb;

class Db implements IDb
{
    private $config;

    private $connection = null;

    public function __construct($driver, $host, $login, $password, $database, $charset = "utf8") {
        $this->config = [
            'driver' => $driver,
            'host' => $host,
            'login' => $login,
            'password' => $password,
            'database' => $database,
            'charset' => $charset
        ];

    }

    private function getConnection() {
        if (is_null($this->connection)) {
            $this->connection = new \PDO($this->prepareDsnString(),
                $this->config['login'],
                $this->config['password']);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        }
        return $this->connection;
    }

   
    private function prepareDsnString() {
         return sprintf("%s:host=%s;dbname=%s;charset=%s",
            $this->config['driver'],
            $this->config['host'],
            $this->config['database'],
            $this->config['charset']
         );

    }

    private function query($sql, $params) {
        $proStatement = $this->getConnection()->prepare($sql);
        $proStatement->execute($params);
        return $proStatement;
    }

    public function execute($sql, $params = []) {
        return $this->query($sql, $params);
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function queryObject($sql, $params, $className) {
        $proStatement = $this->query($sql, $params);
        $proStatement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
        return $proStatement->fetch();
    }

    public function queryObjects($sql, $params, $className) {
        $proStatement = $this->query($sql, $params);
        $proStatement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
        return $proStatement->fetchall();
    }

    public function queryOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function queryAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function beginTransaction() {
        $this->getConnection()->beginTransaction();
    }

    public function commit() {
        $this->getConnection()->commit();   
    }

    public function rollBack() {
        $this->getConnection()->rollBack();
    }

}