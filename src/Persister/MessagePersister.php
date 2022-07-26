<?php
namespace App\Persister;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class MessagePersister
{
    private ConversationRepository $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function persist(Message $message)
    {
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
                ;
    }

    private function senderSideConversation(Message $message)
    {
        $conversation = $this->conversationRepository->findByUserAndInterlocutor($message->getSender(), $message->getReceiver());
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
                            ;
        }
        return $conversation;
    }

    public function receiverSideConversation(Message $message)
    {
         $conversation = $this->conversationRepository->findByUserAndInterlocutor($message->getReceiver(), $message->getSender());
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
                         ;
         }
         return $conversation;
    }
}