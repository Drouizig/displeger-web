<?php

namespace App\Util;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VerbouManager
{
    const STANDARD_DIALECT = 'reolad';

    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Returns all the endings per possible dialect corresponding with the given category & dialects
     */
    public function getEndings($category, $dialects)
    {
        $endings = [];
        $conjugationGroups = $this->parameterBag->get('conjugation_groups');
        $allDialects = $this->parameterBag->get('dialects');

        $selectedDialects = [];
        // If no dialect is specified or the standard dialect is among them, pick all verb endings
        if($dialects === [] || in_array(self::STANDARD_DIALECT, $dialects)) {
            $selectedDialects = $allDialects;
        } else {
            //get all the groups that overlap with the verb dialects
            foreach($dialects as $verbDialect) {
                foreach($conjugationGroups as $conjugationGroup) {
                    foreach($conjugationGroup as $dialect) {
                        if($verbDialect === $dialect) {
                            $selectedDialects[] = $dialect;
                        }
                    }
                }
            }
        }

        foreach($selectedDialects as $dialect) {
            
            
        }

        return $this->parameterBag->get('verbou.'.$category);
    }
}