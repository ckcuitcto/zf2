<?php
namespace Blog\Entity;
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 02-Mar-18
 * Time: 10:04 PM
 *
 * @ORM\Entity
 * @ORM\Table(name="categories")
 *
 */
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Blog\Entity\Post;
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(name="name")
     */
    protected $name;
    /**
     *  @ORM\OneToMany(targetEntity="\Blog\Entity\Post",mappedBy="cate")
     *  @ORM\JoinColumn(name="id",referencedColumnName="cate_id")
     */
    protected $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * set ID of this categories
     * @param int $id
     */
    public function setId($id){
        $this->id = $id;
    }
    /**
     * Return ID of this categories
     * @return integer
     */
    public function getId(){
        return $this->id;
    }
    /**
     * set Name of this categories
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
    }
    /**
     * Return Name of this categories
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    /**
     * Return Posts of this categories
     * @return array
     */
    public function getPosts(){
        return $this->posts;
    }
}