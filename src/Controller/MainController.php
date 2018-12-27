<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Verb;
use App\Util\VerbouManager;

class MainController extends AbstractController
{

    /** @var VerbouManager */
    protected $verbouManager;

    public function __construct(VerbouManager $verbouManager)
    {
        $this->verbouManager = $verbouManager;
    }

    /**
     * @Route("/{anvVerb?}", name="main")
     */
    public function index(Request $request,Verb $verb = null)
    {
        if ($request->query->get('verb')) {
            $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
            /** @var Verb $verb */
            $verb = $verbRepository->findOneByAnvVerb($request->query->get('verb'));
            if ($verb != null) {
                return $this->redirectToRoute('main', ['anvVerb' => $verb->getAnvVerb()]);
            }
        }

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

        $searchTerm = $request->query->get('verb');
        if (null == $searchTerm) {
            $searchTerm = $request->attributes->Get('anvVerb');
        }
        if(null !== $searchTerm) {
            return $this->render('main/error.html.twig', [
                'verb' => $searchTerm,
            ]);
        }

        return $this->render('main/index.html.twig', [
            'verb' => $request->query->get('verb'),
        ]);
        

        
    }
}
