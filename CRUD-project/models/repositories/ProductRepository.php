<?php

namespace app\models\repositories;

use app\models\entities\Product;
use app\models\Repository;

class ProductRepository extends Repository
{

    protected function getEntityClass() {
        return Product::class;
    }

    protected function getTableName()
    {
        return 'products';
    }
}