<?php
namespace App\Service;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private RequestStack $request;
    private ProductRepository $productRepository;
    private Security $security;
    private EntityManagerInterface $em;
    private CartRepository $cartRepository;

    public function __construct(RequestStack $request, ProductRepository $productRepository, CartRepository $cartRepository, EntityManagerInterface $em, Security $security)
    {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->em = $em;
        $this->cartRepository = $cartRepository;
    }

    public function initialize():void 
    {
        /** @var Cart */
        $registeredCart = $this->cartRepository->findOneBy(['user' => $this->security->getUser()]);
        if($registeredCart !== null)
        {
            $cart = [];
            foreach($registeredCart->getProducts() as $product)
            {
                $cart[] = $product->getId();
            }
            $this->setCart($cart);
        }
    }

    public function register():void 
    {
        if($this->getCart() !== null)
        {
            /** @var Cart */
            $cart = $this->cartRepository->findOneBy(['user' => $this->security->getUser()]);
            if($cart === null)
            {
                $cart = new Cart;
                $this->em->persist($cart);
                $cart->setUser($this->security->getUser());
            }
            $cart->setProducts($this->getProducts());
            $this->em->flush();
        }
    }

    public function addOrRemove(int $id)
    {
        $cart = $this->getCart();
        if($this->exists($id))
        {
            $key = array_search($id, $cart);
            unset($cart[$key]);
        }
        else
        {
            $cart[] = $id;
        }
        $this->setCart($cart);
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        return array_map(function ($id) {
            return $this->productRepository->find($id);
        }, $this->getCart());
    }

    public function exists(int $id):bool
    {
        return (in_array($id, $this->getCart()));
    }

    public function getCart()
    {
        return $this->request->getSession()->get('cart', []);
    }
    private function setCart(array $cart)
    {
        $this->request->getSession()->set('cart', $cart);
    }
}