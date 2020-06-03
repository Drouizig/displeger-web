<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
use App\Entity\VerbLocalization;
use App\Entity\VerbTranslation;
use App\Form\AdvancedSearchType;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

class MainController extends AbstractController
{

    const PDF_DIR="pdf_export/";

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
     * @Route("/", name="pre_locale")
     */
    public function preLocale(Request $request)
    {
        $supportedLanguages = ['br', 'fr', 'en'];
        $acceptedLanguages = explode(',',$request->headers->get('accept-language'));
        foreach($acceptedLanguages as $fullLocale) {
            $locale = explode(';', $fullLocale)[0];
            $language = explode('-', $locale)[0];
            if(in_array($language, $supportedLanguages)) {
                return $this->redirectToRoute('main',['_locale' => $language]);
            }
        }
        return $this->redirectToRoute('main', ['_locale' => 'en']);
    }

    /**
     * @Route("/{_locale}", name="main", requirements= {
     *      "_locale": "br|fr|en"
     * })
     */
    public function index(Request $request) {
        if ($request->query->get('verb')) {
            return $this->redirectToRoute('verb', ['anvVerb' => $request->query->get('verb')]);
        }

        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/{_locale}/search", name="search")
     */
    public function search(Request $request, PaginatorInterface $knpPaginator) {
        $term = $request->query->get('term', null); 
        if ( null === $term ) {
            return $this->redirectToRoute('main');  
        }
        /** @var VerbLocalizationRepository $verbRepository */
        $verbLocalizationRepository = $this->getDoctrine()->getRepository(VerbLocalization::class);
        $searchQuery = $verbLocalizationRepository->getFrontSearchQuery($term);
        
        $pagination = $knpPaginator->paginate(
            $searchQuery,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('number', 25)/*limit per page*/
        );

        if($pagination->getTotalItemCount() <= 1) {
            if($pagination->getTotalItemCount() === 1) {
                $term = $pagination->getItems()[0]->getInfinitive();
            }
            return $this->redirectToRoute('verb', ['infinitive' => $term]);
        }

        return $this->render('main/search.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{_locale}/search_advanced", name="search_advanced")
     */
    public function searchAdvanced(Request $request, PaginatorInterface $knpPaginator) {
        
        $form = $this->createForm(AdvancedSearchType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var erbRepository VerbRepository */
            $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
            // $searchQuery = $verbRepository->getFrontSearchQuery($term);
            
            $pagination = $knpPaginator->paginate(
                $searchQuery,
                $request->query->getInt('page', 1)/*page number*/,
                $request->query->getInt('number', 25)/*limit per page*/
            );

            return $this->render('main/search_advanced.html.twig', [
                'pagination' => $pagination
            ]);
        } else {
            return $this->render('main/search_advanced.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }


    /**
     * @Route("/{_locale}/verb/{infinitive}", name="verb", defaults={"print" : false})
     * @Entity("VerbLocalization", expr="repository.findOneByInfinitive(infinitive)")
     */
    public function verb(Request $request,VerbLocalization $verbLocalization = null, LoggerInterface $logger, Pdf $pdf)
    {
        $contactForm = $this->createForm(ContactType::class);
        $reportErrorForm = $this->createForm(ContactType::class);
        $template = 'main/verb.html.twig';

        $print = $request->query->get('print', false);

        if($print) {
            $template = 'main/verb.print.html.twig';
        }

        if(null !== $verbLocalization) {
            $verb = $verbLocalization->getVerb();
            $locale = $request->get('_locale', 'br');
            $verbEndings = $this->verbouManager->getEndings($verbLocalization->getCategory(), $verbLocalization->getDialectCode());
            // if(in_array($verbLocalization->getInfinitive(), ['bezañ', 'boud'])) {
            //     $template = 'main/irregular/bezan.html.twig';
            // }
            $anvGwan = $verbEndings['standard']['gwan'];
            unset($verbEndings['standard']['gwan']);
            unset($verbEndings['standard']['nach']);
            // $mutatedBase = $this->kemmaduriouManager->mutateWord($verbLocalization->getBase(), KemmaduriouManager::BLOTAAT);
            $nach = [];
            // foreach($verbEndings['kadarnaat'] as $ending) {
            //     if(count($ending) > 0) {
            //         $nach[] = 'na '.$mutatedBase.'<strong>'.$ending[0].'</strong> ket';
            //     } else {
            //         $nach[] = null;
            //     }
            // }

            $wikeriadurUrl = $this->getParameter('url_wikeriadur')[$locale].$verbLocalization->getInfinitive();
            $geriafurchUrl = '';
            if(isset($this->getParameter('url_geriafurch')[$locale])) {
                $geriafurchUrl = $this->getParameter('url_geriafurch')[$locale].$verbLocalization->getInfinitive();
            } else {
                $geriafurchUrl = $this->getParameter('url_geriafurch')['br'].$verbLocalization->getInfinitive();
            }
            $wikeriadurConjugationUrl = $this->getParameter('url_wikeriadur_conjugation')[$locale].$verbLocalization->getInfinitive();
            if($print){
                if(!file_exists(self::PDF_DIR.$verbLocalization->getInfinitive() . '.pdf')) {
                    $html = $this->renderView(
                        $template,
                        array(
                            'verb' => $verb,
                            'verbLocalization' => $verbLocalization,
                            'verbEndings' => $verbEndings,
                            'anvGwasn' => $anvGwan,
                            'nach' => $nach,
                            'contactForm' => $contactForm->createView(),
                            'print' => $print,
                            'anvVerb' => $verbLocalization->getInfinitive()
                        )
                    );
                    //TODO will hog the disk in the public folder, maybe we could clean it after. or keep it for cache ?
                    $pdf->generateFromHtml($html, self::PDF_DIR.$verbLocalization->getInfinitive() . '.pdf', [], true);
                }
                return new BinaryFileResponse(self::PDF_DIR.$verbLocalization->getInfinitive() . '.pdf');

            } else {
                return $this->render($template, [
                    'verb' => $verb,
                    'verbLocalization' => $verbLocalization,
                    'verbEndings' => $verbEndings['standard'],
                    'localizedVerbEndings' => $verbEndings['localized'],
                    'anvGwan' => $anvGwan,
                    'nach' => $nach,
                    'contactForm' => $contactForm->createView(),
                    'reportErrorForm' => $reportErrorForm->createView(),
                    'print' => $print,
                    'wikeriadur_url' => $wikeriadurUrl,
                    'geriafurch_url' => $geriafurchUrl,
                    'wikeriadur_conjugation_url' => $wikeriadurConjugationUrl,
                ]);
            }
        }

        $searchTerm = $request->attributes->get('infinitive');
        return $this->render('main/error.html.twig', [
            'verb' => $searchTerm,
            'contactForm' => $contactForm->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/autocomplete", name="autocomplete")
     */
    public function autocomplete(Request $request, RouterInterface $router, TranslatorInterface $translator)
    {
        $term = $request->query->get('term');
        if (null === $term) {
            return new JsonResponse();
        }
        /** @var VerbLocalizationRepository $verbLocalizationRepository */
        $verbLocalizationRepository = $this->getDoctrine()->getRepository(VerbLocalization::class);
        $result = $verbLocalizationRepository->findByTermAutocomplete($term);
        $array = [];
        /** @var Verb $res */
        for ($i = 0; $i < min(5, count($result)); $i++) {
            $res = $result[$i];
            $array[] = ['value' => $router->generate('verb', ['infinitive' => $res->getInfinitive()]), 'label' => $res->getInfinitive()];
        }
        if(count($result) > 5) {
            $array[] = ['value' => $router->generate('search', ['term' => $term]), 'label' => $translator->trans('app.autocomplete.more')];
        }
        return new JsonResponse($array);
    }

    /**
     * @Route("/{_locale}/mail", name="mail")
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
                        'form_object' => $contactForm->createView()
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
                return $this->render('main/email.html.twig', [
                    'form_object' => $contactForm->createView()
                ]);
            }
        } else {
            
            return $this->render('main/email.html.twig', [
                'form_object' => $contactForm->createView()
            ]);
        }
    }

    /**
     * @Route("/{_locale}/notice", name="notice", requirements= {
     *      "_locale": "br|fr|en"
     * })
     */
    public function notice(Request $request) {
        return $this->render('misc/notice.html.twig');
    }

    /**
     * @Route("/{_locale}/thanks", name="thanks", requirements= {
     *      "_locale": "br|fr|en"
     * })
     */
    public function thanks() {
         return $this->CMSPage('thanks');
    }

    public function CMSPage($code) {
        return $this->render('misc/cms.html.twig', ['code' => $code]);
    }

    /**
     * @Route("/{_locale}/random", name="random", requirements= {
     *      "_locale": "br|fr|en"
     * })
     */
    public function randomVerb(){
        $verb = $this->getDoctrine()->getRepository(Verb::class)->findRandomVerb();
        $route = 'main';
        $args = null;
        if($verb != null){
            $route = 'verb';
            $args = ['anvVerb' => $verb->getAnvVerb()];
        }

        return $this->redirectToRoute($route, $args);
    }
}
