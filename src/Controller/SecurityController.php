<?php 
namespace App\Controller;

use App\Entity\User;



use App\Form\RegistrationFormType;
use App\Notification\EmailNotification\WelcomeEmail;
use App\Repository\UserRepository;
use App\Service\CartService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $errorMessage = null;
        if($error)
        {
            $errorMessage = $error->getCode() === 0 ? 'Ces identifiants sont invalides !': $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'current_menu' => 'login',
            'last_username' => $lastUsername,
            'errorMessage'         => $errorMessage,
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
        $this->em->flush();
        $this->addFlash('success', 'Votre adresse email est désormais vérifiée ! Vous pouvez vous connecter !');
        return $this->redirectToRoute('security_login');
    }

  }