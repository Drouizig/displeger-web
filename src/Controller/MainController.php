<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Verb;
use App\Util\VerbouManager;
use App\Util\KemmaduriouManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class MainController extends AbstractController
{

    /** @var VerbouManager */
    protected $verbouManager;

    /** @var KemmaduriouManager */
    protected $kemmaduriouManager;

    public function __construct(VerbouManager $verbouManager, KemmaduriouManager $kemmaduriouManager)
    {
        $this->verbouManager = $verbouManager;
        $this->kemmaduriouManager = $kemmaduriouManager;
    }

    /**
     * @Route("/", name="main")
     */
    public function index(Request $request) {
        if ($request->query->get('verb')) {
            return $this->redirectToRoute('verb', ['anvVerb' => $request->query->get('verb')]);
        }

        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/verb/{anvVerb}", name="verb")
     * @Entity("verb", expr="repository.findOneByAnvVerb(anvVerb)")
     */
    public function verb(Request $request,Verb $verb = null)
    {
        if(null !== $verb) {
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
            $contactForm = $this->createForm(ContactType::class);
            return $this->render('main/verb.html.twig', [
                'verb' => $verb,
                'verbEndings' => $verbEndings,
                'anvGwan' => $anvGwan,
                'nach' => $nach,
                'contactForm' => $contactForm->createView()
            ]);
        }

        $searchTerm = $request->attributes->get('anvVerb');
        return $this->render('main/error.html.twig', [
            'verb' => $searchTerm,
        ]);
    }

    /**
     * @Route("/autocomplete", name="autocomplete")
     */
    public function autocomplete(Request $request)
    {
        $term = $request->query->get('term');
        if (null === $term) {
            return new JsonResponse();
        }
        $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
        $result = $verbRepository->findByTerm($term);
        $array = [];
        /** @var Verb $res */
        foreach ($result as $res) {
            $array[] = $res->getAnvVerb();
        }
        return new JsonResponse($array);
    }
}
