<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class HighlightExtensionRuntime implements RuntimeExtensionInterface
{
    public function highlight($value, $term)
    {
        return str_replace($term, '<span>'.$term.'</span>', $value);
    }
}
