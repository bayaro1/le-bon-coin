<?php

namespace App\Subscriber;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Exception\AuthenticationException\Authentication2FAException;
use App\Notification\EmailNotification\Auth2FAEmail;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthSubscriber implements EventSubscriberInterface
{
    private Auth2FAEmail $auth2FAEmail;

    private RequestStack $requestStack;

    private EntityManagerInterface $em;
    
    private TokenGenerator $tokenGenerator;

    public function __construct(RequestStack $requestStack, Auth2FAEmail $auth2FAEmail, EntityManagerInterface $em, TokenGenerator $tokenGenerator)
    {
        $this->requestStack = $requestStack;
        $this->auth2FAEmail = $auth2FAEmail;
        $this->em = $em;
        $this->tokenGenerator = $tokenGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccessEvent',
        ];
    }


    /** 
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessEvent($event): void
    {
        /** @var User */
        $user = $event->getAuthenticationToken()->getUser();
        $this->verifyConfirmed($user)
            ->verify2FA($user, $this->requestStack->getMainRequest()->get('_token2FA'))
            ;
    }

    private function verifyConfirmed(User $user): self
    {
        if($user->getConfirmedAt() === null)
        {
            throw new AuthenticationException('Vous devez confirmer votre adresse e-mail', 0);
        }

        return $this;
    }

    private function verify2FA(User $user, ?string $token2FA)
    {
        if($user->isChoice2FA())
        {
            if($token2FA === null)
            {
                $user->setToken2FA($this->tokenGenerator->numeric(6));
                $this->em->flush();
                $this->auth2FAEmail->send($user);
                $message = 'Veuillez entrer le code qui vous a été envoyé par email';
            }
            elseif($token2FA !== $user->getToken2FA())
            {
                $message = 'Le code est incorrect';
            }
            elseif($token2FA === $user->getToken2FA())
            {
                $user->setToken2FA(null);
                return;
            }
            throw new Authentication2FAException($message, Authentication2FAException::ERROR_CODE);
        }
    }
}
