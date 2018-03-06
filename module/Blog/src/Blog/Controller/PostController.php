<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 03-Mar-18
 * Time: 2:38 PM
 */

namespace Blog\Controller;


use Blog\Entity\Comment;
use Blog\Entity\Post;
use Blog\Entity\Tag;
use Blog\Form\PostForm;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;

class PostController extends AbstractActionController
{
    public function __construct()
    {
        session_start();
        $_SESSION['CKFinder']['authentication'] = true;
    }

    protected function getEntityManager()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        return $em;
    }

    public function indexAction()
    {
        $em = $this->getEntityManager();
        $page = (int)$this->params()->fromRoute('page', 1);
        $postRepository = $em->getRepository('\Blog\Entity\Post');
        $pagingConfig = array(
            'ItemCountPerPage' => 3,
            'CurrentPageNumber' => $page,
        );
        $posts = $postRepository->getAll($pagingConfig);

//        $ormPaging = new ORMPaginator($postRepository->createQueryBuilder('posts'));
        $ormPaging = new ORMPaginator($posts);
        $adapter = new DoctrineAdapter($ormPaging);

        $paging = new Paginator($adapter);
        $paging->setDefaultItemCountPerPage($pagingConfig['ItemCountPerPage']);
        $paging->setCurrentPageNumber($pagingConfig['CurrentPageNumber']);

        return new ViewModel(array('paging' => $paging));
    }

    public function tagAction()
    {
        $tag = $this->params()->fromRoute('tag');
        $tagEncode = str_replace("+", " ", $tag);
        $em = $this->getEntityManager();
        $page = (int)$this->params()->fromRoute('page', 1);
        $postRepository = $em->getRepository('\Blog\Entity\Post');
        $pagingConfig = array(
            'ItemCountPerPage' => 3,
            'CurrentPageNumber' => $page,
        );

        $posts = $postRepository->getPostByTag($tagEncode, $pagingConfig);
        $ormPaging = new ORMPaginator($posts);
        $adapter = new DoctrineAdapter($ormPaging);

        $paging = new Paginator($adapter);
        $paging->setDefaultItemCountPerPage($pagingConfig['ItemCountPerPage']);
        $paging->setCurrentPageNumber($pagingConfig['CurrentPageNumber']);

        return new ViewModel(array('paging' => $paging, 'tagEncode' => $tagEncode, 'tag' => $tag));
    }

    public function cateAction()
    {
        $cateId = $this->params()->fromRoute('id');

        $em = $this->getEntityManager();
        $page = (int)$this->params()->fromRoute('page', 1);
        $postRepository = $em->getRepository('\Blog\Entity\Post');
        $pagingConfig = array(
            'ItemCountPerPage' => 3,
            'CurrentPageNumber' => $page,
        );

        $cateRepository = $em->getRepository('\Blog\Entity\Category');
        $cate = $cateRepository->findOneBy(array('id' => $cateId));

        $posts = $postRepository->getPostByCateId($cateId, $pagingConfig);
        $ormPaging = new ORMPaginator($posts);
        $adapter = new DoctrineAdapter($ormPaging);

        $paging = new Paginator($adapter);
        $paging->setDefaultItemCountPerPage($pagingConfig['ItemCountPerPage']);
        $paging->setCurrentPageNumber($pagingConfig['CurrentPageNumber']);

        return new ViewModel(array('paging' => $paging, 'cate' => $cate));
    }

    public function addAction()
    {
        $sm = $this->getServiceLocator();
        $form = $sm->get('FormElementManager')->get('PostForm');

        $em = $this->getEntityManager();
        $cates = $em->getRepository('\Blog\Entity\Category')->findAll();

        $options = array();
        foreach ($cates as $cate) {
            $options[$cate->getId()] = $cate->getName();
        }
        $form->get('cate_id')->setValueOptions($options);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $dataInput = $request->getPost();

            $form->setData($dataInput);
            if ($form->isValid()) {
                $data = $form->getData();

                /*
                $em = $this->getEntityManager();
                $cate = $em->getRepository('\Blog\Entity\Category')->findOneBy(array('id' => $data['cate_id']));
                $newPost = new Post();
                $newPost->setStatus($dataInput['status']);
                $newPost->setInfo($dataInput['info']);
                $newPost->setContent($dataInput['content']);
                $newPost->setTitle($dataInput['title']);
                $current = date('Y-m-d H:i:s');
                $newPost->setDateCreated($current);
                $newPost->setCate($cate);

                $tags = explode(",", $data['tags']);
                foreach ($tags as $tagName) {
                    $tagName = trim($tagName);
                    $tag = $em->getRepository('\Blog\Entity\Tag')->findOneBy(array('name' => $tagName));
                    if (empty($tag)) {
                        $tag = new Tag();
                    }
                    $tag->setName($tagName);
                    $tag->addPost($newPost); // thêm post vào tag
                    $em->persist($tag);
                    $newPost->addTag($tag); // thêm post vào tag xong thì thêm ngược lại tag vào post. => cả 2 bảng đều có
                }
                $em->persist($newPost);
                $em->flush();
                */


                // đưa tất cả code xử lí vào postManager;
                $sm->get('PostManager')->addPost($dataInput);

                $this->flashMessenger()->addMessage('Thêm bài viết thành công !');
                $this->redirect()->toRoute('blog/post', array('action' => 'list'));
            }
        }

        return new ViewModel(array('form' => $form));
    }

    public function listAction()
    {
        $sm = $this->getServiceLocator();
        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
        $view->headScript()->appendFile($view->basePath() . "/js/script.js", 'text/javascript');

        $en = $this->getEntityManager();
        $posts = $en->getRepository('\Blog\Entity\Post')->findAll();

        $flash = $this->flashMessenger()->getMessages();

        return new ViewModel(array('posts' => $posts, 'flash' => $flash));
    }

    public function editAction()
    {
        $em = $this->getEntityManager();
        $sm = $this->getServiceLocator();
        $id = (int)$this->params()->fromRoute('id', 0);

        $post = $em->getRepository('\Blog\Entity\Post')->findOneBy(array('id' => $id));
        if (!$id OR empty($post)) {
            return $this->redirect()->toRoute('blog/post', array(
                'action' => 'list'
            ));
        }

//        $form = new PostForm();
        $form = $sm->get('FormElementManager')->get('PostForm');
        /*
        $arrTag = array();
        if(count($post->getTags())) {
            foreach ($post->getTags() as $tag) {
                $arrTag[] = $tag->getName();
            }
        }
        $strTag = implode(',', $arrTag);
*/
        $strTag = $sm->get('PostManager')->convertTagToString($post);
        // dữ liệu hiển thi jra form
        $dataPost = array(
            'title' => $post->getTitle(),
            'info' => $post->getInfo(),
            'content' => $post->getContent(),
            'status' => $post->getStatus(),
            'tags' => $strTag,
        );
        $form->setData($dataPost);

        //set category
        $cates = $em->getRepository('\Blog\Entity\Category')->findAll();
        $arrCate = array();
        foreach ($cates as $cate) {
            $arrCate[$cate->getId()] = $cate->getName();
        }
        $form->get('cate_id')->setValueOptions($arrCate);
        $form->get('cate_id')->setAttributes(array('value' => $post->getCate()->getId(), 'selected' => true));

        // đổi tên ntú submit
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();


                /*
                // gán các giá trị dât mới vào post cũ
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
                $tags = explode(",", $data['tags']);
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

                $em->persist($post);
                $em->flush();
                */
                $sm->get('PostManager')->editPost($post, $data);
                // Redirect to list of posts
                $this->flashMessenger()->addMessage("Sửa bài viết " . $post->getTitle() . " thành công !");
                return $this->redirect()->toRoute('blog/post', array('action' => 'list'));
            }
        }

        return new ViewModel(array('form' => $form, 'postId' => $id));
    }

    public function deleteAction()
    {

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('blog/post', array('action' => 'list'));
        }

        $em = $this->getEntityManager();
        $post = $em->getRepository('\Blog\Entity\Post')->findOneBy(array('id' => $id));
        $tags = $post->getTags();
        foreach ($tags as $tag) {
            $post->removeTag($tag);
        }
        $cmts = $post->getComments();
        foreach ($cmts as $cm){
            $em->remove($cm);
        }
        $em->remove($post);
        $em->flush();

        $this->flashMessenger()->addMessage("Xoá bài viết " . $post->getTitle() . " thành công !");
        return $this->redirect()->toRoute('blog/post', array('action' => 'list'));

    }

    public function readAction()
    {
        $sm = $this->getServiceLocator();
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('blog/post', array('action' => 'index'));
        }

        $em = $this->getEntityManager();
        $post = $em->getRepository('\Blog\Entity\Post')->findOneBy(array('id' => $id));

        $form = $sm->get('FormElementManager')->get('CommentForm');
        $form->get('submit')->setAttribute('value', 'Send');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $dataInput = $form->getData();

                $sm->get('PostManager')->addComment($post, $dataInput);
                /*
                $comment = new Comment();
                $comment->setEmail($dataInput['email']);
                $comment->setContent($dataInput['content']);
                $current = date('Y-m-d H:i:s');
                $comment->setDateCreated($current);
                $comment->setPost($post);
                $em->persist($comment);
                $em->flush();
                */

                //
                $urlTitle = $sm->get('ViewHelperManager')->get('Unicode')->make($post->getTitle());

                $this->flashMessenger()->addMessage("Comment bài viết " . $post->getTitle() . " thành công !");
                return $this->redirect()->toRoute('blog/post', array('action' => 'read', 'id' => $id,'title'=>$urlTitle));
            }
        }
        $flash = $this->flashMessenger()->getMessages();
        return new ViewModel(array('post' => $post, 'form' => $form, 'flash' => $flash));
    }
}