<?php
namespace App\Twig;

use App\Entity\ConfigurationTranslation;
use App\Repository\ConfigurationTranslationRepository;
use App\Util\KemmaduriouManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ConfigurationExtension extends AbstractExtension
{

    /** @var ConfigurationTranslationRepository */
    protected $configurationTranslationRepository;

    /** @var RequestSack */
    protected $requestStack;

    /** @var KemmaduriouManager */
    protected $kemmaduriouManager;

    public function __construct(ConfigurationTranslationRepository $configurationTranslationRepository,RequestStack $requestStack, KemmaduriouManager $kemmaduriouManager)
    {
        $this->configurationTranslationRepository = $configurationTranslationRepository;
        $this->requestStack = $requestStack;
        $this->kemmaduriouManager = $kemmaduriouManager;
        
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('configuration', [$this, 'configuration']),
        ];
    }
    public function getFilters()
    {
        return [
            new TwigFilter('mutate', [$this, 'mutate']),
        ];
    }

    public function configuration($code, $locale = null)
    {
        if($locale === null) {
            $locale = $this->requestStack->getCurrentRequest()->getLocale();
        }
        
        /** @var ConfigurationTranslation $configurationTranslation  */
        $configurationTranslation = $this->configurationTranslationRepository->findByCodeAndLocale($code, $locale);
        if($configurationTranslation === null) {
            $configurationTranslation = $this->configurationTranslationRepository->findByCodeAndLocale($code, 'fr');
        }
        
        if($configurationTranslation !== null) {
            return $configurationTranslation->getText();
        } else {
            return '';
        }
    }

    public function mutate($verb, $mutation)
    {
        $mutated = $this->kemmaduriouManager->mutateWord($verb, $mutation);
        return $mutated === $verb ? '-' : $mutated;
    }
}