<?php 
namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Product;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\JavascriptAdaptation\TemplatingClassAdaptor\CommentAdaptor;
use App\JavascriptAdaptation\TemplatingClassAdaptor\ConstraintViolationListAdaptor;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractController
{
    public function __construct(
        private CommentRepository $commentRepository,
        private CommentAdaptor $commentAdaptor,
        private ConstraintViolationListAdaptor $constraintViolationListAdaptor,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    )
    {}

    #[Route('/produit-{id}/chargement-des-commentaires', name: 'comment_loadPagination')]
    #[ParamConverter('product', options: ['mapping' => ['id' => 'id']])]
    public function loadPagination(Product $product, Request $request): Response
    {
        $comments = $this->commentRepository->findBy(['product' => $product], ['createdAt' => 'DESC'], $request->get('limit', 15), $request->get('offset', 0));
        return new Response(json_encode($this->commentAdaptor->adapteAll($comments)));
    }

    #[Route('/produit{id}/ajout-de-commentaire', name: 'comment_new')]
    public function new(Request $request, Product $product): Response
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return new Response(json_encode([
                null, 
                ['content' => 'Vous devez vous connecter avant de poster un commentaire']
            ]));
        }

        $comment = new Comment;
        $comment->setUser($this->getUser())
                ->setProduct($product)
                ->setContent($request->get('content'))
                ->setCreatedAt(new DateTimeImmutable())
                ;

        $constraintViolationList = $this->validator->validate($comment);
        
        if($constraintViolationList->count() === 0)
        {
            $this->em->persist($comment);
            $this->em->flush();
        }
        
        return new Response(
            json_encode([
                $this->commentAdaptor->adapte($comment),
                $this->constraintViolationListAdaptor->adapte($constraintViolationList)
            ])
        );
    }
}