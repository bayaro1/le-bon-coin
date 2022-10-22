<?php

namespace App\Repository;

use App\Entity\Product;
use App\Service\Paginator;
use Doctrine\ORM\QueryBuilder;
use App\DataModel\SearchFilter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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
    private Paginator $paginator;

    private PictureRepository $pictureRepository;

    public function __construct(ManagerRegistry $registry, Paginator $paginator, PictureRepository $pictureRepository)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
        $this->pictureRepository = $pictureRepository;
    }

    public function findFiltered(SearchFilter $searchFilter, int $offset = 0, int $limit = 10)
    {
        $qb = $this->createQueryBuilder('p')
                    ->select('p', 'c')
                    ->join('p.category', 'c')
                    ->orderBy('p.createdAt', 'desc')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ;
        
        $this->applyFilters($qb, $searchFilter);
        $products = $qb->getQuery()
                    ->getResult()
                    ;
        $this->hydrateWithFirstPicture($products);
        return $products;
    }

    public function countFiltered(SearchFilter $searchFilter)
    {
        $qb = $this->createQueryBuilder('p')
                    ->select('p.id')
                    ->join('p.category', 'c')
                    ;

        $this->applyFilters($qb, $searchFilter);
        $result = $qb->getQuery()
                    ->getResult()
                    ;
        return count($result);
    }


    public function findPaginatedFiltered(Request $request, SearchFilter $searchFilter, ?int $perPage = 5):Paginator
    {
        $this->paginator->configure($request, $this->countQuery($searchFilter), $this->findFilteredQuery($searchFilter), $perPage);
        $this->hydrateWithFirstPicture($this->paginator->getItems());
        return $this->paginator;
    }

    public function countQuery(SearchFilter $searchFilter)
    {
        $qb = $this->createQueryBuilder('p')
                    ->select('p.id')
                    ->join('p.category', 'c')
                    ;

        $this->applyFilters($qb, $searchFilter);
        return $qb->getQuery();
    }

    public function findFilteredQuery(SearchFilter $searchFilter)
    {
        $qb = $this->createQueryBuilder('p')
                    ->select('p', 'c')
                    ->join('p.category', 'c')
                    ->orderBy('p.createdAt', 'desc')
                    ;
        
        $this->applyFilters($qb, $searchFilter);
        return $qb->getQuery();
    }

    private function applyFilters(QueryBuilder $qb, SearchFilter $searchFilter)
    {
        if($searchFilter->category !== null)
        {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $searchFilter->category)
                ;
        }
        if($searchFilter->city !== null)
        {
            $qb->andWhere('p.city = :city')
                ->setParameter('city', $searchFilter->city)
                ;
        }
        if($searchFilter->qSearch !== null)
        {
            $qb->andWhere('p.title LIKE :q OR c.name LIKE :q')
                ->setParameter('q', '%'.$searchFilter->qSearch.'%')
                ;
        }
        if($searchFilter->getSortField() !== null)
        {
            $qb->orderBy('p.' . $searchFilter->getSortField(), $searchFilter->getSortOrder());
        }

    }

    /**
     * @param Product[]
     */
    public function hydrateWithFirstPicture($products):void 
    {
        $picturesByProductId = $this->pictureRepository->findByProducts($products);
        foreach($products as $product)
        {
            if(array_key_exists($product->getId(), $picturesByProductId))
            {
                $product->setFirstPicture($picturesByProductId[$product->getId()]);
            }
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
