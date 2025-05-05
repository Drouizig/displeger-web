<?php

namespace App\Controller\Api;

use App\Entity\VerbLocalization;
use App\Repository\VerbLocalizationRepository;
use App\Transformer\VerbTransformer;
use App\Util\KemmaduriouManager;
use App\Util\VerbouManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VerbController extends AbstractController
{

    public function __construct(
        private readonly VerbouManager $verbouManager,
        private readonly KemmaduriouManager $kemmaduriouManager,
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface $client,
        private readonly VerbTransformer $transformer,
    )
    {
    }

    #[Route('/api/front/verb/{infinitive}', name: 'api_verb', priority: 2)]
    public function verb(
        string $infinitive
    )
    {
        $verbLocalization = $this->entityManager
            ->getRepository(VerbLocalization::class)->findOneBy(['infinitive' => $infinitive]);


        if (null === $verbLocalization) {
            throw new NotFoundHttpException();
        }
        $verb = $verbLocalization->getVerb();
        $locales = ['fr', 'br', 'en'];

        /** @var verbLocalizationRepository VerbLocalizationRepository */
        $verbLocalizationRepository = $this->entityManager->getRepository(VerbLocalization::class);
        $previousVerb = $verbLocalizationRepository->getPreviousVerb($verbLocalization->getInfinitive());
        $nextVerb = $verbLocalizationRepository->getNextVerb($verbLocalization->getInfinitive());
        $verbEndings = $this->verbouManager->getEndings($verbLocalization->getCategory(), $verbLocalization->getDialectCode());
        $softMutatedBase = $this->kemmaduriouManager->mutateWord($verbLocalization->getBase(), KemmaduriouManager::BLOTAAT, $verbLocalization->getGouMutation());
        foreach($verbEndings['standard']['nach'] as $person => $ending) {
            if(count($ending) > 0) {
                $prefix = 'na ';
                if(in_array(substr(($softMutatedBase.$ending[0]), 0, 1), ['a', 'e', 'i' , 'o' , 'u', 'y'])){
                    $prefix = 'n\'';
                }
                $verbEndings['standard']['nach'][$person] = $prefix.$softMutatedBase.'<strong>'.$ending[0].'</strong> ket';
            } else {
                $verbEndings['standard']['nach'][$person] = null;
            }
        }
        $mixedMutatedInfinitive = $this->kemmaduriouManager->mutateWord($verbLocalization->getInfinitive(), KemmaduriouManager::KEMMESKET, $verbLocalization->getGouMutation());
        $stummOber = 'bezañ o '.$mixedMutatedInfinitive;
        if(in_array(substr($mixedMutatedInfinitive, 0,1), ['a', 'e', 'i', 'o', 'u'])) {
            $stummOber = 'bezañ oc\'h '.$mixedMutatedInfinitive;
        }
        if($verbLocalization->getInfinitive() ==='gouzout') {
            $stummOber = 'bezañ o c\'houzout';
        } elseif($verbLocalization->getInfinitive() ==='ober') {
            $stummOber = 'bezañ oc\'h ober';
        }
        $softMutatedInfinitive = $this->kemmaduriouManager->mutateWord($verbLocalization->getInfinitive(), KemmaduriouManager::BLOTAAT, $verbLocalization->getGouMutation());
        $stummEnUr = 'en ur '.$softMutatedInfinitive;
        if($verbLocalization->getInfinitive() ==='gouzout') {
            $stummEnUr = 'en ur c\'houzout';
        } elseif($verbLocalization->getInfinitive() ==='ober') {
            $stummEnUr = 'en ur ober';
        }
        $verbEndings['standard']['ober'] = $stummOber;
        $verbEndings['standard']['enur'] = $stummEnUr;

        $wikeriadurUrls = [];
        $geriafurchUrls = [];
        $wikeriadurConjugationUrls = [];
        foreach($locales as $locale) {
            $wikeriadurUrls[$locale] = $this->getParameter('url_wikeriadur')[$locale].$verbLocalization->getInfinitive();
            if(null === $verb->getWiktionnaryExists()) {
                $result = $this->client->request('GET', $wikeriadurUrls[$locale]);
                if ($result->getStatusCode() == \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND) {
                    $verb->setWiktionnaryExists(false);
                } else {
                    $verb->setWiktionnaryExists(true);
                }
            }

            if(isset($this->getParameter('url_geriafurch')[$locale])) {
                $geriafurchUrls[$locale] = $this->getParameter('url_geriafurch')[$locale].$verbLocalization->getInfinitive();
            } else {
                $geriafurchUrls[$locale] = $this->getParameter('url_geriafurch')['br'].$verbLocalization->getInfinitive();
            }

            $wikeriadurConjugationUrls[$locale] = $this->getParameter('url_wikeriadur_conjugation')[$locale].$verbLocalization->getInfinitive();
            if(null === $verb->getWiktionnaryConjugationExists()) {
                $result = $this->client->request('GET', $wikeriadurConjugationUrls[$locale]);
                if ($result->getStatusCode() == \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND) {
                    $verb->setWiktionnaryConjugationExists(false);
                } else {
                    $verb->setWiktionnaryConjugationExists(true);
                }
            }
        }

        $organisation = $this->getParameter('organisation');
        try {
            $organisation = $this->getParameter('organisation.'.$verbLocalization->getInfinitive());
        } catch (ParameterNotFoundException $e) {
        }

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader());
        $normalizers = [
            new BackedEnumNormalizer(),
            new ObjectNormalizer($classMetadataFactory),
        ];

        //dump($this->transformer->transform($verbLocalization, $previousVerb, $nextVerb, $verbEndings, $organisation));die();
        $serializer = new Serializer($normalizers);
        return new JsonResponse([
            'verb' =>
                $serializer->normalize($this->transformer->transform($verbLocalization, $previousVerb, $nextVerb, $verbEndings, $organisation))
            ]
        );

    }
}
