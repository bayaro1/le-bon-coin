<?php
namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/favoris/ajout/{id}', name: 'cart_add')]
    public function addOrRemove(Product $product, Request $request):Response
    {
        if($product)
        {
            $this->cartService->addOrRemove($product->getId());
        }
        return $this->redirect('/');
    }

    #[Route('/favoris', name: 'cart_index')]
    public function index():Response
    {
        return $this->render('cart/index.html.twig', [
            'products' => $this->cartService->getProducts(),
            'current_menu' => 'cart'
        ]);
    }
}