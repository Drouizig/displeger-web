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
    public function getEndings($category, $dialects = null)
    {
        if($dialects === null) {
            $dialects = [];
        }
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

        $baseEndings = $this->parameterBag->get('verbou.regular');
        $categoryEndings = $this->parameterBag->get('verbou.'.$category);
        $standardEndings = array_merge($baseEndings, $categoryEndings ?? []);

        $localizedEndings = [];
        foreach($selectedDialects as $dialect) {
            if($this->parameterBag->has('verbou.regular.'.$dialect)) {
                $baseLocalizedEndings = $this->parameterBag->get('verbou.regular.'.$dialect);
                $categoryLocalizedEndings = [];
                if($this->parameterBag->has('verbou.'.$category.'.'.$dialect)) {
                    $categoryLocalizedEndings = $this->parameterBag->get('verbou.'.$category.'.'.$dialect);
                }
                $localizedEndings[$dialect] = array_merge($baseLocalizedEndings, $categoryLocalizedEndings);
            } else if($dialect == 'gwened') {
                $baseLocalizedEndingsBerr = $this->parameterBag->get('verbou.regular.gwened_berr');
                $baseLocalizedEndingsHir = $this->parameterBag->get('verbou.regular.gwened_hir');
                $localizedEndings[$dialect.'_berr'] = $baseLocalizedEndingsBerr;
                $localizedEndings[$dialect.'_hir'] = $baseLocalizedEndingsHir;
                if($this->parameterBag->has('verbou.'.$category.'.'.$dialect.'_berr')) {
                    $categoryLocalizedEndingsBerr = $this->parameterBag->get('verbou.'.$category.'.'.$dialect.'_berr');
                    $localizedEndings[$dialect.'_berr'] = array_merge($baseLocalizedEndingsBerr, $categoryLocalizedEndingsBerr);
                }
                if($this->parameterBag->has('verbou.'.$category.'.'.$dialect.'_hir')) {
                    $categoryLocalizedEndingsHir = $this->parameterBag->get('verbou.'.$category.'.'.$dialect.'_hir');
                    $localizedEndings[$dialect.'_hir'] = array_merge($baseLocalizedEndingsHir, $categoryLocalizedEndingsHir);
                }
            }
        }

        return ['standard' => $standardEndings, 'localized' => $localizedEndings];
    }
}