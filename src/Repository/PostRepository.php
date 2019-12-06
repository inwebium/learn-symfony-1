<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    
    public function findLatest($limit = 6)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCommented($limit = 6)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('count(c) AS HIDDEN ncomments')
            ->andWhere('c.createdAt >= :dateCommented')
            ->leftJoin('p.comments', 'c')
            ->groupBy('p')
            ->orderBy('ncomments', 'DESC')
            ->addOrderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(['dateCommented' => new \DateTime('today')])
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function getPage($pageNumber = 1, $limit = 6): \Doctrine\ORM\Tools\Pagination\Paginator
    {
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($pageNumber - 1) * $limit)
            ->setMaxResults($limit)
        ;
        
        return new \Doctrine\ORM\Tools\Pagination\Paginator($query);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
