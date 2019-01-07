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
        return new Response('ok');
    }

}
