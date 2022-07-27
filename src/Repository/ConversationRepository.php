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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
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

    public function findAllByUser(User $user)
    {
        return $this->createQueryBuilder('c')
                    ->select('c', 'm')
                    ->leftJoin('c.messages', 'm')
                    ->where('c.user = :user')
                    ->setParameter('user', $user)
                    ->orderBy('c.updatedAt', 'DESC')
                    ->getQuery()
                    ->getResult()
                    ;
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
