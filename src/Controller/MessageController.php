<?php 
namespace App\Controller;

use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    #[Route('/reply/{product_id}', name: 'message_new')]
    #[ParamConverter('product', class: Product::class, options: ['mapping' => ['product_id' => 'id']])]
    public function new(Product $product)
    {
        return $this->render('message/new.html.twig', [
            'product' => $product,
            'user' => $product->getUser()
        ]);
    }
}