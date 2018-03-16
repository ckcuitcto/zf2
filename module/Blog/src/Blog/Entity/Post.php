<?php
namespace Blog\Entity;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="\Blog\Repository\PostRepository")
 * @ORM\Table(name="posts")
 *
 */
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Blog\Entity\Category;

class Post
{
    const STATUS_UNCHECK = 1;
    const STATUS_CHECK = 2;
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(name="title")
     */
    protected $title;
    /**
     * @ORM\Column(name="info")
     */
    protected $info;
    /**
     * @ORM\Column(name="content")
     */
    protected $content;
    /**
     * @ORM\Column(name="status")
     */
    protected $status;
    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;
    /**
     * @ORM\ManyToOne(targetEntity="\Blog\Entity\Category",inversedBy="posts")
     * @ORM\JoinColumn(name="cate_id",referencedColumnName="id")
     */
    protected $cate;
    /**
     * @ORM\ManyToMany(targetEntity="\Blog\Entity\Tag", inversedBy="posts")
     * @ORM\JoinTable(name="post_tag",
     *              joinColumns= {@ORM\JoinColumn(name="post_id",referencedColumnName ="id")},
     *              inverseJoinColumns={@ORM\JoinColumn(name="tag_id",referencedColumnName ="id")}
     *     )
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="\Blog\Entity\Comment",mappedBy="post")
     * @ORM\JoinColumn(name="id",referencedColumnName="post_id")
     *
     */

    protected $comments;
    public function __construct()
    {
        $this->cate = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * set ID of this posts
     * @param int $id
     */
    public function setId($id){
        $this->id = $id;
    }
    /**
     * Return ID of this posts
     * @return integer
     */
    public function getId(){
        return $this->id;
    }
    /**
     * set Title of this posts
     * @param string $title
     */
    public function setTitle($title){
        $this->title = $title;
    }
    /**
     * Return Title of this posts
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }
    /**
     * set Info of this posts
     * @param string $info
     */
    public function setInfo($info){
        $this->info = $info;
    }
    /**
     * Return Info of this posts
     * @return string
     */
    public function getInfo(){
        return $this->info;
    }

    /**
     * set Content of this posts
     * @param string $content
     */
    public function setContent($content){
        $this->content = $content;
    }
    /**
     * Return Info of this posts
     * @return string
     */
    public function getContent(){
        return $this->content;
    }

    /**
     * set Status of this posts
     * @param integer $status
     */
    public function setStatus($status){
        $this->status = $status;
    }
    /**
     * Return status of this posts
     * @return integer
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * set the date when this post was created
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated){
        $this->dateCreated = $dateCreated;
    }
    /**
     * Return the date when this post was created
     * @return string
     */
    public function getDateCreated(){
        return $this->dateCreated;
    }
    /**
     * Return Cate of this post
     * @return array
     */
    public function getCate(){
        return $this->cate;
    }
    /**
     * set the Cate 
     * @param Category $cate
     */
    public function setCate(Category $cate){
        $this->cate = $cate;
    }

    public function getTags(){
        return $this->tags;
    }

    public function addTag($tag){
        $this->tags[] = $tag;
    }
    public function removeTag($tag){
        $this->tags->removeElement($tag);
    }

    public function getComments(){
        return $this->comments;
    }

}