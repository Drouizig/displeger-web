<?php

namespace App\Controller;

use App\Entity\Verb;
use App\Util\KemmaduriouManager;
use App\Util\VerbouManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{


    public function __construct(VerbouManager $verbouManager, KemmaduriouManager $kemmaduriouManager)
    {
        $this->verbouManager = $verbouManager;
        $this->kemmaduriouManager = $kemmaduriouManager;
    }

    /**
     * @Route("/verb/{anvVerb}", name="api_verb")
     * @Entity("verb", expr="repository.findOneByAnvVerb(anvVerb)")
     */
    public function index(Verb $verb)
    {
        if(null !== $verb) {
            $gour = [
                'U1',
                'U2',
                'U3',
                'L1',
                'L2',
                'L3',
                'D'
            ];
            $verbEndings = $this->verbouManager->getEndings($verb->getCategory());
            $anvGwan = $verbEndings['gwan'];
            unset($verbEndings['gwan']);
            unset($verbEndings['nach']);
            $mutatedBase = $this->kemmaduriouManager->mutateWord($verb->getPennrann(), KemmaduriouManager::BLOTAAT);
            $nach = [];
            foreach($verbEndings['kadarnaat'] as $ending) {
                if(count($ending) > 0) {
                    $nach[] = 'na '.$mutatedBase.'<strong>'.$ending[0].'</strong> ket';
                } else {
                    $nach[] = null;
                }
            }

            $data = [];
            foreach($verbEndings as $time => $endings) {
                foreach($endings as $k => $ending) {
                    $data[$time][$gour[$k]] = array_map(function($e) use ($verb) {
                        return $verb->getPennrann().$e;
                    }, $ending);
                }
            }
            return new JsonResponse($data);
        } else {
            return new NotFoundHttpException();
        }
    }
}
