<?php
namespace App\Controller\Account;

use App\Entity\SearchFilter;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('compte/mes-annonces')]
class AccountProductController extends AbstractController
{
    private ProductRepository $repository;

    private EntityManagerInterface $em;

    public function __construct(ProductRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }


    #[Route('/', name: 'account_product_index')]
    public function index(Request $request):Response
    {
        $searchFilter = new SearchFilter;
        $searchFilter->setUser($this->getUser());
        $paginator = $this->repository->findPaginatedFiltered($request, $searchFilter);
        $count = $this->repository->count(['user' => $this->getUser()]);
        return $this->render('account/product/index.html.twig', [
            'current_menu' => 'account',
            'current_submenu' => 'account_product',
            'paginator' => $paginator,
            'count' => $count
        ]);
    }

    #[Route('/suppr', name: 'account_product_delete', methods: 'POST')]
    public function delete(Request $request)
    {
        $product = $this->repository->find($request->get('id'));
        $this->em->remove($product);
        $this->em->flush();
        $this->addFlash('success', 'L\'annonce a bien été supprimée !');
        return $this->redirectToRoute('account_product_index');
    }
}