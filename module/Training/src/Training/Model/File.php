<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 24-Feb-18
 * Time: 10:25 AM
 */

namespace Training\Model;


class File
{
    public $id;
    public $label;
    public $filename;
    public $user_id;

    public function exchangeArray($data){
        if(isset($data['id'])){
            $this->id = $data['id'];
        }

        if(isset($data['label'])){
            $this->label = $data['label'];
        }

        if(isset($data['filename'])){
            $this->filename = $data['filename'];
        }
        if(isset($data['user_id'])){
            $this->user_id = $data['user_id'];
        }
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }
}