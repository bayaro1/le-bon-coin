<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\SearchFilter;
use App\Service\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;

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
        if($searchFilter->getUser() !== null)
        {
            $qb->andWhere('p.user = :user')
                ->setParameter('user', $searchFilter->getUser())
                ;
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
