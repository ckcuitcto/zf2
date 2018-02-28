<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 27-Feb-18
 * Time: 2:03 PM
 */
namespace Training\Model;
class Book{
    public $id;
    public $name;
    public $info;
    public $img;
    public $author;
    public $price;

    public function exchangeArray($data){
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        if(isset($data['name'])){
            $this->name = $data['name'];
        }
        if(isset($data['info'])){
            $this->info = $data['info'];
        }
        if(isset($data['img'])){
            $this->img = $data['img'];
        }
        if(isset($data['author'])){
            $this->author = $data['author'];
        }
        if(isset($data['price'])){
            $this->price = $data['price'];
        }
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }
}