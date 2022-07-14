<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Services\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ContainerZMcLRYO\PaginatorInterface_82dac15;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Paginator $paginator, Request $request): Response
    {
        $paginator->configure($request, $this->repository->findAllQuery(), 10);
        return $this->render('search/index.html.twig', [
            'current_menu' => 'search',
            'paginator' => $paginator
        ]);
    }

}
