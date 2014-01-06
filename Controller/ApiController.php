<?php

namespace Jiabin\HolterBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends Controller
{
    /**
     * Badge action
     */
    public function badgeAction()
    {
        $cf = $this->get('holter.check_factory');
        $status = $cf->createStatus();

        $badgeExtension = '.png';
        $badgePath = __DIR__.'/../Resources/public/img/badge';
        $badgeContents = file_get_contents($badgePath.'/'.$status->getStatusName().$badgeExtension);
        $response = new Response($badgeContents, 200, array(
            'Content-type' => 'image/png'
        ));
        $response->setPublic();
        $response->setSharedMaxAge(30);

        return $response;
    }

    /**
     * Returns all avaiable checks
     */
    public function checksAction()
    {
        $cf = $this->get('holter.check_factory');
        $data = array();
        foreach ($cf->getChecks() as $check) {
            $data[$check->getId()] = $check->getName();
        }

        $response = JsonResponse::create($data, 200);
        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * Get status
     */
    public function statusAction()
    {
        $cf = $this->get('holter.check_factory');
        $status = $cf->createStatus();

        $response = JsonResponse::create($status->toArray(), 200);
        $response->setPublic();
        $response->setSharedMaxAge(30);

        return $response;
    }

    /**
     * Api documentation
     */
    public function documentationAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $this->render('JiabinHolterBundle:Api:documentation.html.twig', array(), $response);
    }
}
