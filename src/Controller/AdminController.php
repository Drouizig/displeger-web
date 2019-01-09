<?php

namespace App\Controller;

use Doctrine\ORM\Query;
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
use App\Form\VerbType;

use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

class AdminController extends AbstractController
{

    private $knpPaginator;

    public function __construct(PaginatorInterface $knpPaginator)
    {
        $this->knpPaginator = $knpPaginator;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(Request $request) {

        $verbRepository = $this->getDoctrine()->getRepository(Verb::class);

        $search = $request->query->get('search', null);
        $verbsQuery = $verbRepository->getSearchQuery($search);

        $pagination = $this->knpPaginator->paginate(
            $verbsQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('number', 25)/*limit per page*/
        );
        $offset = ($request->query->getInt('page', 1) -1) * $request->query->getInt('number', 25);

        return $this->render('admin/home.html.twig', ['pagination' => $pagination, 'offset' => $offset]);
    }

    /**
     * @Route("/admin/verb/{id}", name="admin_verb")
     */
    public function verb(Request $request,Verb $verb = null)
    {
        $form = $this->createForm(VerbType::class, $verb);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $verb = $form->getData();
                $this->getDoctrine()->getManager()->persist($verb);
                $this->getDoctrine()->getManager()->flush();
                if(key_exists('save', $request->request->get('verb'))) {
                    return $this->redirect($request->getUri());
                } elseif(key_exists('save_return', $request->request->get('verb'))) {
                    return $this->redirectToRoute('admin', $request->query->get('params', []));
                } else {
                    $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
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
                    /** @var Query $verbsQuery */
                    $verbsQuery = $verbRepository->getSearchQuery($search, $offset, 1);
                    $result = $verbsQuery->getOneOrNullResult();
                    return $this->redirectToRoute('admin_verb',
                        [
                            'id' => $result->getId(),
                            'params' => $params,
                            'offset' => $offset +1
                        ]
                    );
                }
            }
        }
        return $this->render('admin/verb.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
