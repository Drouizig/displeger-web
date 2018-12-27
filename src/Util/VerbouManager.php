<?php

namespace App\Util;

class VerbouManager
{
    protected $verbConfig;

    public function __construct($verbConfig)
    {
        $this->verbConfig = $verbConfig;
    }

    public function getEndings($category)
    {
        return $this->verbConfig[$category];
    }
}