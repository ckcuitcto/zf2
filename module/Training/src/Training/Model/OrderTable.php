<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 28-Feb-18
 * Time: 11:19 AM
 */

namespace Training\Model;


use Zend\Db\TableGateway\TableGateway;

class OrderTable
{
    protected $tableGateWay;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateWay = $tableGateway;
    }

    public function saveOrder(Order $order){
        $data = array(
            'user_id' => $order->user_id,
            'total' => $order->total,
            'detail' => $order->detail,
            'ship_name' => $order->ship_name,
            'ship_address' => $order->ship_address,
        );

        $this->tableGateWay->insert($data);
        return $this->tableGateWay->getLastInsertValue();
    }

    public function getOrderById($orderId){
        $result = $this->tableGateWay->select(array('id' => $orderId));
        return $result->current();
    }

    public function getAllOrderByUserId($userId){
        return $this->tableGateWay->select(array('user_id' => $userId));
    }
}