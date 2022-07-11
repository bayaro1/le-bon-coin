<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ContainerZMcLRYO\PaginatorInterface_82dac15;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }


    #[Route('/annonces', name: 'search_index')]
    public function index(): Response
    {
        $products = $this->repository->findAll();
        return $this->render('search/index.html.twig', [
            'current_menu' => 'search',
            'products' => $products
        ]);
    }

}
