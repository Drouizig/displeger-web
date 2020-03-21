<?php

namespace App\Controller;

use App\Util\StatisticsManager;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Verb;
use App\Form\VerbType;

use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Configuration;
use App\Entity\Source;
use App\Form\ConfigurationType;
use App\Form\SourceType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AdminController extends AbstractController
{

    private $knpPaginator;

    public function __construct(PaginatorInterface $knpPaginator)
    {
        $this->knpPaginator = $knpPaginator;
    }

    /**
     * @Route("/admin", name="admin_home")
     */
    public function adminHome(Request $request, StatisticsManager $statisticsManager) {


        return $this->render('admin/home.html.twig', ['statisticsManager' => $statisticsManager]);
    }

    /**
     * @Route("/admin/verbs", name="admin_verbs")
     */
    public function verbs(Request $request) {
        return $this->adminList($request, Verb::class, 'admin/verbs.html.twig');
    }

    /**
     * @Route("/admin/sources", name="admin_sources")
     */
    public function sources(Request $request) {
        return $this->adminList($request, Source::class, 'admin/sources.html.twig');    
    }

    public function adminList(Request $request, string $class,string $twig) {
        /** @var AdminRepositoryInterface */
        $repository = $this->getDoctrine()->getRepository($class);

        $search = $request->query->get('search', null);
        $query = $repository->getBackSearchQuery($search);

        $pagination = $this->knpPaginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('number', 25)/*limit per page*/
        );
        $offset = ($request->query->getInt('page', 1) -1) * $request->query->getInt('number', 25);

        return $this->render($twig, ['pagination' => $pagination, 'offset' => $offset]);

    }


    
    /**
     * @Route("/admin/verb/{id?}", name="admin_verb")
     */
    public function verb(Request $request,Verb $verb = null)
    {
        return $this->adminEdit(
            $request, 
            VerbType::class, 
            Verb::class, 
            'admin_verb', 
            'admin/verb.html.twig',
            $verb);
    }
    /**
     * @Route("/admin/source/{id?}", name="admin_source")
     */
    public function source(Request $request,Source $source = null)
    {
        return $this->adminEdit(
            $request, 
            SourceType::class, 
            Source::class, 
            'admin_source', 
            'admin/source.html.twig',
            $source);
    }

    public function adminEdit(Request $request,string $form, string $class, $redirect_to, $twig, $editable = null)
    {
        $form = $this->createForm($form, $editable);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $this->getDoctrine()->getManager()->persist($data);
                $this->getDoctrine()->getManager()->flush();
                if(key_exists('save', $request->request->get($form->getName()))) {
                    return $this->redirect($request->getUri());
                } elseif(key_exists('save_return', $request->request->get($form->getName()))) {
                    return $this->redirectToRoute('admin_verbs', $request->query->get('params', []));
                } else {
                    /** @var AdminRepositoryInterface $repository */
                    $repository = $this->getDoctrine()->getRepository($class);
                    $params = $request->query->get('params', []);
                    $search = null;
                    $offset = $request->query->get('offset', 0);
                    if (key_exists('search', $params)) {
                        $search = $params['search'];
                    }
                    //recalculate page
                    if (key_exists('page', $params)) {
                        $params['page'] = floor($offset / 25)+1;
                    }
                    /** @var Query $query */
                    $query = $repository->getBackSearchQuery($search, $offset, 1);
                    $result = $query->getOneOrNullResult();
                    return $this->redirectToRoute($redirect_to,
                        [
                            'id' => $result['verb']->getId(),
                            'params' => $params,
                            'offset' => $offset +1
                        ]
                    );
                }
            }
        }
        return $this->render($twig, [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/configuration", name="admin_configuration")
     */
    public function configurationEdit(Request $request, SessionInterface $session, TranslatorInterface $translator)
    {
        $configurationObject = $this->getDoctrine()->getRepository(Configuration::class)->findFirst();
        $form = $this->createForm(ConfigurationType::class, $configurationObject);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $configuration = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($configuration);
                $em->flush();
                $session->getFlashBag()->set('message', $translator->trans('app.configuration.saved'));
                return $this->redirectToRoute('admin_configuration');
            }
        }

        return $this->render('admin/configuration.html.twig', [
            'form' => $form->createView()
        ]);
        

    }

}
