<?php
namespace App\Subscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogoutSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
    public function onLogout(LogoutEvent $event)
    {
        /** @var FlashBag */
        $flashBag = $event->getRequest()->getSession()->getBag('flashes');
        $flashBag->add('success', 'Vous avez bien été déconnecté !');
    }
}