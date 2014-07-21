<?php

namespace Jiabin\HolterBundle\Form\Flow;

use Jiabin\HolterBundle\Manager\HolterManager;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormTypeInterface;

class CheckFlow extends FormFlow
{
    /**
     * @var FormTypeInterface
     */
    protected $formType;

    /**
     * @var HolterManager
     */
    protected $manager;

    public function setFormType(FormTypeInterface $formType)
    {
        $this->formType = $formType;
    }

    public function getName()
    {
        return 'check';
    }

    protected function loadStepsConfig()
    {
        return array(
            array(
                'label' => 'base',
                'type' => $this->formType,
            ),
            array(
                'label' => 'options',
                'type' => $this->formType,
                'skip'  => function ($currentStepNumber, $flow) {
                    if ($currentStepNumber > 1) {
                        return $flow->createFormForStep(2)->get('options')->count() === 0;
                    } else {
                        return false;
                    }
                }
            ),
            array(
                'label' => 'confirm',
                'type' => $this->formType
            )
        );
    }

    /**
     * Set manager
     */
    public function setManager(HolterManager $manager)
    {
        $this->manager = $manager;

        return $this;
    }
}