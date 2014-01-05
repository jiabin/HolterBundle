<?php

namespace Jiabin\HolterBundle\Controller;

use Jiabin\HolterBundle\Document\Check;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * Delete check
     */
    public function deleteCheckAction()
    {
        $request = $this->getRequest();
        $checks = $request->request->get('checks', array());
        
        if (empty($checks)) {
            $this->get('session')->getFlashBag()->add('notice', 'No checks selected!');

            return $this->redirect($this->generateUrl('holter'));
        }

        $cf = $this->get('holter.check_factory');
        $om = $cf->getObjectManager();

        $om->getRepository($cf->checkClass)->createQueryBuilder()->remove()->field('name')->in($checks)->getQuery()->execute();
        $om->getRepository($cf->resultClass)->createQueryBuilder()->remove()->field('checkName')->in($checks)->getQuery()->execute();

        return $this->redirect($this->generateUrl('holter'));
    }

    /**
     * New check action
     */
    public function newCheckAction()
    {
        $data = new Check();

        $flow = $this->get('holter.form.flow.check');
        $flow->bind($data);

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                $cf = $this->get('holter.check_factory');
                
                $om = $cf->getObjectManager();
                $om->persist($data);
                $om->flush();

                $flow->reset();

                return $this->redirect($this->generateUrl('holter'));
            }
        }

        return $this->render('JiabinHolterBundle:Admin:new_check.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }
}
