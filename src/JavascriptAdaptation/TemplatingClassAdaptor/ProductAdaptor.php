<?php
namespace App\JavascriptAdaptation\TemplatingClassAdaptor;

use App\Entity\Picture;
use App\Entity\Product;
use App\Service\CartService;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductAdaptor
{
    private array $cart;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private CacheManager $cacheManager,
        private StorageInterface $storageInterface,
        CartService $cartService,
    )
    {
        $this->cart = $cartService->getCart();
    }
    /**
     * transform an object Product to an array with all necessaries values to fill a product-card-template 
     *
     * @param Product[] $products
     * @return array
     */
    public function adapte($products)
    {
        return array_map(function($product) {
            /** @var Product */
            $product = $product;
            return [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'price' => number_format($product->getPrice() / 100, 2, ',', ' ') . ' €',
                'categoryName' => $product->getCategory()->getName(),
                'city' => $product->getCity(),
                'postalCode' => $product->getPostalCode(),
                'createdAt' => $product->getCreatedAt()->format('d/m/Y'),
                'showPath' => $this->getShowPath($product),
                'firstPicturePath' => $this->getPicturePath($product),
                'inCart' => in_array($product->getId(), $this->cart),
                'cartAddPath' => $this->getCartAddPath($product)
            ];
        }, $products);
    }

    private function getCartAddPath(Product $product): string 
    {
        return $this->urlGenerator->generate('cart_add', [
            'id' => $product->getId()
        ]);
    }

    private function getPicturePath(Product $product): string
    {
        if($product->getFirstPicture() !== null)
        {
            return $this->cacheManager->getBrowserPath($this->storageInterface->resolveUri($product->getFirstPicture(), 'uploadedFile', Picture::class), 'my_mini');
        }
        else
        {
            return '/images/products/lorem.jpg';
        }
    }

    private function getShowPath(Product $product): string 
    {
        return $this->urlGenerator->generate('product_show', [
            'category' => $product->getCategory()->getName(),
            'product_id' => $product->getId()
        ]);
    }
}

