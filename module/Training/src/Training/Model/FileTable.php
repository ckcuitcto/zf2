<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 24-Feb-18
 * Time: 10:27 AM
 */

namespace Training\Model;


use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class FileTable
{
    protected $tableGateWay;
    protected $shareTableGateWay;

    public function __construct(TableGateway $tableGateway, TableGateway $shareTableGateWay)
    {
        $this->shareTableGateWay = $shareTableGateWay;
        $this->tableGateWay = $tableGateway;
    }

    public function saveFile(File $file)
    {
        $data = array(
            'label' => $file->label,
            'filename' => $file->filename,
            'user_id' => $file->user_id,
        );
        if (empty($file->id)) {
            $this->tableGateWay->insert($data);
        } else {
            $this->tableGateWay->update($data, array('id' => $file->id));
        }
    }

    public function getFileByUserId($userId)
    {
        return $this->tableGateWay->select(array('user_id' => $userId));

    }

    public function getFileById($id, $option = null)
    {
        if($option == 'withUser'){
            $result = $this->tableGateWay->select(function(Select $select) use ($id){
                $select->columns(array('id','label','user_id'))
                        ->where(array('files.id' => $id))
                        ->join('users', 'files.user_id = users.id',array('username','email'));
            });
        }else{
            $result = $this->tableGateWay->select(array('id' => $id));
        }
        return $result->current();
    }

    public function deleteFileById($id)
    {
        return $this->tableGateWay->delete(array('id' => $id));
    }

    public function saveShare($fileId, $userId)
    {
        $data = array(
            'file_id' => $fileId,
            'user_id' => $userId,
        );
        $this->shareTableGateWay->insert($data);
    }

    public function checkFileShared($fileId, $userId)
    {
        $rowSet = $this->shareTableGateWay->select(array('file_id' => $fileId, 'user_id' => $userId));
        if ($rowSet->current()) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserSharedByFileId($fileId)
    {
        $row = $this->shareTableGateWay->select(function (Select $select) use ($fileId) {
            $select->columns(array('file_id', 'id', 'stamp'))
                ->where(array('sharings.file_id' => $fileId))
                ->join('users', 'sharings.user_id = users.id', array('username'));
        });
        return $row;
    }

    public function removeShareById($id)
    {
        return $this->shareTableGateWay->delete(array('id' => $id));
    }

    // public function getOwnerOfFileByFileId($fileId){
    //     $row = $this->shareTableGateWay->select(function(Select $select) use ($fileId){
    //         $select->columns(array('file_id'))
    //         ->where(array('sharings.file_id' => $fileId))
    //         ->join('files','sharings.file_id = files.id',array('user_id'));
    //     });
    //     return $row;
    // }

    public function getSharingById($id)
    {
        $result = $this->shareTableGateWay->select(array('id' => $id));
        return $result->current();
    }

    public function getAllSharedByUserId($userId)
    {
        $result = $this->shareTableGateWay->select(function (Select $select) use ($userId) {
            $select->columns(array('stamp'))
                ->where(array('sharings.user_id' => $userId))
                ->join('files', 'sharings.file_id = files.id', array('id', 'label'))
                ->join('users', 'files.user_id = users.id', array('username'));
        });
        return $result;
    }

    public function getLastFileUpload($userId, $number = 10)
    {
        $result = $this->tableGateWay->select(function(Select $select) use ($userId,$number){
            $select->columns(array('id','label','user_id'))
                    ->where->notEqualTo('user_id',$userId);
            $select->order('id desc')->limit($number);
            $select->join('users', 'files.user_id = users.id', array('username'));
        });
        // sử dụng resultSet để ép về kiểu file, nên khi xuất ra kiểu file thì k hiểu username là gì
        // muốn nó hiểu thì phải vào File model để thêm vào cột username
        return $result;
    }
}