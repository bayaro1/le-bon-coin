<?php

namespace App\Twig;

use DateTimeImmutable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DateFormaterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('date_formater', [$this, 'dateFormat']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('date_formater', [$this, 'dateFormat']),
        ];
    }

    public function dateFormat(DateTimeImmutable $date)
    {
        return $date->format('d/m/Y \à H\:i');
    }
}
