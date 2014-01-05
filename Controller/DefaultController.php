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
        $cf = $this->get('holter.check_factory');

        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $this->render('JiabinHolterBundle:Default:index.html.twig', array(
            'status' => $cf->createStatus()
        ), $response);
    }
}
