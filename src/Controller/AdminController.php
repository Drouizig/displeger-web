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

        $search = $request->query->get('search');
        $verbsQuery = null;
        if (null === $search || '' === $search) {
            $verbsQuery = $verbRepository->getAllVerbQuery();
        } else {
            $verbsQuery = $verbRepository->getSearchQuery($search);
        }

        $pagination = $this->knpPaginator->paginate(
            $verbsQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('number', 25)/*limit per page*/
        );

        return $this->render('admin/home.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/admin/verb/{id}", name="admin_verb")
     */
    public function verb(Request $request,Verb $verb = null)
    {

        $form = $this->createForm(VerbType::class, $verb);
        if(!strpos($request->headers->get('referer'), '/verb/')) {
            $this->get('session')->set('referer', $request->headers->get('referer'));
        }
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $verb = $form->getData();
                $this->getDoctrine()->getManager()->persist($verb);
                $this->getDoctrine()->getManager()->flush();
                if(key_exists('save', $request->request->get('verb'))) {
                    return $this->redirectToRoute('admin_verb', ['id' => $verb->getId()]);
                } else {
                    if($this->get('session')->has('referer')) {
                        return $this->redirect($this->get('session')->get('referer'));
                    } else {
                        return $this->redirectToRoute('admin');
                    }
                }
            }
        }
        return $this->render('admin/verb.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
