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

    public function countAllUser(){
        return $this->tableGateWay->select()->count();
    }

    public function listUserWithPaging(array $data){
        $row = $this->tableGateWay->select(function(Select $select) use ($data){
            $select->columns(array('id','username','level'))
                ->limit($data['ItemCountPerPage'])
                ->offset(($data['CurrentPageNumber'] - 1)*$data['ItemCountPerPage']);
        });
        return $row;
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

    public function resetPassword($user,$pass){
        $data = array();
        $data['password'] = $pass;
        $this->tableGateWay->update($data,array('username'=> $user));
    }
//  select * from users WHERE users.id NOT IN (SELECT user_id from sharings where file_id = 10)
    // public function getUsersNotYetSharedByFileId($fileId){
    //     $select = new Select('users');
    //     $select->where('users.id NOT IN ');

    //     $select->from(array('u' => 'users'))  // base table
    //     ->join(array('s' => 'sharings'),     // join table with alias
    //     'u.id = s.user_id')->where('user_id = ?');         // join expression
    //     $params = array($fileId);
    //     $select->exec($params);
    //     return $select;
    // }

    public function saveAccess($access, $userId){
        $data['access'] = $access;
        $this->tableGateWay->update($data,array('id'=>$userId));
    }
}