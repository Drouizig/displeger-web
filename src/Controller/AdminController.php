<?php

namespace App\Controller;

use App\Entity\SourceTranslation;
use App\Entity\Tag;
use App\Entity\TagCategoryTranslation;
use App\Entity\TagTranslation;
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
use App\Entity\VerbTag;
use App\Entity\VerbTranslation;
use App\Form\ConfigurationType;
use App\Form\ImportType;
use App\Form\SourceType;
use App\Form\TagCategoryType;
use App\Repository\SourceRepository;
use App\Repository\TagRepository;
use App\Repository\VerbLocalizationRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        if($source == null){
            $source = new Source();
            $source->addTranslation(new SourceTranslation());
        }
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
        if($tag == null){
            $tag = new Tag();
            $tag->addTranslation(new TagTranslation());
        }
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
        if($tagCategory == null){
            $tagCategory = new TagCategory();
            $tagCategory->addTranslation(new TagCategoryTranslation());
        }
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


    /**
     * @Route("/admin/import", name="admin_import")
     */
    public function importFile(Request $request, VerbLocalizationRepository $vbRepo, SourceRepository $sourceRepo, TagRepository $tagRepo)
    {
        $form = $this->createForm(ImportType::class);

        $form->handleRequest($request);

        $modifiedTsVerbs = [];
        $modifiedFlatVerbs = [];
        $newVerbs = [];
        if($form->isSubmitted() && $form->isValid()) {

            $csvFile = $form->get('file')->getData();

            if ($csvFile) {
                $originalFilename = pathinfo($csvFile->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$csvFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $csvFile->move(
                        $this->getParameter('csv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $handle = fopen($this->getParameter('csv_directory').'/'.$newFilename, "r"); 
                $csvHandle = fgetcsv($handle, 0, ';');

                // 1 : infinitive
                // 2 : pennrann
                // 3 : rummad
                // 4 : bzhg
                // 5 : galleg
                // 6 : saozneg
                // 8 : Mammenn
                // 9 : tikedennn
                while(($csvHandle = fgetcsv($handle, 0, ';'))!== FALSE) {
                    /** @var VerbLocalization */
                    $verb = $vbRepo->findOneBy(['infinitive' => $csvHandle[1]]);
                    if($verb === null) {
                        $verb = $vbRepo->findOneBy(['infinitive' => str_replace('’', '\'', $csvHandle[1])]);
                    }
                    $tmpVerb = [];
                    if($verb != null) {
                        $isFlat = false;
                        $isModified = false;
                        $tmpVerb['dbVerb'] = $verb;
                        if($csvHandle[1] !== $verb->getInfinitive()) {
                            $tmpVerb['infinitive'] = $csvHandle[1];
                             $isModified = true;
                             $isFlat = true;
                        }
                        if($csvHandle[2] !== $verb->getBase()) {
                            $tmpVerb['base'] = $csvHandle[2];
                             $isModified = true;
                             $isFlat = true;
                        }
                        if($csvHandle[3] !== $verb->getCategory()) {
                            $tmpVerb['category'] = $csvHandle[3];
                            $isModified = true;
                            $isFlat = true;
                        }
                        if($verb->getVerb()->getTranslation('br') 
                        && $csvHandle[4] !== $verb->getVerb()->getTranslation('br')->getTranslation() 
                        && $csvHandle[4] !== '#brezhoneg') {
                            $tmpVerb['br'] = $csvHandle[4];
                            $isModified = true;
                        }
                        if($verb->getVerb()->getTranslation('fr')
                        && $csvHandle[5] !== $verb->getVerb()->getTranslation('fr')->getTranslation() 
                        && $csvHandle[5] !== '#galleg') {
                            $tmpVerb['fr'] = $csvHandle[5];
                            $isModified = true;
                        }
                        if($verb->getVerb()->getTranslation('en')
                        && $csvHandle[6] !== $verb->getVerb()->getTranslation('en')->getTranslation() 
                        && $csvHandle[6] !== '#saozneg') {
                            $tmpVerb['en'] = $csvHandle[6];
                            $isModified = true;
                        }
                        $mamennou = explode(',', $csvHandle[7]);
                        foreach($mamennou as $mammenn) {
                            $mammenn = trim($mammenn);
                            /** @var Source */
                            $source = $sourceRepo->findOneBy(['code' => $mammenn]);
                            if(!$source || !$verb->hasSource($source)) {
                                $tmpVerb['sources'][] = $mammenn;
                                $isModified = true;
                            }
                            
                        }
                        $tags = explode(',', $csvHandle[8]);
                        foreach($tags as $tag) {
                            $tag = trim($tag);
                            /** @var Tag */
                            $tagObject = $tagRepo->findOneBy(['code' => $tag]);
                            if(!$tagObject || !$verb->getVerb()->hasTag($tagObject)) {
                                $tmpVerb['tags'][] = $tag;
                                $isModified = true;
                            }
                            
                        }
                        if($isModified) {
                            if($isFlat) {
                                $modifiedFlatVerbs[] = $tmpVerb;
                            } else {
                                $modifiedTsVerbs[] = $tmpVerb;
                            }
                        }
                    } else {
                        // 1 : infinitive
                        // 2 : pennrann
                        // 3 : rummad
                        // 4 : bzhg
                        // 5 : galleg
                        // 6 : saozneg
                        // 8 : Mammenn
                        // 9 : tikedennn
                        $tmpVerb['infinitive'] = $csvHandle[1];
                        $tmpVerb['base'] = $csvHandle[2];
                        $tmpVerb['category'] = $csvHandle[3];
                        $tmpVerb['br'] = $csvHandle[4];
                        $tmpVerb['fr'] = $csvHandle[5];
                        $tmpVerb['en'] = $csvHandle[6];
                        $tmpVerb['sources'] = $csvHandle[7];
                        $tmpVerb['tags'] = $csvHandle[8];
                        $newVerbs[] = $tmpVerb;
                    }

                }


                
            }
        }


        return $this->render('admin/import.html.twig', [
            'form' => $form->createView(),
            'newVerbs' => $newVerbs,
            'modifiedTsVerbs' => $modifiedTsVerbs,
            'modifiedFlatVerbs' => $modifiedFlatVerbs,
        ]);

    }

    /**
     * @Route("/admin/import-definitive", name="admin_import_definitive")
     */
    public function importDefinitiveFile(Request $request, VerbLocalizationRepository $vbRepo, SourceRepository $sourceRepo, TagRepository $tagRepo)
    {
        $form = $this->createForm(ImportType::class);

        $form->handleRequest($request);

        $modifiedVerbs = [];
        $newVerbs = [];
        $em = $this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid()) {

            $csvFile = $form->get('file')->getData();

            if ($csvFile) {
                $originalFilename = pathinfo($csvFile->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$csvFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $csvFile->move(
                        $this->getParameter('csv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $handle = fopen($this->getParameter('csv_directory').'/'.$newFilename, "r"); 
                $csvHandle = fgetcsv($handle, 0, ';');

                $batchSize = 10;
                $batchIndex = 0;
                // 1 : infinitive
                // 2 : pennrann
                // 3 : rummad
                // 4 : bzhg
                // 5 : galleg
                // 6 : saozneg
                // 8 : Mammenn
                // 9 : tikedennn
                while(($csvHandle = fgetcsv($handle, 0, ';'))!== FALSE) {
                    /** @var VerbLocalization */
                    $verb = $vbRepo->findOneBy(['infinitive' => $csvHandle[1]]);
                    $tmpVerb = [];
                    if($verb != null) {
                        $isModified = false;
                        if($csvHandle[2] !== $verb->getCategory()) {
                             $verb->setBase($csvHandle[2]);
                             $isModified = true;
                        }
                        if($csvHandle[3] !== $verb->getCategory()) {
                             $verb->setCategory($csvHandle[3]);
                             $isModified = true;
                        }
                        if($verb->getVerb()->getTranslation('br') 
                        && $csvHandle[4] !== $verb->getVerb()->getTranslation('br')->getTranslation() 
                        && $csvHandle[4] !== '#brezhoneg') {
                            $verbTranslation = $verb->getVerb()->getTranslation('br');
                            if (null !== $verbTranslation) {
                                $verbTranslation = new VerbTranslation();
                                $verbTranslation->setLanguageCode('br');
                                $verb->getVerb()->addTranslation($verbTranslation);
                            }
                            $verbTranslation->setTranslation($csvHandle[4]);
                            $isModified = true;
                        }
                        if($verb->getVerb()->getTranslation('fr')
                        && $csvHandle[5] !== $verb->getVerb()->getTranslation('fr')->getTranslation() 
                        && $csvHandle[5] !== '#galleg') {
                            $verbTranslation = $verb->getVerb()->getTranslation('fr');
                            if (null !== $verbTranslation) {
                                $verbTranslation = new VerbTranslation();
                                $verbTranslation->setLanguageCode('fr');
                                $verb->getVerb()->addTranslation($verbTranslation);
                            }
                            $verbTranslation->setTranslation($csvHandle[5]);
                            $isModified = true;
                        }
                        if($csvHandle[6] !== $verb->getVerb()->getTranslation('en') && $csvHandle[6] !== '#saozneg') {
                            $verbTranslation = $verb->getVerb()->getTranslation('en');
                            if (null !== $verbTranslation) {
                                $verbTranslation = new VerbTranslation();
                                $verbTranslation->setLanguageCode('en');
                                $verb->getVerb()->addTranslation($verbTranslation);
                            }
                            $verbTranslation->setTranslation($csvHandle[6]);
                            $isModified = true;
                        }
                        $mamennou = explode(',', $csvHandle[7]);
                        foreach($mamennou as $mammenn) {
                            $mammenn = trim($mammenn);
                            /** @var Source */
                            $source = $sourceRepo->findOneBy(['code' => $mammenn]);
                            if(!$source || !$verb->hasSource($source)) {
                                if(!$source) {
                                    $source = new Source();
                                    $source->setCode($mammenn);
                                    $em->persist($source);
                                    $em->flush();
                                }
                                $verb->addSource($source);
                                $isModified = true;
                            }
                            
                        }
                        $tags = explode(',', $csvHandle[8]);
                        foreach($tags as $tag) {
                            $tag = trim($tag);
                            /** @var Tag */
                            $tagObject = $tagRepo->findOneBy(['code' => $tag]);
                            if(!$tagObject || !$verb->getVerb()->hasTag($tagObject)) {
                                if(!$tagObject) {
                                    $tagObject = new Tag();
                                    $tagObject->setCode($tag);
                                    $em->persist($tagObject);
                                    $em->flush();
                                }
                                $verbTag = $verb->getVerb()->getVerbTag($tagObject);
                                if(!$verbTag) {
                                    $verbTag = new VerbTag();
                                    $verbTag->setTag($tagObject);
                                    $verbTag->setVerb($verb->getVerb());
                                }
                                $verb->getVerb()->addTag($verbTag);
                                $isModified = true;
                            }
                            
                        }
                        if($isModified) {
                            $em->persist($verb);
                            $em->persist($verb->getVerb());
                        }
                    } else {
                        $verb = new Verb();
                        $verbLocalization = new VerbLocalization;
                        $verbLocalization->setVerb($verb);
                        $verbLocalization->setInfinitive($csvHandle[1]);
                        $verbLocalization->setBase($csvHandle[2]);
                        $verbLocalization->setCategory($csvHandle[3]);
                        if($csvHandle[4] != '' && $csvHandle[4] != '#brezhoneg') {
                            $brTranslation = new VerbTranslation();
                            $brTranslation->setTranslation($csvHandle[4]);
                            $brTranslation->setLanguageCode('br');
                            $verb->addTranslation($brTranslation);
                            $em->persist($brTranslation);
                        }
                        if($csvHandle[5] != '' && $csvHandle[5] != '#galleg') {
                            $frTranslation = new VerbTranslation();
                            $frTranslation->setTranslation($csvHandle[5]);
                            $frTranslation->setLanguageCode('fr');
                            $verb->addTranslation($frTranslation);
                            $em->persist($frTranslation);
                        }
                        if($csvHandle[6] != '' && $csvHandle[6] != '#saozneg') {
                            $enTranslation = new VerbTranslation();
                            $enTranslation->setTranslation($csvHandle[6]);
                            $enTranslation->setLanguageCode('en');
                            $verb->addTranslation($enTranslation);
                            $em->persist($enTranslation);
                        }
                        $mamennou = explode(',', $csvHandle[7]);
                        foreach($mamennou as $mammenn) {
                            $mammenn = trim($mammenn);
                            /** @var Source */
                            $source = $sourceRepo->findOneBy(['code' => $mammenn]);
                            if(!$source) {
                                $source = new Source();
                                $source->setCode($mammenn);
                                $em->persist($source);
                                $em->flush();
                            }
                            $verbLocalization->addSource($source);
                        }
                        $tags = explode(',', $csvHandle[8]);
                        foreach($tags as $tag) {
                            $tag = trim($tag);
                            /** @var Tag */
                            $tagObject = $tagRepo->findOneBy(['code' => $tag]);
                            if(!$tagObject) {
                                $tagObject = new Tag();
                                $tagObject->setCode($tag);
                                $em->persist($tag);
                                $em->flush();
                            }
                            $verbTag = new VerbTag();
                            $verbTag->setTag($tagObject);
                            $verbTag->setVerb($verb);
                            $verb->addTag($verbTag);
                            $em->persist($verbTag);
                        }
                        $verb->addLocalization($verbLocalization);
                        $em->persist($verbLocalization);
                        $em->persist($verb);
                    }
                    $batchIndex++;
                    if($batchIndex > $batchSize) {
                        $batchIndex = 0;
                        $em->flush();
                    }
                }
            
            }
        }


        return $this->render('admin/import.html.twig', [
            'form' => $form->createView(),
            'newVerbs' => $newVerbs,
            'modifiedVerbs' => $modifiedVerbs,
        ]);

    }

}
