<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\Model;
use app\model\entities\Order;

class OrderRepository extends Repository
{

    
    protected $statuses = [
        'Новый',
        'Подтвержден',
        'Оплачен',
        'Выдан',
        'Отменен'
    ];

    public function getAvaliableStatuses() {
        return $this->statuses;
    }

    public function getEntityClass()
    {
        return Order::class;
    }

    protected function insert(Model $entity) {
        $entity->date = date("Y-m-d H:i:s", time());
        return parent::insert($entity);
    }     

    public function getTableName()
    {
        return "orders";
    }

    public function getOrderList($limit = 0, $offset = 0, $onlyCurUser = false) {
        $limit = (int) $limit;
        $offset = (int) $offset;
        $params = [];
        $isAdmin = \Auth::isAdmin();
        $userInfo = \Auth::getUserInfo();
        if ($userInfo) {
            $params['userId'] = $userInfo['userId']; 
        }
        $sql = "SELECT orders.uId, orders.id, date, status, SUM(order_items.quantity * order_items.price) as total FROM orders 
        JOIN order_items ON orders.id = order_items.orderId " 
        . ((!$isAdmin || $onlyCurUser) ? " WHERE (NOT userId = '0') AND userId = :userId " : "")
        . "GROUP BY orders.uId, orders.id, date, status
        ORDER BY date DESC LIMIT {$offset}, {$limit}";
        return $this->getDb()->queryAll($sql, $params);
    }
}
