<?php
namespace App\Subscriber;

use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class MyEventsSubscriber implements EventSubscriberInterface
{
    private CartService $cartService;

    private Security $security;

    public function __construct(CartService $cartService, Security $security)
    {
        $this->cartService = $cartService;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogout',
            LoginSuccessEvent::class => 'onLogin', 
            KernelEvents::REQUEST => 'onRequest'
        ];
    }
    public function onLogout(LogoutEvent $event)
    {
        /** @var FlashBag */
        $flashBag = $event->getRequest()->getSession()->getBag('flashes');
        $flashBag->add('success', 'Vous avez bien été déconnecté !');
    }
    public function onLogin(LoginSuccessEvent $event)
    {
        /** @var FlashBag */
        $flashBag = $event->getRequest()->getSession()->getBag('flashes');
        $flashBag->add('success', 'Vous êtes connecté !');
        $this->cartService->initialize();
    }
    public function onRequest(KernelEvent $event)
    {
        if($this->security->getUser())
        {
            $this->cartService->register();
        }
    }
}