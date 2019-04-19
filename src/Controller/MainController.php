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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Configuration;

class MainController extends AbstractController
{

    /** @var VerbouManager */
    protected $verbouManager;

    /** @var KemmaduriouManager */
    protected $kemmaduriouManager;

    /** @var \Swift_Mailer */
    protected $mailer;

    public function __construct(VerbouManager $verbouManager, KemmaduriouManager $kemmaduriouManager, \Swift_Mailer $mailer)
    {
        $this->verbouManager = $verbouManager;
        $this->kemmaduriouManager = $kemmaduriouManager;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="main")
     */
    public function index(Request $request) {
        if ($request->query->get('verb')) {
            return $this->redirectToRoute('verb', ['anvVerb' => $request->query->get('verb')]);
        }

        $config = $this->getDoctrine()->getRepository(Configuration::class)->findFirst();

        return $this->render('main/index.html.twig', [
            'intro' => $config === null ? '' : $config->getIntro()
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, PaginatorInterface $knpPaginator) {
        $term = $request->query->get('term', null); 
        if ( null === $term ) {
            return $this->redirectToRoute('main');  
        }
        $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
        $searchQuery = $verbRepository->getFrontSearchQuery($term);
        
        $pagination = $knpPaginator->paginate(
            $searchQuery,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('number', 25)/*limit per page*/
        );

        if($pagination->getTotalItemCount() <= 1) {
            if($pagination->getTotalItemCount() === 1) {
                $term = $pagination->getItems()[0]->getAnvVerb();
            }
            return $this->redirectToRoute('verb', ['anvVerb' => $term]);
        }

        return $this->render('main/search.html.twig', [
            'pagination' => $pagination
        ]);
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
    public function autocomplete(Request $request, RouterInterface $router, TranslatorInterface $translator)
    {
        $term = $request->query->get('term');
        if (null === $term) {
            return new JsonResponse();
        }
        $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
        $result = $verbRepository->findByTermAutocomplete($term);
        $array = [];
        /** @var Verb $res */
        for ($i = 0; $i < min(5, count($result)); $i++) {
            $res = $result[$i];
            $array[] = ['value' => $router->generate('verb', ['anvVerb' => $res->getAnvVerb()]), 'label' => $res->getAnvVerb()];
        }
        if(count($result) > 5) {
            $array[] = ['value' => $router->generate('search', ['term' => $term]), 'label' => $translator->trans    ('app.autocomplete.more')];
        }
        return new JsonResponse($array);
    }

    /**
     * @Route("/mail", name="mail")
     */
    public function sendMail(Request $request, \Swift_Mailer $mailer, Session $session, TranslatorInterface $translator)
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
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['result' => $result == 0? 'nok' : 'ok']);
            } else {
                if($result === 0) {
                    $session->getFlashBag()->set('message', $translator->trans('app.email.error'));

                    return $this->render('main/email.html.twig', [
                        'contactForm' => $contactForm->createView()
                    ]);
                } else {
                    /** SessionInterface $session */
                    $session->getFlashBag()->set('message', $translator->trans('app.email.sent'));
                    return $this->redirectToRoute('main');
                }
            }
        } else if ($contactForm->isSubmitted() && !$contactForm->isValid()) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['result' => 'nok', 'errors' => $contactForm->getErrors(true)]);
            } else {
                return $this->render('email.html.twig', [
                    'contactForm' => $contactForm->createView() 
                ]);
            }
        } else {
            
            return $this->render('main/email.html.twig', [
                'contactForm' => $contactForm->createView()
            ]);
        }
    }
}
