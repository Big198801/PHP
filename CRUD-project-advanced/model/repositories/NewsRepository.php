<?php

namespace app\model\repositories;

use app\model\Repository;

use app\model\entities\News;

class NewsRepository extends Repository
{
    public function getEntityClass()
    {
        return News::class;
    }

    public function getTableName()
    {
        return "news";
    }
}
