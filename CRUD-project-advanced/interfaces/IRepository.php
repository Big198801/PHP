<?php

namespace app\interfaces;

interface IRepository
{
    public function getTableName();
    public function getDb();
    public function newQuery();
    public function getSystemProps();
}
