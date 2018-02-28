<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 27-Feb-18
 * Time: 2:06 PM
 */

namespace Training\Model;


use Zend\Db\TableGateway\TableGateway;

class BookTable
{
    protected $tableGateWay;

    public function __construct(TableGateway $tableGateWay)
    {
        $this->tableGateWay = $tableGateWay;
    }

    public function fetchAll(){
        return $this->tableGateWay->select();
    }

    public function getBookById($id){
        $result = $this->tableGateWay->select(array('id' => $id));
        return $result->current();
    }

}