<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CountExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('count', [$this, 'count']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count', [$this, 'count']),
        ];
    }

    /**
     * @param array|null $var
     */
    public function count($var):int
    {
        return count($var);
    }
}
