<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Article[] Returns an array of Article objects
    */
   public function findByReferenceAndSize($ref, $size): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.ref = :ref')
           ->andWhere('a.size = :size')
           ->setParameter('ref', $ref)
           ->setParameter('size', $size)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneByParams($ref, $size, $color): ?Article
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.ref = :ref')
           ->andWhere('a.size = :size')
           ->andWhere('a.color = :color')
           ->setParameter('ref', $ref)
           ->setParameter('size', $size)
           ->setParameter('color', $color)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
