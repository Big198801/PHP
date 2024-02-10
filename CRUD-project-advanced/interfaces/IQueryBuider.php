<?php

namespace app\interfaces;

interface IQueryBuider
{
    
    public function getSQLAndParams($fields = [], $where = [], $limit = 0, $offset = 0);
    public function first();
    public function find($id);
    public function get($limit=0, $offset=0);
    public function __construct(IRepository $repo);

}
