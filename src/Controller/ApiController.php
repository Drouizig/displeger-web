<?php

namespace App\Controller;

use App\Entity\Verb;
use App\Entity\VerbLocalization;
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
     * @Route("/verb/{infinitive}", name="api_verb")
     * @Entity("VerbLocalization", expr="repository.findOneByInfinitive(infinitive)")
     */
    public function index(VerbLocalization $verbLocalization)
    {
        if(null !== $verbLocalization) {
            $gour = [
                'U1',
                'U2',
                'U3',
                'L1',
                'L2',
                'L3',
                'D'
            ];
            $verbEndings = $this->verbouManager->getEndings($verbLocalization->getCategory(), $verbLocalization->getDialectCode());
            $anvGwan = $verbEndings['standard']['gwan'];
            unset($verbEndings['standard']['gwan']);
            unset($verbEndings['standard']['nach']);
            // $mutatedBase = $this->kemmaduriouManager->mutateWord($verb->getPennrann(), KemmaduriouManager::BLOTAAT);
            // $nach = [];
            // foreach($verbEndings['kadarnaat'] as $ending) {
            //     if(count($ending) > 0) {
            //         $nach[] = 'na '.$mutatedBase.'<strong>'.$ending[0].'</strong> ket';
            //     } else {
            //         $nach[] = null;
            //     }
            // }

            $data = [];
            foreach($verbEndings['standard'] as $time => $endings) {
                foreach($endings as $k => $ending) {
                    $data[$time][$gour[$k]] = array_map(function($e) use ($verbLocalization) {
                        return $verbLocalization->getBase().$e;
                    }, $ending);
                }
            }
            return new JsonResponse($data);
        } else {
            return new NotFoundHttpException();
        }
    }
}
