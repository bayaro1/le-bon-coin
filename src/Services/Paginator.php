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
    private const PAGE_NAME = 'p';

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
        $this->route = $request->attributes->get('_route');
        if($request->query->getInt(self::PAGE_NAME) === 1)
        {
            $response = new RedirectResponse($this->urlGenerator->generate($this->route));
            $response->send();
        }

        $this->page = $request->query->getInt(self::PAGE_NAME, 1);
        if($this->page < 1)
        {
            $response = new RedirectResponse($this->urlGenerator->generate($this->route));
            $response->send();
        }

        $offset = $perPage * ($this->page - 1);
        $this->totalItems = count($query->getResult());
        $this->totalPages = round($this->totalItems / $perPage);
        $this->items = $query->setMaxResults($perPage)
                                ->setFirstResult($offset)
                                ->getResult();

    }
    


    public function getItems():?array 
    {
        return $this->items;
    }


    public function paginationLinks():string 
    {

        return $this->previousLink() . $this->pageNumberLinks() . $this->nextLink() ;
        
    }

    private function pageNumberLinks():string 
    {
        $first = $this->page - 6;
        $last = $this->page + 6;
        if($first < 1)
        {
            $first = 1;
            $last = 13;
        }
        if($last > $this->totalPages)
        {
            $last = $this->totalPages + 1;
            $first = ($this->totalPages - 12) > 1 ? ($this->totalPages - 12): 1;
        }
        
        $numbers_html = [];
        for ($i=$first; $i <= $last; $i++) { 
            $url = $this->urlGenerator->generate($this->route, [self::PAGE_NAME => $i]);
            $style = $i === $this->page ? 'style="background-color: black; color: white;"': '';
            $numbers_html[] = '<a class="btn" '.$style.' href="'.$url.'">'.$i.'</a>';
        }

        return implode(' ', $numbers_html);
    }

    private function previousLink():string 
    {
        if($this->page > 1)
        {
            $previousUrl = $this->urlGenerator->generate($this->route, [self::PAGE_NAME => $this->page - 1]);
            return <<<HTML
                        <a href="$previousUrl" style="color: black;" class="btn me-2"><i class="bi bi-chevron-left"></i></a>
                        HTML;
        }
        else
        {
            return <<<HTML
                        <span style="color: rgba(50, 50, 50, 0.4);" class="btn btn-outline me-2"><i class="bi bi-chevron-left"></i></span>
                        HTML;
        }
    }

    private function nextLink():string
    {
        if($this->page < $this->totalPages)
        {
            $nextUrl = $this->urlGenerator->generate($this->route, [self::PAGE_NAME => $this->page + 1]);
            return <<<HTML
                        <a href="$nextUrl" style="color: black;" class="btn ms-2"><i class="bi bi-chevron-right" ></i></a>
                        HTML;
        }
        else
        {
            return <<<HTML
                        <span style="color: rgba(50, 50, 50, 0.4);" class="btn btn-outline ms-2"><i class="bi bi-chevron-right"></i></span>
                        HTML;
        }
    }
}