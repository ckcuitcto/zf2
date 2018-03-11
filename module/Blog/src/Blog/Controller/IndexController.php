<?php
namespace Blog\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends MainController {
	public function indexAction(){

        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
//        $em = $this->getServiceLocator()->get(\Doctrine\ORM\EntityManager::class);
	    $cate = $em->getRepository('\Blog\Entity\Category')->findOneBy(array('id' => 1));
	    echo $cate->getName();
	    $posts = $cate->getPosts();

        foreach ($posts as $value)
        {
            echo "<pre>";
            print_r($value->getTitle());
            echo "</pre>";
        }

	    echo "<h1> Blog Module- Index ACtion </h1>";
		return new ViewModel;
	}

	public function index2Action(){
	    echo '11';
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $post = $em->getRepository('\Blog\Entity\Post')->findOneBy(array('id' => 1));
//        echo $post->getTitle();
//        $vd = $post[0];
        echo "<pre>";
        print_r($post->getCate()->getName());
        echo "</pre>";
        return false;
    }

    public function index3Action(){
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('p')->from('Blog\Entity\Post','p')
                    ->where('p.id != :id')
                    ->orderBy('p.id','DESC')
                    ->setParameter('id',1);
        $posts = $queryBuilder->getQuery()->getResult();
        foreach ($posts as $value)
        {
            echo "<pre>";
            print_r($value->getTitle());
            echo "</pre>";
        }
        return false;

    }

    // dung query builder
    //select
    public function index4Action(){
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('p.title,p.id,c.name')->from('Blog\Entity\Post','p')
            ->join('p.cate','c') // join vs cate đặt tên cho nó là c
            ->where('p.id != :id')
            ->orderBy('p.id','DESC')
            ->setParameter('id',1);
        $posts = $queryBuilder->getQuery()->getResult();
        foreach ($posts as $value)
        {
            echo "<pre>";
            print_r($value);
            echo "</pre>";
        }
        return false;
    }


    //them
    public function index5Action(){
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $cate = $em->getRepository('\Blog\Entity\Category')->findOneBy(array('id' => 4));

        $post = new \Blog\Entity\Post();
        $post->setTitle('test1');
        $post->setContent('content test add');
        $post->setInfo('info test add');
        $post->setStatus(1);
        $current = date('Y-m-d H:i:s');
        $post->setDateCreated($current);
        $post->setCate($cate);

        $em->persist($post);
        $em->flush();
        echo "done";
        return false;
    }


    //sua
    public function index6Action(){
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $post = $em->getRepository('\Blog\Entity\Post')->findOneBy(array('id' =>6));

//        $post = new \Blog\Entity\Post();
        $post->setTitle('test22222222222222');
        $post->setContent('content test add222222222');
        $post->setInfo('info test add2222');
        $post->setStatus(2);

        $em->persist($post);
        $em->flush();
        echo "done";
        return false;
    }

    //remove
    public function index7Action(){
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $post = $em->getRepository('\Blog\Entity\Post')->findOneBy(array('id' =>7));

        $em->remove($post);

        $em->flush();
        echo "done";
        return false;
    }
}