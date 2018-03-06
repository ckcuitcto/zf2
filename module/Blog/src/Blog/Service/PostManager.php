<?php

/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 06-Mar-18
 * Time: 2:24 PM
 */

namespace Blog\Service;

use Blog\Entity\Comment;
use Blog\Entity\Post;
use Blog\Entity\Tag;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class PostManager implements ServiceManagerAwareInterface
{
    private $sm;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
        // TODO: Implement setServiceManager() method.
    }

    public function getServiceLocator()
    {
        return $this->sm;
    }

    protected function getEntityManager()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        return $em;
    }

    public function addPost($data)
    {
        $em = $this->getEntityManager();
        $cate = $em->getRepository('\Blog\Entity\Category')->findOneBy(array('id' => $data['cate_id']));

        $newPost = new Post();
        $newPost->setStatus($data['status']);
        $newPost->setInfo($data['info']);
        $newPost->setContent($data['content']);
        $newPost->setTitle($data['title']);
        $current = date('Y-m-d H:i:s');
        $newPost->setDateCreated($current);
        $newPost->setCate($cate);
        $em->persist($newPost);
        $this->addTagToPost($data['tags'],$newPost);
        $em->flush();
    }

    public function addTagToPost($tagStr, $post)
    {
        $em = $this->getEntityManager();
        $tags = explode(",", $tagStr);
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            $tag = $em->getRepository('\Blog\Entity\Tag')->findOneBy(array('name' => $tagName));
            if (empty($tag)) {
                $tag = new Tag();
            }
            $tag->setName($tagName);
            $tag->addPost($post); // thêm post vào tag
            $em->persist($tag);
            $post->addTag($tag); // thêm post vào tag xong thì thêm ngược lại tag vào post. => cả 2 bảng đều có
        }
    }

    public function editPost(Post $post,$data){
        $em = $this->getEntityManager();

        $cate = $em->getRepository('\Blog\Entity\Category')->findOneBy(array('id' => $data['cate_id']));
        $post->setStatus($data['status']);
        $post->setInfo($data['info']);
        $post->setContent($data['content']);
        $post->setTitle($data['title']);
        $post->setCate($cate);

        // lấy ra các tag cũ và xoá nó
        $tags = $post->getTags();
        foreach ($tags as $tag){
            $post->removeTag($tag);
        }

        // lấy tag mới và lưu lại
        $this->addTagToPost($data['tags'],$post);
        $em->persist($post);
        $em->flush();

    }

    public function convertTagToString($post){
        $arrTag = array();
        if(count($post->getTags())) {
            foreach ($post->getTags() as $tag) {
                $arrTag[] = $tag->getName();
            }
        }
        $strTag = implode(',', $arrTag);

        return $strTag;
    }

    public function addComment($post,$data){
        $em = $this->getEntityManager();

        $comment = new Comment();
        $comment->setEmail($data['email']);
        $comment->setContent($data['content']);
        $current = date('Y-m-d H:i:s');
        $comment->setDateCreated($current);
        $comment->setPost($post);

        $em->persist($comment);
        $em->flush();
    }


}