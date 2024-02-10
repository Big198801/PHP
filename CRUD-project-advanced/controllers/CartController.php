<?php

namespace app\controllers;

use app\model\entities\CartItem;
use app\model\entities\Order;
use app\model\entities\OrderItem;


class CartController extends Controller
{

    public function makeOrder($params) {

        \Db::beginTransaction();
        $userInfo = \Auth::getUserInfo();

        $cartItems = \Cart::getBaseQuery()->orderBy('id DESC')->get();
        if (count($cartItems) === 0) {
            return false;
        }


        $order = new Order(
            $params['name'], 
            preg_replace('/[^0-9]/', '', $params['phone']), 
            $params['address'], 
            (isset($userInfo)) ? $userInfo['userId'] : 0);
        
        \Orders::save($order);

        if (empty($order->id)) {
            \Db::rollBack();
            return false;
        }

        foreach ($cartItems as $item) {
            $orderItem = new OrderItem(
                $order->id,
                $item->product_id,
                $item->quantity,
                $item->product->price);
            \OrderItems::save($orderItem);
            if (empty($orderItem->id)) {
                \Db::rollBack();
                return false;
            }
            if (!\Cart::delete($item)) {
                \Db::rollBack();
                return false;
            }

        }

        \Db::commit();
        return $order;
    }


    public function actionIndex($params) {

        $paramTmpl = ['page_size' => \App::getConfig('pageSize')];

        if (isset($params['name']) && isset($params['phone']) && isset($params['address']) && \Request::getMethod() == 'POST') {
            if ($order = $this->makeOrder($params))
            {
                echo $this->render('newOrder', [
                    'id' => $order->id,
                    'uId' => $order->uId,
                    'date' => $order->date
                ]);
                return;
            } else {
                $paramTmpl['error'] = "Что-то пошло не так. Повторите попытку!";
            }
        }
        echo $this->render('cart', $paramTmpl);
    }


    public function actionApi($params) {
        $result = [];
        $query = \Cart::getBaseQuery()->orderBy('id DESC');

        if (isset($params['id'])) {
            $item = $query->find($params['id']);
        } elseif (isset($params['product_id'])) {
            $item = $query->where('product_id', $params['product_id'])->first();
        }
        switch ($params['action']) {
            case 'getItems': 
                echo $this->getJSONDynamicList($query, $params);
                return;

            case 'getCount':
                $result['result'] = 'ok';
                break;    

            case 'deleteItem': 
                if ($item) {
                    $item->quantity = 0;
                }
                break;

            case 'addItem': 
                if ($item) {
                    $item->quantity++;
                } elseif ($params['product_id']) {
                    $item = new CartItem($params['product_id']);
                    if ($item->product->id !== $params['product_id']) {
                        $item = null;
                    }
                };
                break;

            case 'subItem': 
                if ($item) {
                    $item->quantity--;
                }
                break;

            default: 
                $result['error'] = 'Не существующий метод';
        }

        if (empty($result['error']) && $item) {
            $res = ($item->quantity > 0) ? \Cart::save($item) : \Cart::delete($item);
            if ($res) {
                $result = [
                    'result' => 'ok',
                    'item' => $item->getDataFields(),
                ];
            } else {
                $result['error'] = 'error';
            }
        }
        
        if (empty($result['error'])) {
            $result['count'] = (int) \Cart::getBaseQuery()->sum('quantity');
        }

        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}