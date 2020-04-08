<?php

namespace App\Util;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VerbouManager
{
    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getEndings($category)
    {
        return $this->parameterBag->get('verbou.'.$category);
    }
}