<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    private ProductRepository $productRepository;

    public function __construct(ManagerRegistry $registry, ProductRepository $productRepository)
    {
        parent::__construct($registry, Conversation::class);
        $this->productRepository = $productRepository;
    }

    public function countNewByUser(?User $user)
    {
        if(!$user)
        {
            return null;
        }
        $count = count(
            $this->createQueryBuilder('c')
                    ->select('c.id')
                    ->where('c.user = :user AND c.hasNewMessage = true')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getResult()
        ); 
        return $count ?: null;
    }

    /**
     * @param User $user
     * @param User $interlocutor
     * @return Conversation|null
     */
    public function findOneOrNull(User $user, User $interlocutor, Product $product)
    {
        return $this->createQueryBuilder('c')
                    ->where('c.user = :user AND c.interlocutor = :interlocutor AND c.product = :product')
                    ->setParameter('user', $user)
                    ->setParameter('interlocutor', $interlocutor)
                    ->setParameter('product', $product)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }

    public function findByUser(User $user)
    {
        $conversations = $this->createQueryBuilder('c')
                    ->select('c', 'p')
                    ->join('c.product', 'p')
                    ->where('c.user = :user')
                    ->setParameter('user', $user)
                    ->orderBy('c.updatedAt', 'DESC')
                    ->getQuery()
                    ->getResult()
                    ;
        $products = [];
        foreach($conversations as $conversation)
        {
            $products[] = $conversation->getProduct();
        }
        $this->productRepository->hydrateWithFirstPicture($products);

        return $conversations;
    }

    public function add(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Conversation[] Returns an array of Conversation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conversation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
