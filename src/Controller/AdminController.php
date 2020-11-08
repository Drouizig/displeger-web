<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
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
use App\Entity\TagCategory;
use App\Entity\VerbLocalization;
use App\Entity\VerbTranslation;
use App\Form\ConfigurationType;
use App\Form\SourceType;
use App\Form\TagCategoryType;
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

    /**
     * @Route("/admin/tags", name="admin_tags")
     */
    public function tags(Request $request){
        return $this->adminList($request, Tag::class, 'admin/tags.html.twig');
    }

    /**
     * @Route("/admin/tagCategories", name="admin_tagCategories")
     */
    public function tagCategories(Request $request){
        return $this->adminList($request, TagCategory::class, 'admin/tagCategories.html.twig');
    }
    /**
     * @Route("/admin/configurations", name="admin_configurations")
     */
    public function configurations(Request $request) {
        return $this->adminList($request, Configuration::class, 'admin/configurations.html.twig');    
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
        if($verb == null) {
            $verb = new Verb();
            $verb->addLocalization(new VerbLocalization());
            $verb->addTranslation(new VerbTranslation());
        }
        return $this->adminEdit(
            $request, 
            VerbType::class, 
            Verb::class, 
            'admin_verb', 
            'admin_verbs', 
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
            'admin_sources', 
            'admin/source.html.twig',
            $source);
    }

    /**
     * @Route("/admin/tag/{id?}", name="admin_tag")
     */
    public function tag(Request $request,Tag $tag = null)
    {
        return $this->adminEdit(
            $request,
            TagType::class,
            Tag::class,
            'admin_tag',
            'admin_tags',
            'admin/tag.html.twig',
            $tag);
    }

    /**
     * @Route("/admin/tagCategory/{id?}", name="admin_tagCategory")
     */
    public function tagCategory(Request $request,TagCategory $tagCategory = null)
    {
        return $this->adminEdit(
            $request,
            TagCategoryType::class,
            TagCategory::class,
            'admin_tagCategory',
            'admin_tagCategories',
            'admin/tagCategory.html.twig',
            $tagCategory);
    }

    /**
     * @Route("/admin/configuration/{id?}", name="admin_configuration")
     */
    public function configuration(Request $request,Configuration $configuration = null)
    {
        return $this->adminEdit(
            $request, 
            ConfigurationType::class, 
            Configuration::class, 
            'admin_configuration',
            'admin_configurations', 
            'admin/configuration.html.twig',
            $configuration);
    }
    public function adminEdit(Request $request,string $form, string $class, $redirect_to_single,$redirect_to_list, $twig, $editable = null)
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
                    return $this->redirectToRoute($redirect_to_list, $request->query->get('params', []));
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
                    if(is_array($result)) {
                        $result = $result['verb'];
                    }
                    return $this->redirectToRoute($redirect_to_single,
                        [
                            'id' => $result->getId(),
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

}
