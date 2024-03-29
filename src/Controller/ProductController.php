<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\Paginator;
use App\Form\SearchFilterType;
use App\DataModel\SearchFilter;
use App\DataModel\SortFilter;
use App\Form\SortFilterType;
use App\Repository\PictureRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
        $searchFilter = new SearchFilter;
        $searchFilterForm = $this->createForm(SearchFilterType::class, $searchFilter);

        $searchFilterForm->handleRequest($request);

        $paginator = $this->repository->findPaginatedFiltered($request, $searchFilter, 5);

        return $this->render('product/index.html.twig', [
            'current_menu' => 'product_view',
            'paginator' => $paginator,
            'search_filter_form' => $searchFilterForm->createView(),
            'search_filter' => $searchFilter
        ]);
    }

    #[Route('/{category}/{product_id}', name: 'product_show')]
    #[ParamConverter('product', options: ['mapping' => ['product_id' => 'id']])]
    public function show(Product $product, Request $request)
    {
        $pos = $request->get('pos') ?: 0;
        
        if($product->getPictures()->get($pos))
        {
            $product->setFirstPicture($product->getPictures()->get($pos));
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'user' => $product->getUser()
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
            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success', 'Votre annonce intitulée "'.$product->getTitle().'" est en ligne !');
            return $this->redirectToRoute('product_index');
        }
        return $this->render('product/new.html.twig', [
                'productForm' => $form->createView(),
                'errors' => ($form->isSubmitted() && !$form->isValid())
        ]);
    }

}
