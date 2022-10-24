<?php 
namespace App\Controller;

use App\Entity\Product;
use App\JavascriptAdaptation\TemplatingClassAdaptor\CommentAdaptor;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentController extends AbstractController
{
    public function __construct(
        private CommentRepository $commentRepository,
        private CommentAdaptor $commentAdaptor
    )
    {}

    #[Route('/produit-{id}/chargement-des-commentaires', name: 'comment_loadPagination')]
    #[ParamConverter('product', options: ['mapping' => ['id' => 'id']])]
    public function loadPagination(Product $product, Request $request): Response
    {
        $comments = $this->commentRepository->findBy(['product' => $product], ['createdAt' => 'DESC'], $request->get('limit', 15), $request->get('offset', 0));
        return new Response(json_encode($this->commentAdaptor->adapte($comments)));
    }

    #[Route('/produit{id}/ajout-de-commentaire', name: 'comment_new')]
    public function new(Request $request, Product $product): Response
    {
        return new Response('ok');
    }
}