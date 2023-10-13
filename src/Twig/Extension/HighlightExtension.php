<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\HighlightExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HighlightExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight', [HighlightExtensionRuntime::class, 'highlight']),
        ];
    }
}
