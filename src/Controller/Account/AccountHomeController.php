<?php
namespace App\Controller\Account;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountHomeController extends AbstractController
{
    #[Route('/compte/accueil', name: 'account_home_index')]
    public function index():Response
    {
        
        return $this->render('account/home/index.html.twig', [
            'current_menu' => 'account'
        ]);
    }
}