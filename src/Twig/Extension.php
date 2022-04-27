<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class Extension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ssa_render_frequently_bought_together',
                [Runtime::class, 'renderFrequentlyBoughtTogether'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
