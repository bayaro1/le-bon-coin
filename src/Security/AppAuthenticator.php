<?php

namespace App\Security;

use Throwable;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\ErrorHandler\ThrowableUtils;
use App\Notification\EmailNotification\Auth2FAEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use App\Exception\AuthenticationException\Authentication2FAException;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'security_login';

    public const LAST_USERNAME = 'auth.last_username';

    public const LAST_PASSWORD = 'auth.last_password';

    private UrlGeneratorInterface $urlGenerator;

    private UserRepository $userRepository;

    private UserPasswordHasherInterface $hasher;

    private Auth2FAEmail $auth2FAEmail;

    private TokenGenerator $tokenGenerator;

    private EntityManagerInterface $em;


    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, UserPasswordHasherInterface $hasher, Auth2FAEmail $auth2FAEmail, TokenGenerator $tokenGenerator, EntityManagerInterface $em)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->auth2FAEmail = $auth2FAEmail;
        $this->tokenGenerator = $tokenGenerator;
        $this->em = $em;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->get('_username');
        $password = $request->get('_password', '');
        $token2FA = $request->get('_token2FA');

        $request->getSession()->set(self::LAST_USERNAME, $email);
        $request->getSession()->set(self::LAST_PASSWORD, $password);

        /**get user or throw exception */
        $user = $this->getUser($email);

        $this->verifyPassword($user, $password)
                ->verifyConfirmed($user)
                ->verify2FA($user, $token2FA)
                ;
        
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('_password')),
            [
                new RememberMeBadge(),
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('account_home_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }



    
    private function getUser(?string $email): User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if(!$user)
        {
            throw new AuthenticationException('Identifiants incorrects', 0);
        }
        return $user;
    }

    private function verifyPassword(User $user, string $password): self
    {
        if(!$this->hasher->isPasswordValid($user, $password))
        {
            throw new AuthenticationException('Identifiants incorrects', 0);
        }

        return $this;
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
