<?php 
namespace App\Controller;

use App\Entity\PasswordInit;
use App\Entity\User;
use App\Exception\AuthenticationException\Authentication2FAException;
use App\Form\LoginType;
use App\Form\PasswordInitType;
use App\Form\RegistrationFormType;
use App\Notification\EmailNotification\PasswordInitEmail;
use App\Notification\EmailNotification\WelcomeEmail;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Service\CartService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private UserRepository $userRepository;

    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    #[Route('/connexion', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils, FormFactoryInterface $formFactoryInterface, Request $request): Response
    {
        dump($request->getSession()->get(AppAuthenticator::LAST_PASSWORD));
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $formFactoryInterface->createNamed('', LoginType::class, null, [
            'choice2FA' => $error instanceof Authentication2FAException,
            'lastUsername' => $request->getSession()->get(AppAuthenticator::LAST_USERNAME),
            'lastPassword' => $request->getSession()->get(AppAuthenticator::LAST_PASSWORD)
        ]);

        if($error)
        {
            if($error instanceof Authentication2FAException)
            {
                $form->get('_token2FA')->addError(new FormError($error->getMessage()));
            }
            else
            {
                $form->get('_password')->addError(new FormError($error->getMessage()));
            }
        }

        dump($form->createView());
        return $this->render('security/login.html.twig', [
            'current_menu' => 'login',
            'form' => $form->createView()
        ]);
    }

    #[Route('/déconnexion', name: 'security_logout')]
    public function logout():void
    {
        
    }

    #[Route('/inscription', name: 'security_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, WelcomeEmail $welcomeEmail, TokenGeneratorInterface $tokenGenerator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPlainPassword()
                )
            )
                ->setConfirmationToken($tokenGenerator->generateToken())
                ;

            $entityManager->persist($user);
            $entityManager->flush();
            
            $welcomeEmail->send($user);
            $this->addFlash('success', 'Un email de confirmation vous a été envoyé ! Veuillez cliquer sur le lien pour valider votre compte.');
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('vérification-du-compte', name: 'security_confirmAccount')]
    public function confirmAccount(Request $request): Response
    {
        /** @var User */
        $user = $this->userRepository->find($request->get('user'));
        if(!$user OR $user->getConfirmationToken() !== $request->get('token'))
        {
            throw new Exception('Le lien utilisé n\'est pas valide !');
        }
        $user->setConfirmedAt(new DateTimeImmutable());
        $user->setConfirmationToken(null);
        $this->em->flush();
        $this->addFlash('success', 'Votre adresse email est désormais vérifiée ! Vous pouvez vous connecter !');
        return $this->redirectToRoute('security_login');
    }


    #[Route('réinitialisation-du-mot-de-passe', name: 'security_passwordInit')]
    public function passwordInit(Request $request, TokenGeneratorInterface $tokenGenerator, PasswordInitEmail $passwordInitEmail): Response
    {
        if($request->get('email'))
        {
            /** @var User */
            $user = $this->userRepository->findOneBy(['email' => $request->get('email')]);
            if(!$user)
            {
                throw new Exception('Il n\'y a pas de compte associé à cet email');
            }
            $user->setPasswordInitToken($tokenGenerator->generateToken());
            $this->em->flush();
            $passwordInitEmail->send($user);
            $this->addFlash('success', 'Un lien de réinitialisation du mot de passe vous a été envoyé par email !');
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/password_init.html.twig');
    }

    #[Route('verifyPasswordInit', name: 'security_verifyPasswordInit')]
    public function verifyPasswordInit(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $passwordInit = new PasswordInit;
        
        if(!$request->isMethod('POST'))
        {
            /** @var User */
            $user = $this->userRepository->find($request->get('user'));
            if(!$user OR $user->getPasswordInitToken() !== $request->get('token'))
            {
                throw new Exception('Le lien utilisé n\'est pas valide !');
            }
            
            $passwordInit->setUserId($user->getId());
        }
        $form = $this->createForm(PasswordInitType::class, $passwordInit);
        $form->handleRequest($request);

        
        if($form->isSubmitted() && $form->isValid()) 
        { 
            $user = $this->userRepository->find($passwordInit->getUserId());
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $passwordInit->getPassword())
            );
            $user->setPasswordInitToken(null);
            $this->em->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié !');
            return $this->redirectToRoute('security_login');
        }
        
        return $this->render('security/new_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

  }