<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Verb;
use App\Util\VerbouManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class MainController extends AbstractController
{

    /** @var VerbouManager */
    protected $verbouManager;

    public function __construct(VerbouManager $verbouManager)
    {
        $this->verbouManager = $verbouManager;
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
            return $this->render('main/verb.html.twig', [
                'verb' => $verb,
                'verbEndings' => $verbEndings,
                'anvGwan' => $anvGwan,
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
