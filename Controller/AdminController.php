<?php

namespace Jiabin\HolterBundle\Controller;

use Jiabin\HolterBundle\Document\Check;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use FOS\UserBundle\Model\UserInterface;
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

    /**
     * Index user action
     */
    public function indexUserAction(Request $request)
    {
        $manager = $this->get('holter.manager');
        $repo    = $manager->getObjectManager()->getRepository($manager->userClass);
        $users   = $repo->findAll();

        return $this->render('JiabinHolterBundle:Admin:user.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * New user action
     */
    public function newUserAction(Request $request)
    {
        $user = $this->get('fos_user.user_manager')->createUser();
        $form = $this->createUserForm($request, $user);
        if ($form instanceof Response) {
            return $form;
        }

        return $this->render('JiabinHolterBundle:Admin:user_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Edit user action
     */
    public function editUserAction(Request $request, $id)
    {
        $user = $this->get('fos_user.user_manager')->findUserBy(array('id' => $id));
        $form = $this->createUserForm($request, $user);
        if ($form instanceof Response) {
            return $form;
        }

        return $this->render('JiabinHolterBundle:Admin:user_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Create user form
     *
     * @param  Request       $request
     * @param  UserInterface $user
     * @return Response|FormInterface
     */
    public function createUserForm(Request $request, UserInterface $user)
    {
        $form = $this->createForm('holter_user', $user);
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid($form)) {
                $this->get('fos_user.user_manager')->updateUser($user);

                $this->get('session')->getFlashBag()->add('success', 'User saved successfully!');

                return $this->redirect($this->generateUrl('holter_admin_user'));
            }
        }

        return $form;
    }

    /**
     * Delete user
     */
    public function deleteUserAction($id)
    {
        $current = $this->get('security.context')->getToken()->getUser();
        $manager = $this->get('holter.manager');
        $repo    = $manager->getObjectManager()->getRepository($manager->userClass);
        $user    = $repo->find($id);
        $name    = $user->getName();

        if ($current->getId() === $user->getId()) {
            $this->get('session')->getFlashBag()->add('error', "You have to login with another account to delete your self.");

            return $this->redirect($this->generateUrl('holter_admin_user'));
        }

        $om = $manager->getObjectManager();
        $om->remove($user);
        $om->flush();

        $this->get('session')->getFlashBag()->add('success', "User \"$name\" deleted successfully!");

        return $this->redirect($this->generateUrl('holter_admin_user'));
    }
}
