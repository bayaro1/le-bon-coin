<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\SearchFilter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function countQuery(SearchFilter $searchFilter)
    {
        $qb = $this->createQueryBuilder('p')
                    ->select('p.id');

        $this->filter($qb, $searchFilter);
        return $qb->getQuery();
    }

    public function findFilteredQuery(SearchFilter $searchFilter)
    {
        $qb = $this->createQueryBuilder('p')
                    ->select('p', 'c', 'pics')
                    ->join('p.category', 'c')
                    ->leftJoin('p.pictures', 'pics')
                    ->orderBy('p.createdAt', 'desc')
                    ;
        
        $this->filter($qb, $searchFilter);
        return $qb->getQuery();
    }

    private function filter(QueryBuilder $qb, SearchFilter $searchFilter)
    {
        if($searchFilter->getCategory() !== null)
        {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $searchFilter->getCategory())
                ;
        }
        if($searchFilter->getCity() !== null)
        {
            $qb->andWhere('p.city = :city')
                ->setParameter('city', $searchFilter->getCity())
                ;
        }
        if($searchFilter->getQSearch() !== null)
        {
            $qb->andWhere('p.title LIKE :q OR c.name LIKE :q')
                ->setParameter('q', '%'.$searchFilter->getQSearch().'%')
                ;
        }

    }


    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
