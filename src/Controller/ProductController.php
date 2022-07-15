<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Services\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ContainerZMcLRYO\PaginatorInterface_82dac15;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    private ProductRepository $repository;

    private EntityManagerInterface $em;

    public function __construct(ProductRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }


    #[Route('/annonces', name: 'product_index')]
    public function index(Paginator $paginator, Request $request): Response
    {
        $paginator->configure($request, $this->repository->findAllQuery(), 5);
        return $this->render('product/index.html.twig', [
            'current_menu' => 'product_view',
            'paginator' => $paginator
        ]);
    }

    #[Route('/déposer-une-annonce', name: 'product_new')]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) 
        { 
            $product->setUser($this->getUser());
            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success', 'Votre annonce intitulée "'.$product->getTitle().'" est en ligne !');
            return $this->redirectToRoute('product_index');
        }
        return $this->renderForm('product/new.html.twig', [
                'productForm' => $form
        ]);
    }

}
