<?php
namespace App\Subscriber;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class ProductSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function getSubscribedEvents()
    {
        return [
            Events::prePersist => 'prePersist'
        ];
    }
    public function prePersist(LifecycleEventArgs $lifecycleEventArgs)
    {
        $object = $lifecycleEventArgs->getObject();
        if(! $object instanceof Product)
        {
            return;
        }
        $object->setUser($this->security->getUser());
    }
}