<?php

namespace Jiabin\HolterBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge(30);

        return $this->render('JiabinHolterBundle:Default:index.html.twig', array(), $response);
    }
}
