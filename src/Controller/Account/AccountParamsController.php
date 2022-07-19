<?php
namespace App\Controller\Account;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('compte/paramètres')]
class AccountParamsController extends AbstractController 
{
    #[Route('/', name: 'account_params_index')]
    public function index():Response
    {
        return $this->render('account/params/index.html.twig', [
            'current_menu' => 'account',
            'current_submenu' => 'account_params'
        ]);
    }
}