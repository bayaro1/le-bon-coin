<?php 
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use App\JavascriptAdaptation\TemplatingClassAdaptor\CommentAdaptor;
use App\Repository\CommentRepository;
use DateTime;
use DateTimeImmutable;
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
        $comment = new Comment;
        $user = new User;
        $user->setEmail($request->get('email'));
        $comment->setUser($user)
                ->setContent($request->get('content'))
                ->setCreatedAt(new DateTimeImmutable())
                ;
        //a faire plus tard, enregistrer le commentaire pour de vrai  (adapter pour qu'il faille être connecté pour poster un commentaire)

        return new Response(json_encode($this->commentAdaptor->adapte([$comment])[0]));
    }
}