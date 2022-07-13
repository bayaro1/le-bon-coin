<?php 
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;



use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

  }