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
use Symfony\Component\HttpFoundation\Response;

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
        $contactForm = $this->createForm(ContactType::class);
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
            'contactForm' => $contactForm->createView()
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

    /**
     * @Route("/mail", name="mail")
     */
    public function sendMail(Request $request, \Swift_Mailer $mailer)
    {
        $contactForm = $this->createForm(ContactType::class);

        $contactForm->handleRequest($request);
        if($contactForm->isSubmitted() && $contactForm->isValid()) {
            $name = $contactForm->get('name')->getData();
            $email = $contactForm->get('email')->getData();
            $text = $contactForm->get('message')->getData();
            $message = (new \Swift_Message('[Displeger verboù] Kemennadenn digant '.$name))
            ->setFrom('drouizig@drouizig.org')
            ->setTo('drouizig@drouizig.org')
            ->setBody(
                $this->renderView(
                    'emails/contact.html.twig',
                    [
                        'name' => $name,
                        'email' => $email,
                        'text' => $text   
                    ]
                ),
                'text/html'
            )
            ;
    
            $result = $mailer->send($message);
            return new JsonResponse(['result' => $result == 0? 'nok' : 'ok']);
        } else{
            return new JsonResponse(['result' => 'nok', 'errors' => $contactForm->getErrors(true)]);
        }
    }
}
