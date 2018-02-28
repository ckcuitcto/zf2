<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 28-Feb-18
 * Time: 11:16 AM
 */

namespace Training\Model;


class Order
{
    public $id;
    public $user_id;
    public $total;
    public $detail;
    public $stamp;
    public $ship_name;
    public $ship_address;

    public function exchangeArray(array $data){
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        if(isset($data['user_id'])){
            $this->user_id = $data['user_id'];
        }
        if(isset($data['total'])){
            $this->total = $data['total'];
        }
        if(isset($data['detail'])){
            $this->detail = $data['detail'];
        }
        if(isset($data['stamp'])){
            $this->stamp = $data['stamp'];
        }
        if(isset($data['ship_name'])){
            $this->ship_name = $data['ship_name'];
        }
        if(isset($data['ship_address'])){
            $this->ship_address = $data['ship_address'];
        }
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }

}