<?php
namespace App\Twig;

use App\Entity\ConfigurationTranslation;
use App\Repository\ConfigurationTranslationRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigurationExtension extends AbstractExtension
{

    /** @var ConfigurationTranslationRepository */
    protected $configurationTranslationRepository;

    /** @var RequestSack */
    protected $requestStack;

    public function __construct(ConfigurationTranslationRepository $configurationTranslationRepository,RequestStack $requestStack)
    {
        $this->configurationTranslationRepository = $configurationTranslationRepository;
        $this->requestStack = $requestStack;
        
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('configuration', [$this, 'configuration']),
        ];
    }

    public function configuration($code, $locale = null)
    {
        if($locale === null) {
            $locale = $this->requestStack->getCurrentRequest()->getLocale();
        }
        
        /** @var ConfigurationTranslation $configurationTranslation  */
        $configurationTranslation = $this->configurationTranslationRepository->findByCodeAndLocale($code, $locale);
        
        if($configurationTranslation !== null) {
            return $configurationTranslation->getText();
        } else {
            return '';
        }
    }
}