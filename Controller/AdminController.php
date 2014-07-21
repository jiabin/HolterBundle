<?php

namespace Jiabin\HolterBundle\Controller;

use Jiabin\HolterBundle\Document\Check;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * Index action
     */
    public function indexCheckAction()
    {
        $manager = $this->get('holter.manager');

        return $this->render('JiabinHolterBundle:Admin:check.html.twig', array(
            'checks' => $manager->getChecks()
        ));
    }

    /**
     * New check action
     */
    public function newCheckAction()
    {
        $check = new Check();
        $flow = $this->get('holter.form.flow.check');
        $flow->bind($check);

        $form = $this->createCheckForm($check, $flow);
        if ($form instanceof Response) {
            return $form;
        }

        return $this->render('JiabinHolterBundle:Admin:check_form.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }

    /**
     * Edit check action
     */
    public function editCheckAction($id)
    {
        $manager = $this->get('holter.manager');
        $check   = $manager->getCheck($id);

        $flow = $this->get('holter.form.flow.check');
        $flow->bind($check);

        $form = $this->createCheckForm($check, $flow);
        if ($form instanceof Response) {
            return $form;
        }

        return $this->render('JiabinHolterBundle:Admin:check_form.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }

    /**
     * Create check form
     *
     * @param  Check                  $check
     * @param  FormFlowInterface      $flow
     * @return Response|FormInterface
     */
    public function createCheckForm(Check $check, FormFlowInterface $flow)
    {
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                $manager = $this->get('holter.manager');

                $om = $manager->getObjectManager();
                $om->persist($check);
                $om->flush();

                $flow->reset();

                $this->get('session')->getFlashBag()->add('success', 'Check saved successfully!');

                return $this->redirect($this->generateUrl('holter_admin_check'));
            }
        }

        return $form;
    }

    /**
     * Delete check
     */
    public function deleteCheckAction($id)
    {
        $manager = $this->get('holter.manager');
        $check   = $manager->getCheck($id);
        $name    = $check->getName();

        $om = $manager->getObjectManager();
        $om->remove($check);
        $om->flush();

        $this->get('session')->getFlashBag()->add('success', "Check \"$name\" deleted successfully!");

        return $this->redirect($this->generateUrl('holter_admin_check'));
    }

    /**
     * Index config action
     */
    public function indexConfigAction(Request $request)
    {
        $config = $this->get('holter.manager')->getConfig();
        $form   = $this->createForm('holter_config', $config);
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid($form)) {
                $om = $this->get('holter.manager')->getObjectManager();
                $om->persist($config);
                $om->flush();

                $this->get('session')->getFlashBag()->add('success', 'Config saved successfully!');
            }
        }

        return $this->render('JiabinHolterBundle:Admin:config.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
