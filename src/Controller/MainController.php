<?php

namespace App\Controller;

use App\Entity\Tag;
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
use App\Entity\Source;
use App\Entity\SourceTypeEnum;
use App\Entity\VerbLocalization;
use App\Entity\VerbTranslation;
use App\Form\AdvancedSearchType;
use App\Repository\ConfigurationTranslationRepository;
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
        $term = trim($term);
        /** @var $verbRepository VerbLocalizationRepository */
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
        
        $form = $this->createForm(AdvancedSearchType::class, 
            [
                'term_advanced' => $request->query->get('term_advanced', null),
                'language' => $request->query->get('language', null),
                'conjugated' => $request->query->get('conjugated', null)
            ]
        );

        if ($request->query->has('term_advanced')) {
            $searchQuery = null;
            $type = 'localization';
            $term = trim($request->query->get('term_advanced'));
            /**  @var verbRepository VerbRepository  */
            $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
            if($request->query->get('language') == 'br') {
                $searchQuery = $verbRepository->getLocalizationSearchBuilder($term);
            } else {
                $type = 'translation';
                $searchQuery = $verbRepository->getTranslationSearchBuilder($term, $request->query->get('language'));
            }
            
            $pagination = $knpPaginator->paginate(
                $searchQuery->getQuery(),
                $request->query->getInt('page', 1)/*page number*/,
                $request->query->getInt('number', 25)/*limit per page*/
            );

            return $this->render('main/search_advanced.html.twig', [
                'pagination' => $pagination,
                'form' => $form->createView(),
                'type' => $type,
                'term' => $term,
                'language' => $request->query->get('language', null)
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

            /** @var verbLocalizationRepository VerbLocalizationRepository */
            $verbLocalizationRepository = $this->getDoctrine()->getRepository(VerbLocalization::class);
            $previousVerb = $verbLocalizationRepository->getPreviousVerb($verbLocalization->getInfinitive());
            $nextVerb = $verbLocalizationRepository->getNextVerb($verbLocalization->getInfinitive());
            $locale = $request->get('_locale', 'br');
            $verbEndings = $this->verbouManager->getEndings($verbLocalization->getCategory(), $verbLocalization->getDialectCode());

            $anvGwan = $verbEndings['standard']['gwan'];
            unset($verbEndings['standard']['gwan']);
            unset($verbEndings['standard']['nach']);
            $softMutatedBase = $this->kemmaduriouManager->mutateWord($verbLocalization->getBase(), KemmaduriouManager::BLOTAAT, $verbLocalization->getGouMutation());
            $nach = [];
            foreach($verbEndings['standard']['kadarnaat'] as $person => $ending) {
                if(count($ending) > 0) {
                    $nach[$person] = 'na '.$softMutatedBase.'<strong>'.$ending[0].'</strong> ket';
                } else {
                    $nach[$person] = null;
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

            $wikeriadurUrl = $this->getParameter('url_wikeriadur')[$locale].$verbLocalization->getInfinitive();
            $geriafurchUrl = '';
            if(isset($this->getParameter('url_geriafurch')[$locale])) {
                $geriafurchUrl = $this->getParameter('url_geriafurch')[$locale].$verbLocalization->getInfinitive();
            } else {
                $geriafurchUrl = $this->getParameter('url_geriafurch')['br'].$verbLocalization->getInfinitive();
            }
            $organisation = $this->getParameter('organisation');
            if( null != $this->get('parameter_bag')->has('organisation.'.$verbLocalization->getInfinitive())) {
                $organisation = $this->getParameter('organisation.'.$verbLocalization->getInfinitive());
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
                    'organisation' => $organisation,
                    'wikeriadur_conjugation_url' => $wikeriadurConjugationUrl,
                    'previousVerb' => $previousVerb,
                    'nextVerb' => $nextVerb,
                    'enur' => $stummEnUr,
                    'ober' => $stummOber
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
            ->setFrom($this->getParameter('email.from'))
            ->setTo($this->getParameter('email.to'))
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
     * @Route("/{_locale}/sources", name="sources", requirements= {
     *      "_locale": "br|fr|en"
     * })
     */
    public function sources() {
        $sourceRepo = $this->getDoctrine()->getRepository(Source::class);
        $sourceEntities = $sourceRepo->findBy(['active'=>true]);
        $sources = [
            SourceTypeEnum::GRAMMAR => [],
            SourceTypeEnum::VERB => [],
            SourceTypeEnum::TRADUCTION => [],
        ];
        /** @var $sourceEntity Source */
        foreach($sourceEntities as $sourceEntity) {
            if(SourceTypeEnum::TRADUCTION === $sourceEntity->getType()) {
                $sources[$sourceEntity->getType()][$sourceEntity->getLocale()][] = $sourceEntity;
            } elseif (null !== $sourceEntity->getType()) {
                $sources[$sourceEntity->getType()][] = $sourceEntity;
            }
        }
        return $this->render('misc/sources.html.twig', ['sources' => $sources]);
    }

    /**
     * @Route("/{_locale}/page/{code}", name="page", requirements= {
     *      "_locale": "br|fr|en"
     * })
     */
    public function CMSPage(
        Request $request, 
        $code, 
        ConfigurationTranslationRepository $configurationTranslationRepository) {
        /** @var ConfigurationTranslation $configurationTranslation  */
        $configurationTranslation = $configurationTranslationRepository->findByCodeAndLocale($code, $request->getLocale());
        
        return $this->render('misc/cms.html.twig', ['configurationTranslation' => $configurationTranslation]);
    }

    /**
     * @Route("/{_locale}/random", name="random", requirements= {
     *      "_locale": "br|fr|en"
     * })
     */
    public function randomVerb(){
        $verb = $this->getDoctrine()->getRepository(VerbLocalization::class)->findRandomVerb();
        $route = 'main';
        $args = null;
        if($verb != null){
            $route = 'verb';
            $args = ['infinitive' => $verb->getInfinitive()];
        }

        return $this->redirectToRoute($route, $args);
    }

    /**
     * @Route("/{_locale}/verbs_by_tag", name="verbs_by_tag")
     */
    public function verbsByTag(Request $request, PaginatorInterface $knpPaginator)
    {
        $result = [];
        $tag = $request->get('tag', null);
        $tagRepo = $this->getDoctrine()->getRepository(Tag::class);
        $verbLocalizationRepo = $this->getDoctrine()->getRepository(VerbLocalization::class);

        $tagObject = $tagRepo->findOneBy(['code' => $tag]);
        if($tagObject != null)
        {
            foreach($tagObject->getVerbs() as $verbTag)
            {
                $currentLocalization = $verbLocalizationRepo->findOneByVerbId($verbTag->getVerb()->getId());
                if($currentLocalization != null) {
                    array_push($result, $currentLocalization);
                }
            }
        }

        usort($result, function($a, $b) {
            return $a->getInfinitive() > $b->getInfinitive();
        });

        $pagination = $knpPaginator->paginate(
            $result,
            $request->query->getInt('page', 1), //page number,
            $request->query->getInt('number', 25) //limit per page
        );

        return $this->render('main/verbs_by_tag.html.twig', [
            'pagination' => $pagination,
            'search_tag' => $tag
            ]);
    }

    /**
     * @Route("/{_locale}/verbs_by_category", name="verbs_by_category")
     */
    public function verbsByCategory(Request $request, PaginatorInterface $knpPaginator, TranslatorInterface $translator)
    {
        $category = $request->get('category', null);
        $verbLocalizationRepo = $this->getDoctrine()->getRepository(VerbLocalization::class);

        $pagination = $knpPaginator->paginate(
            $verbLocalizationRepo->findBy(['category' => $category], ['infinitive' => 'ASC']),
            $request->query->getInt('page', 1), //page number,
            $request->query->getInt('number', 25) //limit per page
        );

        return $this->render('main/verbs_by_tag.html.twig', [
            'pagination' => $pagination,
            'search_tag' => $translator->trans('app.category.'.$category)
            ]);
    }
}
