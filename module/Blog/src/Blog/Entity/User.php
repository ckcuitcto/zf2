<?php
namespace Blog\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="users")
*/
class User{
	/**
	* @ORM\Id
	* @ORM\Column(name="id")
	* @ORM\GeneratedValue
	*/
	protected $id;
	/**
	* @ORM\Column(name="username")
	*/
	protected $username;

	/**
	* @ORM\Column(name="password")
	*/
	protected $password;
	/**
	* @ORM\Column(name="email")
	*/
	protected $email;
	/**
	* @ORM\Column(name="name")
	*/
	protected $name;
	/**
	* @ORM\Column(name="level")
	*/
	protected $level;
	/**
	* @ORM\Column(name="access")
	*/
	protected $access;
	/**
	* set ID of this users
	* @param int $id
	*/
	public function setId($id){
		$this->id=$id;
	}
	/**
	* Return ID of this users
	* @return integer
	*/
	public function getId(){
		return $this->id;
	}
	/**
	* set username of this users
	* @param string $user
	*/
	public function setUsername($user){
		$this->username=$user;
	}
	/**
	* Return username of this users
	* @return string
	*/
	public function getUsername(){
		return $this->username;
	}		
	/**
	* set Name of this users
	* @param string $name
	*/
	public function setName($name){
		$this->name=$name;
	}
	/**
	* Return Name of this users
	* @return string
	*/
	public function getName(){
		return $this->name;
	}	
	/**
	* set Email of this users
	* @param string $email
	*/
	public function setEmail($email){
		$this->email=$email;
	}
	/**
	* Return Email of this users
	* @return string
	*/
	public function getEmail(){
		return $this->email;
	}	
	/**
	* set password of this users
	* @param string $pass
	*/
	public function setPassword($pass){
		$this->password=$pass;
	}
	/**
	* Return password of this users
	* @return string
	*/
	public function getPassword(){
		return $this->password;
	}
	/**
	* set level of this users
	* @param int $lv
	*/
	public function setLevel($lv){
		$this->level=$lv;
	}
	/**
	* Return level of this users
	* @return integer
	*/
	public function getLevel(){
		return $this->level;
	}	
	/**
	* set access of this users
	* @param string $access
	*/
	public function setAccess($access){
		$this->access=$access;
	}
	/**
	* Return access of this users
	* @return string
	*/
	public function getAccess(){
		return $this->access;
	}		
}