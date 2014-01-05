<?php

namespace Jiabin\HolterBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormTypeInterface;

class CheckFlow extends FormFlow
{
    /**
     * @var FormTypeInterface
     */
    protected $formType;

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
                'type' => $this->formType
            ),
            array(
                'label' => 'confirm',
                'type' => $this->formType
            )
        );
    }
}