<?php
namespace App\Persister;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

use function PHPUnit\Framework\throwException;

class MessagePersister
{
    private ConversationRepository $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function persist(Message $message)
    {
        if($message->getSender() === $message->getReceiver())
        {
            throw new \Exception("Vous ne pouvez pas vous envoyer un message à vous même", 1);
        }
        $message->setSentAt(new DateTimeImmutable());

        $this->conversationRepository->add($this->senderSideConversation($message));
        $this->conversationRepository->add($this->receiverSideConversation($this->copyMessage($message)), true);  //copyMessage permet que deux messages différents soient enregistrés, un pour chaque conversation.
    }

    private function copyMessage(Message $message):Message
    {
        $message2 = new Message;
        return $message2->setSender($message->getSender())
                ->setReceiver($message->getReceiver())
                ->setContent($message->getContent())
                ->setSentAt($message->getSentAt())
                ->setProduct($message->getProduct())
                ;
    }

    private function senderSideConversation(Message $message)
    {
        $conversation = $this->conversationRepository->findOneOrNull($message->getSender(), $message->getReceiver(), $message->getProduct());
        if($conversation)
        {
            $conversation->addMessage($message)
                                ->setUpdatedAt(new DateTimeImmutable())
                                ;
        }
        else
        {
            $conversation = new Conversation;
            $conversation->setUser($message->getSender())
                            ->setInterlocutor($message->getReceiver())
                            ->addMessage($message)
                            ->setUpdatedAt(new DateTimeImmutable())
                            ->setProduct($message->getProduct())
                            ;
        }
        return $conversation;
    }

    public function receiverSideConversation(Message $message)
    {
         $conversation = $this->conversationRepository->findOneOrNull($message->getReceiver(), $message->getSender(), $message->getProduct());
         if($conversation)
         {
             $conversation->addMessage($message)
                                 ->setUpdatedAt(new DateTimeImmutable())
                                 ;
         }
         else
         {
             $conversation = new Conversation;
             $conversation->setUser($message->getReceiver())
                         ->setInterlocutor($message->getSender())
                         ->addMessage($message)
                         ->setUpdatedAt(new DateTimeImmutable())
                         ->setProduct($message->getProduct())
                         ;
         }
         return $conversation;
    }
}