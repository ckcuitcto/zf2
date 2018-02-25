<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 24-Feb-18
 * Time: 10:27 AM
 */

namespace Training\Model;


use Zend\Db\TableGateway\TableGateway;

class FileTable
{
    protected $tableGateWay;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateWay = $tableGateway;
    }

    public function saveFile(File $file){
        $data = array(
            'label' => $file->label,
            'filename' => $file->filename,
            'user_id' => $file->user_id,
        );
        if(empty($file->id)){
            $this->tableGateWay->insert($data);
        }else{
            $this->tableGateWay->update($data,array('id' => $file->id));
        }
    }

    public function getFileByUserId($userId){
        return $this->tableGateWay->select(array('user_id' => $userId));

    }

    public function getFileById($id){
        $result = $this->tableGateWay->select(array('id' => $id));
        return $result->current();
    }

    public function deleteFileById($id){
        return $this->tableGateWay->delete(array('id' => $id));
    }
}