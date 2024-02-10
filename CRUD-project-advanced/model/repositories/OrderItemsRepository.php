<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\OrderItem;

class OrderItemsRepository extends Repository
{

    public function getEntityClass()
    {
        return OrderItem::class;
    }

    public function getTableName()
    {
        return "order_items";
    }

}
