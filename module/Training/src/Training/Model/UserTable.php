<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 18-Feb-18
 * Time: 12:01 AM
 */

namespace Training\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UserTable{
    protected $tableGateWay;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateWay = $tableGateway;
    }

    public function fetchAll($paging = false){
        if($paging == true) {
            $select = new Select('users');
            $result = new ResultSet();
            $result->setArrayObjectPrototype(new User());
            $config = new DbSelect($select, $this->tableGateWay->getAdapter(), $result);
            $resultData = new Paginator($config);
            return $resultData;
        }else{
            return $this->tableGateWay->select();
        }
    }

    public function saveUser(User $user){
        $data = array(
            'username' => $user->username,
            'email' => $user->email,
            'level' => $user->level,
            'name' => $user->name,
        );
        if(!empty($user->password )){
            $data['password'] = $user->password;
        }
        if($user->id == 0){
            $this->tableGateWay->insert($data);
        }else{
            $this->tableGateWay->update($data,array('id' => $user->id));
        }
    }

    public function getUserById($id){
        $rowSet = $this->tableGateWay->select(array('id' => $id));
        $row = $rowSet->current();
        return $row;
    }

    public function deleteUserById($id){
        $this->tableGateWay->delete(array('id'=> $id));
    }

    public function getUserByUsername($username){
        $rowSet = $this->tableGateWay->select(array('username' => $username));
        $row = $rowSet->current();
        return $row;
    }

    public function getUserByEmail($email){
        $rowSet = $this->tableGateWay->select(array('email' => $email));
        $row = $rowSet->current();
        return $row;
    }
}