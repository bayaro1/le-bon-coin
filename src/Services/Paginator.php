<?php
namespace App\Services;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Paginator
{
    private const PAGE_NAME = 'page';

    private const PER_PAGE = 20;

    private array $items;

    private int $totalItems;

    private int $totalPages;

    private int $page;

    private UrlGeneratorInterface $urlGenerator;

    private string $route;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    public function configure(Request $request, Query $query, ?int $perPage = self::PER_PAGE):void
    {
        $this->page = $request->query->getInt(self::PAGE_NAME, 1);
        if($this->page < 1)
        {
            $response = new RedirectResponse('?page=1');
            $response->send();
        }
        $offset = $perPage * ($this->page - 1);
        $this->totalItems = count($query->getResult());
        $this->totalPages = round($this->totalItems / $perPage);
        $this->items = $query->setMaxResults($perPage)
                                ->setFirstResult($offset)
                                ->getResult();

        $this->route = $request->attributes->get('_route');
    }
    


    public function getItems():?array 
    {
        return $this->items;
    }


    public function paginationLinks():string 
    {

        if($this->page > 1)
        {
            $previousUrl = $this->urlGenerator->generate($this->route, ['page' => $this->page - 1]);
            $previousLink = <<<HTML
                        <a href="$previousUrl" style="color: black;" class="btn me-2"><i class="bi bi-chevron-left"></i></a>
                        HTML;
        }
        else
        {
            $previousLink = <<<HTML
                        <span style="color: rgba(50, 50, 50, 0.4);" class="btn btn-outline me-2"><i class="bi bi-chevron-left"></i></span>
                        HTML;
        }

        if($this->page < $this->totalPages)
        {
            $nextUrl = $this->urlGenerator->generate($this->route, ['page' => $this->page + 1]);
            $nextLink = <<<HTML
                        <a href="$nextUrl" style="color: black;" class="btn ms-2"><i class="bi bi-chevron-right" ></i></a>
                        HTML;
        }
        else
        {
            $nextLink = <<<HTML
                        <span style="color: rgba(50, 50, 50, 0.4);" class="btn btn-outline ms-2"><i class="bi bi-chevron-right"></i></span>
                        HTML;
        }
        
        return $previousLink . $this->page . '/' . $this->totalPages . $nextLink ;
        
    }
}