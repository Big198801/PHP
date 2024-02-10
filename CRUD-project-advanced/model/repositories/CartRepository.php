<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\CartItem;

class CartRepository extends Repository
{
    protected $systemProps = [
        'user_id',
        'session_id',
    ];

    public function getEntityClass()
    {
        return CartItem::class;
    }

    public function getTableName()
    {
        return "cart";
    }

    public function getBaseQuery() {
        $userInfo = \Auth::getUserInfo();
        if (isset($userInfo) && $userInfo['userId']) {
            $query = $this->newQuery()->where('user_id', $userInfo['userId']);
        } else {
            $query = $this->newQuery()->where('session_id', \Session::getId()); 
        } 

        return $query->where('quantity','>',0);
    }

}
