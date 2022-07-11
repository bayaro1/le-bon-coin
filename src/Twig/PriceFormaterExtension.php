<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PriceFormaterExtension extends AbstractExtension
{
    public const EUR = '€';
    public const USD = '$';

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('price_formater', [$this, 'format']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('price_formater', [$this, 'format']),
        ];
    }

    public function format($value, ?string $currency = self::EUR)
    {
        return number_format($value / 100, 2, ',', ' ') .' '.$currency;
    }
}
