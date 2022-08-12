<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\GroupBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /** 
     * @param Conversation[]
     */
    public function findByConversations($conversations)
    {
        $qb = $this->createQueryBuilder('m');
        $lastMessages = $qb
                            ->select('m')
                            ->where(
                                $qb->expr()->in(
                                    'm.sentAt', 
                                    $this->createQueryBuilder('m2')
                                            ->select('MAX(m2.sentAt)')
                                            ->where('m2.conversation IN (:conversations)')
                                            ->groupBy('m2.conversation')
                                            ->getDQL()
                                )
                            )
                            ->groupBy('m.conversation')
                            ->setParameter('conversations', $conversations)
                            ->getQuery()
                            ->getResult()
                            ;

        $lastMessagesByConversationId = [];
        foreach($lastMessages as $lastMessage)
        {
            $lastMessagesByConversationId[$lastMessage->getConversation()->getId()] = $lastMessage;
        }
        return $lastMessagesByConversationId;
    }

    public function add(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
