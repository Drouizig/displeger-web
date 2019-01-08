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

class AdminController extends AbstractController
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
     * @Route("/admin", name="admin")
     */
    public function admin(Request $request) {

        $verbRepository = $this->getDoctrine()->getRepository(Verb::class);
        $verbs = $verbRepository->findAll();

        return $this->render('admin/home.html.twig', ['verbs' => $verbs]);
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
                return $this->redirectToRoute('admin_verb', ['id' => $verb->getId()]);
            }
        }
        return $this->render('admin/verb.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
