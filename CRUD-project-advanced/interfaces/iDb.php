<?php

namespace app\interfaces;


interface iDb
{

    public function execute($sql, $params);
    public function lastInsertId();
    public function queryObject($sql, $params, $className);
    public function queryObjects($sql, $params, $className); 
    public function queryOne($sql, $params);
    public function queryAll($sql, $params);

}