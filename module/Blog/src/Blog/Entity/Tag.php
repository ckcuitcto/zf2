<?php
namespace Blog\Entity;
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 02-Mar-18
 * Time: 10:04 PM
 *
 * @ORM\Entity
 * @ORM\Table(name="tags")
 *
 */
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
class Tag
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
     * @ORM\ManyToMany(targetEntity="Blog\Entity\Post",mappedBy="tags")
     */

    protected $posts;
    public function __construct()
    {
        $this->posts = new ArrayCollection();

    }

    /**
     * set ID of this tag
     * @param int $id
     */
    public function setId($id){
        $this->id = $id;
    }
    /**
     * Return ID of this tag
     * @return integer
     */
    public function getId(){
        return $this->id;
    }
    /**
     * set Name of this tag
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
    }
    /**
     * Return Name of this tag
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    public function getPosts(){
        return $this->posts;
    }

    public function addPost($post){
        $this->posts[] = $post;
    }
}