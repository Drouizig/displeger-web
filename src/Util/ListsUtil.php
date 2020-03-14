<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Intl;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListsUtil
{

    /** @var TranslatorInterface translator */
    protected $translator;

    /** @var RequestStack $requestStack */
    protected $requestStack;

    /** @var array $dialects */
    protected $dialects;

    /** @var array $categories */
    protected $categories;

    public function __construct(
        RequestStack $requestStack, 
        TranslatorInterface $translator,
        array $dialects,
        array $categories)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->dialects = $dialects;
        $this->categories = $categories;
    }

    public function getLocales() 
    {
        $requestLocale = 'br';
        if($this->requestStack->getCurrentRequest() !== null) {
            $requestLocale = $this->requestStack->getCurrentRequest()->getLocale();
        }
        $locales = Intl::getLocaleBundle()->getLocaleNames($requestLocale);
        $locales['gal'] = $this->translator->trans('global.gallo');
        ksort($locales);
        return $locales;
    }

    public function getDialects() 
    {
        $translatedDialects = [];
        foreach($this->dialects as $dialect) {
            $translatedDialects[$dialect] = 'app.dialect.'.$dialect;
        }
        return $translatedDialects;
    }

    public function getCategories() 
    {
        $translatedCategories = [];
        foreach($this->categories as $category) {
            $translatedCategories[$category] = 'app.category.'.$category;
        }
        return $translatedCategories;
    }
    
}