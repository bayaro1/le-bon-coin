<?php 
namespace App\Controller;

use App\Entity\User;



use App\Form\RegistrationFormType;
use App\Notification\EmailNotification\WelcomeEmail;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{

    #[Route('/connexion', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'current_menu' => 'login',
            'last_username' => $lastUsername,
            'error'         => $error,
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

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

  }