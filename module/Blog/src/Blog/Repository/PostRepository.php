<?php

/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 05-Mar-18
 * Time: 9:05 PM
 */
namespace Blog\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function getAll(array $data){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('p')->from('\BLog\Entity\Post','p')
                            ->orderBy('p.id','DESC')
                            ->setMaxResults($data['ItemCountPerPage'])
                            ->setFirstResult(($data['CurrentPageNumber'] - 1)*$data['ItemCountPerPage']);
        $post = $qb->getQuery();
        return $post;
    }

    public function getPostByTag($tag,array $data){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')->from('\Blog\Entity\Post','p')
                            ->join('p.tags','t')
                            ->where('t.name = :name')
                            ->orderBy('p.id','DESC')
                            ->setParameter('name',$tag)
                            ->setMaxResults($data['ItemCountPerPage'])
                            ->setFirstResult(($data['CurrentPageNumber'] - 1)*$data['ItemCountPerPage']);
        $post = $qb->getQuery();
        return $post;
    }

    public function getPostByCateId($cate,array $data){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')->from('\Blog\Entity\Post','p')
            ->join('p.cate','c')
            ->where('c.id = :id')
            ->orderBy('p.id','DESC')
            ->setParameter('id',$cate)
            ->setMaxResults($data['ItemCountPerPage'])
            ->setFirstResult(($data['CurrentPageNumber'] - 1)*$data['ItemCountPerPage']);
        $post = $qb->getQuery();
        return $post;
    }
}